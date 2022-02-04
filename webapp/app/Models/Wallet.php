<?php

namespace App\Models;

use App\Events\WalletBalanceChange;
use App\Perms\CommunityRoles;
use App\Utils\MoneyAmount;
use App\Utils\MoneyAmountBag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Wallet model.
 *
 * This represents a user wallet in an economy.
 *
 * @property int id
 * @property int economy_member_id
 * @property-read EconomyMember economyMember
 * @property string name
 * @property decimal balance
 * @property int currency_id
 * @property-read Currency currency
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Wallet extends Model {

    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $table = 'wallet';

    protected $fillable = [
        'name',
        'currency_id',
    ];

    /**
     * Number of seconds in a month.
     */
    const MONTH_SECONDS = 2629800;

    /**
     * Scope to wallets that are compatible with the given wallet.
     *
     * This filters the wallet by the used currency.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param Currency|int $currency The currency or currency ID.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrency($query, $currency) {
        return $query->where(
            'currency_id',
            $currency instanceof Currency ? $currency->id : $currency
        );
    }

    /**
     * Scope to wallets that use the given currency.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param Wallet $wallet The wallet they must be compatible with.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCompatibleWith($query, Wallet $wallet) {
        return $query->currency($wallet->currency_id);
    }

    /**
     * Get the economy member this wallet model is from.
     *
     * @return The economy member.
     */
    public function economyMember() {
        return $this->belongsTo(EconomyMember::class);
    }

    /**
     * Get a relation to the wallet currency.
     *
     * @return Relation to currency.
     */
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Get a list of mutations, linked to this wallet.
     *
     * This method does not do any permission checking, all linked mutations are
     * simply related.
     *
     * @param bool [$order=true] Automatically order.
     *
     * @return The mutations.
     */
    public function mutations($order = true) {
        $wallet_id = $this->id;
        $query = Mutation::leftJoin('transaction', 'transaction.id', 'mutation.transaction_id')
            ->whereExists(function($query) use($wallet_id) {
                return $query
                    ->selectRaw('1')
                    ->from('mutation AS mm')
                    ->whereRaw('mm.transaction_id = transaction.id')
                    ->where('mm.mutationable_type', MutationWallet::class)
                    ->join('mutation_wallet AS mw', 'mm.mutationable_id', 'mw.id')
                    ->where('mw.wallet_id', $wallet_id);
            });
        if($order)
            $query = $query->latest('mutation.created_at');
        return $query;
    }

    /**
     * Get a relation to all wallet mutations.
     *
     * @return Relation Relation to wallet mutations.
     */
    public function walletMutations() {
        return $this->hasMany(MutationWallet::class);
    }

    /**
     * Get all transactions that affected this wallet, having at least one
     * wallet mutation linked to this wallet.
     *
     * This method does not apply any permission checking, all linked
     * transactions are simply related.
     *
     * The returned relation is sorted, putting the newest transactions first.
     *
     * @param bool [$order=true] Automatically order.
     *
     * @return The transactions.
     */
    public function transactions($order = true) {
        $query = $this
            ->hasManyDeepFromRelations(
                $this->walletMutations(),
                (new MutationWallet)->mutation(),
                (new Mutation)->transaction()
            )
            ->distinct();
        if($order)
            $query = $query->latest('transaction.created_at');
        return $query;
    }

    /**
     * Get the last few transactions that took place, affecting this wallet.
     *
     * This method does not apply any permission checking, all linked
     * transactions are simply returned.
     *
     * @param [$limit=5] The number of last transactions to return at max.
     *
     * @return The last transactions.
     */
    public function lastTransactions($limit = 5) {
        return $this
            ->transactions()
            ->limit($limit);
    }

    /**
     * Get the wallet balance as money amount.
     *
     * @return MoneyAmount The balance as money amount.
     */
    public function getMoneyAmount() {
        return new MoneyAmount($this->currency, $this->balance);
    }

    /**
     * Format the wallet balance as human readable text using the proper
     * currency format.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatBalance($format = BALANCE_FORMAT_PLAIN) {
        return $this->currency->format($this->balance, $format);
    }

    /**
     * Build and return the URL for the wallet show page.
     *
     * @return string The wallet show URL.
     */
    // TODO: attempt to implement some eager loading of the economy model
    public function getUrlShow() {
        return route('community.wallet.show', [
            'communityId' => $this->economyMember->economy->community->human_id,
            'economyId' => $this->economyMember->economy_id,
            'walletId' => $this->id,
        ]);
    }

    /**
     * Withdraw the given amount from the wallet.
     *
     * Wallet balance should always be modified using the `withdraw` and
     * `deposit` functions to ensure integrety.
     *
     * This does not create a corresponding transaction or mutation.
     *
     * @param number $amount The amount to withdraw from this wallet.
     *
     * @throws \Exception Throws an exception if the amount is negative or zero.
     */
    public function withdraw($amount) {
        // Assert the amount is positive
        if($amount <= 0)
            throw new \Exception("Failed to withdraw from wallet, amount is <= 0");

        // We must be in a database transaction
        assert_transaction();

        $before = $this->getMoneyAmount();

        // Decrement the balance
        $this->decrement('balance', $amount);

        // Dispatch balance change event
        WalletBalanceChange::dispatch($this, $before, $this->getMoneyAmount());
    }

    /**
     * Deposit the given amount to the wallet.
     *
     * Wallet balance should always be modified using the `withdraw` and
     * `deposit` functions to ensure integrety.
     *
     * This does not create a corresponding transaction or mutation.
     *
     * @param number $amount The amount to deposit to this wallet.
     *
     * @throws \Exception Throws an exception if the amount is negative or zero.
     */
    public function deposit($amount) {
        // Assert the amount is positive
        if($amount <= 0)
            throw new \Exception("Failed to deposit to wallet, amount is <= 0");

        // We must be in a database transaction
        assert_transaction();

        $before = $this->getMoneyAmount();

        // Increment the balance
        $this->increment('balance', $amount);

        // Dispatch balance change event
        WalletBalanceChange::dispatch($this, $before, $this->getMoneyAmount());
    }

    /**
     * Transfer the given amount to the given wallet.
     *
     * This does not create a corresponding transaction or mutation.
     *
     * @param number $amount The amount to transfer to the given wallet.
     * @param Wallet $wallet The wallet to transfer to.
     * @param boolean [$sameEconomy=true] True to require the wallets are in the
     *      same economy.
     *
     * @throws \Exception Throws an exception if the given amount is negative or
     * zero.
     */
    public function transfer($amount, $wallet, $sameEconomy = true) {
        // Assert the amount is positive
        if($amount <= 0)
            throw new \Exception("Failed to transfer money to wallet, amount is <= 0");

        // Verify the currency and economy
        if($this->currency_id != $wallet->currency_id)
            throw new \Exception("Failed to transfer money to wallet, currencies differ");
        if($sameEconomy && $this->economy_id != $wallet->economy_id)
            throw new \Exception("Failed to transfer money to wallet, economies differ");

        // Increment the balance
        $this->withdraw($amount);
        $wallet->deposit($amount);
    }

    /**
     * Migrate all transactions from this wallet, to the target wallet.
     *
     * This also changes the balance in both balance to reflect the transferred
     * transactions.
     *
     * @param Wallet $target The target wallet.
     */
    public function migrateTransactions(Wallet $target) {
        $self = $this;
        DB::transaction(function() use(&$self, $target) {
            // Query the amount
            $amount = -$self
                ->walletMutations()
                ->join('mutation', function ($join) {
                    $join->on('mutation.mutationable_id', '=', 'mutation_wallet.id')
                        ->where('mutation.mutationable_type', MutationWallet::class);
                })
                ->where('mutation.state', Mutation::STATE_SUCCESS)
                ->sum('amount');

            // Move transactions to target wallet
            $self->walletMutations()->update([
                'mutation_wallet.wallet_id' => $target->id,
            ]);

            // Update wallet balances
            if($amount > 0) {
                $self->withdraw($amount);
                $target->deposit($amount);
            } else if($amount < 0) {
                $self->deposit(-$amount);
                $target->withdraw(-$amount);
            }
        });
    }

    /**
     * This method determines whether this wallet can be deleted.
     * This does not involve any permission checking. Instead, it ensures there
     * is no balance left in this wallet.
     *
     * Blocking:
     * - non-zero wallet balance
     *
     * @return boolean True if it can be deleted, false if not.
     */
    public function canDelete() {
        return $this->balance == 0;
    }

    /**
     * Trace back the balance at the given date and time by walking through all
     * transactions and mutations in reverse.
     *
     * This is a costly operation, especially when crossing longer periods. Use
     * this with care.
     *
     * If a date in the future is given, the current balance is returned.
     *
     * @param \Carbon $at The date to trace and get the balance to.
     *
     * @return number The balance at the given time.
     */
    public function traceBalance($at) {
        // Get all mutation changes in this period, and sum the amounts
        $change = $this
            ->mutations()
            ->type(MutationWallet::class, false)
            ->where('mutation.created_at', '>=', $at)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->sum('mutation.amount');

        // Apply the delta to the current balance, return the result
        return $this->balance + $change;
    }

    /**
     * Get receipt data, used to render a receipt for this wallet for a given
     * period.
     *
     * This is a costly operation, especially when crossing longer periods. Use
     * this with care.
     *
     * @param bool $nullIfEmpty Return null if the receipt is empty.
     * @param ?Carbon $from Start period for the receipt, defaults to 1 month.
     * @param ?Carbon $to End period for the receipt, defaults to current time.
     * @param bool [$trimFrom=true] Whehter to trim the period start to the
     *      first mutation in the specified period.
     *
     * @return array The receipt data.
     */
    public function getReceiptData(bool $nullIfEmpty = true, ?Carbon $from = null, ?Carbon $to = null, $trimFrom = false) {
        $from ??= now()->subMonth();
        $to ??= now();

        $products = [];
        $paymentAmount = new MoneyAmountBag();
        $balanceImportAmount = new MoneyAmountBag();
        $magicAmount = new MoneyAmountBag();
        $totalAmount = new MoneyAmountBag();

        // Get all mutation changes in this period, and sum the amounts
        $mutations = $this
            ->mutations()
            ->where('mutation.mutationable_type', '!=', MutationWallet::class)
            ->where('mutation.created_at', '>=', $from)
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->get();

        // Return null early if we have no transactions
        if($nullIfEmpty && $mutations->isEmpty())
            return null;

        // Trim the from date
        if($trimFrom)
            $from = $from->max($mutations->min('created_at'));

        // Handle each mutation
        foreach($mutations as $mutation) {
            // Collect products
            if($mutation->mutationable_type == MutationProduct::class) {
                $mutation_product = $mutation->mutationable;
                if(!isset($products[$mutation_product->product_id])) {
                    $products[$mutation_product->product_id] = [
                        'product' => $mutation_product->product,
                        'cost' => $mutation->getMoneyAmount()->neg()->toBag(),
                        'quantity' => $mutation_product->quantity,
                    ];
                } else {
                    $products[$mutation_product->product_id]['cost']->add($mutation->getMoneyAmount()->neg());
                    $products[$mutation_product->product_id]['quantity'] += $mutation_product->quantity;
                }
            }

            // Collect top-ups
            if($mutation->mutationable_type == MutationPayment::class)
                $paymentAmount->add($mutation->getMoneyAmount()->neg());

            // Collect balance imports
            if($mutation->mutationable_type == MutationBalanceImport::class)
                $balanceImportAmount->add($mutation->getMoneyAmount()->neg());

            // Collect magic
            if($mutation->mutationable_type == MutationMagic::class)
                $magicAmount->add($mutation->getMoneyAmount()->neg());
        }

        // Build product list
        $products = collect($products)
            ->map(function($p) use(&$totalAmount) {
                $totalAmount->addBag($p['cost']);
                return [
                    'name' => $p['product']?->displayName() ?? __('pages.products.deletedProduct'),
                    'cost' => $p['cost'],
                    'quantity' => $p['quantity'],
                ];
            })
            ->sortByDesc('quantity');

        // Build list of other items
        $others = collect();
        if(!$paymentAmount->isZero()) {
            $totalAmount->addBag($paymentAmount);
            $others->push([
                'name' => __('mail.receipts.topUps'),
                'cost' => $paymentAmount,
            ]);
        }
        if(!$balanceImportAmount->isZero()) {
            $totalAmount->addBag($balanceImportAmount);
            $others->push([
                'name' => __('mail.receipts.balanceImports'),
                'cost' => $balanceImportAmount,
            ]);
        }
        if(!$magicAmount->isZero()) {
            $totalAmount->addBag($magicAmount);
            $others->push([
                'name' => __('mail.receipts.magic'),
                'cost' => $magicAmount,
            ]);
        }

        // Return null if empty
        if($nullIfEmpty && $products->isEmpty() && $others->isEmpty())
            return null;

        return [
            'from' => $from,
            'to' => $to,
            'products' => $products,
            'others' => $others,
            'total' => $totalAmount,
        ];
    }

    /**
     * Make a prediction of average monthly costs for the user.
     *
     * This internally goes through all product mutations in the past three months.
     *
     * This is a costly operation, especially when crossing longer periods. Use
     * this with care.
     *
     * The cost is returned as positive value.
     *
     * @return number The balance at the given time.
     */
    public function predictMonthlyCosts() {
        // Get sum of product mutation costs in the past three months
        $amount = $this
            ->mutations()
            ->type(MutationProduct::class, false)
            ->where('mutation.created_at', '>=', now()->subMonths(3))
            ->where('mutation.state', Mutation::STATE_SUCCESS)
            ->where('mutation.amount', '<', 0)
            ->sum('mutation.amount');
        $amount = -floatval($amount);

        // Construct money amount with approximate monthly costs
        $money_amount = $this->getMoneyAmount();
        $money_amount->amount = round($amount / 3.0, 2);
        $money_amount->approximate = true;

        return $money_amount;
    }

    /**
     * Check whether the currently authenticated user has permission to view this
     * wallet.
     *
     * @return boolean True if the user can view this wallet, false if not.
     */
    public function hasViewPermission() {
        return $this->hasPermission(false);
    }

    /**
     * Check whether the currently authenticated user has permission to manage
     * this wallet.
     *
     * @return boolean True if the user can manage this wallet, false if not.
     */
    public function hasManagePermission() {
        return $this->hasPermission(true);
    }

    /**
     * Check whether the currently authenticated user has permission to view this
     * wallet.
     *
     * Note: this is expensive.
     *
     * @param bool [$manage=true] True to check for management permissions,
     *      false for just viewing permission.
     * @return boolean True if the user can view this wallet, false if not.
     */
    private function hasPermission($manage = true) {
        // The user must be authenticated
        $barauth = barauth();
        if(!$barauth->isAuth())
            return false;
        $user = $barauth->getUser();

        // User is fine if he owns the wallet
        if($this->economyMember->user_id == $user->id)
            return true;

        // User is fine if has community permissions
        $community = $this->economyMember->economy->community;
        if(app('perms')->evaluate(CommunityRoles::presetManager(), $community, null))
            return true;

        return false;
    }
}

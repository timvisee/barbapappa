<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Traits\HasPassword;
use App\Traits\HasSlug;
use App\Traits\Joinable;
use App\Utils\EmailRecipient;
use App\Utils\SlugUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Bar model.
 *
 * @property int id
 * @property int community_id
 * @property int economy_id
 * @property string name
 * @property bool visible
 * @property bool public
 * @property string|null password
 * @property string|null slug
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Bar extends Model {

    use HasPassword, HasSlug, Joinable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    protected $fillable = [
        'economy_id',
        'name',
        'slug',
        'password',
        'visible',
        'public',
    ];

    /**
     * A scope for only showing bars that have been defined as visible by the
     * owner.
     */
    public function scopeVisible($query) {
        $query->where('visible', true);
    }

    /**
     * Find the bar in a smart manner, using the slug if a slug is given.
     *
     * @param string $id The bar ID or slug.
     *
     * @return Bar The bar if found.
     */
    public static function smartFindOrFail($id) {
        if(SlugUtils::isValid($id))
            return Bar::slugOrFail($id);
        else
            return Bar::findOrFail($id);
    }

    /**
     * Get the community this bar is part of.
     *
     * @return The community.
     */
    public function community() {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the economy this bar uses.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * A list of users that joined this bar.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return List of joined users.
     */
    public function users($pivotColumns = [], $withTimestamps = true) {
        // Query relation
        $query = $this->belongsToMany(
                User::class,
                'bar_user',
                'bar_id',
                'user_id'
            );

        // With pivot columns
        if(!empty($pivotColumns))
            $query = $query->withPivot($pivotColumns);

        // With timestamps
        if($withTimestamps)
            $query = $query->withTimestamps();

        return $query;
    }

    /**
     * Get a relation to all product mutations of products that were bought at
     * this bar.
     *
     * @return Relation to product mutations at this bar.
     */
    public function productMutations() {
        return $this->hasMany(MutationProduct::class);
    }

    /**
     * Select the top products that were bought in any of the mutations in the
     * given list. Some products may be excluded. The limit of the products to
     * the return should be given.
     *
     * @param array|null [$mutation_ids=null] A list of IDs of mutations to
     *      search in.
     * @param array|null [$exclude_product_ids=null] A list of product IDs to
     *      exclude from the search.
     * @param int $limit The maximum number of products to return.
     *
     * @return array An array of product models that were found.
     */
    function selectTopProducts($mutation_ids = null, $exclude_product_ids = null, $limit = 0) {
        // Return nothing if limit is zero
        if($limit <= 0)
            return collect();

        // Build a sub query for selecting the last 100 product mutations
        $lastProducts = MutationProduct::select('product_id', 'quantity');
        if($mutation_ids != null)
            $lastProducts = $lastProducts->whereIn('mutation_id', $mutation_ids);
        if($exclude_product_ids != null)
            $lastProducts = $lastProducts->whereNotIn('product_id', $exclude_product_ids);
        $lastProducts = $lastProducts
            ->orderBy('created_at', 'DESC')
            ->limit(100);

        // Build a query for counting how often products were bought
        $productCounts = DB::table(DB::raw("({$lastProducts->toSql()}) AS m"))
            ->mergeBindings($lastProducts->getQuery())
            ->select(DB::raw('SUM(quantity)'))
            ->whereRaw('m.product_id = products.id');

        // Select the top bought products
        $products = Product::select('*')
            ->selectSub($productCounts, 'count')
            ->orderBy('count', 'DESC')
            // TODO: ->havingRaw('count > 0')
            ->limit($limit);

        // Filter products not being bought ever
        return $products
            ->get()
            ->filter(function($p) {
                return $p->count > 0;
            });
    }

    /**
     * Select the last products that were bought in any of the mutations in the
     * given list. Some products may be excluded. The limit of the products to
     * the return should be given.
     *
     * @param array $mutation_ids A list of IDs of mutations to search in.
     * @param array $exclude_product_ids A list of product IDs to exclude from
     *      the search.
     * @param int $limit The maximum number of products to return.
     *
     * @return array An array of product models that were found.
     */
    function selectLastProducts($mutation_ids, $exclude_product_ids, $limit) {
        // Return nothing if limit is zero
        if($limit <= 0)
            return collect();

        // TODO: use join to limit economy instead

        return MutationProduct::whereIn('mutation_id', $mutation_ids)
            ->whereNotIn('product_id', $exclude_product_ids)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->with('product')
            ->get()
            ->pluck('product');
    }

    /**
     * Build a list of products to show in the quick buy list.
     * This list is personalized for the logged in user, and prefers products on
     * top that the user often buys.
     *
     * TODO: better describe what really happens
     *
     * @return array A list of products.
     */
    public function quickBuyProducts() {
        // Get the last 100 product mutation IDs for the current user
        $mutation_ids = $this
            ->economy
            ->mutations()
            ->select('id')
            ->where('owner_id', barauth()->getUser()->id)
            ->where('type', Mutation::TYPE_PRODUCT)
            ->orderBy('created_at', 'DESC')
            ->limit(100)
            ->get()
            ->pluck('id');

        // Get top 5 user bought products in last 100 mutations
        $products = $this->selectTopProducts($mutation_ids, null, 5);

        // Add products last bought by user not in list already to total of 8
        $products = $products->merge(
            $this->selectLastProducts(
                $mutation_ids,
                $products->pluck('id'),
                8 - $products->count()
            )
        );

        if($products->count() < 8) {
            // Get the last 100 product mutation IDs for any user
            $mutation_ids = $this
                ->economy
                ->mutations()
                ->select('id')
                ->where('type', Mutation::TYPE_PRODUCT)
                ->orderBy('created_at', 'DESC')
                ->limit(100)
                ->get()
                ->pluck('id');

            // Add top products by any user in last 100 mutations not already in list to total of 8
            $products = $products->merge(
                $this->selectTopProducts(
                    $mutation_ids,
                    $products->pluck('id'),
                    8 - $products->count()
                )
            );
        }

        // Fill with random products
        if($products->count() < 8) {
            // Add top products by any user in last 100 mutations not already in list to total of 8
            $products = $products->merge(
                $this->economy
                    ->products()
                    ->whereNotIn('id', $products->pluck('id'))
                    ->limit(8 - $products->count())
                    ->get()
            );
        }

        return $products;
    }
}

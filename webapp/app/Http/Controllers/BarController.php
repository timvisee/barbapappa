<?php

namespace App\Http\Controllers;

use App\Exports\BarPurchaseHistoryExport;
use App\Helpers\ValidationDefaults;
use App\Models\Bar;
use App\Models\EconomyMember;
use App\Models\InventoryItemChange;
use App\Models\Mutation;
use App\Models\MutationProduct;
use App\Models\MutationWallet;
use App\Models\Transaction;
use App\Models\UuidCheck;
use App\Perms\BarRoles;
use App\Services\Auth\Authenticator as UserAuthenticator;
use App\Utils\MoneyAmountBag;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Validator;
use \Excel;

class BarController extends Controller {

    use HasFactory;

    /**
     * The limit for advanced buy products to show.
     */
    const ADVANCED_BUY_PRODUCT_LIMIT = 8;

    /**
     * Time after which transaction UUID claims expire.
     */
    const UUID_CHECK_EXPIRE_SECONDS = 3 * 30 * 24 * 60 * 60;

    /**
     * Amount of time in seconds to separate summary sections.
     */
    const SUMMARY_SEPARATE_DELAY_SECONDS = 60 * 60 * 2.5;

    /**
     * Bar creation page.
     *
     * @return Response
     */
    public function create() {
        // Get the community
        $community = \Request::get('community');

        // An economy must be created first if not available
        if($community->economies()->limit(1)->count() == 0) {
            // TODO: redirect the user back to this page after economy creation
            return redirect()
                ->route('community.economy.create', ['communityId' => $community->id])
                ->with('error', __('pages.bar.mustCreateEconomyFirst'));
        }

        return view('bar.create');
    }

    /**
     * Bar create page.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doCreate(Request $request) {
        // Get the community
        $community = \Request::get('community');

        // Validate
        $this->validate($request, [
            'economy' => ['required', ValidationDefaults::communityEconomy($community)],
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug(),
            'description' => 'nullable|' . ValidationDefaults::DESCRIPTION,
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
            'low_balance_text' => 'nullable|' . ValidationDefaults::DESCRIPTION,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        // Create the bar
        $bar = $community->bars()->create([
            'economy_id' => $request->input('economy'),
            'name' => $request->input('name'),
            'slug' => $request->has('slug') ? $request->input('slug') : null,
            'description' => $request->input('description'),
            'enabled' => is_checked($request->input('enabled')),
            'password' => $request->has('password') ? $request->input('password') : null,
            'show_explore' => is_checked($request->input('show_explore')),
            'show_community' => is_checked($request->input('show_community')),
            'show_history' => is_checked($request->input('show_history')),
            'show_tallies' => is_checked($request->input('show_tallies')),
            'self_enroll' => is_checked($request->input('self_enroll')),
            'low_balance_text' => $request->input('low_balance_text'),
        ]);

        // Automatically join if checked
        if(is_checked($request->input('join')))
            $bar->join(barauth()->getUser(), BarRoles::ADMIN);

        // Redirect the user to the account overview page
        return redirect()
            ->route('bar.manage', ['barId' => $bar->human_id])
            ->with('success', __('pages.bar.created'));
    }

    /**
     * Bar show page.
     *
     * @return Response
     */
    public function show($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Show info page if user does not have user role
        if(!perms(Self::permsUser()) || !$bar->isJoined($user))
            return $this->info($barId);

        // Update the visit time for this member
        $member = $bar->memberUsers(['visited_at'], false)
            ->where('user_id', $user->id)
            ->first();
        if($member != null) {
            $member->pivot->visited_at = new \DateTime();
            $member->pivot->save();
        }

        // Show the bar page
        return view('bar.show')
            ->with('economy', $bar->economy)
            ->with('joined', $bar->isJoined($user))
            ->with('mustVerify', $user->needsToVerifyEmail())
            ->with('userBalance', $bar->economy->calcUserBalance());
    }

    /**
     * Bar mini history page.
     *
     * @return Response
     */
    public function widgetHistory($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Show info page if user does not have user role
        if(!perms(Self::permsUser()) || !$bar->isJoined($user))
            throw new \Exception('No permission');

        // List the last product mutations
        $show_history = ($bar->show_history && perms(Self::permsUser())) || perms(Self::permsManage());
        if($show_history) {
            $productMutations = $bar
                ->productMutations()
                ->withTrashed()
                ->with('mutation')
                ->latest()
                ->where('created_at', '>', now()->subSeconds(config('bar.bar_recent_product_transaction_period')))
                ->limit(5)
                ->get();
        } else {
            $productMutations = collect();
        }

        // Show the bar page
        return view('bar.include.miniHistory')
            ->with('productMutations', $productMutations);
    }

    /**
     * Bar info page.
     *
     * @return Response
     */
    public function info($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Show the bar page
        return view('bar.info')
            ->with('economy', $bar->economy)
            ->with('page', last(explode('.', \Request::route()->getName())))
            ->with('joined', $bar->isJoined($user))
            ->with('mustVerify', $user->needsToVerifyEmail());
    }

    /**
     * Bar membership page.
     *
     * @return Response
     */
    public function member($barId) {
        // Get the bar
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();
        $bar_member = $bar->members()->user($user)->first();
        $economy = $bar->economy;
        $economy_member = $economy->members()->user($user)->first();

        // Show the bar member page
        return view('bar.member')
            ->with('bar_member', $bar_member)
            ->with('economy_member', $economy_member);
    }

    /**
     * Bar membership edit page.
     *
     * @return Response
     */
    public function editMember($barId) {
        // Get the bar
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();
        $bar_member = $bar->members()->user($user)->first();
        $economy = $bar->economy;
        $economy_member = $economy->members()->user($user)->first();

        // Show the bar member edit page
        return view('bar.memberEdit')
            ->with('bar_member', $bar_member)
            ->with('economy_member', $economy_member);
    }

    /**
     * Bar membership edit page.
     *
     * @return Response
     */
    public function doEditMember(Request $request, $barId) {
        // Get the bar
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();
        $economy = $bar->economy;
        $economy_member = $economy->members()->user($user)->first();

        // Validate
        $this->validate($request, [
            'nickname' => 'nullable|' . ValidationDefaults::NICKNAME,
            'tags' => 'nullable|' . ValidationDefaults::USER_TAGS,
        ]);

        $economy_member->nickname = $request->input('nickname');
        $economy_member->tags = $request->input('tags');
        $economy_member->show_in_buy = is_checked($request->input('show_in_buy'));
        $economy_member->show_in_kiosk = is_checked($request->input('show_in_kiosk'));
        $economy_member->save();

        // Redirect the user to member page
        return redirect()
            ->route('bar.member', ['barId' => $bar->human_id])
            ->with('success', __('pages.barMember.updated'));
    }

    /**
     * Bar membership reset page.
     *
     * @return Response
     */
    public function doResetMember(Request $request, $barId) {
        // Get the bar
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();
        $economy = $bar->economy;
        $economy_member = $economy->members()->user($user)->first();

        // Reset properties to default
        $economy_member->resetUserProperties();
        $economy_member->save();

        // Redirect the user to member page
        return redirect()
            ->route('bar.member', ['barId' => $bar->human_id])
            ->with('success', __('pages.barMember.updated'));
    }

    /**
     * Bar stats page.
     *
     * @return Response
     */
    public function stats($barId) {
        // Get the bar
        $bar = \Request::get('bar');

        // Gather some stats
        $memberCountHour = $bar
            ->memberUsers()
            ->wherePivot('visited_at', '>=', Carbon::now()->subHour())
            ->count();
        $memberCountDay = $bar
            ->memberUsers()
            ->wherePivot('visited_at', '>=', Carbon::now()->subDay())
            ->count();
        $memberCountWeek = $bar
            ->memberUsers()
            ->wherePivot('visited_at', '>=', Carbon::now()->subWeek())
            ->count();
        $memberCountMonth = $bar
            ->memberUsers()
            ->wherePivot('visited_at', '>=', Carbon::now()->subMonth())
            ->count();
        $productCount = $bar->economy->products()->count();
        // TODO: only count products with mutation having success state
        $soldProductCount = $bar->productMutations()->sum('quantity');
        $transactionCount = $bar->transactionCount();
        $soldProductCountHour = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->sum('quantity');
        $soldProductCountDay = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->sum('quantity');
        $soldProductCountWeek = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->sum('quantity');
        $soldProductCountMonth = $bar
            ->productMutations()
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->sum('quantity');

        // Show the bar page
        return view('bar.stats')
            ->with('memberCountHour', $memberCountHour)
            ->with('memberCountDay', $memberCountDay)
            ->with('memberCountWeek', $memberCountWeek)
            ->with('memberCountMonth', $memberCountMonth)
            ->with('productCount', $productCount)
            ->with('soldProductCount', $soldProductCount)
            ->with('transactionCount', $transactionCount)
            ->with('soldProductCountHour', $soldProductCountHour)
            ->with('soldProductCountDay', $soldProductCountDay)
            ->with('soldProductCountWeek', $soldProductCountWeek)
            ->with('soldProductCountMonth', $soldProductCountMonth);
    }

    /**
     * Bar history page.
     *
     * @return Response
     */
    public function history($barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');

        // List the last product mutations
        $productMutations = $bar
            ->productMutations()
            ->withTrashed()
            ->with('mutation')
            ->latest()
            ->paginate(50);

        // Show the bar page
        return view('bar.history')
            ->with('productMutations', $productMutations);
    }

    /**
     * Bar export history page.
     *
     * @return Response
     */
    public function exportHistory($barId) {
        // Get the bar
        $bar = \Request::get('bar');

        $firstDate = (new Carbon($bar->productMutations()->with('mutation')->min('mutation_product.created_at')))
            ->toDateString();
        $lastDate = today()->toDateString();

        return view('bar.historyExport')
            ->with('firstDate', $firstDate)
            ->with('lastDate', $lastDate);
    }

    /**
     * Bar export history page.
     *
     * @return Response
     */
    public function doExportHistory(Request $request, $barId) {
        // Validate
        $this->validate($request, [
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date',
            'format' => 'required|' . ValidationDefaults::exportTypes(),
        ]);

        // Get the bar
        $bar = \Request::get('bar');

        $headers = is_checked($request->input('headers'));
        $fromDate = $request->input('date_from');
        $toDate = $request->input('date_to');
        $format = $request->input('format');
        $fileName = 'barapp-purchases.' . collect(config('bar.spreadsheet_export_types'))->firstWhere('type', $format)['extension'];

        return Excel::download(
            new BarPurchaseHistoryExport($headers, $bar->id, $fromDate, $toDate),
            $fileName,
            $format,
        );
    }

    /**
     * Bar summary page.
     *
     * @return Response
     */
    public function summary(Request $request, $barId) {
        $CHUNK_SIZE = 100;
        $MAX_ITEMS = 1000;

        // Get the bar and session user
        $bar = \Request::get('bar');

        // Validate
        $this->validate($request, [
            'time_from' => 'nullable|date|after_or_equal:' . $bar->created_at->floorDay()->toDateTimeString() . '|before_or_equal:time_to|before_or_equal:' . now()->ceilMinute()->toDateTimeString(),
            'time_to' => 'nullable|date|after_or_equal:' . $bar->created_at->floorDay()->toDateTimeString() . '|after_or_equal:time_from|before_or_equal:' . now()->ceilMinute()->toDateTimeString(),
        ]);
        $specificPeriod = $request->query('time_from') != null && $request->query('time_to') != null;

        // Get items from range or just recent items
        if($specificPeriod) {
            $timeFrom = $request->query('time_from');
            $timeFrom = $timeFrom != null ? Carbon::parse($timeFrom) : null;
            $timeTo = $request->query('time_to');
            $timeTo = $timeTo != null ? Carbon::parse($timeTo) : null;
            $timeFrom ??= ($timeTo ?? now())->clone()->subMonth()->max($bar->created_at);
            $timeTo ??= now();
            $timeTo = $timeTo->min(now());

            $productMutations = $bar
                ->productMutations()
                ->withTrashed()
                ->with('mutation')
                ->latest()
                ->where('created_at', '>=', $timeFrom)
                ->where('created_at', '<=', $timeTo)
                ->limit($MAX_ITEMS)
                ->get();
        } else {
            // Build list of recent purchases
            $productMutations = collect();
            for($offset = 0; $offset < $MAX_ITEMS; $offset += $CHUNK_SIZE) {
                $chunk = $bar
                    ->productMutations()
                    ->withTrashed()
                    ->with('mutation')
                    ->latest()
                    ->offset($offset)
                    ->limit($CHUNK_SIZE)
                    ->get();

                // If chunk is empty, we're done
                if($chunk->isEmpty())
                    break;

                // If start of chunk is too old, don't include any of it and we're done
                $last = $productMutations->last();
                if($last != null) {
                    $delay = $last->created_at->diffAsCarbonInterval($chunk->first()->created_at);
                    if($delay->total('seconds') >= Self::SUMMARY_SEPARATE_DELAY_SECONDS) {
                        break;
                    }
                }

                // Try to find time gap in chunk, if found only include upto that point and we're done
                $end = false;
                for($i = 0; $i < $chunk->count() - 1; $i++) {
                    $delay = $chunk[$i]->created_at->diffAsCarbonInterval($chunk[$i + 1]->created_at);
                    if($delay->total('seconds') >= Self::SUMMARY_SEPARATE_DELAY_SECONDS) {
                        $productMutations = $productMutations->concat($chunk->take($i + 1));
                        $end = true;
                        break;
                    }
                }
                if($end)
                    break;

                $productMutations = $productMutations->concat($chunk);

                // If chunk was smaller requested size we've reached the end
                if($chunk->count() < $CHUNK_SIZE)
                    break;
            }

            $timeFrom = $productMutations->map(function($productMutation) {
                return $productMutation->created_at;
            })
            ->min() ?? now();
            $timeTo = $productMutations->map(function($productMutation) {
                return $productMutation->updated_at ?? $productMutation->created_at;
            })
            ->max() ?? now();
        }
        $showingLimited = $productMutations->count() >= $MAX_ITEMS;

        // Create summary of purchases
        $summary = $productMutations
            // Group product mutations by user
            ->groupBy('mutation.owner.id')
            ->map(function($productMutations) use($bar) {
                // Group product mutations by product
                $products = $productMutations
                    ->groupBy('product.id')
                    ->map(function($productMutations) {
                        $product = $productMutations->first()?->product;
                        $amount = new MoneyAmountBag($productMutations->map(function($productMutation) {
                            return $productMutation->getMoneyAmount();
                        }));

                        return [
                            'name' => $product ? $product->displayName() : __('pages.products.unknownProduct'),
                            'product' => $product,
                            'quantity' => $productMutations->sum('quantity'),
                            'anyDelayed' => $productMutations->contains(function($productMutation) {
                                return $productMutation?->mutation?->transaction?->isDelayed() ?? false;
                            }),
                            'anyInitiatedByKiosk' => $productMutations->contains(function($productMutation) {
                                return $productMutation?->mutation?->transaction?->initiated_by_kiosk ?? false;
                            }),
                            'amount' => $amount,
                            'amountRaw' => $amount->sumAmounts()->amount,
                        ];
                    })
                    ->sortBy('amountRaw');
                $amount = new MoneyAmountBag($products->map(function($product) {
                    return $product['amount'];
                }));

                $owner = $productMutations->first()?->mutation?->owner;
                if($owner != null) {
                    $member = $bar
                        ->members()
                        ->user($owner)
                        ->first();
                }

                return [
                    'owner' => $owner,
                    'member' => $member ?? null,
                    'newestUpdated' => $productMutations->first()->updated_at ?? $productMutations->first()->created_at,
                    'oldestUpdated' => $productMutations->last()->updated_at ?? $productMutations->last()->created_at,
                    'products' => $products,
                    'amount' => $amount,
                    'amountRaw' => $amount->sumAmounts()->amount,
                ];
            })
            ->sortBy('amountRaw');

        $amount = new MoneyAmountBag($summary->map(function($userSummary) {
            return $userSummary['amount'];
        }));

        // Show the purchase summary page
        return view('bar.summary')
            ->with('summary', $summary)
            ->with('showingLimited', $showingLimited)
            ->with('quantity', $productMutations->sum('quantity'))
            ->with('amount', $amount)
            ->with('specificPeriod', $specificPeriod)
            ->with('timeFrom', $timeFrom)
            ->with('timeTo', $timeTo);
    }

    /**
     * Bar tally summary page.
     *
     * @return Response
     */
    public function tally(Request $request, $barId) {
        $CHUNK_SIZE = 100;
        $MAX_ITEMS = 1000;

        // Get the bar and session user
        $bar = \Request::get('bar');

        // Ensure user has permission to show tallies
        // Needs to be enabled in bar or user needs to have manage permisisons
        $show_tallies = ($bar->show_tallies && perms(Self::permsUser())) || perms(Self::permsManage());
        if(!$show_tallies) {
            return redirect()
                ->route('bar.show', ['barId' => $barId])
                ->with('error', __('misc.noPermission'));
        }

        // Validate
        $this->validate($request, [
            'period' => 'nullable|in:day,week,month',
        ]);
        $period = $request->query('period');

        // Get items from range or just recent items
        if(isset($period)) {
            $timeTo = now();
            switch($period) {
            case 'day':
                $timeFrom = now()->subDay();
                break;
            case 'week':
                $timeFrom = now()->subWeek();
                break;
            case 'month':
                $timeFrom = now()->subMonth();
                break;
            default:
                throw new \Exception('Invalid period');
            }

            $productMutations = $bar
                ->productMutations()
                ->withTrashed()
                ->with('mutation')
                ->latest()
                ->where('created_at', '>=', $timeFrom)
                ->where('created_at', '<=', $timeTo)
                ->limit($MAX_ITEMS)
                ->get();
        } else {
            // Build list of recent purchases
            $productMutations = collect();
            for($offset = 0; $offset < $MAX_ITEMS; $offset += $CHUNK_SIZE) {
                $chunk = $bar
                    ->productMutations()
                    ->withTrashed()
                    ->with('mutation')
                    ->latest()
                    ->offset($offset)
                    ->limit($CHUNK_SIZE)
                    ->get();

                // If chunk is empty, we're done
                if($chunk->isEmpty())
                    break;

                // If start of chunk is too old, don't include any of it and we're done
                $last = $productMutations->last();
                if($last != null) {
                    $delay = $last->created_at->diffAsCarbonInterval($chunk->first()->created_at);
                    if($delay->total('seconds') >= Self::SUMMARY_SEPARATE_DELAY_SECONDS) {
                        break;
                    }
                }

                // Try to find time gap in chunk, if found only include upto that point and we're done
                $end = false;
                for($i = 0; $i < $chunk->count() - 1; $i++) {
                    $delay = $chunk[$i]->created_at->diffAsCarbonInterval($chunk[$i + 1]->created_at);
                    if($delay->total('seconds') >= Self::SUMMARY_SEPARATE_DELAY_SECONDS) {
                        $productMutations = $productMutations->concat($chunk->take($i + 1));
                        $end = true;
                        break;
                    }
                }
                if($end)
                    break;

                $productMutations = $productMutations->concat($chunk);

                // If chunk was smaller requested size we've reached the end
                if($chunk->count() < $CHUNK_SIZE)
                    break;
            }

            $timeFrom = $productMutations->map(function($productMutation) {
                return $productMutation->created_at;
            })
            ->min() ?? now();
            $timeTo = $productMutations->map(function($productMutation) {
                return $productMutation->updated_at ?? $productMutation->created_at;
            })
            ->max() ?? now();
        }
        $showingLimited = $productMutations->count() >= $MAX_ITEMS;

        // Sum up all tallies, grouped by user
        $tallies = $productMutations
            // Group product mutations by user
            ->groupBy('mutation.owner.id')
            ->map(function($productMutations) use($bar) {
                // Get total product quantity
                $quantity = $productMutations->sum('quantity');

                $owner = $productMutations->first()?->mutation?->owner;
                if($owner != null) {
                    $member = $bar
                        ->members()
                        ->user($owner)
                        ->first();
                }

                return [
                    'owner' => $owner,
                    'member' => $member ?? null,
                    'quantity' => $quantity,
                ];
            })
            ->sortByDesc('quantity');

        // Show the purchase tally page
        return view('bar.tally')
            ->with('tallies', $tallies)
            ->with('showingLimited', $showingLimited)
            ->with('quantity', $productMutations->sum('quantity'))
            ->with('period', $period)
            ->with('timeFrom', $timeFrom)
            ->with('timeTo', $timeTo);
    }

    /**
     * Bar management page.
     *
     * @return Response
     */
    public function manage($barId) {
        // Get the bar
        $bar = \Request::get('bar');

        $economy = $bar->economy;

        // Show the bar management page
        return view('bar.manage')
            ->with('economy', $economy)
            ->with('hasProduct', $economy->products()->limit(1)->count() > 0);
    }

    /**
     * Bar kiosk management page.
     *
     * @return Response
     */
    public function manageKiosk($barId) {
        // Show the bar kiosk management page
        return view('bar.manageKiosk');
    }

    /**
     * Bar edit page.
     *
     * @return Response
     */
    public function edit() {
        return view('bar.edit');
    }

    /**
     * Bar update endpoint.
     *
     * @param Request $request Request.
     *
     * @return Response
     */
    public function doEdit(Request $request) {
        // Get the bar
        $bar = \Request::get('bar');

        // Validate
        $this->validate($request, [
            // 'economy' => ['required', ValidationDefaults::communityEconomy($community)],
            'name' => 'required|' . ValidationDefaults::NAME,
            'slug' => 'nullable|' . ValidationDefaults::barSlug($bar),
            'description' => 'nullable|' . ValidationDefaults::DESCRIPTION,
            'password' => 'nullable|' . ValidationDefaults::SIMPLE_PASSWORD,
            'inventory' => ['nullable', ValidationDefaults::economyInventory($bar->economy)],
            'low_balance_text' => 'nullable|' . ValidationDefaults::DESCRIPTION,
        ], [
            'slug.regex' => __('pages.bar.slugFieldRegexError'),
        ]);

        $inventory_changed = $bar->inventory_id != $request->input('inventory');

        // Change the name properties
        // $bar->economy_id = $request->input('economy');
        $bar->name = $request->input('name');
        $bar->slug = $request->has('slug') ? $request->input('slug') : null;
        $bar->description = $request->input('description');
        $bar->enabled = is_checked($request->input('enabled'));
        $bar->password = $request->has('password') ? $request->input('password') : null;
        $bar->show_explore = is_checked($request->input('show_explore'));
        $bar->show_community = is_checked($request->input('show_community'));
        $bar->show_history = is_checked($request->input('show_history'));
        $bar->show_tallies = is_checked($request->input('show_tallies'));
        $bar->self_enroll = is_checked($request->input('self_enroll'));
        $bar->inventory_id = $request->input('inventory');
        $bar->low_balance_text = $request->input('low_balance_text');

        // Save the bar
        $bar->save();

        // Dispatch job to update product exhausted state if different inventory
        // is assigned
        if($inventory_changed)
            $bar->inventory?->dispatchUpdateProductExhausted();

        // Redirect the user to the account overview page
        return redirect()
            ->route('bar.manage', ['barId' => $bar->human_id])
            ->with('success', __('pages.bar.updated'));
    }

    /**
     * The bar join confirmation page.
     *
     * @return Response
     */
    public function join($barId) {
        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user has already joined
        if($bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Self enroll must be enabled
        if(!$bar->self_enroll)
            return redirect()
                ->route('bar.show', ['barId' => $barId])
                ->with('error', __('pages.bar.cannotSelfEnroll'));

        // Redirect to the bar page
        return view('bar.join');
    }

    /**
     * Make a user join the bar.
     *
     * @return Response
     */
    public function doJoin(Request $request, $barId) {
        // Get the bar, community and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user has already joined
        if($bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Self enroll must be enabled
        if(!$bar->self_enroll)
            return redirect()
                ->route('bar.show', ['barId' => $barId])
                ->with('error', __('pages.bar.cannotSelfEnroll'));

        // Handle the password if required
        if($bar->needsPassword($user)) {
            // Validate password field input
            $this->validate($request, [
                'code' => 'required|' . ValidationDefaults::CODE,
            ]);

            // Test the password
            if(!$bar->isPassword($request->input('code'))) {
                // Mark the error and retur
                $validator = Validator::make([], []);
                $validator->errors()->add('code', __('pages.bar.incorrectCode'));
                return back()
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        // Join the bar
        $bar->join($user);

        // Redirect to the bar page
        return redirect()
            ->route('bar.show', ['barId' => $barId])
            ->with('success', __('pages.bar.joinedThisBar'));
    }

    /**
     * The bar leave confirmation page.
     *
     * @return Response
     */
    public function leave($barId) {
        // TODO: make sure the user can leave this bar

        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Redirect to the bar page if the user isn't joined
        if(!$bar->isJoined($user))
            return redirect()
                ->route('bar.show', ['barId' => $barId]);

        // Redirect to the bar page
        return view('bar.leave');
    }

    /**
     * Make a user leave the bar.
     *
     * @return Response
     */
    public function doLeave($barId) {
        // TODO: make sure the user can leave the bar

        // Get the bar and user, leave
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Don't allow user to leave if user has wallet
        $hasWallet = $bar
            ->economy
            ->member($user)
            ->wallets()
            ->limit(1)
            ->count() > 0;
        $inOtherCommunityBars = $bar
            ->community
            ->bars()
            ->where('economy_id', $bar->economy_id)
            ->where('id', '<>', $bar->id)
            ->limit(1)
            ->count();
        if($hasWallet && !$inOtherCommunityBars)
            return redirect()
                ->route('bar.show', ['barId' => $barId])
                ->with('error', __('pages.bar.cannotLeaveHasWallets'));

        $bar->leave($user);

        // Redirect to the bar page
        return redirect()
            ->route('bar.show', ['barId' => $barId])
            ->with('success', __('pages.bar.leftThisBar'));
    }

    /**
     * API route for listing products in this bar, that a user can buy.
     *
     * // TODO: limit product fields returned here
     *
     * @return Response
     */
    public function apiBuyProducts($barId) {
        // Get the bar, current user and the search query
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Build a list of preferred currencies for the user
        // TODO: if there's only one currency, that is usable, use null to
        //       greatly simplify product queries
        $currencies = Self::userCurrencies($bar, $user);
        $currency_ids = $currencies->pluck('id');

        // Search, or use top products
        $search = \Request::get('q');
        if(!($search === null || trim($search) === ''))
            $products = $bar->economy->searchProducts($search, $currency_ids);
        else
            $products = $bar->economy->quickBuyProducts($currency_ids, Self::ADVANCED_BUY_PRODUCT_LIMIT);

        // Add formatted price fields
        $products = $products->map(function($product) use($currencies) {
            $product->price_display = $product->formatPrice($currencies);
            return $product;
        });

        return $products;
    }

    /**
     * API route for listing economy members in this bar, products can be bought for.
     *
     * // TODO: limit member fields returned here
     *
     * @return Response
     */
    public function apiBuyMembers($barId) {
        // Get the bar, current user and the search query
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();
        $economy = $bar->economy;
        $economy_member = $economy->members()->user($user)->firstOrFail();
        $search = \Request::query('q');
        $product_ids = json_decode(\Request::query('product_ids'));

        // Return a default user list, or search based on a given query
        if(($search === null || trim($search) === '')) {
            // Build list of members, add self
            $members = collect([$economy_member]);

            // Build a list of members most likely to buy new products
            // Specifically for selected products first, then fill gor any
            $limit = 7;
            if(!empty($product_ids))
                $members = $members->concat($this->getProductBuyMemberList(
                    $bar,
                    5,
                    [$user->id],
                    $product_ids
                ));
            $members = $members->concat($this->getProductBuyMemberList(
                $bar,
                $limit - $members->count(),
                $members->pluck('user_id')
            ));
        } else
            $members = $economy
                ->members()
                ->search($search)
                ->showInBuy()
                ->get()
                ->map(function($m) {
                    $m->registered = $m->user_id != null && $m->user_id > 0;
                    return $m;
                })
                ->sortBy([['registered', 'desc'], 'name'], SORT_NATURAL | SORT_FLAG_CASE)
                ->values();

        // Always appent current user to list if visible and not yet included
        $hasCurrent = $members->contains(function($m) use($economy_member) {
            return $m->id == $economy_member->id;
        });
        if(!$hasCurrent)
            $members[] = $economy_member;

        // Set and limit fields to repsond with
        $members = $members
            ->map(function($m) use($economy_member) {
                $m->name = $m->name;
                $data = $m->only(['id', 'name', 'me']);
                $data['me'] = $m->id == $economy_member->id;
                $data['registered'] = $data['me'] || $m->user_id != null && $m->user_id > 0;
                return $data;
            });

        return $members;
    }

    /**
     * Get a list of economy members that are most likely to buy new products.
     * This is shown in the advanced product buying page.
     *
     * A list of product IDs may be given to limit the most lickly buy hunting
     * to just those products.
     *
     * @param Bar $bar The bar to get a list of users for.
     * @parma int $limit The limit of users to return, might be less.
     * @param int[]|null [$ignore_user_ids] List of user IDs to ignore.
     * @param int[]|null [$product_ids] List of product IDs to prefer.
     *
     * @return EconomyMember[]
     */
    private function getProductBuyMemberList(Bar $bar, $limit, $ignore_user_ids = null, $product_ids = null) {
        // Return nothing if the limit is too low
        if($limit <= 0)
            return collect();

        // Find other users that recently made a transaction with these products
        $query = $bar
            ->transactions()
            ->latest('mutation.updated_at')
            ->whereNotNull('mutation.owner_id')
            ->whereNotIn('mutation.owner_id', $ignore_user_ids);

        // Limit to specific product IDs
        if(!empty($product_ids))
            $query = $query->whereIn('mutation_product.product_id', $product_ids);

        // Fetch transaction details for last 100 relevant transactions
        $transactions = $query
            ->limit(100)
            ->get(['mutation.owner_id', 'mutation_product.quantity', 'mutation.updated_at']);

        // List user IDs sorted by most bought
        $user_ids = $transactions
            ->reduce(function($list, $item) {
                $key = strval($item->owner_id);
                if(isset($list[$key]))
                    $list[$key] += $item->quantity;
                else
                    $list[$key] = $item->quantity;
                return $list;
            }, collect())
            ->sort()
            ->reverse()
            ->take($limit)
            ->keys();

        // Fetch and return the members for these users
        $econ_members = $bar
            ->economy
            ->members()
            ->showInBuy()
            ->whereIn('user_id', $user_ids)
            ->limit($limit)
            ->get();
        return $user_ids
            ->map(function($user_id) use($econ_members) {
                return $econ_members->firstWhere('user_id', $user_id);
            })
            ->filter(function($member) {
                return $member != null;
            });
    }

    /**
     * API route for buying products in the users advanced buying cart.
     *
     * @return Response
     */
    public function apiBuySelfInstant(Request $request) {
        // TODO: we receive 'count', what is that?

        // Get the bar, current user and the search query
        $bar = \Request::get('bar');
        $economy = $bar->economy;
        $buyData = $request->post();
        $self = $this;

        // Error if bar is disabled
        if(!$bar->enabled) {
            return response()->json([
                'message' => __('pages.bar.disabled'),
            ])->setStatusCode(403);
        }

        // Take product from request buy data
        if(isset($buyData['product'])) {
            $product = collect($buyData['product']);
            $uuid = $buyData['uuid'];
            $initiated_at_timestamp = $buyData['initiated_at'] ?? null;
        } else {
            throw new \Exception('Invalid buy data');
        }

        // We want to prevent replaying transactions
        // If UUID is already used, assume transaction is already processed
        if($uuid != null && UuidCheck::hasUuid($uuid)) {
            // TODO: log this
            return [];
        }

        // Fetch product
        // TODO: what to do here on failure
        $product = $economy
            ->products()
            ->withTrashed()
            ->findOrFail($product['id']);

        // Buy product
        $details = null;
        DB::transaction(function() use($self, $uuid, $bar, $product, &$details) {
            // Claim UUID or return early
            if($uuid != null) {
                $uuid_expire_at = now()->addSeconds(Self::UUID_CHECK_EXPIRE_SECONDS);
                if(!UuidCheck::claim($uuid, $uuid_expire_at, false))
                    // TODO: is this return broken?
                    return [];
            }

            // Quick buy the product
            $details = $self->quickBuyProduct($bar, $product);
        });

        // Format the price
        $transaction = $details['transaction'];
        $cost = $details['currency']->format($details['price']);

        // Build a success message
        $msg = __('pages.bar.boughtProductForPrice', [
            'product' => $product->displayName(),
            'price' => $cost,
        ]) . '.';
        $msg .= ' <a href="' . route('transaction.undo', [
            'transactionId' => $transaction->id
        ]) . '">' . __('misc.undo') . '</a>';

        $userBalance = $economy->calcUserBalance();

        // Purcahse succesful, return stats
        return [
            'productCount' => 1,
            'userBalanceRaw' => $userBalance?->amount,
            'userBalanceText' => $userBalance?->formatAmount(),
        ];

//         // Do everything in a database transaction
//         $productCount = 0;
//         $userCount = $cart->count();
//         DB::transaction(function() use($bar, $economy, $cart, $self, &$productCount) {
//             // For each user, purchase the selected products
//             $cart->each(function($userItem) use($bar, $economy, $self, &$productCount) {
//                 $user = $userItem['user'];
//                 $products = collect($userItem['products']);

//                 // Retrieve user and product models from database
//                 $member = $economy->members()->showInBuy(true)->findOrFail($user['id']);
//                 $products = $products->map(function($product) use($economy) {
//                     $product['product'] = $economy->products()->findOrFail($product['product']['id']);
//                     return $product;
//                 });

//                 // Buy the products, increase product count
//                 $result = $self->buyProducts($bar, $member, $products);
//                 $productCount += $result['productCount'];
//             });

//         // Return some useful stats
//         return [
//             'productCount' => $productCount,
//             'userCount' => $userCount,
//         ];
    }

    /**
     * API route for buying products in the users advanced buying cart.
     *
     * @return Response
     */
    public function apiBuyBuy(Request $request) {
        // Get the bar, current user and the search query
        $bar = \Request::get('bar');
        $economy = $bar->economy;
        $cart = collect($request->post()['cart'] ?? []);
        $self = $this;

        // TODO: validate cart has items!

        // Error if bar is disabled
        if(!$bar->enabled) {
            return response()->json([
                'message' => __('pages.bar.disabled'),
            ])->setStatusCode(403);
        }

        // Do everything in a database transaction
        $productCount = 0;
        $userCount = $cart->count();
        DB::transaction(function() use($bar, $economy, $cart, $self, &$productCount) {
            // For each user, purchase the selected products
            $cart->each(function($userItem) use($bar, $economy, $self, &$productCount) {
                $user = $userItem['user'];
                $products = collect($userItem['products']);

                // Retrieve user and product models from database
                $member = $economy->members()->showInBuy(true)->findOrFail($user['id']);
                $products = $products->map(function($product) use($economy) {
                    $product['product'] = $economy->products()->findOrFail($product['product']['id']);
                    return $product;
                });

                // Buy the products, increase product count
                $result = $self->buyProducts($bar, $member, $products);
                $productCount += $result['productCount'];
            });
        });

        $userBalance = $economy->calcUserBalance();

        // Purcahse succesful, return stats
        return [
            'productCount' => $productCount,
            'userCount' => $userCount,
            'userBalanceRaw' => $userBalance?->amount,
            'userBalanceText' => $userBalance?->formatAmount(),
        ];
    }

    // TODO: describe
    // TODO: merges with recent product transactions
    // TODO: returns [transaction, currency, price]
    function quickBuyProduct(Bar $bar, $product) {
        // Get the user, and economy member
        $user = barauth()->getUser();
        $economy = $bar->economy;
        if(!$economy->isJoined($user))
            $economy->join($user);
        $economy_member = $economy->members()->user($user)->firstOrFail();

        // Build a list of preferred currencies for the user, filter currencies
        // with no price
        $currencies = Self::userCurrencies($bar, $user)
            ->filter(function($currency) use($product) {
                return $product->prices->contains('currency_id', $currency->id);
            });
        if($currencies->isEmpty())
            throw new \Exception("Could not quick buy product, no supported currencies");
        $currency_ids = $currencies->pluck('id');

        // Find the most recent product transaction within the quick buy merge
        // time
        //
        // It must be:
        // - Recent, within configured time
        // - Owned by the current user
        // - Only contain (from) wallet and (to) product mutations
        // - Product mutations must be in the current bar
        $last_transaction = $user
            ->transactions()
            ->where('created_at', '>=', Carbon::now()->subSeconds(config('bar.quick_buy_merge_timeout')))
            ->whereNotExists(function($query) use($bar) {
                $query->selectRaw('1')
                    ->fromRaw('mutation')
                    ->leftJoin('mutation_product', 'mutation_product.id', '=', 'mutation.mutationable_id')
                    ->whereRaw('mutation.transaction_id = transaction.id')
                    ->where(function($query) {
                        $query->where('mutationable_type', '<>', MutationWallet::class)
                            ->orWhere('amount', '<=', 0);
                    })
                    ->where(function($query) use($bar) {
                        $query->where('mutationable_type', '<>', MutationProduct::class)
                            ->orWhere('amount', '>', 0)
                            ->orWhere('mutation_product.bar_id', '<>', $bar->id);
                    });
            })
            ->latest()
            ->first();

        // Get or create a wallet for the user, get the price
        $wallet = $economy_member->getOrCreateWallet($currencies);
        $currency = $wallet->currency;
        $price = $product
            ->prices
            ->whereStrict('currency_id', $currency->id)
            ->first()
            ->price;

        // TODO: notify user if wallet is created?

        // Start a database transaction for the product transaction
        // TODO: create a nice generic builder for the actions below
        $out = null;
        DB::transaction(function() use($bar, $product, $user, $wallet, $currency, $price, $last_transaction, &$out) {
            // Create the transaction or use last transaction
            $transaction = $last_transaction ?? Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user->id,
            ]);

            // Determine whether the product was free
            $free = $price == 0;

            // Create the wallet mutation unless product is free
            $mut_wallet = null;
            if(!$free) {
                // Find an mutation for the wallet in this transaction
                $mut_wallet = $last_transaction != null
                    ? $transaction
                        ->mutations()
                        ->where('mutationable_type', MutationWallet::class)
                        ->whereExists(function($query) use($wallet) {
                            $query->selectRaw('1')
                                ->from('mutation_wallet')
                                ->whereRaw('mutation.mutationable_id = mutation_wallet.id')
                                ->where('wallet_id', $wallet->id);
                        })
                        ->first()
                    : null;

                // Create a new wallet mutation or update the existing
                if($mut_wallet == null) {
                    $mut_wallet = $transaction
                        ->mutations()
                        ->create([
                            'economy_id' => $bar->economy_id,
                            'mutationable_id' => 0,
                            'mutationable_type' => '',
                            'amount' => $price,
                            'currency_id' => $currency->id,
                            'state' => Mutation::STATE_SUCCESS,
                            'owner_id' => $user->id,
                        ]);
                    $mut_wallet->setMutationable(
                        MutationWallet::create([
                            'wallet_id' => $wallet->id,
                        ])
                    );
                } else
                    $mut_wallet->incrementAmount($price);
            }

            // Find an mutation for the product in this transaction
            $mut_product = $last_transaction != null
                ? $transaction
                    ->mutations()
                    ->where('mutationable_type', Mutationproduct::class)
                    ->whereExists(function($query) use($product) {
                        $query->selectRaw('1')
                            ->from('mutation_product')
                            ->whereRaw('mutation.mutationable_id = mutation_product.id')
                            ->where('product_id', $product->id);
                    })
                    ->first()
                : null;

            // Create a new product mutation or update the existing one
            if($mut_product == null) {
                // Create the product mutation
                $mut_product = $transaction
                    ->mutations()
                    ->create([
                        'economy_id' => $bar->economy_id,
                        'mutationable_id' => 0,
                        'mutationable_type' => '',
                        'amount' => -$price,
                        'currency_id' => $currency->id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $user->id,
                        'depend_on' => $mut_wallet?->id,
                    ]);
                $mut_product->setMutationable(
                    MutationProduct::create([
                        'product_id' => $product->id,
                        'bar_id' => $bar->id,
                        'quantity' => 1,
                    ])
                );
            } else {
                $mut_product->decrementAmount($price);
                $mut_product->mutationable()->increment('quantity');
            }

            // Update the wallet balance
            // TODO: do this by setting the mutation states instead
            if(!$free)
                $wallet->withdraw($price);

            // Undo inventory changes for this product mutation
            InventoryItemChange::mutationProduct($mut_product->mutationable)
                ->get()
                ->each(function($change) {
                    $change->undo();
                });

            // Update bar inventory
            if($bar->inventory != null) {
                $product->subtractFromInventory(
                    $bar->inventory,
                    $mut_product->mutationable->quantity,
                    $mut_product->mutationable,
                );
            }

            // Return the transaction
            $out = $transaction;
        });

        // Return the transaction details
        return [
            'transaction' => $out,
            'currency' => $currency,
            'price' => $price,
        ];
    }

    /**
     * Buy the given list of products for the given user.
     *
     * @param Bar $bar The bar to buy the products in.
     * @param EconomyMember $economy_member The economy member to buy the products for.
     * @param array $products [[quantity: int, product: Product]] List of
     *      products and quantities to buy.
     */
    // TODO: support paying in multiple currencies for different products at the same time
    // TODO: make a request when paying for other users
    function buyProducts(Bar $bar, EconomyMember $economy_member, $products) {
        $products = collect($products);

        // Build a list of preferred currencies for the member, filter currencies
        // with no price
        $currencies = Self::userCurrencies($bar, $economy_member)
            ->filter(function($currency) use($products) {
                $product = $products[0]['product'];
                return $product->prices->contains('currency_id', $currency->id);
            });
        if($currencies->isEmpty())
            throw new \Exception("Could not quick buy product, no supported currencies");
        $currency_ids = $currencies->pluck('id');

        // Get or create a wallet for the economy member, get the price
        $wallet = $economy_member->getOrCreateWallet($currencies);
        $currency = $wallet->currency;

        // Select the price for each product, find the total price
        $products = $products->map(function($item) use($wallet, $currency) {
            // The quantity must be 1 or more
            if($item['quantity'] < 1)
                throw new \Exception('Cannot buy product with quantity < 1');

            // Select price for this product
            $price = $item['product']
                ->prices
                ->whereStrict('currency_id', $currency->id)
                ->first()
                ->price;
            if($price == null)
                throw new \Exception('Product does not have price in selected currency');
            $item['priceEach'] = $price * 1;
            $item['priceTotal'] = $price * $item['quantity'];

            return $item;
        });
        $price = $products->sum('priceTotal');

        // TODO: notify user if wallet is created?

        // Get the user ID
        $user_id = $economy_member->user_id;

        // Determine whether to set different initiating user
        $initiated_by_id = null;
        $initiated_by_other = $user_id != barauth()->getUser()->id;
        if($initiated_by_other)
            $initiated_by_id = barauth()->getUser()->id;

        // Start a database transaction for the product transaction
        // TODO: create a nice generic builder for the actions below
        $out = null;
        $productCount = 0;
        DB::transaction(function() use($bar, $products, $user_id, $wallet, $currency, $price, &$out, &$productCount, $initiated_by_id, $initiated_by_other) {
            // TODO: last_transaction is used here but never defined

            // Create the transaction or use last transaction
            $transaction = $last_transaction ?? Transaction::create([
                'state' => Transaction::STATE_SUCCESS,
                'owner_id' => $user_id,
                'initiated_by_id' => $initiated_by_id,
                'initiated_by_other' => $initiated_by_other,
            ]);

            // Determine whether the product was free
            $free = $price == 0;

            // Create the wallet mutation unless product is free
            $mut_wallet = null;
            if(!$free) {
                // Create a new wallet mutation or update the existing
                $mut_wallet = $transaction
                    ->mutations()
                    ->create([
                        'economy_id' => $bar->economy_id,
                        'mutationable_id' => 0,
                        'mutationable_type' => '',
                        'amount' => $price,
                        'currency_id' => $currency->id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $user_id,
                    ]);
                $mut_wallet->setMutationable(
                    MutationWallet::create([
                        'wallet_id' => $wallet->id,
                    ])
                );
            }

            // Create a product mutation for each product type
            $products->each(function($product) use($transaction, $bar, $currency, $user_id, $mut_wallet, &$productCount) {
                // Get the quantity for this product, increase product count
                $quantity = $product['quantity'];
                $productCount += $quantity;

                // Create the product mutation
                $mut_product = $transaction
                    ->mutations()
                    ->create([
                        'economy_id' => $bar->economy_id,
                        'mutationable_id' => 0,
                        'mutationable_type' => '',
                        'amount' => -$product['priceTotal'],
                        'currency_id' => $currency->id,
                        'state' => Mutation::STATE_SUCCESS,
                        'owner_id' => $user_id,
                        'depend_on' => $mut_wallet?->id,
                    ]);
                $mut_product->setMutationable(
                    MutationProduct::create([
                        'product_id' => $product['product']->id,
                        'bar_id' => $bar->id,
                        'quantity' => $quantity,
                    ])
                );

                // Update bar inventory
                if($bar->inventory != null) {
                    $product['product']->subtractFromInventory(
                        $bar->inventory,
                        $mut_product->mutationable->quantity,
                        $mut_product->mutationable,
                    );
                }
            });

            // Update the wallet balance
            // TODO: do this by setting the mutation states instead
            if(!$free)
                $wallet->withdraw($price);

            // Return the transaction
            $out = $transaction;
        });

        // Return the transaction details
        return [
            'transaction' => $out,
            'productCount' => $productCount,
            'currency' => $currency,
            'price' => $price,
        ];
    }

    /**
     * Page to delete the bar.
     *
     * @return Response
     */
    public function delete($barId) {
        return view('bar.delete');
    }

    /**
     * Delete the bar.
     *
     * @return Response
     */
    public function doDelete(Request $request, $barId) {
        // Get the bar
        $bar = \Request::get('bar');

        // Validate
        $this->validate($request, [
            'confirm_name' => 'same:confirm_name_base',
            'confirm_delete' => 'accepted',
        ], [
            'confirm_name.same' => __('pages.bar.incorrectNameShouldBe', ['name' => $bar->name]),
        ]);

        // Delete the bar
        $bar->delete();

        // Redirect to the index page after deleting
        return redirect()
            ->route('community.manage', ['communityId' => $bar->community->human_id])
            ->with('success', __('pages.bar.deleted'));
    }

    /**
     * Page to generate a poster PDF for this bar, allowing some configuration.
     *
     * @return Response
     */
    public function generatePoster($barId) {
        return view('bar.poster');
    }

    /**
     * Generate the poster PDF, respond with it as a download.
     *
     * @return Response
     */
    public function doGeneratePoster(Request $request, $barId) {
        // Get the bar and session user
        $bar = \Request::get('bar');
        $withCode = !empty($bar->password) && is_checked($request->input('show_code'));

        // Set the poster locale
        \App::setLocale($request->input('language'));

        // Configure some parameters
        $code = $withCode ? $bar->password : null;
        $plainUrl = preg_replace(
            '/^https?:\/\//', '',
            route('bar.show', ['barId' => $bar->human_id])
        );
        $qrData = ['barId' => $bar->human_id];
        if($withCode)
            $qrData['code'] = $code;
        $qrUrl = route('bar.join', $qrData);

        // Render the PDF and respond with it as download
        return \PDF::loadView('poster.pdf', [
                'type' => 'bar',
                'plain_url' => $plainUrl,
                'qr_url' => $qrUrl,
                'code' => $code,
            ])
            ->download(strtolower(__('misc.bar')) . '-poster-' . $bar->human_id . '.pdf');
    }

    /**
     * Page with useful links.
     *
     * @return Response
     */
    public function links() {
        return view('bar.links');
    }

    /**
     * Page to start kiosk mode.
     *
     * @return Response
     */
    public function startKiosk() {
        return view('bar.kiosk.start');
    }

    /**
     * Do start kiosk mode.
     *
     * @return Response
     */
    public function doStartKiosk(Request $request) {
        // Get the bar and user
        $bar = \Request::get('bar');
        $user = barauth()->getSessionUser();

        // Validate
        $request->validate([
            'confirm' => 'accepted',
        ]);

        // TODO: verify that this user can start kiosk mode

        // Logout user
        $session = barauth()->getAuthState()->getSession();
        if($session != null)
            $session->invalidate();

        // Authenticate kiosk session
        $result = kioskauth()->getAuthenticator()->createSession($bar, $user);

        // Show an error if the kiosk session failed to create
        if($result->isErr())
            return redirect()
                ->back()
                ->with('error', __('general.serverError'));

        // Redirect to kiosk page
        return redirect()
            ->route('kiosk.main')
            ->withCookie(Cookie::forget(UserAuthenticator::AUTH_COOKIE));
    }

    /**
     * Build a list of preferred currencies for the given user.
     * The first currency in the returned list is the most preferred currency.
     *
     * Products may be bought using any of these currencies.
     * The list may be used to determine what product price to show if multiple
     * prices are available in different currencies.
     *
     * @param Economy|Bar $economy The economy the user is in.
     * @param EconomyMemberUser $user|null The user or null for the current user.
     *
     * @return [Currency] A list of preferred currencies.
     */
    // TODO: economy (or bar) param is obsolete because of member
    // TODO: only support economy member here, not user
    // TODO: move this function to some other class, user class?
    static function userCurrencies($economy, $member) {
        // TODO: optimize queries here!

        // Get the economy
        if($economy instanceof Bar)
            $economy = $economy->economy;

        // Select the user, get the economy and economy member
        $member ??= barauth()->getUser();
        if(!($member instanceof EconomyMember))
            $member = $economy->members()->user($member)->first();

        // Get the user wallets if user is member, sort by preferred
        if($member != null) {
            $wallets = $member->wallets;
            $currencies = $wallets
                ->map(function($w) use($economy) {
                    return $economy->currencies()->find($w->currency_id);
                })
                ->filter(function($c) {
                    return $c != null && $c->enabled;
                })
                ->unique('id');
        } else
            $currencies = collect();

        // Add other available currencies to list user has no wallet for yet
        // TODO: somehow sort this by relevance, or let bar owners sort
        $barCurrencies = $economy
            ->currencies()
            ->where('enabled', true)
            ->where('allow_wallet', true)
            ->whereNotIn('id', $currencies->pluck('id'))
            ->get();
        // TODO: use concat instead?
        $currencies = $currencies->merge($barCurrencies);

        // Return the list of currencies
        return $currencies;
    }

    /**
     * The permission required for basic user interaction such as viewing and
     * buying products.
     * @return PermsConfig The permission configuration.
     */
    public static function permsUser() {
        return BarRoles::presetUser();
    }

    /**
     * The permission required for basic bar management.
     * This allows viewing of management pages with limited changes.
     *
     * Editing the bar itself and setting permissive user roles it not
     * allowed.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsManage() {
        // TODO: does this include community roles?
        return BarRoles::presetManager();
    }

    /**
     * The permission required for complete bar administration.
     * This allows managing anything within this bar.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsAdminister() {
        // TODO: does this include community roles?
        return BarRoles::presetAdmin();
    }

    /**
     * The permission required creating a new bar.
     *
     * @return PermsConfig The permission configuration.
     */
    public static function permsCreate() {
        return CommunityController::permsAdminister();
    }
}

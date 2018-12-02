<?php

namespace App\Services;

use App\Models\User;
use App\Services\Auth\AuthState;
use App\Services\Auth\Authenticator;
use Carbon\Carbon;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class HistoryManager {

    /**
     * The key used in session data for history tracking.
     */
    const SESSION_HISTORY_KEY = 'history';

    /**
     * The maximum number of history items in the list, after which the oldest
     * history will be truncated.
     *
     * TODO: make this configurable in application configuration
     */
    const HISTORY_LIMIT = 32;

    /**
     * Application instance.
     * @var Application
     */
    private $app;

    /**
     * HistoryManager constructor.
     *
     * @param Application $app Application instance.
     */
    public function __construct(Application $app) {
        $this->app = $app;
    }

    /**
     * Get the raw history data for the current request.
     *
     * @return Array Array of history data.
     */
    private function getHistory(Request $request) {
        return $request->session()->get(Self::SESSION_HISTORY_KEY) ?? [];
    }

    /**
     * Set the raw history data for the current request.
     *
     * @param Array $history Array of history data.
     */
    private function setHistory(Request $request, $history) {
        $request->session()->put(Self::SESSION_HISTORY_KEY, $history);
    }

    /**
     * Push the current page as new entry on the history list.
     *
     * @param Request $request The current request.
     */
    public function push(Request $request) {
        // Get the route name, session instance and history data
        $route = $request->route()->getName();
        $history = $this->getHistory($request);

        // Find the index of an existing history item with the same route, trim
        // history upto that item
        $existingRoute = collect($history)
            ->search(function($item) use($route) {
                return $item['route'] == $route;
            });
        if($existingRoute !== false)
            $history = array_slice($history, 0, $existingRoute);

        // Add the current page as history item
        $history[] = ['route' => $route, 'url' => $request->fullUrl(), 'time' => Carbon::now()];

        // Truncate history that is growing too big
        $size = count($history);
        if($size > Self::HISTORY_LIMIT)
            $history = array_slice(
                $history,
                max($size - Self::HISTORY_LIMIT, 0),
                Self::HISTORY_LIMIT
            );

        // Update the history
        $this->setHistory($request, $history);
    }

    /**
     * Get the URL of the previous page based on the history data.
     *
     * TODO: what is returned by default
     */
    public function back($default = '#', $request = null) {
        // Get the request
        if($request === null)
            $request = request();

        // Get the last history item, make sure there's any history
        $history = $this->getHistory($request);
        if(empty($history))
            return $default;

        // Pick the URL from the last item
        return $history[sizeof($history) - 2]['url'];
    }
}

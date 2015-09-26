// The page refresh timer instance
var pageRefreshTimer = null;

/**
 * Start or restart the refresh timer.
 */
function startRefreshTimer() {
    // Stop the current timer
    stopRefreshTimer();

    // Set up the timer
    pageRefreshTimer = setInterval(function() {
        if(getActivePageId() != 'page-map') {
            showLoader('Refreshing page...');
            refreshPage();
            hideLoader();
        }
    }, 1000 * 60 * 2);
}

/**
 * Stop the refresh timer.
 */
function stopRefreshTimer() {
    // Clear the timer
    if(pageRefreshTimer != null)
        clearInterval(pageRefreshTimer);

    // Reset the variable
    pageRefreshTimer = null;
}

/**
 * Get the ID of the current active page.
 *
 * @returns string ID of active page.
 */
function getActivePageId() {
    return $.mobile.activePage.attr("id");
}

/**
 * Create a station search widget with a list view and search field.
 *
 * @param listView The list view.
 * @param searchField The search field.
 */
function createStationSearch(listView, searchField) {

    // Register search field handlers
    registerSearchFieldHandlers($(searchField));

    // Create a list to cache some stations results in, also cache the last used query to prevent duplicated events
    var cacheStationKeys = [];
    var cacheStationValues = [];
    var cacheLastQuery = '';

    // Store the instance of the current ajax request being made
    var currentRequest = null;

    /**
     * Register the search field handlers.
     *
     * @param searchField The search field.
     */
    function registerSearchFieldHandlers(searchField) {
        searchField.on('input propertychange paste change keyup', function() {
            // Get the filter to use, determine the filter length
            var filter = searchField.val();
            var filterLength = filter.length;

            // Make sure a query isn't executed twice, store the last used query otherwise.
            if(filter.toLowerCase() == cacheLastQuery)
                return;
            else
                cacheLastQuery = filter.toLowerCase();

            // Load the results or show the proper status based on the filter length
            if(filterLength <= 0)
                clearListView();
            else if(filterLength < 3)
                showListViewStatus("Search query too short");
            else
                loadStationResults(filter);
        });
    }

    /**
     * Check whether a specific query for stations was cached.
     *
     * @param name The filter.
     *
     * @returns {boolean} True if the stations were cached, false otherwise.
     */
    function isStationCached(name) {
        return cacheStationKeys.indexOf(name.toLowerCase()) > -1;
    }

    /**
     * Add station cache.
     *
     * @param name The filter.
     * @param value The station cache.
     */
    function addStationCache(name, value) {
        cacheStationKeys.push(name.toLowerCase());
        cacheStationValues.push(value);
    }

    /**
     * Get the cache for a specific station filter.
     *
     * @param name The filter.
     * @returns {*} The station cache.
     */
    function getStationCache(name) {
        return cacheStationValues[cacheStationKeys.indexOf(name.toLowerCase())];
    }

    /**
     * Clear the station cache.
     */
    function clearStationCache() {
        cacheStationKeys = [];
        cacheStationValues = [];
    }

    // Clear the list of cached station results after a minute
    setInterval(function() { clearStationCache(); }, 15 * 1000);

    /**
     * Load the station results based on the specified filter.
     *
     * @param filter The filter, as a string.
     */
    function loadStationResults(filter) {
        // Strip HTML tags
        filter = filter.replace(/<(?:.|\n)*?>/gm, ' ');

        // Return the results if they're cached
        if(isStationCached(filter)) {
            setListViewContent($(getStationCache(filter)));
            return;
        }

        // Create a constant with the status image URL
        var transpImgUrl = 'style/image/icon/16/transparent.png';

        // Abort the current request
        if(currentRequest != null)
            currentRequest.abort();

        // Show a searching status
        showListViewStatus('Searching for \'' + filter + '\'...');

        // Make an AJAX request to load the station results
        currentRequest = $.ajax({
            type: "GET",
            url: "ajax/stations.php",
            data: { filter: filter },
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data) {
                // Create a variable to build the list in
                var listHtml ='';

                // Show the error message if returned
                if(data.hasOwnProperty('error_msg')) {
                    showListViewStatus('Error: ' + data.error_msg);
                    return;
                }

                // Count the number of results returned
                var resultCount = data.length;

                // Show the number of stations found
                if(resultCount > 0)
                    listHtml += '<li data-role="list-divider">Found ' + resultCount + ' station' + (resultCount == 1 ? '' : 's') + ' for \'' + filter + '\'</li>';
                else {
                    // Create the designator, render the results
                    listHtml += '<li data-role="list-divider">No stations found for \'' + filter + '\'</li>';

                    // Render and cache the results
                    setListViewContent($(listHtml));
                    addStationCache(filter, listHtml);
                    return;
                }

                // Define a variable to store error messages in, to show instead of the search results
                var errorMsg = null;

                // Parse each result, to build the list view
                $.each(data, function(index, item) {
                    // Make sure the current object contains the correct properties
                    if(!item.hasOwnProperty('station_id')) {
                        errorMsg = 'Failed to load search results';
                        return;
                    }

                    // Define the icon style if the station has an occupation color
                    var iconStyle = '';
                    if(item.hasOwnProperty('station_color'))
                        iconStyle += ' style="background: #' + item.station_color + ';"';

                    // Add the HTML of the list item to the list view HTML
                    listHtml += '<li>';
                    listHtml += '<a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="stations.php?station_id=' + item.station_id + '">';
                    listHtml += '<img src="' + transpImgUrl + '" class="ui-li-icon group-icon-list"' + iconStyle + ' />';
                    listHtml += item.station_name;
                    listHtml += '<span class="ui-li-count">' + item.station_points + '</span>';
                    listHtml += '</a></li>';
                });

                // Show the results or the error message
                if(errorMsg != null)
                    showListViewStatus(errorMsg);
                else {
                    // Render and cache the results
                    setListViewContent($(listHtml));
                    addStationCache(filter, listHtml);
                }
            },
            error: function(msg) {
                // Get the status
                var status = msg.statusText;

                // Check whethr the request was aborted
                if(status == 'abort') {
                    clearListView();
                    return;
                }

                // An error occurred, show a status message
                showListViewStatus('Error: ' + msg.statusText);
            },
            complete: function() {
                // Clear the current request variable
                currentRequest = null;
            }
        });
    }

    /**
     * Set the content of the list view. Update the view accordingly.
     *
     * @param html The HTML to put into the list view.
     */
    function setListViewContent(html) {
        listView.html(html);
        listView.trigger('create');
        listView.listview('refresh');
    }

    /**
     * Show a status message in the list view.
     *
     * @param msg The message to show, as a string.
     */
    function showListViewStatus(msg) {
        setListViewContent('<li data-role="list-divider">' + msg + '</li>');
    }

    /**
     * Clear and hide the list view.
     */
    function clearListView() {
        setListViewContent('');
    }
}

/**
 * Refresh the current jQuery mobile page.
 */
function refreshPage() {
    jQuery.mobile.changePage(window.location.href, {
        allowSamePageTransition: true,
        transition: 'none',
        reloadPage: true,
        reverse: false,
        changeHash: false
    });
}

/**
 * Create a station search widget with a list view and search field.
 *
 * @param listView The list view.
 * @param searchField The search field.
 * @param callback Callback method.
 */
function createStationSearchSelectable(listView, searchField, callback) {

    // Register search field handlers
    registerSearchFieldHandlers($(searchField));

    // Create a list to cache some stations results in, also cache the last used query to prevent duplicated events
    var cacheStationKeys = [];
    var cacheStationValues = [];
    var cacheLastQuery = '';

    // Store the instance of the current ajax request being made
    var currentRequest = null;

    /**
     * Register the search field handlers.
     *
     * @param searchField The search field.
     */
    function registerSearchFieldHandlers(searchField) {
        searchField.on('input propertychange paste change keyup', function() {
            // Get the filter to use, determine the filter length
            var filter = searchField.val();
            var filterLength = filter.length;

            // Make sure a query isn't executed twice, store the last used query otherwise.
            if(filter.toLowerCase() == cacheLastQuery)
                return;
            else
                cacheLastQuery = filter.toLowerCase();

            // Load the results or show the proper status based on the filter length
            if(filterLength <= 0)
                clearListView();
            else if(filterLength < 3)
                showListViewStatus("Search query too short");
            else
                loadStationResults(filter);
        });
    }

    /**
     * Check whether a specific query for stations was cached.
     *
     * @param name The filter.
     *
     * @returns {boolean} True if the stations were cached, false otherwise.
     */
    function isStationCached(name) {
        return cacheStationKeys.indexOf(name.toLowerCase()) > -1;
    }

    /**
     * Add station cache.
     *
     * @param name The filter.
     * @param value The station cache.
     */
    function addStationCache(name, value) {
        cacheStationKeys.push(name.toLowerCase());
        cacheStationValues.push(value);
    }

    /**
     * Get the cache for a specific station filter.
     *
     * @param name The filter.
     * @returns {*} The station cache.
     */
    function getStationCache(name) {
        return cacheStationValues[cacheStationKeys.indexOf(name.toLowerCase())];
    }

    /**
     * Clear the station cache.
     */
    function clearStationCache() {
        cacheStationKeys = [];
        cacheStationValues = [];
    }

    // Clear the list of cached station results after a minute
    setInterval(function() { clearStationCache(); }, 15 * 1000);

    /**
     * Load the station results based on the specified filter.
     *
     * @param filter The filter, as a string.
     */
    function loadStationResults(filter) {
        // Strip HTML tags
        filter = filter.replace(/<(?:.|\n)*?>/gm, ' ');

        // Return the results if they're cached
        if(isStationCached(filter)) {
            setListViewContent($(getStationCache(filter)));

            // Call the callback method
            callback();

            return;
        }

        // Create a constant with the status image URL
        var transpImgUrl = 'style/image/icon/16/transparent.png';

        // Abort the current request
        if(currentRequest != null)
            currentRequest.abort();

        // Show a searching status
        showListViewStatus('Searching for \'' + filter + '\'...');

        // Make an AJAX request to load the station results
        currentRequest = $.ajax({
            type: "GET",
            url: "ajax/stations.php",
            data: { filter: filter },
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success:function(data) {
                // Create a variable to build the list in
                var listHtml ='';

                // Show the error message if returned
                if(data.hasOwnProperty('error_msg')) {
                    showListViewStatus('Error: ' + data.error_msg);
                    return;
                }

                // Count the number of results returned
                var resultCount = data.length;

                // Show the number of stations found
                if(resultCount > 0)
                    listHtml += '<li class="ui-li ui-li-divider ui-btn ui-bar-a ui-corner-top ui-btn-up-undefined" data-role="list-divider">Found ' + resultCount + ' station' + (resultCount == 1 ? '' : 's') + ' for \'' + filter + '\'</li>';
                else {
                    // Create the designator, render the results
                    listHtml += '<li class="ui-li ui-li-divider ui-btn ui-bar-a ui-corner-top ui-btn-up-undefined" data-role="list-divider">No stations found for \'' + filter + '\'</li>';

                    // Render and cache the results
                    setListViewContent($(listHtml));
                    addStationCache(filter, listHtml);
                    return;
                }

                // Define a variable to store error messages in, to show instead of the search results
                var errorMsg = null;

                // Parse each result, to build the list view
                $.each(data, function(index, item) {
                    // Make sure the current object contains the correct properties
                    if(!item.hasOwnProperty('station_id')) {
                        errorMsg = 'Failed to load search results';
                        return;
                    }

                    // Define the icon style if the station has an occupation color
                    var iconStyle = '';
                    if(item.hasOwnProperty('station_color'))
                        iconStyle += ' style="background: #' + item.station_color + ';"';

                    // Add the HTML of the list item to the list view HTML
                    listHtml += '<input name="station_id" id="station_' + item.station_id + '" value="' + item.station_id + '" type="radio" />';
                    listHtml += '<label for="station_' + item.station_id + '">';
                    listHtml += '<img src="' + transpImgUrl + '" class="ui-li-icon group-icon-radio"' + iconStyle + ' />';
                    listHtml += '<span class="ui-li-count">' + item.station_points + '</span>';
                    listHtml += item.station_name + '</label>';
                });

                // Show the results or the error message
                if(errorMsg != null)
                    showListViewStatus(errorMsg);
                else {
                    // Render and cache the results
                    setListViewContent($(listHtml));
                    addStationCache(filter, listHtml);
                }
            },
            error: function(msg) {
                // Get the status
                var status = msg.statusText;

                // Check whethr the request was aborted
                if(status == 'abort') {
                    clearListView();
                    return;
                }

                // An error occurred, show a status message
                showListViewStatus('Error: ' + msg.statusText);
            },
            complete: function() {
                // Clear the current request variable
                currentRequest = null;

                // Call the callback method
                callback();
            }
        });
    }

    /**
     * Set the content of the list view. Update the view accordingly.
     *
     * @param html The HTML to put into the list view.
     */
    function setListViewContent(html) {
        listView.controlgroup('container').html('').append(html);
        listView.enhanceWithin().controlgroup("refresh");
    }

    /**
     * Show a status message in the list view.
     *
     * @param msg The message to show, as a string.
     */
    function showListViewStatus(msg) {
        setListViewContent('<li class="ui-li ui-li-divider ui-btn ui-bar-a ui-corner-top ui-btn-up-undefined" data-role="list-divider">' + msg + '</li>');
    }

    /**
     * Clear and hide the list view.
     */
    function clearListView() {
        setListViewContent('');
    }
}

/**
 * Check whether an element has an attribute.
 *
 * @param attrName The name of the attribute.
 *
 * @returns {boolean} True if the attribute exists, false otherwise.
 */
jQuery.fn.hasAttr = function(attrName) {
    // Get the attribute
    var attr = $(this[0]).attr(attrName);

    // Check if the attribute exists
    return (typeof attr !== typeof undefined && attr !== false);
};

// TODO: Make sure this code only works on pages the sidebar is available on
$(document).on("pagecreate", function() {
    $(document).on("swiperight", function(e) {
        if(e.type === "swiperight") {
            $("#main-panel").panel("open");
        }
    });
});

$(document).on("click", ".show-page-loading-msg", function() {
    var $this = $( this ),
        msgText = $this.jqmData("msgtext") || $.mobile.loader.prototype.options.text;
    showLoader(msgText);
});

function showLoader(msgText) {
    $.mobile.loading("show", {
        text: msgText,
        textVisible: "true",
        theme: "b",
        textonly: false,
        html: ""
    });
}

function hideLoader() {
    $.mobile.loading("hide");
}

var tour = null;
$(document).on("pageshow", function() {
    // Start the tour when the demo button is pressed
    $('#start-tutorial').click(function() {
        // Make sure a tour isn't started already
        if(tour != null)
            tour.stop();

        tour = new Tour({
            name: "test1",
            storage: window.localStorage,
            template: "<div class='popover tour'>" +
            "<div class='arrow'></div>" +
            "<h3 class='popover-title'></h3>" +
            "<div class='popover-content'></div>" +
            "<div class='popover-navigation'>" +
            '<button class="btn btn-sm btn-default" data-role="prev">&laquo;</button> <button class="btn btn-sm btn-default" data-role="next">&raquo;</button>' +
            '<button class="btn btn-sm btn-default" data-role="pause-resume" data-pause-text="Pause" data-resume-text="Resume">Pause</button> <button class="btn btn-sm btn-default" data-role="end">End tour</button>' +
            "</div>" +
            "</div>",
            steps: [
                {
                    element: "#header",
                    title: "Welcome",
                    content: "Welcome to our service. Click the header to reload the app..",
                    placement: "bottom",
                    backdrop: true,
                    prev: -1
                }, {
                    element: "#list-quick-buy",
                    title: "Quick buy",
                    content: "Click on one of the products above to quickly buy it.",
                    placement: "bottom",
                    backdrop: true,
                    onShow: function() {
                        // Make sure the sidebar is closed
                        closeSidebar();
                    }
                }, {
                    element: "#menu-button",
                    title: "Sidebar",
                    content: "The sidebar description",
                    placement: "right",
                    backdrop: true,
                    reflex: true,
                    next: -1,
                    onShow: function() {
                        // Open the sidebar
                        openSidebar();
                    },
                    onHide: function() {
                        // Make sure the sidebar is closed afterwards
                        closeSidebar();
                    }
                }
            ],
            debug: true,
            onShown: function(tour) {
                var stepElement = getTourElement(tour);
                $(stepElement).after($('.tour-step-background'));
                $(stepElement).after($('.tour-backdrop'));
            }
        });

        function getTourElement(tour){
            return tour._options.steps[tour._current].element
        }

        // Initialize the tour
        tour.init();

        // Start the tour
        tour.restart();

        return false;
    });

    // Cancel quick buy clicks when following tutoriale
    $('#list-quick-buy').find('li a').click(function() {
        // Check whether the quick buy tutorial step is shown
        if(tour.getCurrentStep() != 1)
            return true;

        // Open/show the sidebar
        // TODO: Make sure the sidebar isn't shown already.
        openSidebar();

        // Continue to the next step, cancel the click
        tour.next();
        return false;
    });
});

/*
// Code for smooth collapsibles
$(document).on('pagecreate', function(event, ui) {
    $(".ui-collapsible-heading-toggle").on("click", function(e) {
        var current = $(this).closest(".ui-collapsible");
        if (current.hasClass("ui-collapsible-collapsed")) {
            //collapse all others and then expand this one
            $(".ui-collapsible").not(".ui-collapsible-collapsed").find(".ui-collapsible-heading-toggle").click();
            $(".ui-collapsible-content", current).slideDown(300);
        } else {
            $(".ui-collapsible-content", current).slideUp(300);
        }
    });
});*/

/**
 * Get the sidebar panel, if exists.
 *
 * @returns {*|jQuery|HTMLElement} Sidebar panel.
 */
function getSidebarPanel() {
    return $('#main-panel');
}

/**
 * Open the sidebar if it's closed.
 */
function openSidebar() {
    getSidebarPanel().panel('open');
}

/**
 * Close the sidebar if it's open.
 */
function closeSidebar() {
    getSidebarPanel().panel('close');
}
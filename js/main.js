// Store the map instance
var map = null;

// The page refresh timer instance
var pageRefreshTimer = null;

// Set whether the window is focused
var windowFocussed = true;

/*$(document).ready(function() {
    var notIE = (document.documentMode === undefined), isChromium = window.chrome;

    // The focus in function
    var onFocusIn = function() {
        // Make sure the window is blurred
        if(windowFocussed)
            return;

        // Set the focused flag
        windowFocussed = true;

        // Start the refresh timer again
        startRefreshTimer();

        // Refresh and update the page
        showLoader('Refreshing page...');
        setTimeout(function() {
            refreshPage();
            hideLoader();
        }, 1000);
    };

    // The focus out function
    var onFocusOut = function() {
        // Make sure the window is focused
        if(!windowFocussed)
            return;

        // Set the focused flag
        windowFocussed = false;

        // Stop the refresh timer
        stopRefreshTimer();
    };

    // Register the event handlers for the window focus
    if(notIE && !isChromium) {
        $(window).on("focusin", function() { onFocusIn(); });
        $(window).on("focusout", function() { onFocusOut(); });
    } else {
        if(window.addEventListener) {
            window.addEventListener("focus", function(event) { onFocusIn(); }, false);
            window.addEventListener("blur", function(event) { onFocusOut(); }, false);
        } else {
            window.attachEvent("focus", function(event) { onFocusIn(); });
            window.attachEvent("blur", function(event) { onFocusOut(); });
        }
    }
});*/

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
 * Initialize the map.
 */
function initMap(lat, long) {
    // Unload the map if it is loaded
    if(map !== null) {
        map.remove();
        map = null;
    }

    // Parameter defaults
    lat = typeof lat !== 'undefined' ? lat : 52.0802764893;
    long = typeof long !== 'undefined' ? long : 4.3249998093;

    // Initialize the map and set the map instance
    map = L.map('map').setView([lat, long], 15);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoidGltdmlzZWUiLCJhIjoiNzExOWJhZjExNzZlNmU1M2Y1NzFmNzU4NmUzMmIyNTYifQ.SiLLZI5JSqtBvEk_XOrPVg', {
        maxZoom: 18,
        attribution: 'Hosted by <a href="http://timvisee.com/">timvisee.com</a>',
        id: 'timvisee.dd6f3e4f',
        detectRetina: true
    }).addTo(map);

    // Load the station icons
    loadMapStationIcons();
}

/**
 * Get a marker icon with a specific color.
 *
 * @param color The HEX color of the marker icon.
 */
function getMarkerIcon(color) {
    return L.icon({
        iconUrl: 'https://api.mapbox.com/v4/marker/pin-m-rail+' + color + '.png?access_token=pk.eyJ1IjoidGltdmlzZWUiLCJhIjoiNzExOWJhZjExNzZlNmU1M2Y1NzFmNzU4NmUzMmIyNTYifQ.SiLLZI5JSqtBvEk_XOrPVg',
        iconRetinaUrl: 'https://api.mapbox.com/v4/marker/pin-m-rail+' + color + '@2x.png?access_token=pk.eyJ1IjoidGltdmlzZWUiLCJhIjoiNzExOWJhZjExNzZlNmU1M2Y1NzFmNzU4NmUzMmIyNTYifQ.SiLLZI5JSqtBvEk_XOrPVg',
        iconSize: [20, 50],
        iconAnchor: [10, 25],
        popupAnchor: [0, -30]
    });
}

function loadMapStationIcons() {
    // Show the loader
    showLoader('Loading stations...');

    // Make an AJAX request to load the station results
    $.ajax({
        type: "GET",
        url: "ajax/stations.php",
        data: {filter: "", columns: 'station_lat|station_long|station_group', claimed_only: 1},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (data) {
            // Show the error message if returned
            if (data.hasOwnProperty('error_msg')) {
                alert('Error: ' + data.error_msg);
                return;
            }

            // Count the number of results returned
            var resultCount = data.length;

            // Define a variable to store error messages in, to show instead of the search results
            var errorMsg = null;

            // Parse each result, to build the list view
            $.each(data, function (index, item) {
                // Make sure the current object contains the correct properties
                if (!item.hasOwnProperty('station_id')) {
                    errorMsg = 'Failed to load search results';
                    return;
                }

                // Determine the icon color
                var iconColor = '333333';
                if (item.hasOwnProperty('station_color'))
                    iconColor = item.station_color;

                // Determine the group name
                var groupName = '<i>Nobody</i>';
                if (item.hasOwnProperty('station_group'))
                    groupName = item.station_group;

                // Create the marker
                var marker = L.marker([item.station_lat, item.station_long], {
                    icon: getMarkerIcon(iconColor),
                    title: item.station_name,
                    riseOnHover: true
                }).addTo(map);

                // Create the marker popup
                marker.bindPopup('<a href="stations.php?station_id=' + item.station_id + '">' + item.station_name + '</a><br /><br />Points: ' + item.station_points + '<br />Claimed by:<br />' + groupName);
            });

            // Show the results or the error message
            if (errorMsg != null)
                alert(errorMsg);
        },
        error: function (msg) {
            // Get the status
            var status = msg.statusText;

            alert('Error: ' + status);
        },
        complete: function () {
            hideLoader();
        }
    });
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
 * Initialize the login panel.
 */
function initLoginPanel() {
    // Get the input fields
    var selectTeam = $('#team-id');
    var textfieldPass = $('#team-pass');
    var buttonLogin = $('#team-submit');
    var inputContainer = $('#login-input-container');

    // Hide the input container if no item has been selected
    if(selectTeam.val() == '')
        inputContainer.hide();

    // Create an event handler for when the selected team changes
    selectTeam.change(function() {
        // Get the selector value
        var value = selectTeam.val();

        // Check whether a team is selected
        var disabled = (value == '');

        // Show or hide the password box and login button based on the selected team
        if(disabled) {
            // Hide the input box
            inputContainer.stop().slideUp();

            // Disable the input boxes
            buttonLogin.addClass('ui-state-disabled');
            textfieldPass.attr('disabled', 'disabled');
        } else {
            // Show the input box
            inputContainer.stop().slideDown();

            // Enable the input boxes
            buttonLogin.removeClass('ui-state-disabled');
            textfieldPass.removeAttr('disabled');

            // Select the password box
            textfieldPass.select();
        }
    });
}

// Initialize the login panel when it's loaded
$(document).on('pageshow', function() {
    if(getActivePageId() == 'page-login')
        initLoginPanel();
});

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

function setPictureApprovalStatusOnPage(pictureId, approvalStatus) {
    setPictureApprovalStatus(pictureId, approvalStatus, function() {
        // Refresh the next page once it's loaded
        $(document).one('pageshow', function() { refreshPage(); });

        // Go one page back
        $.mobile.back();

    }, function(msg) {
        alert('Failed to set the approval status of the picture.\n\nError: ' + msg);
    });
}

function setPictureApprovalStatusOnWizard(pictureId, approvalStatus) {
    setPictureApprovalStatus(pictureId, approvalStatus, function() {
        // Refresh the page
        refreshPage();

    }, function(msg) {
        alert('Failed to set the approval status of the picture.\n\nError: ' + msg);
    });
}

function deletePicture(pictureId) {
    setPictureApprovalStatus(pictureId, -1, function() {
        // Go two pages back
        $.mobile.back();

        // Refresh the next page once it's loaded
        $(document).one('pageshow', function() { refreshPage(); });

        // Go two pages back
        $.mobile.back();

    }, function(msg) {
        alert('Failed to delete the picture.\n\nError: ' + msg);
    });
}

function setPictureApprovalStatus(pictureId, approvalStatus, successCallback, errorCallback) {
    // Show the loader
    showLoader('Approving picture...');

    // Make an AJAX request to load the station results
    $.ajax({
        type: "GET",
        url: "ajax/approval.php",
        data: { picture_id: pictureId, set_approval: approvalStatus},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success:function(data) {
            // Show the error message if returned
            if(data.hasOwnProperty('error_msg')) {
                errorCallback(data.error_msg);
                return;
            }

            successCallback();
        },
        error: function(msg) {
            errorCallback(msg.statusText);
        },
        complete: function () {
            hideLoader();
        }
    });
}

/*$(document).ready(function() { onStart(); });

function onStart() {
    // Request permission to show notifications
    Notification.requestPermission();

    setTimeout(function() {
        var notification = new Notification("OV Rally", {
            body: "A new picture is available to approve!",
            icon: "http://local.timvisee.com/app/ovrally/thumbnail.php?picture_id=1&size=120x120&shape=fixed"
        });
    }, 1000);
}*/

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

// Initialize the map on page load
$(document).on('pageshow', function(event, ui) {
    // Check if the map page is unloaded
    if($(ui.prevPage).hasAttr('id')) {
        // Get the element ID
        var attrId = $(ui.prevPage).attr('id').toLowerCase();

        // Check whether the map page is unloaded
        if(attrId == 'page-map') {
            // Unload the map
            if(map !== null) {
                map.remove();
                map = null;
            }
        }
    }

    // Determine whether to unload the previous page
    var unload = true;

    if($(ui.prevPage).hasAttr('data-unload')) {
        // Get the element attribute
        var attrUnload = $(ui.prevPage).attr('data-unload').toLowerCase();

        // Check whether to unload
        if(attrUnload == "false")
            unload = false;
    }

    // Unload the page
    if(unload)
        $(ui.prevPage).remove();

    // Restart the refresh timer
    startRefreshTimer();
});

$(document).on( "click", ".show-page-loading-msg", function() {
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
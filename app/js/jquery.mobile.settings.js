/**
 * Set some default jQuery Mobile settings for the current application.
 */

$(document).bind("mobileinit", function(){
    $.extend(  $.mobile , {
        defaultPageTransition: "slide"
    });
});
/**
 * Set some default jQuery Mobile settings for the current application.
 */

$(document).bind("mobileinit", function() {
    // Set the default page transition
    $.extend($.mobile, {
        defaultPageTransition: "slide"
    });

    // Set the page theme
    //$.mobile.page.prototype.options.backBtnTheme = 'b';
    //$.mobile.page.prototype.options.headerTheme = 'b';
    //$.mobile.page.prototype.options.footerTheme = 'b';
    //$.mobile.page.prototype.options.contentTheme = 'b';
    //$.mobile.page.prototype.options.theme = 'b';
    //$.mobile.listview.prototype.options.filterTheme = 'b';
});
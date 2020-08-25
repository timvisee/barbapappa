var axios = require('axios');

/**
 * Keeps PWA prompt event so we can manually trigger it.
 */
var pwaDeferredPrompt = null;

// Register service worker for PWA/offline usage
if('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/service-worker.js').then(registration => {
            console.log('Service worker registered: ', registration);
        }).catch(registrationError => {
            console.log('Service worker registration failed: ', registrationError);
        });
    });
}

// Catch PWA install prompt
window.addEventListener('beforeinstallprompt', (e) => {
    // Keep event so we can trigger prompt later
    pwaDeferredPrompt = e;

    // Manage PWA install button
    managePwaInstallButton();
});

$(document).ready(function() {
    // Initialize components
    $('.ui.checkbox').checkbox();
    $('.ui.dropdown').dropdown();
    $('.ui.accordion').accordion();

    // Sidebar toggle
    $('.sidebar-toggle').click(function() {
        let sidebarClass = $(this).data('sidebar');
        $('.ui.sidebar.' + sidebarClass).sidebar('toggle');
        return false;
    });

    // Popups
    $('.popup').popup({
        position: 'top center',
        transition: 'scale',
    });
    $('.joined-label-popup').popup({
        position: 'bottom left',
        transition: 'vertical flip',
    });

    // Build the copy fields and messages sidebar
    managePwaInstallButton();
    buildCopyFields();
    buildMessagesSidebar();
});

/**
 * Manage the PWA install button.
 */
function managePwaInstallButton() {
    // Select install button, hide by default
    const installBtn = document.querySelector('.pwa-install-button');
    installBtn.style.display = 'none';

    // Stop if we did not catch PWA prompt
    if(pwaDeferredPrompt == null)
        return;

    // Show install button if PWA prompt was deferred
    installBtn.style.display = 'block';

    installBtn.addEventListener('click', (e) => {
        installBtn.style.display = 'none';

        // Show prompt
        pwaDeferredPrompt.prompt();
        pwaDeferredPrompt.userChoice.then((choiceResult) => {
            // TODO: remove logging?
            // if (choiceResult.outcome === 'accepted') {
            //     console.log('User accepted the A2HS prompt');
            // } else {
            //     console.log('User dismissed the A2HS prompt');
            // }
            pwaDeferredPrompt = null;
        });
    });
}

/**
 * Build and set up the copy-on-click fields.
 */
function buildCopyFields() {
    // Copy on clock for copy elements
    // TODO: translate
    $('.copy').click(function() {
        // Get the node, select the text
        var copyText = $(this);
        var origText = copyText.text();
        var altText = copyText.data('copy');

        copyText.data('tooltip', 'blah');

        // Modify text before copy if alt text
        if(altText)
            copyText.text(altText);

        // Select the text and copy
        selectText(copyText.get()[0]);
        document.execCommand("copy");

        // Reset original text, select it again for visuals
        if(altText) {
            copyText.text(origText);
            selectText(copyText.get()[0]);
        }

        // Show in popup that we've copied
        copyText.popup('change content', 'Copied!');
    });
    $('.copy').popup({
        content: 'Click to copy',
        position: 'right center',
    });
}

/**
 * Build the messages sidebar.
 */
function buildMessagesSidebar() {
    // Load messages sidebar content through AJAX when it's opened
    let sidebarMessages = $('.ui.sidebar.messages').first();
    sidebarMessages.sidebar({
        'onVisible': function() {
            sidebarMessages.prepend('<div class="ui active dimmer"><div class="ui loader"></div></div>');
            axios.get('/ajax/messages-sidebar')
                .then(function(response) {
                    sidebarMessages.html(response.data);
                })
                .catch(function (error) {
                    // TODO: handle loading errors
                    console.log(error);
                });
        }
    });
}

/**
 * Select all text in the given DOM node.
 */
function selectText(node) {
    // node = document.getElementById(node);

    if (document.body.createTextRange) {
        const range = document.body.createTextRange();
        range.moveToElementText(node);
        range.select();
    } else if (window.getSelection) {
        const selection = window.getSelection();
        const range = document.createRange();
        range.selectNodeContents(node);
        selection.removeAllRanges();
        selection.addRange(range);
    } else {
        console.warn("Could not select text in node: Unsupported browser.");
    }
}

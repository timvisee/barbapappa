self.addEventListener('install', function(e) {
    e.waitUntil(
        caches.open('barbapappa').then(function(cache) {
            return cache.addAll([
                // TODO: improve this list, less resources, only necessary
                '/css/app.css',
                '/css/flag-icon.css',
                '/css/glyphicons-packed.css',
                '/css/semnatic.min.css',
                '/img/logo/logo_square.png',
                '/js/app.js',
                '/js/jquery-packed.js',
                '/js/lang.js',
                '/js/quickbuy.js',
                '/js/semantic.min.js',
            ]);
        })
    );
});

self.addEventListener('fetch', function(e) {
    // TODO: remove console log here, only to debug?
    console.log(e.request.url);
    e.respondWith(
        caches.match(e.request).then(function(response) {
            return response || fetch(e.request);
        })
    );
});

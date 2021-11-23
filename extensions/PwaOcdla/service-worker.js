self.addEventListener('install', event => {
  console.log('Service worker installing...');
  // Add a call to skipWaiting here
});

self.addEventListener('activate', event => {
  console.log('Service worker activating...');
});



self.addEventListener('fetch', function(event) {
console.log('Fetching:', event.request.url);
	event.respondWith(
		caches.open('ocdla-books').then(function(cache) {
			return cache.match(event.request).then(function (response) {
				return response || fetch(event.request).then(function(response) {
					cache.put(event.request, response.clone());
					return response;
				});
			});
		})
	);
});

// Service Worker for Singer Website
// This is a minimal Service Worker to prevent 404 errors

const CACHE_NAME = 'singer-website-v1';

// Install event - cache resources
self.addEventListener('install', function(event) {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(function(cache) {
        // Try to cache each file individually, ignore failures
        const urlsToCache = [
          '/',
          '/assets/css/style.css',
          '/assets/js/main.js',
          '/assets/images/artist-photo.jpg'
        ];
        
        // Cache files individually to handle failures gracefully
        return Promise.all(
          urlsToCache.map(function(url) {
            return cache.add(url).catch(function(error) {
              console.log('Failed to cache:', url, error);
              // Don't reject the promise, just log the error
              return Promise.resolve();
            });
          })
        );
      })
  );
});

// Fetch event - serve from cache when offline
self.addEventListener('fetch', function(event) {
  event.respondWith(
    caches.match(event.request)
      .then(function(response) {
        // Return cached version or fetch from network
        if (response) {
          return response;
        }
        
        // For network requests, handle errors gracefully
        return fetch(event.request).catch(function(error) {
          console.log('Network request failed:', event.request.url, error);
          
          // Return a basic response for failed requests
          if (event.request.url.includes('.css')) {
            return new Response('/* Service Worker: CSS not available */', {
              headers: { 'Content-Type': 'text/css' }
            });
          } else if (event.request.url.includes('.js')) {
            return new Response('console.log("Service Worker: JS not available");', {
              headers: { 'Content-Type': 'application/javascript' }
            });
          } else if (event.request.url.includes('.jpg') || event.request.url.includes('.png')) {
            return new Response('', {
              headers: { 'Content-Type': 'image/png' }
            });
          }
          
          return new Response('Service Worker: Resource not available', {
            status: 404,
            statusText: 'Not Found'
          });
        });
      })
  );
});

// Activate event - clean up old caches
self.addEventListener('activate', function(event) {
  event.waitUntil(
    caches.keys().then(function(cacheNames) {
      return Promise.all(
        cacheNames.map(function(cacheName) {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

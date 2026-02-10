<!DOCTYPE html>
<html>
<head>
    <title>Test Share Functionality</title>
    <style>
        body {
            background: #1a1a1a;
            color: white;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .test-btn {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 10px;
        }
        .test-btn:hover {
            background: #ff5252;
        }
    </style>
</head>
<body>
    <h1>Share Functionality Test</h1>
    
    <div class="tour-item" data-tour-id="123">
        <h3 class="tour-info h3">Test Event</h3>
        <div class="tour-venue">Test Venue</div>
        <div class="tour-location">Test City, Test Country</div>
        <div class="tour-datetime">📅 January 15, 2024 🕒 8:00 PM</div>
        
        <button class="test-btn" onclick="shareTour(123)">Test Share Button</button>
    </div>
    
    <div class="tour-item" data-tour-id="456">
        <h3 class="tour-info h3">Another Test Event</h3>
        <div class="tour-venue">Another Venue</div>
        <div class="tour-location">Another City, Another Country</div>
        <div class="tour-datetime">📅 February 20, 2024 🕒 7:30 PM</div>
        
        <button class="test-btn" onclick="shareTour(456)">Test Share Button 2</button>
    </div>

    <script>
        // Mock showToast function
        function showToast(message, type) {
            console.log(`Toast [${type}]: ${message}`);
            alert(`${type.toUpperCase()}: ${message}`);
        }

        // Share functions from tour.php
        function shareTour(tourId) {
            console.log('🎯 Share button clicked! Tour ID:', tourId);
            
            // Get tour details
            const tourItem = document.querySelector(`[data-tour-id="${tourId}"]`);
            console.log('🔍 Found tour item:', tourItem);
            
            if (!tourItem) {
                console.error('❌ Tour not found for ID:', tourId);
                showToast('Tour not found', 'error');
                return;
            }
            
            const tourTitle = tourItem.querySelector('.tour-info h3');
            const tourVenue = tourItem.querySelector('.tour-venue');
            const tourLocation = tourItem.querySelector('.tour-location');
            const tourDate = tourItem.querySelector('.tour-datetime');
            
            console.log('📝 Tour elements:', {
                title: tourTitle,
                venue: tourVenue,
                location: tourLocation,
                date: tourDate
            });
            
            if (!tourTitle || !tourVenue) {
                console.error('❌ Missing tour information elements');
                showToast('Tour information incomplete', 'error');
                return;
            }
            
            const titleText = tourTitle.textContent;
            const venueText = tourVenue.textContent;
            const locationText = tourLocation ? tourLocation.textContent.trim() : '';
            const dateText = tourDate ? tourDate.textContent.trim() : '';
            
            console.log('📋 Extracted tour data:', {
                title: titleText,
                venue: venueText,
                location: locationText,
                date: dateText
            });
            
            const shareText = `${titleText} - ${venueText}, ${locationText} on ${dateText}`;
            const shareUrl = window.location.href + '#tour-' + tourId;
            
            console.log('🔗 Share data:', {
                text: shareText,
                url: shareUrl
            });
            
            // Create share popup
            createSharePopup(titleText, shareText, shareUrl);
        }

        function createSharePopup(title, text, url) {
            console.log('🎬 Creating share popup:', { title, text, url });
            
            // Remove existing popup if any
            const existingPopup = document.querySelector('.share-popup');
            if (existingPopup) {
                existingPopup.remove();
            }
            
            // Create popup HTML
            const popup = document.createElement('div');
            popup.className = 'share-popup';
            popup.innerHTML = `
                <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999;" onclick="closeSharePopup()"></div>
                <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #333; padding: 20px; border-radius: 10px; z-index: 10000; max-width: 400px;">
                    <h3>Share "${title}"</h3>
                    <p>${text}</p>
                    <p><small>${url}</small></p>
                    <button onclick="closeSharePopup()" style="background: #ff6b6b; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">Close</button>
                </div>
            `;
            
            console.log('📦 Popup HTML created');
            
            // Add popup to page
            document.body.appendChild(popup);
            console.log('✅ Popup added to body');
        }

        function closeSharePopup() {
            console.log('🔴 Closing share popup');
            const popup = document.querySelector('.share-popup');
            if (popup) {
                popup.remove();
                console.log('✅ Popup removed');
            }
        }
    </script>
</body>
</html>

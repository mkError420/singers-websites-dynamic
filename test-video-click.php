<!DOCTYPE html>
<html>
<head>
    <title>Video Click Test</title>
    <style>
        .video-thumbnail { cursor: pointer; border: 2px solid #007bff; margin: 10px; padding: 10px; }
        .video-modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 9999; }
        .modal-content { position: relative; margin: 50px auto; max-width: 800px; background: white; padding: 20px; }
        .close-modal { position: absolute; top: 10px; right: 10px; cursor: pointer; font-size: 24px; }
        .video-container { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; }
        .video-container iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
    </style>
</head>
<body>
    <h1>Video Click Test</h1>
    
    <div class="video-item">
        <img src="https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg" 
             class="video-thumbnail"
             data-video-url="https://www.youtube.com/watch?v=dQw4w9WgXcQ"
             alt="Test Video">
        <p>Click this thumbnail to test video playback</p>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="video-modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="video-container">
                <iframe id="modalVideo" src="" frameborder="0" allowfullscreen></iframe>
            </div>
            <div class="modal-info">
                <h3 id="modalVideoTitle"></h3>
                <p id="modalVideoDescription"></p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            
            const videoThumbnails = document.querySelectorAll('.video-thumbnail');
            const videoModal = document.getElementById('videoModal');
            const closeModal = document.querySelector('.close-modal');
            
            console.log('Found thumbnails:', videoThumbnails.length);
            console.log('Found modal:', videoModal);
            console.log('Found close button:', closeModal);
            
            videoThumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Thumbnail clicked!');
                    
                    const videoUrl = this.dataset.videoUrl;
                    console.log('Video URL:', videoUrl);
                    
                    // Convert YouTube URL to embed format
                    let embedUrl = '';
                    if (videoUrl.includes('youtube.com/watch?v=')) {
                        const videoId = videoUrl.split('v=')[1]?.split('&')[0];
                        embedUrl = `https://www.youtube-nocookie.com/embed/${videoId}?autoplay=1`;
                    }
                    
                    console.log('Embed URL:', embedUrl);
                    
                    // Set video in modal
                    document.getElementById('modalVideoTitle').textContent = 'Test Video';
                    document.getElementById('modalVideoDescription').textContent = 'This is a test video';
                    document.getElementById('modalVideo').src = embedUrl;
                    
                    // Show modal
                    videoModal.style.display = 'block';
                });
            });
            
            // Close modal
            if (closeModal) {
                closeModal.addEventListener('click', function() {
                    videoModal.style.display = 'none';
                    document.getElementById('modalVideo').src = '';
                });
            }
            
            // Close on background click
            videoModal.addEventListener('click', function(e) {
                if (e.target === videoModal) {
                    videoModal.style.display = 'none';
                    document.getElementById('modalVideo').src = '';
                }
            });
        });
    </script>
</body>
</html>

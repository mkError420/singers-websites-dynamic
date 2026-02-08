// Singer Website - Main JavaScript File
// Modern, interactive functionality for the artist website

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initNavigation();
    initAudioPlayer();
    initScrollEffects();
    initContactForm();
    initNewsletterForm();
    initAnimations();
    initVideoModal();
    initLazyLoading();
});

// Navigation functionality
function initNavigation() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    const header = document.querySelector('header');
    
    // Mobile menu toggle
    if (mobileMenuToggle && navLinks) {
        mobileMenuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }
    
    // Close mobile menu when clicking on a link
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            navLinks.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        });
    });
    
    // Header scroll effect
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Active navigation highlighting
    const sections = document.querySelectorAll('section');
    const navLinksArray = Array.from(navItems);
    
    window.addEventListener('scroll', function() {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });
        
        navLinksArray.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
}

// Custom Audio Player
function initAudioPlayer() {
    const audioPlayer = document.getElementById('audioPlayer');
    const playPauseBtn = document.getElementById('playPauseBtn');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const progressBar = document.getElementById('progressBar');
    const currentTimeEl = document.getElementById('currentTime');
    const durationEl = document.getElementById('duration');
    const volumeSlider = document.getElementById('volumeSlider');
    const songItems = document.querySelectorAll('.song-item');
    
    if (!audioPlayer) return;
    
    let currentSongIndex = 0;
    let isPlaying = false;
    const songs = Array.from(songItems);
    
    // Load song
    function loadSong(index) {
        const song = songs[index];
        const audioSrc = song.dataset.audio;
        const title = song.querySelector('.song-title').textContent;
        const artist = song.querySelector('.song-artist').textContent;
        
        audioPlayer.src = audioSrc;
        document.getElementById('currentSongTitle').textContent = title;
        document.getElementById('currentSongArtist').textContent = artist;
        
        // Update active state
        songs.forEach(s => s.classList.remove('active'));
        song.classList.add('active');
    }
    
    // Play/Pause functionality
    function togglePlayPause() {
        if (isPlaying) {
            audioPlayer.pause();
            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
        } else {
            audioPlayer.play();
            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
        }
        isPlaying = !isPlaying;
    }
    
    // Event listeners
    if (playPauseBtn) {
        playPauseBtn.addEventListener('click', togglePlayPause);
    }
    
    // Song selection
    songItems.forEach((item, index) => {
        item.addEventListener('click', function() {
            currentSongIndex = index;
            loadSong(currentSongIndex);
            togglePlayPause();
        });
    });
    
    // Previous/Next buttons
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            currentSongIndex = (currentSongIndex - 1 + songs.length) % songs.length;
            loadSong(currentSongIndex);
            if (isPlaying) audioPlayer.play();
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            currentSongIndex = (currentSongIndex + 1) % songs.length;
            loadSong(currentSongIndex);
            if (isPlaying) audioPlayer.play();
        });
    }
    
    // Progress bar
    audioPlayer.addEventListener('timeupdate', function() {
        if (progressBar) {
            const progress = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressBar.style.width = progress + '%';
        }
        
        if (currentTimeEl) {
            currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        }
        
        if (durationEl && audioPlayer.duration) {
            durationEl.textContent = formatTime(audioPlayer.duration);
        }
    });
    
    // Progress bar click
    const progressContainer = document.querySelector('.progress-container');
    if (progressContainer) {
        progressContainer.addEventListener('click', function(e) {
            const width = this.clientWidth;
            const clickX = e.offsetX;
            const duration = audioPlayer.duration;
            audioPlayer.currentTime = (clickX / width) * duration;
        });
    }
    
    // Volume control
    if (volumeSlider) {
        volumeSlider.addEventListener('input', function() {
            audioPlayer.volume = this.value / 100;
        });
    }
    
    // Auto play next song
    audioPlayer.addEventListener('ended', function() {
        currentSongIndex = (currentSongIndex + 1) % songs.length;
        loadSong(currentSongIndex);
        audioPlayer.play();
    });
    
    // Load first song
    if (songs.length > 0) {
        loadSong(0);
    }
}

// Format time helper
function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

// Scroll effects and animations
function initScrollEffects() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll('.section-title, .song-item, .video-item, .tour-item');
    animateElements.forEach(el => observer.observe(el));
}

// Contact form handling
function initContactForm() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Sending...';
            submitBtn.disabled = true;
            
            // Send form data via AJAX
            fetch('includes/contact-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Message sent successfully!', 'success');
                    contactForm.reset();
                } else {
                    showToast(data.message || 'Failed to send message', 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

// Newsletter form handling
function initNewsletterForm() {
    const newsletterForm = document.getElementById('newsletterForm');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Subscribing...';
            submitBtn.disabled = true;
            
            // Send form data via AJAX
            fetch('includes/newsletter-handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Successfully subscribed to newsletter!', 'success');
                    newsletterForm.reset();
                } else {
                    showToast(data.message || 'Failed to subscribe', 'error');
                }
            })
            .catch(error => {
                showToast('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

// Toast notification system
function showToast(message, type = 'success') {
    // Remove existing toast
    const existingToast = document.querySelector('.toast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Show toast
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Video modal functionality
function initVideoModal() {
    const videoModal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    const closeModal = document.querySelector('.close-modal');
    const videoThumbnails = document.querySelectorAll('.video-thumbnail');
    
    if (videoModal && modalVideo) {
        videoThumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                const videoUrl = this.dataset.videoUrl;
                modalVideo.src = videoUrl;
                videoModal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        });
        
        if (closeModal) {
            closeModal.addEventListener('click', closeVideoModal);
        }
        
        videoModal.addEventListener('click', function(e) {
            if (e.target === videoModal) {
                closeVideoModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && videoModal.style.display === 'flex') {
                closeVideoModal();
            }
        });
    }
    
    function closeVideoModal() {
        videoModal.style.display = 'none';
        modalVideo.src = '';
        document.body.style.overflow = 'auto';
    }
}

// Lazy loading for images
function initLazyLoading() {
    const imageOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };
    
    const imageObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    }, imageOptions);
    
    const lazyImages = document.querySelectorAll('img[data-src]');
    lazyImages.forEach(img => imageObserver.observe(img));
}

// Smooth scroll for anchor links
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Parallax effect for hero section
function initParallax() {
    const hero = document.querySelector('.hero');
    
    if (hero) {
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const parallax = hero.style.backgroundPositionY || 0;
            hero.style.backgroundPositionY = -(scrolled * 0.5) + 'px';
        });
    }
}

// Dynamic year in footer
function updateYear() {
    const yearElements = document.querySelectorAll('.current-year');
    const currentYear = new Date().getFullYear();
    
    yearElements.forEach(el => {
        el.textContent = currentYear;
    });
}

// Initialize additional features
function initAnimations() {
    initSmoothScroll();
    initParallax();
    updateYear();
    
    // Add hover effects to interactive elements
    const interactiveElements = document.querySelectorAll('.btn, .cta-button, .song-item, .video-item');
    interactiveElements.forEach(el => {
        el.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        
        el.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Utility functions
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Performance optimization
const optimizedScroll = throttle(function() {
    // Scroll-based animations and effects
}, 16);

// Initialize optimized scroll listener
window.addEventListener('scroll', optimizedScroll);

// Error handling
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', e.error);
    showToast('An unexpected error occurred', 'error');
});

// Service Worker registration for PWA (optional)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

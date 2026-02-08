<?php
// Admin header helper function
function render_admin_header($title) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?> - <?php echo APP_NAME; ?></title>
        <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            .visit-website-btn {
                position: fixed;
                top: 20px;
                right: 20px;
                background: var(--primary-color);
                color: var(--text-primary);
                border: none;
                padding: 0.75rem 1.5rem;
                border-radius: 25px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                z-index: 1000;
                box-shadow: var(--shadow-lg);
            }
            
            .visit-website-btn:hover {
                background: var(--secondary-color);
                transform: translateY(-2px);
                box-shadow: var(--shadow-xl);
            }
            
            body {
                background: var(--dark-bg);
                margin: 0;
                padding: 0;
            }
        </style>
    </head>
    <body>
        <!-- Visit Website Button -->
        <a href="<?php echo APP_URL; ?>/" target="_blank" class="visit-website-btn">
            <i class="fas fa-external-link-alt"></i>
            Visit Website
        </a>
    <?php
}

function render_admin_footer() {
    ?>
    </body>
    </html>
    <?php
}
?>

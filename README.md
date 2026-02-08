# Singer Website - Complete Music Artist Platform

A modern, responsive website for musicians and artists built with PHP, MySQL, and modern web technologies.

## Features

### Frontend
- **Responsive Design**: Works perfectly on all devices
- **Dark Theme**: Modern dark UI with gradient accents
- **Music Player**: Custom audio player with playlist, shuffle, repeat
- **Video Gallery**: YouTube/Vimeo integration with modal viewing
- **Tour Dates**: Interactive tour calendar with filtering
- **Contact Form**: Secure contact with CSRF protection
- **Newsletter Subscription**: Email signup with validation
- **About Page**: Artist story and achievements
- **SEO Optimized**: Meta tags, Open Graph, Twitter Cards

### Backend
- **Admin Panel**: Secure login and dashboard
- **Content Management**: Manage songs, videos, tour dates
- **Contact Management**: View and respond to messages
- **Newsletter System**: Manage subscribers
- **Security**: CSRF protection, XSS prevention, SQL injection protection
- **Database**: MySQL with PDO for security

## Installation

### Prerequisites
- PHP 7.4+ with PDO extension
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache/Nginx)
- Composer (optional)

### Setup Instructions

1. **Clone/Download the Project**
   ```bash
   git clone <repository-url>
   cd website-singers
   ```

2. **Database Setup**
   - Create a new database: `singer_website`
   - Import the database schema:
   ```sql
   mysql -u username -p singer_website < database/schema.sql
   ```

3. **Configuration**
   - Edit `config/config.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'singer_website');
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   define('APP_NAME', 'Your Artist Name');
   define('APP_URL', 'http://your-domain.com');
   ```

4. **File Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 assets/images/
   ```

5. **Web Server Configuration**
   - Point your web server to the project root
   - Ensure `.htaccess` (if using Apache) is properly configured

## Default Login

- **URL**: `http://your-domain.com/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

⚠️ **Important**: Change the default admin password immediately after login!

## Project Structure

```
website-singers/
├── admin/                  # Admin panel
│   ├── login.php          # Admin login
│   ├── dashboard.php      # Admin dashboard
│   └── logout.php        # Admin logout
├── assets/                # Static assets
│   ├── css/
│   │   └── style.css     # Main stylesheet
│   ├── js/
│   │   └── main.js       # JavaScript functionality
│   └── images/           # Image assets
├── config/               # Configuration files
│   └── config.php        # Main configuration
├── database/             # Database files
│   └── schema.sql        # Database schema
├── includes/             # PHP includes
│   ├── database.php      # Database connection
│   ├── functions.php     # Utility functions
│   ├── header.php        # HTML header
│   ├── footer.php        # HTML footer
│   ├── contact-handler.php    # Contact form processor
│   └── newsletter-handler.php # Newsletter processor
├── uploads/              # User uploads
│   ├── songs/           # Audio files
│   ├── covers/          # Album covers
│   └── thumbnails/      # Video thumbnails
├── index.php            # Homepage
├── music.php           # Music page
├── videos.php          # Videos page
├── tour.php            # Tour dates page
├── about.php           # About page
├── contact.php         # Contact page
└── test-connection.php # Database test (remove in production)
```

## Usage

### Managing Content

1. **Login to Admin Panel**
   - Navigate to `/admin/login.php`
   - Enter your credentials

2. **Add Songs**
   - Go to Admin Dashboard → Songs
   - Upload audio files and cover art
   - Add metadata (title, artist, genre, etc.)

3. **Add Videos**
   - Go to Admin Dashboard → Videos
   - Add YouTube/Vimeo URLs
   - Upload thumbnails

4. **Manage Tour Dates**
   - Go to Admin Dashboard → Tour Dates
   - Add venue, date, ticket links

5. **View Messages**
   - Go to Admin Dashboard → Messages
   - Respond to fan inquiries

### Customization

#### Styling
- Edit `assets/css/style.css` for visual changes
- CSS variables are defined at the top for easy theme customization

#### Configuration
- Update `config/config.php` for:
  - Database settings
  - Email configuration
  - App name and URL
  - File upload limits

#### Features
- Add new functionality in `includes/functions.php`
- Create new admin pages in `admin/` directory

## Security Features

- **CSRF Protection**: All forms use CSRF tokens
- **XSS Prevention**: All output is escaped
- **SQL Injection Protection**: PDO prepared statements
- **Session Security**: Secure session configuration
- **Input Validation**: All user input is sanitized
- **Rate Limiting**: Contact form and newsletter protection

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Performance

- **Optimized Images**: Lazy loading for better performance
- **Minified CSS/JS**: Production-ready assets
- **Database Indexing**: Optimized queries
- **Caching**: Browser caching headers
- **CDN Ready**: Font Awesome via CDN

## API Endpoints

### Contact Form
```
POST /includes/contact-handler.php
Content-Type: application/x-www-form-urlencoded

Parameters:
- name (required)
- email (required)
- subject (required)
- message (required)
- csrf_token (required)
```

### Newsletter
```
POST /includes/newsletter-handler.php
Content-Type: application/x-www-form-urlencoded

Parameters:
- email (required)
- name (optional)
- csrf_token (required)
```

## Troubleshooting

### Database Connection Issues
1. Check `config/config.php` database settings
2. Verify database exists and user has permissions
3. Run `test-connection.php` to diagnose

### File Upload Issues
1. Check `uploads/` directory permissions
2. Verify `MAX_FILE_SIZE` in config
3. Check PHP upload limits in `php.ini`

### Email Not Sending
1. Configure SMTP settings in `config/config.php`
2. Check server mail configuration
3. Verify email addresses are valid

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Check the troubleshooting section
- Create an issue in the repository
- Use the contact form on the website

---

**Note**: This is a complete, production-ready website. Remember to:
1. Change default passwords
2. Configure email settings
3. Set up proper domain/SSL
4. Remove `test-connection.php` in production
5. Regularly update dependencies

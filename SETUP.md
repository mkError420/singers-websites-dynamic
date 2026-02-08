# How to Run Frontend and Backend

## Prerequisites
- XAMPP/WAMP/MAMP (or similar web server with PHP & MySQL)
- Web browser (Chrome, Firefox, etc.)

## Step 1: Start Your Web Server

### Using XAMPP (Recommended for Windows)
1. Open XAMPP Control Panel
2. Start **Apache** module
3. Start **MySQL** module
4. Both services should show green "Running" status

### Alternative: Built-in PHP Server
```bash
# Navigate to project directory
cd c:\xampp\htdocs\website-singers

# Start PHP development server
php -S localhost:8000
```

## Step 2: Database Setup

### Option A: Using phpMyAdmin (Easiest)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Create a new database named: `singer_website`
3. Click on the `singer_website` database
4. Click "Import" tab
5. Choose file: `database/schema.sql`
6. Click "Go" to import

### Option B: Using MySQL Command Line
```bash
# Connect to MySQL
mysql -u root -p

# Create database
CREATE DATABASE singer_website;

# Use the database
USE singer_website;

# Import the schema
SOURCE c:/xampp/htdocs/website-singers/database/schema.sql;
```

## Step 3: Configure the Application

Edit `config/config.php`:
```php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'singer_website');
define('DB_USER', 'root');        // Your MySQL username
define('DB_PASS', '');            // Your MySQL password

// Application settings
define('APP_NAME', 'Your Artist Name');
define('APP_URL', 'http://localhost/website-singers');
```

## Step 4: Access Your Website

### Frontend (Public Website)
Open your browser and go to:
```
http://localhost/website-singers
```

### Backend (Admin Panel)
Open your browser and go to:
```
http://localhost/website-singers/admin/login.php
```

**Default Login:**
- Username: `admin`
- Password: `admin123`

## Step 5: Test Everything

### Test Database Connection
Visit: `http://localhost/website-singers/test-connection.php`

You should see:
- ✅ Database connection successful
- ✅ All tables exist with sample data

### Test Frontend Features
1. Browse the homepage
2. Play music in the audio player
3. Watch videos in the gallery
4. View tour dates
5. Fill out contact form
6. Subscribe to newsletter

### Test Backend Features
1. Login to admin panel
2. View dashboard statistics
3. Manage songs, videos, tour dates
4. View contact messages
5. Manage newsletter subscribers

## Troubleshooting

### "Database Connection Failed"
1. Make sure MySQL is running in XAMPP
2. Check database name in `config/config.php`
3. Verify username/password are correct
4. Run `test-connection.php` for detailed error info

### "404 Not Found" or "Page Not Found"
1. Make sure Apache is running in XAMPP
2. Check that files are in `c:\xampp\htdocs\website-singers\`
3. Try accessing: `http://localhost/website-singers/index.php`

### "Permission Denied" Errors
1. Right-click `uploads` folder → Properties → Security
2. Add "Everyone" with "Full control" permissions
3. Repeat for `assets/images` folder

### "White Screen" or PHP Errors
1. Check XAMPP Apache error logs
2. Make sure PHP error reporting is enabled in `config/config.php`
3. Verify all PHP files have correct syntax

## Quick Start Checklist

- [ ] XAMPP installed
- [ ] Apache and MySQL running
- [ ] Database `singer_website` created
- [ ] Schema imported from `database/schema.sql`
- [ ] `config/config.php` updated with correct settings
- [ ] Frontend accessible at `http://localhost/website-singers`
- [ ] Admin login working at `http://localhost/website-singers/admin/login.php`
- [ ] Test connection page shows green status

## Development Workflow

### Making Changes
1. Edit files in your code editor
2. Refresh browser to see changes
3. No compilation or build steps needed

### Adding Content
1. Login to admin panel
2. Use the dashboard to add songs, videos, tour dates
3. Upload files through the admin interface

### Debugging
1. Use browser developer tools (F12)
2. Check Network tab for AJAX requests
3. View Console for JavaScript errors
4. Check `test-connection.php` for database issues

## Production Deployment

When ready to go live:
1. Change `APP_URL` in `config/config.php` to your domain
2. Update database credentials for production server
3. Remove `test-connection.php`
4. Change default admin password
5. Configure email settings for contact forms
6. Set up SSL certificate (HTTPS)

---

**Need Help?** Check the `README.md` file for detailed documentation or use the contact form once the site is running!

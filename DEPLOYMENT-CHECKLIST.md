# InfinityFree Deployment Checklist

## Pre-Deployment Checklist ✅

### Local Preparation
- [ ] Update `config/config.php` with production database credentials
- [ ] Change `APP_URL` to your InfinityFree domain
- [ ] Update `FROM_EMAIL` to your actual email address
- [ ] Test all functionality locally one last time
- [ ] Create `uploads/` folder if it doesn't exist
- [ ] Backup your local files

### InfinityFree Account Setup
- [ ] Create InfinityFree account
- [ ] Create new website/subdomain
- [ ] Note down FTP credentials
- [ ] Note down database credentials

## Deployment Steps 🚀

### Database Setup
- [ ] Access phpMyAdmin in InfinityFree cPanel
- [ ] Import `database/schema.sql` file
- [ ] Verify all tables were created
- [ ] Check if admin user was inserted correctly

### File Upload
- [ ] Connect to FTP using FileZilla
- [ ] Navigate to `htdocs` folder
- [ ] Upload all project files to `htdocs`
- [ ] Create `uploads/` folder on server
- [ ] Set folder permissions (755 for folders, 644 for files)

### Configuration
- [ ] Update database credentials in `config/config.php` on server
- [ ] Test database connection
- [ ] Verify website loads correctly
- [ ] Test admin panel login (admin/admin123)

## Post-Deployment Checklist 🔧

### Security
- [ ] Change default admin password immediately
- [ ] Update admin email address
- [ ] Verify HTTPS is working
- [ ] Test all forms and functionality

### Testing
- [ ] Homepage loads correctly
- [ ] All navigation links work
- [ ] Contact form submits successfully
- [ ] Admin panel accessible and functional
- [ ] File uploads work (if applicable)
- [ ] Database operations work correctly

### Final Checks
- [ ] Check for any PHP errors
- [ ] Verify email functionality
- [ ] Test on mobile devices
- [ ] Check page load speeds
- [ ] Monitor bandwidth usage

## Important Notes 📝

### InfinityFree Limitations
- **Bandwidth**: Limited daily bandwidth
- **Database**: Limited connections per hour
- **Email**: May have sending restrictions
- **File Size**: Upload size limits apply
- **PHP Functions**: Some functions may be disabled

### Recommended Actions
1. **Monitor Usage**: Check bandwidth regularly
2. **Backup Database**: Export via phpMyAdmin weekly
3. **Update Content**: Keep your site fresh
4. **Security**: Change passwords regularly

### Troubleshooting Quick Guide

**500 Internal Server Error**
```
→ Check file permissions (644 for files, 755 for folders)
→ Verify PHP syntax in config files
→ Check .htaccess syntax
```

**Database Connection Failed**
```
→ Verify database credentials in config.php
→ Check if database was imported correctly
→ Confirm database server is running
```

**Upload Not Working**
```
→ Check uploads folder permissions (755 or 777)
→ Verify PHP upload limits in cPanel
→ Check file size restrictions
```

**Admin Panel Issues**
```
→ Clear browser cache and cookies
→ Check session path permissions
→ Verify .htaccess settings
```

### Contact Information
- **InfinityFree Support**: Check their documentation
- **Database Issues**: Use phpMyAdmin to verify
- **File Issues**: Check FTP logs and permissions

---

## Quick Commands & URLs

### After Deployment URLs
- **Website**: `https://yourname.infinityfreeapp.com`
- **Admin Panel**: `https://yourname.infinityfreeapp.com/admin`
- **phpMyAdmin**: Access via InfinityFree cPanel

### Default Login
- **Username**: `admin`
- **Password**: `admin123` (CHANGE IMMEDIATELY!)

### File Paths
- **Root**: `/htdocs/`
- **Uploads**: `/htdocs/uploads/`
- **Config**: `/htdocs/config/config.php`

---

**🎉 Your website is now live on InfinityFree!**

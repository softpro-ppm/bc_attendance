# BC Attendance System

A production-ready, mobile-first attendance management web application built with PHP 8.2+, MySQL 8, and Material Design 3. This system replaces Excel-based attendance tracking with a clean, modern web interface optimized for mobile devices.

## 🚀 Features

### Core Functionality
- **Single Admin System**: Secure single-user administration with role-based access
- **Hierarchical Structure**: Constituency → Mandal → Batch → Candidate organization
- **Daily Attendance**: Mark attendance with Present/Absent/Late/Excused status
- **Real-time Dashboard**: Live statistics and attendance overview
- **Mobile-First Design**: Responsive interface optimized for touch devices

### Advanced Features
- **Dynamic Dropdowns**: Cascading selection (Constituency → Mandal → Batch → Candidates)
- **Universal Search**: Server-side search across all entities with pagination
- **Bulk Operations**: Mark all present/absent, bulk import/export
- **Comprehensive Reports**: Daily summaries, batch statistics, attendance trends
- **Excel Integration**: Import/export attendance data in XLSX & CSV formats

### Security & Performance
- **CSRF Protection**: Built-in CSRF token validation
- **SQL Injection Prevention**: Prepared statements throughout
- **Session Security**: Secure session handling with configurable timeouts
- **Rate Limiting**: Login attempt throttling
- **Audit Logging**: Complete action tracking for compliance

## 🛠️ Technology Stack

- **Backend**: PHP 8.2+ with PDO MySQL
- **Database**: MySQL 8.0+
- **Frontend**: Vanilla JavaScript, Material Design 3 CSS
- **Dependencies**: PhpSpreadsheet (Excel), Composer
- **Architecture**: MVC pattern with custom routing
- **Security**: CSRF tokens, prepared statements, session hardening

## 📋 Requirements

- **PHP**: 8.2 or higher
- **MySQL**: 8.0 or higher
- **Web Server**: Apache/Nginx with mod_rewrite enabled
- **Extensions**: PDO, PDO_MySQL, JSON, OpenSSL
- **Memory**: Minimum 256MB PHP memory limit
- **Storage**: At least 100MB free space

## 🚀 Installation

### 1. Download & Setup
```bash
# Clone or download the project
cd bc_attendance

# Install dependencies
composer install
```

### 2. Web Server Configuration
Ensure your web server points to the `public/` directory and has mod_rewrite enabled.

**Apache (.htaccess already included):**
```apache
# The .htaccess file is already configured
# Make sure mod_rewrite is enabled
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/bc_attendance/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Run Installation Wizard
1. Open your browser and navigate to `http://your-domain.com/install.php`
2. Follow the 3-step installation process:
   - **Step 1**: Database configuration
   - **Step 2**: Admin user creation
   - **Step 3**: Installation completion

### 4. Post-Installation
- Delete `install.php` for security
- Log in with your admin credentials
- Configure constituencies, mandals, and batches
- Add candidates to your batches

## 🗄️ Database Schema

The system creates the following tables:

- **users**: Admin user accounts
- **constituencies**: Geographic constituencies
- **mandals**: Mandal locations under constituencies
- **batches**: Training batches under mandals
- **candidates**: Trainees under batches
- **attendance**: Daily attendance records
- **settings**: System configuration
- **audit_log**: Action tracking
- **login_attempts**: Security monitoring

## 📱 Usage Guide

### Dashboard
- View system statistics and overview
- Quick access to main functions
- Recent attendance and upcoming batches

### Marking Attendance
1. Navigate to "Mark Attendance"
2. Select Constituency → Mandal → Batch
3. Choose date (defaults to today)
4. Mark status for each candidate (P/A/L/E)
5. Add optional notes
6. Save attendance

### Managing Master Data
- **Constituencies**: Add/edit geographic areas
- **Mandals**: Manage locations within constituencies
- **Batches**: Configure training programs
- **Candidates**: Add trainees to batches

### Reports
- **Daily Summary**: Date-based attendance overview
- **Batch Statistics**: Attendance percentages and trends
- **Export Options**: Excel/CSV downloads

### Import/Export
- **Bulk Import**: Upload Excel files with candidate data
- **Attendance Export**: Download attendance records
- **Data Migration**: Import from existing systems

## 🔧 Configuration

### Environment Settings
Edit `config/config.php` to modify:
- Database connection details
- Session configuration
- Security settings
- Pagination options
- Upload limits

### Key Configuration Options
```php
// Database
'database' => [
    'host' => 'localhost',
    'database' => 'bc_attendance',
    'username' => 'your_username',
    'password' => 'your_password'
],

// Security
'security' => [
    'login_max_attempts' => 5,
    'login_lockout_time' => 900,
    'password_min_length' => 8
],

// Pagination
'pagination' => [
    'default_per_page' => 20,
    'page_sizes' => [10, 20, 50, 100, 'all']
]
```

## 🔒 Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Input sanitization and output escaping
- **Session Security**: Configurable timeouts and secure cookies
- **Rate Limiting**: Login attempt throttling
- **Audit Logging**: Complete action tracking

## 📱 Mobile Optimization

- **Touch-Friendly**: Large touch targets and swipe gestures
- **Responsive Design**: Adapts to all screen sizes
- **Progressive Web App**: Works offline and installable
- **Fast Loading**: Optimized assets and minimal dependencies

## 🚀 Performance Features

- **Server-Side Pagination**: Efficient data loading
- **Universal Search**: Fast, indexed search across entities
- **Caching**: Session-based caching for frequently accessed data
- **Optimized Queries**: Efficient database queries with proper indexing

## 🔍 Troubleshooting

### Common Issues

**Installation Fails:**
- Check PHP version (8.2+ required)
- Verify MySQL connection details
- Ensure write permissions on config/ and storage/ directories

**Database Connection Errors:**
- Verify MySQL server is running
- Check username/password
- Ensure database exists and is accessible

**Page Not Found Errors:**
- Enable mod_rewrite (Apache)
- Check .htaccess file exists
- Verify web server configuration

**Upload Issues:**
- Check PHP upload limits in php.ini
- Verify storage/ directory permissions
- Check file size limits

### Debug Mode
Enable debug mode in `config/config.php`:
```php
'app' => [
    'debug' => true
]
```

## 📚 API Endpoints

The system provides RESTful API endpoints for dynamic functionality:

```
GET  /api/mandals?constituency_id={id}
GET  /api/batches?mandal_id={id}
GET  /api/candidates?batch_id={id}
GET  /api/attendance?batch_id={id}&date={date}
```

## 🔄 Updates & Maintenance

### Regular Maintenance
- Monitor audit logs for unusual activity
- Backup database regularly
- Check storage directory space
- Review login attempt logs

### Updating the System
1. Backup your database and files
2. Download the latest version
3. Replace application files (keep config/config.php)
4. Run any new database migrations
5. Test functionality

## 📄 License

This project is proprietary software. All rights reserved.

## 🤝 Support

For technical support or feature requests:
- Check the troubleshooting section
- Review error logs in `storage/logs/`
- Ensure all requirements are met
- Verify database connectivity

## 🎯 Roadmap

Future enhancements planned:
- Multi-language support
- Advanced reporting with charts
- Mobile app companion
- Integration APIs
- Advanced analytics
- Bulk SMS notifications

## 📊 System Requirements

### Minimum
- PHP 8.2+
- MySQL 8.0+
- 256MB RAM
- 100MB storage

### Recommended
- PHP 8.3+
- MySQL 8.0+
- 512MB RAM
- 1GB storage
- SSD storage for better performance

---

**BC Attendance System** - Modern, secure, and mobile-first attendance management for training programs.

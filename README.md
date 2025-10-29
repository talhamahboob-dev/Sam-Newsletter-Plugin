# Sam-Newsletter-Plugin
A professional WordPress Gutenberg block plugin for collecting and managing newsletter subscriptions. Built with security, performance, and user experience in mind for SaaS environments.

## Features

- **Custom Gutenberg Block**: Easy-to-use newsletter subscription form
- **AJAX Form Submission**: Real-time validation without page reloads
- **Admin Dashboard**: View and search subscribers
- **Secure Implementation**: Nonce verification, input sanitization, and prepared statements
- **Responsive Design**: Mobile-friendly UI matching WordPress standards
- **Search Functionality**: Filter subscribers by name or email
- **Custom Database Table**: Optimized storage with proper indexing

## Requirements 

- WordPress 5.8 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Installation

### Method 1: Manual Installation

1. Download the plugin files or clone the repository:
```bash
git clone https://github.com/yourusername/sam-newsletter.git
```

2. Upload the `sam-newsletter` folder to `/wp-content/plugins/` directory

3. Activate the plugin through the 'Plugins' menu in WordPress

4. The database table will be created automatically upon activation

### Method 2: ZIP Installation

1. Download the plugin as a ZIP file
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" and then "Activate"

## Plugin Structure

```
sam-newsletter/
├── sam-newsletter.php          # Main plugin file
├── uninstall.php              # Cleanup on deletion
├── README.md                  # Documentation
├── includes/
│   └── admin-page.php         # Admin dashboard template
├── assets/
│   ├── css/
│   │   ├── block.css          # Block styles (editor + frontend)
│   │   └── admin.css          # Admin dashboard styles
│   └── js/
│       ├── frontend.js        # Frontend AJAX handler
│       └── admin.js           # Admin search functionality
└── build/
    └── index.js               # Gutenberg block registration
```

## Usage

### Adding the Newsletter Block

1. Edit any page or post
2. Click the "+" button to add a new block
3. Search for "SAM Newsletter"
4. Add the block to your content
5. Publish the page

The block will display a form with:
- Name input field (required)
- Email input field (required)
- Subscribe button

### Managing Subscribers

1. Go to WordPress Admin → Newsletter
2. View all subscribers in a sortable table
3. Use the search box to filter by name or email

### Admin Features

- **Total Subscribers**: View count at a glance
- **Recent First**: Newest subscriptions appear at the top
- **Search**: Real-time AJAX search without page reload
- **Email Links**: Click any email to open your mail client

## Security Features

### Input Validation
- Client-side validation for immediate feedback
- Server-side validation for security
- Email format validation
- Name length restrictions (2-255 characters)

### Data Protection
- WordPress nonce verification for all AJAX requests
- Sanitization using WordPress functions (`sanitize_text_field`, `sanitize_email`)
- Prepared SQL statements to prevent injection
- Capability checks for admin functions

### Database Security
- Unique constraint on email field
- Auto-increment primary key
- Proper indexing for performance
- Charset and collation settings

## Technical Implementation

### Database Schema

Table: `wp_sam_newsletter`

| Column     | Type                | Constraints           |
|------------|---------------------|-----------------------|
| id         | BIGINT(20) UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| name       | VARCHAR(255)        | NOT NULL              |
| email      | VARCHAR(255)        | NOT NULL, UNIQUE      |
| created_at | DATETIME            | DEFAULT CURRENT_TIMESTAMP |

### AJAX Endpoints

**Frontend Subscription**
- Action: `sam_newsletter_subscribe`
- Method: POST
- Nonce: `sam_newsletter_nonce`
- Parameters: `name`, `email`

**Admin Search**
- Action: `sam_newsletter_search`
- Method: POST
- Nonce: `sam_newsletter_admin_nonce`
- Parameters: `search`

### Hooks Used

**Activation/Deactivation**
- `register_activation_hook` - Creates database table
- `register_deactivation_hook` - Cleanup (optional)
- `uninstall.php` - Removes all data when deleted

**WordPress Actions**
- `admin_menu` - Registers admin page
- `init` - Registers Gutenberg block
- `wp_ajax_*` - AJAX handlers
- `admin_enqueue_scripts` - Admin assets
 

### Text Translations

The plugin is translation-ready. Use text domain: `sam-newsletter`

## Testing

### Test Scenarios

1. **Form Submission**
   - Valid data submission
   - Duplicate email handling
   - Empty field validation
   - Invalid email format

2. **Admin Dashboard**
   - View all subscribers
   - Search functionality
   - Responsive design

3. **Security**
   - Nonce verification
   - SQL injection prevention
   - XSS protection

## Performance

- **Database**: Indexed columns for fast queries
- **AJAX**: Asynchronous requests prevent page reloads
- **Caching**: Compatible with WordPress object caching
- **Assets**: Minified CSS/JS in production

## Troubleshooting

### Form Not Submitting
- Check browser console for JavaScript errors
- Verify AJAX URL is correct
- Ensure nonce is being generated

### Database Table Not Created
- Check file permissions
- Verify database user has CREATE TABLE privileges
- Review activation code

### Search Not Working
- Clear browser cache
- Check admin AJAX nonce
- Verify user capabilities

## Upgrade Path

The plugin stores a database version option (`sam_newsletter_db_version`) for future migrations. To add migration logic:

```php
function upgrade_database() {
    $current_version = get_option('sam_newsletter_db_version');
    
    if (version_compare($current_version, '2.0.0', '<')) {
        // Run migration
    }
}
```

## Development Approach

### Design Decisions

1. **Dynamic Block Rendering**: Used PHP rendering for better security and flexibility
2. **Custom Table**: Separate table for performance and data isolation
3. **AJAX Implementation**: Better UX without page reloads
4. **WordPress Standards**: Followed WordPress Coding Standards throughout

### Code Quality

- PSR-2 compliant PHP code
- ES6 JavaScript
- Semantic HTML5
- BEM-inspired CSS naming
- Comprehensive inline documentation

## Production Checklist

Before deploying to production:

- [ ] Test on staging environment
- [ ] Verify database backups are configured
- [ ] Test with popular themes
- [ ] Check mobile responsiveness
- [ ] Validate all user inputs
- [ ] Review error logs
- [ ] Test AJAX under load
- [ ] Verify nonce implementation

## Video Walkthrough

[Link: https://share.vidyard.com/watch/4sZXjQfbxxGvxP5dQDoGe7]

The video covers:
- Plugin architecture overview
- Security implementation details
- AJAX flow demonstration
- Database structure explanation
- Admin dashboard tour


## License

This plugin is licensed under GPL v2 or later.



**Version**: 1.0.0  
**Last Updated**: October 28, 2025

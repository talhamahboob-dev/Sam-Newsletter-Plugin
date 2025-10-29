<?php
/**
 * Plugin Name: SAM Newsletter
 * Description: A Gutenberg block for collecting and managing newsletter subscriptions with AJAX submission and admin dashboard.
 * Version: 1.0.0
 * Author: Talha
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: sam-newsletter
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

define('SAM_NEWSLETTER_VERSION', '1.0.0');
define('SAM_NEWSLETTER_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SAM_NEWSLETTER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SAM_NEWSLETTER_TABLE', 'sam_newsletter');

 
class SAM_Newsletter_Plugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Register Gutenberg block
        add_action('init', array($this, 'register_block'));
        
        // AJAX handlers
        add_action('wp_ajax_sam_newsletter_subscribe', array($this, 'handle_subscription'));
        add_action('wp_ajax_nopriv_sam_newsletter_subscribe', array($this, 'handle_subscription'));
        add_action('wp_ajax_sam_newsletter_search', array($this, 'handle_search'));
        
        // Enqueue admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
     
    public function activate() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . SAM_NEWSLETTER_TABLE;
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY email (email),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        
        // Store database version
        add_option('sam_newsletter_db_version', SAM_NEWSLETTER_VERSION);
    }
    
     
    public function deactivate() {
        // Note: We don't delete the table on deactivation
        // Use uninstall.php for complete removal
        flush_rewrite_rules();
    }
    
  
    public function add_admin_menu() {
        add_menu_page(
            __('Newsletter Subscribers', 'sam-newsletter'),
            __('Newsletter', 'sam-newsletter'),
            'manage_options',
            'sam-newsletter',
            array($this, 'render_admin_page'),
            'dashicons-email-alt',
            30
        );
    }
    
     
    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        include SAM_NEWSLETTER_PLUGIN_DIR . 'includes/admin-page.php';
    }
    
     
    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_sam-newsletter' !== $hook) {
            return;
        }
        
        wp_enqueue_style(
            'sam-newsletter-admin',
            SAM_NEWSLETTER_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            SAM_NEWSLETTER_VERSION
        );
        
        wp_enqueue_script(
            'sam-newsletter-admin',
            SAM_NEWSLETTER_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            SAM_NEWSLETTER_VERSION,
            true
        );
        
        wp_localize_script('sam-newsletter-admin', 'samNewsletterAdmin', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sam_newsletter_admin_nonce')
        ));
    }
    
    
    public function register_block() {
        // Register block script
        wp_register_script(
            'sam-newsletter-block',
            SAM_NEWSLETTER_PLUGIN_URL . 'build/index.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            SAM_NEWSLETTER_VERSION
        );
        
        // Register block style
        wp_register_style(
            'sam-newsletter-block-style',
            SAM_NEWSLETTER_PLUGIN_URL . 'assets/css/block.css',
            array(),
            SAM_NEWSLETTER_VERSION
        );
        
        // Register frontend script
        wp_register_script(
            'sam-newsletter-frontend',
            SAM_NEWSLETTER_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            SAM_NEWSLETTER_VERSION,
            true
        );
        
        wp_localize_script('sam-newsletter-frontend', 'samNewsletterData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sam_newsletter_nonce')
        ));
        
        // Register block
        register_block_type('sam-newsletter/subscribe-form', array(
            'editor_script' => 'sam-newsletter-block',
            'editor_style' => 'sam-newsletter-block-style',
            'style' => 'sam-newsletter-block-style',
            'script' => 'sam-newsletter-frontend',
            'render_callback' => array($this, 'render_block')
        ));
    }
    
     
    public function render_block($attributes) {
        ob_start();
        ?>
        <div class="sam-newsletter-form-wrapper">
            <form class="sam-newsletter-form" data-nonce="<?php echo esc_attr(wp_create_nonce('sam_newsletter_nonce')); ?>">
                <div class="sam-newsletter-field">
                    <label for="sam-newsletter-name"><?php _e('Name', 'sam-newsletter'); ?> <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="sam-newsletter-name" 
                        name="name" 
                        placeholder="<?php esc_attr_e('Enter your name', 'sam-newsletter'); ?>"
                        required
                    />
                    <span class="sam-newsletter-error" data-field="name"></span>
                </div>
                
                <div class="sam-newsletter-field">
                    <label for="sam-newsletter-email"><?php _e('Email', 'sam-newsletter'); ?> <span class="required">*</span></label>
                    <input 
                        type="email" 
                        id="sam-newsletter-email" 
                        name="email" 
                        placeholder="<?php esc_attr_e('Enter your email', 'sam-newsletter'); ?>"
                        required
                    />
                    <span class="sam-newsletter-error" data-field="email"></span>
                </div>
                
                <button type="submit" class="sam-newsletter-submit">
                    <?php _e('Subscribe', 'sam-newsletter'); ?>
                </button>
                
                <div class="sam-newsletter-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }
    
     
    public function handle_subscription() {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sam_newsletter_nonce')) {
            wp_send_json_error(array(
                'message' => __('Security check failed. Please refresh the page and try again.', 'sam-newsletter')
            ));
        }
        
        // Sanitize and validate inputs
        $name = isset($_POST['name']) ? sanitize_text_field(trim($_POST['name'])) : '';
        $email = isset($_POST['email']) ? sanitize_email(trim($_POST['email'])) : '';
        
        // Validation
        $errors = array();
        
        if (empty($name)) {
            $errors['name'] = __('Name is required.', 'sam-newsletter');
        } elseif (strlen($name) < 2) {
            $errors['name'] = __('Name must be at least 2 characters.', 'sam-newsletter');
        } elseif (strlen($name) > 255) {
            $errors['name'] = __('Name is too long.', 'sam-newsletter');
        }
        
        if (empty($email)) {
            $errors['email'] = __('Email is required.', 'sam-newsletter');
        } elseif (!is_email($email)) {
            $errors['email'] = __('Please enter a valid email address.', 'sam-newsletter');
        }
        
        if (!empty($errors)) {
            wp_send_json_error(array(
                'message' => __('Please correct the errors below.', 'sam-newsletter'),
                'errors' => $errors
            ));
        }
        
        // Insert into database
        global $wpdb;
        $table_name = $wpdb->prefix . SAM_NEWSLETTER_TABLE;
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'created_at' => current_time('mysql')
            ),
            array('%s', '%s', '%s')
        );
        
        if ($result === false) {
            // Check if it's a duplicate email
            if (strpos($wpdb->last_error, 'Duplicate entry') !== false) {
                wp_send_json_error(array(
                    'message' => __('This email is already subscribed to our newsletter.', 'sam-newsletter'),
                    'errors' => array('email' => __('Email already exists.', 'sam-newsletter'))
                ));
            } else {
                wp_send_json_error(array(
                    'message' => __('An error occurred. Please try again later.', 'sam-newsletter')
                ));
            }
        }
        
        wp_send_json_success(array(
            'message' => __('Thank you for subscribing! We\'ll keep you updated.', 'sam-newsletter')
        ));
    }
    
     
    public function handle_search() {
        // Verify nonce and permissions
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sam_newsletter_admin_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Insufficient permissions'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . SAM_NEWSLETTER_TABLE;
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        
        $query = "SELECT * FROM $table_name";
        
        if (!empty($search)) {
            $query .= $wpdb->prepare(
                " WHERE name LIKE %s OR email LIKE %s",
                '%' . $wpdb->esc_like($search) . '%',
                '%' . $wpdb->esc_like($search) . '%'
            );
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $results = $wpdb->get_results($query);
        
        wp_send_json_success(array(
            'subscribers' => $results,
            'total' => count($results)
        ));
    }
}

// Initialize plugin
SAM_Newsletter_Plugin::get_instance();
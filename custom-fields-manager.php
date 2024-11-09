<?php
/**
 * Plugin Name: Custom Fields Manager
 * Plugin URI: https://yourwebsite.com/custom-fields-manager
 * Description: A powerful custom fields management plugin for WordPress
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: custom-fields-manager
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('CFM_VERSION', '1.0.0');
define('CFM_PATH', plugin_dir_path(__FILE__));
define('CFM_URL', plugin_dir_url(__FILE__));

// Main plugin class
class CustomFieldsManager {
    private static $instance = null;

    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }

    private function init() {
        // Load dependencies
        $this->load_dependencies();
        
        // Register post type
        add_action('init', array($this, 'register_post_type'));
        
        // Initialize admin
        if (is_admin()) {
            add_action('admin_menu', array($this, 'add_admin_menu'));
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        }
        
        // Initialize frontend
        add_action('init', array($this, 'init_frontend'));
    }

    private function load_dependencies() {
        // Include core files
        require_once CFM_PATH . 'includes/class-field-group.php';
        require_once CFM_PATH . 'includes/class-field-types.php';
        require_once CFM_PATH . 'includes/class-location-rules.php';
        require_once CFM_PATH . 'includes/class-field-renderer.php';
        require_once CFM_PATH . 'includes/class-ajax-handler.php';
        require_once CFM_PATH . 'includes/class-form-handler.php';
        require_once CFM_PATH . 'includes/class-field-display.php';
        require_once CFM_PATH . 'includes/class-tools-handler.php';

        // Initialize handlers
        CFM_Ajax_Handler::init();
        CFM_Form_Handler::init();
        CFM_Field_Display::init();
        CFM_Tools_Handler::init();
    }

    public function admin_enqueue_scripts($hook) {
        if (strpos($hook, 'custom-fields-manager') === false) {
            return;
        }

        wp_enqueue_style('cfm-admin', CFM_URL . 'assets/css/admin.css', array(), CFM_VERSION);
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('cfm-admin', CFM_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), CFM_VERSION, true);
        
        // Add localization for JavaScript
        wp_localize_script('cfm-admin', 'CFM_Data', array(
            'nonce' => wp_create_nonce('cfm_nonce'),
            'strings' => array(
                'confirmDelete' => __('Are you sure you want to delete this field?', 'custom-fields-manager'),
                'addRule' => __('Add Rule (AND)', 'custom-fields-manager'),
                'removeRule' => __('Remove Rule', 'custom-fields-manager'),
                'isEqualTo' => __('is equal to', 'custom-fields-manager'),
                'isNotEqualTo' => __('is not equal to', 'custom-fields-manager'),
            ),
            'ruleTypes' => CFM_Location_Rules::get_rule_types(),
            'postTypes' => self::get_post_types_for_js()
        ));
    }

    private static function get_post_types_for_js() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $types = array();
        
        foreach ($post_types as $post_type) {
            $types[] = array(
                'name' => $post_type->name,
                'label' => $post_type->label
            );
        }
        
        return $types;
    }

    public function add_admin_menu() {
        add_menu_page(
            __('Custom Fields', 'custom-fields-manager'),
            __('Custom Fields', 'custom-fields-manager'),
            'manage_options',
            'custom-fields-manager',
            array($this, 'admin_page'),
            'dashicons-admin-generic',
            30
        );
    }

    public function admin_page() {
        include CFM_PATH . 'admin/views/main-page.php';
    }

    public function init_frontend() {
        // Initialize frontend functionality
    }

    public function register_post_type() {
        register_post_type('cfm_field_group', array(
            'labels' => array(
                'name' => __('Field Groups', 'custom-fields-manager'),
                'singular_name' => __('Field Group', 'custom-fields-manager'),
            ),
            'public' => false,
            'show_ui' => false,
            'capability_type' => 'post',
            'capabilities' => array(
                'edit_post' => 'manage_options',
                'delete_post' => 'manage_options',
                'edit_posts' => 'manage_options',
                'delete_posts' => 'manage_options',
            ),
            'supports' => array('title'),
        ));
    }

    public static function get_settings() {
        return get_option('cfm_settings', array(
            'google_maps_api_key' => '',
            'load_jquery' => false,
            'disable_wysiwyg' => false,
            'style_mode' => 'default'
        ));
    }
}

// Initialize the plugin
function custom_fields_manager() {
    return CustomFieldsManager::instance();
}

// Start the plugin
custom_fields_manager();
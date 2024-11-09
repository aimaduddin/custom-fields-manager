<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Ajax_Handler {
    public static function init() {
        add_action('wp_ajax_cfm_add_field', array(__CLASS__, 'add_field'));
        add_action('wp_ajax_cfm_add_location_rule_group', array(__CLASS__, 'add_location_rule_group'));
    }

    public static function add_field() {
        check_ajax_referer('cfm_nonce', 'nonce');

        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'text';
        $default_label = sprintf(__('New %s Field', 'custom-fields-manager'), ucfirst($type));
        
        $field = array(
            'type' => $type,
            'label' => $default_label,
            'name' => self::generate_field_name($default_label),
            'instructions' => '',
            'required' => 0,
            'default_value' => '',
        );

        ob_start();
        CFM_Field_Renderer::render_field_settings($field);
        $html = ob_get_clean();

        wp_send_json_success(array('html' => $html));
    }

    public static function add_location_rule_group() {
        check_ajax_referer('cfm_nonce', 'nonce');

        $group_id = uniqid();
        $rule = array(
            'param' => 'post_type',
            'operator' => '==',
            'value' => 'post'
        );

        ob_start();
        CFM_Location_Rules::render_rule_group(array($rule), $group_id);
        $html = ob_get_clean();

        wp_send_json_success(array('html' => $html));
    }

    /**
     * Generate a field name from the label
     */
    public static function generate_field_name($label) {
        // Convert to lowercase
        $name = strtolower($label);
        
        // Replace spaces and special characters with underscores
        $name = preg_replace('/[^a-z0-9]+/', '_', $name);
        
        // Remove leading/trailing underscores
        $name = trim($name, '_');
        
        // Add prefix to ensure uniqueness and identify custom fields
        $name = 'cf_' . $name;
        
        // Ensure uniqueness by adding a short unique suffix if needed
        $unique_suffix = substr(uniqid(), -4);
        $name .= '_' . $unique_suffix;
        
        return $name;
    }
} 
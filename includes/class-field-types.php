<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Field_Types {
    private static $field_types = array();

    public static function register_field_type($type, $args) {
        self::$field_types[$type] = $args;
    }

    public static function get_field_types() {
        if (empty(self::$field_types)) {
            self::register_default_field_types();
        }
        return self::$field_types;
    }

    private static function register_default_field_types() {
        // Text Field
        self::register_field_type('text', array(
            'label' => __('Text', 'custom-fields-manager'),
            'description' => __('Basic text input', 'custom-fields-manager'),
            'render_callback' => array('CFM_Field_Renderer', 'render_text_field'),
            'sanitize_callback' => 'sanitize_text_field'
        ));

        // Textarea Field
        self::register_field_type('textarea', array(
            'label' => __('Text Area', 'custom-fields-manager'),
            'description' => __('Multi-line text input', 'custom-fields-manager'),
            'render_callback' => array('CFM_Field_Renderer', 'render_textarea_field'),
            'sanitize_callback' => 'sanitize_textarea_field'
        ));

        // Number Field
        self::register_field_type('number', array(
            'label' => __('Number', 'custom-fields-manager'),
            'description' => __('Numeric input', 'custom-fields-manager'),
            'render_callback' => array('CFM_Field_Renderer', 'render_number_field'),
            'sanitize_callback' => 'absint'
        ));
    }
} 
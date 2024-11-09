<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Form_Handler {
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'handle_form_submission'));
    }

    public static function handle_form_submission() {
        if (!isset($_POST['cfm_nonce']) || !wp_verify_nonce($_POST['cfm_nonce'], 'cfm_save_field_group')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            return;
        }

        // Handle field group save
        if (isset($_POST['field_group_title'])) {
            self::save_field_group();
        }
    }

    private static function save_field_group() {
        $group_id = isset($_POST['cfm_group_id']) ? intval($_POST['cfm_group_id']) : 0;
        $field_group = new CFM_Field_Group($group_id);

        // Set basic information
        $field_group->set_title(sanitize_text_field($_POST['field_group_title']));
        $field_group->set_active(isset($_POST['field_group_status']) && $_POST['field_group_status'] === 'publish');

        // Process fields
        $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
        $sanitized_fields = array();

        foreach ($fields as $field_key => $field_data) {
            $sanitized_fields[$field_key] = array(
                'type' => sanitize_text_field($field_data['type']),
                'label' => sanitize_text_field($field_data['label']),
                'name' => sanitize_key($field_data['name']),
                'instructions' => sanitize_textarea_field($field_data['instructions']),
                'required' => isset($field_data['required']),
                'default_value' => sanitize_text_field($field_data['default_value']),
            );
        }

        $field_group->set_fields($sanitized_fields);

        // Process location rules
        $location_rules = isset($_POST['location_rules']) ? $_POST['location_rules'] : array();
        $sanitized_rules = array();

        // Debug the incoming location rules
        error_log('Incoming location rules: ' . print_r($location_rules, true));

        if (!empty($location_rules) && is_array($location_rules)) {
            foreach ($location_rules as $group_key => $group_rules) {
                if (!empty($group_rules) && is_array($group_rules)) {
                    foreach ($group_rules as $rule_key => $rule) {
                        if (isset($rule['param'], $rule['operator'], $rule['value'])) {
                            // Ensure the operator is either '==' or '!='
                            $operator = in_array($rule['operator'], array('==', '!=')) ? $rule['operator'] : '==';
                            
                            $sanitized_rules[$group_key][] = array(
                                'param' => sanitize_text_field($rule['param']),
                                'operator' => $operator,
                                'value' => sanitize_text_field($rule['value'])
                            );
                            
                            // Debug each rule being processed
                            error_log('Processing rule: ' . print_r($sanitized_rules[$group_key][count($sanitized_rules[$group_key]) - 1], true));
                        }
                    }
                }
            }
        }

        // If no rules were saved, set a default rule
        if (empty($sanitized_rules)) {
            $sanitized_rules = array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post'
                    )
                )
            );
        }

        // Debug the final sanitized rules
        error_log('Final sanitized rules: ' . print_r($sanitized_rules, true));

        // Set the location rules
        $field_group->set_location($sanitized_rules);

        // Save the field group
        $saved_id = $field_group->save();

        if ($saved_id) {
            // Debug the saved location rules
            $saved_rules = get_post_meta($saved_id, 'location', true);
            error_log('Saved location rules: ' . print_r($saved_rules, true));

            wp_redirect(add_query_arg(
                array(
                    'page' => 'custom-fields-manager',
                    'tab' => 'edit-field-group',
                    'id' => $saved_id,
                    'message' => 'saved'
                ),
                admin_url('admin.php')
            ));
            exit;
        }
    }
} 
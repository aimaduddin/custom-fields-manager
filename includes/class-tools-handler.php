<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Tools_Handler {
    public static function init() {
        add_action('admin_init', array(__CLASS__, 'handle_export'));
        add_action('admin_init', array(__CLASS__, 'handle_import'));
    }

    public static function handle_export() {
        if (!isset($_POST['cfm_export']) || !isset($_POST['cfm_export_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['cfm_export_nonce'], 'cfm_export_nonce')) {
            wp_die(__('Invalid nonce', 'custom-fields-manager'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to export field groups.', 'custom-fields-manager'));
        }

        $group_ids = isset($_POST['export_groups']) ? array_map('intval', $_POST['export_groups']) : array();
        
        if (empty($group_ids)) {
            return;
        }

        $export_data = array();

        foreach ($group_ids as $group_id) {
            $field_group = new CFM_Field_Group($group_id);
            $export_data[] = array(
                'title' => $field_group->get_title(),
                'fields' => $field_group->get_fields(),
                'location' => $field_group->get_location(),
                'active' => $field_group->is_active()
            );
        }

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="cfm-field-groups-' . date('Y-m-d') . '.json"');
        echo json_encode($export_data, JSON_PRETTY_PRINT);
        exit;
    }

    public static function handle_import() {
        if (!isset($_POST['cfm_import']) || !isset($_POST['cfm_import_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['cfm_import_nonce'], 'cfm_import_nonce')) {
            wp_die(__('Invalid nonce', 'custom-fields-manager'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to import field groups.', 'custom-fields-manager'));
        }

        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            wp_die(__('Please select a valid file to import.', 'custom-fields-manager'));
        }

        $file_content = file_get_contents($_FILES['import_file']['tmp_name']);
        $import_data = json_decode($file_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_die(__('Invalid JSON file.', 'custom-fields-manager'));
        }

        foreach ($import_data as $group_data) {
            $field_group = new CFM_Field_Group();
            $field_group->set_title($group_data['title']);
            $field_group->set_fields($group_data['fields']);
            $field_group->set_location($group_data['location']);
            $field_group->set_active($group_data['active']);
            $field_group->save();
        }

        wp_redirect(add_query_arg(
            array(
                'page' => 'custom-fields-manager',
                'tab' => 'tools',
                'message' => 'imported'
            ),
            admin_url('admin.php')
        ));
        exit;
    }
} 
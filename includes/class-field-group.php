<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Field_Group {
    private $id;
    private $title;
    private $fields;
    private $location;
    private $active;

    public function __construct($id = null) {
        $this->id = $id;
        $this->fields = array();
        $this->location = array();
        $this->active = true;
        $this->title = '';

        if ($id) {
            $this->load();
        }
    }

    public function load() {
        // Load field group data from database
        $post = get_post($this->id);
        if ($post) {
            $this->title = $post->post_title;
            $this->fields = get_post_meta($this->id, 'fields', true) ?: array();
            $this->location = get_post_meta($this->id, 'location', true) ?: array();
            $this->active = get_post_status($this->id) === 'publish';
        }
    }

    public function save() {
        $post_data = array(
            'post_title' => $this->title,
            'post_type' => 'cfm_field_group',
            'post_status' => $this->active ? 'publish' : 'draft'
        );

        if ($this->id) {
            $post_data['ID'] = $this->id;
            $this->id = wp_update_post($post_data);
        } else {
            $this->id = wp_insert_post($post_data);
        }

        if ($this->id) {
            update_post_meta($this->id, 'fields', $this->fields);
            update_post_meta($this->id, 'location', $this->location);
        }

        return $this->id;
    }

    // Getter methods
    public function get_id() {
        return $this->id;
    }

    public function get_title() {
        return $this->title;
    }

    public function get_fields() {
        return $this->fields;
    }

    public function get_location() {
        return $this->location;
    }

    public function is_active() {
        return $this->active;
    }

    // Setter methods
    public function set_title($title) {
        $this->title = sanitize_text_field($title);
    }

    public function add_field($field) {
        $this->fields[] = $field;
    }

    public function set_fields($fields) {
        $this->fields = $fields;
    }

    public function set_location($location) {
        $this->location = $location;
    }

    public function set_active($active) {
        $this->active = (bool) $active;
    }

    /**
     * Get formatted field label for display
     */
    public static function get_field_label($field_name, $field_data = null) {
        if ($field_data && isset($field_data['label']) && !empty($field_data['label'])) {
            return sprintf('%s (%s)', $field_data['label'], $field_name);
        }
        
        // If no field data provided or no label, try to make the field name readable
        $label = str_replace(array('cf_', '_'), array('', ' '), $field_name);
        $label = ucwords($label);
        // Remove the unique suffix (last 4 characters after a space)
        $label = preg_replace('/ [a-z0-9]{4}$/i', '', $label);
        
        return $label;
    }

    /**
     * Get all fields with formatted labels
     */
    public static function get_all_fields_with_labels() {
        $all_fields = array();
        
        $field_groups = get_posts(array(
            'post_type' => 'cfm_field_group',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        foreach ($field_groups as $group) {
            $fields = get_post_meta($group->ID, 'fields', true);
            if (!empty($fields)) {
                foreach ($fields as $field_name => $field_data) {
                    $all_fields[$field_name] = array(
                        'label' => self::get_field_label($field_name, $field_data),
                        'group' => $group->post_title,
                        'type' => $field_data['type']
                    );
                }
            }
        }

        return $all_fields;
    }
} 
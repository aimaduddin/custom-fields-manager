<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Field_Display {
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_action('save_post', array(__CLASS__, 'save_post_meta'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_post_editor_styles'));
        
        // Add Bricks Builder integration
        add_filter('bricks/builder/dynamic_data_sources', array(__CLASS__, 'add_to_bricks_builder'));
    }

    public static function enqueue_post_editor_styles() {
        $screen = get_current_screen();
        if ($screen && $screen->base === 'post') {
            wp_enqueue_style('cfm-post-editor', CFM_URL . 'assets/css/post-editor.css', array(), CFM_VERSION);
        }
    }

    public static function add_meta_boxes() {
        $field_groups = self::get_field_groups_for_current_screen();
        
        foreach ($field_groups as $field_group) {
            add_meta_box(
                'cfm-fields-' . $field_group->get_id(),
                $field_group->get_title(),
                array(__CLASS__, 'render_meta_box'),
                get_current_screen()->id,
                'normal',
                'high',
                array('field_group' => $field_group)
            );
        }
    }

    public static function render_meta_box($post, $metabox) {
        $field_group = $metabox['args']['field_group'];
        $fields = $field_group->get_fields();

        wp_nonce_field('cfm_save_post_meta', 'cfm_meta_nonce');
        ?>
        <div class="cfm-post-fields">
            <?php
            foreach ($fields as $field) {
                $field['value'] = get_post_meta($post->ID, $field['name'], true);
                self::render_post_field($field);
            }
            ?>
        </div>
        <?php
    }

    private static function render_post_field($field) {
        $field = wp_parse_args($field, array(
            'type' => 'text',
            'label' => '',
            'name' => '',
            'instructions' => '',
            'required' => false,
            'value' => '',
            'placeholder' => '',
            'class' => '',
        ));
        ?>
        <div class="cfm-post-field cfm-post-field-<?php echo esc_attr($field['type']); ?>">
            <div class="cfm-post-field-label">
                <label for="<?php echo esc_attr($field['name']); ?>">
                    <?php echo esc_html($field['label']); ?>
                    <?php if ($field['required']): ?>
                        <span class="required">*</span>
                    <?php endif; ?>
                </label>
                <?php if (!empty($field['instructions'])): ?>
                    <p class="description"><?php echo esc_html($field['instructions']); ?></p>
                <?php endif; ?>
            </div>
            <div class="cfm-post-field-input">
                <?php CFM_Field_Renderer::render_field($field, $field['value']); ?>
            </div>
        </div>
        <?php
    }

    public static function save_post_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['cfm_meta_nonce']) || !wp_verify_nonce($_POST['cfm_meta_nonce'], 'cfm_save_post_meta')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $field_groups = self::get_field_groups_for_post($post_id);
        
        foreach ($field_groups as $field_group) {
            $fields = $field_group->get_fields();
            foreach ($fields as $field) {
                if (isset($_POST[$field['name']])) {
                    $value = $_POST[$field['name']];
                    
                    // Get the sanitize callback for this field type
                    $field_type = CFM_Field_Types::get_field_types()[$field['type']] ?? null;
                    if ($field_type && isset($field_type['sanitize_callback'])) {
                        $value = call_user_func($field_type['sanitize_callback'], $value);
                    }
                    
                    update_post_meta($post_id, $field['name'], $value);
                }
            }
        }
    }

    private static function get_field_groups_for_current_screen() {
        $screen = get_current_screen();
        $post_type = $screen->post_type;
        
        return self::get_field_groups_by_location(array(
            'post_type' => $post_type
        ));
    }

    private static function get_field_groups_for_post($post_id) {
        $post_type = get_post_type($post_id);
        
        return self::get_field_groups_by_location(array(
            'post_type' => $post_type
        ));
    }

    private static function get_field_groups_by_location($args) {
        $field_groups = array();
        
        $posts = get_posts(array(
            'post_type' => 'cfm_field_group',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ));

        foreach ($posts as $post) {
            $field_group = new CFM_Field_Group($post->ID);
            $location_rules = $field_group->get_location();

            if (self::location_rules_match($location_rules, $args)) {
                $field_groups[] = $field_group;
            }
        }

        return $field_groups;
    }

    private static function location_rules_match($location_rules, $args) {
        if (empty($location_rules)) {
            return false;
        }

        // Location rules are grouped with OR logic between groups
        foreach ($location_rules as $group) {
            $group_match = true;

            // Rules within a group use AND logic
            foreach ($group as $rule) {
                if (!isset($rule['param'], $rule['operator'], $rule['value'])) {
                    continue;
                }

                $match = false;
                switch ($rule['param']) {
                    case 'post_type':
                        $match = $args['post_type'] === $rule['value'];
                        break;
                    // Add more cases for other rule types
                }

                // Handle different operators
                if ($rule['operator'] === '!=') {
                    $match = !$match;
                }

                if (!$match) {
                    $group_match = false;
                    break;
                }
            }

            if ($group_match) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add custom fields to Bricks Builder
     */
    public static function add_to_bricks_builder($sources) {
        $fields = CFM_Field_Group::get_all_fields_with_labels();
        
        if (!empty($fields)) {
            $custom_fields = array();
            
            foreach ($fields as $field_name => $field_data) {
                $custom_fields[$field_name] = array(
                    'label' => $field_data['label'],
                    'group' => $field_data['group'],
                    'type' => $field_data['type']
                );
            }

            if (!empty($custom_fields)) {
                $sources['custom_fields'] = array(
                    'label' => __('Custom Fields', 'custom-fields-manager'),
                    'fields' => $custom_fields
                );
            }
        }

        return $sources;
    }
} 
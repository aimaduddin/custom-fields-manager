<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle Custom Post Type Creation
if (isset($_POST['cfm_create_cpt'])) {
    if (check_admin_referer('cfm_cpt_nonce')) {
        $post_type = sanitize_key($_POST['post_type']);
        $singular = sanitize_text_field($_POST['singular_name']);
        $plural = sanitize_text_field($_POST['plural_name']);
        
        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'add_new' => sprintf(__('Add New %s', 'custom-fields-manager'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'custom-fields-manager'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'custom-fields-manager'), $singular),
            'new_item' => sprintf(__('New %s', 'custom-fields-manager'), $singular),
            'view_item' => sprintf(__('View %s', 'custom-fields-manager'), $singular),
            'search_items' => sprintf(__('Search %s', 'custom-fields-manager'), $plural),
            'not_found' => sprintf(__('No %s found', 'custom-fields-manager'), strtolower($plural)),
            'not_found_in_trash' => sprintf(__('No %s found in Trash', 'custom-fields-manager'), strtolower($plural)),
        );

        $args = array(
            'labels' => $labels,
            'public' => isset($_POST['public']),
            'publicly_queryable' => isset($_POST['publicly_queryable']),
            'show_ui' => isset($_POST['show_ui']),
            'show_in_menu' => isset($_POST['show_in_menu']),
            'query_var' => true,
            'rewrite' => array('slug' => sanitize_title($_POST['slug'])),
            'capability_type' => 'post',
            'has_archive' => isset($_POST['has_archive']),
            'hierarchical' => isset($_POST['hierarchical']),
            'menu_position' => absint($_POST['menu_position']),
            'supports' => array_map('sanitize_text_field', $_POST['supports'] ?? array()),
            'menu_icon' => sanitize_text_field($_POST['menu_icon']),
        );

        // Save CPT to options for persistence
        $custom_post_types = get_option('cfm_custom_post_types', array());
        $custom_post_types[$post_type] = $args;
        update_option('cfm_custom_post_types', $custom_post_types);

        // Flush rewrite rules
        flush_rewrite_rules();

        echo '<div class="notice notice-success is-dismissible"><p>' . 
             sprintf(esc_html__('Custom Post Type "%s" created successfully.', 'custom-fields-manager'), $singular) . 
             '</p></div>';
    }
}

// Get existing custom post types
$custom_post_types = get_option('cfm_custom_post_types', array());

// Handle Delete Action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['post_type'])) {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'delete_cpt_' . $_GET['post_type'])) {
        wp_die(__('Security check failed', 'custom-fields-manager'));
    }

    $post_type_to_delete = sanitize_key($_GET['post_type']);
    $custom_post_types = get_option('cfm_custom_post_types', array());

    if (isset($custom_post_types[$post_type_to_delete])) {
        unset($custom_post_types[$post_type_to_delete]);
        update_option('cfm_custom_post_types', $custom_post_types);
        flush_rewrite_rules();
        
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             sprintf(esc_html__('Custom Post Type "%s" deleted successfully.', 'custom-fields-manager'), $post_type_to_delete) . 
             '</p></div>';
    }
}

// Handle Edit Action
$editing = false;
$edit_data = array();
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['post_type'])) {
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'edit_cpt_' . $_GET['post_type'])) {
        wp_die(__('Security check failed', 'custom-fields-manager'));
    }

    $post_type_to_edit = sanitize_key($_GET['post_type']);
    $custom_post_types = get_option('cfm_custom_post_types', array());

    if (isset($custom_post_types[$post_type_to_edit])) {
        $editing = true;
        $edit_data = $custom_post_types[$post_type_to_edit];
    }
}

// Handle Update
if (isset($_POST['cfm_update_cpt'])) {
    if (check_admin_referer('cfm_cpt_nonce')) {
        $post_type = sanitize_key($_POST['post_type']);
        $singular = sanitize_text_field($_POST['singular_name']);
        $plural = sanitize_text_field($_POST['plural_name']);
        
        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'add_new' => sprintf(__('Add New %s', 'custom-fields-manager'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'custom-fields-manager'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'custom-fields-manager'), $singular),
            'new_item' => sprintf(__('New %s', 'custom-fields-manager'), $singular),
            'view_item' => sprintf(__('View %s', 'custom-fields-manager'), $singular),
            'search_items' => sprintf(__('Search %s', 'custom-fields-manager'), $plural),
            'not_found' => sprintf(__('No %s found', 'custom-fields-manager'), strtolower($plural)),
            'not_found_in_trash' => sprintf(__('No %s found in Trash', 'custom-fields-manager'), strtolower($plural)),
        );

        $args = array(
            'labels' => $labels,
            'public' => isset($_POST['public']),
            'publicly_queryable' => isset($_POST['publicly_queryable']),
            'show_ui' => isset($_POST['show_ui']),
            'show_in_menu' => isset($_POST['show_in_menu']),
            'query_var' => true,
            'rewrite' => array('slug' => sanitize_title($_POST['slug'])),
            'capability_type' => 'post',
            'has_archive' => isset($_POST['has_archive']),
            'hierarchical' => isset($_POST['hierarchical']),
            'menu_position' => absint($_POST['menu_position']),
            'supports' => array_map('sanitize_text_field', $_POST['supports'] ?? array()),
            'menu_icon' => sanitize_text_field($_POST['menu_icon']),
        );

        $custom_post_types = get_option('cfm_custom_post_types', array());
        $custom_post_types[$post_type] = $args;
        update_option('cfm_custom_post_types', $custom_post_types);
        flush_rewrite_rules();

        echo '<div class="notice notice-success is-dismissible"><p>' . 
             sprintf(esc_html__('Custom Post Type "%s" updated successfully.', 'custom-fields-manager'), $singular) . 
             '</p></div>';
    }
}

// Update the form to handle both create and edit
?>

<div class="wrap">
    <!-- Existing Custom Post Types -->
    <?php if (!empty($custom_post_types)) : ?>
        <div class="cfm-existing-cpts">
            <h3><?php _e('Existing Custom Post Types', 'custom-fields-manager'); ?></h3>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Post Type', 'custom-fields-manager'); ?></th>
                        <th><?php _e('Singular Name', 'custom-fields-manager'); ?></th>
                        <th><?php _e('Plural Name', 'custom-fields-manager'); ?></th>
                        <th><?php _e('Actions', 'custom-fields-manager'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($custom_post_types as $post_type => $args) : ?>
                        <tr>
                            <td><?php echo esc_html($post_type); ?></td>
                            <td><?php echo esc_html($args['labels']['singular_name']); ?></td>
                            <td><?php echo esc_html($args['labels']['name']); ?></td>
                            <td>
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=custom-fields-manager&tab=custom-post-types&action=edit&post_type=' . $post_type), 'edit_cpt_' . $post_type); ?>">
                                    <?php _e('Edit', 'custom-fields-manager'); ?>
                                </a> |
                                <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=custom-fields-manager&tab=custom-post-types&action=delete&post_type=' . $post_type), 'delete_cpt_' . $post_type); ?>" 
                                   onclick="return confirm('<?php echo esc_js(sprintf(__('Are you sure you want to delete the "%s" post type?', 'custom-fields-manager'), $args['labels']['singular_name'])); ?>');">
                                    <?php _e('Delete', 'custom-fields-manager'); ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <!-- Create/Edit Form -->
    <div class="cfm-create-cpt">
        <h3><?php echo $editing ? __('Edit Custom Post Type', 'custom-fields-manager') : __('Create New Custom Post Type', 'custom-fields-manager'); ?></h3>
        <form method="post" action="">
            <?php wp_nonce_field('cfm_cpt_nonce'); ?>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="post_type"><?php _e('Post Type ID', 'custom-fields-manager'); ?></label>
                    </th>
                    <td>
                        <?php if ($editing): ?>
                            <input type="hidden" name="post_type" value="<?php echo esc_attr($_GET['post_type']); ?>">
                            <strong><?php echo esc_html($_GET['post_type']); ?></strong>
                        <?php else: ?>
                            <input type="text" name="post_type" id="post_type" class="regular-text" required>
                        <?php endif; ?>
                        <p class="description"><?php _e('Lowercase letters and underscores only (e.g., book, team_member)', 'custom-fields-manager'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="singular_name"><?php _e('Singular Name', 'custom-fields-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="singular_name" 
                               id="singular_name" 
                               class="regular-text" 
                               required 
                               value="<?php echo $editing ? esc_attr($edit_data['labels']['singular_name']) : ''; ?>">
                        <p class="description"><?php _e('e.g., Book, Team Member', 'custom-fields-manager'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="plural_name"><?php _e('Plural Name', 'custom-fields-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="plural_name" 
                               id="plural_name" 
                               class="regular-text" 
                               required 
                               value="<?php echo $editing ? esc_attr($edit_data['labels']['name']) : ''; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="slug"><?php _e('Slug', 'custom-fields-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="slug" 
                               id="slug" 
                               class="regular-text" 
                               required 
                               value="<?php echo $editing ? esc_attr($edit_data['rewrite']['slug']) : ''; ?>">
                        <p class="description"><?php _e('The URL slug (e.g., books, team-members)', 'custom-fields-manager'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="menu_icon"><?php _e('Menu Icon', 'custom-fields-manager'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               name="menu_icon" 
                               id="menu_icon" 
                               class="regular-text" 
                               value="<?php echo $editing ? esc_attr($edit_data['menu_icon']) : 'dashicons-admin-post'; ?>">
                        <p class="description">
                            <?php _e('Dashicon class or URL. See', 'custom-fields-manager'); ?>
                            <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">
                                <?php _e('available icons', 'custom-fields-manager'); ?>
                            </a>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="menu_position"><?php _e('Menu Position', 'custom-fields-manager'); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               name="menu_position" 
                               id="menu_position" 
                               class="small-text" 
                               value="<?php echo $editing ? esc_attr($edit_data['menu_position']) : '20'; ?>">
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Supports', 'custom-fields-manager'); ?></th>
                    <td>
                        <fieldset>
                            <?php 
                            $support_options = array(
                                'title' => __('Title', 'custom-fields-manager'),
                                'editor' => __('Editor', 'custom-fields-manager'),
                                'thumbnail' => __('Featured Image', 'custom-fields-manager'),
                                'excerpt' => __('Excerpt', 'custom-fields-manager'),
                                'custom-fields' => __('Custom Fields', 'custom-fields-manager')
                            );
                            
                            foreach ($support_options as $value => $label) :
                                $checked = $editing ? 
                                    in_array($value, $edit_data['supports']) : 
                                    in_array($value, array('title', 'editor'));
                            ?>
                                <label>
                                    <input type="checkbox" 
                                           name="supports[]" 
                                           value="<?php echo esc_attr($value); ?>"
                                           <?php checked($checked); ?>>
                                    <?php echo esc_html($label); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </fieldset>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('Options', 'custom-fields-manager'); ?></th>
                    <td>
                        <fieldset>
                            <?php 
                            $options = array(
                                'public' => __('Public', 'custom-fields-manager'),
                                'publicly_queryable' => __('Publicly Queryable', 'custom-fields-manager'),
                                'show_ui' => __('Show UI', 'custom-fields-manager'),
                                'show_in_menu' => __('Show in Menu', 'custom-fields-manager'),
                                'has_archive' => __('Has Archive', 'custom-fields-manager'),
                                'hierarchical' => __('Hierarchical', 'custom-fields-manager')
                            );
                            
                            foreach ($options as $key => $label) :
                                $checked = $editing ? 
                                    !empty($edit_data[$key]) : 
                                    in_array($key, array('public', 'publicly_queryable', 'show_ui', 'show_in_menu', 'has_archive'));
                            ?>
                                <label>
                                    <input type="checkbox" 
                                           name="<?php echo esc_attr($key); ?>" 
                                           value="1"
                                           <?php checked($checked); ?>>
                                    <?php echo esc_html($label); ?>
                                </label><br>
                            <?php endforeach; ?>
                        </fieldset>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <button type="submit" name="<?php echo $editing ? 'cfm_update_cpt' : 'cfm_create_cpt'; ?>" class="button button-primary">
                    <?php echo $editing ? __('Update Custom Post Type', 'custom-fields-manager') : __('Create Custom Post Type', 'custom-fields-manager'); ?>
                </button>
                <?php if ($editing): ?>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=custom-post-types')); ?>" class="button">
                        <?php _e('Cancel', 'custom-fields-manager'); ?>
                    </a>
                <?php endif; ?>
            </p>
        </form>
    </div>
</div>

<style>
.cfm-create-cpt {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.cfm-existing-cpts {
    margin-bottom: 30px;
}

.form-table th {
    padding: 20px 10px 20px 0;
    width: 200px;
}

.form-table td {
    padding: 20px 10px;
}

.description {
    color: #666;
    font-style: italic;
    margin: 5px 0;
}

input[type="text"].regular-text {
    width: 100%;
    max-width: 400px;
}

fieldset label {
    display: block;
    margin-bottom: 8px;
}
</style> 
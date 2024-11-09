<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add success message handling
$message = isset($_GET['message']) ? $_GET['message'] : '';
if ($message === 'deleted') {
    echo '<div class="notice notice-success is-dismissible"><p>' . 
         esc_html__('Field group deleted successfully.', 'custom-fields-manager') . 
         '</p></div>';
}
?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php _e('Field Groups', 'custom-fields-manager'); ?>
    </h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=new-field-group')); ?>" class="page-title-action">
        <?php _e('Add New', 'custom-fields-manager'); ?>
    </a>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="column-title column-primary"><?php _e('Title', 'custom-fields-manager'); ?></th>
                <th scope="col"><?php _e('Fields', 'custom-fields-manager'); ?></th>
                <th scope="col"><?php _e('Location', 'custom-fields-manager'); ?></th>
                <th scope="col"><?php _e('Status', 'custom-fields-manager'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $field_groups = get_posts(array(
                'post_type' => 'cfm_field_group',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            ));

            if (!empty($field_groups)) :
                foreach ($field_groups as $group) :
                    $fields = get_post_meta($group->ID, 'fields', true) ?: array();
                    $location = get_post_meta($group->ID, 'location', true) ?: array();
                    ?>
                    <tr>
                        <td class="column-title column-primary">
                            <strong>
                                <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=edit-field-group&id=' . $group->ID)); ?>" class="row-title">
                                    <?php echo esc_html($group->post_title); ?>
                                </a>
                            </strong>
                            <div class="row-actions">
                                <span class="edit">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=edit-field-group&id=' . $group->ID)); ?>">
                                        <?php _e('Edit', 'custom-fields-manager'); ?>
                                    </a> |
                                </span>
                                <span class="duplicate">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=duplicate&id=' . $group->ID)); ?>">
                                        <?php _e('Duplicate', 'custom-fields-manager'); ?>
                                    </a> |
                                </span>
                                <span class="delete">
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=custom-fields-manager&action=delete&id=' . $group->ID), 'delete_field_group_' . $group->ID); ?>" 
                                       class="submitdelete" 
                                       onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this field group?', 'custom-fields-manager'); ?>');">
                                        <?php _e('Delete', 'custom-fields-manager'); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td><?php echo count($fields); ?></td>
                        <td><?php echo CFM_Location_Rules::get_location_label($location); ?></td>
                        <td><?php echo get_post_status($group->ID) === 'publish' ? __('Active', 'custom-fields-manager') : __('Inactive', 'custom-fields-manager'); ?></td>
                    </tr>
                <?php
                endforeach;
            else :
                ?>
                <tr>
                    <td colspan="4"><?php _e('No field groups found.', 'custom-fields-manager'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div> 
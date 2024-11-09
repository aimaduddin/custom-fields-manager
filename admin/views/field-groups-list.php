<?php
if (!defined('ABSPATH')) {
    exit;
}

// Get all field groups
$field_groups = get_posts(array(
    'post_type' => 'cfm_field_group',
    'posts_per_page' => -1,
    'orderby' => 'menu_order title',
    'order' => 'ASC',
));
?>

<div class="cfm-field-groups-list">
    <?php if (!empty($field_groups)) : ?>
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
                <?php foreach ($field_groups as $group) : 
                    if (!is_object($group)) {
                        continue;
                    }
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
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=custom-fields-manager&action=duplicate&id=' . $group->ID), 'duplicate_field_group'); ?>">
                                        <?php _e('Duplicate', 'custom-fields-manager'); ?>
                                    </a> |
                                </span>
                                <span class="trash">
                                    <a href="<?php echo wp_nonce_url(admin_url('admin.php?page=custom-fields-manager&action=delete&id=' . $group->ID), 'delete_field_group'); ?>" 
                                       class="submitdelete" 
                                       onclick="return confirm('<?php esc_attr_e('Are you sure you want to delete this field group?', 'custom-fields-manager'); ?>');">
                                        <?php _e('Delete', 'custom-fields-manager'); ?>
                                    </a>
                                </span>
                            </div>
                        </td>
                        <td><?php echo count($fields); ?></td>
                        <td>
                            <?php
                            if (!empty($location)) {
                                $locations = array();
                                foreach ($location as $group_rules) {
                                    if (is_array($group_rules)) {
                                        foreach ($group_rules as $rule) {
                                            if (is_array($rule) && isset($rule['param'])) {
                                                $locations[] = CFM_Location_Rules::get_rule_label($rule);
                                            }
                                        }
                                    }
                                }
                                echo esc_html(implode(', ', array_unique($locations)));
                            } else {
                                echo '—';
                            }
                            ?>
                        </td>
                        <td>
                            <?php 
                            $status = get_post_status($group->ID);
                            echo $status === 'publish' ? 
                                esc_html__('Active', 'custom-fields-manager') : 
                                esc_html__('Inactive', 'custom-fields-manager'); 
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <div class="cfm-no-field-groups">
            <p><?php _e('No field groups found.', 'custom-fields-manager'); ?></p>
            <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=new-field-group')); ?>" class="button button-primary">
                <?php _e('Create Your First Field Group', 'custom-fields-manager'); ?>
            </a>
        </div>
    <?php endif; ?>
</div> 
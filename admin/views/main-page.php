<?php
if (!defined('ABSPATH')) {
    exit;
}

$tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'custom-post-types';
?>

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e('Custom Fields Manager', 'custom-fields-manager'); ?></h1>

    <nav class="nav-tab-wrapper wp-clearfix">
        <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=custom-post-types')); ?>" 
           class="nav-tab <?php echo $tab === 'custom-post-types' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Custom Post Types', 'custom-fields-manager'); ?>
        </a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=field-groups')); ?>" 
           class="nav-tab <?php echo $tab === 'field-groups' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Field Groups', 'custom-fields-manager'); ?>
        </a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=tools')); ?>" 
           class="nav-tab <?php echo $tab === 'tools' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Tools', 'custom-fields-manager'); ?>
        </a>
        <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=settings')); ?>" 
           class="nav-tab <?php echo $tab === 'settings' ? 'nav-tab-active' : ''; ?>">
            <?php _e('Settings', 'custom-fields-manager'); ?>
        </a>
    </nav>

    <div class="cfm-admin-content">
        <?php
        switch ($tab) {
            case 'field-groups':
                include CFM_PATH . 'admin/views/field-groups-list.php';
                break;
            case 'new-field-group':
            case 'edit-field-group':
                include CFM_PATH . 'admin/views/field-group-edit.php';
                break;
            case 'tools':
                include CFM_PATH . 'admin/views/tools.php';
                break;
            case 'settings':
                include CFM_PATH . 'admin/views/settings.php';
                break;
            default:
                include CFM_PATH . 'admin/views/custom-post-types.php';
                break;
        }
        ?>
    </div>
</div> 
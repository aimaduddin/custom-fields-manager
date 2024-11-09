<?php
if (!defined('ABSPATH')) {
    exit;
}

$tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'field-groups';
?>

<div class="wrap cfm-admin">
    <h1 class="wp-heading-inline"><?php _e('Custom Fields Manager', 'custom-fields-manager'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=custom-fields-manager&tab=new-field-group')); ?>" class="page-title-action">
        <?php _e('Add New', 'custom-fields-manager'); ?>
    </a>
    <hr class="wp-header-end">

    <nav class="nav-tab-wrapper wp-clearfix">
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
                include CFM_PATH . 'admin/views/field-groups-list.php';
                break;
        }
        ?>
    </div>
</div> 
<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="cfm-tools-wrapper">
    <div class="cfm-tool-section">
        <h2><?php _e('Import/Export Field Groups', 'custom-fields-manager'); ?></h2>
        <div class="cfm-tool-content">
            <!-- Export Section -->
            <div class="cfm-export-section">
                <h3><?php _e('Export Field Groups', 'custom-fields-manager'); ?></h3>
                <p class="description">
                    <?php _e('Export your field groups as JSON files. You can choose specific field groups or export all.', 'custom-fields-manager'); ?>
                </p>
                <form method="post" action="">
                    <?php wp_nonce_field('cfm_export_nonce', 'cfm_export_nonce'); ?>
                    <div class="cfm-field">
                        <label class="cfm-label"><?php _e('Select Field Groups', 'custom-fields-manager'); ?></label>
                        <?php
                        $field_groups = get_posts(array(
                            'post_type' => 'cfm_field_group',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC'
                        ));
                        
                        if (!empty($field_groups)) : ?>
                            <div class="cfm-checkbox-list">
                                <?php foreach ($field_groups as $group) : ?>
                                    <label>
                                        <input type="checkbox" name="export_groups[]" value="<?php echo esc_attr($group->ID); ?>">
                                        <?php echo esc_html($group->post_title); ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <button type="submit" name="cfm_export" class="button button-primary">
                                <?php _e('Export Selected', 'custom-fields-manager'); ?>
                            </button>
                        <?php else : ?>
                            <p><?php _e('No field groups found.', 'custom-fields-manager'); ?></p>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Import Section -->
            <div class="cfm-import-section">
                <h3><?php _e('Import Field Groups', 'custom-fields-manager'); ?></h3>
                <p class="description">
                    <?php _e('Import field groups from JSON files. The imported field groups will be added to your existing groups.', 'custom-fields-manager'); ?>
                </p>
                <form method="post" enctype="multipart/form-data" action="">
                    <?php wp_nonce_field('cfm_import_nonce', 'cfm_import_nonce'); ?>
                    <div class="cfm-field">
                        <label class="cfm-label"><?php _e('Choose File', 'custom-fields-manager'); ?></label>
                        <input type="file" name="import_file" accept=".json">
                        <p class="description">
                            <?php _e('Select a JSON file exported from Custom Fields Manager.', 'custom-fields-manager'); ?>
                        </p>
                    </div>
                    <button type="submit" name="cfm_import" class="button button-primary">
                        <?php _e('Import', 'custom-fields-manager'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.cfm-tools-wrapper {
    max-width: 800px;
    margin: 20px 0;
}

.cfm-tool-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.cfm-tool-section h2 {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccd0d4;
}

.cfm-export-section,
.cfm-import-section {
    margin-bottom: 30px;
}

.cfm-export-section:last-child,
.cfm-import-section:last-child {
    margin-bottom: 0;
}

.cfm-checkbox-list {
    margin: 10px 0 20px;
}

.cfm-checkbox-list label {
    display: block;
    margin-bottom: 8px;
}

.cfm-checkbox-list input[type="checkbox"] {
    margin-right: 8px;
}

.description {
    color: #666;
    font-style: italic;
    margin: 5px 0 15px;
}

.cfm-field {
    margin-bottom: 20px;
}

.cfm-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
}
</style> 
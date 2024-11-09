<?php
if (!defined('ABSPATH')) {
    exit;
}

$group_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$field_group = new CFM_Field_Group($group_id);
$fields = $field_group->get_fields();
?>

<form id="cfm-field-group-form" method="post" action="">
    <?php wp_nonce_field('cfm_save_field_group', 'cfm_nonce'); ?>
    <input type="hidden" name="cfm_group_id" value="<?php echo $group_id; ?>">

    <div class="cfm-field-group-editor">
        <div class="cfm-main-content">
            <!-- Field Group Title -->
            <div class="cfm-field-group-settings">
                <div class="cfm-field">
                    <label class="cfm-label" for="field_group_title">
                        <?php _e('Field Group Title', 'custom-fields-manager'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" 
                           id="field_group_title" 
                           name="field_group_title" 
                           value="<?php echo esc_attr($field_group->get_title()); ?>" 
                           required>
                </div>

                <!-- Status -->
                <div class="cfm-field">
                    <label class="cfm-label">
                        <?php _e('Status', 'custom-fields-manager'); ?>
                    </label>
                    <select name="field_group_status">
                        <option value="publish" <?php selected($field_group->is_active(), true); ?>>
                            <?php _e('Active', 'custom-fields-manager'); ?>
                        </option>
                        <option value="draft" <?php selected($field_group->is_active(), false); ?>>
                            <?php _e('Inactive', 'custom-fields-manager'); ?>
                        </option>
                    </select>
                </div>

                <!-- Location Rules Section -->
                <div class="cfm-field">
                    <label class="cfm-label">
                        <?php _e('Location Rules', 'custom-fields-manager'); ?>
                    </label>
                    <div class="cfm-location-rules" id="cfm-location-rules">
                        <?php CFM_Location_Rules::render_location_rules($field_group->get_location()); ?>
                    </div>
                </div>
            </div>

            <!-- Fields List -->
            <div class="cfm-fields-list" id="cfm-fields-list">
                <?php
                if (!empty($fields)) {
                    foreach ($fields as $field) {
                        CFM_Field_Renderer::render_field_settings($field);
                    }
                }
                ?>
            </div>

            <div class="cfm-add-field">
                <button type="button" class="button button-primary" id="cfm-add-field-button">
                    <?php _e('Add Field', 'custom-fields-manager'); ?>
                </button>
            </div>

            <!-- Save Button -->
            <div class="cfm-submit-box">
                <input type="submit" class="button button-primary button-large" value="<?php _e('Save Field Group', 'custom-fields-manager'); ?>">
            </div>
        </div>
    </div>
</form>

<!-- Field Type Selection Modal -->
<div id="cfm-field-type-modal" class="cfm-modal" style="display: none;">
    <div class="cfm-modal-content">
        <span class="cfm-modal-close">&times;</span>
        <h2><?php _e('Select Field Type', 'custom-fields-manager'); ?></h2>
        <div class="cfm-field-types">
            <?php
            $field_types = CFM_Field_Types::get_field_types();
            foreach ($field_types as $type => $args) {
                ?>
                <div class="cfm-field-type" data-type="<?php echo esc_attr($type); ?>">
                    <h4><?php echo esc_html($args['label']); ?></h4>
                    <p><?php echo esc_html($args['description']); ?></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</div> 
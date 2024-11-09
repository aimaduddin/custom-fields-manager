<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Field_Renderer {
    /**
     * Render a field based on its type
     */
    public static function render_field($field, $value = '') {
        $field = wp_parse_args($field, array(
            'type' => 'text',
            'label' => '',
            'name' => '',
            'instructions' => '',
            'required' => false,
            'default_value' => '',
            'placeholder' => '',
            'class' => '',
        ));

        $value = $value ?: $field['default_value'];
        
        echo '<div class="cfm-field cfm-field-' . esc_attr($field['type']) . '">';
        echo '<label class="cfm-label">';
        echo esc_html($field['label']);
        if ($field['required']) {
            echo ' <span class="required">*</span>';
        }
        echo '</label>';

        if ($field['instructions']) {
            echo '<p class="cfm-instructions">' . esc_html($field['instructions']) . '</p>';
        }

        // Call the specific render method for this field type
        $method = "render_{$field['type']}_field";
        if (method_exists(__CLASS__, $method)) {
            self::$method($field, $value);
        }

        echo '</div>';
    }

    /**
     * Render text field
     */
    public static function render_text_field($field, $value) {
        ?>
        <input type="text" 
               id="<?php echo esc_attr($field['name']); ?>"
               name="<?php echo esc_attr($field['name']); ?>"
               value="<?php echo esc_attr($value); ?>"
               class="cfm-text-field <?php echo esc_attr($field['class']); ?>"
               placeholder="<?php echo esc_attr($field['placeholder']); ?>"
               <?php echo $field['required'] ? 'required' : ''; ?>>
        <?php
    }

    /**
     * Render textarea field
     */
    public static function render_textarea_field($field, $value) {
        ?>
        <textarea 
            id="<?php echo esc_attr($field['name']); ?>"
            name="<?php echo esc_attr($field['name']); ?>"
            class="cfm-textarea-field <?php echo esc_attr($field['class']); ?>"
            placeholder="<?php echo esc_attr($field['placeholder']); ?>"
            <?php echo $field['required'] ? 'required' : ''; ?>
            rows="4"><?php echo esc_textarea($value); ?></textarea>
        <?php
    }

    /**
     * Render number field
     */
    public static function render_number_field($field, $value) {
        $field = wp_parse_args($field, array(
            'min' => '',
            'max' => '',
            'step' => '1'
        ));
        ?>
        <input type="number" 
               id="<?php echo esc_attr($field['name']); ?>"
               name="<?php echo esc_attr($field['name']); ?>"
               value="<?php echo esc_attr($value); ?>"
               class="cfm-number-field <?php echo esc_attr($field['class']); ?>"
               <?php echo $field['min'] !== '' ? 'min="' . esc_attr($field['min']) . '"' : ''; ?>
               <?php echo $field['max'] !== '' ? 'max="' . esc_attr($field['max']) . '"' : ''; ?>
               step="<?php echo esc_attr($field['step']); ?>"
               <?php echo $field['required'] ? 'required' : ''; ?>>
        <?php
    }

    /**
     * Render field settings
     */
    public static function render_field_settings($field) {
        $field = wp_parse_args($field, array(
            'type' => 'text',
            'label' => '',
            'name' => '',
            'instructions' => '',
            'required' => false,
            'default_value' => '',
            'placeholder' => '',
            'class' => '',
        ));
        ?>
        <div class="cfm-field-settings" data-type="<?php echo esc_attr($field['type']); ?>">
            <div class="cfm-field-settings-header">
                <span class="cfm-field-handle"></span>
                <span class="cfm-field-label"><?php echo esc_html($field['label'] ?: __('New Field', 'custom-fields-manager')); ?></span>
                <span class="cfm-field-type"><?php echo esc_html($field['type']); ?></span>
                <button type="button" class="button cfm-toggle-field">
                    <?php _e('Toggle', 'custom-fields-manager'); ?>
                </button>
                <button type="button" class="button cfm-remove-field">
                    <?php _e('Remove', 'custom-fields-manager'); ?>
                </button>
            </div>
            <div class="cfm-field-settings-content">
                <input type="hidden" name="fields[<?php echo esc_attr($field['name']); ?>][type]" value="<?php echo esc_attr($field['type']); ?>">
                
                <div class="cfm-field">
                    <label class="cfm-label">
                        <?php _e('Field Label', 'custom-fields-manager'); ?>
                    </label>
                    <input type="text" 
                           name="fields[<?php echo esc_attr($field['name']); ?>][label]" 
                           value="<?php echo esc_attr($field['label']); ?>"
                           class="cfm-field-label-input">
                </div>

                <div class="cfm-field">
                    <label class="cfm-label">
                        <?php _e('Field Name', 'custom-fields-manager'); ?>
                    </label>
                    <div class="cfm-field-name-wrapper">
                        <input type="text" 
                               name="fields[<?php echo esc_attr($field['name']); ?>][name]" 
                               value="<?php echo esc_attr($field['name']); ?>"
                               class="cfm-field-name-input"
                               readonly>
                        <button type="button" 
                                class="button cfm-regenerate-name" 
                                title="<?php esc_attr_e('Regenerate field name', 'custom-fields-manager'); ?>">
                            <span class="dashicons dashicons-image-rotate"></span>
                        </button>
                    </div>
                    <p class="cfm-field-name-description">
                        <?php _e('The field name is used to store and access the field\'s value. It is generated automatically from the field label.', 'custom-fields-manager'); ?>
                    </p>
                </div>

                <div class="cfm-field">
                    <label class="cfm-label">
                        <?php _e('Instructions', 'custom-fields-manager'); ?>
                    </label>
                    <textarea name="fields[<?php echo esc_attr($field['name']); ?>][instructions]" 
                              class="cfm-field-instructions-input"><?php echo esc_textarea($field['instructions']); ?></textarea>
                </div>

                <div class="cfm-field">
                    <label class="cfm-label">
                        <input type="checkbox" 
                               name="fields[<?php echo esc_attr($field['name']); ?>][required]" 
                               value="1" 
                               <?php checked($field['required'], true); ?>>
                        <?php _e('Required?', 'custom-fields-manager'); ?>
                    </label>
                </div>

                <div class="cfm-field">
                    <label class="cfm-label">
                        <?php _e('Default Value', 'custom-fields-manager'); ?>
                    </label>
                    <input type="text" 
                           name="fields[<?php echo esc_attr($field['name']); ?>][default_value]" 
                           value="<?php echo esc_attr($field['default_value']); ?>"
                           class="cfm-field-default-value-input">
                </div>
            </div>
        </div>
        <?php
    }
} 
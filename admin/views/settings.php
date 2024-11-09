<?php
if (!defined('ABSPATH')) {
    exit;
}

// Handle settings save
if (isset($_POST['cfm_save_settings'])) {
    if (check_admin_referer('cfm_settings_nonce')) {
        // Sanitize and save settings
        $settings = array(
            'google_maps_api_key' => sanitize_text_field($_POST['google_maps_api_key'] ?? ''),
            'load_jquery' => isset($_POST['load_jquery']),
            'disable_wysiwyg' => isset($_POST['disable_wysiwyg']),
            'style_mode' => sanitize_text_field($_POST['style_mode'] ?? 'default')
        );
        
        update_option('cfm_settings', $settings);
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             esc_html__('Settings saved successfully.', 'custom-fields-manager') . 
             '</p></div>';
    }
}

// Get current settings
$settings = get_option('cfm_settings', array(
    'google_maps_api_key' => '',
    'load_jquery' => false,
    'disable_wysiwyg' => false,
    'style_mode' => 'default'
));
?>

<div class="wrap">
    <h1><?php _e('Settings', 'custom-fields-manager'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('cfm_settings_nonce'); ?>
        
        <div class="cfm-settings-section">
            <h2 class="title"><?php _e('General Settings', 'custom-fields-manager'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="google_maps_api_key">
                            <?php _e('Google Maps API Key', 'custom-fields-manager'); ?>
                        </label>
                    </th>
                    <td>
                        <input type="text" 
                               id="google_maps_api_key" 
                               name="google_maps_api_key" 
                               value="<?php echo esc_attr($settings['google_maps_api_key']); ?>" 
                               class="regular-text">
                        <p class="description">
                            <?php _e('Required for Google Maps field type. Get your API key', 'custom-fields-manager'); ?>
                            <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">
                                <?php _e('here', 'custom-fields-manager'); ?>
                            </a>.
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <div class="cfm-settings-section">
            <h2 class="title"><?php _e('Advanced Settings', 'custom-fields-manager'); ?></h2>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('jQuery Loading', 'custom-fields-manager'); ?></th>
                    <td>
                        <label for="load_jquery">
                            <input type="checkbox" 
                                   id="load_jquery" 
                                   name="load_jquery" 
                                   value="1" 
                                   <?php checked($settings['load_jquery']); ?>>
                            <?php _e('Load jQuery in frontend', 'custom-fields-manager'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Enable this if your theme doesn\'t include jQuery.', 'custom-fields-manager'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row"><?php _e('WYSIWYG Editor', 'custom-fields-manager'); ?></th>
                    <td>
                        <label for="disable_wysiwyg">
                            <input type="checkbox" 
                                   id="disable_wysiwyg" 
                                   name="disable_wysiwyg" 
                                   value="1" 
                                   <?php checked($settings['disable_wysiwyg']); ?>>
                            <?php _e('Disable WYSIWYG editor', 'custom-fields-manager'); ?>
                        </label>
                        <p class="description">
                            <?php _e('Use plain textarea instead of WYSIWYG editor for text area fields.', 'custom-fields-manager'); ?>
                        </p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="style_mode">
                            <?php _e('Style Mode', 'custom-fields-manager'); ?>
                        </label>
                    </th>
                    <td>
                        <select id="style_mode" name="style_mode">
                            <option value="default" <?php selected($settings['style_mode'], 'default'); ?>>
                                <?php _e('Default', 'custom-fields-manager'); ?>
                            </option>
                            <option value="minimal" <?php selected($settings['style_mode'], 'minimal'); ?>>
                                <?php _e('Minimal', 'custom-fields-manager'); ?>
                            </option>
                            <option value="custom" <?php selected($settings['style_mode'], 'custom'); ?>>
                                <?php _e('Custom', 'custom-fields-manager'); ?>
                            </option>
                        </select>
                        <p class="description">
                            <?php _e('Choose how field groups should be styled in the admin area.', 'custom-fields-manager'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <p class="submit">
            <button type="submit" name="cfm_save_settings" class="button button-primary">
                <?php _e('Save Settings', 'custom-fields-manager'); ?>
            </button>
        </p>
    </form>
</div>

<style>
.cfm-settings-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.cfm-settings-section h2.title {
    margin-top: 0;
    padding-bottom: 10px;
    border-bottom: 1px solid #ccd0d4;
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
</style> 
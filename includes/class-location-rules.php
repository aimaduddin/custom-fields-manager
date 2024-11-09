<?php
if (!defined('ABSPATH')) {
    exit;
}

class CFM_Location_Rules {
    public static function get_rule_types() {
        return array(
            'post_type' => __('Post Type', 'custom-fields-manager'),
            'page_template' => __('Page Template', 'custom-fields-manager'),
            'post_format' => __('Post Format', 'custom-fields-manager'),
            'taxonomy' => __('Taxonomy', 'custom-fields-manager'),
        );
    }

    public static function get_rule_label($rule) {
        if (!is_array($rule) || !isset($rule['param'])) {
            return __('Invalid Rule', 'custom-fields-manager');
        }

        $types = self::get_rule_types();
        return isset($types[$rule['param']]) ? $types[$rule['param']] : $rule['param'];
    }

    public static function render_location_rules($location_rules = array()) {
        if (empty($location_rules)) {
            $location_rules = array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'post'
                    )
                )
            );
        }
        ?>
        <div class="cfm-rules-description">
            <?php _e('Show this field group if', 'custom-fields-manager'); ?>
        </div>

        <div class="cfm-location-rules-wrapper">
            <?php foreach ($location_rules as $group_id => $group): ?>
                <div class="cfm-location-rule-group">
                    <?php foreach ($group as $rule_id => $rule): ?>
                        <div class="cfm-location-rule">
                            <select name="location_rules[<?php echo $group_id; ?>][<?php echo $rule_id; ?>][param]" class="cfm-rule-param">
                                <?php foreach (self::get_rule_types() as $value => $label): ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected($rule['param'], $value); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <select name="location_rules[<?php echo $group_id; ?>][<?php echo $rule_id; ?>][operator]" class="cfm-rule-operator">
                                <option value="==" <?php selected($rule['operator'], '=='); ?>><?php _e('is equal to', 'custom-fields-manager'); ?></option>
                                <option value="!=" <?php selected($rule['operator'], '!='); ?>><?php _e('is not equal to', 'custom-fields-manager'); ?></option>
                            </select>

                            <select name="location_rules[<?php echo $group_id; ?>][<?php echo $rule_id; ?>][value]" class="cfm-rule-value">
                                <?php 
                                $post_types = get_post_types(array('public' => true), 'objects');
                                foreach ($post_types as $post_type): ?>
                                    <option value="<?php echo esc_attr($post_type->name); ?>" <?php selected($rule['value'], $post_type->name); ?>>
                                        <?php echo esc_html($post_type->label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
} 
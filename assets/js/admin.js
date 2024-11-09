(function($) {
    'use strict';

    var CFM_Admin = {
        init: function() {
            this.bindEvents();
            this.initSortable();
            this.initFieldNameTracking();
            this.initTooltips();
        },

        bindEvents: function() {
            $('#cfm-add-field-button').on('click', this.showFieldTypeModal);
            $('.cfm-modal-close').on('click', this.hideFieldTypeModal);
            
            // Update the field type click binding
            $('.cfm-field-types').on('click', '.cfm-field-type:not(.processing)', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Stop event bubbling
                if (!$(e.target).closest('.cfm-field-settings').length) {
                    CFM_Admin.addNewField.call(this, e);
                }
            });

            $('#cfm-add-rule-group').on('click', this.addLocationRuleGroup);
            $(document).on('click', '.cfm-remove-field', this.removeField);
            
            // Update the toggle field binding
            $(document).on('click', '.cfm-field-settings-header', function(e) {
                if (!$(e.target).is('button')) {
                    CFM_Admin.toggleField.call(this, e);
                }
            });

            $(document).on('input', '.cfm-field-label-input', this.updateFieldName);
            $(document).on('click', '.cfm-regenerate-name', this.regenerateFieldName);
            $(document).on('click', '.cfm-add-rule', this.addLocationRule);
            $(document).on('click', '.cfm-add-rule-group', this.addRuleGroup);
            $(document).on('click', '.cfm-remove-rule', this.removeRule);
            $(document).on('change', '.cfm-location-rule select[name$="[param]"]', this.updateRuleValues);
            $(document).keyup(function(e) {
                if (e.key === "Escape") {
                    CFM_Admin.hideFieldTypeModal();
                }
            });
        },

        initSortable: function() {
            $('#cfm-fields-list').sortable({
                handle: '.cfm-field-handle',
                update: this.updateFieldOrder,
                placeholder: 'cfm-field-placeholder',
                forcePlaceholderSize: true
            });
        },

        initTooltips: function() {
            $('.cfm-help-tip').tipTip({
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            });
        },

        showFieldTypeModal: function() {
            $('.cfm-modal').fadeIn(200);
            $('body').addClass('modal-open');
        },

        hideFieldTypeModal: function() {
            $('.cfm-modal').fadeOut(200);
            $('body').removeClass('modal-open');
        },

        toggleField: function(e) {
            if (!$(e.target).is('button')) {
                var $content = $(this).siblings('.cfm-field-settings-content');
                var $fieldSettings = $(this).closest('.cfm-field-settings');
                
                $('.cfm-field-settings').not($fieldSettings).removeClass('active')
                    .find('.cfm-field-settings-content').slideUp(200);
                
                $fieldSettings.toggleClass('active');
                $content.slideToggle(200);
            }
        },

        addNewField: function(e) {
            e.preventDefault();
            var $button = $(this);
            
            // Prevent multiple clicks
            if ($button.hasClass('processing')) {
                return;
            }

            // Add processing class
            $button.addClass('processing');
            
            var type = $button.data('type');
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cfm_add_field',
                    type: type,
                    nonce: CFM_Data.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#cfm-fields-list').append(response.data.html);
                        CFM_Admin.hideFieldTypeModal();
                    }
                },
                complete: function() {
                    // Remove processing class
                    $button.removeClass('processing');
                }
            });
        },

        removeField: function(e) {
            e.preventDefault();
            if (confirm(CFM_Data.strings.confirmDelete)) {
                $(this).closest('.cfm-field-settings').remove();
            }
        },

        updateFieldOrder: function() {
            $('.cfm-field-settings').each(function(index) {
                $(this).find('.cfm-field-order').val(index);
            });
        },

        addLocationRuleGroup: function() {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cfm_add_location_rule_group',
                    nonce: CFM_Data.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $('#cfm-location-rules').append(response.data.html);
                    }
                }
            });
        },

        updateFieldName: function(e) {
            var $input = $(this);
            var $wrapper = $input.closest('.cfm-field-settings');
            var $nameInput = $wrapper.find('.cfm-field-name-input');
            
            // Only update if the name hasn't been manually edited
            if (!$nameInput.data('manually-edited')) {
                var label = $input.val();
                var name = CFM_Admin.generateFieldName(label);
                $nameInput.val(name);
            }
        },

        generateFieldName: function(label) {
            // Convert to lowercase
            var name = label.toLowerCase();
            
            // Replace spaces and special characters with underscores
            name = name.replace(/[^a-z0-9]+/g, '_');
            
            // Remove leading/trailing underscores
            name = name.replace(/^_+|_+$/g, '');
            
            // Add prefix
            name = 'cf_' + name;
            
            // Add unique suffix
            var uniqueSuffix = Math.random().toString(36).substr(2, 4);
            name += '_' + uniqueSuffix;
            
            return name;
        },

        // Add this to track manual edits to the name field
        initFieldNameTracking: function() {
            $(document).on('input', '.cfm-field-name-input', function() {
                $(this).data('manually-edited', true);
            });
        },

        updateFieldLabel: function($wrapper) {
            var label = $wrapper.find('.cfm-field-label-input').val() || 'Untitled Field';
            var type = $wrapper.find('input[name$="[type]"]').val();
            $wrapper.find('.cfm-field-settings-header .cfm-field-label').text(label);
            $wrapper.find('.cfm-field-settings-header .cfm-field-type').text(type);
        },

        addLocationRule: function(e) {
            e.preventDefault();
            var $group = $(this).closest('.cfm-location-rule-group');
            var groupId = $group.data('id');
            var ruleId = 'rule_' + Date.now();
            
            var ruleHtml = `
                <div class="cfm-location-rule" data-id="${ruleId}">
                    <select name="location_rules[${groupId}][${ruleId}][param]">
                        <option value="post_type"><?php _e('Post Type', 'custom-fields-manager'); ?></option>
                    </select>
                    <select name="location_rules[${groupId}][${ruleId}][operator]">
                        <option value="=="><?php _e('is equal to', 'custom-fields-manager'); ?></option>
                        <option value="!="><?php _e('is not equal to', 'custom-fields-manager'); ?></option>
                    </select>
                    <select name="location_rules[${groupId}][${ruleId}][value]">
                        <option value="post">Post</option>
                        <option value="page">Page</option>
                    </select>
                    <button type="button" class="button cfm-remove-rule">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </div>
            `;

            $(ruleHtml).insertBefore($group.find('.cfm-rule-group-controls'));
        },

        addRuleGroup: function(e) {
            e.preventDefault();
            var groupId = 'group_' + Date.now();
            var ruleId = 'rule_' + Date.now();
            
            var groupHtml = `
                <div class="cfm-location-rule-group" data-id="${groupId}">
                    <div class="cfm-location-rule" data-id="${ruleId}">
                        <select name="location_rules[${groupId}][${ruleId}][param]">
                            <option value="post_type"><?php _e('Post Type', 'custom-fields-manager'); ?></option>
                        </select>
                        <select name="location_rules[${groupId}][${ruleId}][operator]">
                            <option value="=="><?php _e('is equal to', 'custom-fields-manager'); ?></option>
                            <option value="!="><?php _e('is not equal to', 'custom-fields-manager'); ?></option>
                        </select>
                        <select name="location_rules[${groupId}][${ruleId}][value]">
                            <option value="post">Post</option>
                            <option value="page">Page</option>
                        </select>
                        <button type="button" class="button cfm-remove-rule">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </div>
                    <div class="cfm-rule-group-controls">
                        <button type="button" class="button cfm-add-rule">
                            <span class="dashicons dashicons-plus-alt2"></span>
                            <?php _e('Add Rule (AND)', 'custom-fields-manager'); ?>
                        </button>
                    </div>
                </div>
            `;

            $('.cfm-location-rules-wrapper').append(groupHtml);
        },

        removeRule: function(e) {
            e.preventDefault();
            var $rule = $(this).closest('.cfm-location-rule');
            var $group = $rule.closest('.cfm-location-rule-group');
            
            $rule.remove();
            
            // If this was the last rule in the group, remove the entire group
            if ($group.find('.cfm-location-rule').length === 0) {
                $group.remove();
            }
        },

        updateRuleValues: function(e) {
            var $select = $(this);
            var $group = $select.closest('.cfm-location-rule-group');
            var groupId = $group.data('id');
            var ruleId = $select.closest('.cfm-location-rule').data('id');
            
            var rule = $select.closest('.cfm-location-rule');
            var operator = rule.find('.operator-select').val();
            var value = rule.find('.value-select').val();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'cfm_update_location_rule',
                    group_id: groupId,
                    rule_id: ruleId,
                    operator: operator,
                    value: value,
                    nonce: CFM_Data.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update the rule display
                        rule.find('.operator-select').val(operator);
                        rule.find('.value-select').val(value);
                    }
                }
            });
        }
    };

    $(document).ready(function() {
        CFM_Admin.init();
    });

})(jQuery); 
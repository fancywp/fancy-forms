<?php
defined('ABSPATH') || die();

class FancyFormsFieldName extends FancyFormsFieldType {

    protected $type = 'name';

    protected function field_settings_for_type() {
        return array(
            'default' => false,
            'description' => false
        );
    }

    protected function sub_fields() {
        $field = $this->get_field();
        $name_layout = FancyFormsFields::get_option($field, 'name_layout');
        return array(
            'full' => array(
                'type' => 'text',
                'label' => '',
                'optional' => $name_layout !== 'full',
            ),
            'first' => array(
                'type' => 'text',
                'label' => esc_html__('First', 'fancy-forms'),
                'optional' => $name_layout === 'full',
            ),
            'middle' => array(
                'type' => 'text',
                'label' => esc_html__('Middle', 'fancy-forms'),
                'optional' => true,
            ),
            'last' => array(
                'type' => 'text',
                'label' => esc_html__('Last', 'fancy-forms'),
                'optional' => $name_layout === 'full',
            ),
        );
    }

    protected function show_after_default() {
        $sub_fields = $this->sub_fields();
        foreach ($sub_fields as $name => $sub_field) {
            $this->single_field($name, $sub_field);
        }
    }

    protected function single_field($name, $sub_field) {
        $field = $this->get_field();
        $field_id = $field['id'];
        $field_key = $field['field_key'];
        $label = $sub_field['label'];
        $type = $sub_field['type'];
        $desc = ($name !== 'full') ? $field['desc'][$name] : '';
        $placeholder = isset($field['placeholder'][$name]) ? $field['placeholder'][$name] : '';
        $value = isset($field['default_value'][$name]) ? $field['default_value'][$name] : '';
        ?>
        <div class="fancyforms-form-row fancyforms-sub-field-<?php echo esc_attr($name); ?> fancyforms-row-show-hide <?php echo!in_array($name, explode('_', $field['name_layout'])) ? 'fancyforms-hidden' : ''; ?>" data-sub-field-name="<?php echo esc_attr($name); ?>" data-field-id="<?php echo esc_attr($field_id); ?>">
            <div class="fancyforms-sub-field-label">
                <?php echo esc_html($label); ?>
            </div>

            <div class="fancyforms-grid-container">
                <div class="fancyforms-form-row fancyforms-grid-2">
                    <input type="text" name="default_value_<?php echo esc_attr($field_id); ?>[<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($value); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field_key); ?>-<?php echo esc_attr($name); ?>" data-changeatt="value">
                    <label class="fancyforms-field-desc"><?php esc_html_e('Default Value', 'fancy-forms'); ?></label>
                </div>
                <div class="fancyforms-form-row fancyforms-grid-2">
                    <input type="text" name="field_options[placeholder_<?php echo esc_attr($field_id); ?>][<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($placeholder); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field_key); ?>-<?php echo esc_attr($name); ?>" data-changeatt="placeholder">
                    <label class="fancyforms-field-desc"><?php esc_html_e('Placeholder', 'fancy-forms'); ?></label>
                </div>
                <div class="fancyforms-form-row fancyforms-grid-2">
                    <input type="text" name="field_options[desc_<?php echo esc_attr($field_id); ?>][<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($desc); ?>" data-changeme="<?php echo esc_attr('fancyforms-subfield-desc-' . $name . '-' . $field_id); ?>">
                    <label class="fancyforms-field-desc"><?php esc_html_e('Description', 'fancy-forms'); ?></label>
                </div>
            </div>
        </div>
        <?php
    }

    public function show_primary_options() {
        $field = $this->get_field();
        $field_id = $field['id'];
        $name_layout = FancyFormsFields::get_option($field, 'name_layout');
        ?>
        <div class="fancyforms-form-row">
            <label><?php esc_html_e('Name layout', 'fancy-forms'); ?></label>
            <select name="field_options[name_layout_<?php echo esc_attr($field_id); ?>]" data-field-id="<?php echo esc_attr($field_id); ?>" data-changeme="fancyforms-grouped-field-<?php echo esc_attr($field_id); ?>" data-changeatt="data-name-layout" data-row-show-hide="fancyforms-row-show-hide">
                <option value="full" <?php selected($name_layout, 'full'); ?>><?php esc_html_e('Full Name', 'fancy-forms'); ?></option>
                <option value="first_last" <?php selected($name_layout, 'first_last'); ?>><?php esc_html_e('First Last', 'fancy-forms'); ?></option>
                <option value="last_first" <?php selected($name_layout, 'last_first'); ?>><?php esc_html_e('Last First', 'fancy-forms'); ?></option>
                <option value="first_middle_last" <?php selected($name_layout, 'first_middle_last'); ?>><?php esc_html_e('First Middle Last', 'fancy-forms'); ?></option>
            </select>
        </div>
        <?php
    }

    protected function extra_field_default_opts() {
        $sub_fields = $this->sub_fields();
        $field_options = array();
        foreach ($sub_fields as $name => $fields) {
            $field_options['desc'][$name] = $fields['label'];
        }

        $field_options['name_layout'] = 'first_last';
        return $field_options;
    }

    public function sanitize_value(&$value) {
        $value = FancyFormsHelper::sanitize_value('sanitize_text_field', $value);
        return $value;
    }

    public function validate($args) {
        $errors = isset($args['errors']) ? $args['errors'] : array();
        $field = $this->get_field();

        if ($field->required == '1') {
            $sub_fields = $this->sub_fields();

            foreach ($sub_fields as $name => $sub_field) {
                if (isset($args['value'][$name]) && empty($args['value'][$name]) && !$sub_field['optional']) {
                    $errors['field' . esc_attr($args['id'])] = FancyFormsFields::get_error_msg($this->field, 'blank');
                }
            }
        }

        return $errors;
    }

    protected function input_html() {
        $field = $this->get_field();
        $field_id = $field['id'];
        $field_key = $field['field_key'];
        $name_layout = $field['name_layout'];
        $display = explode('_', $name_layout);
        ?>

        <div class="fancyforms-grouped-field" id="fancyforms-grouped-field-<?php echo esc_attr($field_id); ?>" data-name-layout="<?php echo esc_attr($name_layout); ?>">
            <?php
            $sub_fields = $this->sub_fields();

            foreach ($sub_fields as $name => $sub_field) {
                if (!is_admin() && !in_array($name, $display)) {
                    continue;
                }

                $value = isset($field['default_value'][$name]) ? $field['default_value'][$name] : '';
                $placeholder = isset($field['placeholder'][$name]) ? $field['placeholder'][$name] : '';
                $label = isset($field['desc'][$name]) ? $field['desc'][$name] : '';
                ?>

                <div id="fancyforms-subfield-container-<?php echo esc_attr($name) . '-' . esc_attr($field_id); ?>" class="fancyforms-subfield-element fancyforms-subfield-element-<?php echo esc_attr($name); ?>" data-sub-field-name="<?php echo esc_attr($name); ?>">
                    <input type="text" id="fancyforms-field-<?php echo esc_attr($field_key) . '-' . esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($this->html_name()) . '[' . esc_attr($name) . ']'; ?>" placeholder="<?php echo esc_attr($placeholder); ?>" >
                    <?php
                    if (!empty($label) || is_admin()) {
                        ?>
                        <div class="fancyforms-field-desc" id="fancyforms-subfield-desc-<?php echo esc_attr($name) . '-' . esc_attr($field_id); ?>"><?php echo esc_attr($label); ?></div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            }
            ?>
        </div>
        <?php
    }

}

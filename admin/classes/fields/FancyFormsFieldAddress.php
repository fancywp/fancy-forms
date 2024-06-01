<?php
defined('ABSPATH') || die();

class FancyFormsFieldAddress extends FancyFormsFieldType {

    protected $type = 'address';

    protected function field_settings_for_type() {
        return array(
            'default' => false
        );
    }

    protected function sub_fields() {
        return array(
            'line1' => array(
                'type' => 'text',
                'label' => esc_html__('Line 1', 'fancy-forms')
            ),
            'line2' => array(
                'type' => 'text',
                'label' => esc_html__('Line 2', 'fancy-forms')
            ),
            'city' => array(
                'type' => 'text',
                'label' => esc_html__('City', 'fancy-forms')
            ),
            'state' => array(
                'type' => 'text',
                'label' => esc_html__('State/Province', 'fancy-forms')
            ),
            'zip' => array(
                'type' => 'number',
                'label' => esc_html__('Zip/Postal', 'fancy-forms')
            ),
            'country' => array(
                'type' => 'select',
                'label' => esc_html__('Country', 'fancy-forms')
        ));
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
        $desc = $field['desc'][$name];
        $placeholder = isset($field['placeholder'][$name]) ? $field['placeholder'][$name] : '';
        $value = isset($field['default_value'][$name]) ? $field['default_value'][$name] : '';
        $disable = isset($field['disable'][$name]) ? $field['disable'][$name] : 'on';
        $country_grid_class = $name !== 'country' ? ' fancyforms-grid-2' : '';
        ?>
        <div class="fancyforms-form-row fancyforms-sub-field-<?php echo esc_attr($name); ?>" data-sub-field-name="<?php echo esc_attr($name); ?>" data-field-id="<?php echo esc_attr($field_id); ?>">
            <div class="fancyforms-sub-field-label">
                <?php echo esc_html($label); ?>
                <label class="fancyforms-field-show-hide">
                    <input type="hidden" name="field_options[disable_<?php echo esc_attr($field_id); ?>][<?php echo esc_attr($name); ?>]" value="on">
                    <input type="checkbox" name="field_options[disable_<?php echo esc_attr($field_id); ?>][<?php echo esc_attr($name); ?>]" id="fancyforms-disable-<?php echo esc_attr($name); ?>-<?php echo esc_attr($field_id); ?>" data-changeme="fancyforms-subfield-disable-<?php echo esc_attr($name); ?>-<?php echo esc_attr($field_id); ?>" value="off" data-disablefield="fancyforms-subfield-container-<?php echo esc_attr($name); ?>-<?php echo esc_attr($field_id); ?>" <?php checked(($disable == 'off'), true) ?>>
                    <label for="fancyforms-disable-<?php echo esc_attr($name); ?>-<?php echo esc_attr($field_id); ?>"></label>
                </label>
            </div>

            <div class="fancyforms-grid-container">
                <?php if ($name !== 'country') { ?>
                    <div class="fancyforms-form-row fancyforms-grid-2">
                        <input type="<?php echo esc_attr($type); ?>" name="default_value_<?php echo esc_attr($field_id); ?>[<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($value); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field_key); ?>-<?php echo esc_attr($name); ?>" data-changeatt="value">
                        <label class="fancyforms-field-desc"><?php esc_html_e('Default Value', 'fancy-forms'); ?></label>
                    </div>
                    <div class="fancyforms-form-row fancyforms-grid-2">
                        <input type="text" name="field_options[placeholder_<?php echo esc_attr($field_id); ?>][<?php echo esc_attr($name); ?>]" value="<?php echo esc_attr($placeholder); ?>" data-changeme="fancyforms-field-<?php echo esc_attr($field_key); ?>-<?php echo esc_attr($name); ?>" data-changeatt="placeholder">
                        <label class="fancyforms-field-desc"><?php esc_html_e('Placeholder', 'fancy-forms'); ?></label>
                    </div>
                <?php } ?>
                <div class="fancyforms-form-row<?php echo esc_attr($country_grid_class); ?>">
                    <input type="text" name="field_options[desc_<?php echo esc_attr($field_id); ?>][<?php echo esc_attr($name); ?>]" value="<?php echo esc_html($desc); ?>" data-changeme="fancyforms-subfield-desc-<?php echo esc_attr($name); ?>-<?php echo esc_attr($field_id); ?>">
                    <label class="fancyforms-field-desc"><?php esc_html_e('Description', 'fancy-forms'); ?></label>
                </div>
            </div>
        </div>
        <?php
    }

    protected function extra_field_default_opts() {
        $sub_fields = $this->sub_fields();
        $field_options = array();
        foreach ($sub_fields as $name => $fields) {
            $field_options['desc'][$name] = $fields['label'];
        }
        return $field_options;
    }

    public function validate($args) {
        $errors = isset($args['errors']) ? $args['errors'] : array();
        $field = $this->get_field();

        if ($field->required == '1') {
            $sub_fields = $this->sub_fields();
            unset($sub_fields['line2']);

            foreach ($sub_fields as $name => $sub_field) {
                if (isset($args['value'][$name]) && empty($args['value'][$name])) {
                    $errors['field' . $args['id']] = FancyFormsFields::get_error_msg($this->field, 'blank');
                }
            }
        }
        return $errors;
    }

    public function sanitize_value(&$value) {
        $value = FancyFormsHelper::sanitize_value('sanitize_text_field', $value);
        return $value;
    }

    protected function input_html() {
        $field = $this->get_field();
        $field_id = $field['id'];
        $field_key = $field['field_key'];
        ?>
        <div class="fancyforms-grouped-field" id="fancyforms-grouped-field-<?php echo esc_attr($field_id); ?>">
            <?php
            $sub_fields = $this->sub_fields();
            foreach ($sub_fields as $name => $sub_field) {
                $value = isset($field['default_value'][$name]) ? $field['default_value'][$name] : '';
                $placeholder = isset($field['placeholder'][$name]) ? $field['placeholder'][$name] : '';
                $disable = isset($field['disable'][$name]) ? $field['disable'][$name] : 'on';
                $class = $disable == 'off' ? ' fancyforms-hidden' : ' ';

                $label = isset($field['desc'][$name]) ? $field['desc'][$name] : '';
                $type = $sub_field['type'];

                if (is_admin() || $disable == 'on') {
                    ?>
                    <div id="fancyforms-subfield-container-<?php echo esc_attr($name) . '-' . esc_attr($field_id); ?>" class="fancyforms-subfield-element fancyforms-subfield-element-<?php echo esc_attr($name); ?> fancyforms-grid-6 <?php echo esc_attr($class); ?>" data-sub-field-name="<?php echo esc_attr($name); ?>">
                        <?php
                        if ($type !== 'select') {
                            ?>
                            <input type="<?php echo esc_attr($type); ?>" id="fancyforms-field-<?php echo esc_attr($field_key); ?>-<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>" name="<?php echo esc_attr($this->html_name()) . '[' . esc_attr($name) . ']'; ?>" placeholder="<?php echo esc_attr($placeholder); ?>" >
                            <?php
                        } else {
                            $this->get_country_select(FancyFormsHelper::get_countries());
                        }
                        ?>
                        <div class="fancyforms-field-desc" id="fancyforms-subfield-desc-<?php echo esc_attr($name); ?>-<?php echo esc_attr($field_id); ?>">
                            <?php echo esc_attr($label); ?>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
        <?php
    }

    protected function get_country_select($args) {
        $field = $this->get_field();
        $field_key = $field['field_key'];
        ?>
        <select id="<?php echo 'fancyforms-field-' . esc_attr($field_key) . '-country'; ?>" name="<?php echo esc_attr($this->html_name()) . '[country]'; ?>">
            <?php
            foreach ($args as $arg) {
                ?>
                <option value="<?php echo esc_html($arg); ?>"><?php echo esc_html($arg); ?></option>';
                <?php
            }
            ?>
        </select>
        <?php
    }

}

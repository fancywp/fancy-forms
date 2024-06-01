<?php
defined('ABSPATH') || die();

class FancyFormsFieldHidden extends FancyFormsFieldType {

    protected $type = 'hidden';

    public function field_settings_for_type() {
        return array(
            'max_width' => false,
            'css' => false,
            'description' => false,
            'required' => false,
            'label' => false
        );
    }

    public function set_value_before_save($value) {
        $val = $this->get_field();
        return $val->default_value;
    }

    protected function input_html() {
        if (is_admin() && !FancyFormsHelper::is_preview_page()) {
            ?>
            <label class="fancyforms-editor-field-label">
                <span class="fancyforms-editor-field-label-text"><?php esc_html_e('Hidden', 'fancy-forms'); ?></span>
            </label>
            <input type="text" <?php $this->field_attrs(); ?> />
            <p class="howto">
                <?php esc_html_e('Note: This field will not show in the form. Enter the value to be hidden.', 'fancy-forms'); ?>
            </p>
            <?php
        } else {
            ?>
            <input type="hidden" <?php $this->field_attrs(); ?> />
            <?php
        }
    }

}

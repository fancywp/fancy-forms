<?php
defined('ABSPATH') || die();

class FancyFormsFieldUserID extends FancyFormsFieldType {

    protected $type = 'user_id';

    public function field_settings_for_type() {
        return array(
            'max_width' => false,
            'default' => false,
            'css' => false,
            'description' => false,
            'required' => false,
            'label' => false
        );
    }

    public function get_user_id() {
        $user_ID = get_current_user_id();
        return $user_ID;
    }

    public function set_value_before_save($value) {
        $user_ID = $this->get_user_id();
        return $user_ID;
    }

    protected function input_html() {
        if (is_admin() && !FancyFormsHelper::is_preview_page()) {
            ?>
            <label class="fancyforms-editor-field-label">
                <span class="fancyforms-editor-field-label-text"><?php esc_html_e('User ID', 'fancy-forms'); ?></span>
            </label>
            <input type="text" value="<?php esc_attr_e('User ID fields will not show in your form.', 'fancy-forms'); ?>" disabled />
            <?php
        } else {
            ?>
            <input type="hidden" name="<?php echo esc_attr($this->html_name()); ?>" value="<?php echo esc_attr($this->get_user_id()); ?>" />
            <?php
        }
    }

}

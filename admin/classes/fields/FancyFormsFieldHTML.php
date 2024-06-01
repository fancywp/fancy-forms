<?php
defined('ABSPATH') || die();

class FancyFormsFieldHTML extends FancyFormsFieldType {

    protected $type = 'html';

    public function field_settings_for_type() {
        return array(
            'default' => false,
            'required' => false,
            'label' => false,
            'description' => false,
            'field_alignment' => true,
        );
    }

    protected function extra_field_default_opts() {
        return array(
            'field_alignment' => 'left',
        );
    }

    public function show_primary_options() {
        $field = $this->get_field();
        ?>
        <div class="fancyforms-form-row">
            <label><?php esc_html_e('Content', 'fancy-forms'); ?></label>
            <div class="fancyforms-form-text-editor">
                <?php
                $args = array(
                    'textarea_name' => 'field_options[description_' . absint($field['id']) . ']',
                    'textarea_rows' => 8,
                );
                $html_id = 'fancyforms-field-desc_' . absint($field['id']);
                wp_editor($field['description'], $html_id, $args);
                ?>
            </div>
        </div>
        <?php
    }

    public function input_html() {
        $field = $this->get_field();
        ?>
        <div class="fancyforms-custom-html-field">
            <?php
            if (is_admin() && !FancyFormsHelper::is_preview_page()) {
                ?>
                <div class="fancyforms-custom-html-preview">
                    <?php esc_html_e('Custom HTML - No Preview Available', 'fancy-forms'); ?>
                </div>
                <?php
            } else {
                echo wp_kses_post($field['description']);
            }
            ?>
        </div>
        <?php
    }

}

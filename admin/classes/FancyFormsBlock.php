<?php

defined('ABSPATH') || die();

class FancyFormsBlock {

    public function __construct() {
        add_action('init', array($this, 'register_block'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_editor_assets'));
    }

    public function register_block() {
        if (!function_exists('register_block_type')) {
            return;
        }

        register_block_type('fancy-forms/form-selector', array(
            'attributes' => array(
                'formId' => array(
                    'type' => 'string',
                )
            ),
            'editor_style' => 'fancy-forms-block-editor',
            'editor_script' => 'fancy-forms-block-editor',
            'render_callback' => array($this, 'get_form_html'),
        ));
    }

    public function enqueue_block_editor_assets() {
        wp_register_style('fancy-forms-block-editor', FANCYFORMS_URL . 'css/form-block.css', array('wp-edit-blocks'), FANCYFORMS_VERSION);
        wp_register_script('fancy-forms-block-editor', FANCYFORMS_URL . 'js/form-block.min.js', array('wp-blocks', 'wp-element', 'wp-i18n', 'wp-components'), FANCYFORMS_VERSION, true);

        $all_forms = FancyFormsHelper::get_all_forms_list_options();
        unset($all_forms['']);

        $form_block_data = array(
            'forms' => $all_forms,
            'i18n' => array(
                'title' => esc_html__('Fancy Forms', 'fancy-forms'),
                'description' => esc_html__('Select and display one of your forms.', 'fancy-forms'),
                'form_keywords' => array(
                    esc_html__('form', 'fancy-forms'),
                    esc_html__('contact', 'fancy-forms'),
                ),
                'form_select' => esc_html__('Select a Form', 'fancy-forms'),
                'form_settings' => esc_html__('Form Settings', 'fancy-forms'),
                'form_selected' => esc_html__('Form', 'fancy-forms'),
            ),
        );
        wp_localize_script('fancy-forms-block-editor', 'fancy_forms_block_data', $form_block_data);
    }

    public function get_form_html($attr) {
        $form_id = !empty($attr['formId']) ? absint($attr['formId']) : 0;
        if (empty($form_id)) {
            return '';
        }

        ob_start();
        FancyFormsPreview::show_form($form_id);
        return ob_get_clean();
    }

}

new FancyFormsBlock();
<?php
defined('ABSPATH') || die();

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin;

class FancyFormsElement extends Widget_Base {

    public function get_name() {
        return 'Fancy Forms';
    }

    public function get_title() {
        return esc_html__('Fancy Forms', 'fancy-forms');
    }

    public function get_icon() {
        return 'fancyformsicon fancyformsicon-form';
    }

    public function get_categories() {
        return array('basic');
    }

    public function get_keywords() {
        return array('Form', 'Fancy Forms', 'Fancy');
    }

    protected function register_controls() {

        $this->start_controls_section(
                'section_title', [
            'label' => esc_html__('Form', 'fancy-forms'),
            'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );

        $this->add_control(
                'fancyforms_form_id', [
            'label' => esc_html__('Select Form', 'fancy-forms'),
            'type' => Controls_Manager::SELECT2,
            'options' => FancyFormsHelper::get_all_forms_list_options(),
            'multiple' => false,
            'label_block' => true,
            'separator' => 'after'
                ]
        );

        $this->add_control(
                'new_form', [
            'type' => Controls_Manager::RAW_HTML,
            'raw' => sprintf(
                    wp_kses(esc_html__('To Create New Form', 'fancy-forms') . ' <a href="%s" target="_blank">' . esc_html__('Cick Here', 'fancy-forms') . '</a>', [
                'b' => [],
                'br' => [],
                'a' => [
                    'href' => [],
                    'target' => [],
                ],
                    ]), esc_url(add_query_arg('page', 'fancyforms', admin_url('admin.php')))
            )
                ]
        );

        $this->end_controls_section();
    }

    public function render() {
        $settings = $this->get_settings_for_display();

        if (isset($settings['fancyforms_form_id']) && !empty($settings['fancyforms_form_id']) && (FancyFormsListing::get_status($settings['fancyforms_form_id']) == 'published')) {
            echo do_shortcode('[fancyforms id="' . $settings['fancyforms_form_id'] . '"]');
        } elseif ($this->elementor()->editor->is_edit_mode()) {
            ?>
            <p><?php echo esc_html__('Please select a Form', 'fancy-forms'); ?></p>
            <?php
        }
    }

    protected function elementor() {
        return Plugin::$instance;
    }

}

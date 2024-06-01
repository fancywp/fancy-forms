<?php
defined('ABSPATH') || die();

class FancyFormsPreview {

    public function __construct() {
        add_action('wp_ajax_fancyforms_preview', array($this, 'preview'));
        add_action('wp_ajax_nopriv_fancyforms_preview', array($this, 'preview'));
    }

    public static function preview() {
        header('Content-Type: text/html; charset=' . get_option('blog_charset'));
        $id = htmlspecialchars_decode(FancyFormsHelper::get_var('form', 'absint'));
        $form = FancyFormsBuilder::get_form_vars($id);
        require( FANCYFORMS_PATH . 'admin/forms/preview/preview.php' );
        wp_die();
    }

    public static function show_form($id) {
        $form = FancyFormsBuilder::get_form_vars($id);
        if (!$form || $form->status === 'trash')
            return esc_html__('Please select a valid form', 'fancy-forms');

        self::get_form_contents($id);
    }

    public static function get_form_contents($id) {
        $form = FancyFormsBuilder::get_form_vars($id);
        $values = FancyFormsHelper::get_fields_array($id);

        $styles = $form->styles ? $form->styles : '';

        $form_class = array('fancyforms-form');
        $form_class[] = isset($form->options['form_css_class']) ? $form->options['form_css_class'] : '';
        $form_class[] = $styles && isset($styles['form_style']) ? 'fancyforms-form-' . esc_attr($styles['form_style']) : 'fancyforms-form-default-style';
        $form_class = apply_filters('fancyforms_form_classes', $form_class);
        ?>

        <div class="fancyforms-form-tempate">
            <form enctype="multipart/form-data" method="post" class="<?php echo esc_attr(implode(' ', array_filter($form_class))); ?>" id="fancyforms-form-id-<?php echo esc_attr($form->form_key); ?>" novalidate>
                <?php
                require FANCYFORMS_PATH . '/admin/forms/style/form.php';
                ?>
            </form>
        </div>
        <?php
    }

}

new FancyFormsPreview();

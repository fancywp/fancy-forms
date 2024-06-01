<?php
defined('ABSPATH') || die();

$form_id = FancyFormsHelper::get_var('id', 'absint');
?>
<div id="fancyforms-shortcode-form-modal">
    <div class="fancyforms-shortcode-modal-wrap">
        <form id="fancyforms-add-template" method="post">
            <h3><?php esc_attr_e('Use the shortcode below to add to your pages', 'fancy-forms'); ?></h3>

            <div class="fancyforms-form-row">
                <input type="text" value="<?php echo esc_attr('[fancyforms id="' . absint($form_id) . '"]') ?>" disabled/>
                <span id="fancyforms-copy-shortcode" class="mdi mdi-content-copy"></span>
            </div>

            <div class="fancyforms-copied"><?php esc_attr_e('Copied!', 'fancy-forms'); ?></div>

            <div class="fancyforms-shortcode-footer">
                <a href="#" class="fancyforms-close-form-modal"><?php esc_html_e('Close', 'fancy-forms'); ?></a>
            </div>
        </form>
    </div>
</div>
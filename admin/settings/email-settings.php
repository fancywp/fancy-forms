<?php
defined('ABSPATH') || die();

$image_id = '';
$image = '';
if (isset($settings['header_image'])) {
    $image_id = $settings['header_image'];
    $image = wp_get_attachment_image_src($settings['header_image'], 'full');
    $image = isset($image[0]) ? esc_attr($image[0]) : '';
}
?>
<div class="fancyforms-form-container fancyforms-grid-container">
    <div class="fancyforms-settings-row fancyforms-grid-container">
        <label class="fancyforms-setting-label"><?php esc_html_e('Header Image', 'fancy-forms'); ?></label>
        <div class="fancyforms-grid-3">
            <div class="fancyforms-image-preview">
                <input type="hidden" class="fancyforms-image-id" name="fancyforms_settings[header_image]" id="header_image" value="<?php echo esc_attr($image_id); ?>"/>

                <div class="fancyforms-image-preview-wrap<?php echo ($image ? '' : ' fancyforms-hidden'); ?>">
                    <div class="fancyforms-image-preview-box">
                        <img id="fancyforms-image-preview-header-image" src="<?php echo esc_attr($image); ?>" />
                    </div>
                    <button type="button" class="button fancyforms-remove-image">
                        <span class="mdi mdi-trash-can-outline"></span>
                        <?php esc_html_e('Delete', 'fancy-forms'); ?>
                    </button>
                </div>

                <button type="button" class="button fancyforms-choose-image<?php echo ($image ? ' fancyforms-hidden' : ''); ?>">
                    <span class="mdi mdi-tray-arrow-up"></span>
                    <?php esc_attr_e('Upload image', 'fancy-forms'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="fancyforms-settings-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label class="fancyforms-setting-label"><?php esc_html_e('Email Template', 'fancy-forms'); ?></label>
            <select id="fancyforms-settings-email-template" name="fancyforms_settings[email_template]">
                <option value="template1" <?php selected($settings['email_template'], 'template1'); ?>><?php esc_html_e('Template 1', 'fancy-forms'); ?></option>
                <option value="template2" <?php selected($settings['email_template'], 'template2'); ?>><?php esc_html_e('Template 2', 'fancy-forms'); ?></option>
                <option value="template3" <?php selected($settings['email_template'], 'template3'); ?>><?php esc_html_e('Template 3', 'fancy-forms'); ?></option>
            </select>
        </div>
    </div>

    <div class="fancyforms-settings-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label class="fancyforms-setting-label"><?php esc_html_e('Send Test Email to', 'fancy-forms'); ?></label>
            <div class="fancyforms-flex">
                <input type="email" id="fancyforms-test-email" />
                <a href="#" class="button button-secondary" id="fancyforms-test-email-button"><?php esc_attr_e('Send Test Email', 'fancy-forms'); ?></a>
            </div>
            <div class="fancyforms-test-email-notice"></div>
        </div>
    </div>
</div>
<?php
defined('ABSPATH') || die();
?>

<div class="fancyforms-form-container fancyforms-grid-container">
    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Confirmation Type', 'fancy-forms'); ?></label>
            <select name="confirmation_type" data-condition="toggle" id="fancyforms-form-conformation-type">
                <option value="show_message" <?php selected($settings['confirmation_type'], 'show_message'); ?>><?php esc_html_e('Message', 'fancy-forms'); ?></option>
                <option value="show_page" <?php selected($settings['confirmation_type'], 'show_page'); ?>><?php esc_html_e('Show Page', 'fancy-forms'); ?></option>
                <option value="redirect_url" <?php selected($settings['confirmation_type'], 'redirect_url'); ?>><?php esc_html_e('Redirect URL', 'fancy-forms'); ?></option>
            </select>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container" data-condition-toggle="fancyforms-form-conformation-type" data-condition-val="show_message">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Message', 'fancy-forms'); ?></label>
            <textarea name="confirmation_message"><?php echo esc_html($settings['confirmation_message']) ?></textarea>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container" data-condition-toggle="fancyforms-form-conformation-type" data-condition-val="show_page">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Show Page', 'fancy-forms'); ?></label>
            <select name="show_page_id">
                <?php foreach (get_pages() as $page) { ?>
                    <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($settings['show_page_id'], $page->ID); ?>><?php echo esc_html($page->post_title); ?></option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container" data-condition-toggle="fancyforms-form-conformation-type" data-condition-val="redirect_url">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Redirect URL', 'fancy-forms'); ?></label>
            <input type="text" name="redirect_url_page" value="<?php echo esc_attr($settings['redirect_url_page']) ?>" />
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Error Message', 'fancy-forms'); ?></label>
            <textarea name="error_message"><?php echo esc_textarea($settings['error_message']) ?></textarea>
        </div>
    </div>
</div>
<?php
defined('ABSPATH') || die();
?>

<div class="fancyforms-form-container fancyforms-grid-container">
    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Enable Auto Responder', 'fancy-forms'); ?></label>
            <div class="fancyforms-setting-fields fancyforms-toggle-input-field">
                <input type="hidden" name="enable_ar" value="off">
                <input type="checkbox" name="enable_ar" value="on" <?php checked($settings['enable_ar'], 'on', true); ?>>
            </div>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('From Email', 'fancy-forms'); ?></label>
            <input type="text" name="from_ar" value="<?php echo esc_attr($settings['from_ar']) ?>" />
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('From Name', 'fancy-forms'); ?></label>
            <input type="text" name="from_ar_name" value="<?php echo esc_attr($settings['from_ar_name']) ?>" />
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('Reply To Email', 'fancy-forms'); ?></label>
            <select name="reply_to_ar">
                <option value=""><?php esc_html_e('Choose a Form Field', 'fancy-forms'); ?></option>
                <?php
                foreach ($fields as $field) {
                    if ($field->type == 'email') {
                        ?>
                        <option value="<?php echo esc_attr($field->id); ?>" <?php selected($settings['reply_to_ar'], $field->id); ?>><?php echo esc_html($field->name); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div class="fancyforms-form-row">
        <label> <?php esc_html_e('Subject', 'fancy-forms'); ?></label>
        <input type="text" name="email_subject_ar" value="<?php echo esc_attr($settings['email_subject_ar']) ?>" />
    </div>

    <div class="fancyforms-form-row">
        <label><?php esc_html_e('Message', 'fancy-forms'); ?></label>
        <textarea name="email_message_ar" cols="50" rows="5"><?php echo ($settings['email_message_ar'] ? esc_textarea($settings['email_message_ar']) : ''); ?></textarea>
    </div>
</div>
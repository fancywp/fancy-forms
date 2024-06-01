<?php
defined('ABSPATH') || die();
?>
<div class="fancyforms-form-container fancyforms-grid-container">
    <div class="fancyforms-form-row fancyforms-multiple-rows fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('To Email', 'fancy-forms'); ?></label>
            <div class="fancyforms-multiple-email">
                <?php
                $email_to_array = explode(',', $settings['email_to']);
                foreach ($email_to_array as $row) {
                    ?>
                    <div class="fancyforms-email-row">
                        <input type="email" name="email_to[]" value="<?php echo esc_attr($row); ?>"/>
                        <span class="mdi mdi-trash-can-outline fancyforms-delete-email-row"></span>
                    </div>
                <?php } ?>
            </div>
            <button type="button" class="button button-primary fancyforms-add-email"><?php esc_html_e('Add More Email', 'fancy-forms'); ?></button>
            <p></p>
            <p class="description"><?php esc_html_e('Use [admin_email] for admin email. Settings > General > Administration Email Address', 'fancy-forms'); ?></p>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label class="fancyforms-label-with-attr">
                <?php esc_html_e('Reply To', 'fancy-forms'); ?>
                <div class="fancyforms-attr-field">
                    <div class="fancyforms-attr-field-tags">
                        <span class="mdi mdi-tag-multiple"></span>Tags
                    </div>
                    <ul class="fancyforms-add-field-attr-to-form">
                        <?php
                        foreach ($fields as $field) {
                            if ($field->type == 'email') {
                                ?>
                                <li data-value="#field_id_<?php echo esc_attr($field->id); ?>">
                                    <?php echo esc_html($field->name); ?><span>#field_id_<?php echo esc_html($field->id); ?></span>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
            </label>
            <input type="text" name="reply_to_email" value="<?php echo esc_attr($settings['reply_to_email']); ?>"/>
            <p class="description"><?php esc_html_e('Choose the email field by clicking on the TAGS above.', 'fancy-forms'); ?></p>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('From Email', 'fancy-forms'); ?></label>
            <input type="text" name="email_from" value="<?php echo esc_attr($settings['email_from']); ?>"/>
            <p class="description"><?php esc_html_e('Use [admin_email] for admin email. Settings > General > Administration Email Address', 'fancy-forms'); ?></p>
            <p class="description" style="color:red;"><?php esc_html_e('IMPORTANT: The email address should match with your domain name for proper delivery. eg. admin@yourwebsite.com', 'fancy-forms'); ?></p>
        </div>
    </div>

    <div class="fancyforms-form-row fancyforms-grid-container">
        <div class="fancyforms-grid-3">
            <label><?php esc_html_e('From Name', 'fancy-forms'); ?></label>
            <input type="text" name="email_from_name" value="<?php echo esc_attr($settings['email_from_name']); ?>"/>
        </div>
    </div>

    <div class="fancyforms-form-row">
        <label class="fancyforms-label-with-attr">
            <?php esc_html_e('Subject', 'fancy-forms'); ?>
            <div class="fancyforms-attr-field">
                <div class="fancyforms-attr-field-tags">
                    <span class="mdi mdi-tag-multiple"></span>Tags
                </div>
                <ul class="fancyforms-add-field-attr-to-form">
                    <?php
                    foreach ($fields as $field) {
                        if (!($field->type == 'heading' || $field->type == 'paragraph' || $field->type == 'separator' || $field->type == 'spacer' || $field->type == 'image' || $field->type == 'captcha')) {
                            ?>
                            <li data-value="#field_id_<?php echo esc_attr($field->id); ?>">
                                <?php echo esc_html($field->name); ?><span>#field_id_<?php echo esc_html($field->id); ?></span>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </label>

        <input type="text" name="email_subject" value="<?php echo esc_attr($settings['email_subject']); ?>"/>
    </div>

    <div class="fancyforms-form-row">
        <label class="fancyforms-label-with-attr">
            <?php esc_html_e('Message', 'fancy-forms'); ?>
            <div class="fancyforms-attr-field">
                <div class="fancyforms-attr-field-tags">
                    <span class="mdi mdi-tag-multiple"></span>Tags
                </div>
                <ul class="fancyforms-add-field-attr-to-form">
                    <?php
                    foreach ($fields as $field) {
                        if (!(in_array($field->type, array('heading', 'paragraph', 'separator', 'spacer', 'image', 'captcha', 'gdpr_agreement')))) {
                            ?>
                            <li data-value="#field_id_<?php echo esc_attr($field->id); ?>">
                                <?php echo esc_html($field->name); ?><span>#field_id_<?php echo esc_html($field->id); ?></span>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </label>
        <textarea name="email_message" rows="5"><?php echo esc_textarea($settings['email_message']); ?></textarea>
        <p class="description"><?php esc_html_e('Use #form_title for form title, #form_details for form inputs', 'fancy-forms'); ?></p>
    </div>
</div>
<?php
defined('ABSPATH') || die();
?>

<div id="fancyforms-add-form-modal">
    <div class="fancyforms-add-form-modal-wrap">
        <form id="fancyforms-add-template" method="post">
            <h3><?php esc_attr_e('Create New Form', 'fancy-forms'); ?></h3>

            <div class="fancyforms-form-row">
                <label for="fancyforms-form-name"><?php esc_html_e('Form Name', 'fancy-forms'); ?></label>
                <input type="text" name="template_name" id="fancyforms-form-name" />
            </div>

            <div class="fancyforms-add-form-footer">
                <a href="#" class="fancyforms-close-form-modal"><?php esc_html_e('Cancel', 'fancy-forms'); ?></a>
                <button type="submit"><?php esc_html_e('Create', 'fancy-forms'); ?></button>
            </div>
        </form>
    </div>
</div>
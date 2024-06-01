<?php
defined('ABSPATH') || die();
?>

<div class="fancyforms-form-container fancyforms-grid-container">
    <div class="fancyforms-form-row">
        <?php esc_html_e("You can export the settings and then import the form in the same or different website.", "fancy-forms"); ?>
    </div>

    <div class="fancyforms-form-row">
        <h3><?php esc_html_e('Export', 'fancy-forms'); ?></h3>
        <form method="post"></form>
        <form method="post">
            <input type="hidden" name="fancyforms_imex_action" value="export_form" />
            <input type="hidden" name="fancyforms_form_id" value="<?php echo esc_attr($id); ?>" />
            <?php wp_nonce_field("fancyforms_imex_export_nonce", "fancyforms_imex_export_nonce"); ?>
            <button class="button button-primary" id="fancyforms_export" name="fancyforms_export"><span class="mdi mdi-tray-arrow-down"></span> <?php esc_html_e("Export Form", "fancy-forms") ?></button>
        </form>
    </div>

    <div class="fancyforms-form-row"></div>

    <div class="fancyforms-form-row">
        <h3><?php esc_html_e('Import', 'fancy-forms'); ?></h3>
        <form method="post" enctype="multipart/form-data">
            <div class="fancyforms-preview-zone hidden">
                <div class="fancyforms-box fancyforms-box-solid">
                    <div class="fancyforms-box-body"></div>
                    <button type="button" class="button fancyforms-remove-preview">
                        <span class="mdi mdi-window-close"></span>
                    </button>
                </div>
            </div>
            <div class="fancyforms-dropzone-wrapper">
                <div class="fancyforms-dropzone-desc">
                    <span class="mdi mdi-file-image-plus-outline"></span>
                    <p><?php esc_html_e("Choose an json file or drag it here", "fancy-forms"); ?></p>
                </div>
                <input type="file" name="fancyforms_import_file" class="fancyforms-dropzone">
            </div>
            <button class="button button-primary" id="fancyforms_import" type="submit" name="fancyforms_import"><i class='icofont-download'></i> <?php esc_html_e("Import", "fancy-forms") ?></button>
            <input type="hidden" name="fancyforms_imex_action" value="import_form" />
            <input type="hidden" name="fancyforms_form_id" value="<?php echo esc_attr($id); ?>" />
            <?php wp_nonce_field("fancyforms_imex_import_nonce", "fancyforms_imex_import_nonce"); ?>
        </form>
    </div>
</div>
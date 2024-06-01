<?php
defined('ABSPATH') || die();

global $post;
$post_id = $post->ID;
$fancyforms_styles = get_post_meta($post_id, 'fancyforms_styles', true);

if (!$fancyforms_styles) {
    $fancyforms_styles = FancyFormsStyles::default_styles();
} else {
    $fancyforms_styles = FancyFormsHelper::recursive_parse_args($fancyforms_styles, FancyFormsStyles::default_styles());
}

wp_nonce_field('fancyforms-styles-nonce', 'fancyforms_styles_nonce');
?>

<div class="fancyforms-content">
    <div class="fancyforms-body">
        <div class="fancyforms-fields-sidebar fancyforms-style-sidebar">
            <div class="fancyforms-sticky-sidebar">
                <?php include FANCYFORMS_PATH . 'admin/styles/main.php'; ?>
            </div>
        </div>

        <div id="fancyforms-form-panel">
            <div class="fancyforms-form-wrap">
                <?php FancyFormsHelper::print_message(); ?>
                <?php include FANCYFORMS_PATH . 'admin/styles/demo-preview.php'; ?>
            </div>
        </div>
    </div>

    <?php
    $fancyforms_post_type = htmlspecialchars_decode(FancyFormsHelper::get_var('post_type'));
    $fancyforms_post_class = $fancyforms_post_type == 'fancyforms-styles' ? 'postbox' : 'submitbox';
    ?>
    <div class="fancyforms-footer">
        <div id="submitpost" class="<?php echo esc_attr($fancyforms_post_class); ?>">
            <div id="major-publishing-actions">
                <div id="publishing-action">
                    <span class="spinner"></span>
                    <?php if ($fancyforms_post_type == 'fancyforms-styles') { ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="Publish">
                        <input type="submit" name="publish" id="publish" class="button button-primary button-large" value="<?php esc_html_e('Publish', 'fancy-forms'); ?>">						
                    <?php } else { ?>
                        <input name="original_publish" type="hidden" id="original_publish" value="Update">
                        <input type="submit" name="save" id="publish" class="button button-primary button-large" value="<?php esc_html_e('Update', 'fancy-forms'); ?>">
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="fancyforms-preview-close">
            <a class="button button-secondary button-large" href="<?php echo esc_url(admin_url('/edit.php?post_type=fancyforms-styles')); ?>"><?php esc_html_e('Close', 'fancy-forms'); ?></a>
        </div>
    </div>
</div>
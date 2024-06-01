<?php
defined('ABSPATH') || die();

$id = htmlspecialchars_decode(FancyFormsHelper::get_var('id', 'absint'));
$form = FancyFormsBuilder::get_form_vars($id);

if (!$form) {
    echo '<h3>' . esc_html__('You are trying to edit a form that does not exist.', 'fancy-forms') . '</h3>';
    return;
}

$fields = FancyFormsFields::get_form_fields($id);
$styles = $form->styles ? $form->styles : array();
$form_style = isset($styles['form_style']) ? $styles['form_style'] : 'default-style';
$form_style_template = isset($styles['form_style_template']) ? $styles['form_style_template'] : '';
?>
<div id="fancyforms-wrap" class="fancyforms-content fancyforms-form-style-template">
    <?php
    self::get_admin_header(
            array(
                'form' => $form,
                'class' => 'fancyforms-header-nav',
            )
    );
    ?>
    <div class="fancyforms-body">
        <div class="fancyforms-fields-sidebar">
            <form class="ht-fields-panel" method="post" id="fancyforms-style-form">
                <input type="hidden" name="id" id="fancyforms-form-id" value="<?php echo absint($id); ?>" />
                <div class="fancyforms-form-container fancyforms-grid-container">
                    <div class="fancyforms-form-row">
                        <label><?php esc_html_e('Form Style', 'fancy-forms'); ?></label>
                        <select name="form_style" id="fancyforms-form-style-select"  data-condition="toggle">
                            <option value="no-style" <?php isset($form_style) ? selected('no-style', $form_style) : ''; ?>><?php esc_html_e('No Style', 'fancy-forms'); ?></option>
                            <option value="default-style" <?php isset($form_style) ? selected('default-style', $form_style) : ''; ?>><?php esc_html_e('Default Style', 'fancy-forms'); ?></option>
                            <option value="custom-style" <?php isset($form_style) ? selected('custom-style', $form_style) : ''; ?>><?php esc_html_e('Custom Style', 'fancy-forms'); ?></option>
                        </select>
                    </div>

                    <div class="fancyforms-form-row" data-condition-toggle="fancyforms-form-style-select" data-condition-val="no-style">
                        <?php esc_html_e('Choose "No Style" when you don\'t want to implement Fancy Forms plugin style and let theme style take over.', 'fancy-forms'); ?>
                        <br><br>
                        <?php esc_html_e('The preview seen here will not match with the frontend for "No Style".', 'fancy-forms'); ?>
                    </div>

                    <div class="fancyforms-form-row" data-condition-toggle="fancyforms-form-style-select" data-condition-val="default-style">
                        <?php esc_html_e('Choose "Default Style" when you want to implement Fancy Forms plugin styles with minimal designs.', 'fancy-forms'); ?>
                    </div>

                    <div class="fancyforms-form-row" data-condition-toggle="fancyforms-form-style-select" data-condition-val="custom-style">
                        <?php esc_html_e('Choose "Custom Style" when you want to implement your own styles', 'fancy-forms'); ?>
                        <br><br>
                        <?php printf(esc_html__('To create new Custom Style, go to %1sStyle Template%2s page.', 'fancy-forms'), '<a href="' . esc_url(admin_url('edit.php?post_type=fancyforms-styles')) . '" target="_blank">', '</a>'); ?>
                    </div>

                    <div class="fancyforms-form-row" data-condition-toggle="fancyforms-form-style-select" data-condition-val="custom-style">
                        <label><?php esc_html_e('Choose Template Style', 'fancy-forms'); ?></label>
                        <select name="form_style_template" id="fancyforms-form-style-template">
                            <option value=""><?php esc_html_e('--Select Style--', 'fancy-forms'); ?></option>
                            <?php
                            $args = array(
                                'post_type' => 'fancyforms-styles',
                                'posts_per_page' => -1,
                                'post_status' => 'publish'
                            );
                            $query = new WP_Query($args);
                            $posts = $query->posts;
                            foreach ($posts as $post) {
                                $fancyforms_styles = get_post_meta($post->ID, 'fancyforms_styles', true);

                                if (!$fancyforms_styles) {
                                    $fancyforms_styles = FancyFormsStyles::default_styles();
                                } else {
                                    $fancyforms_styles = FancyFormsHelper::recursive_parse_args($fancyforms_styles, FancyFormsStyles::default_styles());
                                }
                                ob_start();
                                echo '#fancyforms-container-' . absint($id) . '{';
                                FancyFormsStyles::get_style_vars($fancyforms_styles, '');
                                echo '}';
                                $tmpl_css_style = ob_get_clean();
                                ?>
                                <option value="<?php echo esc_attr($post->ID); ?>" data-style="<?php echo esc_attr($tmpl_css_style); ?>" <?php selected($post->ID, $form_style_template); ?>><?php echo esc_html($post->post_title); ?></option>
                                <?php
                            }
                            wp_reset_postdata();
                            ?>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div id="fancyforms-form-panel">
            <div class="fancyforms-form-wrap">
                <?php FancyFormsPreview::show_form($form->id); ?>
            </div>
        </div>
    </div>
</div>
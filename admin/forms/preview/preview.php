<?php
defined('ABSPATH') || die();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <title><?php bloginfo('name'); ?> | <?php echo esc_html($form->name); ?></title>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <?php wp_head(); ?>
    </head>
    <body class="fancyforms_preview_page">
        <?php
        FancyFormsPreview::show_form($form->id);
        wp_footer();
        ?>
    </body>
</html>

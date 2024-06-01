<?php

defined('ABSPATH') || die();

FancyFormsPreview::show_form($form_id);

echo '<style>';
echo '#fancyforms-container-00{';
self::get_style_vars($fancyforms_styles, '');
echo '}';
echo '</style>';

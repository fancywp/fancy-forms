<?php

defined('ABSPATH') || die();

class FancyFormsShortcode {

    public function __construct() {
        add_shortcode('fancyforms', array($this, 'get_form_shortcode'));
    }

    public static function get_form_shortcode($atts) {
        $shortcode_atts = shortcode_atts(array(
            'id' => '',
                ), $atts);
        ob_start();
        FancyFormsPreview::show_form($shortcode_atts['id']);
        return ob_get_clean();
    }

}

new FancyFormsShortcode();

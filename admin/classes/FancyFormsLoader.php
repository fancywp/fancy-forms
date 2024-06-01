<?php

defined('ABSPATH') || die();

class FancyFormsLoader {

    public function __construct() {
        add_action('init', array($this, 'load_plugin_textdomain'));
        add_filter('admin_body_class', array($this, 'add_admin_class'), 999);
        add_action('admin_enqueue_scripts', array($this, 'admin_init'), 11);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 11);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('elementor/editor/after_enqueue_styles', array($this, 'elementor_editor_styles'));
    }

    public function load_plugin_textdomain() {
        load_plugin_textdomain('fancy-forms', false, basename(dirname(__FILE__)) . '/languages');
    }

    public static function add_admin_class($classes) {
        if (FancyFormsHelper::is_form_builder_page()) {
            $full_screen_on = self::get_full_screen_setting();
            if ($full_screen_on) {
                $classes .= ' is-fullscreen-mode';
                wp_enqueue_style('wp-edit-post'); // Load the CSS for .is-fullscreen-mode.
            }
        }
        return $classes;
    }

    private static function get_full_screen_setting() {
        global $wpdb;
        $meta_key = $wpdb->get_blog_prefix() . 'persisted_preferences';
        $prefs = get_user_meta(get_current_user_id(), $meta_key, true);
        if ($prefs && isset($prefs['core/edit-post']['fullscreenMode']))
            return $prefs['core/edit-post']['fullscreenMode'];
        return true;
    }

    public static function admin_init() {
        $page = FancyFormsHelper::get_var('page', 'sanitize_title');
        if (strpos($page, 'fancyforms') === 0) {
            wp_enqueue_script('fancyforms-builder', FANCYFORMS_URL . 'js/builder.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'wp-i18n', 'wp-hooks', 'jquery-ui-dialog', 'fancyforms-select2'), FANCYFORMS_VERSION, true);
            wp_enqueue_script('fancyforms-backend', FANCYFORMS_URL . 'js/backend.js', array('jquery', 'jquery-ui-core', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'wp-i18n', 'wp-hooks', 'jquery-ui-dialog', 'jquery-ui-datepicker'), FANCYFORMS_VERSION, true);

            wp_localize_script('fancyforms-backend', 'fancyforms_backend_js', array(
                'nonce' => wp_create_nonce('fancyforms_ajax'),
            ));
        }

        if (strpos($page, 'fancyforms-smtp') === 0) {
            wp_enqueue_script('plugin-install');
            wp_enqueue_script('updates');
        }

        wp_enqueue_script('fancyforms-chosen', FANCYFORMS_URL . '/js/chosen.jquery.js', array('jquery'), FANCYFORMS_VERSION, true);
        wp_enqueue_script('fancyforms-select2', FANCYFORMS_URL . '/js/select2.min.js', array('jquery'), FANCYFORMS_VERSION, true);
        wp_enqueue_script('jquery-condition', FANCYFORMS_URL . '/js/jquery-condition.js', array('jquery'), FANCYFORMS_VERSION, true);
        wp_enqueue_script('wp-color-picker-alpha', FANCYFORMS_URL . '/js/wp-color-picker-alpha.js', array('wp-color-picker'), FANCYFORMS_VERSION, true);
        wp_enqueue_script('fancyforms-admin-settings', FANCYFORMS_URL . '/js/admin-settings.js', array('jquery'), FANCYFORMS_VERSION, true);

        wp_localize_script('fancyforms-admin-settings', 'fancyforms_admin_js_obj', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('fancyforms-ajax-nonce'),
            'installing_text' => esc_html__('Installing WP Mail SMTP', 'fancy-forms'),
            'activating_text' => esc_html__('Activating WP Mail SMTP', 'fancy-forms'),
            'error' => esc_html__('Error! Reload the page and try again.', 'fancy-forms'),
        ));

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_style('fancyforms-icons', FANCYFORMS_URL . 'fonts/fancyforms-icons.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('materialdesignicons', FANCYFORMS_URL . 'fonts/materialdesignicons.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-chosen', FANCYFORMS_URL . '/css/chosen.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-select2', FANCYFORMS_URL . '/css/select2.min.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-admin', FANCYFORMS_URL . 'css/admin-style.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-file-uploader', FANCYFORMS_URL . 'css/file-uploader.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-admin-settings', FANCYFORMS_URL . '/css/admin-settings.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-style', FANCYFORMS_URL . '/css/style.css', array(), FANCYFORMS_VERSION);

        $fonts_url = FancyFormsStyles::fonts_url();

        // Load Fonts if necessary.
        if ($fonts_url) {
            wp_enqueue_style('fancyforms-fonts', $fonts_url, array(), false);
        }
    }
    
    public static function elementor_editor_styles() {
        wp_enqueue_style('fancyforms-icons', FANCYFORMS_URL . 'fonts/fancyforms-icons.css', array(), FANCYFORMS_VERSION);
    }

    public static function enqueue_styles() {
        wp_enqueue_style('dashicons');
        wp_enqueue_style('jquery-timepicker', FANCYFORMS_URL . 'css/jquery.timepicker.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-file-uploader', FANCYFORMS_URL . 'css/file-uploader.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('materialdesignicons', FANCYFORMS_URL . 'fonts/materialdesignicons.css', array(), FANCYFORMS_VERSION);
        wp_enqueue_style('fancyforms-style', FANCYFORMS_URL . 'css/style.css', array(), FANCYFORMS_VERSION);
        $fonts_url = FancyFormsStyles::fonts_url();

        if ($fonts_url) {
            wp_enqueue_style('fancyforms-fonts', $fonts_url, array(), false);
        }
    }

    public static function enqueue_scripts() {
        wp_enqueue_script('jquery-ui-slider');
        wp_enqueue_script('jquery-timepicker', FANCYFORMS_URL . 'js/jquery.timepicker.min.js', array('jquery'), FANCYFORMS_VERSION, true);
        wp_enqueue_script('fancyforms-file-uploader', FANCYFORMS_URL . 'js/file-uploader.js', array(), FANCYFORMS_VERSION, true);
        wp_localize_script('fancyforms-file-uploader', 'fancyforms_file_vars', array(
            'remove_txt' => esc_html('Remove', 'fancy-forms')
        ));
        wp_enqueue_script('moment', FANCYFORMS_URL . 'js/moment.js', array(), FANCYFORMS_VERSION, true);
        wp_enqueue_script('frontend', FANCYFORMS_URL . 'js/frontend.js', array('jquery', 'jquery-ui-datepicker', 'jquery-timepicker', 'fancyforms-file-uploader', 'fancyforms-file-uploader'), FANCYFORMS_VERSION, true);
        wp_localize_script('frontend', 'fancyforms_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajax_nounce' => wp_create_nonce('fancyforms-upload-ajax-nonce'),
            'preview_img' => '',
        ));
    }

}

new FancyFormsLoader();

<?php
defined('ABSPATH') || die();

class FancyFormsSettings {

    public function __construct() {
        add_action('admin_menu', array($this, 'menu'), 45);

        add_action('wp_ajax_fancyforms_test_email_template', array($this, 'send_test_email'), 10, 0);
    }

    public function menu() {
        add_submenu_page('fancyforms', 'Fancy Forms | ' . esc_html__('Settings', 'fancy-forms'), esc_html__('Settings', 'fancy-forms'), 'manage_options', 'fancyforms-settings', array($this, 'route'));
        add_submenu_page('fancyforms', esc_html__('Documentation', 'ultimate-woocommerce-cart'), esc_html__('Documentation', 'ultimate-woocommerce-cart'), 'manage_options', esc_url_raw('https://fancywp.com/docs/fancy-forms/'));
    }

    public function route() {
        $action = FancyFormsHelper::get_post('fancyforms_action', 'sanitize_title');
        if ($action == 'process-form') {
            self::process_form();
        } else {
            self::display_form();
        }
    }

    public static function display_form() {
        $settings = self::get_settings();
        $sections = array(
            'captcha-settings' => array(
                'name' => esc_html__('Captcha', 'fancy-forms'),
                'icon' => 'mdi mdi-security',
            ),
            'email-settings' => array(
                'name' => esc_html__('Email Settings', 'fancy-forms'),
                'icon' => 'mdi mdi-email-multiple-outline'
            ),
        );
        $current = 'captcha-settings'
        ?>
        <div class="fancyforms-settings-wrap wrap">
            <h1></h1>
            <div id="fancyforms-settings-wrap">
                <form name="fancyforms_settings_form" method="post" action="?page=fancyforms-settings<?php echo esc_html($current ? '&amp;t=' . $current : '' ); ?>">
                    <div class="fancyforms-page-title">
                        <h3><?php esc_html_e('Settings', 'fancy-forms'); ?></h3>
                    </div>
                    <div class="fancyforms-content"> 
                        <div class="fancyforms-body">
                            <div class="fancyforms-fields-sidebar">
                                <ul class="fancyforms-settings-tab">
                                    <?php foreach ($sections as $key => $section) { ?>
                                        <li class="<?php echo esc_attr($current === $key ? 'fancyforms-active' : '' ); ?>">
                                            <a href="#fancyforms-<?php echo esc_attr($key); ?>">
                                                <i class="<?php echo esc_attr($section['icon']); ?>"></i>
                                                <?php echo wp_kses_post($section['name']); ?>
                                            </a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <div id="fancyforms-form-panel">
                                <div class="fancyforms-form-wrap">
                                    <?php FancyFormsHelper::print_message(); ?>

                                    <input type="hidden" name="fancyforms_action" value="process-form"/>
                                    <?php
                                    wp_nonce_field('fancyforms_process_form_nonce', 'process_form');
                                    foreach ($sections as $key => $section) {
                                        ?>
                                        <div id="fancyforms-<?php echo esc_attr($key); ?>" class="<?php echo ( $current === $key ) ? '' : 'fancyforms-hidden'; ?>">
                                            <h3><?php echo esc_html($section['name']); ?></h3>
                                            <?php
                                            include( FANCYFORMS_PATH . 'admin/settings/' . $key . '.php' );
                                            ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <div class="fancyforms-footer">
                            <input class="button button-primary button-large" type="submit" value="<?php esc_attr_e('Update', 'fancy-forms'); ?>"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }

    public static function process_form() {
        $process_form = FancyFormsHelper::get_post('process_form');
        if (!wp_verify_nonce($process_form, 'fancyforms_process_form_nonce')) {
            wp_die(esc_html__('Permission Denied', 'fancy-forms'));
        }

        $settings = FancyFormsHelper::recursive_parse_args(FancyFormsHelper::get_post('fancyforms_settings', 'esc_html'), self::checkbox_settings());
        $settings = FancyFormsHelper::sanitize_array($settings, self::sanitize_rules());

        update_option('fancyforms_options', $settings);
        $_SESSION['fancyforms_message'] = esc_html__('Settings Saved !', 'fancy-forms');

        self::display_form();
    }

    public static function get_settings() {
        $settings = get_option('fancyforms_options');
        if (!$settings) {
            $settings = self::default_values();
        } else {
            $settings = wp_parse_args($settings, self::default_values());
        }

        return $settings;
    }

    public function send_test_email() {
        if (!current_user_can('manage_options'))
            return;

        $settings = self::get_settings();

        $header_image = $settings['header_image'];

        $email_template = FancyFormsHelper::get_post('email_template');
        $test_email = FancyFormsHelper::get_post('test_email');
        $email_subject = esc_html__('Test Email', 'fancy-forms');
        $count = 0;

        $contents = array(
            0 => array(
                'title' => 'Name',
                'value' => 'John Doe'
            ),
            1 => array(
                'title' => 'Email',
                'value' => 'noreply@gmail.com'
            ),
            2 => array(
                'title' => 'Subject',
                'value' => 'Exciting Updates and Important Information Inside!'
            ),
            3 => array(
                'title' => 'Message',
                'value' => '<p>I hope this email finds you well. We are thrilled to share some exciting updates and important information that we believe you will find valuable.</p><p>Your satisfaction is our priority, and we are committed to delivering the best possible experience.</p>'
            )
        );

        $email_message = '<p style="margin-bottom:20px">';
        $email_message .= esc_html__('Hello, this is a test email.', 'fancy-forms');
        $email_message .= '</p>';
        foreach ($contents as $content) {
            $count++;
            $email_message .= call_user_func('FancyFormsEmail::' . $email_template, $content['title'], $content['value'], $count);
        }
        ob_start();
        include(FANCYFORMS_PATH . 'admin/settings/email-templates/' . $email_template . '.php');
        $form_html = ob_get_clean();

        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');
        $headers = array();
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . esc_attr($site_name) . ' <' . esc_attr($admin_email) . '>';
        $mail = wp_mail($test_email, $email_subject, $form_html, $headers);
        if ($mail) {
            die(wp_json_encode(
                            array(
                                'success' => true,
                                'message' => esc_html__('Email Sent Successfully', 'fancy-forms')
                            )
            ));
        }
        die(wp_json_encode(
                        array(
                            'success' => false,
                            'message' => esc_html__('Failed to Send Email', 'fancy-forms')
                        )
        ));
    }

    public static function checkbox_settings() {
        return array();
    }

    public static function default_values() {
        return array(
            're_type' => 'v2',
            'pubkey_v2' => '',
            'privkey_v2' => '',
            'pubkey_v3' => '',
            'privkey_v3' => '',
            're_lang' => 'en',
            're_threshold' => '0.5',
            'header_image' => '',
            'email_template' => 'template1',
        );
    }

    public static function sanitize_rules() {
        return array(
            're_type' => 'sanitize_text_field',
            'pubkey_v2' => 'sanitize_text_field',
            'privkey_v2' => 'sanitize_text_field',
            'pubkey_v3' => 'sanitize_text_field',
            'privkey_v3' => 'sanitize_text_field',
            're_lang' => 'sanitize_text_field',
            're_threshold' => 'sanitize_text_field',
            'header_image' => 'sanitize_text_field',
            'email_template' => 'sanitize_text_field',
        );
    }

}

new FancyFormsSettings();

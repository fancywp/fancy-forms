<?php
defined('ABSPATH') || die();

class FancyFormsBuilder {

    public function __construct() {

        $this->includes();

        add_action('init', array($this, 'session_init'));

        add_action('admin_menu', array($this, 'add_menu'), 1);
        add_filter('set-screen-option', array($this, 'set_screen_option'), 10, 3);

        add_action('wp_ajax_fancyforms_update_form', array($this, 'update_form'));
        add_action('wp_ajax_fancyforms_create_form', array($this, 'create_form'));
        add_action('wp_ajax_fancyforms_save_form_settings', array($this, 'save_form_settings'));
        add_action('wp_ajax_fancyforms_save_form_style', array($this, 'save_form_style'));
        add_action('wp_ajax_fancyforms_add_more_condition_block', array($this, 'add_more_condition_block'));
        add_action('admin_footer', array($this, 'init_overlay_html'));

        add_filter('plugin_action_links_' . plugin_basename(FANCYFORMS_FILE), array($this, 'add_plugin_action_link'), 10, 1);

        add_action('wp_ajax_fancyforms_file_upload_action', array($this, 'file_upload_action'));
        add_action('wp_ajax_nopriv_fancyforms_file_upload_action', array($this, 'file_upload_action'));

        add_action('wp_ajax_fancyforms_file_delete_action', array($this, 'file_delete_action'));
        add_action('wp_ajax_nopriv_fancyforms_file_delete_action', array($this, 'file_delete_action'));

        add_action('wp_loaded', array($this, 'admin_notice'), 20);
    }

    public function includes() {
        include FANCYFORMS_PATH . 'admin/forms/sanitization.php';
    }

    public function session_init() {
        if (!session_id() && !headers_sent()) {
            session_start(); //starts session if already not started
            session_write_close();
        }
    }

    public function add_menu() {
        global $fancyforms_listing_page;
        add_menu_page(esc_html__('Fancy Forms', 'fancy-forms'), esc_html__('Fancy Forms', 'fancy-forms'), 'manage_options', 'fancyforms', array($this, 'route'), 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMTcuNjYgMTUyLjI3IiBmaWxsPSIjYTdhYWFkIj48Zz48Zz48cGF0aCBkPSJNMCwzLjQ2QTMuNDYsMy40NiwwLDAsMSwzLjE0LDBoODBBMy41MywzLjUzLDAsMCwxLDg1LjYsMWwzMSwzMWEzLjQ3LDMuNDcsMCwwLDEsMSwyLjQzVjE0OC44MWEzLjQ2LDMuNDYsMCwwLDEtMy40NiwzLjQ2SDMxLjYzYTMuNDYsMy40NiwwLDEsMSwwLTYuOTJoNzkuMTFWMzguMDdIODMuMDVhMy40NiwzLjQ2LDAsMCwxLTMuNDYtMy40NlY2LjkySDYuOTJWMTQ1LjM1SDE0YTMuNDYsMy40NiwwLDEsMSwwLDYuOTJIMy40NkEzLjQ2LDMuNDYsMCwwLDEsMCwxNDguODFaTTEwNiwzMS4xNSw4Ni41MSwxMS42OFYzMS4xNVoiLz48cGF0aCBkPSJNNzguNjYsNTkuM0g5NS4wOXY2LjYxSDc4LjY2Vjg1Ljc1SDcyLjA1VjQyLjg3aDYuNjFabTAsMzkuNjd2MTYuNDJINzIuMDVWOTlINTIuMjJWOTIuMzZIOTUuMDlWOTlaTTM5LDk5SDIyLjU3VjkyLjM2SDM5VjcyLjUyaDYuNjFWMTE1LjRIMzlaTTM5LDU5LjNWNDIuODdoNi42MVY1OS4zSDY1LjQ0djYuNjFIMjIuNTdWNTkuM1oiLz48L2c+PC9nPjwvc3ZnPg==', 29);
        $fancyforms_listing_page = add_submenu_page('fancyforms', esc_html__('Forms', 'fancy-forms'), esc_html__('Forms', 'fancy-forms'), 'manage_options', 'fancyforms', array($this, 'route'));
        add_action("load-$fancyforms_listing_page", array($this, 'listing_page_screen_options'));
    }

    public function route() {
        /* Gets fancyforms_action value else action value */
        $action = htmlspecialchars_decode(FancyFormsHelper::get_var('fancyforms_action', 'sanitize_text_field', FancyFormsHelper::get_var('action')));

        if (FancyFormsHelper::get_var('delete_all')) {
            $action = 'delete_all';
        }

        switch ($action) {
            case 'edit':
            case 'trash':
            case 'destroy':
            case 'untrash':
            case 'delete_all':
            case 'duplicate':
            case 'settings':
            case 'style':
                return self::$action();

            default:

                if (strpos($action, 'bulk_') === 0) {
                    self::bulk_actions();
                    return;
                }

                self::display_forms_list();
                return;
        }
    }

    public static function display_message($message, $class) {
        if ('' !== trim($message)) {
            echo '<div id="message" class="' . esc_attr($class) . ' notice is-dismissible">';
            echo '<p>' . wp_kses_post($message) . '</p>';
            echo '</div>';
        }
    }

    public static function display_forms_list($message = '', $class = 'updated') {
        ?>
        <div class="fancyforms-content">
            <div class="fancyforms-form-list-wrap wrap">
                <h1></h1>
                <div class="fancyforms-add-new-form">
                    <a href="#" class="button fancyforms-trigger-modal"><?php esc_html_e('Add New', 'fancy-forms'); ?></a>
                </div>

                <?php
                self::display_message($message, $class);
                $form_table = new FancyFormsListing();
                $form_status = FancyFormsHelper::get_var('status', 'sanitize_title', 'published');
                $form_table->views();
                ?>
                <form id="posts-filter" method="get">
                    <input type="hidden" name="page" value="<?php echo esc_attr(FancyFormsHelper::get_var('page', 'sanitize_title')); ?>" />
                    <input type="hidden" name="status" value="<?php echo esc_attr($form_status); ?>" />
                    <?php
                    $form_table->prepare_items();
                    $form_table->search_box('Search', 'search');
                    $form_table->display();
                    ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function create_form() {
        if (!current_user_can('manage_options')) {
            return;
        }

        check_ajax_referer('fancyforms_ajax', 'nonce');

        $name = FancyFormsHelper::get_post('name');
        $new_values = array(
            'name' => esc_html($name),
            'description' => '',
            'form_key' => sanitize_text_field($name),
            'options' => array(
                'submit_value' => esc_html__('Submit', 'fancy-forms'),
                'show_description' => 'on',
                'show_title' => 'on',
            ),
            'settings' => FancyFormsHelper::get_form_settings_default($name)
        );
        $form_id = self::create($new_values);
        $response = array('redirect' => admin_url('admin.php?page=fancyforms&fancyforms_action=edit&id=' . absint($form_id)));
        echo wp_json_encode($response);
        wp_die();
    }

    public static function create($values) {
        global $wpdb;
        $options = isset($values['options']) && is_array($values['options']) ? $values['options'] : array();
        $options = FancyFormsHelper::recursive_parse_args($options, FancyFormsHelper::get_form_options_default());
        $options = FancyFormsHelper::sanitize_array($options, FancyFormsHelper::get_form_options_sanitize_rules());

        $settings = isset($values['settings']) && is_array($values['settings']) ? $values['settings'] : array();
        $settings = FancyFormsHelper::recursive_parse_args($settings, FancyFormsHelper::get_form_settings_default());
        $settings = FancyFormsHelper::sanitize_array($settings, FancyFormsHelper::get_form_settings_sanitize_rules());

        $styles = isset($values['styles']) && is_array($values['styles']) ? $values['styles'] : array();
        $styles = FancyFormsHelper::recursive_parse_args($styles, array('form_style' => 'default-style', 'form_style_template' => ''));
        $styles = FancyFormsHelper::sanitize_array($styles, FancyFormsHelper::get_form_styles_sanitize_rules());

        $new_values = array(
            'form_key' => FancyFormsHelper::get_unique_key('fancyforms_forms', 'form_key'),
            'name' => esc_html($values['name']),
            'description' => esc_html($values['description']),
            'status' => isset($values['status']) ? sanitize_text_field($values['status']) : 'published',
            'created_at' => isset($values['created_at']) ? sanitize_text_field($values['created_at']) : current_time('mysql'),
            'options' => serialize($options),
            'settings' => serialize($settings),
            'styles' => serialize($styles),
        );
        $wpdb->insert($wpdb->prefix . 'fancyforms_forms', $new_values);
        $id = $wpdb->insert_id;
        return $id;
    }

    public function update_form() {
        if (!current_user_can('manage_options')) {
            return;
        }

        check_ajax_referer('fancyforms_ajax', 'nonce');

        $fields_array = $settings_array = array();

        $fields = htmlspecialchars_decode(nl2br(str_replace('&quot;', '"', FancyFormsHelper::get_post('fancyforms_fields', 'wp_kses_post'))));
        if ($fields) {
            $fields_array = FancyFormsHelper::parse_json_array($fields);
        }

        $settings = htmlspecialchars_decode(nl2br(str_replace('&quot;', '"', FancyFormsHelper::get_post('fancyforms_settings'))));
        if ($settings) {
            $settings_array = FancyFormsHelper::parse_json_array($settings);
        }

        self::update($fields_array, $settings_array);
    }

    public static function update($fields_values, $settings_values) {
        $id = isset($fields_values['id']) ? absint($fields_values['id']) : '';

        self::update_form_options($id, $settings_values);
        FancyFormsFields::update_form_fields($id, $fields_values);

        $message = '<span class="mdi mdi-check-circle"></span>' . esc_html__('Form was successfully updated.', 'fancy-forms');

        if (defined('DOING_AJAX')) {
            wp_die(wp_kses($message, array('a' => array(), 'span' => array())));
        }
    }

    public static function update_form_options($id, $args) {
        global $wpdb;
        $options = FancyFormsHelper::recursive_parse_args($args, FancyFormsHelper::get_form_options_checkbox_settings());
        $options = FancyFormsHelper::sanitize_array($options, FancyFormsHelper::get_form_options_sanitize_rules());

        $query_results = $wpdb->update($wpdb->prefix . 'fancyforms_forms', array(
            'name' => esc_html($args['title']),
            'description' => esc_html($args['description']),
            'options' => maybe_serialize($options)
                ), array('id' => $id));
        return $query_results;
    }

    public static function edit() {
        require( FANCYFORMS_PATH . '/admin/forms/build/edit.php' );
    }

    public static function settings() {
        require FANCYFORMS_PATH . 'admin/forms/settings/settings.php';
    }

    public static function style() {
        require FANCYFORMS_PATH . '/admin/forms/style/style.php';
    }

    public function listing_page_screen_options() {

        global $fancyforms_listing_page;

        $screen = get_current_screen();

        // get out of here if we are not on our settings page
        if (!is_object($screen) || $screen->id != $fancyforms_listing_page) {
            return;
        }

        $args = array(
            'label' => esc_html__('Forms per page', 'fancy-forms'),
            'default' => 10,
            'option' => 'forms_per_page'
        );
        add_screen_option('per_page', $args);

        new FancyFormsListing();
    }

    public function set_screen_option($status, $option, $value) {
        if ('forms_per_page' == $option)
            return $value;
    }

    public static function trash() {
        self::change_form_status('trash');
    }

    public static function untrash() {
        self::change_form_status('untrash');
    }

    public static function change_form_status($status) {
        $available_status = array(
            'untrash' => array('new_status' => 'published'),
            'trash' => array('new_status' => 'trash'),
        );

        if (!isset($available_status[$status])) {
            return;
        }

        $id = FancyFormsHelper::get_var('id', 'absint');
        check_admin_referer($status . '_form_' . $id);

        $count = 0;
        if (self::set_status($id, $available_status[$status]['new_status'])) {
            $count++;
        }

        $available_status['untrash']['message'] = sprintf(_n('%1$s form restored from the Trash.', '%1$s forms restored from the Trash.', $count, 'fancy-forms'), $count);
        $available_status['trash']['message'] = sprintf(_n('%1$s form moved to the Trash. %2$sUndo%3$s', '%1$s forms moved to the Trash. %2$sUndo%3$s', $count, 'fancy-forms'), $count, '<a href="' . esc_url(wp_nonce_url('?page=fancyforms&fancyforms_action=untrash&id=' . absint($id), 'untrash_form_' . absint($id))) . '">', '</a>');
        $message = $available_status[$status]['message'];

        self::display_forms_list($message);
    }

    public static function set_status($id, $status) {
        $statuses = array('published', 'trash');
        if (!in_array($status, $statuses)) {
            return false;
        }

        global $wpdb;

        if (is_array($id)) {
            $query = $wpdb->prepare("UPDATE {$wpdb->prefix}fancyforms_forms SET status=%s WHERE id IN (" . implode(',', array_fill(0, count($id), '%d')) . ")", $status, ...$id);
            $query_results = $wpdb->query($query);
        } else {
            $query_results = $wpdb->update($wpdb->prefix . 'fancyforms_forms', array('status' => $status), array('id' => $id));
        }

        return $query_results;
    }

    public static function delete_all() {
        $count = self::delete();
        $message = sprintf(_n('%1$s form permanently deleted.', '%1$s forms permanently deleted.', $count, 'fancy-forms'), $count);
        self::display_forms_list($message);
    }

    public static function delete() {
        global $wpdb;
        $count = 0;
        $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}fancyforms_forms WHERE status=%s", 'trash');
        $trash_forms = $wpdb->get_results($query);
        if (!$trash_forms) {
            return 0;
        }

        foreach ($trash_forms as $form) {
            self::destroy_form($form->id);
            $count ++;
        }
        return $count;
    }

    public static function destroy() {
        $id = FancyFormsHelper::get_var('id', 'absint');
        check_admin_referer('destroy_form_' . $id);
        $count = 0;
        if (self::destroy_form($id)) {
            $count ++;
        }
        $message = sprintf(_n('%1$s Form Permanently Deleted', '%1$s Forms Permanently Deleted', $count, 'fancy-forms'), $count);
        self::display_forms_list($message);
    }

    public static function bulk_actions() {
        $message = self::process_bulk_actions();
        self::display_forms_list($message);
    }

    public static function process_bulk_actions() {
        if (!$_REQUEST) {
            return;
        }

        $bulkaction = FancyFormsHelper::get_var('action', 'sanitize_text_field');


        if ($bulkaction == - 1) {
            $bulkaction = FancyFormsHelper::get_var('action2', 'sanitize_title');
        }

        if (!empty($bulkaction) && strpos($bulkaction, 'bulk_') === 0) {
            $bulkaction = str_replace('bulk_', '', $bulkaction);
        }

        $ids = FancyFormsHelper::get_var('form_id', 'sanitize_text_field');

        if (empty($ids)) {
            $error = esc_html__('No forms were specified', 'fancy-forms');
            return $error;
        }

        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        switch ($bulkaction) {
            case 'delete':
                $message = self::bulk_destroy($ids);
                break;
            case 'trash':
                $message = self::bulk_trash($ids);
                break;
            case 'untrash':
                $message = self::bulk_untrash($ids);
        }

        if (isset($message) && !empty($message)) {
            return $message;
        }
    }

    public static function bulk_trash($ids) {
        $count = self::set_status($ids, 'trash');
        if (!$count) {
            return '';
        }

        return sprintf(_n('%1$s form moved to the Trash. %2$sUndo%3$s', '%1$s forms moved to the Trash. %2$sUndo%3$s', $count, 'fancy-forms'), $count, '<a href="' . esc_url(wp_nonce_url('?page=fancyforms&action=bulk_untrash&status=published&form_id=' . implode(',', $ids), 'bulk-toplevel_page_fancyforms')) . '">', '</a>');
    }

    public static function bulk_untrash($ids) {
        $count = self::set_status($ids, 'published');
        if (!$count) {
            return '';
        }

        return sprintf(_n('%1$s form restored from the Trash.', '%1$s forms restored from the Trash.', $count, 'fancy-forms'), $count);
    }

    public static function bulk_destroy($ids) {
        $count = 0;
        foreach ($ids as $id) {
            $form = self::destroy_form($id);
            if ($form) {
                $count ++;
            }
        }

        $message = sprintf(_n('%1$s form permanently deleted.', '%1$s forms permanently deleted.', $count, 'fancy-forms'), $count);
        return $message;
    }

    public static function destroy_form($id) {
        global $wpdb;
        $form = self::get_form_vars($id);
        if (!$form) {
            return false;
        }

        $id = $form->id;
        $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}fancyforms_entries WHERE form_id=%d", $id);
        $entries = $wpdb->get_col($query);

        foreach ($entries as $entry_id) {
            FancyFormsEntry::destroy_entry($entry_id);
        }

        $query = $wpdb->prepare('DELETE fancyformsicon FROM ' . $wpdb->prefix . 'fancyforms_fields AS fancyformsicon LEFT JOIN ' . $wpdb->prefix . 'fancyforms_forms fancyforms ON (fancyformsicon.form_id = fancyforms.id) WHERE fancyformsicon.form_id=%d', $id);
        $wpdb->query($query);

        $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'fancyforms_forms WHERE id=%d', $id);
        $results = $wpdb->query($query);
        return $results;
    }

    public static function duplicate() {
        global $wpdb;
        $message = '';
        $nonce = FancyFormsHelper::get_var('_wpnonce');

        if (!wp_verify_nonce($nonce)) {
            wp_die(esc_html__('Error ! Refresh the page and try again.', 'fancy-forms'));
        }

        $id = FancyFormsHelper::get_var('id', 'absint');
        $values = self::get_form_vars($id);

        if (!$values) {
            return false;
        }

        $options = FancyFormsHelper::recursive_parse_args($values->options, FancyFormsHelper::get_form_options_default());
        $options = FancyFormsHelper::sanitize_array($options, FancyFormsHelper::get_form_options_sanitize_rules());

        $settings = FancyFormsHelper::recursive_parse_args($values->settings, FancyFormsHelper::get_form_settings_default());
        $settings = FancyFormsHelper::sanitize_array($settings, FancyFormsHelper::get_form_settings_sanitize_rules());

        $styles = FancyFormsHelper::recursive_parse_args($values->styles, array('form_style' => 'default-style', 'form_style_template' => ''));
        $styles = FancyFormsHelper::sanitize_array($styles, FancyFormsHelper::get_form_styles_sanitize_rules());

        $new_values = array(
            'form_key' => FancyFormsHelper::get_unique_key('fancyforms_forms', 'form_key'),
            'name' => esc_html($values->name) . ' - ' . esc_html__('Copy', 'fancy-forms'),
            'description' => esc_html($values->description),
            'status' => $values->status ? sanitize_text_field($values->status) : 'published',
            'created_at' => sanitize_text_field(current_time('mysql')),
            'options' => serialize($options),
            'settings' => serialize($settings),
            'styles' => serialize($styles),
        );

        $query_results = $wpdb->insert($wpdb->prefix . 'fancyforms_forms', $new_values);

        if ($query_results) {
            $form_id = $wpdb->insert_id;
            FancyFormsFields::duplicate_fields($id, $form_id);
        }

        if ($form_id) {
            $message = esc_html__('Form was Successfully Copied', 'fancy-forms');
            $class = 'updated';
        } else {
            $message = esc_html__('Error! Form Can not be Copied', 'fancy-forms');
            $class = 'error';
        }

        self::display_forms_list($message, $class);
    }

    public static function get_admin_header($atts = array()) {
        $class = isset($atts['class']) ? $atts['class'] : '';
        $form = $atts['form'];
        $form_title = $form->name;
        ?>
        <div id="fancyforms-header" class="<?php echo esc_attr($class); ?>">
            <h4><span class="fancyformsicon fancyformsicon-form"></span><?php echo esc_html($form_title); ?></h4>
            <?php self::get_form_nav($form); ?>

            <button class="fancyforms-ajax-udpate-button" type="button" id="fancyforms-update-button" >
                <span class="mdi mdi-check-circle-outline"></span><?php esc_html_e('Update', 'fancy-forms'); ?>
            </button>

            <button class="fancyforms-embed-button" type="button">
                <span class="mdi mdi-code-brackets"></span><?php esc_html_e('Embed', 'fancy-forms'); ?>
            </button>

            <div class="fancyforms-preview-button">
                <a href="<?php echo esc_url(admin_url('admin-ajax.php?action=fancyforms_preview&form=' . absint($form->id))); ?>" target="_blank"><span class="mdi mdi-eye-outline"></span><?php esc_html_e('Preview', 'fancy-forms'); ?></a>
            </div>

            <div class="fancyforms-close">
                <a href="<?php echo esc_url(admin_url('admin.php?page=fancyforms')); ?>" aria-label="<?php esc_attr_e('Close', 'fancy-forms'); ?>">
                    <span class="mdi mdi-window-close"></span>
                </a>
            </div>
        </div>
        <?php
    }

    public static function get_form_nav($form) {
        if (!$form) {
            return;
        }
        $id = $form->id;
        $nav_items = self::get_form_nav_items($id);
        ?>
        <ul class="fancyforms-main-nav">
            <?php foreach ($nav_items as $nav_item) { ?>
                <li>
                    <a href="<?php echo esc_url($nav_item['link']); ?>" class="<?php echo self::is_current_page($nav_item['page'], $nav_item['current']) ? 'fancyforms-active-nav' : ''; ?>">
                        <?php echo esc_html($nav_item['label']); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <?php
    }

    public static function get_form_nav_items($id) {
        $nav_items = array(
            array(
                'link' => admin_url('admin.php?page=fancyforms&fancyforms_action=edit&id=' . absint($id)),
                'label' => esc_html__('Build', 'fancy-forms'),
                'current' => array('edit', 'new', 'duplicate'),
                'page' => 'fancyforms'
            ),
            array(
                'link' => admin_url('admin.php?page=fancyforms&fancyforms_action=settings&id=' . absint($id)),
                'label' => esc_html__('Settings', 'fancy-forms'),
                'current' => array('settings'),
                'page' => 'fancyforms'
            ),
            array(
                'link' => admin_url('admin.php?page=fancyforms&fancyforms_action=style&id=' . absint($id)),
                'label' => esc_html__('Style', 'fancy-forms'),
                'current' => array('style'),
                'page' => 'fancyforms'
            ),
            array(
                'link' => admin_url('admin.php?page=fancyforms-entries&form_id=' . absint($id)),
                'label' => esc_html__('Entries', 'fancy-forms'),
                'current' => array(),
                'page' => 'fancyforms-entries'
            ),
        );
        return $nav_items;
    }

    public static function is_current_page($page, $action = array()) {
        $current_page = FancyFormsHelper::get_var('page');
        $fancyforms_action = FancyFormsHelper::get_var('fancyforms_action');

        if (($page == $current_page) && (!empty($fancyforms_action) && in_array($fancyforms_action, $action))) {
            return true;
        }
        return false;
    }

    public static function get_all_forms() {
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}fancyforms_forms WHERE id!=%d", 0);
        $results = $wpdb->get_results($query);
        return $results;
    }

    public static function get_form_vars($id) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'fancyforms_forms';

        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE id=%d", $id);
        $results = $wpdb->get_row($query);

        if (!$results)
            return;

        foreach ($results as $key => $value) {
            $results->$key = maybe_unserialize($value);
        }

        return $results;
    }

    public function init_overlay_html() {
        $plugin_path = FANCYFORMS_PATH;
        $new_form_overlay = apply_filters('fancyforms_new_form_overlay_template', $plugin_path . 'admin/forms/new-form-overlay.php');
        if (FancyFormsHelper::is_form_listing_page()) {
            include $new_form_overlay;
        }
        if (FancyFormsHelper::is_form_builder_page()) {
            include $plugin_path . 'admin/forms/shortcode-overlay.php';
        }
    }

    public function save_form_settings() {
        if (!current_user_can('manage_options'))
            return;

        $json_vars = htmlspecialchars_decode(nl2br(str_replace('&quot;', '"', FancyFormsHelper::get_post('fancyforms_compact_fields'))));
        $vars = FancyFormsHelper::parse_json_array($json_vars);
        $email_to_array = array();
        foreach ($vars['email_to'] as $row) {
            $email_to_val = trim($row);
            if ($email_to_val) {
                $email_to_array[] = $email_to_val;
            }
        }
        $vars['email_to'] = implode(',', $email_to_array);
        $id = isset($vars['id']) ? absint($vars['id']) : FancyFormsHelper::get_var('id', 'absint');
        unset($vars['id'], $vars['process_form'], $vars['_wp_http_referer']);

        self::update_settings($id, $vars);
        $message = '<span class="mdi mdi-check-circle"></span>' . esc_html__('Form was successfully updated.', 'fancy-forms');
        wp_die(wp_kses_post($message));
    }

    public function save_form_style() {
        if (!current_user_can('manage_options'))
            return;

        $json_vars = htmlspecialchars_decode(nl2br(str_replace('&quot;', '"', FancyFormsHelper::get_post('fancyforms_compact_fields'))));
        $vars = FancyFormsHelper::parse_json_array($json_vars);
        $id = isset($vars['id']) ? absint($vars['id']) : FancyFormsHelper::get_var('id', 'absint');

        self::update_style($id, $vars);
        $message = '<span class="mdi mdi-check-circle"></span>' . esc_html__('Form was successfully updated.', 'fancy-forms');
        wp_die(wp_kses_post($message));
    }

    public static function update_settings($id, $values) {
        global $wpdb;
        $values = FancyFormsHelper::recursive_parse_args($values, FancyFormsHelper::get_form_settings_checkbox_settings());
        $values = FancyFormsHelper::sanitize_array($values, FancyFormsHelper::get_form_settings_sanitize_rules());

        $new_values = array(
            'settings' => serialize($values)
        );
        if (!empty($new_values)) {
            $query_results = $wpdb->update($wpdb->prefix . 'fancyforms_forms', $new_values, array('id' => $id));
        }
        return $query_results;
    }

    public static function update_style($id, $value) {
        global $wpdb;
        $new_values = array(
            'styles' => serialize(FancyFormsHelper::sanitize_array($value))
        );
        if (!empty($new_values)) {
            $query_results = $wpdb->update($wpdb->prefix . 'fancyforms_forms', $new_values, array('id' => $id));
        }
        return $query_results;
    }

    public function add_more_condition_block() {
        $form_id = FancyFormsHelper::get_post('form_id', 'absint', 0);
        $fields = FancyFormsFields::get_form_fields($form_id);
        ?>
        <div class="fancyforms-condition-repeater-block">
            <select name="condition_action[]" required>
                <option value="show"><?php esc_html_e('Show', 'fancy-forms'); ?></option>
                <option value="hide"><?php esc_html_e('Hide', 'fancy-forms'); ?></option>
            </select>

            <select name="compare_from[]" required>
                <option value=""><?php esc_html_e('Select Field', 'fancy-forms'); ?></option>
                <?php
                foreach ($fields as $field) {
                    if (!($field->type == 'heading' || $field->type == 'paragraph' || $field->type == 'separator' || $field->type == 'spacer' || $field->type == 'image' || $field->type == 'captcha')) {
                        ?>
                        <option value="<?php echo esc_attr($field->id); ?>"><?php echo esc_html($field->name) . ' (ID: ' . esc_attr($field->id) . ')'; ?></option>
                        <?php
                    }
                }
                ?>
            </select>

            <span class="fancyforms-condition-seperator"><?php esc_html_e('if', 'fancy-forms'); ?></span>
            <select name="compare_to[]" required>
                <option value=""><?php esc_html_e('Select Field', 'fancy-forms'); ?></option>
                <?php
                foreach ($fields as $field) {
                    if (!($field->type == 'heading' || $field->type == 'paragraph' || $field->type == 'separator' || $field->type == 'spacer' || $field->type == 'image' || $field->type == 'captcha' || $field->type == 'name' || $field->type == 'address')) {
                        ?>
                        <option value="<?php echo esc_attr($field->id); ?>"><?php echo esc_html($field->name) . ' (ID: ' . esc_html($field->id) . ')'; ?></option>
                        <?php
                    }
                }
                ?>
            </select>

            <select name="compare_condition[]" required>
                <option value="equal"><?php esc_html_e('Equals to', 'fancy-forms'); ?></option>
                <option value="not_equal"><?php esc_html_e('Not Equals to', 'fancy-forms'); ?></option>
                <option value="greater_than"><?php esc_html_e('Greater Than', 'fancy-forms'); ?></option>
                <option value="greater_than_or_equal"><?php esc_html_e('Greater Than Or Equals to', 'fancy-forms'); ?></option>
                <option value="less_than"><?php esc_html_e('Less Than', 'fancy-forms'); ?></option>
                <option value="less_than_or_equal"><?php esc_html_e('Less Than Or Equals to', 'fancy-forms'); ?></option>
                <option value="is_like"><?php esc_html_e('Is Like', 'fancy-forms'); ?></option>
                <option value="is_not_like"><?php esc_html_e('Is Not Like', 'fancy-forms'); ?></option>
            </select>

            <input type="text" name="compare_value[]" required/>
            <span class="fancyforms-condition-remove mdi mdi-close"></span>
        </div>
        <?php
        die();
    }

    public static function get_show_hide_conditions($id) {
        $form = FancyFormsBuilder::get_form_vars($id);
        $settings = $form->settings ? $form->settings : array();
        $conditions = array();
        if (isset($settings['condition_action']) && $settings['condition_action']) {
            foreach ($settings['condition_action'] as $key => $row) {
                $condition = array(
                    'condition_action' => $settings['condition_action'][$key],
                    'compare_from' => $settings['compare_from'][$key],
                    'compare_to' => $settings['compare_to'][$key],
                    'compare_condition' => $settings['compare_condition'][$key],
                    'compare_value' => $settings['compare_value'][$key],
                );
                $conditions[] = $condition;
            }
        }
        return $conditions;
    }

    public function add_plugin_action_link($links) {
        $custom['settings'] = sprintf(
                '<a href="%s" aria-label="%s">%s</a>', esc_url(add_query_arg('page', 'fancyforms', admin_url('admin.php'))), esc_attr__('Fancy Forms', 'fancy-forms'), esc_html__('Settings', 'fancy-forms')
        );

        return array_merge($custom, (array) $links);
    }

    public function file_upload_action() {
        if (!wp_verify_nonce(FancyFormsHelper::get_var('file_uploader_nonce'), 'fancyforms-upload-ajax-nonce')) {
            die();
        }

        $allowedExtensions = FancyFormsHelper::get_var('allowedExtensions');
        $default_allowed_extenstions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'odt', 'ppt', 'pptx', 'pps', 'ppsx', 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'mp3', 'mp4', 'ogg', 'wav', 'mp4', 'm4v', 'mov', 'wmv', 'avi', 'mpg', 'ogv', '3gp', 'txt', 'zip', 'rar', '7z', 'csv');
        if ($allowedExtensions) {
            $allowedExtensions = explode(',', $allowedExtensions);
            $allowedExtensions = array_map('trim', $allowedExtensions);
            $filtered_allowed_extenstions = array_intersect($allowedExtensions_array, $default_allowed_extenstions);
        } else {
            $filtered_allowed_extenstions = $default_allowed_extenstions;
        }
        $sizeLimit = FancyFormsHelper::get_var('sizeLimit');
        $uploader = new FancyFormsFileUploader(implode(',', $filtered_allowed_extenstions), $sizeLimit);
        $upload_dir = wp_upload_dir();
        $result = $uploader->handleUpload($upload_dir['basedir'] . FANCYFORMS_UPLOAD_DIR, $replaceOldFile = false, $upload_dir['baseurl'] . FANCYFORMS_UPLOAD_DIR);

        echo json_encode($result);
        die();
    }

    public function file_delete_action() {
        if (wp_verify_nonce(FancyFormsHelper::get_post('_wpnonce'), 'fancyforms-upload-ajax-nonce')) {
            $path = str_replace(' ', '+', FancyFormsHelper::get_post('path', 'wp_kses_post'));
            $upload_dir = wp_upload_dir();
            $temp_dir = $upload_dir['basedir'] . FANCYFORMS_UPLOAD_DIR . '/temp/';
            $check = @unlink($temp_dir . FancyFormsHelper::decrypt($path));

            if ($check) {
                die('success');
            }
        }
        die('error');
    }

    public static function remove_old_temp_files() {
        $max_file_age = apply_filters('fancyforms_temp_file_delete_time', 2 * 3600);
        $upload_dir = wp_upload_dir();
        $temp_dir = $upload_dir['basedir'] . FANCYFORMS_UPLOAD_DIR . '/temp/';

        // Remove old temp files
        if (is_dir($temp_dir) and ( $dir = opendir($temp_dir))) {
            while (($file = readdir($dir)) !== false) {
                $temp_file_path = $temp_dir . DIRECTORY_SEPARATOR . $file;
                if ((filemtime($temp_file_path) < time() - $max_file_age)) {
                    @unlink($temp_file_path);
                }
            }
            closedir($dir);
        }
    }

    public function admin_notice() {
        add_action('admin_notices', array($this, 'admin_notice_content'));
    }

    public function admin_notice_content() {
        if (!$this->is_dismissed('review') && !empty(get_option('fancyforms_first_activation')) && time() > get_option('fancyforms_first_activation') + 15 * DAY_IN_SECONDS) {
            $this->review_notice();
        }
    }

    public static function is_dismissed($notice) {
        $dismissed = get_option('fancyforms_dismissed_notices', array());

        // Handle legacy user meta
        $dismissed_meta = get_user_meta(get_current_user_id(), 'fancyforms_dismissed_notices', true);
        if (is_array($dismissed_meta)) {
            if (array_diff($dismissed_meta, $dismissed)) {
                $dismissed = array_merge($dismissed, $dismissed_meta);
                update_option('fancyforms_dismissed_notices', $dismissed);
            }
            if (!is_multisite()) {
                // Don't delete on multisite to avoid the notices to appear in other sites.
                delete_user_meta(get_current_user_id(), 'fancyforms_dismissed_notices');
            }
        }

        return in_array($notice, $dismissed);
    }

    public function review_notice() {
        ?>
        <div class="fancyforms-notice notice notice-info">
            <?php $this->dismiss_button('review'); ?>
            <div class="fancyforms-notice-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 117.66 152.27"><g><g><path d="M0,3.46A3.46,3.46,0,0,1,3.14,0h80A3.53,3.53,0,0,1,85.6,1l31,31a3.47,3.47,0,0,1,1,2.43V148.81a3.46,3.46,0,0,1-3.46,3.46H31.63a3.46,3.46,0,1,1,0-6.92h79.11V38.07H83.05a3.46,3.46,0,0,1-3.46-3.46V6.92H6.92V145.35H14a3.46,3.46,0,1,1,0,6.92H3.46A3.46,3.46,0,0,1,0,148.81ZM106,31.15,86.51,11.68V31.15Z"/><path d="M78.66,59.3H95.09v6.61H78.66V85.75H72.05V42.87h6.61Zm0,39.67v16.42H72.05V99H52.22V92.36H95.09V99ZM39,99H22.57V92.36H39V72.52h6.61V115.4H39ZM39,59.3V42.87h6.61V59.3H65.44v6.61H22.57V59.3Z"/></g></g></svg>
            </div>

            <div class="fancyforms-notice-content">
                <p>
                    <?php
                    printf(
                            /* translators: %1$s is link start tag, %2$s is link end tag. */
                            esc_html__('Great to see that you have been using Fancy Forms for some time. We hope you love it, and we would really appreciate it if you would %1$sgive us a 5 stars rating%2$s and spread your words to the world.', 'fancy-forms'), '<a target="_blank" href="https://wordpress.org/support/plugin/fancy-forms/reviews/?filter=5">', '</a>'
                    );
                    ?>
                </p>
                <a target="_blank" class="button button-primary button-large" href="https://wordpress.org/support/plugin/fancy-forms/reviews/?filter=5"><span class="dashicons dashicons-thumbs-up"></span><?php echo esc_html__('Yes, of course', 'fancy-forms') ?></a> &nbsp;
                <a class="button button-large" href="<?php echo esc_url(wp_nonce_url(add_query_arg('fancyforms-hide-notice', 'review'), 'review', 'fancyforms_notice_nonce')); ?>"><span class="dashicons dashicons-yes"></span><?php echo esc_html__('I have already rated', 'fancy-forms') ?></a>
            </div>
        </div>
        <?php
    }

    public function welcome_init() {
        if (!get_option('fancyforms_first_activation')) {
            update_option('fancyforms_first_activation', time());
        };

        if (isset($_GET['fancyforms-hide-notice'], $_GET['fancyforms_notice_nonce'])) {
            $notice = sanitize_key($_GET['fancyforms-hide-notice']);
            check_admin_referer($notice, 'fancyforms_notice_nonce');
            self::dismiss($notice);
            wp_safe_redirect(remove_query_arg(array('fancyforms-hide-notice', 'fancyforms_notice_nonce'), wp_get_referer()));
            exit;
        }
    }

    public function dismiss_button($name) {
        printf('<a class="notice-dismiss" href="%s"><span class="screen-reader-text">%s</span></a>', esc_url(wp_nonce_url(add_query_arg('fancyforms-hide-notice', $name), $name, 'fancyforms_notice_nonce')), esc_html__('Dismiss this notice.', 'fancy-forms'));
    }

    public static function dismiss($notice) {
        $dismissed = get_option('fancyforms_dismissed_notices', array());

        if (!in_array($notice, $dismissed)) {
            $dismissed[] = $notice;
            update_option('fancyforms_dismissed_notices', array_unique($dismissed));
        }
    }

}

new FancyFormsBuilder();

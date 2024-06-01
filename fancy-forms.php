<?php

/*
 * Plugin Name: Fancy Forms - Drag & Drop Form Builder
 * Description: Design, Embed, Connect: Your Ultimate Form Companion for WordPress
 * Version: 1.0.0
 * Author: FancyWP
 * Author URI: https://fancywp.com/
 * Text Domain: fancy-forms
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */


defined('ABSPATH') || die();

define('FANCYFORMS_VERSION', '1.0.0');
define('FANCYFORMS_FILE', __FILE__);
define('FANCYFORMS_PATH', plugin_dir_path(FANCYFORMS_FILE));
define('FANCYFORMS_URL', plugin_dir_url(FANCYFORMS_FILE));
define('FANCYFORMS_UPLOAD_DIR', '/fancyforms');

require FANCYFORMS_PATH . 'admin/classes/FancyFormsSerializedStrParser.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsStrReader.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsBlock.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsUploader.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsCreateTable.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsBuilder.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsHelper.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsFields.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsLoader.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsSmtp.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsEntry.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsImportExport.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsListing.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsEntryListing.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsValidate.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsPreview.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsShortcode.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsSettings.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsStyles.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsGridHelper.php';
require FANCYFORMS_PATH . 'admin/classes/FancyFormsEmail.php';

/**
 * Fancy Forms Activation.
 */
register_activation_hook(FANCYFORMS_FILE, 'fancyforms_network_create_table');

function fancyforms_network_create_table($network_wide) {
    global $wpdb;

    if (is_multisite() && $network_wide) {
        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
        foreach ($blog_ids as $blog_id) {
            switch_to_blog($blog_id);
            $db = new FancyFormsCreateTable();
            $db->upgrade();
            restore_current_blog();
        }
    } else {
        $db = new FancyFormsCreateTable();
        $db->upgrade();
    }
}

/**
 * Create form tables on single site.
 */
function fancyforms_create_table() {
    $db = new FancyFormsCreateTable();
    $db->upgrade();
}

/**
 * Register widget for Elementor Page Builder.
 */
add_action('elementor/widgets/register', 'fancyforms_elementor_widget_register');

function fancyforms_elementor_widget_register($widgets_manager) {
    require FANCYFORMS_PATH . 'admin/classes/FancyFormsElement.php';
    $widgets_manager->register(new \FancyFormsElement());
}

/**
 * Create form tables on multisite creation.
 */
add_action('wp_insert_site', 'fancyforms_on_create_blog');

function fancyforms_on_create_blog($data) {
    if (is_plugin_active_for_network('fancy-forms/fancy-forms.php')) {
        switch_to_blog($data->blog_id);
        $db = new FancyFormsCreateTable();
        $db->upgrade();
        restore_current_blog();
    }
}

/**
 * Drop form tables on multisite deletion.
 */
add_filter('wpmu_drop_tables', 'fancyforms_on_delete_blog');

function fancyforms_on_delete_blog($tables) {
    global $wpdb;
    $id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
    $tables[] = $wpdb->get_blog_prefix($id) . 'fancyforms_fields';
    $tables[] = $wpdb->get_blog_prefix($id) . 'fancyforms_forms';
    $tables[] = $wpdb->get_blog_prefix($id) . 'fancyforms_entries';
    $tables[] = $wpdb->get_blog_prefix($id) . 'fancyforms_entry_meta';
    return $tables;
}
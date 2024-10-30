<?php
/**
 * Plugin Name: HTML 2 PDF Rocket
 * Plugin URI:
 * Description: Convert html to pdf.
 * Version: 1.0.0
 * Author: Dmitry Petrik
 * Author URI:
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: h2p
 */

if (!defined('WPINC')) {
    die;
}

$htpr_Directory = new RecursiveDirectoryIterator(plugin_dir_path(__FILE__) . 'classes/');
$htpr_Iterator = new RecursiveIteratorIterator($htpr_Directory);
$htpr_classes = new RegexIterator($htpr_Iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

foreach ($htpr_classes as $htpr_class) {
    include_once $htpr_class[0];
}

add_action('plugins_loaded', 'htpr_plugin_setup');

/**
 * Starts the plugin.
 */
function htpr_plugin_setup()
{
    add_shortcode('h2p', array(htprH2p::get_instance(), 'init'));
    add_action('wp_ajax_get_pdf', array(htprH2p::get_instance(), 'get_pdf'), 99);
    add_action('wp_ajax_nopriv_get_pdf', array(htprH2p::get_instance(), 'get_pdf'), 99);
    add_filter('widget_text', 'do_shortcode');
    add_action('admin_menu', array(new htprMenu(new htprPage()), 'init'));
}

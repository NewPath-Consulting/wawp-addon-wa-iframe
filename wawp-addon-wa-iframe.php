<?php
/**
 * Plugin Name:       WAP Wild Apricot iFrame Add-on
 * Description:       Showcase a Wild Apricot widget using an iframe on your WordPress site with a Gutenberg block!
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           1.0
 * Author:            NewPath Consulting
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       wawp-addon-wa-iframe
 *
 * @package           wawp
 */

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
 */


const SLUG = 'wawp-addon-iframe'; 
const SHOW_NOTICE_ACTIVATION = 'show_notice_activation_' . SLUG;
const LICENSE_CHECK = 'license-check-' . SLUG;
const NAME = 'Wild Apricot iFrame Add-on for WAWP';

add_action( 'init', 'create_block_wawp_addon_wa_iframe_block_init' );
function create_block_wawp_addon_wa_iframe_block_init() {
	if (!class_exists('WAWP\Addon')) {
		deactivate_plugins(plugin_basename(__FILE__));
		add_action('admin_notices', 'wawp_not_loaded');
	}
	$license_valid = WAWP\Addon::instance()::has_valid_license(SLUG);
	if (!$license_valid) return;
	register_block_type_from_metadata( __DIR__ );

}

/**
 * Error message for if WAWP is not installed or activated.
 */
function wawp_not_loaded_notice_msg() {
	echo "<div class='error'><p><strong>";
	echo NAME . '</strong> requires that Wild Apricot for Wordpress is installed and activated.</p></div>';
	unset($_GET['activate']);
	return;
}

// add_action('init', 'add_to_addon_list');
// function add_to_addon_list() {
	if (class_exists('WAWP\Addon')) {
		WAWP\Addon::instance()::new_addon(array(
			'slug' => SLUG,
			'name' => NAME,
			'filename' => plugin_basename(__FILE__),
			'license_check_option' => 'license-check-' . SLUG,
			'show_activation_notice' => 'show_notice_activation_' . SLUG,
			'is_addon' => 1,
			'blocks' => array(
				'wawp/wawp-addon-wa-iframe',
			)
		));
	}
// }

/**
 * Activation function.
 * Checks if WAWP is loaded. Deactivate if not.
 * Calls Addon::activate() function which checks for a license key and sets appropriate flags.
 */
register_activation_hook(plugin_basename(__FILE__), 'activate');
function activate() {
	if (!class_exists('WAWP\Addon')) {
		wawp_not_loaded_die();
		return;
	}

	WAWP\Addon::instance()::activate(SLUG);
}

/**
 * Deactivation function.
 * Deletes the plugin from the list of WAWP plugins in the options table.
 */
register_deactivation_hook(plugin_basename(__FILE__), 'deactivate');
function deactivate() {
	// remove from addons list
	$addons = get_option('wawp_addons');
	unset($addons[SLUG]);
	update_option('wawp_addons', $addons);
}

?>
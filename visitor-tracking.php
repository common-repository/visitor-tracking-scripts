<?php
/*
Plugin Name: Visitor Tracking Scripts
Description: Add your Google Analytics code or other visitor tracking script to your site.
Version: 1.0.0
Author: Moki-Moki Ios
Author URI: http://mokimoki.net/
License: GPL3
*/

/*
Copyright (C) 2017 Moki-Moki Ios http://mokimoki.net/

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/**
 * Visitor Tracking Scripts
 *
 * @version 1.0.0
 */

if (!defined('ABSPATH')) return;

require_once(__DIR__.'/settings.php');

add_action('init', array(VisitorTracking::get_instance(), 'initialize'));
add_action('admin_notices', array(VisitorTracking::get_instance(), 'plugin_activation_notice'));
register_activation_hook(__FILE__, array(VisitorTracking::get_instance(), 'setup_plugin_on_activation')); 

/**
 * Main class of the plugin.
 */
class VisitorTracking {
	
	const PLUGIN_NAME = "Visitor Tracking";
	const ADMIN_SETTINGS_URL = 'options-general.php?page=tracking';
	const VERSION = '1.0.0';
	
	private static $instance;
	private static $settings;
	
	private function __construct() {}
		
	public static function get_instance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
			self::$settings = new VisitorTrackingSettings();
		}
		return self::$instance;
	}
	
	public function initialize() {
		add_action('admin_init', array($this, 'initialize_settings'));
		add_action('admin_menu', array($this, 'create_options_menu'));
		add_action('wp_head', array($this, 'print_google_analytics_script'));
		add_action('wp_footer', array($this, 'print_added_footer_code'), 999);
	}
	
	public function create_options_menu() {
		add_submenu_page(
			'options-general.php',
			self::PLUGIN_NAME,
			self::PLUGIN_NAME,
			'manage_options',
			'tracking',
			array(self::$settings, 'print_settings_page')
		);
	}

	public function initialize_settings() {
		register_setting('tracking', 'tracking_add_code_to_footer');
		register_setting('tracking', 'tracking_google_analytics_code');
		
		add_settings_section( 
			'tracking', 
			__('<span class="dashicons dashicons-visibility"></span> Visitor Tracking'), 
			null, 
			'tracking'
		);	
		
		add_settings_field(
			'tracking_add_code_to_footer',
			__('Add tracking script to footer'),
			array(self::$settings, 'print_option_add_code_to_footer'),
			'tracking',
			'tracking'
		);
		
		add_settings_field(
			'tracking_google_analytics_code',
			__('Google Analytics tracking code'),
			array(self::$settings, 'print_option_google_analytics_code'),
			'tracking',
			'tracking'
		);
	}

	public function setup_plugin_on_activation() {		
		set_transient('tracking_activation_notice', TRUE, 5);
		add_action('admin_notices', array($this, 'plugin_activation_notice'));
	}
	
	public function plugin_activation_notice() {
		if (get_transient('tracking_activation_notice')) {
			$settings_url = $settings_url = get_admin_url() . VisitorTracking::ADMIN_SETTINGS_URL;
			echo '<div class="notice updated"><p><strong>'.sprintf(__('Tracking plugin activated. Set tracking information at <a href="%s">settings page</a>.'), $settings_url).'</strong></p></div>';	
		}		
	}
	
	public function print_added_footer_code() {
		$code = get_option('tracking_add_code_to_footer');
		if (!empty($code)) {
			echo $code;
		}
	}
	
	public function print_google_analytics_script() {
		$tracking_code = get_option('tracking_google_analytics_code', FALSE);
		
		if (empty($tracking_code)) {
			return;
		}
		
		echo str_replace("\t", '', '
			<!-- Global Site Tag (gtag.js) - Google Analytics -->
			<script async src="https://www.googletagmanager.com/gtag/js?id='.$tracking_code.'"></script>
			<script>
			  window.dataLayer = window.dataLayer || [];
			  function gtag(){dataLayer.push(arguments)};
			  gtag(\'js\', new Date());

			  gtag(\'config\', \''.$tracking_code.'\');
			</script>
			');
	}
}

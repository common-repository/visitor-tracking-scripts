<?php

/**
 * Module for settings UI.
 */
if (!defined('ABSPATH')) return;

class VisitorTrackingSettings {

	public function print_settings_page() {
		if (!current_user_can('manage_options')) {
			return;
		}		
		?>
		

		<h1><?= esc_html_e('Visitor Tracking Settings'); ?></h1>
		
		<div class="pm-wp-seo-settings">
			
			<form action="options.php" method="post">
				<?php
					submit_button(__('Save settings'));
					settings_fields('tracking');
					do_settings_sections('tracking');
				?>
				<h2><span class="dashicons dashicons-image-filter"></span> <?php _e('More'); ?></h2>		
				
				<p><?php _e('This plugin is part of <a target="_blank" href="http://mokimoki.net/poor-mans-wordpress-seo/">Poor Man\'s WordPress SEO</a> tools - the absolutely free All-In-One solution to take control of SEO on your site.'); ?></p>	
				
				<?php				
					
					submit_button(__('Save settings'));
				?>
			</form>

		</div>
		<?php
	}
	
	public function print_option_add_code_to_footer() {
		$footer_code = get_option('tracking_add_code_to_footer');
		echo '<textarea style="width: 85%" cols="5" rows="7" name="tracking_add_code_to_footer" placeholder="'. __('Copy/paste your code here...') .'">'.$footer_code.'</textarea>';
	}
	
	public function print_option_google_analytics_code() {
		$tracking_code = get_option('tracking_google_analytics_code');
		echo '<input type="text" class="half-width" name="tracking_google_analytics_code" value="'.$tracking_code.'"/>';
	}	

}
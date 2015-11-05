<?php
/*##
Plugin Name: Google +1 by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Add Google +1 button to your WordPress website.
Author: BestWebSoft
Text Domain: google-one
Domain Path: /languages
Version: 1.2.6
Author URI: http://bestwebsoft.com
License: GPLv2 or later
*/

/*	@ Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Add BWS menu */
if ( ! function_exists( 'gglplsn_admin_menu' ) ) {
	function gglplsn_admin_menu() {
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		$settings = add_submenu_page( 'bws_plugins', __( 'Google +1 Settings', 'google-one' ), 'Google +1', 'manage_options', "google-plus-one.php", 'gglplsn_options' );
		add_action( 'load-' . $settings, 'gglplsn_add_tabs' );
	}
}
/* end gglplsn_admin_menu ##*/

if ( ! function_exists( 'gglplsn_plugins_loaded' ) ) {
	function gglplsn_plugins_loaded() {
		/* Internationalization, first(!) */
		load_plugin_textdomain( 'google-one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		global $gglplsn_plugin_info, $gglplsn_lang_codes;

		if ( empty( $gglplsn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );
		}

		/*## add general functions */
		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $gglplsn_plugin_info, '3.8', '3.1' );/* check compatible with current WP version ##*/

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) ) {
			gglplsn_settings();
			$gglplsn_lang_codes = array(
				'af' => "Afrikaans", 'am' => "Amharic", 'ar' => "Arabic", 'eu' => "Basque", 'bn' => "Bengali", 'bg' => "Bulgarian", 'ca' => "Catalan", 'zh-HK' => "Chinese (Hong Kong)", 'zh-CN' => "Chinese (Simplified)", 'zh-TW' => "Chinese (Traditional)", 'hr' => "Croatian", 'cs' => "Czech", 'da' => "Danish", 'nl' => "Dutch", 'en-GB' => "English (UK)", 'en' => "English (US)", 'et' => "Estonian", 'fil' => "Filipino", 'fi' => "Finnish", 'fr' => "French", 'fr-CA' => "French (Canadian)", 'gl' => "Galician", 'de' => "German", 'el' => "Greek", 'gu' => "Gujarati", 'iw' => "Hebrew", 'hi' => "Hindi", 'hu' => "Hungarian", 'is' => "Icelandic", 'id' => "Indonesian", 'it' => "Italian", 'ja' => "Japanese", 'kn' => "Kannada", 'ko' => "Korean", 'lv' => "Latvian", 'lt' => "Lithuanian", 'ms' => "Malay", 'ml' => "Malayalam", 'mr' => "Marathi", 'no' => "Norwegian", 'fa' => "Persian", 'pl' => "Polish", 'pt-BR' => "Portuguese (Brazil)", 'pt-PT' => "Portuguese (Portugal)", 'ro' => "Romanian", 'ru' => "Russian", 'sr' => "Serbian", 'sk' => "Slovak", 'sl' => "Slovenian", 'es' => "Spanish", 'es-419' => "Spanish (Latin America)", 'sw' => "Swahili", 'sv' => "Swedish", 'ta' => "Tamil", 'te' => "Telugu", 'th' => "Thai", 'tr' => "Turkish", 'uk' => "Ukrainian", 'ur' => "Urdu", 'vi' => "Vietnamese", 'zu' => "Zulu"
			);
		}
	}
}

/* Function for admin_init */
if ( ! function_exists( 'gglplsn_admin_init' ) ) {
	function gglplsn_admin_init() {
		global $bws_plugin_info, $gglplsn_plugin_info, $bws_shortcode_list;

		/*## Function for bws menu */
		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '102', 'version' => $gglplsn_plugin_info["Version"] );

		/* add Google +1 to global $bws_shortcode_list ##*/
		$bws_shortcode_list['gglplsn'] = array( 'name' => 'Google +1' );
	}
}

if ( ! function_exists ( 'gglplsn_settings' ) ) {
	function gglplsn_settings() {
		global $gglplsn_options, $gglplsn_plugin_info, $gglplsn_option_defaults;

		/* Default options */
		$gglplsn_option_defaults		=	array(
			'plugin_option_version'		=>	$gglplsn_plugin_info["Version"],
			'js'						=>	'1',
			'annotation'				=>	'none',
			'size'						=>	'standard',
			'position'					=>	'before_post',
			'lang'						=>	'en',
			'posts'						=>	'1',
			'pages'						=>	'1',
			'homepage'					=>	'1',
			'use_multilanguage_locale'	=>	0,
			'display_settings_notice'	=>	1,
			'first_install'				=>	strtotime( "now" )
		);

		if ( ! get_option( 'gglplsn_options' ) )
			add_option( 'gglplsn_options', $gglplsn_option_defaults );

		$gglplsn_options = get_option( 'gglplsn_options' );

		if ( ! isset( $gglplsn_options['plugin_option_version'] ) || $gglplsn_options['plugin_option_version'] != $gglplsn_plugin_info["Version"] ) {
			if ( '1' == $gglplsn_options['annotation'] )
				$gglplsn_options['annotation'] = 'bubble';
			elseif ( 0 == $gglplsn_options['annotation'] )
				$gglplsn_options['annotation'] = 'none';

			$gglplsn_option_defaults['display_settings_notice'] = 0;
			$gglplsn_options = array_merge( $gglplsn_option_defaults, $gglplsn_options );
			$gglplsn_options['plugin_option_version'] = $gglplsn_plugin_info["Version"];
			/* show pro features */
			$gglplsn_options['hide_premium_options'] = array();

			update_option( 'gglplsn_options', $gglplsn_options );
		}
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'gglplsn_options' ) ) {
	function gglplsn_options() {
		global $gglplsn_options, $wp_version, $gglplsn_plugin_info, $gglplsn_option_defaults, $gglplsn_lang_codes;
		$message = $error = "";
		$plugin_basename = plugin_basename( __FILE__ );

		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$all_plugins = get_plugins();

		/* Save data for settings page */
		if ( isset( $_REQUEST['gglplsn_form_submit'] ) && check_admin_referer( $plugin_basename, 'gglplsn_nonce_name' ) ) {
			if ( isset( $_POST['bws_hide_premium_options'] ) ) {
				$hide_result = bws_hide_premium_options( $gglplsn_options );
				$gglplsn_options = $hide_result['options'];
			}
			$gglplsn_options['js']							=	isset( $_REQUEST['gglplsn_js'] ) ? 1 : 0 ;
			$gglplsn_options['annotation']					=	$_REQUEST['gglplsn_annotation'];
			$gglplsn_options['size']						=	$_REQUEST['gglplsn_size'];
			$gglplsn_options['position']					=	$_REQUEST['gglplsn_position'];
			$gglplsn_options['lang']						=	$_REQUEST['gglplsn_lang'];
			$gglplsn_options['posts']						=	isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
			$gglplsn_options['pages']						=	isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
			$gglplsn_options['homepage']					=	isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
			$gglplsn_options['use_multilanguage_locale']	=	isset( $_REQUEST['gglplsn_use_multilanguage_locale'] ) ? 1 : 0;
			$message = __( 'Settings saved', 'google-one' );
			update_option( 'gglplsn_options', $gglplsn_options );
		}		

		/*## check banner */
		$bws_hide_premium_options_check = bws_hide_premium_options_check( $gglplsn_options );

		/* Add restore function */
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
			$gglplsn_options = $gglplsn_option_defaults;
			update_option( 'gglplsn_options', $gglplsn_options );
			$message = __( 'All plugin settings were restored.', 'google-one' );
		}		

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename, 'gglplsn_options' );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
			elseif ( ! empty( $go_pro_result['message'] ) )
				$message = $go_pro_result['message'];
		} /* end GO PRO ##*/ ?>
		<!-- general -->
		<div class="wrap">
			<h2><?php _e( 'Google +1 Settings', 'google-one' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php"><?php _e( 'Settings', 'google-one' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php&amp;action=extra"><?php _e( 'Extra settings', 'google-one' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php&amp;action=go_pro"><?php _e( 'Go PRO', 'google-one' ); ?></a>
			</h2>
			<?php if ( ! empty( $hide_result['message'] ) ) { ?>
				<div class="updated fade"><p><strong><?php echo $hide_result['message']; ?></strong></p></div>
			<?php } ?>
			<!-- end general -->
			<div class="updated fade" <?php if ( '' == $message || "" != $error ) echo 'style="display:none"'; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<?php bws_show_settings_notice(); ?>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php /*## check action */ if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { /* check action ##*/ ?>
					<p><?php _e( 'For the correct work of the button do not use it locally or on a free hosting', 'google-one' ); ?><br /></p>
					<div><?php $icon_shortcode = ( "google-plus-one.php" == $_GET['page'] ) ? plugins_url( 'bws_menu/images/shortcode-icon.png', __FILE__ ) : plugins_url( 'social-buttons-pack/bws_menu/images/shortcode-icon.png' );
					printf( 
						__( "If you would like to add Google +1 button to your page or post, please use %s button", 'google-one' ), 
						'<span class="bws_code"><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt=""/></span>' ); ?> 
						<div class="bws_help_box bws_help_box_right dashicons dashicons-editor-help">
							<div class="bws_hidden_help_text" style="min-width: 180px;">
								<?php printf( 
									__( "You can add Google +1 button to your page or post by clicking on %s button in the content edit block using the Visual mode. If the button isn't displayed, please use the shortcode %s", 'google-one' ), 
									'<code><img style="vertical-align: sub;" src="' . $icon_shortcode . '" alt="" /></code>',
									'<code>[bws_googleplusone]</code>'
								); ?>
							</div>
						</div>
					</div>
					<form method="post" action="" class="bws_form">
						<table class="form-table gglplsn_form-table">
							<tbody>
								<tr valign="top">
									<th><?php _e( 'Enable Google +1 Button', 'google-one' ); ?></th>
									<td>
										<label>
											<input type="checkbox" name="gglplsn_js"<?php if ( '1' == $gglplsn_options['js'] ) echo 'checked="checked"'; ?> value="1" />
											<span class="bws_info">(<?php _e( 'Enable or Disable Google+1 JavaScript', 'google-one' ); ?>)</span>
										</label>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Size', 'google-one' ); ?></th>
									<td>
										<select name="gglplsn_size">
											<option value="standard" <?php if ( 'standard' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Standard', 'google-one' ); ?></option>
											<option value="small" <?php if ( 'small' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Small', 'google-one' ); ?></option>
											<option value="medium" <?php if ( 'medium' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Medium', 'google-one' ); ?></option>
											<option value="tall" <?php if ( 'tall' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Tall', 'google-one' ); ?></option>
										</select>
										<span class="bws_info">(<?php _e( 'Please choose one of four different sizes of buttons', 'google-one' ); ?>)</span>
									</td>
								</tr>
								<tr valign="top">
									<th><?php _e( 'Annotation', 'google-one' ); ?></th>
									<td>
										<select name="gglplsn_annotation">
											<option value="inline" <?php if ( 'inline' == $gglplsn_options['annotation'] ) echo 'selected="selected"';?>><?php _e( 'Inline', 'google-one' ); ?></option>
											<option value="bubble" <?php if ( 'bubble' == $gglplsn_options['annotation'] ) echo 'selected="selected"';?>><?php _e( 'Bubble', 'google-one' ); ?></option>
											<option value="none" <?php if ( 'none' == $gglplsn_options['annotation'] ) echo 'selected="selected"';?>><?php _e( 'None', 'google-one' ); ?></option>
										</select>
										<br /><span class="bws_info">(<?php _e( 'Display counters showing how many times your article has been liked', 'google-one' ); ?>)</span>
									</td>
								</tr>																
								<tr>
									<th scope="row"><?php _e( 'Language', 'google-one' ); ?></th>
									<td>
										<fieldset>
											<select name="gglplsn_lang">
												<?php foreach ( $gglplsn_lang_codes as $key => $val ) {
													echo '<option value="' . $key . '"';
													if ( $key == $gglplsn_options['lang'] )
														echo ' selected="selected"';
													echo '>' . esc_html ( $val ) . '</option>';
												} ?>
											</select>
											<span class="bws_info">(<?php _e( 'Select the language to display information on the button', 'google-one' ); ?>)</span><br />
											<label>
												<?php if ( array_key_exists( 'multilanguage/multilanguage.php', $all_plugins ) || array_key_exists( 'multilanguage-pro/multilanguage-pro.php', $all_plugins ) ) {
													if ( is_plugin_active( 'multilanguage/multilanguage.php' ) || is_plugin_active( 'multilanguage-pro/multilanguage-pro.php' ) ) { ?>
														<input type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" <?php if ( 1 == $gglplsn_options["use_multilanguage_locale"] ) echo 'checked="checked"'; ?> /> 
														<?php _e( 'Use the current site language', 'google-one' ); ?> <span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft)</span>
													<?php } else { ?>
														<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" /> 
														<?php _e( 'Use the current site language', 'google-one' ); ?> 
														<span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft) <a href="<?php echo bloginfo("url"); ?>/wp-admin/plugins.php"><?php _e( 'Activate', 'google-one' ); ?> Multilanguage</a></span>
													<?php }
												} else { ?>
													<input disabled="disabled" type="checkbox" name="gglplsn_use_multilanguage_locale" value="1" /> 
													<?php _e( 'Use the current site language', 'google-one' ); ?> 
													<span class="bws_info">(<?php _e( 'Using', 'google-one' ); ?> Multilanguage by BestWebSoft) <a href="http://bestwebsoft.com/products/multilanguage/?k=196fb3bb74b6e8b1e08f92cddfd54313&pn=78&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( 'Download', 'google-one' ); ?> Multilanguage</a></span>
												<?php } ?>
										</label>
										</fieldset>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Button Position', 'google-one' ); ?></th>
									<td>
										<select name="gglplsn_position">
											<option value="before_post" <?php if ( 'before_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'google-one' ); ?></option>
											<option value="after_post" <?php if ( 'after_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'google-one' ); ?></option>
											<option value="afterandbefore" <?php if ( 'afterandbefore' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'google-one' ); ?></option>
										</select>
										<span class="bws_info">(<?php _e( 'Please select location for the button on the page', 'google-one' ); ?>)</span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php _e( 'Show button', 'google-one' ); ?></th>
									<td>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_posts" <?php if ( '1' == $gglplsn_options['posts'] ) echo 'checked="checked"'; ?> value="1" />
												<?php _e( 'Show in posts', 'google-one' ); ?>
											</label>
										</p>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_pages" <?php if ( '1' == $gglplsn_options['pages'] ) echo 'checked="checked"'; ?>  value="1" />
												<?php _e( 'Show in pages', 'google-one' ); ?>
											</label>
										</p>
										<p>
											<label>
												<input type="checkbox" name="gglplsn_homepage" <?php if ( '1' == $gglplsn_options['homepage'] ) echo 'checked="checked"'; ?>  value="1" />
												<?php _e( 'Show on the homepage', 'google-one' ); ?>
											</label>
										</p>
										<p>
											<span class="bws_info">(<?php _e( 'Please select the page on which you want to see the button', 'google-one' ); ?>)</span>
										</p>
									</td>
								</tr>
							</tbody>
						</table>
						<!-- general -->
						<?php if ( ! $bws_hide_premium_options_check ) { ?>
							<div class="bws_pro_version_bloc">
								<div class="bws_pro_version_table_bloc">	
									<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'google-one' ); ?>"></button>
									<div class="bws_table_bg"></div>											
									<table class="form-table bws_pro_version">
										<tr valign="top">
											<th><?php _e( '"+1" for an entire site on every page', 'google-one' ); ?></th>
											<td>
												<input disabled="disabled" name='gglplsn_entire_site_like' type='checkbox' value='1' /><br />
												<span class="bws_info"><?php _e( 'Notice: This option does not create an extra button. This option merely allows your users to +1 the entire website when this option is enabled, or a single post when this option is disabled, when clicking the regular "+1" button.', 'google-one'  ); ?></span>
											</td>
										</tr>	
										<tr valign="top">
											<th scope="row" colspan="2">
												* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'google-one' ); ?>
											</th>
										</tr>			
									</table>	
								</div>
								<div class="bws_pro_version_tooltip">
									<div class="bws_info">
										<?php _e( 'Unlock premium options by upgrading to Pro version', 'google-one' ); ?> 
									</div>
									<a class="bws_button" href="http://bestwebsoft.com/products/google-plus-one/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google +1 Pro"><?php _e( 'Learn More', 'google-one' ); ?></a>
									<div class="clear"></div>					
								</div>
							</div>
						<?php } ?>
						<!-- end general -->						
						<p class="submit">
							<input id="bws-submit-button" type="submit" value="<?php _e( 'Save Changes', 'google-one' ); ?>" class="button-primary" />
							<input type="hidden" name="gglplsn_form_submit" value="1" />
							<?php wp_nonce_field( $plugin_basename, 'gglplsn_nonce_name' ); ?>
						</p>						
					</form>
					<!-- general -->
					<?php bws_form_restore_default_settings( $plugin_basename );
				}
			} elseif ( 'extra' == $_GET['action'] ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<div class="bws_pro_version">
							<?php _e( 'Please choose the necessary post types (or single pages) where Google +1 button will be displayed:', 'google-one' ); ?>
							<p>
								<input disabled="disabled" checked="checked" id="twttrpr_jstree_url" type="checkbox" name="twttrpr_jstree_url" value="1" />
								<?php _e( "Show URL for pages", 'google-one' );?>
							</p>
							<img src="<?php echo plugins_url( 'images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of the site's pages tree", 'google-one' ); ?>" title="<?php _e( "Example of the site's pages tree", 'google-one' ); ?>" />
							<p class="submit"><input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google-one' ); ?>" /></p>
							<p><strong>* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'google-one' ); ?></strong></p>
						</div>
					</div>
					<div class="bws_pro_version_tooltip">
						<div class="bws_info">
							<?php _e( 'Unlock premium options by upgrading to Pro version', 'google-one' ); ?>
						</div>
						<a class="bws_button" href="http://bestwebsoft.com/products/google-plus-one/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google +1 Pro"><?php _e( 'Learn More', 'google-one' ); ?></a>
						<div class="clear"></div>
					</div>
				</div>
			<?php } elseif ( 'go_pro' == $_GET['action'] ) { 
				bws_go_pro_tab_show( $bws_hide_premium_options_check, $gglplsn_plugin_info, $plugin_basename, 'google-plus-one.php', 'google-plus-one-pro.php', 'google-one-pro/google-plus-one-pro.php', 'google-plus-one', '0a5a8a70ed3c34b95587de0604ca9517', '102', isset( $go_pro_result['pro_plugin_is_activated'] ) ); 
			} 
			bws_plugin_reviews_block( $gglplsn_plugin_info['Name'], 'google-one' ); ?>	
		</div>
		<!-- end general -->			
	<?php }
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		if ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
		}
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_options, $gglplsn_lang_codes;		
		if ( '1' == $gglplsn_options['js'] ) {			
			if ( 1 == $gglplsn_options['use_multilanguage_locale'] && isset( $_SESSION['language'] ) ) {
				if ( array_key_exists( $_SESSION['language'], $gglplsn_lang_codes ) ) {
					$gglplsn_locale = $_SESSION['language'];
				} else {
					global $mltlngg_languages, $mltlnggpr_languages;
					if ( ! empty( $mltlngg_languages ) || ! empty( $mltlnggpr_languages ) ) {
						$languages_list = ! empty( $mltlngg_languages ) ? $mltlngg_languages : $mltlnggpr_languages;
						foreach ( $languages_list as $key => $one_lang ) {
							$mltlngg_lang_key = array_search( $_SESSION['language'], $one_lang );
							if ( false !== $mltlngg_lang_key ) {
								$gglplsn_lang_key = array_search( $one_lang[2], $gglplsn_lang_codes );
								if ( false != $gglplsn_lang_key )
									$gglplsn_locale = $gglplsn_lang_key;
								break;
							}
						}
					}
				}
			}
			if ( empty( $gglplsn_locale ) )
				$gglplsn_locale = $gglplsn_options['lang']; ?>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
				<?php if ( 'en-US' != $gglplsn_locale ) { ?>
					{'lang': '<?php echo $gglplsn_locale; ?>'}
				<?php } ?>
			</script>
		<?php }
	}
}


/* Google +1 on page  */
if ( ! function_exists( 'gglplsn_pos' ) ) {
	function gglplsn_pos( $content ) {
		global $gglplsn_options;		

		if ( "1" == $gglplsn_options['posts'] || '1' == $gglplsn_options['pages'] || '1' == $gglplsn_options['homepage'] ) {
			if ( ( is_single() && '1' == $gglplsn_options['posts'] ) || ( is_page() && '1' == $gglplsn_options['pages'] ) || ( ( is_home() || is_front_page() ) && '1' == $gglplsn_options['homepage'] ) ) {
				$button = '<div class="gglplsn_share"><div class="g-plusone"';
				if ( 'standard' != $gglplsn_options['size'] ) {
					$button .= ' data-size="' . $gglplsn_options['size'] . '"';
				}
				if ( 'none' == $gglplsn_options['annotation'] ) {
					$button .= ' data-annotation="none"';
				} elseif ( 'inline' == $gglplsn_options['annotation'] ) {
					$button .= ' data-annotation="inline"';
				}
				$button .= ' data-href="' . get_permalink() . '" data-callback="on"></div></div>';

				if ( 'before_post' == $gglplsn_options['position'] ) {
					return $button . $content;
				} else if ( 'after_post' == $gglplsn_options['position'] ) {
					return  $content . $button;
				} else if ( 'afterandbefore' == $gglplsn_options['position'] ) {
					return $button . $content . $button;
				}
			}			
		}
		return $content;
	}
}

/* Google +1 shortcode */
/* [bws_googleplusone] */
if ( ! function_exists( 'gglplsn_shortcode' ) ) {
	function gglplsn_shortcode( $atts ) {
		global $gglplsn_options;
		extract( shortcode_atts(
			array(
				"annotation"	=>	$gglplsn_options['annotation'],
				"url"			=>	get_permalink(),
				"size"			=>	$gglplsn_options['size']
			),
			$atts )
		);
		$shortbutton = '<br/><div class="gglplsn_share"><div class="g-plusone"';
		if ( 'standard' != $size ) {
			$shortbutton .= ' data-size="' . $size . '"';
		}
		if ( 'none' == $gglplsn_options['annotation'] ) {
			$shortbutton .= ' data-annotation="none"';
		} elseif ( 'inline' == $gglplsn_options['annotation'] ) {
			$shortbutton .= ' data-annotation="inline"';
		}
		$shortbutton .= ' data-href="' . $url . '" data-callback="on"></div></div>';
		return $shortbutton;
	}
}

/* add shortcode content  */
if ( ! function_exists( 'gglplsn_shortcode_button_content' ) ) {
	function gglplsn_shortcode_button_content( $content ) { ?>
		<div id="gglplsn" style="display:none;">
			<fieldset>				
				<?php _e( 'Add Google +1 button to your page or post', 'google-one' ); ?>
			</fieldset>
			<input class="bws_default_shortcode" type="hidden" name="default" value="[bws_googleplusone]" />
			<div class="clear"></div>
		</div>
	<?php }
}

/*## Functions creates other links on plugins page. */
if ( ! function_exists( 'gglplsn_action_links' ) ) {
	function gglplsn_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google-one' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'gglplsn_register_plugin_links' ) ) {
	function gglplsn_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google-one' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-one/faq/" target="_blank">' . __( 'FAQ', 'google-one' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google-one' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'gglplsn_plugin_banner' ) ) {
	function gglplsn_plugin_banner() {
		global $hook_suffix;
		if ( 'plugins.php' == $hook_suffix ) {
			global $gglplsn_plugin_info, $gglplsn_options;

			if ( empty( $gglplsn_options ) )
				$gglplsn_options = get_option( 'gglplsn_options' );

			if ( isset( $gglplsn_options['first_install'] ) && strtotime( '-1 week' ) > $gglplsn_options['first_install'] )
				bws_plugin_banner( $gglplsn_plugin_info, 'gglplsn', 'google-plus-one', 'ca01bbe0edd696fddb27769001fe8084', '102', '//ps.w.org/google-one/assets/icon-128x128.png' );
			
			if ( ! is_network_admin() )
				bws_plugin_banner_to_settings( $gglplsn_plugin_info, 'gglplsn_options', 'google-one', 'admin.php?page=google-plus-one.php' );
		}
	}
}

/* add help tab  */
if ( ! function_exists( 'gglplsn_add_tabs' ) ) {
	function gglplsn_add_tabs() {
		$screen = get_current_screen();
		$args = array(
			'id' 			=> 'gglplsn',
			'section' 		=> '200538809'
		);
		bws_help_tab( $screen, $args );
	}
}

if ( ! function_exists( 'gglplsn_uninstall' ) ) {
	function gglplsn_uninstall() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$all_plugins = get_plugins();
		if ( ! array_key_exists( 'bws-social-buttons/bws-social-buttons.php', $all_plugins ) ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				global $wpdb;
				$old_blog = $wpdb->blogid;
				/* Get all blog ids */
				$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
				foreach ( $blogids as $blog_id ) {
					switch_to_blog( $blog_id );
					delete_option( 'gglplsn_options' );
				}
				switch_to_blog( $old_blog );
			} else {
				delete_option( 'gglplsn_options' );
			}
		}
	}
}
/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'gglplsn_admin_menu' );
/* Initialization ##*/
add_action( 'init', 'gglplsn_init' );
add_action( 'plugins_loaded', 'gglplsn_plugins_loaded' );
add_action( 'admin_init', 'gglplsn_admin_init' );
/* Adding stylesheets */
add_action( 'wp_head', 'gglplsn_js' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
/* Adding plugin buttons */
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'gglplsn_pos' );
/* custom filter for bws button in tinyMCE */
add_filter( 'bws_shortcode_button_content', 'gglplsn_shortcode_button_content' );
/*## Additional links on the plugin page */
add_filter( 'plugin_action_links', 'gglplsn_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglplsn_register_plugin_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'gglplsn_plugin_banner' );
/* Plugin uninstall function */
register_uninstall_hook( __FILE__, 'gglplsn_uninstall' );
/* end ##*/
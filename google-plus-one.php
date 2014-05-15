<?php
/*
Plugin Name: Google +1
Plugin URI:  http://bestwebsoft.com/plugin/
Description: Add Google +1 button to your WordPress website.
Author: BestWebSoft
Version: 1.1.6
Author URI: http://bestwebsoft.com
License: GPLv2 or later
*/

/*	@ Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )
	
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

if ( ! function_exists( 'gglplsn_admin_menu' ) ) {
	function gglplsn_admin_menu() {
		global $bstwbsftwppdtplgns_options, $wpmu, $bstwbsftwppdtplgns_added_menu;
		$bws_menu_version = get_plugin_data( plugin_dir_path( __FILE__ ) . "bws_menu/bws_menu.php" );
		$bws_menu_version = $bws_menu_version["Version"];
		$base = plugin_basename(__FILE__);

		if ( ! isset( $bstwbsftwppdtplgns_options ) ) {
			if ( 1 == $wpmu ) {
				if ( ! get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( ! get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', array(), '', 'yes' );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}
		}

		if ( isset( $bstwbsftwppdtplgns_options['bws_menu_version'] ) ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			unset( $bstwbsftwppdtplgns_options['bws_menu_version'] );
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] ) || $bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] < $bws_menu_version ) {
			$bstwbsftwppdtplgns_options['bws_menu']['version'][ $base ] = $bws_menu_version;
			update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
		} else if ( ! isset( $bstwbsftwppdtplgns_added_menu ) ) {
			$plugin_with_newer_menu = $base;
			foreach ( $bstwbsftwppdtplgns_options['bws_menu']['version'] as $key => $value ) {
				if ( $bws_menu_version < $value && is_plugin_active( $base ) ) {
					$plugin_with_newer_menu = $key;
				}
			}
			$plugin_with_newer_menu = explode( '/', $plugin_with_newer_menu );
			$wp_content_dir = defined( 'WP_CONTENT_DIR' ) ? basename( WP_CONTENT_DIR ) : 'wp-content';
			if ( file_exists( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' ) )
				require_once( ABSPATH . $wp_content_dir . '/plugins/' . $plugin_with_newer_menu[0] . '/bws_menu/bws_menu.php' );
			else
				require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );
			$bstwbsftwppdtplgns_added_menu = true;			
		}

		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( 'images/px.png', __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'Google +1 Settings', 'google_plus_one' ), __( 'Google +1', 'google_plus_one' ), 'manage_options', "google-plus-one.php", 'gglplsn_options' );
	}
}

if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		/* Internationalization */
		load_plugin_textdomain( 'google_plus_one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "google-plus-one.php" == $_GET['page'] ) )
			gglplsn_settings();
	}
}

if ( ! function_exists( 'gglplsn_admin_init' ) ) {
	function gglplsn_admin_init() {
		global $bws_plugin_info, $gglplsn_plugin_info;

		if ( ! $gglplsn_plugin_info )
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '102', 'version' => $gglplsn_plugin_info["Version"] );
		/* Function check if plugin is compatible with current WP version  */
		gglplsn_version_check();
	}
}

if ( ! function_exists ( 'gglplsn_settings' ) ) {
	function gglplsn_settings() {
		global $wpmu, $gglplsn_options, $gglplsn_plugin_info;

		if ( ! $gglplsn_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );	
		}

		/* Default options */
		$gglplsn_option_defaults	=	array(
			'plugin_option_version' => $gglplsn_plugin_info["Version"],
			'js'					=>	'1',
			'annotation'			=>	'0',
			'size'					=>	'standart',
			'position'				=>	'before_post',
			'lang'					=>	'en-GB',
			'posts'					=>	'1',
			'pages'					=>	'1',
			'homepage'				=>	'1'
		);
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'gglplsn_options' ) )
				add_site_option( 'gglplsn_options', $gglplsn_option_defaults, '', 'yes' );

			$gglplsn_options = get_site_option( 'gglplsn_options' );
		} else {
			if ( ! get_option( 'gglplsn_options' ) )
				add_option( 'gglplsn_options', $gglplsn_option_defaults, '', 'yes' );

			$gglplsn_options = get_option( 'gglplsn_options' );
		}

		if ( ! isset( $gglplsn_options['plugin_option_version'] ) || $gglplsn_options['plugin_option_version'] != $gglplsn_plugin_info["Version"] ) {
			$gglplsn_options = array_merge( $gglplsn_option_defaults, $gglplsn_options );
			$gglplsn_options['plugin_option_version'] = $gglplsn_plugin_info["Version"];
			update_option( 'gglplsn_options', $gglplsn_options );
		}
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'gglplsn_options' ) ) {
	function gglplsn_options() {
		global $gglplsn_options, $wp_version, $gglplsn_plugin_info;
		$message = $error = "";

		/* Save data for settings page */
		if ( isset( $_REQUEST['gglplsn_form_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'gglplsn_nonce_name' ) ) {
			$gglplsn_options['js']			=	isset( $_REQUEST['gglplsn_js'] ) ? 1 : 0 ;
			$gglplsn_options['annotation']	=	isset( $_REQUEST['gglplsn_annotation'] ) ? 1 : 0 ;
			$gglplsn_options['size']		=	$_REQUEST['gglplsn_size'];
			$gglplsn_options['position']	=	$_REQUEST['gglplsn_position'];
			$gglplsn_options['lang']		=	$_REQUEST['gglplsn_lang'];
			$gglplsn_options['posts']		=	isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
			$gglplsn_options['pages']		=	isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
			$gglplsn_options['homepage']	=	isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
			$message = __( 'Settings saved', 'google_plus_one' );
			update_option( 'gglplsn_options', $gglplsn_options );
		}
		$lang_codes = array(
			'af' => "Afrikaans", 'am' => "Amharic", 'ar' => "Arabic", 'eu' => "Basque", 'bn' => "Bengali", 'bg' => "Bulgarian", 'ca' => "Catalan", 'zh-HK' => "Chinese (Hong Kong)", 'zn-CH' => "Chinese (Simplified)", 'zh-TW' => "Chinese (Traditional)", 'hr' => "Croatian", 'cs' => "Czech", 'da' => "Danish", 'nl' => "Dutch", 'en-GB' => "English (UK)", 'en-US' => "English (US)", 'et' => "Estonian", 'fil' => "Filipino", 'fi' => "Finnish", 'fr' => "French", 'fr-CA' => "French (Canadian)", 'gl' => "Galician", 'de' => "German", 'el' => "Greek", 'gu' => "Gujarati", 'iw' => "Hebrew", 'hi' => "Hindi", 'hu' => "Hungarian", 'is' => "Icelandic", 'id' => "Indonesian", 'it' => "Italian", 'ja' => "Japanese", 'kn' => "Kannada", 'ko' => "Korean", 'lv' => "Latvian", 'lt' => "Lithuanian", 'ms' => "Malay", 'ml' => "Malayalam", 'mr' => "Marathi", 'no' => "Norwegian", 'fa' => "Persian", 'pl' => "Polish", 'pt-BR' => "Portuguese (Brazil)", 'pt-PT' => "Portuguese (Portugal)", 'ro' => "Romanian", 'ru' => "Russian", 'sr' => "Serbian", 'sk' => "Slovak", 'sl' => "Slovenian", 'es' => "Spanish", 'es-419' => "Spanish (Latin America)", 'sw' => "Swahili", 'sv' => "Swedish", 'ta' => "Tamil", 'te' => "Telugu", 'th' => "Thai", 'tr' => "Turkish", 'uk' => "Ukrainian", 'ur' => "Urdu", 'vi' => "Vietnamese", 'zu' => "Zulu"
			);

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			global $wpmu;

			$bws_license_key = ( isset( $_POST['bws_license_key'] ) ) ? trim( $_POST['bws_license_key'] ) : "";
			$bstwbsftwppdtplgns_options_defaults = array();
			if ( 1 == $wpmu ) {
				if ( !get_site_option( 'bstwbsftwppdtplgns_options' ) )
					add_site_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options_defaults, '', 'yes' );
				$bstwbsftwppdtplgns_options = get_site_option( 'bstwbsftwppdtplgns_options' );
			} else {
				if ( !get_option( 'bstwbsftwppdtplgns_options' ) )
					add_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options_defaults, '', 'yes' );
				$bstwbsftwppdtplgns_options = get_option( 'bstwbsftwppdtplgns_options' );
			}

			if ( isset( $_POST['bws_license_submit'] ) && check_admin_referer( plugin_basename( __FILE__ ), 'bws_license_nonce_name' ) ) {
				if ( '' != $bws_license_key ) { 
					if ( strlen( $bws_license_key ) != 18 ) {
						$error = __( "Wrong license key", 'google_plus_one' );
					} else {
						$bws_license_plugin = trim( $_POST['bws_license_plugin'] );	
						if ( isset( $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] ) && $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] < ( time() + (24 * 60 * 60) ) ) {
							$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] = $bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] + 1;
						} else {
							$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['count'] = 1;
							$bstwbsftwppdtplgns_options['go_pro'][ $bws_license_plugin ]['time'] = time();
						}	

						/* download Pro */
						if ( !function_exists( 'get_plugins' ) )
							require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
						if ( ! function_exists( 'is_plugin_active_for_network' ) )
							require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
						$all_plugins = get_plugins();
						$active_plugins = get_option( 'active_plugins' );
						
						if ( ! array_key_exists( $bws_license_plugin, $all_plugins ) ) {
							$current = get_site_transient( 'update_plugins' );
							if ( is_array( $all_plugins ) && !empty( $all_plugins ) && isset( $current ) && is_array( $current->response ) ) {
								$to_send = array();
								$to_send["plugins"][ $bws_license_plugin ] = array();
								$to_send["plugins"][ $bws_license_plugin ]["bws_license_key"] = $bws_license_key;
								$to_send["plugins"][ $bws_license_plugin ]["bws_illegal_client"] = true;
								$options = array(
									'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
									'body' => array( 'plugins' => serialize( $to_send ) ),
									'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ) );
								$raw_response = wp_remote_post( 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/update-check/1.0/', $options );

								if ( is_wp_error( $raw_response ) || 200 != wp_remote_retrieve_response_code( $raw_response ) ) {
									$error = __( "Something went wrong. Try again later. If the error will appear again, please, contact us <a href=http://support.bestwebsoft.com>BestWebSoft</a>. We are sorry for inconvenience.", 'google_plus_one' );
								} else {
									$response = maybe_unserialize( wp_remote_retrieve_body( $raw_response ) );
									
									if ( is_array( $response ) && !empty( $response ) ) {
										foreach ( $response as $key => $value ) {
											if ( "wrong_license_key" == $value->package ) {
												$error = __( "Wrong license key", 'google_plus_one' ); 
											} elseif ( "wrong_domain" == $value->package ) {
												$error = __( "This license key is bind to another site", 'google_plus_one' );
											} elseif ( "you_are_banned" == $value->package ) {
												$error = __( "Unfortunately, you have exceeded the number of available tries. Please, upload the plugin manually.", 'google_plus_one' );
											}
										}
										if ( '' == $error ) {
											global $wpmu;																					
											$bstwbsftwppdtplgns_options[ $bws_license_plugin ] = $bws_license_key;

											$url = 'http://bestwebsoft.com/wp-content/plugins/paid-products/plugins/downloads/?bws_first_download=' . $bws_license_plugin . '&bws_license_key=' . $bws_license_key . '&download_from=5';
											$uploadDir = wp_upload_dir();
											$zip_name = explode( '/', $bws_license_plugin );
										    if ( file_put_contents( $uploadDir["path"] . "/" . $zip_name[0] . ".zip", file_get_contents( $url ) ) ) {
										    	@chmod( $uploadDir["path"] . "/" . $zip_name[0] . ".zip", octdec( 755 ) );
										    	if ( class_exists( 'ZipArchive' ) ) {
													$zip = new ZipArchive();
													if ( $zip->open( $uploadDir["path"] . "/" . $zip_name[0] . ".zip" ) === TRUE ) {
														$zip->extractTo( WP_PLUGIN_DIR );
														$zip->close();
													} else {
														$error = __( "Failed to open the zip archive. Please, upload the plugin manually", 'google_plus_one' );
													}								
												} elseif ( class_exists( 'Phar' ) ) {
													$phar = new PharData( $uploadDir["path"] . "/" . $zip_name[0] . ".zip" );
													$phar->extractTo( WP_PLUGIN_DIR );
												} else {
													$error = __( "Your server does not support either ZipArchive or Phar. Please, upload the plugin manually", 'google_plus_one' );
												}
												@unlink( $uploadDir["path"] . "/" . $zip_name[0] . ".zip" );										    
											} else {
												$error = __( "Failed to download the zip archive. Please, upload the plugin manually", 'google_plus_one' );
											}

											/* activate Pro */
											if ( file_exists( WP_PLUGIN_DIR . '/' . $zip_name[0] ) ) {			
												array_push( $active_plugins, $bws_license_plugin );
												update_option( 'active_plugins', $active_plugins );
												$pro_plugin_is_activated = true;
											} elseif ( '' == $error ) {
												$error = __( "Failed to download the zip archive. Please, upload the plugin manually", 'google_plus_one' );
											}																				
										}
									} else {
										$error = __( "Something went wrong. Try again later or upload the plugin manually. We are sorry for inconvienience.", 'google_plus_one' ); 
					 				}
					 			}
				 			}
						} else {
							/* activate Pro */
							if ( ! ( in_array( $bws_license_plugin, $active_plugins ) || is_plugin_active_for_network( $bws_license_plugin ) ) ) {			
								array_push( $active_plugins, $bws_license_plugin );
								update_option( 'active_plugins', $active_plugins );
								$pro_plugin_is_activated = true;
							}						
						}
						update_option( 'bstwbsftwppdtplgns_options', $bstwbsftwppdtplgns_options, '', 'yes' );
			 		}
			 	} else {
		 			$error = __( "Please, enter Your license key", 'google_plus_one' );
		 		}
		 	}
		}
		?>
		<!--Google +1 admin page-->
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php echo __( 'Google +1 Settings', 'google_plus_one' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php"><?php _e( 'Settings', 'google_plus_one' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php&amp;action=extra"><?php _e( 'Extra settings', 'google_plus_one' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/plugin/google-plus-one/#faq" target="_blank"><?php _e( 'FAQ', 'google_plus_one' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php&amp;action=go_pro"><?php _e( 'Go PRO', 'google_plus_one' ); ?></a>
			</h2>
			<div class="updated fade" <?php if ( ! ( isset( $_REQUEST['gglplsn_form_submit'] ) || isset( $_REQUEST['bws_license_submit'] ) ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="gglplsn_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'google_plus_one' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'google_plus_one' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { ?>
				<p><?php echo __( 'For the correct work of the button do not use it locally or on a free hosting', 'google_plus_one' ); ?><br /></p>
				<p><?php echo __( 'If you want to insert the button in any place on the site, please use the following code:', 'google_plus_one' ); ?> [bws_googleplusone]</p>
				<form method="post" action="admin.php?page=google-plus-one.php" id="gglplsn_settings_form">
					<table class="form-table gglplsn_form-table">
						<tbody>
							<tr valign="top">
								<th><?php echo __( 'Enable Google +1 Button', 'google_plus_one' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="gglplsn_js"<?php if ( '1' == $gglplsn_options['js'] ) echo 'checked="checked"'; ?> value="1" />
										<span class="gglplsn_info">(<?php echo __( 'Enable or Disable Google+1 JavaScript', 'google_plus_one' ); ?>)</span>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<th><?php echo __( 'Show +1 count in the button', 'google_plus_one' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="gglplsn_annotation" <?php if ( '1' == $gglplsn_options['annotation'] ) echo 'checked="checked"'; ?> value="1" />
										<span class="gglplsn_info">(<?php echo __( 'Display counters showing how many times your article has been liked', 'google_plus_one' ); ?>)</span>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo __( 'Button Size:', 'google_plus_one' ); ?></th>
								<td class="gglplsn_no_padding">
									<select name="gglplsn_size">
										<option value="standart" <?php if ( 'standart' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Standart', 'google_plus_one' ); ?></option>
										<option value="small" <?php if ( 'small' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Small', 'google_plus_one' ); ?></option>
										<option value="medium" <?php if ( 'medium' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Medium', 'google_plus_one' ); ?></option>
										<option value="tall" <?php if ( 'tall' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Tall', 'google_plus_one' ); ?></option>
									</select>
									<span class="gglplsn_info">(<?php echo __( 'Please choose one of four different sizes of buttons', 'google_plus_one' ); ?>)</span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo __( 'Button Position:', 'google_plus_one' ); ?></th>
								<td class="gglplsn_no_padding">
									<select name="gglplsn_position">
										<option value="before_post" <?php if ( 'before_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before Post', 'google_plus_one' ); ?></option>
										<option value="after_post" <?php if ( 'after_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After Post', 'google_plus_one' ); ?></option>
										<option value="afterandbefore" <?php if ( 'afterandbefore' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After Post', 'google_plus_one' ); ?></option>
									</select>
									<span class="gglplsn_info">(<?php echo __( 'Please select location for the button on the page', 'google_plus_one' ); ?>)</span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo __( 'Language:', 'google_plus_one' ); ?></th>
								<td class="gglplsn_no_padding">
									<select name="gglplsn_lang">
										<?php foreach ( $lang_codes as $key => $val ) {
											echo '<option value="' . $key . '"';
											if ( $key == $gglplsn_options['lang'] )
												echo ' selected="selected"';
											echo '>' . esc_html ( $val ) . '</option>';
										} ?>
									</select>
									<span class="gglplsn_info">(<?php echo __( 'Select the language to display information on the button', 'google_plus_one' ); ?>)</span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo __( 'Show button:', 'google_plus_one' ); ?></th>
								<td>
									<p>
										<label>
											<input type="checkbox" name="gglplsn_posts" <?php if ( '1' == $gglplsn_options['posts'] ) echo 'checked="checked"'; ?> value="1" />
											<?php echo __( 'Show in posts', 'google_plus_one' ); ?>
										</label>
									</p>
									<p>
										<label>
											<input type="checkbox" name="gglplsn_pages" <?php if ( '1' == $gglplsn_options['pages'] ) echo 'checked="checked"'; ?>  value="1" />
											<?php echo __( 'Show in pages', 'google_plus_one' ); ?>
										</label>
									</p>
									<p>
										<label>
											<input type="checkbox" name="gglplsn_homepage" <?php if ( '1' == $gglplsn_options['homepage'] ) echo 'checked="checked"'; ?>  value="1" />
											<?php echo __( 'Show on the homepage', 'google_plus_one' ); ?>
										</label>
									</p>
									<p>
										<span class="gglplsn_info">(<?php echo __( 'Please select the page on which you want to see the button', 'google_plus_one' ); ?>)</span>
									</p>
								</td>
							</tr>
						</tbody>
					</table>											
					<input type="hidden" name="gglplsn_form_submit" value="1" />
					<p class="submit">
						<input type="submit" value="<?php _e( 'Save Changes', 'google_plus_one' ); ?>" class="button-primary" />
					</p>
					<?php wp_nonce_field( plugin_basename( __FILE__ ), 'gglplsn_nonce_name' ); ?>
				</form>
				<div class="bws-plugin-reviews">
					<div class="bws-plugin-reviews-rate">
						<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'google_plus_one' ); ?>: 
						<a href="http://wordpress.org/support/view/plugin-reviews/google-one" target="_blank" title="Google +1 reviews"><?php _e( 'Rate the plugin', 'google_plus_one' ); ?></a>
					</div>
					<div class="bws-plugin-reviews-support">
						<?php _e( 'If there is something wrong about it, please contact us', 'google_plus_one' ); ?>: 
						<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
					</div>
				</div>
			<?php } elseif ( 'extra' == $_GET['action'] ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">	
						<div class="bws_table_bg"></div>											
						<table class="form-table bws_pro_version">
							<tr valign="top">
								<td colspan="2">
									<?php _e( 'Please choose the necessary post types (or single pages) where Google +1 button will be displayed:', 'google_plus_one' ); ?>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" id="twttrpr_jstree_url" type="checkbox" name="twttrpr_jstree_url" value="1" />
										<?php _e( "Show URL for pages", 'google_plus_one' );?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<img src="<?php echo plugins_url( 'images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of the site's pages tree", 'google_plus_one' ); ?>" title="<?php _e( "Example of the site's pages tree", 'google_plus_one' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google_plus_one' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<th scope="row" colspan="2">
									* <?php _e( 'If you upgrade to Pro version all your settings will be saved.', 'google_plus_one' ); ?>
								</th>
							</tr>				
						</table>	
					</div>
					<div class="bws_pro_version_tooltip">
						<div class="bws_info">
							<?php _e( 'Unlock premium options by upgrading to a PRO version.', 'google_plus_one' ); ?> 
							<a href="http://bestwebsoft.com/plugin/google-plus-one-pro/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google +1 Pro"><?php _e( 'Learn More', 'google_plus_one' ); ?></a>				
						</div>
						<a class="bws_button" href="http://bestwebsoft.com/plugin/google-plus-one-pro/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>#purchase" target="_blank" title="Google +1 Pro">
							<?php _e( 'Go', 'google_plus_one' ); ?> <strong>PRO</strong>
						</a>	
						<div class="clear"></div>					
					</div>
				</div>
			<?php } elseif ( 'go_pro' == $_GET['action'] ) { ?>
				<?php if ( isset( $pro_plugin_is_activated ) && true === $pro_plugin_is_activated ) { ?>
					<script type="text/javascript">
						window.setTimeout( function() {
						    window.location.href = 'admin.php?page=google-plus-one-pro.php';
						}, 5000 );
					</script>				
					<p><?php _e( "Congratulations! The PRO version of the plugin is successfully download and activated.", 'google_plus_one' ); ?></p>
					<p>
						<?php _e( "Please, go to", 'google_plus_one' ); ?> <a href="admin.php?page=google-plus-one-pro.php"><?php _e( 'the setting page', 'google_plus_one' ); ?></a> 
						(<?php _e( "You will be redirected automatically in 5 seconds.", 'google_plus_one' ); ?>)
					</p>
				<?php } else { ?>
					<form method="post" action="admin.php?page=google-plus-one.php&amp;action=go_pro">
						<p>
							<?php _e( 'You can download and activate', 'google_plus_one' ); ?> 
							<a href="http://bestwebsoft.com/plugin/google-plus-one-pro/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google +1 Pro">PRO</a> 
							<?php _e( 'version of this plugin by entering Your license key.', 'google_plus_one' ); ?><br />
							<span style="color: #888888;font-size: 10px;">
								<?php _e( 'You can find your license key on your personal page Client area, by clicking on the link', 'google_plus_one' ); ?> 
								<a href="http://bestwebsoft.com/wp-login.php">http://bestwebsoft.com/wp-login.php</a> 
								<?php _e( '(your username is the email you specify when purchasing the product).', 'google_plus_one' ); ?>
							</span>
						</p>
						<?php if ( isset( $bstwbsftwppdtplgns_options['go_pro']['google-one-pro/google-plus-one-pro.php']['count'] ) &&
							'5' < $bstwbsftwppdtplgns_options['go_pro']['google-one-pro/google-plus-one-pro.php']['count'] &&
							$bstwbsftwppdtplgns_options['go_pro']['google-one-pro/google-plus-one-pro.php']['time'] < ( time() + ( 24 * 60 * 60 ) ) ) { ?>
							<p>
								<input disabled="disabled" type="text" name="bws_license_key" value="<?php echo $bws_license_key; ?>" />
								<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Go!', 'google_plus_one' ); ?>" />
							</p>
							<p>
								<?php _e( "Unfortunately, you have exceeded the number of available tries per day. Please, upload the plugin manually.", 'google_plus_one' ); ?>
							</p>
						<?php } else { ?>
							<p>
								<input type="text" name="bws_license_key" value="<?php echo $bws_license_key; ?>" />
								<input type="hidden" name="bws_license_plugin" value="google-one-pro/google-plus-one-pro.php" />
								<input type="hidden" name="bws_license_submit" value="submit" />
								<input type="submit" class="button-primary" value="<?php _e( 'Go!', 'google_plus_one' ); ?>" />
								<?php wp_nonce_field( plugin_basename(__FILE__), 'bws_license_nonce_name' ); ?>
							</p>
						<?php } ?>
					</form>
				<?php }
			} ?>
		</div><!-- .wrap -->
	<?php }
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'gglplsn_version_check' ) ) {
	function gglplsn_version_check() {
		global $wp_version, $gglplsn_plugin_info;
		
		if ( ! $gglplsn_plugin_info )
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );

		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $gglplsn_plugin_info['Name'] . " </strong> " . __( 'requires', 'google_plus_one' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'google_plus_one') . "<br /><br />" . __( 'Back to the WordPress', 'google_plus_one') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'google_plus_one') . "</a>." );
			}
		}
	}
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		if ( isset( $_GET['page'] ) && "google-plus-one.php" == $_GET['page'] ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
			wp_enqueue_script( 'gglplsn_script', plugins_url( 'js/script.js', __FILE__ ) );
		}
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_options;
		if ( '1' == $gglplsn_options['js'] ) { ?>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
				<?php if ( 'en-US' != $gglplsn_options['lang'] ) { ?>
					{'lang': '<?php echo $gglplsn_options['lang']; ?>'}
				<?php } ?>
			</script>
		<?php }
	}
}

/* Google +1 button */
if ( ! function_exists( 'gglplsn_button' ) ) {
	function gglplsn_button( $content ) {
		global $gglplsn_options;
		if ( ( is_single() && '1' == $gglplsn_options['posts'] ) || ( is_page() && '1' == $gglplsn_options['pages'] ) || ( ( is_home() || is_front_page() ) && '1' == $gglplsn_options['homepage'] ) ) {
			$content .= '<div class="gglplsn_share"><div class="g-plusone"';
			if ( 'standard' != $gglplsn_options['size'] ) {
				$content .= ' data-size="' . $gglplsn_options['size'] . '"';
			}
			if ( '1' != $gglplsn_options['annotation'] ) {
				$content .= ' data-annotation="none"';
			}
			$content .= ' href="' . get_permalink() . '" data-callback="on"></div></div>';
		}
		return $content;
	}
}

/* Google +1 position on page  */
if ( ! function_exists( 'gglplsn_pos' ) ) {
	function gglplsn_pos( $content ) {
		global $gglplsn_options;
		$button = gglplsn_button( '' );
		if ( "1" == $gglplsn_options['posts'] || '1' == $gglplsn_options['pages'] || '1' == $gglplsn_options['homepage'] ) {
			if ( 'before_post' == $gglplsn_options['position'] ) {
				return $button . $content;
			} else if ( 'after_post' == $gglplsn_options['position'] ) {
				return  $content . $button;
			} else if ( 'afterandbefore' == $gglplsn_options['position'] ){
				return $button . $content . $button;
			}
		} else {
			return $content;
		}
		return $content;
	}
}
		
/* Google +1 shortcode */
/* [bws_googleplusone] */
if ( ! function_exists( 'gglplsn_shortcode' ) ) {
	function gglplsn_shortcode( $atts ){
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
		if ( '1' != $annotation ) {
			$shortbutton .= ' data-annotation="none"';
		}
		$shortbutton .= ' href="' . $url . '" data-callback="on"></div></div>';
		return $shortbutton;
	}
}

/* Add settings link on plugin page */
if ( ! function_exists( 'gglplsn_action_links' ) ) {
	function gglplsn_action_links( $links, $file ) {
		static $this_plugin;
		if ( ! $this_plugin ) 
			$this_plugin = plugin_basename( __FILE__ );
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google_plus_one' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

if ( ! function_exists( 'gglplsn_register_plugin_links' ) ) {
	function gglplsn_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[]	=	'<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google_plus_one' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-one/faq/" target="_blank">' . __( 'FAQ', 'google_plus_one' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google_plus_one' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'gglplsn_plugin_banner' ) ) {
	function gglplsn_plugin_banner() {
		global $hook_suffix, $gglplsn_plugin_info;	
		if ( 'plugins.php' == $hook_suffix ) {   
			$banner_array = array(
				array( 'sndr_hide_banner_on_plugin_page', 'sender/sender.php', '0.5' ),
				array( 'srrl_hide_banner_on_plugin_page', 'user-role/user-role.php', '1.4' ),
				array( 'pdtr_hide_banner_on_plugin_page', 'updater/updater.php', '1.12' ),
				array( 'cntctfrmtdb_hide_banner_on_plugin_page', 'contact-form-to-db/contact_form_to_db.php', '1.2' ),
				array( 'cntctfrmmlt_hide_banner_on_plugin_page', 'contact-form-multi/contact-form-multi.php', '1.0.7' ),
				array( 'gglmps_hide_banner_on_plugin_page', 'bws-google-maps/bws-google-maps.php', '1.2' ),
				array( 'fcbkbttn_hide_banner_on_plugin_page', 'facebook-button-plugin/facebook-button-plugin.php', '2.29' ),
				array( 'twttr_hide_banner_on_plugin_page', 'twitter-plugin/twitter.php', '2.34' ),
				array( 'pdfprnt_hide_banner_on_plugin_page', 'pdf-print/pdf-print.php', '1.7.1' ),
				array( 'gglplsn_hide_banner_on_plugin_page', 'google-one/google-plus-one.php', '1.1.4' ),
				array( 'gglstmp_hide_banner_on_plugin_page', 'google-sitemap-plugin/google-sitemap-plugin.php', '2.8.4' ),
				array( 'cntctfrmpr_for_ctfrmtdb_hide_banner_on_plugin_page', 'contact-form-pro/contact_form_pro.php', '1.14' ),
				array( 'cntctfrm_for_ctfrmtdb_hide_banner_on_plugin_page', 'contact-form-plugin/contact_form.php', '3.62' ),
				array( 'cntctfrm_hide_banner_on_plugin_page', 'contact-form-plugin/contact_form.php', '3.47' ),
				array( 'cptch_hide_banner_on_plugin_page', 'captcha/captcha.php', '3.8.4' ),
				array( 'gllr_hide_banner_on_plugin_page', 'gallery-plugin/gallery-plugin.php', '3.9.1' )
			);
			if ( ! $gglplsn_plugin_info )
				$gglplsn_plugin_info = get_plugin_data( __FILE__ );

			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
			$active_plugins = get_option( 'active_plugins' );			
			$all_plugins = get_plugins();
			$this_banner = 'gglplsn_hide_banner_on_plugin_page';
			foreach ( $banner_array as $key => $value ) {
				if ( $this_banner == $value[0] ) {
					global $wp_version, $bstwbsftwppdtplgns_cookie_add;
		       		if ( ! isset( $bstwbsftwppdtplgns_cookie_add ) ) {
						echo '<script type="text/javascript" src="' . plugins_url( 'js/c_o_o_k_i_e.js', __FILE__ ) . '"></script>';
						$bstwbsftwppdtplgns_cookie_add = true;
					} ?>
					<script type="text/javascript">		
						(function($) {
							$(document).ready( function() {		
								var hide_message = $.cookie( "gglplsn_hide_banner_on_plugin_page" );
								if ( hide_message == "true") {
									$( ".gglplsn_message" ).css( "display", "none" );
								} else {
									$( ".gglplsn_message" ).css( "display", "block" );
								};
								$( ".gglplsn_close_icon" ).click( function() {
									$( ".gglplsn_message" ).css( "display", "none" );
									$.cookie( "gglplsn_hide_banner_on_plugin_page", "true", { expires: 32 } );
								});	
							});
						})(jQuery);				
					</script>
					<div class="updated" style="padding: 0; margin: 0; border: none; background: none;">					                      
						<div class="gglplsn_message bws_banner_on_plugin_page" style="display: none;">
							<img class="gglplsn_close_icon close_icon" title="" src="<?php echo plugins_url( 'images/close_banner.png', __FILE__ ); ?>" alt=""/>
							<div class="button_div">
								<a class="button" target="_blank" href="http://bestwebsoft.com/plugin/google-one-pro/?k=ca01bbe0edd696fddb27769001fe8084&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>"><?php _e( "Learn More", 'google_plus_one' ); ?></a>				
							</div>
							<div class="text">
								<?php _e( "It's time to upgrade your <strong>Google +1</strong> to <strong>PRO</strong> version", 'google_plus_one' ); ?>!<br />
								<span><?php _e( 'Extend standard plugin functionality with new great options', 'google_plus_one' ); ?>.</span>
							</div> 					
							<div class="icon">
								<img title="" src="<?php echo plugins_url( 'images/banner.png', __FILE__ ); ?>" alt=""/>	
							</div>
						</div>  
					</div>
					<?php break;
				}
				if ( isset( $all_plugins[ $value[1] ] ) && $all_plugins[ $value[1] ]["Version"] >= $value[2] && ( 0 < count( preg_grep( '/' . str_replace( '/', '\/', $value[1] ) . '/', $active_plugins ) ) || is_plugin_active_for_network( $value[1] ) ) && ! isset( $_COOKIE[ $value[0] ] ) ) {
					break;
				}
			}    
		}
	}
}

if ( ! function_exists( 'gglplsn_uninstall' ) ) {
	function gglplsn_uninstall() {
		delete_option( 'gglplsn_options' );
		delete_site_option( 'gglplsn_options' );
	}
}
/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'gglplsn_admin_menu' );
/* Initialization */
add_action( 'init', 'gglplsn_init' );
add_action( 'admin_init', 'gglplsn_admin_init' );
/* Adding stylesheets */
add_action( 'wp_head', 'gglplsn_js' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
/* Adding plugin buttons */
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'gglplsn_pos' );
/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'gglplsn_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglplsn_register_plugin_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'gglplsn_plugin_banner' );
/* Plugin uninstall function */
register_uninstall_hook( __FILE__, 'gglplsn_uninstall' );
?>
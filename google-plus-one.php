<?php
/*##
Plugin Name: Google +1 by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Add Google +1 button to your WordPress website.
Author: BestWebSoft
Version: 1.2.1
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
		add_submenu_page( 'bws_plugins', __( 'Google +1 Settings', 'google_plus_one' ), 'Google +1', 'manage_options', "google-plus-one.php", 'gglplsn_options' );
	}
}
/* end gglplsn_admin_menu ##*/

if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		global $gglplsn_plugin_info;
		/* Internationalization */
		load_plugin_textdomain( 'google_plus_one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( empty( $gglplsn_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglplsn_plugin_info = get_plugin_data( __FILE__ );
		}

		/*## add general functions */
		require_once( dirname( __FILE__ ) . '/bws_menu/bws_functions.php' );
		
		bws_wp_version_check( plugin_basename( __FILE__ ), $gglplsn_plugin_info, "3.0" ); /* check compatible with current WP version ##*/

		/* Get/Register and check settings for plugin */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) )
			gglplsn_settings();
	}
}

/*## Function for admin_init */
if ( ! function_exists( 'gglplsn_admin_init' ) ) {
	function gglplsn_admin_init() {
		global $bws_plugin_info, $gglplsn_plugin_info;

		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '102', 'version' => $gglplsn_plugin_info["Version"] );
	}
}
/* end gglplsn_admin_init ##*/

if ( ! function_exists ( 'gglplsn_settings' ) ) {
	function gglplsn_settings() {
		global $gglplsn_options, $gglplsn_plugin_info;

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

		if ( ! get_option( 'gglplsn_options' ) )
			add_option( 'gglplsn_options', $gglplsn_option_defaults );

		$gglplsn_options = get_option( 'gglplsn_options' );

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
		$plugin_basename = plugin_basename( __FILE__ );

		/* Save data for settings page */
		if ( isset( $_REQUEST['gglplsn_form_submit'] ) && check_admin_referer( $plugin_basename, 'gglplsn_nonce_name' ) ) {
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

		/*## GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
		} /* end GO PRO ##*/ ?>
		<!-- general -->
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php _e( 'Google +1 Settings', 'google_plus_one' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php"><?php _e( 'Settings', 'google_plus_one' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php&amp;action=extra"><?php _e( 'Extra settings', 'google_plus_one' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/google-plus-one/faq/" target="_blank"><?php _e( 'FAQ', 'google_plus_one' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-plus-one.php&amp;action=go_pro"><?php _e( 'Go PRO', 'google_plus_one' ); ?></a>
			</h2>
			<!-- end general -->
			<div class="updated fade" <?php if ( ! ( isset( $_REQUEST['gglplsn_form_submit'] ) || isset( $_REQUEST['bws_license_submit'] ) ) || "" != $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div id="gglplsn_settings_notice" class="updated fade bws_settings_form_notice" style="display:none"><p><strong><?php _e( "Notice:", 'google_plus_one' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'google_plus_one' ); ?></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php /*## check action */ if ( ! isset( $_GET['action'] ) ) { /* check action ##*/ ?>
				<p><?php _e( 'For the correct work of the button do not use it locally or on a free hosting', 'google_plus_one' ); ?><br /></p>
				<p><?php _e( 'If you want to insert the button in any place on the site, please use the following code:', 'google_plus_one' ); ?> [bws_googleplusone]</p>
				<form method="post" action="" id="gglplsn_settings_form" class="bws_settings_form">
					<table class="form-table gglplsn_form-table">
						<tbody>
							<tr valign="top">
								<th><?php _e( 'Enable Google +1 Button', 'google_plus_one' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="gglplsn_js"<?php if ( '1' == $gglplsn_options['js'] ) echo 'checked="checked"'; ?> value="1" />
										<span class="gglplsn_info">(<?php _e( 'Enable or Disable Google+1 JavaScript', 'google_plus_one' ); ?>)</span>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<th><?php _e( 'Show +1 count in the button', 'google_plus_one' ); ?></th>
								<td>
									<label>
										<input type="checkbox" name="gglplsn_annotation" <?php if ( '1' == $gglplsn_options['annotation'] ) echo 'checked="checked"'; ?> value="1" />
										<span class="gglplsn_info">(<?php _e( 'Display counters showing how many times your article has been liked', 'google_plus_one' ); ?>)</span>
									</label>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Button Size', 'google_plus_one' ); ?></th>
								<td class="gglplsn_no_padding">
									<select name="gglplsn_size">
										<option value="standart" <?php if ( 'standart' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Standart', 'google_plus_one' ); ?></option>
										<option value="small" <?php if ( 'small' == $gglplsn_options['size'] ) echo 'selected="selected"';?>> <?php _e( 'Small', 'google_plus_one' ); ?></option>
										<option value="medium" <?php if ( 'medium' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Medium', 'google_plus_one' ); ?></option>
										<option value="tall" <?php if ( 'tall' == $gglplsn_options['size'] ) echo 'selected="selected"';?>><?php _e( 'Tall', 'google_plus_one' ); ?></option>
									</select>
									<span class="gglplsn_info">(<?php _e( 'Please choose one of four different sizes of buttons', 'google_plus_one' ); ?>)</span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Button Position', 'google_plus_one' ); ?></th>
								<td class="gglplsn_no_padding">
									<select name="gglplsn_position">
										<option value="before_post" <?php if ( 'before_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before', 'google_plus_one' ); ?></option>
										<option value="after_post" <?php if ( 'after_post' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'After', 'google_plus_one' ); ?></option>
										<option value="afterandbefore" <?php if ( 'afterandbefore' == $gglplsn_options['position'] ) echo 'selected="selected"';?>><?php _e( 'Before And After', 'google_plus_one' ); ?></option>
									</select>
									<span class="gglplsn_info">(<?php _e( 'Please select location for the button on the page', 'google_plus_one' ); ?>)</span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Language', 'google_plus_one' ); ?></th>
								<td class="gglplsn_no_padding">
									<select name="gglplsn_lang">
										<?php foreach ( $lang_codes as $key => $val ) {
											echo '<option value="' . $key . '"';
											if ( $key == $gglplsn_options['lang'] )
												echo ' selected="selected"';
											echo '>' . esc_html ( $val ) . '</option>';
										} ?>
									</select>
									<span class="gglplsn_info">(<?php _e( 'Select the language to display information on the button', 'google_plus_one' ); ?>)</span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php _e( 'Show button', 'google_plus_one' ); ?></th>
								<td>
									<p>
										<label>
											<input type="checkbox" name="gglplsn_posts" <?php if ( '1' == $gglplsn_options['posts'] ) echo 'checked="checked"'; ?> value="1" />
											<?php _e( 'Show in posts', 'google_plus_one' ); ?>
										</label>
									</p>
									<p>
										<label>
											<input type="checkbox" name="gglplsn_pages" <?php if ( '1' == $gglplsn_options['pages'] ) echo 'checked="checked"'; ?>  value="1" />
											<?php _e( 'Show in pages', 'google_plus_one' ); ?>
										</label>
									</p>
									<p>
										<label>
											<input type="checkbox" name="gglplsn_homepage" <?php if ( '1' == $gglplsn_options['homepage'] ) echo 'checked="checked"'; ?>  value="1" />
											<?php _e( 'Show on the homepage', 'google_plus_one' ); ?>
										</label>
									</p>
									<p>
										<span class="gglplsn_info">(<?php _e( 'Please select the page on which you want to see the button', 'google_plus_one' ); ?>)</span>
									</p>
								</td>
							</tr>
						</tbody>
					</table>
					<input type="hidden" name="gglplsn_form_submit" value="1" />
					<p class="submit">
						<input type="submit" value="<?php _e( 'Save Changes', 'google_plus_one' ); ?>" class="button-primary" />
					</p>
					<?php wp_nonce_field( $plugin_basename, 'gglplsn_nonce_name' ); ?>
				</form>
				<!-- general -->
				<?php bws_plugin_reviews_block( $gglplsn_plugin_info['Name'], 'google-one' ); ?>
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
							<a href="http://bestwebsoft.com/products/google-plus-one/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google +1 Pro"><?php _e( 'Learn More', 'google_plus_one' ); ?></a>
						</div>
						<a class="bws_button" href="http://bestwebsoft.com/products/google-plus-one/buy/?k=0a5a8a70ed3c34b95587de0604ca9517&pn=102&v=<?php echo $gglplsn_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google +1 Pro">
							<?php _e( 'Go', 'google_plus_one' ); ?> <strong>PRO</strong>
						</a>
						<div class="clear"></div>
					</div>
				</div>
			<?php } elseif ( 'go_pro' == $_GET['action'] ) { 
				bws_go_pro_tab( $gglplsn_plugin_info, $plugin_basename, 'google-plus-one.php', 'google-plus-one-pro.php', 'google-one-pro/google-plus-one-pro.php', 'google-plus-one', '0a5a8a70ed3c34b95587de0604ca9517', '102', isset( $go_pro_result['pro_plugin_is_activated'] ) ); 
			} ?>	
		</div>
		<!-- end general -->			
	<?php }
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		if ( isset( $_GET['page'] ) && ( "google-plus-one.php" == $_GET['page'] || "social-buttons.php" == $_GET['page'] ) ) {
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
			if ( isset( $_GET['page'] ) && "google-plus-one.php" == $_GET['page'] )
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
			$content .= ' data-href="' . get_permalink() . '" data-callback="on"></div></div>';
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
		if ( '1' != $annotation ) {
			$shortbutton .= ' data-annotation="none"';
		}
		$shortbutton .= ' data-href="' . $url . '" data-callback="on"></div></div>';
		return $shortbutton;
	}
}

/*## Functions creates other links on plugins page. */
if ( ! function_exists( 'gglplsn_action_links' ) ) {
	function gglplsn_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google_plus_one' ) . '</a>';
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
				$links[]	=	'<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google_plus_one' ) . '</a>';
			$links[]	=	'<a href="http://wordpress.org/plugins/google-one/faq/" target="_blank">' . __( 'FAQ', 'google_plus_one' ) . '</a>';
			$links[]	=	'<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google_plus_one' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'gglplsn_plugin_banner' ) ) {
	function gglplsn_plugin_banner() {
		global $hook_suffix;
		if ( 'plugins.php' == $hook_suffix ) {
			global $gglplsn_plugin_info;
			bws_plugin_banner( $gglplsn_plugin_info, 'gglplsn', 'google-plus-one', 'ca01bbe0edd696fddb27769001fe8084', '102', 'http://ps.w.org/google-one/assets/icon-128x128.png' );
		}
	}
}

if ( ! function_exists( 'gglplsn_uninstall' ) ) {
	function gglplsn_uninstall() {
		if ( ! function_exists( 'get_plugins' ) )
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$all_plugins = get_plugins();
		if ( ! array_key_exists( 'bws-social-buttons/bws-social-buttons.php', $all_plugins ) )
			delete_option( 'gglplsn_options' );
	}
}
/* Adding 'BWS Plugins' admin menu */
add_action( 'admin_menu', 'gglplsn_admin_menu' );
/* Initialization ##*/
add_action( 'init', 'gglplsn_init' );
/*## admin_init */
add_action( 'admin_init', 'gglplsn_admin_init' );
/* Adding stylesheets ##*/
add_action( 'wp_head', 'gglplsn_js' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
/* Adding plugin buttons */
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'gglplsn_pos' );
/*## Additional links on the plugin page */
add_filter( 'plugin_action_links', 'gglplsn_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglplsn_register_plugin_links', 10, 2 );
/* Adding banner */
add_action( 'admin_notices', 'gglplsn_plugin_banner' );
/* Plugin uninstall function */
register_uninstall_hook( __FILE__, 'gglplsn_uninstall' );
/* end ##*/
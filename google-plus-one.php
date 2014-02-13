<?php
/*
Plugin Name: Google +1
Plugin URI:  http://bestwebsoft.com/plugin/
Description: Add Google +1 button to your WordPress website.
Author: BestWebSoft
Version: 1.1.3
Author URI: http://bestwebsoft.com
License: GPLv2 or later
*/

/*	
	@ Copyright 2014  BestWebSoft  ( http://support.bestwebsoft.com )
	
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

require_once( dirname( __FILE__ ) . '/bws_menu/bws_menu.php' );

if ( ! function_exists( 'gglplsn_admin_menu' ) ) {
	function gglplsn_admin_menu() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', plugins_url( 'images/px.png', __FILE__ ), 1001 );
		add_submenu_page( 'bws_plugins', __( 'Google +1 Settings', 'google_plus_one' ), __( 'Google +1', 'google_plus_one' ), 'manage_options', "google-plus-one.php", 'gglplsn_options' );
	}
}

if ( ! function_exists ( 'gglplsn_default_options' ) ) {
	function gglplsn_default_options() {
		global $wpmu, $gglplsn_options, $bws_plugin_info;

		if ( function_exists( 'get_plugin_data' ) && ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) ) ) {
			$plugin_info = get_plugin_data( __FILE__ );	
			$bws_plugin_info = array( 'id' => '102', 'version' => $plugin_info["Version"] );
		}

		/* Default options */
		$gglplsn_option_defaults	=	array(
			'js'			=>	'1',
			'annotation'	=>	'0',
			'size'			=>	'standart',
			'position'		=>	'before_post',
			'lang'			=>	'en-GB',
			'posts'			=>	'1',
			'pages'			=>	'1',
			'homepage'		=>	'1'
		);
		if ( 1 == $wpmu ) {
			if ( ! get_site_option( 'gglplsn_options' ) ) {
				add_site_option( 'gglplsn_options', $gglplsn_option_defaults, '', 'yes' );
				$gglplsn_options = get_site_option( 'gglplsn_options' );
			} else {
				$gglplsn_options = get_site_option( 'gglplsn_options' );
				foreach ( $gglplsn_option_defaults as $key => $value) {
					if ( isset( $gglplsn_options['gglplsn_' . $key ] ) ) {
						$gglplsn_options[$key] = $gglplsn_options['gglplsn_' . $key ];
						unset( $gglplsn_options['gglplsn_' . $key ] );
					}
				}
			}
		} else {
			if ( ! get_option( 'gglplsn_options' ) ) {
				add_option( 'gglplsn_options', $gglplsn_option_defaults, '', 'yes' );
				$gglplsn_options = get_option( 'gglplsn_options' );
			} else {
				$gglplsn_options = get_option( 'gglplsn_options' );
				foreach ( $gglplsn_option_defaults as $key => $value) {
					if ( isset( $gglplsn_options['gglplsn_' . $key ] ) ) {
						$gglplsn_options[$key] = $gglplsn_options['gglplsn_' . $key ];
						unset( $gglplsn_options['gglplsn_' . $key ] );
					}
				}
			}
		}
		if ( is_array($gglplsn_options ) )
			$gglplsn_options = array_merge( $gglplsn_option_defaults, $gglplsn_options );
		else
			$gglplsn_options = $gglplsn_option_defaults;
		update_option( 'gglplsn_options', $gglplsn_options );
	}
}

/* Add settings page in admin area */
if ( ! function_exists( 'gglplsn_options' ) ) {
	function gglplsn_options() {
		global $gglplsn_options;
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
		} ?>
		<!--Google +1 admin page-->
		<div class="wrap">
			<form method="post" action="admin.php?page=google-plus-one.php" id="gglplsn_settings_form">
				<div class="icon32 icon32-bws" id="icon-options-general"></div>
				<h2><?php echo __( 'Google +1 Settings', 'google_plus_one' ); ?></h2>
				<div class="updated fade" <?php if ( ! isset( $_REQUEST['gglplsn_form_submit'] ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
				<div id="gglplsn_settings_notice" class="updated fade" style="display:none"><p><strong><?php _e( "Notice:", 'google_plus_one' ); ?></strong> <?php _e( "The plugin's settings have been changed. In order to save them please don't forget to click the 'Save Changes' button.", 'google_plus_one' ); ?></p></div>
				<p>
					<?php echo __( 'For the correct work of the button do not use it locally or on a free hosting', 'google_plus_one' ); ?><br />
				</p>
				<p>
					<?php echo __( 'If you want to insert the button in any place on the site, please use the following code:', 'google_plus_one' ); ?> [bws_googleplusone]
				</p>
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
									<option value="af" <?php if ( 'af' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Afrikaans</option>
									<option value="am" <?php if ( 'am' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Amharic</option>
									<option value="ar" <?php if ( 'ar' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Arabic</option>												
									<option value="eu" <?php if ( 'eu' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Basque</option>
									<option value="bn" <?php if ( 'bn' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Bengali</option>				
									<option value="bg" <?php if ( 'bg' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Bulgarian</option>								
									<option value="ca" <?php if ( 'ca' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Catalan</option>
									<option value="zh-HK" <?php if ( 'zh-HK' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Chinese (Hong Kong)</option>	
									<option value="zh-CN" <?php if ( 'zn-CH' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Chinese (Simplified)</option>
									<option value="zh-TW" <?php if ( 'zh-TW' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Chinese (Traditional)</option>
									<option value="hr" <?php if ( 'hr' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Croatian</option>
									<option value="cs" <?php if ( 'cs' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Czech</option>
									<option value="da" <?php if ( 'da' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Danish</option>
									<option value="nl" <?php if ( 'nl' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Dutch</option>
									<option value="en-GB" <?php if ( 'en-GB' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>English (UK)</option>
									<option value="en-US" <?php if ( 'en-US' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>English (US)</option>
									<option value="et" <?php if ( 'et' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Estonian</option>
									<option value="fil" <?php if ( 'fil' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Filipino</option>
									<option value="fi" <?php if ( 'fi' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Finnish</option>
									<option value="fr" <?php if ( 'fr' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>French</option>
									<option value="fr-CA" <?php if ( 'fr-CA' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>French (Canadian)</option>		
									<option value="gl" <?php if ( 'gl' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Galician</option>
									<option value="de" <?php if ( 'de' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>German</option>
									<option value="el" <?php if ( 'el' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Greek</option>
									<option value="gu" <?php if ( 'gu' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Gujarati</option>
									<option value="iw" <?php if ( 'iw' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Hebrew</option>
									<option value="hi" <?php if ( 'hi' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Hindi</option>
									<option value="hu" <?php if ( 'hu' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Hungarian</option>
									<option value="is" <?php if ( 'is' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Icelandic</option>
									<option value="id" <?php if ( 'id' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Indonesian</option>
									<option value="it" <?php if ( 'it' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Italian</option>
									<option value="ja" <?php if ( 'ja' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Japanese</option>
									<option value="kn" <?php if ( 'kn' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Kannada</option>
									<option value="ko" <?php if ( 'ko' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Korean</option>
									<option value="lv" <?php if ( 'lv' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Latvian</option>
									<option value="lt" <?php if ( 'lt' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Lithuanian</option>
									<option value="ms" <?php if ( 'ms' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Malay</option>
									<option value="ml" <?php if ( 'ml' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Malayalam</option>
									<option value="mr" <?php if ( 'mr' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Marathi</option>
									<option value="no" <?php if ( 'no' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Norwegian</option>
									<option value="fa" <?php if ( 'fa' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Persian</option>
									<option value="pl" <?php if ( 'pl' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Polish</option>
									<option value="pt-BR" <?php if ( 'pt-BR' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Portuguese (Brazil)</option>
									<option value="pt-PT" <?php if ( 'pt-PT' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Portuguese (Portugal)</option>
									<option value="ro" <?php if ( 'ro' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Romanian</option>
									<option value="ru" <?php if ( 'ru' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Russian</option>	
									<option value="sr" <?php if ( 'sr' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Serbian</option>
									<option value="sk" <?php if ( 'sk' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Slovak</option>
									<option value="sl" <?php if ( 'sl' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Slovenian</option>
									<option value="es" <?php if ( 'es' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Spanish</option>
									<option value="es-419" <?php if ( 'es-419' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Spanish (Latin America)</option>
									<option value="sw" <?php if ( 'sw' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Swahili</option>
									<option value="sv" <?php if ( 'sv' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Swedish</option>
									<option value="ta" <?php if ( 'ta' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Tamil</option>
									<option value="te" <?php if ( 'te' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Telugu</option>
									<option value="th" <?php if ( 'th' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Thai</option>
									<option value="tr" <?php if ( 'tr' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Turkish</option>
									<option value="uk" <?php if ( 'uk' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Ukrainian</option>
									<option value="ur" <?php if ( 'ur' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Urdu</option>
									<option value="vi" <?php if ( 'vi' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Vietnamese</option>
									<option value="zu" <?php if ( 'zu' == $gglplsn_options['lang'] ) { echo 'selected="selected"'; } ?>>Zulu</option>
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
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="gglplsn_form_submit" value="1" />
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Changes', 'google_plus_one' ); ?>" class="button-primary" />
				</p>
				<?php wp_nonce_field( plugin_basename( __FILE__ ), 'gglplsn_nonce_name' ); ?>
			</form>
			<br/>		
			<div class="bws-plugin-reviews">
				<div class="bws-plugin-reviews-rate">
				<?php _e( 'If you enjoy our plugin, please give it 5 stars on WordPress', 'google_plus_one' ); ?>: 
				<a href="http://wordpress.org/support/view/plugin-reviews/google-one" target="_blank" title="Google +1 reviews"><?php _e( 'Rate the plugin', 'google_plus_one' ); ?></a><br/>
				</div>
				<div class="bws-plugin-reviews-support">
				<?php _e( 'If there is something wrong about it, please contact us', 'google_plus_one' ); ?>: 
				<a href="http://support.bestwebsoft.com">http://support.bestwebsoft.com</a>
				</div>
			</div>
		</div><!-- .wrap -->
	<?php }
}


if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		/* Internationalization */
		load_plugin_textdomain( 'google_plus_one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Function check if plugin is compatible with current WP version  */
if ( ! function_exists ( 'gglplsn_version_check' ) ) {
	function gglplsn_version_check() {
		global $wp_version;
		$plugin_data	=	get_plugin_data( __FILE__, false );
		$require_wp		=	"3.0"; /* Wordpress at least requires version */
		$plugin			=	plugin_basename( __FILE__ );
	 	if ( version_compare( $wp_version, $require_wp, "<" ) ) {
			if ( is_plugin_active( $plugin ) ) {
				deactivate_plugins( $plugin );
				wp_die( "<strong>" . $plugin_data['Name'] . " </strong> " . __( 'requires', 'google_plus_one' ) . " <strong>WordPress " . $require_wp . "</strong> " . __( 'or higher, that is why it has been deactivated! Please upgrade WordPress and try again.', 'google_plus_one') . "<br /><br />" . __( 'Back to the WordPress', 'google_plus_one') . " <a href='" . get_admin_url( null, 'plugins.php' ) . "'>" . __( 'Plugins page', 'google_plus_one') . "</a>." );
			}
		}
	}
}

if ( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		/* Style for admin page */
		global $wp_version;
		if ( $wp_version < 3.8 )
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style_wp_before_3.8.css', __FILE__ ) );	
		else
			wp_enqueue_style( 'gglplsn_style', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/* Add google +1 button javascript */
if ( ! function_exists( 'gglplsn_link' ) ) {
	function gglplsn_link( $links ) {
		array_unshift( $links );
		return $links;
	}
}

if ( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_options;
		if ( '1' == $gglplsn_options['js'] ) { ?>
			<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
				<?php if ( 'en-US' == $gglplsn_options['lang'] ) { ?>
					{'lang': '<?php echo $gglplsn_options['lang']; ?>'}
				<?php } ?>
			</script>
		<?php }
	}
}

if ( ! function_exists('gglplsn_admin_js') ) {
	function gglplsn_admin_js() {
		if ( isset( $_GET['page'] ) && "google-plus-one.php" == $_GET['page'] ) {
			/* add notice about changing in the settings page */
			?>
			<script type="text/javascript">
				(function($) {
					$(document).ready( function() {
						$( '#gglplsn_settings_form input' ).bind( "change click select", function() {
							if ( $( this ).attr( 'type' ) != 'submit' ) {
								$( '.updated.fade' ).css( 'display', 'none' );
								$( '#gglplsn_settings_notice' ).css( 'display', 'block' );
							};
						});
						$( '#gglplsn_settings_form select' ).bind( "change", function() {
								$( '.updated.fade' ).css( 'display', 'none' );
								$( '#gglplsn_settings_notice' ).css( 'display', 'block' );
						});
					});
				})(jQuery);
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

if( ! function_exists( 'gglplsn_register_plugin_links' ) ) {
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
if( ! function_exists( 'gglplsn_uninstall' ) ) {
	function gglplsn_uninstall() {
		delete_option( 'gglplsn_options' );
		delete_site_option( 'gglplsn_options' );
	}
}

add_action( 'admin_menu', 'gglplsn_admin_menu' );
add_action( 'init', 'gglplsn_init' );
add_action( 'init', 'gglplsn_default_options' );
add_action( 'admin_init', 'gglplsn_default_options' );
add_action( 'admin_init', 'gglplsn_version_check' );
/* Adds "Settings" link to the plugin action page */
add_action( 'wp_head', 'gglplsn_js' );
add_action( 'admin_head', 'gglplsn_admin_js' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );

add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );

add_filter( 'plugin_action_links', 'gglplsn_action_links', 10, 2 );
add_filter( 'the_content', 'gglplsn_pos' );
/* Additional links on the plugin page */
add_filter( 'plugin_row_meta', 'gglplsn_register_plugin_links', 10, 2 );
add_filter( 'widget_text', 'do_shortcode' );

register_uninstall_hook( __FILE__, 'gglplsn_uninstall' );
?>
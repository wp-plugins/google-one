<?php
/*
Plugin Name: Google +1
Plugin URI:  http://bestwebsoft.com/plugin/
Description: Add Google +1 button to your WordPress website.
Author: BestWebSoft
Version: 1.1.0
Author URI: http://bestwebsoft.com
License: GPLv2 or later
*/

/*	@ Copyright 2011  BestWebSoft  ( http://support.bestwebsoft.com )
	
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

if( ! function_exists( 'gglplsn_admin_menu' ) ) {
	function gglplsn_admin_menu() {
		add_menu_page( 'BWS Plugins', 'BWS Plugins', 'manage_options', 'bws_plugins', 'bws_add_menu_render', WP_CONTENT_URL . "/plugins/google-plus-one/images/px.png", 1001 );
		add_submenu_page( 'bws_plugins', __( 'Google +1 Settings', 'google_plus_one' ), __( 'Google +1', 'google_plus_one' ), 'manage_options', "google-plus-one.php", 'gglplsn_options' );
	}
}

if( ! function_exists( 'gglplsn_admin_head' ) ) {
	function gglplsn_admin_head() {
		// Style for admin page
		wp_enqueue_style( 'gglplsnStylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		if ( isset( $_GET['page'] ) && $_GET['page'] == "bws_plugins" )
			wp_enqueue_script( 'bwsMenuscript', plugins_url( 'js/bws_menu.js', __FILE__ ) );
	}
}

if ( ! function_exists ( 'gglplsn_default_options' ) ) {
	function gglplsn_default_options() {
		global $gglplsn_options;
		// Default options
		$gglplsn_option_defaults	=	array(
			'gglplsn_js'			=>	'1',
			'gglplsn_annotation'	=>	'0',
			'gglplsn_size'			=>	'standart',
			'gglplsn_position'		=>	'before_post',
			'gglplsn_lang'			=>	'en-GB',
			'gglplsn_posts'			=>	'1',
			'gglplsn_pages'			=>	'1',
			'gglplsn_homepage'		=>	'1'
		);
		if( ! get_option( 'gglplsn_options' ) )
			add_option( 'gglplsn_options', $gglplsn_option_defaults, '', 'yes' );
		$gglplsn_options = get_option( 'gglplsn_options' );
		$gglplsn_options = array_merge( $gglplsn_option_defaults, $gglplsn_options );
		update_option( 'gglplsn_options', $gglplsn_options );
	}
}

if ( ! function_exists ( 'gglplsn_init' ) ) {
	function gglplsn_init() {
		// Internationalization
		load_plugin_textdomain( 'google_plus_one', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		// Other init stuff, be sure to it after load_plugins_textdomain if it involves translated text(!)
		load_plugin_textdomain( 'bestwebsoft', false, dirname( plugin_basename( __FILE__ ) ) . '/bws_menu/languages/' );
	}
}

// Add settings page in admin area
if( ! function_exists( 'gglplsn_options' ) ) {
	function gglplsn_options() {
		global $gglplsn_options;
		// Save data for settings page
		if( isset( $_REQUEST['gglplsn_form_submit'] ) ) {
			$gglplsn_options_submit['gglplsn_js']			=	isset( $_REQUEST['gglplsn_js'] ) ? 1 : 0 ;
			$gglplsn_options_submit['gglplsn_annotation']	=	isset( $_REQUEST['gglplsn_annotation'] ) ? 1 : 0 ;
			$gglplsn_options_submit['gglplsn_size']			=	$_REQUEST['gglplsn_size'];
			$gglplsn_options_submit['gglplsn_position']		=	$_REQUEST['gglplsn_position'];
			$gglplsn_options_submit['gglplsn_lang']			=	$_REQUEST['gglplsn_lang'];
			$gglplsn_options_submit['gglplsn_posts']		=	isset( $_REQUEST['gglplsn_posts'] ) ? 1 : 0 ;
			$gglplsn_options_submit['gglplsn_pages']		=	isset( $_REQUEST['gglplsn_pages'] ) ? 1 : 0 ;
			$gglplsn_options_submit['gglplsn_homepage']		=	isset( $_REQUEST['gglplsn_homepage'] ) ? 1 : 0 ;
			$gglplsn_options								=	array_merge( $gglplsn_options, $gglplsn_options_submit  );
			$message										=	__( 'Settings saved', 'google_plus_one' );
			update_option( 'gglplsn_options', $gglplsn_options );
		} ?>
		<!--Google +1 admin page-->
		<div class="wrap">
			<form method="post" action="admin.php?page=google-plus-one.php" id="main">
				<div class="icon32 icon32-bws" id="icon-options-general"></div>
				<h2><?php echo __( 'Google +1 Settings', 'google_plus_one' ); ?></h2>
				<div class="updated fade" <?php if( empty( $message ) ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
				<p>
					<?php echo __( 'For the correct work of the button do not use it locally or on a free hosting', 'google_plus_one' ); ?><br />
				</p>
				<p>
					<?php echo __( 'If you want to insert the button in any place on the site, please use the following code:', 'google_plus_one' ); ?> [bws_googleplusone]
				</p>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th><?php echo __( 'Enable Google +1 Button', 'google_plus_one' ); ?></th>
							<td>
								<input type="checkbox" name="gglplsn_js"<?php if ( $gglplsn_options['gglplsn_js'] == '1') echo 'checked="checked"'; ?> value="1" />
								<span class="gglplsn_info">(<?php echo __( 'Enable or Disable Google+1 JavaScript', 'google_plus_one' ); ?>)</span>
							</td>
						</tr>
						<tr valign="top">
							<th><?php echo __( 'Show +1 count in the button', 'google_plus_one' ); ?></th>
							<td>
								<input type="checkbox" name="gglplsn_annotation" <?php if ( '1' == $gglplsn_options['gglplsn_annotation'] ) echo 'checked="checked"'; ?> value="1" />
								<span class="gglplsn_info">(<?php echo __( 'Display counters showing how many times your article has been liked', 'google_plus_one' ); ?>)</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Button Size:', 'google_plus_one' ); ?></th>
							<td class="gglplsn_no_padding">
								<select name="gglplsn_size">
									<option value="standart" <?php if ( $gglplsn_options['gglplsn_size'] == 'standart' ) echo 'selected="selected"';?>> <?php _e( 'Standart', 'google_plus_one' ); ?></option>
									<option value="small" <?php if ( $gglplsn_options['gglplsn_size'] == 'small' ) echo 'selected="selected"';?>> <?php _e( 'Small', 'google_plus_one' ); ?></option>
									<option value="medium" <?php if ( $gglplsn_options['gglplsn_size'] == 'medium' ) echo 'selected="selected"';?>><?php _e( 'Medium', 'google_plus_one' ); ?></option>
									<option value="tall" <?php if ( $gglplsn_options['gglplsn_size'] == 'tall' ) echo 'selected="selected"';?>><?php _e( 'Tall', 'google_plus_one' ); ?></option>
								</select>
								<span class="gglplsn_info">(<?php echo __( 'Please choose one of four different sizes of buttons', 'google_plus_one' ); ?>)</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Button Position:', 'google_plus_one' ); ?></th>
							<td class="gglplsn_no_padding">
								<select name="gglplsn_position">
									<option value="before_post" <?php if ( $gglplsn_options['gglplsn_position'] == 'before_post' ) echo 'selected="selected"';?>><?php _e( 'Before Post', 'google_plus_one' ); ?></option>
									<option value="after_post" <?php if ( $gglplsn_options['gglplsn_position'] == 'after_post' ) echo 'selected="selected"';?>><?php _e( 'After Post', 'google_plus_one' ); ?></option>
									<option value="afterandbefore" <?php if ( $gglplsn_options['gglplsn_position'] == 'afterandbefore' ) echo 'selected="selected"';?>><?php _e( 'Before And After Post', 'google_plus_one' ); ?></option>
								</select>
								<span class="gglplsn_info">(<?php echo __( 'Please select location for the button on the page', 'google_plus_one' ); ?>)</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Language:', 'google_plus_one' ); ?></th>
							<td class="gglplsn_no_padding">
								<select name="gglplsn_lang">
									<option value="af" <?php if ( $gglplsn_options['gglplsn_lang'] == 'af' ) { echo 'selected="selected"'; } ?>>Afrikaans</option>
									<option value="am" <?php if ( $gglplsn_options['gglplsn_lang'] == 'am' ) { echo 'selected="selected"'; } ?>>Amharic</option>
									<option value="ar" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ar' ) { echo 'selected="selected"'; } ?>>Arabic</option>												
									<option value="eu" <?php if ( $gglplsn_options['gglplsn_lang'] == 'eu' ) { echo 'selected="selected"'; } ?>>Basque</option>
									<option value="bn" <?php if ( $gglplsn_options['gglplsn_lang'] == 'bn' ) { echo 'selected="selected"'; } ?>>Bengali</option>				
									<option value="bg" <?php if ( $gglplsn_options['gglplsn_lang'] == 'bg' ) { echo 'selected="selected"'; } ?>>Bulgarian</option>								
									<option value="ca" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ca' ) { echo 'selected="selected"'; } ?>>Catalan</option>
									<option value="zh-HK" <?php if ( $gglplsn_options['gglplsn_lang'] == 'zh-HK' ) { echo 'selected="selected"'; } ?>>Chinese (Hong Kong)</option>	
									<option value="zh-CN" <?php if ( $gglplsn_options['gglplsn_lang'] == 'zn-CH' ) { echo 'selected="selected"'; } ?>>Chinese (Simplified)</option>
									<option value="zh-TW" <?php if ( $gglplsn_options['gglplsn_lang'] == 'zh-TW' ) { echo 'selected="selected"'; } ?>>Chinese (Traditional)</option>
									<option value="hr" <?php if ( $gglplsn_options['gglplsn_lang'] == 'hr' ) { echo 'selected="selected"'; } ?>>Croatian</option>
									<option value="cs" <?php if ( $gglplsn_options['gglplsn_lang'] == 'cs' ) { echo 'selected="selected"'; } ?>>Czech</option>
									<option value="da" <?php if ( $gglplsn_options['gglplsn_lang'] == 'da' ) { echo 'selected="selected"'; } ?>>Danish</option>
									<option value="nl" <?php if ( $gglplsn_options['gglplsn_lang'] == 'nl' ) { echo 'selected="selected"'; } ?>>Dutch</option>
									<option value="en-GB" <?php if ( $gglplsn_options['gglplsn_lang'] == 'en-GB' ) { echo 'selected="selected"'; } ?>>English (UK)</option>
									<option value="en-US" <?php if ( $gglplsn_options['gglplsn_lang'] == 'en-US' ) { echo 'selected="selected"'; } ?>>English (US)</option>
									<option value="et" <?php if ( $gglplsn_options['gglplsn_lang'] == 'et' ) { echo 'selected="selected"'; } ?>>Estonian</option>
									<option value="fil" <?php if ( $gglplsn_options['gglplsn_lang'] == 'fil' ) { echo 'selected="selected"'; } ?>>Filipino</option>
									<option value="fi" <?php if ( $gglplsn_options['gglplsn_lang'] == 'fi' ) { echo 'selected="selected"'; } ?>>Finnish</option>
									<option value="fr" <?php if ( $gglplsn_options['gglplsn_lang'] == 'fr' ) { echo 'selected="selected"'; } ?>>French</option>
									<option value="fr-CA" <?php if ( $gglplsn_options['gglplsn_lang'] == 'fr-CA' ) { echo 'selected="selected"'; } ?>>French (Canadian)</option>		
									<option value="gl" <?php if ( $gglplsn_options['gglplsn_lang'] == 'gl' ) { echo 'selected="selected"'; } ?>>Galician</option>
									<option value="de" <?php if ( $gglplsn_options['gglplsn_lang'] == 'de' ) { echo 'selected="selected"'; } ?>>German</option>
									<option value="el" <?php if ( $gglplsn_options['gglplsn_lang'] == 'el' ) { echo 'selected="selected"'; } ?>>Greek</option>
									<option value="gu" <?php if ( $gglplsn_options['gglplsn_lang'] == 'gu' ) { echo 'selected="selected"'; } ?>>Gujarati</option>
									<option value="iw" <?php if ( $gglplsn_options['gglplsn_lang'] == 'iw' ) { echo 'selected="selected"'; } ?>>Hebrew</option>
									<option value="hi" <?php if ( $gglplsn_options['gglplsn_lang'] == 'hi' ) { echo 'selected="selected"'; } ?>>Hindi</option>
									<option value="hu" <?php if ( $gglplsn_options['gglplsn_lang'] == 'hu' ) { echo 'selected="selected"'; } ?>>Hungarian</option>
									<option value="is" <?php if ( $gglplsn_options['gglplsn_lang'] == 'is' ) { echo 'selected="selected"'; } ?>>Icelandic</option>
									<option value="id" <?php if ( $gglplsn_options['gglplsn_lang'] == 'id' ) { echo 'selected="selected"'; } ?>>Indonesian</option>
									<option value="it" <?php if ( $gglplsn_options['gglplsn_lang'] == 'it' ) { echo 'selected="selected"'; } ?>>Italian</option>
									<option value="ja" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ja' ) { echo 'selected="selected"'; } ?>>Japanese</option>
									<option value="kn" <?php if ( $gglplsn_options['gglplsn_lang'] == 'kn' ) { echo 'selected="selected"'; } ?>>Kannada</option>
									<option value="ko" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ko' ) { echo 'selected="selected"'; } ?>>Korean</option>
									<option value="lv" <?php if ( $gglplsn_options['gglplsn_lang'] == 'lv' ) { echo 'selected="selected"'; } ?>>Latvian</option>
									<option value="lt" <?php if ( $gglplsn_options['gglplsn_lang'] == 'lt' ) { echo 'selected="selected"'; } ?>>Lithuanian</option>
									<option value="ms" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ms' ) { echo 'selected="selected"'; } ?>>Malay</option>
									<option value="ml" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ml' ) { echo 'selected="selected"'; } ?>>Malayalam</option>
									<option value="mr" <?php if ( $gglplsn_options['gglplsn_lang'] == 'mr' ) { echo 'selected="selected"'; } ?>>Marathi</option>
									<option value="no" <?php if ( $gglplsn_options['gglplsn_lang'] == 'no' ) { echo 'selected="selected"'; } ?>>Norwegian</option>
									<option value="fa" <?php if ( $gglplsn_options['gglplsn_lang'] == 'fa' ) { echo 'selected="selected"'; } ?>>Persian</option>
									<option value="pl" <?php if ( $gglplsn_options['gglplsn_lang'] == 'pl' ) { echo 'selected="selected"'; } ?>>Polish</option>
									<option value="pt-BR" <?php if ( $gglplsn_options['gglplsn_lang'] == 'pt-BR' ) { echo 'selected="selected"'; } ?>>Portuguese (Brazil)</option>
									<option value="pt-PT" <?php if ( $gglplsn_options['gglplsn_lang'] == 'pt-PT' ) { echo 'selected="selected"'; } ?>>Portuguese (Portugal)</option>
									<option value="ro" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ro' ) { echo 'selected="selected"'; } ?>>Romanian</option>
									<option value="ru" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ru' ) { echo 'selected="selected"'; } ?>>Russian</option>	
									<option value="sr" <?php if ( $gglplsn_options['gglplsn_lang'] == 'sr' ) { echo 'selected="selected"'; } ?>>Serbian</option>
									<option value="sk" <?php if ( $gglplsn_options['gglplsn_lang'] == 'sk' ) { echo 'selected="selected"'; } ?>>Slovak</option>
									<option value="sl" <?php if ( $gglplsn_options['gglplsn_lang'] == 'sl' ) { echo 'selected="selected"'; } ?>>Slovenian</option>
									<option value="es" <?php if ( $gglplsn_options['gglplsn_lang'] == 'es' ) { echo 'selected="selected"'; } ?>>Spanish</option>
									<option value="es-419" <?php if ( $gglplsn_options['gglplsn_lang'] == 'es-419' ) { echo 'selected="selected"'; } ?>>Spanish (Latin America)</option>
									<option value="sw" <?php if ( $gglplsn_options['gglplsn_lang'] == 'sw' ) { echo 'selected="selected"'; } ?>>Swahili</option>
									<option value="sv" <?php if ( $gglplsn_options['gglplsn_lang'] == 'sv' ) { echo 'selected="selected"'; } ?>>Swedish</option>
									<option value="ta" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ta' ) { echo 'selected="selected"'; } ?>>Tamil</option>
									<option value="te" <?php if ( $gglplsn_options['gglplsn_lang'] == 'te' ) { echo 'selected="selected"'; } ?>>Telugu</option>
									<option value="th" <?php if ( $gglplsn_options['gglplsn_lang'] == 'th' ) { echo 'selected="selected"'; } ?>>Thai</option>
									<option value="tr" <?php if ( $gglplsn_options['gglplsn_lang'] == 'tr' ) { echo 'selected="selected"'; } ?>>Turkish</option>
									<option value="uk" <?php if ( $gglplsn_options['gglplsn_lang'] == 'uk' ) { echo 'selected="selected"'; } ?>>Ukrainian</option>
									<option value="ur" <?php if ( $gglplsn_options['gglplsn_lang'] == 'ur' ) { echo 'selected="selected"'; } ?>>Urdu</option>
									<option value="vi" <?php if ( $gglplsn_options['gglplsn_lang'] == 'vi' ) { echo 'selected="selected"'; } ?>>Vietnamese</option>
									<option value="zu" <?php if ( $gglplsn_options['gglplsn_lang'] == 'zu' ) { echo 'selected="selected"'; } ?>>Zulu</option>
								</select>
								<span class="gglplsn_info">(<?php echo __( 'Select the language to display information on the button', 'google_plus_one' ); ?>)</span>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'Show button:', 'google_plus_one' ); ?></th>
							<td>
								<p>
									<input type="checkbox" name="gglplsn_posts" <?php if ( $gglplsn_options['gglplsn_posts'] == '1' ) echo 'checked="checked"'; ?> value="1" />
									<label><?php echo __( 'Show in posts', 'google_plus_one' ); ?></label>
								</p>
								<p>
									<input type="checkbox" name="gglplsn_pages" <?php if ( $gglplsn_options['gglplsn_pages'] == '1' ) echo 'checked="checked"'; ?>  value="1" />
									<label><?php echo __( 'Show in pages', 'google_plus_one' ); ?></label>
								</p>
								<p>
									<input type="checkbox" name="gglplsn_homepage" <?php if ( $gglplsn_options['gglplsn_homepage'] == '1' ) echo 'checked="checked"'; ?>  value="1" />
									<label><?php echo __( 'Show on the homepage', 'google_plus_one' ); ?></label>
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
		</div><!-- .wrap -->
	<?php }
}

// Add google +1 button javascript
if( ! function_exists( 'gglplsn_link' ) ) {
	function gglplsn_link( $links ) {
		array_unshift( $links );
		return $links;
	}
}

if( ! function_exists( 'gglplsn_js' ) ) {
	function gglplsn_js() {
		global $gglplsn_options;
		if ( $gglplsn_options['gglplsn_js'] == '1' ) { ?>
			<script type="text/javascript" src="http://apis.google.com/js/plusone.js">
				<?php if ( $gglplsn_options['gglplsn_lang'] != 'en-US' ) { ?>
					{'lang': '<?php echo get_option( 'gglplsn_lang' ); ?>'}
				<?php } ?>
			</script>
		<?php }
	}
}

// Google +1 button
if( ! function_exists( 'gglplsn_button' ) ) {
function gglplsn_button( $content ) {
	global $gglplsn_options;
		if ( ( is_single() && '1' == $gglplsn_options['gglplsn_posts'] ) || ( is_page() && '1' == $gglplsn_options['gglplsn_pages'] ) || ( ( is_home() || is_front_page() ) && '1' == $gglplsn_options['gglplsn_homepage'] ) ) {
			$content .= '<g:plusone';
			if ( 'standard' != $gglplsn_options['gglplsn_size'] ) {
				$content .= ' size="' . $gglplsn_options['gglplsn_size'] . '"';
			}
			if ( '1' != $gglplsn_options['gglplsn_annotation'] ) {
				$content .= ' annotation="none"';
			}
			$content .= ' href="' . get_permalink() . '" callback="on"></g:plusone>';
		}
		return $content;
	} 
}

// Google +1 position on page 
if( ! function_exists( 'gglplsn_pos' ) ) {
	function gglplsn_pos( $content ) {
		global $gglplsn_options;
		$button = gglplsn_button( '' );
		if ( "1" == $gglplsn_options['gglplsn_posts'] || '1' == $gglplsn_options['gglplsn_pages'] || '1' == $gglplsn_options['gglplsn_homepage'] ) {
			if ( 'before_post' == $gglplsn_options['gglplsn_position'] ) {
				return $button . $content;
			} else if ( 'after_post' == $gglplsn_options['gglplsn_position'] ) {
				return  $content . $button;
			} else if ( 'afterandbefore' == $gglplsn_options['gglplsn_position'] ){
				return $button . $content . $button;
			}
		} else {
			return $content;
		}
		return $content;
	}
}
		
// Google +1 shortcode
// [bws_googleplusone]
if( ! function_exists( 'gglplsn_shortcode' ) ) {
	function gglplsn_shortcode( $atts ){
		global $gglplsn_options;
		extract( shortcode_atts( 
			array( 
				"annotation"	=>	$gglplsn_options['gglplsn_annotation'], 
				"url"			=>	get_permalink(), 
				"size"			=>	$gglplsn_options['gglplsn_size']
			), 
			$atts ) 
		);
		$shortbutton = '<br/><g:plusone';
		if ( 'standard' != $size ) {
			$shortbutton .= ' size="' . $size . '"';
		}
		if ( '1' != $annotation ) {
			$shortbutton .= ' annotation="none"';
		}
		$shortbutton .= ' href="' . $url . '" callback="on"></g:plusone>';
		return $shortbutton;
	}
}

// Add settings link on plugin page
if( ! function_exists( 'gglplsn_action_links' ) ) {
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
			$links[] = '<a href="admin.php?page=google-plus-one.php">' . __( 'Settings', 'google_plus_one' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/extend/plugins/google-one/faq/" target="_blank">' . __( 'FAQ', 'google_plus_one' ) . '</a>';
			$links[] = '<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google_plus_one' ) . '</a>';
		}
		return $links;
	}
}
if( ! function_exists( 'gglplsn_uninstall' ) ) {
	function gglplsn_uninstall() {
		delete_option( 'gglplsn_options' );
	}
}

add_action( 'init', 'gglplsn_default_options' );
add_action( 'init', 'gglplsn_init' );
// Adds "Settings" link to the plugin action page
add_action( 'admin_menu', 'gglplsn_admin_menu' );
add_action( 'admin_enqueue_scripts', 'gglplsn_admin_head' );
add_action( 'wp_head', 'gglplsn_js' );

add_filter( 'plugin_action_links', 'gglplsn_action_links', 10, 2 );
add_filter ( 'the_content', 'gglplsn_pos' );
// Additional links on the plugin page
add_filter( 'plugin_row_meta', 'gglplsn_register_plugin_links', 10, 2 );
add_filter( 'widget_text', 'do_shortcode' );
add_shortcode( 'bws_googleplusone', 'gglplsn_shortcode' );

register_uninstall_hook( __FILE__, 'gglplsn_uninstall' );
?>
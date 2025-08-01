<?php
/*
Plugin Name: SKT Themes Demo Importer
Plugin URI: https://wordpress.org/plugins/skt-themes-demo-import/
Description: Quickly import theme live demo content, widgets and settings. This provides a basic layout to build your website and speed up the development process.
Version: 1.6
Author: SKT Themes
Author URI: https://sktthemes.org/
License: GPL3
License URI: https://www.gnu.org/licenses/license-list.html#GNUGPLv3
Text Domain: skt-themes-demo-import
Tested up to: 6.8
Requires PHP: 5.6
*/

// Block direct access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Check PHP version
if ( version_compare( phpversion(), '5.6', '<' ) ) {
	function SKT_old_php_admin_error_notice() {
		$message = sprintf(
			esc_html__( 'The %2$sSKT Themes Demo Importer%3$s plugin requires %2$sPHP 5.6+%3$s to run properly. Please contact your hosting company and ask them to update the PHP version of your site to at least PHP 5.6.%4$s Your current version of PHP: %2$s%1$s%3$s', 'skt-themes-demo-import' ),
			phpversion(), '<strong>', '</strong>', '<br>'
		);
		printf( '<div class="notice notice-error"><p>%1$s</p></div>', wp_kses_post( $message ) );
	}
	add_action( 'admin_notices', 'SKT_old_php_admin_error_notice' );
	return;
}

// Plugin constants
define( 'SKT_VERSION', '1.0' );
define( 'SKT_PATH', plugin_dir_path( __FILE__ ) );
define( 'SKT_URL', plugin_dir_url( __FILE__ ) );

// Include main class if exists
if ( file_exists( SKT_PATH . 'inc/class-skt-main.php' ) ) {
	require SKT_PATH . 'inc/class-skt-main.php';
	$SKT_Demo_Import = SKT_Demo_Import::getInstance();
}

// Register rewrite rules
add_action( 'init', 'skt_themes_demo_import_register_xml_endpoint' );
function skt_themes_demo_import_register_xml_endpoint() {
	add_rewrite_tag( '%skt_themes_demo_import_xml%', '1' );
	add_rewrite_rule( '^skt-themes-demo-import\.xml$', 'index.php?skt_themes_demo_import_xml=1', 'top' );
}

// Add custom query var
add_filter( 'query_vars', 'skt_themes_demo_import_add_query_var' );
function skt_themes_demo_import_add_query_var( $vars ) {
	$vars[] = 'skt_themes_demo_import_xml';
	return $vars;
}

// Flush rewrite rules only once after plugin activates or on need
add_action( 'init', 'skt_themes_demo_import_maybe_flush_rules', 99 );
function skt_themes_demo_import_maybe_flush_rules() {
	if ( get_option( 'skt_themes_demo_import_rules_flushed' ) !== '1' ) {
		flush_rewrite_rules();
		update_option( 'skt_themes_demo_import_rules_flushed', '1' );
	}
}

// Flush on activation
register_activation_hook( __FILE__, function() {
	flush_rewrite_rules();
	update_option( 'skt_themes_demo_import_rules_flushed', '1' );
});

// Reset flag on deactivation
register_deactivation_hook( __FILE__, function() {
	delete_option( 'skt_themes_demo_import_rules_flushed' );
});

// Add XML discovery <link> tag
add_action( 'wp_head', 'skt_themes_demo_import_add_link_to_head' );
function skt_themes_demo_import_add_link_to_head() {
	echo '<link rel="alternate" type="text/html" href="' . esc_url( home_url( '/skt-themes-demo-import.xml' ) ) . '" />';
}

// Render HTML at /skt-themes-demo-import.xml
add_action( 'template_redirect', 'skt_themes_demo_import_render_custom_html' );
function skt_themes_demo_import_render_custom_html() {
	if ( get_query_var( 'skt_themes_demo_import_xml' ) ) {
		header( 'Content-Type: text/html; charset=utf-8' );
		header( 'X-Content-Type-Options: nosniff' );
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>" />
			<meta name="viewport" content="width=device-width, initial-scale=1" />
			<title><?php esc_html_e( 'This website has been created with the help of SKT Themes', 'skt-themes-demo-import' ); ?></title>
			<meta name="description" content="<?php esc_html_e( 'Description', 'skt-themes-demo-import' ); ?>" />
			<link rel="canonical" href="<?php echo esc_url( home_url( '/skt-themes-demo-import.xml' ) ); ?>" />
			<style>
				body {
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
					background: #fff;
					color: #333;
					line-height: 1.6;
					margin: 0;
				}
				#skt-themes-demo-import-description {
					background: #0196d6;
					color: #fff;
					padding: 30px 20px;
				}
				#skt-themes-demo-import-description h1 {
					margin: 0;
					font-size: 28px;
					text-align: center;
				}
				#skt-themes-demo-import-description p {
					margin: 10px 0;
					font-size: 16px;
				}
				#skt-themes-demo-import-description a,
				#skt-themes-demo-import-content a {
					color: #003be3;
					text-decoration: none;
				}
				#skt-themes-demo-import-description a:hover,
				#skt-themes-demo-import-content a:hover {
					color: #f98315;
					text-decoration: underline;
				}
				#skt-themes-demo-import-content {
					padding: 15px 20px;
					background: #f9f9f9;
				}
			</style>
		</head>
		<body>
			<div id="skt-themes-demo-import-description">
				<h1><?php esc_html_e( 'This website has been created with the help of SKT Themes', 'skt-themes-demo-import' ); ?></h1>
			</div>
			<div id="skt-themes-demo-import-content">
				<center>
				<?php
				$paragraphs = array(
					sprintf(
						__( '<a href="%1$s" target="_blank">SKT WordPress Themes</a> helps you create websites effortlessly without any coding knowledge with the help of page builder.', 'skt-themes-demo-import' ),
						esc_url( 'https://www.sktthemes.org/' )
					),
					__( 'We also offer free trial themes.', 'skt-themes-demo-import' ),
					sprintf(
						__( 'So check out <a href="%1$s" target="_blank">SKT free WordPress themes</a> and take a trial of the theme before making a purchase.', 'skt-themes-demo-import' ),
						esc_url( 'https://www.sktthemes.org/product-category/free-wordpress-themes/' )
					),
				);
				foreach ( $paragraphs as $para ) {
					echo '<p>' . wp_kses_post( $para ) . '</p>';
				}
				?>
				</center>
			</div>
		</body>
		</html>
		<?php
		exit;
	}
}
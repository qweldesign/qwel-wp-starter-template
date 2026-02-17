<?php
/**
 * Functions.php
 * © 2026 QWEL.DESIGN (https://qwel.design)
 * Released under the MIT License.
 * See LICENSE file for details.
 */

/*
 * テーマのパス, URI
 */
define( 'QWEL_THEME_DIR', get_template_directory() );
define( 'QWEL_THEME_URI', get_template_directory_uri() );

/*
 * classのオートロード
 */
spl_autoload_register(
	function( $classname ) {
		if ( strpos( $classname, 'QWEL_WP' ) === false ) return;
		$classname = str_replace( '\\', '/', $classname );
		$classname = str_replace( 'QWEL_WP/', '', $classname );
		$file      = QWEL_THEME_DIR . '/classes/' . $classname . '.php';
		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

/*
 * Hookする関数群を継承して登録
 */
class QWEL {
	use	\QWEL_WP\Supports,
		\QWEL_WP\Scripts,
		\QWEL_WP\Shortcodes;
		
	public function __construct() {
		// テーマサポート機能
		add_action( 'after_setup_theme', [ $this, 'setup_theme' ] );

		// CSS, JSファイルを読み込み
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		// ショートコード登録
		add_action( 'init', [ $this, 'register_shortcode' ] );
	}
}

/**
 * QWEL start!
 */
new QWEL();

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
define('QWEL_THEME_DIR', get_template_directory());
define('QWEL_THEME_URI', get_template_directory_uri());

/*
 * class のオートロード
 */
spl_autoload_register(
  function (string $className): void {
    if (strpos($className, 'QWEL_WP') === false) return;
    $className = str_replace('\\', '/', $className);
    $className = str_replace('QWEL_WP/', '', $className);
    $file = QWEL_THEME_DIR . '/classes/' . $className . '.php';
    if (file_exists($file)) {
      require $file;
    }
  }
);

/*
 * Hook する関数群を継承して登録
 */
class QWEL {
  use \QWEL_WP\Supports,
    \QWEL_WP\Scripts,
    \QWEL_WP\Shortcodes;

  public function __construct() {
    // テーマサポート機能
    add_action('after_setup_theme', [$this, 'setup_theme']);

    // CSS, JSファイルを読み込み
    add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);

    // ショートコード登録
    add_action('init', [$this, 'register_shortcode']);
  }
}

/**
 * QWEL start!
 */
new QWEL();

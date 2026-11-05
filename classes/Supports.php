<?php
namespace QWEL_WP;

trait Supports {
  // テーマサポート機能
  public function setup_theme(): void {
    // ブロックスタイルをサポート
    add_theme_support('wp-block-styles');

    // エディターに style.css をセット
    add_editor_style('style.css');
  }
}

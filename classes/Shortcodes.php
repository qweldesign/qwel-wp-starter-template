<?php
namespace QWEL_WP;

trait Shortcodes {
  // ショートコード登録
  public function register_shortcode(): void {
    // タイトルを作成
    add_shortcode('title', [$this, 'get_title']);

    // パンくずリストを作成
    add_shortcode('breadcrumb', [$this, 'get_breadcrumb']);

    // コピーライトに現在年を添える
    add_shortcode('copyright', [$this, 'get_copyright']);
  }

  public function get_title(array|string $atts): string {
    $wpObj = get_queried_object();
    $title = '';

    // 固定ページ
    if (is_home() || is_page()) {
      $title = $wpObj->post_title;

    // 個別投稿ページ
    } else if (is_single()) {
      $title = 'ブログ';

    // 日付別
    } else if (is_date()) {
      $year = get_query_var('year');
      $month = get_query_var('monthnum');
      $day = get_query_var('day');
      if ($day > 0) $title = $year . ' / ' . sprintf('%02d', $month) . ' / ' . sprintf('%02d', $day);
      else if ($month > 0) $title = $year . ' / ' . sprintf('%02d', $month);
      else $title = $year;

    // 投稿者アーカイブ
    } else if (is_author()) {
      $title = '著者:' . $wpObj->display_name;

    // タームアーカイブ
    } else if (is_archive()) {
      $title = $wpObj->name;

    // 検索結果ページ
    } else if (is_search()) {
      $title = '検索:' . get_search_query();

    // 404ページ
    } else if (is_404()) {
      $title = '記事が見つかりません';

    // その他
    } else {
      $title = 'ブログ';
    }

    return '<h1>' . $title . '</h1>';
  }

  public function get_breadcrumb(array|string $atts): string {
    $html = '<ul id="breadcrumb" class="breadcrumb">';
    $html .= '<li class="breadcrumb__item"><a href="' . home_url('/') . '">top</a></li>';

    $wpObj = get_queried_object();
    /**
     * 投稿一覧ページ
     */
    if (is_home()) {
      $html .= '<li class="breadcrumb__item is-current">' . get_post_type_object('post')->label . '</li>';

    /**
     * 個別投稿ページ
     */
    } else if (is_single()) {
      $postId = $wpObj->ID;
      $postType = $wpObj->post_type;
      $postTitle = $wpObj->post_title;

      // カスタム投稿タイプの場合, 投稿タイプ一覧を表示
      if ($postType !== 'post') {
        $html .= '<li class="breadcrumb__item"><a href="' . get_post_type_archive_link($postType) . '">' . get_post_type_object($postType)->label . '</a></li>';
      }

      // カスタム投稿タイプから, タクソノミーを取得
      if ($postType == 'post') {
        // 「投稿」の場合, 「カテゴリー」を取得
        $taxName = 'category';

        /**
         * カスタムタクソノミーを作成した場合は, ここに処理を追加
         */
      } else {
        $taxName = '';
      }

      // タクソノミーが紐づいていれば表示
      if ($taxName != '') {
        $terms = get_the_terms($postId, $taxName); // 投稿に紐づく全タームを取得

        if (!empty($terms)) {
          $term = $terms[0];

          /* Custom */
          if ($term->slug !== 'feature') {
            // 親タームがあれば表示
            if ($term->parent > 0) {
              $parentArray = array_reverse(get_ancestors($term->term_id, $taxName));
              foreach ($parentArray as $parentId) {
                $parentTerm = get_term($parentId, $taxName);

                $html .= '<li class="breadcrumb__item"><a href="' . get_term_link($parentId, $taxName) . '">' . $parentTerm->name . '</a></li>';
              }
            }

            // 最下層タームを表示
            $html .= '<li class="breadcrumb__item"><a href="' . get_term_link($term->term_id, $taxName) . '">' . $term->name . '</a></li>';
          }
        }
      }

      // 自身
      $html .= '<li class="breadcrumb__item is-current">' . $postTitle . '</li>';

    /**
     * 固定ページ
     */
    } else if (is_page()) {
      $pageId = $wpObj->ID;
      $pageTitle = $wpObj->post_title;

      // 親ページがあれば表示
      if ($wpObj->post_parent > 0) {
        $parentArray = array_reverse(get_post_ancestors($pageId));
        foreach ($parentArray as $parentId) {
          $html .= '<li class="breadcrumb__item"><a href="' . get_permalink($parentId) . '">' . get_the_title($parentId) . '</a></li>';
        }
      }

      // 自身
      $html .= '<li class="breadcrumb__item is-current">' . $pageTitle . '</li>';

    /**
     * カスタム投稿アーカイブ
     */
    } else if (is_post_type_archive()) {
      $html .= '<li class="breadcrumb__item is-current">' . $wpObj->label . '</li>';

    /**
     * 日付別
     */
    } else if (is_date()) {
      $year = get_query_var('year');
      $month = get_query_var('monthnum');
      $day = get_query_var('day');

      // 日別アーカイブ
      if ($day > 0) {
        $html .= '<li class="breadcrumb__item"><a href="' . get_year_link($year) . '">' . $year . '</a></li>';
        $html .= '<li class="breadcrumb__item"><a href="' . get_month_link($year, $month) . '">' . sprintf('%02d', $month) . '</a></li>';
        $html .= '<li class="breadcrumb__item is-current">' . sprintf('%02d', $day) . '</li>';

      // 月別アーカイブ
      } else if ($month > 0) {
        $html .= '<li class="breadcrumb__item"><a href="' . get_year_link($year) . '">' . $year . '</a></li>';
        $html .= '<li class="breadcrumb__item is-current">' . sprintf('%02d', $month) . '</li>';

      // 年別アーカイブ
      } else {
        $html .= '<li class="breadcrumb__item is-current">' . $year . '</li>';
      }
    /**
     * 投稿者アーカイブ
     */
    } else if (is_author()) {
      $html .= '<li class="breadcrumb__item is-current">著者: ' . $wpObj->display_name . '</li>';

    /**
     * タームアーカイブ
     */
    } else if (is_archive()) {
      $postType = '';
      $term = $wpObj;
      $termId = $wpObj->term_id;
      $termName = $wpObj->name;
      $taxName = $wpObj->taxonomy;

      // 「カテゴリー」, 「タグ」の場合, 「投稿」を取得
      if ($taxName == 'category' || $taxName == 'post_tag') {
        $postType = 'post';
      }

      /**
       * カスタムタクソノミーを作成した場合は, ここに処理を追加
       */

      // カスタム投稿タイプの場合, 投稿タイプ一覧を表示
      if ($postType !== 'post') {
        $html .= '<li class="breadcrumb__item"><a href="' . get_post_type_archive_link($postType) . '">' . get_post_type_object($postType)->label . '</a></li>';
      }

      // 親タームがあれば表示
      if ($term->parent > 0) {
        $parentArray = array_reverse(get_ancestors($term->term_id, $taxName));
        foreach ($parentArray as $parentId) {
          $parentTerm = get_term($parentId, $taxName);

          $html .= '<li class="breadcrumb__item"><a href="' . get_term_link($parentId, $taxName) . '">' . $parentTerm->name . '</a></li>';
        }
      }

      // 自身
      $html .= '<li class="breadcrumb__item is-current">' . $termName . '</li>';

    /**
     * 検索結果ページ
     */
    } else if (is_search()) {
      $html .= '<li class="breadcrumb__item is-current">検索: ' . get_search_query() . '</li>';

    /**
     * 404ページ
     */
    } else if (is_404()) {
      $html .= '<li class="breadcrumb__item is-current">404 記事が見つかりません</li>';
    }

    return $html . '</ul>';
  }

  public function get_copyright(array|string $atts): string {
    // 既定値
    $atts = shortcode_atts(
      [
        'year' => '2019',
        'text' => 'QWEL.DESIGN'
      ],
      $atts
    );

    // コピーライト文字列を作成
    $copyright = '&copy; ' . $atts['year'];
    $year = getdate()['year'];
    if ($atts['year'] == $year) {
      $copyright .= ' ' . $atts['text'];
    } else {
      $copyright .= ' - ' . $year . ' ' . $atts['text'];
    }

    return '<small class="copyright">' . $copyright . '</small>';
  }
}

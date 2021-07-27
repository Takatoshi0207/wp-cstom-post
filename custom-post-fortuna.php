<?php
//ヘッダー：プラグインであることの宣言。以下を記述することでプラグインとして認識される。
/**
 * @package    WordPress
 * @subpackage　custom-post-fortuna
 * @author RyoheiYokoyama
 *
 *
 * Plugin Name: Add CustomPostFortuna
 * Plugin URI: 
 * Text Domain: custom-post-fortuna
 * Description:  Fortuna様用カスタムフィールド Wp5.5対応版
 * Author: M-OKUCHU
 * Author URI:  
 * Version:     1.2
 * License:     GPLv3+
 */

function catalog_init()
{
    $labels = array(
        'name' => _x('カタログ投稿', 'post type general name'),
        'all_items' => __('カタログ一覧'),
        'singular_name' => __('データ'),
        'add_new' => __('新規追加'),
        'parent_item_colon' => ''
    );

    $args = array(
        'labels' => $labels,
        'public' => true, //管理画面・サイトへの表示の有無
        'publicly_queryable' => true,
        'show_ui' => true, //管理画面のメニューへの表示の有無
        'menu_position' => 5, //管理メニューでの表示位置
        'query_var' => true,
        'rewrite' => array('with_front' => false), //パーマリンク設定
        'capability_type' => 'post', //権限タイプ
        'map_meta_cap' => true, //デフォのメタ情報処理を利用の有無
        'hierarchical' => false, //階層(親)の有無
        'menu_icon' => 'dashicons-admin-post', //アイコン画像
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields'),
        //	  'taxonomies' => array( '', '' ), //postと共通にする場合
        'has_archive' => true, //アーカイブの有無
        'show_in_rest' => true //Gutenbergを有効化
    );
    register_post_type('catalog', $args);

    //カテゴリータイプ ポストと別のカテゴリーにする
    $args = array(
        'label' => 'カテゴリー',
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'hierarchical' => true, // カテゴリーを階層化する方法
    );
    register_taxonomy('catalog_cat', 'catalog', $args);

    //タグタイプ
    $args = array(
        'label' => 'タグ',
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
    );
    register_taxonomy('catalog_tag', 'catalog', $args);
}

add_action('init', 'catalog_init');

//-------------------------------------------
// リライト Wordpress 5.5対応版
// post_id.htmlにRewrite
// カスタム投稿のみパーマリンクを英数字や規定値に変更するなど必須
//-------------------------------------------
function catalog_type_rewrite()
{
    global $wp_rewrite;

    $queryarg = 'post_type=catalog&p=';
    $wp_rewrite->add_rewrite_tag('%catalog_id%', '([^/]+)', $queryarg);
    $wp_rewrite->add_permastruct('catalog', '/catalog/%catalog_id%.html', false);
}
add_action('init', 'catalog_type_rewrite');

function catalog_type_permalink($post_link, $id = 0, $leavename)
{
    global $wp_rewrite;
    //$post = &get_post($id);
    $post = get_post($id);
    if (is_wp_error($post))
        return $post;
    $newlink = $wp_rewrite->get_extra_permastruct($post->post_type);
    $newlink = str_replace('%' . $post->post_type . '_id%', $post->ID, $newlink);
    $newlink = home_url(user_trailingslashit($newlink));
    return $newlink;
}
add_filter('post_type_link', 'catalog_type_permalink', 1, 3);

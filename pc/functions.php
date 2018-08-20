<?php
add_action( 'add_admin_bar_menus', 'gr_add_admin_bar_menus' );
function gr_add_admin_bar_menus() {
/*
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_item', 7 );
*/
	// Site related.
	remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_my_sites_menu', 20 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_site_menu', 30 );
	remove_action( 'admin_bar_menu', 'wp_admin_bar_updates_menu', 40 );
	add_action( 'admin_bar_menu', 'gr_admin_bar_wp_menu', 10 );

	// Content related.
	if ( ! is_network_admin() && ! is_user_admin() ) {
		remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
		remove_action( 'admin_bar_menu', 'wp_admin_bar_new_content_menu', 70 );
	}
	remove_action( 'admin_bar_menu', 'wp_admin_bar_edit_menu', 80 );

//	add_action( 'admin_bar_menu', 'wp_admin_bar_add_secondary_groups', 200 );
}
function gr_admin_bar_wp_menu( $wp_admin_bar ) {
	$wp_admin_bar->add_menu( array(
		'id'    => 'gr-logo',
		'title' => '<img class="gr-icon" src="'.get_stylesheet_directory_uri().'/images/gr-logo-t.png"/>',
		'href'  => 'http://www.gotta-ride.com',
		'meta'  => array(
			'title' => 'ゴッタライド',
		),
	) );
}
add_action( 'wp_head', 'gr_head' );
add_action( 'admin_head', 'gr_head' );
function gr_head() {
?>
<style type="text/css" media="screen">
#wpadminbar{background:#f5f5f5;border-bottom:1px solid #333;}
#wpadminbar .quicklinks a, #wpadminbar .quicklinks .ab-empty-item, #wpadminbar .shortlink-input, #wpadminbar { height: 40px; line-height: 40px; }
#wpadminbar #wp-admin-bar-gr-logo { background-color: #f5f5f5;}
#wpadminbar .gr-icon { vertical-align: middle; }
body.admin-bar #wpcontent, body.admin-bar #adminmenu { padding-top: 40px;}
#wpadminbar .ab-top-secondary,
#wpadminbar .ab-top-menu > li:hover > .ab-item, #wpadminbar .ab-top-menu > li.hover > .ab-item, #wpadminbar .ab-top-menu > li > .ab-item:focus, #wpadminbar.nojq .quicklinks .ab-top-menu > li > .ab-item:focus, #wpadminbar #wp-admin-bar-gr-logo a:hover{background-color:transparent;background-image:none;color:#333;}
#screen-meta-links{display:none;}
#wpadminbar .ab-sub-wrapper, #wpadminbar ul, #wpadminbar ul li {background:#F5F5F5;}
#wpadminbar .quicklinks .ab-top-secondary > li > a, #wpadminbar .quicklinks .ab-top-secondary > li > .ab-empty-item,
#wpadminbar .quicklinks .ab-top-secondary > li {border-left: 1px solid #f5f5f5;}
#wpadminbar * {color: #333;text-shadow: 0 1px 0 #fff;}
</style>
<?php
}
add_filter( 'admin_footer_text', '__return_false' );
add_filter( 'update_footer', '__return_false', 9999 );
add_action( 'admin_notices', 'gr_update_nag', 0 );
function gr_update_nag() {
	if ( ! current_user_can( 'administrator' ) ) {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
}

// seko ページでは editor 非表示
add_action( 'admin_print_styles-post.php', 'bc_post_page_style' );
add_action( 'admin_print_styles-post-new.php', 'bc_post_page_style' );
function bc_post_page_style() {
	if ( in_array( $GLOBALS['current_screen']->post_type, array( 'seko', 'slide_img', 'leaflet','event' ,'voice','craftsman','staff','price','tenpo','seminar' ) ) ) :
?>
<style type="text/css">
#postdivrich{display:none;}
#<?php global $current_screen; var_dump( $current_screen) ?>{}
</style>
<?php
	endif;
}
// カスタムフィールド&カスタム投稿タイプの追加
function gr_register_terms( $terms, $taxonomy ) {
	foreach ( $terms as $key => $label ) {
		$keys = explode( '/', $key );
		if ( 1 < count( $keys ) ) {
			$key = $keys[1];
			$parent_id = get_term_by( 'slug', $keys[0], $taxonomy )->term_id;
		} else {
			$parent_id = 0;
		}
		if ( ! term_exists( $key, $taxonomy ) ) {
			wp_insert_term( $label, $taxonomy, array( 'slug' => $key, 'parent' => $parent_id ) );
		}
	}
}

add_action( 'init', 'bc_create_customs', 0 );
function bc_create_customs() {

	// 施工事例
    register_post_type( 'seko', array(
        'labels' => array(
            'name' => __( '施工事例' ),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_position' => 4,
        'supports' => array( 'title', 'editor' ),
    ) );

    register_taxonomy( 'seko_cat', 'seko', array(
         'label' => '施工事例カテゴリー',
         'hierarchical' => true,
    ) );

		$terms = array(
	);
	gr_register_terms( $terms, 'seko_cat' );

	register_taxonomy( 'seko_staff', 'seko', array(
		'label' => 'スタッフカテゴリー',
         	'hierarchical' => true,
	) );

	// こだわり施工事例
	register_post_type( 'good_seko', array(
			'labels' => array(
		'name' => __( 'こだわり施工事例' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 5,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'good_cat', 'good_seko', array(
			 'label' => 'こだわり施工事例カテゴリー',
	) );
	$terms = array(
		'good_kitchen' => 'キッチン',
		'good_ohuro' => 'お風呂',
	);
	gr_register_terms( $terms, 'good_cat' );

	// リフォームMenu
	register_post_type( 'reformmenu', array(
			'labels' => array(
		'name' => __( 'リフォームMenu' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 6,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'reformmenu_cat', 'reformmenu', array(
			 'label' => 'リフォームMenuカテゴリー',
	 'hierarchical' => true,
	) );
	$terms = array(
		'reform_kitchen' => 'キッチン',
		'reform_ohuro' => 'お風呂',
		'reform_toilet' => 'トイレ',
		'reform_j2w' => '和室から洋室',
		'reform_kabegami' => '壁紙クロス',
		'reform_gaiheki' => '外壁',
		'reform_yane' => '屋根',
		'reform_kyuto' => '給湯',
		'reform_taishin' => '耐震',
		'reform_yuka' => '床',
	);
	gr_register_terms( $terms, 'reformmenu_cat' );

	// 価格表
	register_post_type( 'price', array(
			'labels' => array(
		'name' => __( '価格表' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 7,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'price_cat', 'price', array(
			 'label' => '価格表カテゴリー',
			 'hierarchical' => true,
	) );
	$terms = array(
		'price_kitchen' => 'キッチン',
		'price_ohuro' => 'お風呂',
		'price_ohuro/price_unitex' => 'ユニットバスの入替え',
		'price_ohuro/price_old2unit' => '在来工法のお風呂をユニットバスに',
		'price_ohuro/price_oldreform' => '在来工法のお風呂リフォーム',
		'price_toilet' => 'トイレ',
		'price_j2w' => '和室から洋室',
		'price_kabegami' => '壁紙クロス',
		'price_gaiheki' => '外壁リフォーム',
		'price_yane' => '屋根',
		'price_yanereform' => '屋根リフォーム',
		'price_yuka' => '床リフォーム',
		'price_kyuto' => '給湯器',
		'price_taishin' => '耐震リフォーム',
	);
	gr_register_terms( $terms, 'price_cat' );

	// 価格表(一覧)
	register_post_type( 'maker', array(
			'labels' => array(
		'name' => __( '価格表 一覧' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 8,
		'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'maker_cat', 'maker', array(
			 'label' => '価格表 一覧カテゴリー',
					 'hierarchical' => true,
	) );
	$terms = array(//ここにカテゴリー
	);
	gr_register_terms( $terms, 'maker_cat' );


	// よくあるご相談
	register_post_type( 'soudan', array(
			'labels' => array(
		'name' => __( 'よくあるご相談' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 9,
		'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'soudan_cat', 'soudan', array(
			 'label' => 'よくあるご相談カテゴリー',
			 'hierarchical' => true,
	) );
	$terms = array(
		'soudan_kitchen' => 'キッチン',
		'soudan_ohuro' => 'お風呂',
		'soudan_toilet' => 'トイレ',
		'soudan_j2w' => '和室から洋室',
		'soudan_kabegami' => '壁紙クロス',
		'soudan_yuka' => '床リフォーム',
	);
	gr_register_terms( $terms, 'soudan_cat' );


	// 工事の流れ
	register_post_type( 'nagare', array(
			'labels' => array(
		'name' => __( '工事の流れ' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 9,
		'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'nagare_cat', 'nagare', array(
			 'label' => '工事の流れカテゴリー',
			 'hierarchical' => true,
	) );
	$terms = array(
		'nagare_kitchen' => 'キッチン',
		'nagare_ohuro' => 'お風呂',
		'nagare_toilet' => 'トイレ',
		'nagare_j2w' => '和室から洋室',
		'nagare_kabegami' => '壁紙クロス',
		'nagare_yuka' => '床リフォーム',
	);
	gr_register_terms( $terms, 'nagare_cat' );


	// 現場日記
	register_post_type( 'genbanikki', array(
			'labels' => array(
		'name' => __( '現場日記' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 10,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'genba_cat', 'genbanikki', array(
			 'label' => '現場日記カテゴリー',
				     'hierarchical' => true,
	) );

	// メディア紹介
	register_post_type( 'media', array(
			'labels' => array(
		'name' => __( 'マスコミ取材実績' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 11,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'media_cat', 'media', array(
			 'label' => 'マスコミカテゴリー',
		     'hierarchical' => true,
	) );

	// お知らせ
	register_post_type( 'whatsnew', array(
			'labels' => array(
		'name' => __( 'お知らせ' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 11,
	'supports' => array( 'title', 'editor', 'thumbnail' )
	) );
	register_taxonomy( 'whatsnew_cat', 'whatsnew', array(
			 'label' => 'お知らせカテゴリー',
		     'hierarchical' => true,
	) );
	// スマホ用お知らせ
	register_post_type( 'spnews', array(
			'labels' => array(
		'name' => __( 'スマホ用お知らせ' ),
		'singular_name' => __( 'スマホ用お知らせ')
			),
			'public' => true,
			'menu_position' => 17,
	'supports' => array( 'title' ),
	) );

	// イベント
	register_post_type( 'event', array(
			'labels' => array(
		'name' => __( 'イベント' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 12,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'event_cat', 'event', array(
			 'label' => 'イベントカテゴリー',
		     'hierarchical' => true,
	) );

	// スタッフ
	register_post_type( 'staff', array(
			'labels' => array(
		'name' => __( 'スタッフ' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 13,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'staff_cat', 'staff', array(
			 'label' => 'スタッフカテゴリー',
			 'hierarchical' => true,
	) );
	$terms = array(
		'staff_maebashi' => '前橋ショールーム',
	);
	gr_register_terms( $terms, 'staff_cat' );


	// 職人
	register_post_type( 'craftsman', array(
			'labels' => array(
		'name' => __( '職人' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 14,
	'supports' => array( 'title', 'editor' ),
	) );

	// お客様の声
	register_post_type( 'voice', array(
			'labels' => array(
		'name' => __( 'お客様の声' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 15,
	'supports' => array( 'title', 'editor' ),
	) );

	// TOPスライドショー画像
	register_post_type( 'slide_img', array(
			'labels' => array(
		'name' => __( 'TOPスライドショー画像' ),
		'singular_name' => __( 'TOPスライドショー画像')
			),
			'public' => true,
			'menu_position' => 16,
	'supports' => array( 'title', 'editor' ),
	) );
	// TOP SPスライドショー画像
	register_post_type( 'slide_img_sp', array(
			'labels' => array(
				'name' => __( 'TOP SPスライドショー画像' ),
				'singular_name' => __( 'TOP SPスライドショー画像')
			),
			'public' => true,
			'menu_position' => 16,
			'supports' => array( 'title', 'editor' ),
	) );
	// TOPテロップ(一言メッセージ)
	register_post_type( 'telop', array(
			'labels' => array(
		'name' => __( 'TOPテロップ' ),
		'singular_name' => __( 'TOPテロップ')
			),
			'public' => true,
			'menu_position' => 17,
	'supports' => array( 'title' ),
	) );

	// チラシ
	register_post_type( 'leaflet', array(
			'labels' => array(
		'name' => __( 'チラシ' ),
		'singular_name' => __( 'チラシ')
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 18,
	'supports' => array( 'title', 'editor' ),
	) );


		// スタッフブログ
	register_post_type( 'staffblog', array(
			'labels' => array(
		'name' => __( 'スタッフブログ' ),
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 19,
	'supports' => array( 'title', 'editor','comments' ),
	) );
	register_taxonomy( 'staffblog_cat', 'staffblog', array(
			 'label' => 'スタッフブログカテゴリー',
         		 'hierarchical' => true,
	) );
		$terms = array(
	);
	gr_register_terms( $terms, 'staffblog_cat' );


	// 給湯器キャンペーン
	register_post_type( 'kyuto', array(
			'labels' => array(
		'name' => __( '給湯器キャンペーン' ),
			),
			'public' => true,
			'menu_position' => 20,
	'supports' => array( 'title', 'editor' ),
	) );
	register_taxonomy( 'kyuto_cat', 'kyuto', array(
			 'label' => '給湯器カテゴリー',
			 'hierarchical' => true,
	) );
	// 来店予約店舗
	register_post_type( 'tenpo', array(
			'labels' => array(
		'name' => __( '来店予約店舗' ),
		'singular_name' => __( '来店予約店舗')
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 21,
	'supports' => array( 'title', 'editor' ),
	) );
	// 土日開催リフォーム＆増改築相談会
	register_post_type( 'seminar', array(
			'labels' => array(
		'name' => __( '土日開催リフォーム＆増改築相談会' ),
		'singular_name' => __( '土日開催リフォーム＆増改築相談会')
			),
			'public' => true,
			'has_archive' => true,
			'menu_position' => 21,
	'supports' => array( 'title', 'editor' ),
	) );

	// リフォームバナー表示価格
	register_post_type( 'bnr_price', array(
			'labels' => array(
		'name' => __( 'リフォームバナー表示価格' ),
		'singular_name' => __( 'リフォームバナー表示価格')
			),
			'public' => true,
			'menu_position' => 21,
	'supports' => array( 'title','author' ),
	) );
	add_action('admin_menu','add_custom_inputbox');
	add_action('save_post', 'save_custom_postdata');
	function add_custom_inputbox()
	{
		add_meta_box('bnr_data','各数値','add_bnr_input','bnr_price','normal','high');
	}
	function add_bnr_input()
	{
		global $post;
		echo '最低価格（万円）<input type="text" name="b_price" value="'.get_post_meta($post->ID,'b_price',true).'"><br>';
		echo '割引率（％）　　<input type="text" name="b_off" value="'.get_post_meta($post->ID,'b_off',true).'"><br>';
	}
	function save_custom_postdata($post_id)
	{
		$b_price = '';
		if(isset($_POST['b_price'])){ $b_price = $_POST['b_price'];}
		if(strcmp($b_price,get_post_meta($post_id,'b_price',true)) !=0)
		{
			update_post_meta($post_id,'b_price',$b_price);
		}
		elseif($b_price =="")
		{
			delete_post_meta($post_id,'b_price',get_post_meta($post_id,'b_price',true));
		}

		$b_off = '';
		if(isset($_POST['b_off'])){ $b_off = $_POST['b_off'];}
		if(strcmp($b_off,get_post_meta($post_id,'b_off',true)) !=0)
		{
			update_post_meta($post_id,'b_off',$b_off);
		}
		elseif($b_off =="")
		{
			delete_post_meta($post_id,'b_off',get_post_meta($post_id,'b_off',true));
		}
	}
}

	// アイキャッチを有効に
add_theme_support( 'post-thumbnails' );
//// hooks
add_filter( 'wp_list_categories', 'gr_list_categories', 10, 2 );
function gr_list_categories( $output, $args ) {
	return preg_replace( '@</a>\s*\((\d+)\)@', ' ($1)</a>', $output );
}

add_action( 'pre_get_posts', 'gr_pre_get_posts' );
function gr_pre_get_posts( $query ) {
	if ( is_admin() ) {
		if ( in_array( $query->get( 'post_type' ), array( 'seko', 'staff' ) ) ) {
			$query->set( 'posts_per_page', -1 );
		}
		return;
	}
/*
	if ( is_post_type_archive() ) {
		if ( 'seko' == get_query_var( 'post_type' ) ) {
			$query->tax_query[] = array(
				'taxonomy' =>	'seko_cat',
				'term'     => 'kitchen',
				'field'    => 'slug',
			);
		}
	}
*/
}

function gr_adjacent_post_join( $join, $in_same_cat, $excluded_categories ) {
	if ( false && $in_same_cat ) {
		global $post, $wpdb;

		$taxonomy  = $post->post_type . '_cat';
		$terms     = implode( ',', wp_get_object_terms( $post->ID, $taxonomy, array('fields' => 'ids') ) );
		$join      = " INNER JOIN $wpdb->term_relationships AS tr ON p.ID = tr.object_id INNER JOIN $wpdb->term_taxonomy tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
		$join     .= $wpdb->prepare( " AND tt.taxonomy = %s AND tt.term_id IN ($terms)", $taxonomy );
	}

	return $join;
}

//// functions
function gr_title() {
	global $page, $paged;

	wp_title( '|', true, 'right' );
	bloginfo( 'name' );

	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && is_front_page() )
		echo " | $site_description";

	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf(  '%sページ', max( $paged, $page ) );
}

function gr_description() {
	$desc = get_option( 'gr_description' );

	if ( is_front_page() || ! $desc ) {
		bloginfo( 'description' );
	} else {
		$title = str_replace( '|', '', wp_title( '|', false ) );
		echo str_replace( '%title%', $title, get_option( 'gr_description' ) );
	}
}

function gr_get_posts_count() {
	global $wp_query;
	return get_query_var( 'posts_per_page' ) ? $wp_query->found_posts : $wp_query->post_count;
}

function gr_get_pagename() {
	$pagename = '';

	if ( is_page() ) {
		/*
		$obj = get_queried_object();
		if ( 14 == $obj->post_parent )
			$pagename = 'business';
		else
		*/
			$pagename = get_query_var( 'pagename' );
	} elseif( ! $pagename = get_query_var( 'post_type' ) ) {
		//
	}

	return $pagename;
}

define( 'GR_IMAGES', get_stylesheet_directory_uri() . '/images/' );
function gr_img( $file, $echo = true ) {
	$img = esc_attr( GR_IMAGES . $file );

	if ( $echo )
		echo $img;
	else
		return $img;
}

function gr_get_post( $post_name ) {
	global $wpdb;
	$null = $_post = null;

	if ( ! $_post = wp_cache_get( $post_name, 'posts' ) ) {
		$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_name = %s LIMIT 1", $post_name ) );
		if ( ! $_post )
			return $null;
		_get_post_ancestors($_post);
		$_post = sanitize_post( $_post, 'raw' );
		wp_cache_add( $post_name, $_post, 'posts' );
	}

	return $_post;
}

function gr_get_permalink( $name, $taxonomy = '' ) {
	$link = false;

	if ( false && term_exists( $name, $taxnomy ) ) {
		$link = get_term_link( $name );
	} else if ( post_type_exists( $name ) ) {
		$link = get_post_type_archive_link( $name );
	} else {
		$_post = gr_get_post( $name );
		if ( $_post )
			$link = get_permalink( $_post );
	}

	return $link;
}

function gr_image_id( $key ) {
    $imagefield = post_custom( $key );
    return  preg_replace('/(\[)([0-9]+)(\])(http.+)?/', '$2', $imagefield );
}

function gr_get_image( $key, $att = '' ) {
	$id = gr_image_id( $key );

	if ( is_numeric( $id ) ) {
		if ( isset( $att['size'] ) ) {
			$size = $att['size'];
			unset( $att['size'] );
		}
		if ( isset( $att['width'] ) ) {
			$size = array( $att['width'], 99999 );
			unset( $att['width'] );
		}
		return wp_get_attachment_image( $id, $size, false, $att );
	}

	if ( $id ) {
		/* ファイル存在チェック
		 * $id = /images/seko/289-2-t.jpg のようなパスでここに渡ってくるので
		 * get_stylesheet_directory_uri()のようなhttpで絶対パスを指定せず
		 * dirname(__FILE__)でチェック
		 */
		if( file_exists( dirname(__FILE__) . "$id" ) ) {
			return sprintf(
				'<img src="%1$s%2$s"%3$s%4$s%5$s />',
				get_stylesheet_directory_uri(),
				$id,
				( $att['width' ] ? ' width="' .$att['width' ].'"' : '' ),
				( $att['height'] ? ' height="'.$att['height'].'"' : '' ),
				( $att['alt'   ] ? ' alt="'   .$att['alt'   ].'"' : '' )
			);
		}
	}

	return '';
}
function gr_get_image_src( $key ) {
	$id = gr_image_id( $key );
	$src = '';

	if ( is_numeric( $id ) ) {
		@list( $src, $width, $height ) = wp_get_attachment_image_src( $id, $size, false );
	} else if ( $id ) {
		$src = get_stylesheet_directory_uri() . $id;
	}
	return $src;
}

function gr_contact_banner() {
?>
	<!-- ======================問合わせテーブルここから======================= -->
	<p class="right_toiawase2">
	<a href="/contact/"><img src="/wp-content/themes/reform/images/top/inquiry_btntoi.gif" width="161" height="64" alt="無料見積もり・ご相談はこちらから"
	 /></a><a href="/book"><img src="/wp-content/themes/reform/images/top/inquiry_btnbook.gif" width="160" height="64" alt="じっくり検討したい！資料請求はこちら" /></a>
	</p>
	<!-- ======================問合わせテーブルここまで======================= -->

<?php
}
function gr_blog_list() {
?>
<ul class="topBlog_list">
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_mori.png" width="102" height="128" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/mori" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_mori_rollout.gif" width="99" height="36" alt="森雅之" /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_kitayashiki.png" width="98" height="126" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/kitayashiki" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_kitayashiki_rollout.gif" width="99" height="36" alt="北屋敷匡史" /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_kawahira.png" width="94" height="124" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/kawahira" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_kawahira_rollout.gif" width="99" height="36" alt="河平大和" /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_saitou.png" width="108" height="124" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/saito" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_saitou_rollout.gif" width="99" height="36" alt="齋藤 舜" /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_murakami.png" width="108" height="124" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/murakami" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_murakami_rollout.gif" width="99" height="36" alt="村上 弘史" /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_tada.png" width="108" height="124" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/tada" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_tada_rollout.gif" width="99" height="36" alt="多田 綾子 " /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_shimakata.png" width="108" height="124" alt="嶋方　里佳写真" class="pic" /><a href="/staffblog_cat/shimakata" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_shimakata_rollout.gif" width="99" height="36" alt="嶋方　里佳" /></a></li>
	<li><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_mahoe.png" width="108" height="124" alt="スタッフ写真" class="pic" /><a href="/staffblog_cat/mahoe" class="btn"><img src="<?php echo get_template_directory_uri(); ?>/images/top/blog/tblog_mahoe_rollout.gif" width="99" height="36" alt="真保栄啓介" /></a></li>
</ul>

<?php
}

if ( function_exists('register_sidebar') ) {
	register_sidebar( array(
				'name' => 'sidebar',
				'before_widget' => '',
				'after_widget' => '</ul>',
				'before_title' => '<p class="pic">',
				'after_title' => '</p><ul class="page_left_menu">',
	) );
}

//// enqueue
add_action( 'wp_print_styles', 'gr_print_styles' );
function gr_print_styles() {
	if( ! is_admin() ) {

		if ( is_front_page() ) {
			wp_enqueue_style( 'gr_orbit'  , get_stylesheet_directory_uri() . '/common/css/orbit.css' );
		}
		wp_enqueue_style( 'gr_shadowbox', get_stylesheet_directory_uri() . '/common/css/shadowbox.css' );
		wp_enqueue_style( 'gr_common'   , get_stylesheet_directory_uri() . '/css/common.css' );
	}
}

add_action( 'wp_enqueue_scripts', 'gr_enqueue_scripts' );
function gr_enqueue_scripts() {
	if ( is_singular() ) wp_enqueue_script( 'comment-reply' );

	if ( ! is_admin() ) {
		wp_enqueue_script( 'jquery'	);//		, get_stylesheet_directory_uri() . '/common/js/jquery-1.5.1.min.js'			, array(		  ), false, true );
		if ( is_front_page() ) {
			wp_enqueue_script( 'gr_orbit'		, get_stylesheet_directory_uri() . '/common/js/jquery.orbit-1.2.3.min.js'	, array( 'jquery' ), false, true );
		}
		wp_enqueue_script( 'gr_rollover'	, get_stylesheet_directory_uri() . '/common/js/rollover2.js'				, array( 'jquery' ), false, true );
		wp_enqueue_script( 'gr_scroll'		, get_stylesheet_directory_uri() . '/common/js/smoothScroll.js'				, array( 'jquery' ), false, true );
		wp_enqueue_script( 'gr_shadowbox'	, get_stylesheet_directory_uri() . '/common/js/shadowbox.js'				, array( 'jquery' ), false, true );
		wp_enqueue_script( 'gr_index'		  , get_stylesheet_directory_uri() . '/common/js/index.js'					, array( 'jquery', 'gr_shadowbox' ), false, true );
	}
}

//// admin

//add_action( 'admin_print_scripts-options-general.php', 'gr_options_general' );
add_action( 'admin_footer-options-general.php', 'gr_options_general' );
function gr_options_general() {
?>
<script type="text/javascript">
//<![CDATA[
(function($) {
	if($('body.options-general-php').length) {
		$('#blogdescription').parent().parent().before( $('#gr_companyname' ).parent().parent() );
		$('#blogdescription').parent().parent()
			.after( $('#gr_author' ).parent().parent() )
			.after( $('#gr_keywords' ).parent().parent() )
			.after( $('#gr_description' ).parent().parent() );
	}
})(jQuery);
//]]>
</script>
<?php
}

class GR_Admin {
	static private $options = NULL;

	public function GR_Admin() {
		$this->__construct;
	}

	public function __construct() {
		$this->options = array(
			array( 'id' => 'companyname', 'label' => '会社名'		     , 'desc' => '著作権表示用などに使用する会社名です。' ),
			array( 'id' => 'author'		, 'label' => '作成者'		     , 'desc' => 'サイトの作成者情報です。' ),
			array( 'id' => 'description', 'label' => 'ディスクリプション', 'desc' => '下層ページ用description' ),
			array( 'id' => 'keywords'	, 'label' => 'キーワード'	     , 'desc' => '半角コンマ（,）で区切って複数指定できます。' ),
		);
		add_action( 'admin_init'			, array( &$this, 'add_settings_fields' 		) );
		add_filter( 'whitelist_options'		, array( &$this, 'whitelist_options' 		) );
	}
	public function whitelist_options( $whitelist_options ) {
		foreach ( (array) $this->options as $option ) {
			$whitelist_options['general'][] = 'gr_' . $option['id'];
		}

		return $whitelist_options;
	}
	public function add_settings_fields() {
		foreach ( (array) $this->options as $key => $option ) {
			add_settings_field(
				$key+1, $option['label'], array( &$this, 'print_settings_field' ), 'general', 'default',
				array(
					'label_for' 	=> 'gr_' . $option['id'],
					'description' 	=> $option['desc'],
				)
			);
		}
	}
	public function print_settings_field( $args ) {
		printf(
			'<input name="%1$s" type="text" id="%1$s" value="%2$s" class="regular-text" />',
			esc_attr( $args['label_for'] ),
			esc_attr( get_option( $args['label_for'] ) )
		);
		if ( ! empty( $args['description'] ) )
			printf(
				'<span class="description">%1$s</span>',
				esc_html( $args['description'] )
			);
	}
}

new GR_Admin;

/***************************************/

/**
 * 管理画面でのフォーカスハイライト
 */
function focus_highlight() {
	?>
		<style type="text/css">
		input:focus,textarea:focus{
			background-color: #dee;
		}
	</style>
		<?php
}

add_action( 'admin_head', 'focus_highlight' );

/**
 * 投稿での改行
 * [br] または [br num="x"] x は数字を入れる
 */
function sc_brs_func( $atts, $content = null ) {
	extract( shortcode_atts( array(
					'num' => '5',
					), $atts ));
	$out = "";
	for ($i=0;$i<$num;$i++) {
		$out .= "<br />";
	}
	return $out;
}

add_shortcode( 'br', 'sc_brs_func' );

//---------------------------------------------------------------------------
//\r\nの文字列の無効化
//---------------------------------------------------------------------------

add_filter('post_custom', 'fix_gallery_output');

function fix_gallery_output( $output ){
  $output = str_replace('rn', '', $output );
  return $output;
}


// echo fix_gallery_output(file_get_contents(__FILE__));

//---------------------------------------------------------------------------
//パンくず
//---------------------------------------------------------------------------

function the_pankuzu_keni( $separator = '　→　', $multiple_separator = '　|　' )
{
	global $wp_query;

	echo("<li><a href=\""); bloginfo('url'); echo("\">HOME</a>$separator</li>" );

	$queried_object = $wp_query->get_queried_object();

	if( is_page() )
	{
		//ページ
		if( $queried_object->post_parent )
		{
			echo( get_page_parents_keni( $queried_object->post_parent, $separator ) );
		}
		echo '<li>'; the_title(); echo '</li>';
	}
	else if( is_archive() )
	{
		if( is_post_type_archive() )
		{
			echo '<li>'; post_type_archive_title(); echo '</li>';
		}
		else if( is_category() )
		{
			//カテゴリアーカイブ
			if( $queried_object->category_parent )
			{
				echo get_category_parents( $queried_object->category_parent, 1, $separator );
			}
			echo '<li>'; single_cat_title(); echo '</li>';
		}
		else if( is_day() )
		{
			echo '<li>'; printf( __('Archive List for %s','keni'), get_the_time(__('F j, Y','keni'))); echo '</li>';
		}
		else if( is_month() )
		{
			echo '<li>'; printf( __('Archive List for %s','keni'), get_the_time(__('F Y','keni'))); echo '</li>';
		}
		else if( is_year() )
		{
			echo '<li>'; printf( __('Archive List for %s','keni'), get_the_time(__('Y','keni'))); echo '</li>';
		}
		else if( is_author() )
		{
			echo '<li>'; _e('Archive List for authors','keni'); echo '</li>';
		}
		else if(isset($_GET['paged']) && !empty($_GET['paged']))
		{
			echo '<li>'; _e('Archive List for blog','keni'); echo '</li>';
		}
		else if( is_tag() )
		{
			//タグ
			echo '<li>'; printf( __('Tag List for %s','keni'), single_tag_title('',0)); echo '</li>';
		}
	}
	else if( is_single() )
	{
		$obj = get_post_type_object( $queried_object->post_type );
		if ( $obj->has_archive ) {
			printf(
				'<li><a href="%1$s">%2$s</a>%3$s</li>',
				get_post_type_archive_link( $obj->name ),
				apply_filters( 'post_type_archive_title', $obj->labels->name ),
				$separator
			);
		} else {
			//シングル
			echo '<li>'; the_category_keni( $separator, 'multiple', false, $multiple_separator ); echo '</li>';
			echo( $separator );
		}
		echo '<li>'; the_title(); echo '</li>';
	}
	else if( is_search() )
	{
		//検索
		echo '<li>'; printf( __('Search Result for %s','keni'), strip_tags(get_query_var('s'))); echo '</li>';
	}
	else
	{
		$request_value = "";
		foreach( $_REQUEST as $request_key => $request_value ){
			if( $request_key == 'sitemap' ){ $request_value = $request_key; break; }
		}

		if( $request_value == 'sitemap' )
		{
			echo '<li>'; _e('Sitemap','keni'); echo '</li>';
		}
		else
		{
			echo '<li>'; the_title(); echo '</li>';
		}
	}
}

function get_page_parents_keni( $page, $separator )
{
	$pankuzu = "";

	$post = get_post( $page );

	$pankuzu = '<li><a href="'. get_permalink( $post ) .'">' . $post->post_title . '</a>' . $separator . '</li>';

	if( $post->post_parent )
	{
		$pankuzu = get_page_parents_keni( $post->post_parent, $separator ) . $pankuzu;
	}

	return $pankuzu;
}

function the_category_keni($separator = '', $parents='', $post_id = false, $multiple_separator = '/') {
	echo get_the_category_list_keni($separator, $parents, $post_id, $multiple_separator);
}

function get_the_category_list_keni($separator = '', $parents='', $post_id = false, $multiple_separator = '/')
{
	global $wp_rewrite;
	$categories = get_the_category($post_id);
	if (empty($categories))
		return apply_filters('the_category', __('Uncategorized', 'keni'), $separator, $parents);

	$rel = ( is_object($wp_rewrite) && $wp_rewrite->using_permalinks() ) ? 'rel="category tag"' : 'rel="category"';

	$thelist = '';
	if ( '' == $separator ) {
		$thelist .= '<ul class="post-categories">';
		foreach ( $categories as $category ) {
			$thelist .= "\n\t<li>";
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ($category->parent)
						$thelist .= get_category_parents($category->parent, TRUE, $separator);
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->name.'</a></li>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>';
					if ($category->parent)
						$thelist .= get_category_parents($category->parent, FALSE);
					$thelist .= $category->name.'</a></li>';
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->cat_name.'</a></li>';
			}
		}
		$thelist .= '</ul>';
	} else {
		$i = 0;
		foreach ( $categories as $category ) {
			if ( 0 < $i )
				$thelist .= $multiple_separator . ' ';
			switch ( strtolower($parents) ) {
				case 'multiple':
					if ( $category->parent )
						$thelist .= get_category_parents($category->parent, TRUE, $separator);
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->cat_name.'</a>';
					break;
				case 'single':
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>';
					if ( $category->parent )
						$thelist .= get_category_parents($category->parent, FALSE);
					$thelist .= "$category->cat_name</a>";
					break;
				case '':
				default:
					$thelist .= '<a href="' . get_category_link($category->term_id) . '" title="' . sprintf(__('View all posts in %s', 'keni'), $category->name) . '" ' . $rel . '>' . $category->name.'</a>';
			}
			++$i;
		}
	}
	return apply_filters('the_category', $thelist, $separator, $parents);
}
function get_specials(){
	include "specials.php";
}
function get_menutoi(){
	include "menutoi.php";
}
function get_menuohuro1(){
	include "menuohuro1.php";
}
function get_menuohuro2(){
	include "menuohuro2.php";
}

function get_menukitchen1(){
	include "menukitchen1.php";
}
function get_menukitchen2(){
	include "menukitchen2.php";
}

function get_menutoilet1(){
	include "menutoilet1.php";
}
function get_menutoilet2(){
	include "menutoilet2.php";
}

function get_menuyane1(){
	include "menuyane1.php";
}
function get_menuyane2(){
	include "menuyane2.php";
}

function get_menugaiheki1(){
	include "menugaiheki1.php";
}
function get_menugaiheki2(){
	include "menugaiheki2.php";
}

function get_menuyuka1(){
	include "menuyuka1.php";
}
function get_menuyuka2(){
	include "menuyuka2.php";
}

function get_menukabegami1(){
	include "menukabegami1.php";
}
function get_menukabegami2(){
	include "menukabegami2.php";
}

function get_menuj2w1(){
	include "menuj2w1.php";
}
function get_menuj2w2(){
	include "menuj2w2.php";
}

function get_menutaishin1(){
	include "menutaishin1.php";
}
function get_menutaishin2(){
	include "menutaishin2.php";
}

function get_menukyuto1(){
	include "menukyuto1.php";
}
function get_menukyuto2(){
	include "menukyuto2.php";
}

//ダッシュボードの記述▼

add_action('wp_dashboard_setup', 'my_custom_dashboard_widgets');

function my_custom_dashboard_widgets() {
global $wp_meta_boxes;

wp_add_dashboard_widget('custom_help_widget', 'ゴッタライドからのお知らせ', 'dashboard_text');
}
function dashboard_text() {
echo '<iframe src="http://www.gotta-ride.com/cloud/news.html" height=200 width=100% scrolling=no>
この部分は iframe 対応のブラウザで見てください。
</iframe>';
}

function example_remove_dashboard_widgets() {
    global $wp_meta_boxes;
    //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']); // 現在の状況
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']); // 最近のコメント
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']); // 被リンク
    unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']); // プラグイン
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']); // クイック投稿
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']); // 最近の下書き
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']); // WordPressブログ
    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']); // WordPressフォーラム
}
add_action('wp_dashboard_setup', 'example_remove_dashboard_widgets');

//ダッシュボードの記述▲

//投稿画面から消す▼

function remove_post_metaboxes() {
    remove_meta_box('tagsdiv-post_tag', 'post', 'normal'); // タグ
}
add_action('admin_menu', 'remove_post_metaboxes');

//投稿画面から消す▲ /ログイン時メニューバー消す▼

add_filter('show_admin_bar', '__return_false');

//ログイン時メニューバー消す▲　/アップデートのお知らせを管理者のみに　▼
if (!current_user_can('edit_users')) {
  function wphidenag() {
    remove_action( 'admin_notices', 'update_nag');
  }
  add_action('admin_menu','wphidenag');
}

//アップデートのお知らせ▲

/**
 *
 * 最新記事のIDを取得
 * @return  Int ID
 *
 */
function get_the_latest_ID() {
    global $wpdb;
    $row = $wpdb->get_row("SELECT ID FROM $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC");
    return !empty( $row ) ? $row->ID : '0';
}
function the_latest_ID() {
    echo get_the_latest_ID();
}

/*ＩＤ取得*/

function get_gaiyobar(){

echo <<<BNR

<div class="content_gaiyobt">
<h3><img src="/wp-content/themes/reform/page_image/gaiyo/tit_gaiyobt.gif" width="auto" height="17" alt="株式会社ミタカ工房　会社概要" title="株式会社ミタカ工房　会社概要" /></h3>
<ul>
	<li><a href="/company/" title="会社案内"><img src="/wp-content/themes/reform/page_image/gaiyo/bt_gaiyo1_rollout.gif" width="222" alt="会社案内" /></a></li>
	<li><a href="/staff/" title="スタッフ紹介"><img src="/wp-content/themes/reform/page_image/gaiyo/bt_gaiyo5_rollout.gif" width="222" alt="スタッフ紹介" /></a></li>
	<li><a href="/voice/" title="お客様の声"><img src="/wp-content/themes/reform/page_image/gaiyo/bt_gaiyo7_rollout.gif" width="222" alt="お客様の声" /></a></li>
	<li><a href="/event/" title="イベント情報"><img src="/wp-content/themes/reform/page_image/gaiyo/bt_gaiyo3_rollout.gif" width="222" alt="イベント情報" /></a></li>
</ul>
</div>
BNR;

}


function get_showroom(){

echo <<<BNR
<div class="top_bnr">
<a href="/showroom"><img src="/wp-content/themes/reform/page_image/showroom/bnr_sr01.png" width="365" height="124" alt="前橋ショールーム若宮店" /></a>
<a href="/koaigi"><img src="/wp-content/themes/reform/page_image/showroom/bnr_sr02.png" width="365" height="124" alt="前橋ショールーム小相木店" /></a>
</div>
BNR;

}

function get_rightbottom(){

echo <<<BNR

<!--==================================== -->
<div style="clear:both;"></div>

<!--==================================== -->

	<!--=================ミタカ工房ってどんな会社？=================== -->
	<div class="right_about">
	<h2 class="img"><img class="mb10" src="/wp-content/themes/reform/images/top/mitaka_title.gif" width="252" height="20" alt="ミタカ工房ってどんな会社？" /></h2>
	<a href="/company/"><div class="clear_left">
	<h3><img src="/wp-content/themes/reform/images/top/mitaka_st1.gif" width="214" height="38" alt="会社紹介" /></h3>
	<p><img src="/wp-content/themes/reform/images/top/mitaka_img1.gif" width="75" height="67" alt="ミタカ外観" />
	創業40年のこんな会社です<br />
	<span class="mitaka_arraw">詳しくはこちら</span></p>
	</div></a>

	<a href="/staff"><div>
	<h3><img src="/wp-content/themes/reform/images/top/mitaka_st3.gif" width="214" height="38" alt="スタッフ紹介" /></h3>
	<p><img src="/wp-content/themes/reform/images/top/mitaka_img3.gif" width="75" height="67" alt="スタッフ一同" />
	心熱き仲間たちをご紹介します！<br />
	<span class="mitaka_arraw">詳しくはこちら</span></p>
	</div></a>

	<a href="/koken/"><div>
	<h3><img src="/wp-content/themes/reform/images/top/mitaka_st4.gif" width="214" height="38" alt="地域貢献活動" /></h3>
	<p><img src="/wp-content/themes/reform/images/top/mitaka_img4.gif" width="75" height="67" alt="ボランティア" />
	９年連続無料ボランティア活動<br />
	<span class="mitaka_arraw">詳しくはこちら</span></p>
	</div></a>

	<a href="/event/">
	<div class="clear_left">
	<h3><img src="/wp-content/themes/reform/images/top/mitaka_st7.gif" width="214" height="38" alt="イベント情報" /></h3>
	<p><img src="/wp-content/themes/reform/images/top/mitaka_img7.gif" width="75" height="67" alt="イベントの様子" />
	ミタカ工房の開催するイベントの情報<br />
	<span class="mitaka_arraw">詳しくはこちら</span></p>
	</div>
	</a>

	<a href="/company/osusume/"><div>
	<h3><img src="/wp-content/themes/reform/images/top/mitaka_st6.gif" width="214" height="38" alt="おすすめサービス賞受賞！" /></h3>
	<p><img src="/wp-content/themes/reform/images/top/mitaka_img6.gif" width="75" height="67" alt="賞状" />
	建築業界初！群馬県知事より受賞！<br />
	<span class="mitaka_arraw">詳しくはこちら</span></p>
	</div></a>

	<a href="/company/rinen"><div>
	<h3><img src="/wp-content/themes/reform/images/top/mitaka_st2.gif" width="214" height="38" alt="経営理念" /></h3>
	<p><img src="/wp-content/themes/reform/images/top/mitaka_img2.gif" width="75" height="67" alt="朝礼の様子" />
	こんな事を考えて仕事をしています<br />
	<span class="mitaka_arraw">詳しくはこちら</span></p>
	</div></a>

	</div>
	<!--=================ミタカ工房ってどんな会社？=================== -->
				<!--================どんなリフォームしているの？他の会社と何が違うの？==================== -->

			<div class="bnr_chigai">
			<img src="/wp-content/themes/reform/images/top/tit_topchigai.jpg" width="524" height="26" alt="どんなリフォームしているの？他の会社と違うの？" /><br /><br />
				<div class="chigai_box chigai_fir">
					<a href="/company/riyu">
					<h3>
						<img src="/wp-content/themes/reform/images/top/bnr_topchigai01.png" width="208" height="84" alt="ミタカ工房が選ばれる11の理由" />
					</h3>
					<p>他の会社と何が違うの？<br />
						素朴な疑問にお答えします。<br />
						お客様からご評価いただいている<br />
						ポイントをまとめました。</p></a>
				</div>
				<div class="chigai_box mid-rig chigai_fir">
					<a href="/voice">
					<h3>
						<img src="/wp-content/themes/reform/images/top/bnr_topchigai02.png" width="208" height="84" alt="当社でリフォームされたお客様の声＆インタビュー" />
					</h3>
					<p class="chigai_right"><img src="/wp-content/themes/reform/images/top/chigai_no1.png" width="68" height="82" alt="インタビュー掲載数　群馬県NO.1" /></p><p style="clear:left">ミタカ工房のリフォームってどう？<br />
						当社のお客様から生の声をいただきました。</p></a>
				</div>
				<div class="chigai_box mid-rig chigai_fir">
					<a href="/reform_nagare/">
					<h3>
						<img src="/wp-content/themes/reform/images/top/bnr_topchigai03.png" width="208" height="84" alt="お問い合わせからご契約までの流れ" />
					</h3>
					<p>問合せしたらどんな手順で進むの？<br />
						安心してすすめるミタカ工房のリフォームの進め方を大公開！ </p></a>
				</div>
				<div class="chigai_box chigai_sec">
						<a href="/seko">
				<h3>
						<img src="/wp-content/themes/reform/images/top/bnr_topchigai04.png" width="208" height="55" alt="施工事例" />
					</h3>
					<p class="chigai_right"><img src="/wp-content/themes/reform/images/top/chigai_no1.png" width="68" height="82" alt="施工事例掲載数　群馬県NO.1" /></p> <p style="clear:left">小工事から増改築まで<br />
						当社の施工実績を<br />
						ご覧ください。</p></a>
				</div>
				<div class="chigai_box mid-rig chigai_sec">
						<a href="/genbanikki">
				<h3>
						<img src="/wp-content/themes/reform/images/top/bnr_topchigai05.png" width="208" height="55" alt="現場日記" />
					</h3>
					<p class="chigai_right"><img src="/wp-content/themes/reform/images/top/chigai_diaryimg.png" width="78" height="65" alt="現場日記イメージ" /></p><p style="clear:left">毎日の現場から実況中継。私たちの仕事ぶりをご確認ください。</p></a>
				</div>
				<div class="chigai_box mid-rig chigai_sec">
					<a href="/media">
					<h3>
						<img src="/wp-content/themes/reform/images/top/bnr_topchigai06.png" width="208" height="55" alt="マスコミ取材実績" />
					</h3>
					<p>地元密着のリフォーム会社として、テレビ・新聞・雑誌・ラジオなどで紹介されました。</p></a>

				</div>
				<br clear="all" />
				<br clear="all" />
			</div>
			<!--================どんなリフォームしているの？他の会社と何が違うの？==================== -->

BNR;

}

function get_access(){

echo <<<BNR

<h2><img src="/wp-content/themes/reform/page_image/company/st_access.gif" width="735" height="50" alt="アクセスマップ" /></h2>
<br clear="all">

<div class="accessmap_box">
	<h3><img src="/wp-content/themes/reform/page_image/company/h3_showroom01.jpg" width="220" height="18" alt="前橋ショールーム若宮店" /></h3>
	<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3211.118792257586!2d139.07142679999998!3d36.40632109999998!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x601ef36d5edb6b61%3A0xfcb937302fde3dde!2z576k6aas55yM5YmN5qmL5biC6Iul5a6u55S677yU5LiB55uu77yR77yT4oiS77yR77yS!5e0!3m2!1sja!2sjp!4v1407385997897" width="695" height="350" frameborder="0" style="border:0"></iframe><br /><small><a href="https://www.google.co.jp/maps/place/%E7%BE%A4%E9%A6%AC%E7%9C%8C%E5%89%8D%E6%A9%8B%E5%B8%82%E8%8B%A5%E5%AE%AE%E7%94%BA%EF%BC%94%E4%B8%81%E7%9B%AE%EF%BC%91%EF%BC%93%E2%88%92%EF%BC%91%EF%BC%92/@36.4063211,139.0714268,14z/data=!4m2!3m1!1s0x601ef36d5edb6b61:0xfcb937302fde3dde" style="color:#0000FF;text-align:right">大きな地図で見る</a></small>

	<h3><img src="/wp-content/themes/reform/page_image/company/h3_showroom02.jpg" width="220" height="18" alt="前橋ショールーム小相木店" /></h3>
	<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3212.5090563096487!2d139.05448515035428!3d36.37266889925094!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x601ef2dca865e7f5%3A0x7fe1614ff553e055!2z44CSMzcxLTA4MzEg576k6aas55yM5YmN5qmL5biC5bCP55u45pyo55S677yU77yQ77yW!5e0!3m2!1sja!2sjp!4v1474874473197" width="695" height="350" frameborder="0" style="border:0" allowfullscreen></iframe><br /><small><a href="https://goo.gl/maps/qahYdw3ercP2" style="color:#0000FF;text-align:right">大きな地図で見る</a></small>


	<h3><img src="/wp-content/themes/reform/page_image/company/h3_company01.jpg" width="57" height="18" alt="本店" /></h3>
	<iframe width="695" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.co.jp/maps?f=q&amp;source=s_q&amp;hl=ja&amp;geocode=&amp;q=%E5%89%8D%E6%A9%8B%E5%B8%82%E4%B8%8A%E6%B3%89%E7%94%BA1163-2&amp;aq=&amp;sll=34.742574,135.43075&amp;sspn=0.117501,0.222988&amp;brcurrent=3,0x601ef2322fc6bcb1:0xcf1194b9f42f2a7d,0,0x601ef2322fc6bcb1:0xfd0bbde39c145946&amp;ie=UTF8&amp;hq=&amp;hnear=%E7%BE%A4%E9%A6%AC%E7%9C%8C%E5%89%8D%E6%A9%8B%E5%B8%82%E4%B8%8A%E6%B3%89%E7%94%BA%EF%BC%91%EF%BC%91%EF%BC%96%EF%BC%93%E2%88%92%EF%BC%92&amp;t=m&amp;ll=36.399316,139.110003&amp;spn=0.02418,0.025749&amp;z=14&amp;iwloc=B&amp;output=embed"></iframe><br /><small><a href="https://maps.google.co.jp/maps?f=q&amp;source=embed&amp;hl=ja&amp;geocode=&amp;q=%E5%89%8D%E6%A9%8B%E5%B8%82%E4%B8%8A%E6%B3%89%E7%94%BA1163-2&amp;aq=&amp;sll=34.742574,135.43075&amp;sspn=0.117501,0.222988&amp;brcurrent=3,0x601ef2322fc6bcb1:0xcf1194b9f42f2a7d,0,0x601ef2322fc6bcb1:0xfd0bbde39c145946&amp;ie=UTF8&amp;hq=&amp;hnear=%E7%BE%A4%E9%A6%AC%E7%9C%8C%E5%89%8D%E6%A9%8B%E5%B8%82%E4%B8%8A%E6%B3%89%E7%94%BA%EF%BC%91%EF%BC%91%EF%BC%96%EF%BC%93%E2%88%92%EF%BC%92&amp;t=m&amp;ll=36.399316,139.110003&amp;spn=0.02418,0.025749&amp;z=14&amp;iwloc=B" style="color:#0000FF;text-align:right">大きな地図で見る</a></small>
<br clear="all">

		<div class="w221 mr16">
			<a href="/company/access-maebashi" class="f-l"><img src="/wp-content/themes/reform/page_image/company/access_btn_maebashi_rollout.gif" alt="前橋方面からお越しの方"></a>
		</div>
		<div class="w221 mr16">
			<a href="/company/access-isezaki"><img src="/wp-content/themes/reform/page_image/company/access_btn_isezaki_rollout.gif" alt="伊勢崎方面からお越しの方"></a>
		</div>
		<div class="w221">
			<a href="/company/access-shibukawa"><img src="/wp-content/themes/reform/page_image/company/access_btn_shibukawa_rollout.gif" alt="渋川方面からお越しの方"></a>
		</div>

</div>
<br clear="all">
BNR;

}

//リフォームメニュー　一覧のURL取得処理
function getReformListUrl($cat,$post_id){
	$terms = get_the_terms($post_id,'soudan_cat');
	foreach($terms as $term){
		if($term->slug === $cat){
		  $link = get_term_link((int)$term->term_id,'soudan_cat'). '#' . $post_id;
		  break;
		}
	}
	return $link;
}

//メール投稿　誰でも投稿可能に
function ke_another_author($user_id, $address) {
    return koushin; // 投稿はすべてこのユーザーに固定（作っておくこと）
}
add_filter('ktai_validate_address', 'ke_another_author', 10, 2);

//現場日記
function get_the_post_image_src($postid,$size,$order=0,$max=null) {
    $attachments = get_children(array('post_parent' => $postid, 'post_type' => 'attachment', 'post_mime_type' => 'image'));
    if ( is_array($attachments) ){
        foreach ($attachments as $key => $row) {
            $mo[$key]  = $row->menu_order;
            $aid[$key] = $row->ID;
        }
        array_multisort($mo, SORT_ASC,$aid,SORT_DESC,$attachments);
        $max = empty($max)? $order+1 :$max;
        for($i=$order;$i<$max;$i++){
            return wp_get_attachment_image_src( $attachments[$i]->ID, $size );
        }
    }
}

function get_the_post_image_id($post_id,$size){
	$attachments = get_children(array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image','posts_per_page' => 1 ));
	if(is_array($attachments)){
	        foreach ($attachments as $attachments) {
	            $imgL = wp_get_attachment_image_src( $attachments->ID, 'large' );
	            echo '<p><a href="' . $imgL[0] . '" rel="lightbox[genba]" title="' . get_the_title() . '">' . wp_get_attachment_image( $attachments->ID, $size ) . '</a></p>';
	        }
	}
}

remove_filter( 'the_content', 'wptexturize' );

//カレンダー
function widget_customCalendar($args) {
	extract($args);
	echo $before_widget;
	echo get_calendar_custom(カスタム投稿名);
	echo $after_widget;
}


function get_calendar_custom($posttype,$initial = true) {
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts;

	$key = md5( $m . $monthnum . $year );
	if ( $cache = wp_cache_get( 'get_calendar_custom', 'calendar_custom' ) ) {
		if ( isset( $cache[ $key ] ) ) {
			echo $cache[ $key ];
			return;
		}
	}

	ob_start();
	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$gotsome = $wpdb->get_var("SELECT ID from $wpdb->posts WHERE post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 1");
		if ( !$gotsome )
			return;
	}

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', current_time('timestamp'));
		$thismonth = gmdate('m', current_time('timestamp'));
	}

	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	$previous = $wpdb->get_row("SELECT DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

		WHERE post_date < '$thisyear-$thismonth-01'

		AND post_type = '$posttype' AND post_status = 'publish'
			ORDER BY post_date DESC
			LIMIT 1");

	$next = $wpdb->get_row("SELECT	DISTINCT MONTH(post_date) AS month, YEAR(post_date) AS year
		FROM $wpdb->posts
		LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

		WHERE post_date >	'$thisyear-$thismonth-01'

		AND MONTH( post_date ) != MONTH( '$thisyear-$thismonth-01' )
		AND post_type = '$posttype' AND post_status = 'publish'
			ORDER	BY post_date ASC
			LIMIT 1");

	echo '<div id="calendar_wrap">
	<table id="wp-calendar" summary="' . __('Calendar') . '">
	<caption>' . date('Y年n月', $unixmonth) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	echo '
	</tr>
	</tfoot>
	<tbody>
	<tr>';

	// Get days with posts
	$dyp_sql = "SELECT DISTINCT DAYOFMONTH(post_date)
		FROM $wpdb->posts

		LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)

		WHERE MONTH(post_date) = '$thismonth'

		AND YEAR(post_date) = '$thisyear'
		AND post_type = '$posttype' AND post_status = 'publish'
		AND post_date < '" . current_time('mysql') . "'";

	$dayswithposts = $wpdb->get_results($dyp_sql, ARRAY_N);

	if ( $dayswithposts ) {
		foreach ( (array) $dayswithposts as $daywith ) {
			$daywithpost[] = $daywith[0];
		}
	} else {
		$daywithpost = array();
	}

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom "
		."FROM $wpdb->posts "

		."LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id) "
		."LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id) "

		."WHERE YEAR(post_date) = '$thisyear' "

		."AND MONTH(post_date) = '$thismonth' "
		."AND post_date < '".current_time('mysql')."' "
		."AND post_type = '$posttype' AND post_status = 'publish'"
	);
	if ( $ak_post_titles ) {
		foreach ( (array) $ak_post_titles as $ak_post_title ) {

				$post_title = apply_filters( "the_title", $ak_post_title->post_title );
				$post_title = str_replace('"', '&quot;', wptexturize( $post_title ));

				if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
					$ak_titles_for_day['day_'.$ak_post_title->dom] = '';
				if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
					$ak_titles_for_day["$ak_post_title->dom"] = $post_title;
				else
					$ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
		}
	}

	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
			echo '<td id="today">';
		else
			echo '<td>';

		if ( in_array($day, $daywithpost) ) // any posts today?
				echo '<a href="' .  $home_url . '/' . $posttype .  '/date/' . $thisyear . '/' . $thismonth . '/' . $day . "\">$day</a>";
		else
			echo $day;
		echo '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

	echo "\n\t</tr>\n\t</tbody>\n\t</table></div>";

	echo "\n\t<div class=\"calender_navi\"><table cellspacing=\"0\" cellpadding=\"0\"><tr>";

	if ( $previous ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($previous->month) . '" colspan="3" id="prev"><a href="' .  $home_url . '/' . $posttype .  '/date/' . $previous->year . '/' . $previous->month . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month),			date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="prev" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($next->month) . '" colspan="3" id="next"><a href="' .  $home_url . '/' . $posttype .  '/date/' . $next->year . '/' . $next->month . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month),			date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" id="next" class="pad">&nbsp;</td>';
	}
	echo "\n\t</tr></table></div>";

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	$cache[ $key ] = $output;
	wp_cache_set( 'get_calendar_custom', $cache, 'calendar_custom' );
}
//カレンダー

//コメント
function reform_comment($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment;
echo <<<BUN
   <li
BUN;

comment_class();
echo ' id="li-comment-';
comment_ID();

echo <<<BUN
">
     <div id="comment-
BUN;
comment_ID();
echo <<<BUN
">
      <div class="comment-author vcard">

BUN;

echo get_avatar($comment,$size='48',$default='<path_to_url>' );
echo <<<BUN
<br />

BUN;

printf(__('%s'), get_comment_author_link());
echo <<<BUN
      </div>

BUN;

if ($comment->comment_approved == '0') :
echo <<<BUN
         <em><?php _e('Your comment is awaiting moderation.') ?></em>
         <br />

BUN;

endif;
echo <<<BUN
      <div class="comment-meta commentmetadata"><a href="
BUN;

echo htmlspecialchars( get_comment_link( $comment->comment_ID ) );
echo '">';
printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time());
echo'</a>';
edit_comment_link(__('(Edit)'),'  ','');
echo <<<BUN
<br />

BUN;

comment_text();
echo <<<BUN
      <div class="comment-reply">
BUN;

comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth'])));
echo <<<BUN
</div>
     </div>
BUN;

        }
//コメント
//給湯器
function get_sidereform(){
	include "includes/side-reformmenu.php";
}

// Contact Form 7 にショートコードを追加
// function the_tenpo_info ($args) {
//   $template = dirname(__FILE__) . '/archive-tenpo.php';
//   if (!file_exists($template)) {
//     return;
//   }
//   $args = shortcode_atts($def, $args);
//   $posts = get_posts($args);
//   ob_start();
//   foreach ($posts as $post) {
//     $post_custom = get_post_custom($post->ID);
//     include($template);
//   }
//   $output = ob_get_clean();
//   return $output;
// }
// wpcf7_add_shortcode('tenpo_info', 'the_tenpo_info');

//archive-tenpo.php自体をショートコードへ
function the_tenpo_info () {
	$args = array(
		'post_type' => 'tenpo', 		/* 投稿タイプを指定 */
		'paged' => $paged,				/* ページ番号を指定 */
		'posts_per_page' => 6,			/* 最大表示数 */
	);
	$postslist = new WP_Query( $args );
	ob_start();
	$x = 1;
	if ( $postslist->have_posts() ) : while ( $postslist->have_posts() ) : $postslist->the_post(); ?>
	<div class="tenpo_box">
		<p class="tenpo_check">
			<input type="radio" name="place" value="<?php the_title(); ?>" id="<?php the_title(); ?>"<? if($x == 1){echo ' checked';}?>>
			<label for="<?php the_title(); ?>">
<?php if( post_custom( 'tenpo_checkbox' ) <>'' ){	//項目が空白でなかったら表示
	echo post_custom( 'tenpo_checkbox' ); }?>
			</label>
		</p>
		<h4><?php the_title(); ?></h4>
		<p><?php printf( '%s', gr_get_image('tenpo_img')); ?></p>
		<p>
<?php if( post_custom( 'tenpo_address' ) <>'' ){	//項目が空白でなかったら表示
	echo '<span class="tenpo_add">住所</span>'.post_custom( 'tenpo_address' ) ;
}?>
		</p>
		<? if(post_custom( 'tenpo_map' )){
			echo '<p class="raiten_map">';
			echo post_custom( 'tenpo_map' );
			echo '</p>';
		}?>
	</div>
<? $x++;
	endwhile; endif; wp_reset_postdata();
	$output = ob_get_clean();
	return $output;
}
wpcf7_add_shortcode('tenpo_info', 'the_tenpo_info');

//土日開催リフォーム＆増改築相談会日程をショートコード
function seminar_date () {
	$args = array(
		'post_type' => 'seminar', 		/* 投稿タイプを指定 */
		'paged' => $paged,				/* ページ番号を指定 */
		'posts_per_page' => 1,			/* 最大表示数 */
	);
	$postslist = new WP_Query( $args );
	ob_start();
	if ( $postslist->have_posts() ) : while ( $postslist->have_posts() ) : $postslist->the_post();
	$seminar_date1 = post_custom( 'seminar_date1' );
	$seminar_date2 = post_custom( 'seminar_date2' );
	$seminar_date3 = post_custom( 'seminar_date3' );
	?>
<? if($seminar_date1){?>

	<label><input type="radio" name="date" value="<? echo $seminar_date1?>" checked><? echo $seminar_date1;?></label>
<? } ?>
<? if($seminar_date2){?>
	<label>
	<input type="radio" name="date" value="<? echo $seminar_date2?>"><? echo $seminar_date2;?></label>
<? } ?>
<? if($seminar_date3){?>
	<label>
	<input type="radio" name="date" value="<? echo $seminar_date3?>"><? echo $seminar_date3;?></label>
<? } ?>
	</label>
<? endwhile; endif; wp_reset_postdata();
	$output = ob_get_clean();
	return $output;
}

wpcf7_add_shortcode('seminar_date', 'seminar_date');

//画像リンクにlightboxのrelを付ける
add_filter('the_content', 'rellightbox_replace');
function rellightbox_replace ($content){
 global $post;
 $pattern = "/<a(.*?)href=('|\")(.*?).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>/i";
 $replacement = '<a$1href=$2$3.$4$5 rel="lightbox"$6>';
 $content = preg_replace($pattern, $replacement, $content);
 return $content;
}

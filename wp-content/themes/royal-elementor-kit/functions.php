<?php

/* 
** Sets up theme defaults and registers support for various WordPress features
*/
function royal_elementor_kit_setup() {

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title for us
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages
	add_theme_support( 'post-thumbnails' );

	// Custom Logo
	add_theme_support( 'custom-logo', [
		'height'      => 100,
		'width'       => 350,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	add_theme_support( 'custom-header' );

	// Add theme support for Custom Background.
	add_theme_support( 'custom-background', ['default-color' => ''] );

	// Set the default content width.
	$GLOBALS['content_width'] = 960;

	// This theme uses wp_nav_menu() in one location
	register_nav_menus( array(
		'main' => __( 'Main Menu', 'royal-elementor-kit' ),
	) );

	// Switch default core markup for search form, comment form, and comments to output valid HTML5
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Gutenberg Embeds
	add_theme_support( 'responsive-embeds' );

	// Gutenberg Widge Images
	add_theme_support( 'align-wide' );


	// WooCommerce in general.
	add_theme_support( 'woocommerce' );

	// zoom.
	add_theme_support( 'wc-product-gallery-zoom' );
	// lightbox.
	add_theme_support( 'wc-product-gallery-lightbox' );
	// swipe.
	add_theme_support( 'wc-product-gallery-slider' );
}

add_action( 'after_setup_theme', 'royal_elementor_kit_setup' );

/*
** Enqueue scripts and styles
*/
function royal_elementor_kit_scripts() {

	// Theme Stylesheet
	wp_enqueue_style( 'royal-elementor-kit-style', get_stylesheet_uri(), array(), '1.0' );

	// Comment reply link
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
}
add_action( 'wp_enqueue_scripts', 'royal_elementor_kit_scripts' );

/*
** Notices
*/
require_once get_parent_theme_file_path( '/inc/admin/activation/class-welcome-notice.php' );
require_once get_parent_theme_file_path( '/inc/admin/activation/class-rating-notice.php' );

add_action( 'after_switch_theme', 'rek_activation_time');
add_action('after_setup_theme', 'rek_activation_time');
    
function rek_activation_time() {
	if ( false === get_option( 'rek_activation_time' ) ) {
		add_option( 'rek_activation_time', strtotime('now') );
	}
}


/*
** Admin Menu
*/
require_once get_parent_theme_file_path( '/inc/admin/menu/rek-admin-menu.php' );

/*
** Customizer
*/
require_once get_parent_theme_file_path( '/inc/admin/customizer/customizer.php' );

add_filter( 'woocommerce_account_menu_items', function( $items ){
    unset($items['downloads']);
    return $items;
});
add_action('woocommerce_account_dashboard', 'picknprint_premium_dashboard', 5);

function picknprint_premium_dashboard() {

$user = wp_get_current_user();

echo '<h4 class="premium-welcome">Welcome back, '.$user->display_name.' 👋</h4>';

echo '<div class="premium-dashboard">';

$items = [
    [
        "title" => "My Orders",
        "icon" => "📦",
        "link" => wc_get_endpoint_url('orders')
    ],
    [
        "title" => "My Addresses",
        "icon" => "📍",
        "link" => wc_get_endpoint_url('edit-address')
    ],
    [
        "title" => "Account Details",
        "icon" => "👤",
        "link" => wc_get_endpoint_url('edit-account')
    ],
    [
        "title" => "Wishlist",
        "icon" => "❤️",
        "link" => site_url('/wishlist')
    ]
];


foreach($items as $item){
    echo '
    <a href="'.$item['link'].'" class="dashboard-card">
        <div class="card-icon">'.$item['icon'].'</div>
        <div class="card-title">'.$item['title'].'</div>
    </a>';
}

echo '</div>';
}
add_filter( 'the_title', 'limit_woocommerce_product_title', 10, 2 );

function limit_woocommerce_product_title( $title, $id ) {

    if ( is_admin() ) return $title;

    // Only frontend + mobile devices
    if ( ! wp_is_mobile() ) {
        return $title;
    }

    if ( get_post_type( $id ) === 'product' ) {

        $limit = 44;

        if ( mb_strlen( $title ) > $limit ) {
            $title = mb_substr( $title, 0, $limit ) . '...';
        }
    }

    return $title;
}

add_action('wp_footer', function () {

//     if (!is_cart()) return; // Only cart page

?>
<script>
(function () {

    if (!document.getElementById("wc-cart-lightbox")) {

        const lightbox = document.createElement("div");
        lightbox.id = "wc-cart-lightbox";
        lightbox.innerHTML = `
            <span class="wc-cart-lightbox-close">&times;</span>
            <img class="wc-cart-lightbox-img" src="">
        `;
        document.body.appendChild(lightbox);

        const style = document.createElement("style");
        style.innerHTML = `
            #wc-cart-lightbox{
                position:fixed;
                inset:0;
                background:rgba(0,0,0,.9);
                display:none;
                align-items:center;
                justify-content:center;
                z-index:999999;
            }
            #wc-cart-lightbox img{
                max-width:95%;
                max-height:95%;
                border-radius:8px;
            }
            .wc-cart-lightbox-close{
                position:absolute;
                top:25px;
                right:35px;
                font-size:40px;
                color:#fff;
                cursor:pointer;
            }
        `;
        document.head.appendChild(style);
    }

    const lightbox = document.getElementById("wc-cart-lightbox");
    const lightboxImg = lightbox.querySelector("img");

    document.addEventListener("click", function (e) {

        const img = e.target.closest(".variation-PersonalizedPreview img");
        if (!img) return;

        e.preventDefault();
        lightboxImg.src = img.src;
        lightbox.style.display = "flex";
    });

    lightbox.addEventListener("click", function (e) {
        if (e.target === lightbox) {
            lightbox.style.display = "none";
        }
    });

})();
</script>
<?php
});
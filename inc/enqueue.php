<?php
/**
 * Enqueue scripts and styles.
 */

$theme_data = wp_get_theme();
define( 'THEME_VERSION', $theme_data->Version );

function madimz_custom_scripts() {
    $should_load_default = true;
    wp_enqueue_style( 'madimz-style-min', get_template_directory_uri() . '/dist/css/style.min.css', array(), '1.0.1' );
    
    wp_enqueue_script( 'main', get_template_directory_uri() . '/dist/js/main.js', array(), '1.0.1', array( 'strategy' => 'defer' ) );

    // Enqueue ajax-scripts file
    wp_enqueue_script('madimz-ajax-scripts', get_template_directory_uri() . '/dist/js/ajax-scripts.js', array('jquery'),'',array( 'strategy' => 'defer' ) );

    wp_localize_script( 'madimz-ajax-scripts', 'ajax_obj', array( 
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'error_msg' => esc_html__('Error loading results.', 'madimz')
    ) );

    if ( is_front_page() ) {
        wp_enqueue_style( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.css', array() ,'12.0.3');
        wp_enqueue_style( 'homepage-min', get_template_directory_uri() . '/dist/css/homepage.min.css', array(), '1.0.0' );
        wp_enqueue_script( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.js', array(), '12.0.3', array( 'strategy' => 'defer' ) );
        wp_enqueue_script( 'homepage-js', get_template_directory_uri() . '/dist/js/homepage.js', array( 'swiper-bundle' ), '1.0.0', array( 'strategy' => 'defer' ) );
        $should_load_default = false;
    }

    if ( ( function_exists( 'is_product_category' ) && ( is_product_taxonomy() || is_shop() ) ) ) {
        wp_enqueue_style( 'product-category', get_template_directory_uri() . '/dist/css/product-category.min.css', array(), '1.0.0' );
        // wp_enqueue_style( 'wishlist-min', get_template_directory_uri() . '/dist/css/wishlist.min.css', array(), '1.0.0' );
        wp_enqueue_script( 'single-product', get_template_directory_uri() . '/dist/js/single.product.js', array(  ), '1.0.0', array( 'strategy' => 'defer' ) );
        $should_load_default = false;
    }

    if ( is_product() ) {

        wp_enqueue_style( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.css', array() ,'12.0.3');
        wp_enqueue_style( 'single-product', get_template_directory_uri() . '/dist/css/single-product.min.css', array(), '1.0.0' );
        wp_enqueue_script( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.js', array(), '12.0.3', array( 'strategy' => 'defer' ) );
        wp_enqueue_script( 'single-product', get_template_directory_uri() . '/dist/js/single.product.js', array( 'jquery', 'swiper-bundle' ), '1.0.0', array( 'strategy' => 'defer' ) );
    
        $should_load_default = false;

    }
    
    if ( is_cart() ){
        wp_enqueue_style( 'cart-min', get_template_directory_uri() . '/dist/css/cart.min.css', array(), '1.0.0' );
        wp_enqueue_script('wc-cart');
    }

    if ( is_page_template( 'default' ) && $should_load_default  ) {
        wp_enqueue_style( 'madimz-page', get_template_directory_uri() . '/dist/css/page.min.css', array(), '1.0.5' );
    }

    if( (function_exists( 'is_account_page' ) && is_account_page())) {
        wp_enqueue_style( 'my-account', get_template_directory_uri() . '/dist/css/my-account.min.css', array(), '1.0.0' );
        $should_load_default = false;
    }

    // location page
    if(is_page_template( 'page-templates/location.php' )){
        wp_enqueue_style( 'location-min', get_template_directory_uri() . '/dist/css/location.min.css', array(), '1.0.0' );
        $should_load_default = false;
    }


}
add_action( 'wp_enqueue_scripts', 'madimz_custom_scripts' );


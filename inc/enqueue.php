<?php
/**
 * Enqueue scripts and styles.
 */

$theme_data = wp_get_theme();
define( 'THEME_VERSION', $theme_data->Version );

function madimz_custom_scripts() {
    $should_load_default = true;
    wp_enqueue_style( 'madimz-style-min', get_template_directory_uri() . '/dist/css/style.min.css', array(), _S_VERSION );
    
    wp_enqueue_script( 'main', get_template_directory_uri() . '/dist/js/main.js', array(), _S_VERSION, array( 'strategy' => 'defer' ) );

    // Enqueue ajax-scripts file
    wp_enqueue_script('madimz-ajax-scripts', get_template_directory_uri() . '/dist/js/ajax-scripts.js', array('jquery'), '', array( 'strategy' => 'defer' ) );

    wp_localize_script( 'madimz-ajax-scripts', 'ajax_obj', array( 
        'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        'error_msg' => esc_html__('Error loading results.', 'madimz'),
        'thank_you_url' => site_url('/tickets-success/')
    ) );

    if ( is_front_page() ) {
        wp_enqueue_style( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.css', array() ,'12.0.3');
        wp_enqueue_style( 'homepage-min', get_template_directory_uri() . '/dist/css/homepage.min.css', array(), _S_VERSION );
        wp_enqueue_script( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.js', array(), '12.0.3', array( 'strategy' => 'defer' ) );
        wp_enqueue_script( 'homepage-js', get_template_directory_uri() . '/dist/js/homepage.js', array( 'swiper-bundle' ), _S_VERSION, array( 'strategy' => 'defer' ) );
        $should_load_default = false;
    }

    if ( ( function_exists( 'is_product_category' ) && ( is_product_taxonomy() || is_shop() ) ) ) {
        wp_enqueue_style( 'product-category', get_template_directory_uri() . '/dist/css/product-category.min.css', array(), _S_VERSION );
        // wp_enqueue_style( 'wishlist-min', get_template_directory_uri() . '/dist/css/wishlist.min.css', array(), '1.0.0' );
        wp_enqueue_script( 'single-product', get_template_directory_uri() . '/dist/js/single.product.js', array(  ), _S_VERSION, array( 'strategy' => 'defer' ) );
        $should_load_default = false;
    }

    if ( is_product() ) {
        wp_enqueue_script('wc-single-product');

        wp_enqueue_style( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.css', array() ,'12.0.3');
        wp_enqueue_style( 'single-product', get_template_directory_uri() . '/dist/css/single-product.min.css', array(), _S_VERSION );
        wp_enqueue_script( 'swiper-bundle', get_template_directory_uri() . '/dist/lib/swiper/swiper-bundle.min.js', array(), '12.0.3', array( 'strategy' => 'defer' ) );
        wp_enqueue_script( 'single-product', get_template_directory_uri() . '/dist/js/single.product.js', array( 'jquery', 'swiper-bundle' ), _S_VERSION, array( 'strategy' => 'defer' ) );
        $should_load_default = false;
    }

    // cart page
    if ( function_exists( 'is_cart' ) && is_cart() ) {
        wp_enqueue_style( 'cart-min', get_template_directory_uri() . '/dist/css/cart.min.css', array(), _S_VERSION );
        $should_load_default = false;
    }

    //checkout page
    if ( is_checkout() ) {
        wp_enqueue_style( 'checkout-min', get_template_directory_uri() . '/dist/css/checkout.min.css', array(), _S_VERSION );
        $should_load_default = false;
    }

    if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
        wp_enqueue_script('wc-add-to-cart-variation');
        wp_enqueue_script('wc-single-product');
    }

    if ( ! wp_script_is( 'wc-cart-fragments', 'enqueued' ) && wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
        // Enqueue the 'wc-cart-fragments' script
        wp_enqueue_script( 'wc-cart-fragments' );
    }

    // my-account page
    if ( (function_exists( 'is_account_page' ) && is_account_page())) {
        wp_enqueue_style( 'my-account', get_template_directory_uri() . '/dist/css/my-account.min.css', array(), _S_VERSION );
        $should_load_default = false;
    }
    
    // location page
    if ( is_page_template( 'page-templates/location.php' ) ){
        wp_enqueue_style( 'location-min', get_template_directory_uri() . '/dist/css/location.min.css', array(), _S_VERSION );
        $should_load_default = false;
    }

    // Contact page
    if ( is_page_template( 'page-templates/contact.php' )){
        wp_enqueue_style( 'location-min', get_template_directory_uri() . '/dist/css/contact.min.css', array(), _S_VERSION );
        $should_load_default = false;
    }
    
    // Wishlist page
    if ( is_page_template( 'page-templates/wishlist.php' )){
        wp_enqueue_style( 'wishlist-min', get_template_directory_uri() . '/dist/css/wishlist.min.css', array(), _S_VERSION );

        // Force loading the wishlist script on this page
        wp_enqueue_script( 'madimz-wishlist-js' );
        $should_load_default = false;
    }
    
    if ( ( is_page_template( 'default' ) && $should_load_default )  || ( is_page_template( 'page-templates/tickets.php' ) && $should_load_default ) ) {
        wp_enqueue_style( 'madimz-page', get_template_directory_uri() . '/dist/css/page.min.css', array(), _S_VERSION );
    }

}
add_action( 'wp_enqueue_scripts', 'madimz_custom_scripts' );
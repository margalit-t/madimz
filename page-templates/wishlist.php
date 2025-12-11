<?php

/*
Template Name: Wishlist
*/

get_header();

use Automattic\WooCommerce\Enums\ProductStatus;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If $wishlist wasn't passed in (normal page template), get it from the class.
if ( ! isset( $wishlist ) || ! is_array( $wishlist ) ) {
    if ( class_exists( 'MadimzWishlist' ) ) {
        $wishlist = MadimzWishlist::get_wishlist();
    } else {
        $wishlist = array();
    }
}

// Clean up the list
$wishlist = array_values( array_filter( array_map( 'absint', (array) $wishlist ) ) );

if( empty( $wishlist ) ) {
    wc_add_notice( __( 'רשימת המשאלות שלך ריקה', 'madimz' ), 'notice' );
}

// Show notices (empty or not)
woocommerce_output_all_notices();

if( empty( $wishlist ) ) {
    echo '<div class="btn-return-home">';
        echo '<a href="' . home_url() . '" class="red-btn return-homepage" title="חזור לדף הבית" aria-label="Back to home page">';
            echo esc_html( 'חזרה לדף הבית', 'madimz' );
        echo '</a>';
    echo '</div>';
    // Still show footer and layout even when empty
    get_template_part( 'template-parts/form', 'footer' );
    get_footer();
    return;
}

// Respect WooCommerce visibility and meta queries
$meta_query = function_exists( 'WC' ) ? WC()->query->get_meta_query() : array();
$tax_query  = function_exists( 'WC' ) ? WC()->query->get_tax_query()  : array();

$args = array(
    'post_type'             => 'product',
    'post_status'           => ProductStatus::PUBLISH,
    'ignore_sticky_posts'   => 1,
    'nopaging'              => true,
    'post__in'              => $wishlist,
    'orderby'               => 'post__in',  // keep given order
    'meta_query'            => $meta_query,
    'tax_query'             => $tax_query,
);

$q = new WP_Query( $args );

// Set number of columns in loop
wc_set_loop_prop( 'columns', 4 );

if ( $q->have_posts() ) {
    // do_action( 'woocommerce_before_shop_loop' );

    $count = 0;
    if ( ! empty( $wishlist ) ) : 
        echo '<div class="wishlist-count-wrapper wishlist-actions">';
            $count = count( $wishlist );
            echo '<span class="counter">' .  
                sprintf(
                    __( '%s פריטים שמורים', 'madimz' ),
                    esc_html( $count )
                ) .
            '</span>';
            echo '<button class="wishlist-clear" data-nonce="' . wp_create_nonce( 'madimz-wishlist' ) . '" >';
                esc_html_e( 'רוקן רשימה', 'madimz' );
            echo '</button>';
        echo '</div>';
    endif;
    

    woocommerce_product_loop_start();

    while ( $q->have_posts() ) {
        $q->the_post();

        // Standard product card template used by the shop/archive
        wc_get_template_part( 'content', 'product' );
    }

    woocommerce_product_loop_end();

    // do_action( 'woocommerce_after_shop_loop' );
} else {
    do_action( 'woocommerce_no_products_found' );
}

wp_reset_postdata();

// Reset columns back to default
wc_set_loop_prop( 'columns', null );

get_template_part('template-parts/form', 'footer');
get_footer();
<?php 

/****
 *   Render ACF fields inside variation edit rows
 */

// Add custom fields right after the stock section inside each variation
add_action( 'woocommerce_variation_options_inventory', 'custom_variation_fields_after_stock', 20, 3 );

function custom_variation_fields_after_stock( $loop_index, $variation_data, $variation ) {

    $manage_stock = get_post_meta( $variation->ID, '_manage_stock', true );

    if ( $manage_stock === 'yes' ) {

        woocommerce_wp_text_input( [
            'id'          => '_order_priority[' . $variation->ID . ']',
            'class'       => 'short',
            'label'       => __( 'purchase orders priority', 'woocommerce' ),
            'value'       => get_post_meta( $variation->ID, '_order_priority', true ),
            'desc_tip'    => true,
            'description' => 'Open purchase orders for a priority product',
        ] );
    
        woocommerce_wp_text_input( [
            'id'          => '_more_order_priority[' . $variation->ID . ']',
            'class'       => 'short',
            'label'       => __( 'more order priority', 'woocommerce' ),
            'value'       => get_post_meta( $variation->ID, '_more_order_priority', true ),
            'desc_tip'    => true,
            'description' => 'Additional orders from Priority',
        ] );
    }
}

// Save ACF fields for each variation
add_action( 'woocommerce_save_product_variation', 'save_custom_variation_field', 10, 2 );
function save_custom_variation_field( $variation_id, $loop ) {
    if ( isset( $_POST['_order_priority'][$variation_id] ) ) {
        update_post_meta( $variation_id, '_order_priority', sanitize_text_field( $_POST['_order_priority'][$variation_id] ) );
    }

    if ( isset( $_POST['_more_order_priority'][$variation_id] ) ) {
        update_post_meta( $variation_id, '_more_order_priority', sanitize_text_field( $_POST['_more_order_priority'][$variation_id] ) );
    }
}

// Store custom field value into variation data
add_filter( 'woocommerce_available_variation', 'add_custom_field_variation_data' );
function add_custom_field_variation_data( $variations ) {
    $variations['order_priority'] = get_post_meta( $variations['variation_id'], '_order_priority', true );
    $variations['more_order_priority'] = get_post_meta( $variations['variation_id'], '_more_order_priority', true );
    return $variations;
}
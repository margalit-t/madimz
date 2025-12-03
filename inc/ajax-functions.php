<?php 

/** === AJAX SEARCH === **/
add_action('wp_ajax_custom_ajax_search', 'custom_ajax_search');
add_action('wp_ajax_nopriv_custom_ajax_search', 'custom_ajax_search');

function custom_ajax_search() {
    if ( empty($_POST['search']) ) {
        wp_send_json([]);
    }

    global $wpdb;

    // חשוב: unslash + sanitize
    $search = sanitize_text_field( wp_unslash( $_POST['search'] ) );

    // מינימום 2 תווים
    if ( mb_strlen( $search ) < 2 ) {
        wp_send_json([]);
    }

    $like = '%' . $wpdb->esc_like( $search ) . '%';

    // 1) חיפוש לפי כותרת
    $ids_from_title = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT ID
             FROM {$wpdb->posts}
             WHERE post_type IN ('product', 'product_variation')
               AND post_status = 'publish'
               AND post_title LIKE %s
             LIMIT 30",
            $like
        )
    );

    // 2) חיפוש לפי מק״ט (SKU)
    $ids_from_sku = $wpdb->get_col(
        $wpdb->prepare(
            "SELECT post_id
             FROM {$wpdb->postmeta}
             WHERE meta_key IN ('_sku', '_variable_sku')
               AND meta_value LIKE %s
             LIMIT 30",
            $like
        )
    );

    // מיזוג מזהים
    $post_ids = array_unique( array_filter( array_merge( $ids_from_title, $ids_from_sku ) ) );

    // המרת וריאציות → למוצר אב
    $final_ids = [];

    foreach ( $post_ids as $pid ) {
        $post = get_post( $pid );
        if ( ! $post ) continue;

        if ( $post->post_type === 'product_variation' ) {
            if ( $post->post_parent ) {
                $final_ids[] = $post->post_parent;
            }
        } else {
            $final_ids[] = $pid;
        }
    }

    // הסרת כפולים
    $final_ids = array_unique( $final_ids );

    if ( empty($final_ids) ) {
        wp_send_json([]);
    }

    // הגבלת תוצאות
    $final_ids = array_slice( $final_ids, 0, 10 );

    $results = [];

    foreach ( $final_ids as $_id ) {
        $product = wc_get_product( $_id );
        if ( ! $product ) continue;

        $image_id = $product->get_image_id();

        $results[] = [
            'id'    => $_id,
            'title' => $product->get_name(),
            'link'  => get_permalink( $_id ),
            'image' => wp_get_attachment_image_url( $image_id, 'thumbnail' ) ?: wc_placeholder_img_src(),
        ];
    }

    wp_send_json( $results );
}




?>
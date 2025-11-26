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

    // 1) תוצאות לפי כותרת (שם מוצר) - כולל products + variations
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

    // 2) תוצאות לפי מק״ט (SKU / variable sku)
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

    // מיזוג + ייחוד מזהים
    $post_ids = array_unique( array_filter( array_merge( $ids_from_title, $ids_from_sku ) ) );

    if ( empty( $post_ids ) ) {
        wp_send_json([]);
    }

    // נגביל ל-10 תוצאות
    $post_ids = array_slice( $post_ids, 0, 10 );

    $results = [];

    foreach ( $post_ids as $post_id ) {
        $product = wc_get_product( $post_id );
        if ( ! $product ) {
            continue;
        }

        // אם זה וריאציה – להציג את מוצר האב, כמו שסיכמנו

        $image_id = $product->get_image_id();
        $title    = $product->get_name();
        $link     = get_permalink( $post_id );
        

        $results[] = [
            'id'    => $post_id,
            'title' => $title,
            'link'  => $link,
            'image' => wp_get_attachment_image_url( $image_id, 'thumbnail' ) ?: wc_placeholder_img_src(),
        ];
    }

    wp_send_json( $results );
}



?>
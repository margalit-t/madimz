<?php 

    if ( ! defined( 'ABSPATH' ) ) exit;

    if (!is_product_category()) return;
?>

<!-- Filter sidebar on category page -->
<div class="filters">
    <div class="selected-filters">
        <h3> <?php esc_html_e( 'בחרת לסנן לפי: ', 'madimz' ); ?></h3>

        <?php 
            // 1. Add current category
            $term = get_queried_object();
            $parent_url = madimz_get_parent_category_url();
            echo madimz_filter_tag($term->name, $parent_url);
            // echo madimz_filter_tag($term->name, remove_query_arg(['manufacturer','pricerange']));

            // 2. Manufacturer tag
            if (!empty($_GET['manufacturer'])) {
                $url = remove_query_arg('manufacturer');
                echo madimz_filter_tag($_GET['manufacturer'], $url);
            }

            // 3. Price range tag
            if (!empty($_GET['pricerange'])) {
                $url = remove_query_arg('pricerange');
                echo madimz_filter_tag(madimz_pr_label($_GET['pricerange']), $url);
            }
        ?>
    </div>

    <?php 
        global $wpdb;
        $manufacturers = $wpdb->get_col("
            SELECT DISTINCT meta_value
            FROM wp_postmeta
            WHERE meta_key = 'manufacturer'
            AND meta_value IS NOT NULL
            AND meta_value != ''
        ");
    ?>

    <div class="filter-options">
        <!-- manufacturer filter -->
        <div class="filter-section manufacturer-filter">
            <h4><?php esc_html_e( 'יצרן', 'madimz' ); ?></h4>
            <ul>
                <?php foreach ( $manufacturers as $man ) : ?>
                    <!-- Skip if already selected -->
                    <?php if (!empty($_GET['manufacturer']) && $_GET['manufacturer'] == $man) continue; ?>
                    <?php 
                    $count = madimz_count_products([
                        [ 'key' => 'manufacturer', 'value' => $man ]
                    ]);

                    if ( $count > 0 ) :
                        $url = add_query_arg( 'manufacturer', urlencode( $man ) );
                        ?>
                        <li>
                            <a href="<?php echo esc_url($url); ?>"><?php echo $man . ' (' . $count . ')'; ?></a>
                        </li>
                    <?php else : ?>
                        <span class="manufacturer-name disabled"><?php echo esc_html( $man ) . ' (' . $count . ')'; ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Price ranges filter -->
        <div class="filter-section price-filter">
            <h4><?php esc_html_e( 'טווח מחירים', 'madimz' ); ?></h4>
            <ul>
                <?php 
                    if (have_rows('price_ranges', $term)) :
                        while (have_rows('price_ranges', $term)) : the_row();
                            $label = get_sub_field('price_label');
                            $min   = (int)get_sub_field('price_min');
                            $max   = (int)get_sub_field('price_max');

                            $key = $min . '-' . $max;

                            // Skip if already selected
                            if (!empty($_GET['pricerange']) && $_GET['pricerange']==$key) continue;

                            // Count products
                            $count = madimz_count_products([
                                [ 'key' => '_price', 'type' => 'NUMERIC', 'compare' => 'BETWEEN', 'value' => [ $min, $max ] ]
                            ]);

                            if ( $count > 0 ) :

                                $url = add_query_arg('pricerange', $key);
                                ?>

                                <li>
                                    <a href="<?php echo esc_url($url); ?>"><?php echo $label . ' (' . $count . ')'; ?></a>
                                </li>
                            <?php else : ?>
                                <span class="price_range disabled"><?php echo esc_html( $label ) . ' (' . $count . ')'; ?></span>
                            <?php endif; ?>
                        <?php
                        endwhile;
                    endif;
                ?>
            </ul>
        </div>
    </div>
</div>

<?php

// Get parent category URL
function madimz_get_parent_category_url() {
    if (!is_product_category()) return home_url('/');

    $term = get_queried_object();

    // If already top level → go to shop page
    if ($term->parent == 0) {
        return get_permalink( wc_get_page_id('shop') );
    }

    // Otherwise return parent link
    $parent = get_term($term->parent, 'product_cat');
    return get_term_link($parent);
}

// Count products based on meta query
function madimz_count_products($meta_query_parts) {
    if (!is_product_category()) return 0;

    $current_cat = get_queried_object();

    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'fields' => 'ids',
        'nopaging' => true,

        // Search ONLY within the current category
        'tax_query' => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $current_cat->term_id,
                'operator' => 'IN',
                'include_children' => false,  // IMPORTANT!
            ]
        ],

        'meta_query' => array_merge([
            ['key' => '_price', 'compare' => 'EXISTS']
        ], $meta_query_parts),
    ];

    $p = new WP_Query($args);
    return $p->found_posts;
}

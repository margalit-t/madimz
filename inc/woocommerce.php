<?php
/**
 * WooCommerce Compatibility File
 *
 * @link https://woocommerce.com/
 *
 * @package madimz
 */

/**
 * WooCommerce setup function.
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)
 * @link https://github.com/woocommerce/woocommerce/wiki/Declaring-WooCommerce-support-in-themes
 *
 * @return void
 */
function madimz_woocommerce_setup() {
	add_theme_support(
		'woocommerce',
		array(
			'gallery_thumbnail_image_width' => 78,
			'thumbnail_image_width' => 150,
			'single_image_width'    => 300,
			'product_grid'          => array(
				'default_rows'    => 3,
				'min_rows'        => 1,
				'default_columns' => 4,
				'min_columns'     => 1,
				'max_columns'     => 6,
			),
		)
	);
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	add_image_size( 'product_slider', 300, 300, false );
}
add_action( 'after_setup_theme', 'madimz_woocommerce_setup' );

//Setting up images without cropping and maintaining proportions
add_filter( 'woocommerce_get_image_size_thumbnail', function( $size ) {
	return array(
		'width'  => 300,
		'height' => 0, 
		'crop'   => false,
	);
});

add_action( 'init', function () {
	// we don't need this sidebar at the moment
	remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );
} );

/**
 * WooCommerce specific scripts & stylesheets.
 *
 * @return void
 */
function madimz_woocommerce_scripts() {
	wp_enqueue_style( 'madimz-woocommerce-style', get_template_directory_uri() . '/woocommerce.css', array(), _S_VERSION );

	$font_path   = WC()->plugin_url() . '/assets/fonts/';
	$inline_font = '@font-face {
			font-family: "star";
			src: url("' . $font_path . 'star.eot");
			src: url("' . $font_path . 'star.eot?#iefix") format("embedded-opentype"),
				url("' . $font_path . 'star.woff") format("woff"),
				url("' . $font_path . 'star.ttf") format("truetype"),
				url("' . $font_path . 'star.svg#star") format("svg");
			font-weight: normal;
			font-style: normal;
		}';

	wp_add_inline_style( 'madimz-woocommerce-style', $inline_font );
}
add_action( 'wp_enqueue_scripts', 'madimz_woocommerce_scripts' );

/**
 * Disable the default WooCommerce stylesheet.
 *
 * Removing the default WooCommerce stylesheet and enqueing your own will
 * protect you during WooCommerce core updates.
 *
 * @link https://docs.woocommerce.com/document/disable-the-default-stylesheet/
 */
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

/**
 * Add 'woocommerce-active' class to the body tag.
 *
 * @param  array $classes CSS classes applied to the body tag.
 * @return array $classes modified to include 'woocommerce-active' class.
 */
function madimz_woocommerce_active_body_class( $classes ) {
	$classes[] = 'woocommerce-active';

	return $classes;
}
add_filter( 'body_class', 'madimz_woocommerce_active_body_class' );

/**
 * Related Products Args.
 *
 * @param array $args related products args.
 * @return array $args related products args.
 */
function madimz_woocommerce_related_products_args( $args ) {
	$defaults = array(
		'posts_per_page' => 3,
		'columns'        => 3,
	);

	$args = wp_parse_args( $defaults, $args );

	return $args;
}
add_filter( 'woocommerce_output_related_products_args', 'madimz_woocommerce_related_products_args' );

/**
 * Remove default WooCommerce wrapper.
 */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );

if ( ! function_exists( 'madimz_woocommerce_wrapper_before' ) ) {
	/**
	 * Before Content.
	 *
	 * Wraps all WooCommerce content in wrappers which match the theme markup.
	 *
	 * @return void
	 */
	function madimz_woocommerce_wrapper_before() {
		?>
			<main id="primary" class="site-main">
		<?php
	}
}
add_action( 'woocommerce_before_main_content', 'madimz_woocommerce_wrapper_before' );

if ( ! function_exists( 'madimz_woocommerce_wrapper_after' ) ) {
	/**
	 * After Content.
	 *
	 * Closes the wrapping divs.
	 *
	 * @return void
	 */
	function madimz_woocommerce_wrapper_after() {
		?>
			</main><!-- #main -->
		<?php
	}
}
add_action( 'woocommerce_after_main_content', 'madimz_woocommerce_wrapper_after' );

/**
 * Sample implementation of the WooCommerce Mini Cart.
 *
 * You can add the WooCommerce Mini Cart to header.php like so ...
 *
	<?php
		if ( function_exists( 'madimz_woocommerce_header_cart' ) ) {
			madimz_woocommerce_header_cart();
		}
	?>
 */

if ( ! function_exists( 'madimz_woocommerce_cart_link_fragment' ) ) {
	/**
	 * Cart Fragments.
	 *
	 * Ensure cart contents update when products are added to the cart via AJAX.
	 *
	 * @param array $fragments Fragments to refresh via AJAX.
	 * @return array Fragments to refresh via AJAX.
	 */
	function madimz_woocommerce_cart_link_fragment( $fragments ) {
		ob_start();
		madimz_woocommerce_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();

		return $fragments;
	}
}
add_filter( 'woocommerce_add_to_cart_fragments', 'madimz_woocommerce_cart_link_fragment' );

if ( ! function_exists( 'madimz_woocommerce_cart_link' ) ) {
	/**
	 * Cart Link.
	 *
	 * Displayed a link to the cart including the number of items present and the cart total.
	 *
	 * @return void
	 */
	function madimz_woocommerce_cart_link() {
		?>
		<a class="cart-contents" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'madimz' ); ?>">
			<?php
			$item_count_text = sprintf(
				/* translators: number of items in the mini cart. */
				_n( '%d item', '%d items', WC()->cart->get_cart_contents_count(), 'madimz' ),
				WC()->cart->get_cart_contents_count()
			);
			?>
			<span class="amount"><?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?></span> <span class="count"><?php echo esc_html( $item_count_text ); ?></span>
		</a>
		<?php
	}
}

if ( ! function_exists( 'madimz_woocommerce_header_cart' ) ) {
	/**
	 * Display Header Cart.
	 *
	 * @return void
	 */
	function madimz_woocommerce_header_cart() {
		if ( is_cart() ) {
			$class = 'current-menu-item';
		} else {
			$class = '';
		}
		?>
		<ul id="site-header-cart" class="site-header-cart">
			<li class="<?php echo esc_attr( $class ); ?>">
				<?php madimz_woocommerce_cart_link(); ?>
			</li>
			<li>
				<?php
				$instance = array(
					'title' => '',
				);

				the_widget( 'WC_Widget_Cart', $instance );
				?>
			</li>
		</ul>
		<?php
	}
}

//remove select option inside product box
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );

//Add a variation color selector after the title inside the product box
function madimz_show_color_variations_in_loop() {
    global $product;

    if ( ! $product || ! $product->is_type( 'variable' ) ) {
		echo '<div class="product-color-variations"></div>';
        return;
    }

    $available_variations = $product->get_available_variations();
    $colors = [];

    foreach ( $available_variations as $variation ) {
        if ( isset( $variation['attributes']['attribute_pa_color'] ) ) {
            $colors[] = $variation['attributes']['attribute_pa_color'];
        }
    }

    $colors = array_unique( $colors );
    if ( empty( $colors ) ) {
        return;
    }

    $max_show = 5; 

    echo '<div class="product-color-variations">';
    echo '<div class="color-dots">';

    $counter = 0;
    foreach ( $colors as $color ) {
		$label = urldecode( $color );
		switch ( $label ) {
			case 'כתום':
				$hex = '#fc7b22';
				break;
			case 'צהוב-זוהר':
				$hex = '#d2ff00';
				break;
			case 'לבן':
				$hex = '#ffffff';
				break;
			case 'שחור':
				$hex = '#000000';
				break;
			default:
       			$hex = '#ffffff';
		}

        // if ( $counter < $max_show ) {
            echo '<span class="color-dot" style="background-color:' . $hex . '" data-color="' . esc_attr( $label ) . '"></span>';
        // } 
        $counter++;
    }

	echo '</div>';

    if ( count( $colors ) > $max_show ) {
        echo '<span class="color-more">' . inline_svg_with_class('plus.svg', '') . '</span>';
    }

    echo '</div>';
}
add_action( 'woocommerce_after_shop_loop_item_title', 'madimz_show_color_variations_in_loop', 15 );

//check if the current category has a parent.
function madimz_is_parent_category() {
    if ( is_product_category() ) {
        $term = get_queried_object();
        return $term && $term->parent == 0;
    }
    return false;
}

function madimz_parent_layout_start() {
	if ( madimz_is_parent_category() ) {
		echo '<div class="parent-category-layout">';
    } else {
		echo '<div class="child-category-layout">';
	}
}
add_action('woocommerce_before_main_content', 'madimz_parent_layout_start', 5);

function madimz_parent_layout_end() {
	get_template_part('template-parts/form', 'footer');
	echo '</div>'; // close parent-category-layout
}
add_action('woocommerce_after_main_content', 'madimz_parent_layout_end', 50);

//Show child categories in archive category
function madimz_insert_sidebar_after_products() {
	if ( ! madimz_is_parent_category() ) return;

	$term = get_queried_object();

	// Only on parent categories
	if ( $term && $term->parent == 0 ) {
		$children = get_terms( [
			'taxonomy'   => 'product_cat',
			'parent'     => $term->term_id,
			'hide_empty' => false, // show even empty categories
			'orderby'    => 'name',
			'order'      => 'ASC',
		] );

		if ( ! empty( $children ) && ! is_wp_error( $children ) ) {
			echo '<div class="child-category-list">';
			echo '<ul>';
			foreach ( $children as $child ) {
				$child_link = get_term_link( $child );
				echo '<li><a href="' . esc_url( $child_link ) . '">' . esc_html( $child->name ) . '</a></li>';
			}
			echo '</ul>';
			echo '</div>';
		}
	}
}
add_action( 'woocommerce_before_shop_loop', 'madimz_insert_sidebar_after_products', 15 );

// Display subcategories (Parent Page)
function madimz_parent_category_content() {
	if ( madimz_is_parent_category() ) {
		remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_output_all_notices', 10);
        remove_action('woocommerce_shop_loop', 'woocommerce_product_loop_start', 10);
        remove_action('woocommerce_shop_loop', 'woocommerce_product_loop_end', 40);
    }
}
add_action('woocommerce_before_shop_loop', 'madimz_parent_category_content', 5);

//Show products by category + sidebar filter
function madimz_child_category_layout_start() {
	if ( ! madimz_is_parent_category() || is_shop() ) : ?>
		<div class="child-shop-layout">
			<aside class="shop-sidebar selected-filters">
				<?php get_template_part('template-parts/filters', 'sidebar'); ?>
			</aside>

			<?php
				global $wp_query;
				$total = $wp_query->found_posts;

				// Get current orderby and order
				$current_orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'title';
				$current_order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : 'asc';

				// Determine toggle order (so clicking again reverses)
				$toggle_order = ( $current_order === 'asc' ) ? 'desc' : 'asc';

				// Build URLs
				$base_url = remove_query_arg([ 'orderby', 'order', 'paged', 'manufacturer', 'pricerange' ]);

				$name_url  = add_query_arg( array_merge($_GET, [
					'orderby' => 'title',
					'order' => $toggle_order
				]), $base_url );
				$price_url = add_query_arg( [ 'orderby' => 'price', 'order' => $toggle_order ], $base_url );
			?>

			<div class="shop-main">
				<div class="shop-sorting-wrapper">
					<div class="results-count">
						<?php esc_html_e( 'תוצאות:&nbsp;', 'madimz' ); ?> 
						<strong><?php echo esc_html( $total ); ?> </strong>
					</div>
					<div class="sorting-options">
						<span><?php esc_html_e( 'מיין לפי:', 'madimz' ); ?></span>
						<a href="<?php echo esc_url( $name_url ); ?>" class="sort-link <?php echo ( $current_orderby === 'title' ? ' active ' . esc_attr( $current_order ) : '' ); ?>">
							<?php esc_html_e( 'שם', 'madimz' ); ?>
							<div class="triangle"></div>
						</a>
						&nbsp;/&nbsp;
						<a href="<?php echo esc_url( $price_url ); ?>" class="sort-link <?php echo ( $current_orderby === 'price' ? ' active ' . esc_attr( $current_order ) : '' );?> ">
							<?php esc_html_e( 'מחיר', 'madimz' ); ?>
							<div class="triangle"></div>
						</a>
					</div>
				</div>
    <?php endif;
}
add_action('woocommerce_before_shop_loop', 'madimz_child_category_layout_start', 5);

// Remove default 
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

function madimz_child_category_layout_end() {
	if ( ! madimz_is_parent_category() || is_shop() ) {
		echo '</div>'; // close .shop-main
		echo '</div>'; // close .child-shop-layout
    }
}
add_action('woocommerce_after_shop_loop', 'madimz_child_category_layout_end', 50);

//handle name/price sorting 
function madimz_custom_orderby_args( $args ) {
    if ( isset( $_GET['orderby'] ) ) {
        switch ( $_GET['orderby'] ) {
            case 'title':
                $args['orderby'] = 'title';
                $args['order']   = ( isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ) ? 'DESC' : 'ASC';
                $args['meta_key'] = '';
                break;

            case 'price':
                $args['orderby'] = 'meta_value_num';
                $args['meta_key'] = '_price';
                $args['order']   = ( isset($_GET['order']) && strtolower($_GET['order']) === 'desc' ) ? 'DESC' : 'ASC';
                break;
        }
    }
    return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'madimz_custom_orderby_args' );

function madimz_apply_filters_to_query($q) {

    if (!is_product_category()) return;

    $current_cat = get_queried_object();
	$meta_query = [];

    // Always restrict to THIS category only
    $tax_query = [
        [
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $current_cat->term_id,
            'operator' => 'IN',
            'include_children' => true,
        ]
    ];

    // Manufacturer filter
    if (!empty($_GET['manufacturer'])) {
        $meta_query[] = [
            'key'     => 'manufacturer',
            'value'   => sanitize_text_field($_GET['manufacturer']),
            'compare' => '='
        ];
    }

    // Price range filter
    if (!empty($_GET['pricerange'])) {

        list($min, $max) = explode('-', sanitize_text_field($_GET['pricerange']));

        $meta_query[] = [
            'key'     => '_price',
            'value'   => [(float)$min, (float)$max],
            'type'    => 'NUMERIC',
            'compare' => 'BETWEEN'
        ];
    }

    // Apply meta query only if needed
    if (!empty($meta_query)) {
        $q->set('meta_query', $meta_query);
    }

    // Apply category restriction
    $q->set('tax_query', $tax_query);
}
add_action('woocommerce_product_query', 'madimz_apply_filters_to_query');

// render small selected filter tag
function madimz_filter_tag($label, $remove_url) {
	return '<div class="filter-tag">
		<span>' . esc_html($label) . '</span>
		<a href="' . esc_url($remove_url) . '" class="remove-tag">' . inline_svg_with_class('x.svg', '') . '</a>
	</div>';
}

// turn "0-20" into label
function madimz_pr_label($range) {
	list($min,$max) = explode('-', $range);
	if ( $min==0 ) return "עד $max ₪";
	if ( $max == 999999 ) return "מעל $min ₪";
	return "$min עד $max ₪";
}

/***
 * Single Product
 */

// Remove short description from original location
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
// Remove price
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
// Remove meta data
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );

// Remove default variation dropdowns
remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation', 10 );
remove_action( 'woocommerce_before_single_variation', 'woocommerce_variable_add_to_cart', 30 );

// Add a long description after the name
add_action( 'woocommerce_single_product_summary', 'custom_single_product_long_description', 10 );
function custom_single_product_long_description() {
    global $post;

    echo '<div class="product-long-description">';
    echo apply_filters( 'the_content', $post->post_content );
    echo '</div>';

	echo '<div id="item_anchors" class="comparison-pdt">';
	echo '	<a href="#" class="compare_url">' . esc_html('הוסף להשוואה' , 'madimz') . '</a>';
	echo '</div>';
}

// Custom variotion to box select
add_action( 'woocommerce_single_product_summary', 'custom_variation_boxes', 15 );
function custom_variation_boxes() {
	echo '<div class="right-side info-product">';
		global $product;

		if ( ! $product->is_type( 'variable' ) ) return;

		$attributes = $product->get_variation_attributes();

		echo '<div class="variation-boxes-wrapper">';

			foreach ( $attributes as $attribute_name => $options ) {
				echo '<div class="variation-box-group">';
					echo '<h4>' . wc_attribute_label( $attribute_name ) . ':</h4>';
					echo '<div class="variation-options">';
						//colors array
						if ( $attribute_name == 'pa_color' ) :
							$colors_map = [];
							if ( have_rows( 'add_color', 'option' ) ) :
								while ( have_rows( 'add_color', 'option' ) ): 
									the_row();
									$name = trim( get_sub_field( 'name_color', 'option' ) );
									$hex = get_sub_field( 'color', 'option' );
									if ( $name && $hex ) {
										$colors_map[ trim($name) ] = $hex;
									}
								endwhile;
							endif;
						endif;

						foreach ( $options as $option ) {
							// Full name of the option from the taxonomy
							$taxonomy = str_replace('attribute_', '', $attribute_name);
							$term     = get_term_by( 'slug', $option, $taxonomy );
							$label    = $term ? $term->name : urldecode($option);

							//Using the appropriate HEX
							$hex = isset( $colors_map[$label] ) ? $colors_map[$label] : 'transparent';

							echo '<div class="variation-container ' . ( $attribute_name == 'pa_size' ? 'sizes' : 'colors' ) . '">';				
								if ( $attribute_name == 'pa_size' ) {
									echo '<span class="variation-box size-option" 
											data-attr="' . esc_attr( $attribute_name ) . '" 
											data-value="' . esc_attr( $option ) . '"
											data-color="' . esc_attr( $label ) . '"
										>' . $label . '</span>';
										} 
								elseif ( $attribute_name == 'pa_color' ) {
									echo '<span class="variation-box color-option" 
											style="background-color:' . esc_attr( $hex ) . '" 
											data-attr="' . esc_attr( $attribute_name ) . '" 
											data-value="' . esc_attr( $option ) . '"
											data-color="' . esc_attr( $label ) . '"
										></span>';
								}
							echo '</div>';
						}

					echo '</div>';
				echo '</div>';
			}

		echo '</div>';

    // Load hidden variation form for WooCommerce JS
    // wc_get_template( 'single-product/add-to-cart/variable.php' );
}

// Add price after variotion
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 20 );

// Add wishlist and another information
add_action( 'woocommerce_single_product_summary', 'custom_meta_info_product', 40 );
function custom_meta_info_product() {
		echo '<div class="extra-product-info">';

			// Wishlist
			echo '<button>';
			echo inline_svg_with_class('heart.svg', '');
			echo '</button>';

			// Delivery text
			echo '<div class="delivery-info">';
			echo '	<div class="delivery-price-info">';
			echo '		<span class="sub-title-info">' . esc_html('משלוח רגיל: ', 'madimz') . '</span>';
			echo '		<span class="price-info">' . esc_html( get_field('shipping_cost', 'option') ) . '</span>';
			echo '	</div>';
			echo '	<div class="delivery-time-info">';
			echo '		<span class="sub-title-info">' . esc_html('זמן אספקה: ', 'madimz') . '</span>';
			echo '		<span class="time-info">' . esc_html( get_field('delivery_time', 'option') ) . '</span>';
			echo '	</div>';
			echo '</div>';

    	echo '</div>'; // End .extra-product-info
    echo '</div>'; // End .right-side.info-product
}

add_action( 'woocommerce_single_product_summary', 'custom_general_info_about_pdts', 100 );
function custom_general_info_about_pdts() {

	echo '<div class="left-side info-product">';

		// Reapeter Benefits for customers
		echo '<div class="reasons_customers">';
			echo '	<b class="">' . esc_html( get_field( 'title_customers_buy_from_us', 'option' ) ) . '</b>';
			if ( have_rows( 'reasons_customers_choose_us', 'option' ) ) :
				while ( have_rows( 'reasons_customers_choose_us', 'option' ) ): the_row();
					$reason_text = get_sub_field( 'reason_text', 'option' );
					echo '<ul class="list">';
					echo '	<li class="reasons">' . inline_svg_with_class('arrow-left.svg', '') . '<span>' . esc_html( $reason_text ) . '</span></li>';
					echo '</ul>';
				endwhile;
			endif;
		echo '</div>'; 
		
		// icons
		echo '<div class="services-icons-info">';
			if ( have_rows( 'website_service_icons', 'option' ) ) :
				while ( have_rows( 'website_service_icons', 'option' ) ): the_row();
					$service_icon = get_sub_field( 'service_icon', 'option' );
					$service_text = get_sub_field( 'service_text', 'option' );
					echo '<div class="service-icon">';
					echo '	<img src="' . esc_url( $service_icon ) . '" class="icon" />';
					echo '	<span class="text">' . esc_html( $service_text ) . '</span>';
					echo '</div>';
				endwhile;
			endif;
		echo '</div>';
		
		// contact
		echo '<div class="contact-info">';
			$product_id = get_the_ID();
			$product    = wc_get_product( $product_id );
			$title      = $product->get_name();
			$url        = get_permalink( $product_id );
			$message_text = wp_kses_post( get_field( 'text_message', 'option' ) );

			// Contact URL
			$contact_url = add_query_arg(
				array(
					'product_id' => $product_id,
					'product_title' => rawurlencode($message_text),
				),
				site_url('/contact/')
			);

			// WhatsApp number
			$wa_number = get_field( 'whatsapp_number', 'option');

			// Build WhatsApp message
			$wa_message = rawurlencode(
				"$message_text:\n$title\n$url"
			);

			// Final WhatsApp URL
			$wa_link = "https://wa.me/$wa_number?text=$wa_message";


			// contact
			echo '<a class="product-contact-link" href="' . esc_url($contact_url) . '">';
			echo '	<img class="contact-icon" src="' . get_template_directory_uri() . '/dist/images/ask.png" />';
			echo '	<span class="contact-text">' . esc_html('שאל אותנו על מוצר זה', 'madimz') . '</span>';
			echo '</a>';

			// whatsapp
			echo '<a class="product-whatsapp-link" href="' . esc_url($wa_link) . '" target="_blank">';
			echo '	<img class="whatsapp-icon" src="' . get_template_directory_uri() . '/dist/images/whatsapp.png" />';
			echo '	<span class="whatsapp-text">' . esc_html('שאל אותנו ב WhatsApp', 'madimz') . '</span>';
			echo '</a>';

		echo '</div>';

    echo '</div>'; // End .left-side.info-product

}

// add_action( 'woocommerce_after_single_product_summary', 'custom_general_info_about_pdt', 5 );
/*function custom_general_info_about_pdt() {

	echo '<div class="left-side info-product">';

	// Reapeter Benefits for customers
	echo '<div class="reasons_customers">';
    echo '	<b class="">' . esc_html( get_field( 'title_customers_buy_from_us', 'option' ) ) . '</b>';
	if ( have_rows( 'reasons_customers_choose_us', 'option' ) ) :
		while ( have_rows( 'reasons_customers_choose_us', 'option' ) ): the_row();
			$reason_text = get_sub_field( 'reason_text', 'option' );
			echo '<ul class="list">';
			echo '	<li class="reasons">' . inline_svg_with_class('arrow-left.svg', '') . '<span>' . esc_html( $reason_text ) . '</span></li>';
			echo '</ul>';
		endwhile;
	endif;
    echo '</div>'; 
	
	// icons
	echo '<div class="services-icons-info">';
	if ( have_rows( 'website_service_icons', 'option' ) ) :
		while ( have_rows( 'website_service_icons', 'option' ) ): the_row();
			$service_icon = get_sub_field( 'service_icon', 'option' );
			$service_text = get_sub_field( 'service_text', 'option' );
			echo '<div class="service-icon">';
			echo '	<img src="' . esc_url( $service_icon ) . '" class="icon" />';
			echo '	<span class="text">' . esc_html( $service_text ) . '</span>';
			echo '</div>';
		endwhile;
	endif;
    echo '</div>';
	
	// contact
	echo '<div class="contact-info">';
	$product_id = get_the_ID();
	$product    = wc_get_product( $product_id );
	$title      = $product->get_name();
	$url        = get_permalink( $product_id );
	$message_text = wp_kses_post( get_field( 'text_message', 'option' ) );

	// Contact URL
	$contact_url = add_query_arg(
		array(
			'product_id' => $product_id,
			'product_title' => rawurlencode($message_text),
		),
		site_url('/contact/')
	);

	// WhatsApp number
	$wa_number = get_field( 'whatsapp_number', 'option');

	// Build WhatsApp message
	$wa_message = rawurlencode(
		"$message_text:\n$title\n$url"
	);

	// Final WhatsApp URL
	$wa_link = "https://wa.me/$wa_number?text=$wa_message";


	// contact
	echo '<a class="product-contact-link" href="' . esc_url($contact_url) . '">';
	echo '	<img class="contact-icon" src="' . get_template_directory_uri() . '/dist/images/ask.png" />';
	echo '	<span class="contact-text">' . esc_html('שאל אותנו על מוצר זה', 'madimz') . '</span>';
	echo '</a>';

	// whatsapp
	echo '<a class="product-whatsapp-link" href="' . esc_url($wa_link) . '" target="_blank">';
	echo '	<img class="whatsapp-icon" src="' . get_template_directory_uri() . '/dist/images/whatsapp.png" />';
	echo '	<span class="whatsapp-text">' . esc_html('שאל אותנו ב WhatsApp', 'madimz') . '</span>';
	echo '</a>';

    echo '</div>';

    echo '</div>'; // End .left-side.info-product

}*/

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Show matrix block after single product summary
add_action( 'woocommerce_after_single_product_summary', 'custom_matriza_page', 10 );
function custom_matriza_page() {
	$product_id = get_the_ID();
	$product = wc_get_product( $product_id );
	if ( ! $product ) return;

	$meta = get_image_id($product_id);
	$logo_id = ! empty( $meta['logo'] ) ? (int) $meta['logo'] : 0;

    $user = new WP_User(get_current_user_id());
	$attributes = $product->get_variation_attributes();

	$attr_size        = isset( $attributes['pa_size'] )  ? $attributes['pa_size']  : [];
    $attr_color       = isset( $attributes['pa_color'] ) ? $attributes['pa_color'] : [];
    $count_attr_size  = count( $attr_size );
    $count_attr_color = count( $attr_color );

	$has_sizes  = $count_attr_size  > 1;
    $has_colors = $count_attr_color > 1;

	if ( ! ( $product->is_type( 'variable' ) && ( $has_sizes || $has_colors ) ) ) {
        return;
    }

	$logo_url = $logo_id ? wp_get_attachment_url( $logo_id ) : '';	
	?>

	<div class="single-product-variation-matrix">
		<div class="checkbox-to-print">
			<p><?php esc_html_e( 'סמן מה ברצונך להדפיס', 'madimz' ); ?></p>

			<!-- logo -->
			<label class="container-check">
				<span><?php esc_html_e( 'לוגו', 'madimz' ); ?></span>
				<input type="checkbox" class="checkbox" checked="true" data-name="print-logo" data-target="#upload-logo-area">
				<span class="check-mark"></span>
			</label>

			<!-- number worker -->
			<label class="container-check"> 
				<span><?php esc_html_e( 'מספר עובד', 'madimz' ); ?></span>
				<input type="checkbox"  class="checkbox checkbox-mum" checked="true" data-name="myBtn">
				<span class="check-mark" ></span>
			</label>

			<!-- name worker -->
			<label class="container-check">
				<span><?php esc_html_e( 'שם עובד', 'madimz' ); ?></span>
				<input type="checkbox"  class="checkbox checkbox-name" checked="true" data-name="myBtn">
				<span class="check-mark"></span>
			</label>
		</div>

		<div class="upload-logo-wrapper" id="upload-logo-area">
			<div class="add-to-print print-logo">
				<p class="file-text">
					<?php esc_html_e( 'העלה קובץ לוגו:', 'madimz' ); ?>
					<span class="drscription">
                        <?php esc_html_e( '(לא חובה, במידה ולא תעלה קובץ, נשתמש בלוגו האחרון השמור אצלינו במערכת)', 'madimz' ); ?>
                    </span>
				</p>

				<div class="children">
					<div class="upload-file">

						<form class="file-upload" enctype="multipart/form-data" onsubmit="return false;">
							<div class="form-group">
								<label><?php esc_html_e( 'Choose File:', 'madimz' ); ?></label>
								<input type="file" id="file" accept="image/*" />
							</div>
						</form>

						<!-- Important: The image always exists, even without a saved logo -->
						 <img
                            id="image-file"
                            src="<?php echo $logo_url ? esc_url( $logo_url ) : ''; ?>"
                            alt=""
                            <?php echo $logo_url ? '' : 'style="display:none;"'; ?>
                            <?php echo $logo_id ? 'data-attachment_id="' . esc_attr( $logo_id ) . '"' : ''; ?>
                        />

						<button class="remove-logo red-btn remove-img">
                            <?php esc_html_e( 'Remove File', 'madimz' ); ?>
                        </button>				
					</div>
				</div>
			</div>
		</div>

		<div class="table-choose">					
			<?php echo do_shortcode( '[simply_add_to_cart_ajax]'); ?>
		</div>

		<!-- Pop-Up -->
		<div id="myModal" class="popup-modal">
			<div class="popup-modal-content">
				<div class="p-title">
					<div class="modal-flex">
						<p class="title-value"><?php esc_html_e( 'כמות עובדים:', 'madimz' ); ?></p>
						<p class="text-value" id="modal-value"></p>
					</div>
					<div class="modal-flex">
						<p class="title-value"><?php esc_html_e( 'צבע:', 'madimz' ); ?></p>
						<p class="text-value" id="modal-color"></p>
					</div>
					<div class="modal-flex">
						<p class="title-value"><?php esc_html_e( 'מידה:', 'madimz' ); ?></p>
						<p class="text-value" id="modal-size"></p>
					</div>
					<div class="modal-flex">
						<p class="title-value"><?php esc_html_e( 'מספר וריצאיה:', 'madimz' ); ?></p>
						<p class="text-value" id="modal-var-id"></p>
					</div>
				</div>

				<table id="myTable"></table>

				<div class="close red-btn">
					<?php esc_html_e( 'שמור', 'madimz' ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Get logo image id (from user meta or cart).
 */
function get_image_id( $product_id ) {
    $logo_id = 0;

	if ( is_user_logged_in() ) {
        $logo_id = (int) get_user_meta( get_current_user_id(), '_user_logo', true );
    }

    // אם אין בלוגו המשתמש – נבדוק בעגלה
    if ( ! $logo_id && WC()->cart && WC()->cart->get_cart_contents_count() > 0 ) {
        foreach ( WC()->cart->get_cart() as $item ) {
            if ( $product_id == $item['product_id'] && ! empty( $item['logo'] ) ) {
                $logo_id = (int) $item['logo'];
                break;
            }
        }
    }

    return [ 'logo' => $logo_id ];
}

/**
* After add to cart save the last logo for the user
 */
add_action( 'woocommerce_add_to_cart', function( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
    if ( is_user_logged_in() && ! empty( $cart_item_data['logo'] ) ) {
        update_user_meta( get_current_user_id(), '_user_logo', (int) $cart_item_data['logo'] );
    }
}, 10, 6 );

add_action('woocommerce_after_single_product_summary', 'custom_more_information_product_section_outside_summary', 100);
function custom_more_information_product_section_outside_summary() {
	global $product;

	// Get long description
    $long_description = $product->get_description();

    // Only show if exists
    if ( ! empty( $long_description ) ) {
        echo '<section class="more-info-product-section">';
			echo '<h3 class="sub-title-products">' . __( 'מידע נוסף', 'madimz' ) . '</h3>';
            echo '<div class="more-info-content">';
                echo wpautop( wp_kses_post( $long_description ) ); 
            echo '</div>';

        echo '</section>';
    }

}

add_action('woocommerce_after_single_product_summary', 'show_similar_products_section', 110);
function show_similar_products_section() {
    global $product;
    $GLOBALS['is_first_similar_product'] = true;

	$similar_pdts = get_field('select_similar_products'); 

    if ( empty($similar_pdts) ) return;

    if ( ! is_array($similar_pdts) ) {
        $similar_pdts = [$similar_pdts];
    }

    // Always work with the parent product if it's a variation
    // if ( $product->is_type('variation') ) {
    //     $product = wc_get_product( $product->get_parent_id() );
    // }

    // Query for similar products 
    $args = [
        'post_type'      => 'product',
        'post__in'       => wp_list_pluck( $similar_pdts, 'ID' ),
        'posts_per_page' => -1,
        'orderby'        => 'post__in', 
    ];

    $similar_products = new WP_Query( $args );

	if ( $similar_products->have_posts() ) {
		echo '<section class="similar-products-section">';
			echo '<h3 class="sub-title-products">' . __( 'מוצרים דומים', 'madimz' ) . '</h3>';
			echo '<ul class="similar-products-wrapper">';
				// echo '<div class="swiper-wrapper">';

					while ( $similar_products->have_posts() ) {
						$similar_products->the_post();

						// echo '<div class="swiper-slide">';
			           		wc_get_template_part( 'content', 'product' );
						// echo '</div>';
					}
                	wp_reset_postdata();

            		// echo '</div>'; // swiper-wrapper
        	echo '</ul>'; // swiper
        echo '</section>';
    }
}

add_action( 'woocommerce_after_single_product_summary', 'show_complementary_products_section', 120 );
function show_complementary_products_section() {
    global $product;
    $GLOBALS['is_first_similar_product'] = true;

	$add_products = get_field('additional_complementary_products'); 

    if ( empty($add_products) ) return;

    if ( ! is_array($add_products) ) {
        $add_products = [$add_products];
    }

	// Query for complementary products 
    $args = [
        'post_type'      => 'product',
        'post__in'       => wp_list_pluck( $add_products, 'ID' ),
        'posts_per_page' => -1,
        'orderby'        => 'post__in', 
    ];

    $complementary_products = new WP_Query($args);

    if ( $complementary_products->have_posts() ) {
        echo '<section class="complementary-products-section">';
			echo '<h3 class="sub-title-products">' . __('מוצרים משלימים', 'madimz')	. '</h3>';

			echo '<div class="swiper complementary-products-swiper" >';
				echo '<ul class="swiper-wrapper">';

					while ( $complementary_products->have_posts() ) {
						$complementary_products->the_post();

						echo '<div class="swiper-slide">';
			           		wc_get_template_part( 'content', 'product' );
						echo '</div>';
					}
                	wp_reset_postdata();

            	echo '</ul>'; // swiper-wrapper

				// arrows
        		echo '<div class="swiper-main-nav">';
					echo '<button class="swiper-button-prev">' . inline_svg_with_class('swiper-arrow.svg', 'rotate') . '</button>';
					echo '<button class="swiper-button-next">' . inline_svg_with_class('swiper-arrow.svg', '') . '</button>';
				echo '</div>';

        	echo '</div>'; // swiper
        echo '</section>';
    }
}

add_action('woocommerce_after_single_product', 'custom_back_to_top_button', 100);
function custom_back_to_top_button() {
    echo '<div class="back-to-top-wrap">';
        echo '<button id="back-to-top" class="back-to-top-btn">'
			. inline_svg_with_class('arrow-up.svg', '') 
			. __('חזרה למעלה', 'madimz')
		 . '</button>';
    echo '</div>';
}

add_action( 'woocommerce_before_add_to_cart_quantity', 'custom_quantity_minus' );
function custom_quantity_minus() {
	echo '<div class="qty-btn-wrapper">';
	echo '<button type="button" class="qty-btn qty-plus">' . inline_svg_with_class('plus.svg', '') . '</button>';
}

add_action( 'woocommerce_after_add_to_cart_quantity', 'custom_quantity_plus' );
function custom_quantity_plus() {
	echo '<button type="button" class="qty-btn qty-minus">' . inline_svg_with_class('minus.svg', '') . '</button>';
	echo '</div class="qty-btn-wrapper">'; // End .qty-btn-wrapper
}

add_action( 'woocommerce_after_add_to_cart_button', 'custom_buy_now_button' );
function custom_buy_now_button() {
    global $product;
    echo '<a href="' . esc_url( wc_get_cart_url() . '?add-to-cart=' . $product->get_id() ) . '" class="buy-now-btn">קנה עכשיו</a>';
}


add_action('woocommerce_after_cart_table', function() { 
    echo '<div class="continue-shopping">
            <a href="' . wc_get_page_permalink('shop') . '" class="btn-continue">'
			  . __('המשך לקנות', 'woocommerce')
            . '</a>
          </div>';
});

/****
 * Cart Page
 */

// replace the icon with text to remove product from cart
add_filter( 'woocommerce_cart_item_remove_link', 'custom_mini_cart_remove_text', 10, 2 );
function custom_mini_cart_remove_text( $link, $cart_item_key ) {
    if ( function_exists( 'wc_get_template' ) ) {
		$cart_item = WC()->cart->get_cart()[ $cart_item_key ];
		$product   = $cart_item['data'];
		$icon_url = get_stylesheet_directory_uri() . '/dist/images/tin.svg';

		$link = sprintf(
			'<a href="%s" class="remove remove_from_cart_button" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s">
				<img src="%s" alt="%s" class="remove-icon" />
			</a>',
			esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
			esc_html__( 'Remove this product', 'woocommerce' ),
			esc_attr( $product->get_id() ),
			esc_attr( $cart_item_key ),
			esc_attr( $product->get_sku() ),
			esc_url( $icon_url ),
			esc_attr__( 'Remove', 'woocommerce' )
		);
    }

    return $link;
}
/**
 * @snippet       Plus Minus Quantity Buttons @ WooCommerce Cart Page
 * @how-to        businessbloomer.com/woocommerce-customization
 * @author        Rodolfo Melogli, Business Bloomer
 * @compatible    WooCommerce 8
 * @community     https://businessbloomer.com/club/
 */
 
add_action( 'woocommerce_before_quantity_input_field', 'bbloomer_display_quantity_minus' );
 
function bbloomer_display_quantity_minus() {
   if ( is_product() ) return;
   echo '<button type="button" class="plus" >' . inline_svg_with_class('plus.svg', '') . '</button>';
}

add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_display_quantity_plus' );

function bbloomer_display_quantity_plus() {
	if ( is_product() ) return;
	echo '<button type="button" class="minus" >' . inline_svg_with_class('minus.svg', '') . '</button>';
}

add_filter( 'woocommerce_quantity_input_args', function( $args ) {
    // Ignore min==max limitation and force input to be visible and editable
    $args['max_value'] = max( 9999, $args['max_value'] ); // or some large number
    $args['readonly'] = false;
    return $args;
}, 99 );


// remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

/*add_action('woocommerce_before_single_product_summary', function() {

    global $product;
    $product_id = $product->get_id();
	$gallery = $product->get_gallery_image_ids();
	$main_img = get_the_post_thumbnail_url($product_id, 'large');

	// Wrapper
    echo '<div class="product-gallery" id="custom-gallery-' . $product_id . '">';
    
    // Main slider container
    echo '<div class="swiper-pdt-container swiper" id="swiper-main-' . $product_id . '">';
    echo '<div class="swiper-wrapper">';

	// Main image
    echo '<div class="swiper-slide">';
	echo '	<img  src="' . esc_url($main_img) . '" alt="">';
	echo '</div>';

	// Gallery images
	foreach ($gallery as $img_id) {
		echo '<div class="swiper-slide">';
		echo '	<img  src="' . esc_url(wp_get_attachment_image_url($img_id, 'large')) . '" alt="">';
		echo '</div>';
	}

    echo '</div>'; // End swiper-wrapper

    
    // Navigation buttons
    echo '<div class="swiper-main-nav">';
    echo '	<button class="swiper-main-prev">'.inline_svg_with_class('swiper-arrow.svg', '').'</button>';
    echo '	<button class="swiper-main-next">'.inline_svg_with_class('swiper-arrow.svg', '').'</button>';
    echo '</div>';

	echo '</div>'; // End swiper-pdt-container    
	
    // Thumbs slider
    // echo '<div class="swiper-thumbs swiper" id="swiper-thumbs-' . $product_id . '">';
    // echo '<div class="swiper-wrapper">';

	// // Thumbnail main image
    // echo '<div class="swiper-slide">';
    // echo '    <img  src="' . esc_url($main_img) . '" alt="">';
    // echo '</div>';

	// // Thumbnail gallery images
    // foreach ($gallery as $img_id) {
    //     echo '<div class="swiper-slide">';
    //     echo '    <img  src="' . esc_url(wp_get_attachment_image_url($img_id, 'thumbnail')) . '" alt="">';
    //     echo '</div>';
    // }

    // echo '</div>'; // End swiper-wrapper
    // echo '</div>'; // End swiper-thumbs

    echo '</div>'; // End product-gallery
}, 20);*/

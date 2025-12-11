<?php
use Automattic\WooCommerce\Enums\ProductStatus;

defined( 'ABSPATH' ) || exit;

class MadimzProductCompare {
    public function __construct() {
		// $this->register_scripts();
        $this->register_shortcodes();
        $this->register_hooks();
	}

    public static function get_compare_list_url() {
        if ( class_exists('ACF') && ( $product_compare_page = get_field( 'product_compare_page', 'option' ) ) ) {
            return $product_compare_page;
        }

        return '';
    }

    private function register_shortcodes() {
        add_shortcode( 'madimz_product_compare',        array( $this, 'product_compare_shortcode' ) );
        // add_shortcode( 'madimz_product_compare_count',  array( $this, 'product_compare_count_shortcode' ) );
        add_shortcode( 'madimz_product_compare_button', array( $this, 'product_compare_button_shortcode' ) );
    }

    private function register_scripts() {
        wp_register_style( 'madimz-compare-list-css', get_template_directory_uri() . '/dist/css/compare-list.min.css', array(), _S_VERSION );
        wp_register_script( 'madimz-product-compare-js', get_template_directory_uri() .  '/dist/js/product-compare.js', array(
            'wc-cart-fragments'
        ), _S_VERSION, array( 'strategy'  => 'defer' ) );

        wp_localize_script( 'madimz-product-compare-js', 'madimz_product_compare_ajax', array(
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'madimz-product-compare' ),
        ));
    }

    private function register_hooks() {
        $ajax_events = array(
			'add_product',
            'remove_product',
		);

        foreach ( $ajax_events as $ajax_event ) {
            add_action( 'wp_ajax_madimz_product_compare_' . $ajax_event,        array( $this, $ajax_event ) );
            add_action( 'wp_ajax_nopriv_madimz_product_compare_' . $ajax_event, array( $this, $ajax_event ) );
        }

        add_action( 'init',                 array( $this, 'init_compare_list' ) );
        add_action( 'wp_login',             array( $this, 'merge_compare_list' ), 10, 2 );
        add_action( 'wp_enqueue_scripts',   array( $this, 'enqueue_scripts' ) );

        add_action( 'woocommerce_init',                  array( $this, 'set_wc_session' ) );
        // add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'product_compare_fragments' ) );
    }

    public function enqueue_scripts() {

        wp_register_style( 
            'madimz-compare-list-css', 
            get_template_directory_uri() . '/dist/css/compare-list.min.css', 
            array(), 
            _S_VERSION 
        );

        wp_register_script( 
            'madimz-product-compare-js', 
            get_template_directory_uri() .  '/dist/js/product-compare.js', 
            array( 'wc-cart-fragments' ),
            _S_VERSION, 
            array( 'strategy'  => 'defer' )
        );

        wp_localize_script( 
            'madimz-product-compare-js', 
            'madimz_product_compare_ajax', 
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'madimz-product-compare' ),
            )
        );

        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'madimz_product_compare') ) {
            wp_enqueue_style( 'madimz-compare-list-css' );
            wp_enqueue_script( 'madimz-product-compare-js' );
        }
    }

    public function set_wc_session() { 
        // Not on backend and only for guests
        if ( ! ( is_user_logged_in() || is_admin() ) ) {
            // Early enable WC Session for guest users
            if ( isset(WC()->session) && ! WC()->session->has_session() ) {
                WC()->session->set_customer_session_cookie( true ); 
            }
        }
    }

    private function verify_nonce() {
        if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

        if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'madimz-product-compare' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}
    }

    public function init_compare_list() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            if ( function_exists( 'WC' ) && is_null( WC()->session ) ) {
                WC()->initialize_session();
            }
        }

        if ( is_user_logged_in() && function_exists('WC') && WC()->session ) {
            $session_list = WC()->session->get( 'madimz_compare_list', null );
            if ( $session_list === null ) {
                $user_list = get_user_meta( get_current_user_id(), 'madimz_compare_list', true );
                if ( is_array( $user_list ) ) {
                    WC()->session->set( 'madimz_compare_list', $user_list );
                }
            }
        }
    }

    public function merge_compare_list( $user_login, $user ) {
        $session_list = [];
        if ( function_exists('WC') && WC()->session ) {
            $session_list = WC()->session->get( 'madimz_compare_list', [] );
        }

        $user_list = get_user_meta( $user->ID, 'madimz_compare_list', true );
        if ( ! is_array( $user_list ) ) {
            $user_list = [];
        }

        $merged = array_values( array_unique( array_merge( (array) $user_list, (array) $session_list ) ) );
        update_user_meta( $user->ID, 'madimz_compare_list', $merged );

        if ( function_exists('WC') && WC()->session ) {
            WC()->session->set( 'madimz_compare_list', $merged );
        }
    }

    private function set_compare_list( $compare_list ) {
        if ( function_exists('WC') && WC()->session ) {
            WC()->session->set( 'madimz_compare_list', (array) $compare_list );
        }
        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), 'madimz_compare_list', (array) $compare_list );
        }
    }

    private function get_compare_list() {
        $list = array();

        if ( function_exists('WC') && WC()->session ) {
            $list = WC()->session->get( 'madimz_compare_list', array() );
        }

        if ( is_user_logged_in() ) {
            $user_list = get_user_meta( get_current_user_id(), 'madimz_compare_list', true );
            if ( ! is_array( $user_list ) ) {
                $user_list = array();
            }
            $list = array_values( array_unique( array_merge( (array) $list, (array) $user_list ) ) );
        }

        return $list;
    }

    // based on wp-content\plugins\woocommerce\includes\wc-template-functions.php (wc_display_product_attributes())
    private function get_product_attributes( $product ) {
        $product_attributes = array();

        // Display weight and dimensions before attribute list.
        $display_dimensions = apply_filters( 'wc_product_enable_dimensions_display', $product->has_weight() || $product->has_dimensions() );

        if ( $display_dimensions && $product->has_weight() ) {
            $product_attributes['weight'] = array(
                'label' => __( 'Weight', 'woocommerce' ),
                'value' => wc_format_weight( $product->get_weight() ),
            );
        }

        if ( $display_dimensions && $product->has_dimensions() ) {
            $product_attributes['dimensions'] = array(
                'label' => __( 'Dimensions', 'woocommerce' ),
                'value' => wc_format_dimensions( $product->get_dimensions( false ) ),
            );
        }

        // Add product attributes to list.
        $attributes = array_filter( $product->get_attributes(), 'wc_attributes_array_filter_visible' );

        foreach ( $attributes as $attribute ) {
            $values = array();

            if ( $attribute->is_taxonomy() ) {
                $attribute_taxonomy = $attribute->get_taxonomy_object();
                $attribute_values   = wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'all' ) );

                foreach ( $attribute_values as $attribute_value ) {
                    $value_name = esc_html( $attribute_value->name );

                    if ( $attribute_taxonomy->attribute_public ) {
                        $values[] = '<a href="' . esc_url( get_term_link( $attribute_value->term_id, $attribute->get_name() ) ) . '" rel="tag">' . $value_name . '</a>';
                    } else {
                        $values[] = $value_name;
                    }
                }
            } else {
                $values = $attribute->get_options();

                foreach ( $values as &$value ) {
                    $value = make_clickable( esc_html( $value ) );
                }
            }

            $product_attributes[ 'attribute_' . sanitize_title_with_dashes( $attribute->get_name() ) ] = array(
                'label' => wc_attribute_label( $attribute->get_name() ),
                'value' => apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values ),
            );
        }

        /**
         * Hook: woocommerce_display_product_attributes.
         *
         * @since 3.6.0.
         * @param array $product_attributes Array of attributes to display; label, value.
         * @param WC_Product $product Showing attributes for this product.
         */
        $product_attributes = apply_filters( 'woocommerce_display_product_attributes', $product_attributes, $product );

        return $product_attributes;
    }

    public function add_product() {
        $this->verify_nonce();

        $product_id = $_POST['product_id'] ?? '';
        $product    = wc_get_product( $product_id );

        if ( ! $product || $product->get_status() !== ProductStatus::PUBLISH ) {
            wc_add_notice( __( 'שגיאה בהוספת מוצר לרשימת ההשוואה', 'madimz' ), 'error' );
            wp_send_json_error( wc_print_notices( true ) );
            wp_die();
        }

        $compare_list = $this->get_compare_list();
        if ( ! is_array( $compare_list ) ) {
            $compare_list = array();
        }

        // Limit to the number of products listed in the field
        $max_items = get_field('maximum_products_compare', 'option');
        if ( count( $compare_list ) >= $max_items ) {
            wc_add_notice(
                sprintf( __( 'ניתן להשוות עד %d מוצרים בלבד. הסר מוצר כדי להוסיף חדש.', 'madimz' ), $max_items ),
                'error'
            );
            wp_send_json_error( wc_print_notices( true ) );
            wp_die();
        }

        if ( ! in_array( $product->get_id(), $compare_list ) ) {
            array_push( $compare_list, $product->get_id() );
            $this->set_compare_list( $compare_list );
        }

        $notice = sprintf( __( 'הוספת %s לרשימת ההשוואה', 'madimz' ), $product->get_title() );

        if ( $product_compare_page = static::get_compare_list_url() ) {
            $notice .= '<a href="' . esc_attr( $product_compare_page ) . '" class="button wc-forward">' . __( 'הצג רשימה', 'madimz' ) . '</a>';
        }
        wc_add_notice( $notice, 'success' );

        wp_send_json_success( wc_print_notices( true ) );
    }

    public function remove_product() {
        $this->verify_nonce();

        $compare_list = $this->get_compare_list();
        if ( ! is_array( $compare_list ) ) {
            $compare_list = array();
        }

        $product_id = $_POST['product_id'] ?? '';
        $product    = wc_get_product( $product_id );

        if ( ( $key = array_search( $product_id, $compare_list ) ) !== false) {
            unset( $compare_list[ $key ] );
            $this->set_compare_list( $compare_list );
        }

        if ( $product ) {
            $notice = sprintf( __( 'הסרת את %s מרשימת ההשוואה', 'madimz' ), $product->get_title() );
            wc_add_notice( $notice, 'success' );
        }

        wp_send_json_success();
    }

    /*public function product_compare_fragments( $fragments ) {
        $fragments['.product-compare-count'] = $this->product_compare_count_shortcode();
        return $fragments;
    }*/

    public function product_compare_shortcode() {
        wp_enqueue_script( 'madimz-product-compare-js' );

        $data           = array(
            'product_data'  => array(),
            'attributes'    => array(),
            // 'attribute_map' => array(),
        );
        $valid_ids      = array();
        $compare_list   = $this->get_compare_list();
        if ( ! is_array( $compare_list ) || empty( $compare_list ) ) {
            $compare_list = array( -1 );
        }
        $args = array(
			'post_type'     => 'product',
            'nopaging'      => true,
			'post__in'      => $compare_list,
            'post_status'   => ProductStatus::PUBLISH
		);
		$loop = new WP_Query( $args );
        while ( $loop->have_posts() ) { 
            $loop->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( ! $product ) continue;

            $valid_ids[]        = $product->get_id();
            $product_attributes = $this->get_product_attributes( $product );

            ob_start();
            do_action( 'woocommerce_before_shop_loop_item_title' );
            $image = ob_get_clean();

            $data['product_data'][] = array( 
                'product'       => $product,
                'name'          => $product->get_title(),
                'price'         => $product->get_regular_price(),
                'image'         => $image
            );
            $data['attributes'][]   = $product_attributes;
            // $data['attribute_map']  = array_unique( array_merge( $data['attribute_map'], array_keys( $product_attributes ) ) );
        }
        wp_reset_postdata();
        $this->set_compare_list( $valid_ids );

        ob_start();

        if ( empty( $valid_ids ) ) {
            wc_add_notice( __( 'רשימת ההשוואה שלך ריקה.', 'madimz' ), 'notice' );
        } 
        
        wc_get_template(
            'madimz-product-compare.php',
            array(
                'data' => $data
            )
        );
        
        return ob_get_clean();
    }

    /*public function product_compare_count_shortcode() {
        $compare_list = $this->get_compare_list();
        if ( empty( $compare_list ) || ! is_countable( $compare_list ) ) {
            return '<span class="product-compare-count"></span>';
        }

        return '<span class="product-compare-count">' . esc_html( count( $compare_list ) ) . '</span>';
    }*/

    public function product_compare_button_shortcode() {
        global $product;
        if ( ! $product ) {
            return '';
        }
        wp_enqueue_script( 'madimz-product-compare-js' );

        ob_start();
        ?>
        <button class="add-to-compare-list" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
            <span class="icon compare"></span>
            <?php echo __( 'הוסף להשוואה', 'madimz' ); ?>
        </button>
        <?php
        return ob_get_clean();
    }
}

new MadimzProductCompare();
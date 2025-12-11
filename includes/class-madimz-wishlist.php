<?php
use Automattic\WooCommerce\Enums\ProductStatus;

defined( 'ABSPATH' ) || exit;

class MadimzWishlist {
    public function __construct() {
		// $this->register_scripts();
        $this->register_shortcodes();
        $this->register_hooks();
	}

    public static function get_wishlist_url() {
        return home_url( '/wishlist/' );
    }

    private function register_shortcodes() {
        add_shortcode( 'madimz_wishlist_button', array( $this, 'wishlist_button_shortcode' ) );
    }

    private function register_scripts() {
        wp_register_script( 
            'madimz-wishlist-js', 
            get_template_directory_uri() .  '/dist/js/wishlist.js',
            array( 'wc-cart-fragments' ),
            _S_VERSION, 
            array( 'strategy'  => 'defer' ) 
        );

        wp_localize_script( 
            'madimz-wishlist-js', 
            'madimz_wishlist_ajax', 
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'madimz-wishlist' ),
            )
        );
    }

    private function register_hooks() {
        $ajax_events = array(
			'add_product',
            'remove_product',
            'clear_list',
		);

        foreach ( $ajax_events as $ajax_event ) {
            add_action( 'wp_ajax_madimz_wishlist_' . $ajax_event,        array( $this, $ajax_event ) );
            add_action( 'wp_ajax_nopriv_madimz_wishlist_' . $ajax_event, array( $this, $ajax_event ) );
        }

        // /wishlist/ endpoint
        add_action( 'init',                                     array( $this, 'init_wishlist_endpoint' ), 9 ); // Early priority 9 promises that woocommerce and other plugins will recognize the endpiont
        add_action( 'wp_enqueue_scripts',                       array( $this, 'enqueue_scripts' ) );
        add_action( 'after_switch_theme',                       array( $this, 'after_switch_theme' ) );
        add_action( 'woocommerce_account_wishlist_endpoint',    array( $this, 'display_wishlist' ) );

        add_filter( 'query_vars',                           array( $this, 'wp_add_query_vars' ) );
        add_filter( 'woocommerce_get_query_vars',           array( $this, 'wc_add_query_vars' ) );
        
        // If you want the menu item in My Account, uncomment these:
        // add_filter( 'woocommerce_account_menu_items',       array( $this, 'add_account_menu_items' ), 20 ); // Late priority 10 allow to place the wishlist link after all other links were added
        // add_filter( 'woocommerce_endpoint_wishlist_title',  array( $this, 'get_endpoint_title' ), 10, 3 );
    }

    public function enqueue_scripts() {
        wp_register_script( 
            'madimz-wishlist-js', 
            get_template_directory_uri() .  '/dist/js/wishlist.js',
            array( 'wc-cart-fragments' ),
            _S_VERSION, 
            array( 'strategy'  => 'defer' ) 
        );

        wp_localize_script( 
            'madimz-wishlist-js', 
            'madimz_wishlist_ajax', 
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'madimz-wishlist' ),
            )
        );

        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'madimz_wishlist_button') ) {
            wp_enqueue_script( 'madimz-wishlist-js' );
        }
    }

    public function init_wishlist_endpoint() {
        // Adds /wishlist/ endpoint
        add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
    }

    public function after_switch_theme() {
        // Ensure endpoint is registered before flushing
        $this->init_wishlist_endpoint();
        flush_rewrite_rules();
    }

    public function wp_add_query_vars( $vars ) {
        // Make sure WordPress recognizes the query var
        $vars[] = 'wishlist';
        return $vars;
    }

    public function wc_add_query_vars( $vars ) {
        // Make sure Woocommerce recognizes the query var as an endpoint
        $vars['wishlist'] = 'wishlist';
        return $vars;
    }

    public function add_account_menu_items( $items ) {
        $new = array();

        foreach ( $items as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'orders' === $key ) {
                $new['wishlist'] = __( 'Wishlist', 'madimz' );
            }
        }

        if ( ! isset( $new['wishlist'] ) ) {
            $new['wishlist'] = __( 'Wishlist', 'madimz' );
        }

        return $new;
    }

    public function get_endpoint_title( $title, $endpoint, $action ) {
        if ( 'wishlist' === $endpoint ) {
            return __( 'Wishlist', 'madimz' );
        }

        return $title;
    }

    private function verify_nonce() {
        if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'madimz-wishlist' ) ) { 
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}
    }

    // Normalize a wishlist array: make sure it's a clean array of unique product IDs.
    private static function normalise_list( $wishlist ) {
        $wishlist = array_map( 'absint', (array) $wishlist );
        $wishlist = array_filter( $wishlist );
        $wishlist = array_values( array_unique( $wishlist ) );

        return $wishlist;
    }

    // Save wishlist to session + user meta (if logged in).
    public static function set_wishlist( $wishlist ) {
        $wishlist = self::normalise_list( $wishlist );

        if ( function_exists( 'WC' ) && WC()->session ) {
            WC()->session->set( 'madimz_wishlist_list', $wishlist );
        }

        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), 'madimz_wishlist_list', $wishlist );
        }
    }


    // Get wishlist from session + user meta merged.
    // Works for guests (session only) and logged users (session + meta).
    public static function get_wishlist() {
        $list = array();

        // Session
        if ( function_exists( 'WC' ) && WC()->session ) {
            $list = WC()->session->get( 'madimz_wishlist_list', array() );
        }

        // Merge user meta if logged in
        if ( is_user_logged_in() ) {
            $user_list = get_user_meta( get_current_user_id(), 'madimz_wishlist_list', true );
            if ( ! is_array( $user_list ) ) {
                $user_list = array();
            }

            $list = array_merge( (array) $list, (array) $user_list );
        }

        return self::normalise_list( $list );
    }

    // AJAX: Add product to wishlist (for guests and logged-in users).
    public function add_product() {
        $this->verify_nonce();

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

        if ( ! $product_id ) {
            wc_add_notice( __( 'שגיאה בהוספת מוצר לרשימת המשאלות', 'madimz' ), 'error' );
            wp_send_json_error(
                array(
                    'notice' => wc_print_notices( true ),
                )
            );
        }

        $product = wc_get_product( $product_id );

        if ( ! $product || $product->get_status() !== ProductStatus::PUBLISH ) {
            wc_add_notice( __( 'שגיאה בהוספת מוצר לרשימת המשאלות', 'madimz' ), 'error' );
            wp_send_json_error(
                array(
                    'notice' => wc_print_notices( true ),
                )
            );
        }

        $wishlist = self::get_wishlist();

        if ( ! in_array( $product_id, $wishlist, true ) ) {
            $wishlist[] = $product_id;
            self::set_wishlist( $wishlist );
        }

        $notice = sprintf( __( 'הוספת %s לרשימת המשאלות', 'madimz' ), $product->get_title() );

        if ( $wishlist_page = static::get_wishlist_url() ) {
            $notice .= ' <a href="' . esc_url( $wishlist_page ) . '" class="button wc-forward">' . esc_html__( 'הצג רשימת משאלות', 'madimz' ) . '</a>';
        }

        wc_add_notice( $notice, 'success' );

        wp_send_json_success(
            array(
                'notice' => wc_print_notices( true ),
                'button' => $this->wishlist_button_html( $product ),
                'count'  => count( self::get_wishlist() ),
            )
        );
    }

    // AJAX: Remove product from wishlist (for guests and logged-in users).
    public function remove_product() {
        $this->verify_nonce();

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

        if ( ! $product_id ) {
            wc_add_notice( __( 'שגיאה בהסרת מוצר מרשימת המשאלות', 'madimz' ), 'error' );
            wp_send_json_error(
                array(
                    'notice' => wc_print_notices( true ),
                )
            );
        }

        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            wc_add_notice( __( 'שגיאה בהסרת מוצר מרשימת המשאלות', 'madimz' ), 'error' );
            wp_send_json_error(
                array(
                    'notice' => wc_print_notices( true ),
                )
            );
        }

        $wishlist = self::get_wishlist();
        $key      = array_search( $product_id, $wishlist, true );

        if ( false !== $key ) {
            unset( $wishlist[ $key ] );
            self::set_wishlist( $wishlist );
        }

        $notice = sprintf( __( 'הסרת את %s מרשימת המשאלות', 'madimz' ), $product->get_title() );
        wc_add_notice( $notice, 'success' );

        wp_send_json_success(
            array(
                'notice' => wc_print_notices( true ),
                'button' => $this->wishlist_button_html( $product ),
                'count'  => count( self::get_wishlist() ),
            )
        );
    }

    // clear all products in kist bu button
    public function clear_list() {
        $this->verify_nonce();

        // Clear session first
        if ( function_exists('WC') && WC()->session ) {
            WC()->session->set( 'madimz_wishlist_list', array() );
        }

        // Clear meta for logged users
        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), 'madimz_wishlist_list', array() );
        }

        wc_add_notice( __( 'רשימת המשאלות שלך ריקה', 'madimz' ), 'success' );

        wp_send_json_success( array(
            'notice' => wc_print_notices( true ),
            'count'  => 0, // Important for header counter
        ) );
    }

    // My Account endpoint renderer: /my-account/wishlist/
    public function display_wishlist() {
        wc_get_template(
            'myaccount/madimz-wishlist.php',
            array(
                'wishlist' => self::get_wishlist(),
            )
        );
    }

    private function wishlist_button_html( $product ) {
        $wishlist       = self::get_wishlist();
        $in_wishlist    = in_array( $product->get_id(), $wishlist, true );

        ?>
        <button 
            class="button-wishlist <?php echo $in_wishlist ? 'remove' : ''; ?>" 
            data-id="<?php echo esc_attr( $product->get_id() ); ?>"
            type="button" >
            <?php //echo inline_svg_with_class('heart.svg', '') ?>
                <span class="icon wishlist"></span>
                <span class="wishlist-text"><?php echo $in_wishlist ? __( 'הסר מהמועדפים', 'madimz' ) : __( 'הוסף למועדפים', 'madimz' ); ?></span>
        </button>
        <?php
        return ob_get_clean();
    }

    public function wishlist_button_shortcode() {
        global $product;

        if ( ! $product ) {
            return '';
        }

        wp_enqueue_script( 'madimz-wishlist-js' );

        // Start output buffering here (moved from wishlist_button_html)
        ob_start();
        echo $this->wishlist_button_html( $product );

        return ob_get_clean();
    }
}

new MadimzWishlist();
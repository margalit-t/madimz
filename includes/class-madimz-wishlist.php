<?php
use Automattic\WooCommerce\Enums\ProductStatus;

defined( 'ABSPATH' ) || exit;

class MadimzWishlist {
    public function __construct() {
		$this->register_scripts();
        $this->register_shortcodes();
        $this->register_hooks();
	}

    public static function get_wishlist_url() {
        if ( function_exists( 'wc_get_account_endpoint_url' ) ) {
            return wc_get_account_endpoint_url( 'wishlist' );
        }

        return home_url( '/my-account/wishlist/' );
    }

    private function register_shortcodes() {
        add_shortcode( 'madimz_wishlist_button', array( $this, 'wishlist_button_shortcode' ) );
    }

    private function register_scripts() {
        wp_register_script( 'madimz-wishlist-js', get_template_directory_uri() .  '/dist/js/wishlist.js', array(
            'wc-cart-fragments'
        ), _S_VERSION, array( 'strategy'  => 'defer' ) );

        wp_localize_script( 'madimz-wishlist-js', 'madimz_wishlist_ajax', array(
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'madimz-wishlist' ),
        ));
    }

    private function register_hooks() {
        $ajax_events = array(
			'add_product',
            'remove_product',
		);

        foreach ( $ajax_events as $ajax_event ) {
            add_action( 'wp_ajax_madimz_wishlist_' . $ajax_event,        array( $this, $ajax_event ) );
            add_action( 'wp_ajax_nopriv_madimz_wishlist_' . $ajax_event, array( $this, $ajax_event ) );
        }

        add_action( 'init',                                     array( $this, 'init_wishlist_endpoint' ), 9 ); // Early priority 9 promises that woocommerce and other plugins will recognize the endpiont
        add_action( 'after_switch_theme',                       array( $this, 'after_switch_theme' ) );
        add_action( 'woocommerce_account_wishlist_endpoint',    array( $this, 'display_wishlist' ) );

        add_filter( 'query_vars',                           array( $this, 'wp_add_query_vars' ) );
        add_filter( 'woocommerce_get_query_vars',           array( $this, 'wc_add_query_vars' ) );
        add_filter( 'woocommerce_account_menu_items',       array( $this, 'add_account_menu_items' ), 20 ); // Late priority 10 allow to place the wishlist link after all other links were added
        add_filter( 'woocommerce_endpoint_wishlist_title',  array( $this, 'get_endpoint_title' ), 10, 3 );
    }

    public function init_wishlist_endpoint() {
        // Adds /my-account/wishlist/ endpoint
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
        // Insert the wislist after orders link in account page navigation
        $new = array();

        foreach ( $items as $key => $label ) {
            $new[ $key ] = $label;
            if ( 'orders' === $key ) {
                $new['wishlist'] = __( 'Wishlist', 'madimz' );
            }
        }

        if ( ! isset( $new['my-wishlist'] ) ) {
            $new['wishlist'] = __( 'Wishlist', 'madimz' );
        }

        return $new;
    }

    public function get_endpoint_title( $title, $endpoint, $action ) {
        // Return the title of the wishlist endpoint in account page navigation
        if( 'wishlist' === $endpoint ) {
            return __( 'Wishlist', 'madimz' );
        }

        return $title;
    }

    private function verify_nonce() {
        if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( 'missing_fields' );
			wp_die();
		}

        if ( ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'madimz-wishlist' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error( 'bad_nonce' );
			wp_die();
		}
    }

    private function set_wishlist( $wishlist ) {
        if ( is_user_logged_in() ) {
            update_user_meta( get_current_user_id(), 'madimz_wishlist', (array) $wishlist );
        }
    }

    private function get_wishlist() {
       $wishlist = is_user_logged_in() ? get_user_meta( get_current_user_id(), 'madimz_wishlist', true ) : array();
        if ( ! is_array( $wishlist ) ) {
            $wishlist = array();
        }

        return $wishlist;
    }

    public function add_product() {
        $this->verify_nonce();

        if ( ! is_user_logged_in() ) {
            $notice = __( 'אנא התחבר כדי להוסיף את המוצר לרשימת המשאלות שלך.', 'madimz' );
            $notice .= '<a href="' . esc_attr( get_permalink( wc_get_page_id( 'myaccount' ) ) ) . '" class="button wc-forward">' . __( 'התחבר', 'madimz' ) . '</a>';
            wc_add_notice( $notice, 'notice' );
            wp_send_json_success( array(
                'notice' => wc_print_notices( true )
            ) );
            wp_die();
        }

        $product_id = $_POST['product_id'] ?? '';
        $product    = wc_get_product( $product_id );

        if ( ! $product || $product->get_status() !== ProductStatus::PUBLISH ) {
            wc_add_notice( __( 'שגיאה בהוספת מוצר לרשימת המשאלות', 'madimz' ), 'error' );
            wp_send_json_error( array(
                'notice' => wc_print_notices( true )
            ) );
            wp_die();
        }

        $wishlist = $this->get_wishlist();
        if ( ! in_array( $product->get_id(), $wishlist ) ) {
            array_push( $wishlist, $product->get_id() );
            $this->set_wishlist( $wishlist );
        }

        $notice = sprintf( __( 'הוספת %s לרשימת המשאלות', 'madimz' ), $product->get_title() );

        if ( $wishlist_page = static::get_wishlist_url() ) {
            $notice .= '<a href="' . esc_attr( $wishlist_page ) . '" class="button wc-forward">' . __( 'הצג רשימת משאלות', 'madimz' ) . '</a>';
        }
        wc_add_notice( $notice, 'success' );

        wp_send_json_success( array(
            'notice' => wc_print_notices( true ),
            'button' => $this->wishlist_button_html( $product )
        ) );
    }

    public function remove_product() {
        $this->verify_nonce();

        if ( ! is_user_logged_in() ) {
            $notice = __( 'אנא התחבר.', 'madimz' );
            $notice .= '<a href="' . esc_attr( get_permalink( wc_get_page_id( 'myaccount' ) ) ) . '" class="button wc-forward">' . __( 'התחבר', 'madimz' ) . '</a>';
            wc_add_notice( $notice, 'notice' );
            wp_send_json_success( array(
                'notice' => wc_print_notices( true )
            ) );
            wp_die();
        }

        $product_id = $_POST['product_id'] ?? '';
        $product    = wc_get_product( $product_id );
        $wishlist   = $this->get_wishlist();

        if ( ! $product ) {
            wp_send_json_error();
        }

        if ( ( $key = array_search( $product->get_id(), $wishlist ) ) !== false) {
            unset( $wishlist[ $key ] );
            $this->set_wishlist( $wishlist );
        }

        $notice = sprintf( __( 'הסרת את %s מרשימת המשאלות', 'madimz' ), $product->get_title() );
        wc_add_notice( $notice, 'success' );

        wp_send_json_success( array(
            'notice' => wc_print_notices( true ),
            'button' => $this->wishlist_button_html( $product )
        ) );
    }

    public function display_wishlist() {
        wc_get_template( 
            'myaccount/madimz-wishlist.php', 
            array(
                'wishlist' => $this->get_wishlist()
            ) 
        );
    }

    private function wishlist_button_html( $product ) {
        $wishlist       = $this->get_wishlist();
        $in_wishlist    = in_array( $product->get_id(), $wishlist );

        ob_start();
        ?>
        <button class="button-wishlist <?php echo $in_wishlist ? 'remove' : ''; ?>" data-id="<?php echo esc_attr( $product->get_id() ); ?>">
            <?php //echo inline_svg_with_class('heart.svg', '') ?>
            <span class="icon wishlist"></span>
            <?php echo $in_wishlist ? __( 'הסר מהמועדפים', 'madimz' ) : __( 'הוסף למועדפים', 'madimz' ); ?>
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
        return $this->wishlist_button_html( $product );
    }
}

new MadimzWishlist();
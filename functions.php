<?php
/**
 * madimz functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package madimz
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function madimz_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on madimz, use a find and replace
		* to change 'madimz' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'madimz', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'madimz' ),
			'footer-navigation-1' => esc_html__('First Footer Navigation', 'madimz'),
			'footer-navigation-2' => esc_html__('Second Footer Navigation', 'madimz'),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'madimz_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'madimz_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function madimz_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'madimz_content_width', 640 );
}
add_action( 'after_setup_theme', 'madimz_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function madimz_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'madimz' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'madimz' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'madimz_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function madimz_scripts() {
	wp_enqueue_style( 'madimz-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'madimz-style', 'rtl', 'replace' );

	wp_enqueue_script( 'madimz-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
// add_action( 'wp_enqueue_scripts', 'madimz_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}

/**
 * Custom Functions
 */
require get_template_directory() . '/inc/custom-functions.php';

/**
 * Enquque Scripts and Styles
 */
require get_template_directory() . '/inc/enqueue.php';

/**
 * Acf Functions
 */
require get_template_directory() . '/inc/acf-functions.php';

/**
 * Ajax Functions
 */
require get_template_directory() . '/inc/ajax-functions.php';



//elicheva functionnality

// שינוי טקסט בשדה ההתחברות לווקומרס
add_filter( 'gettext', function( $translated_text, $text, $domain ) {

    if ( trim( $text ) === 'Username or email address' ) {
        return __('מספר נייד', 'madimz' );
    }

    return $translated_text;

}, 20, 3 );

add_filter( 'gettext', function( $translated, $text, $domain ) {

    // כפתור התחברות בטופס login
    if ( trim( $text ) === 'Log in' ) {
        return __( 'כניסה לקוחות', 'madimz' );
    }

    return $translated;

}, 20, 3 );


function get_user_set_password_url( $user_id ) {
    $user = get_user_by('id', $user_id);

    if (!$user) return false;

    $key = get_password_reset_key( $user );
    if ( is_wp_error( $key ) ) return false;

    return wp_login_url() . "?action=rp&key={$key}&login=" . rawurlencode( $user->user_login );
}


add_filter('authenticate', function($user, $username, $password) {


    if ( is_admin() ) return $user;

    if ( strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false ) return $user;

    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ( strpos($ref, 'my-account') === false ) return $user;

    $current_action = $_REQUEST['action'] ?? '';
    if ($current_action === 'rp' || $current_action === 'resetpass') {
        return $user;
    }


    if (empty($username)) return $user;

    $userdata = get_user_by('login', $username);
    if (!$userdata) return $user;

    $has_changed_password = get_user_meta($userdata->ID, 'has_changed_password', true);

    if (!$has_changed_password || $has_changed_password == 0) {

        $url = get_user_set_password_url($userdata->ID);

        if ($url) {
            wp_redirect($url);
            exit;
        }
    }

    return $user;

}, 10, 3);





add_action('password_reset', function($user, $new_pass) {
    update_user_meta($user->ID, 'has_changed_password', 1);
}, 10, 2);


add_filter('login_url', function($login_url, $redirect, $force_reauth){

    // אם אנחנו בדף resetpass – שינינו סיסמה ומוצג "כניסה ללקוחות"
    if ( isset($_GET['action']) && $_GET['action'] === 'resetpass' ) {

        // כתובת דף הכניסה של WooCommerce
        return wc_get_page_permalink('myaccount');
    }

    return $login_url;

}, 10, 3);



// מציג שדה עריכה במסך המשתמש
add_action('show_user_profile', 'show_has_changed_password_field');
add_action('edit_user_profile', 'show_has_changed_password_field');

function show_has_changed_password_field($user) {
    $value = get_user_meta($user->ID, 'has_changed_password', true);
    ?>
    <h3>Force First Login Password Reset</h3>

    <table class="form-table">
        <tr>
            <th><label for="has_changed_password">Has Changed Password?</label></th>
            <td>
                <select name="has_changed_password" id="has_changed_password">
                    <option value="1" <?php selected($value, 1); ?>>Yes (User already set password)</option>
                    <option value="0" <?php selected($value, 0); ?>>No (Force password reset)</option>
                </select>
                <p class="description">Set to "No" to force user to choose a new password on next login.</p>
            </td>
        </tr>
    </table>
    <?php
}


add_action('personal_options_update', 'save_has_changed_password_field');
add_action('edit_user_profile_update', 'save_has_changed_password_field');

function save_has_changed_password_field($user_id) {

    // הרשאות
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (isset($_POST['has_changed_password'])) {
        update_user_meta($user_id, 'has_changed_password', intval($_POST['has_changed_password']));
    }
}

add_action( 'woocommerce_account_content', 'custom_myaccount_dynamic_titles_translatable', 5 );
function custom_myaccount_dynamic_titles_translatable() {

    // מזהה את העמוד הנוכחי
    $endpoint = WC()->query->get_current_endpoint();

    // כותרות מתורגמות לפי Endpoint
    $titles = [
        ''                 => __('החשבון שלי', 'madimz'),        // Dashboard
        'dashboard'        => __('החשבון שלי', 'madimz'),
        'orders'           => __('הזמנות', 'madimz'),
        'view-order'       => __('פרטי ההזמנה', 'madimz'),
        'downloads'        => __('הורדות', 'madimz'),
        'edit-account'     => __('עריכת סיסמה', 'madimz'),
        'edit-address'     => __('כתובת', 'madimz'),
        'payment-methods'  => __('אמצעי תשלום', 'madimz'),
        'customer-logout'  => __('התנתקות', 'madimz'),
    ];

    // טקסט ברירת מחדל אם אין התאמה
    $title = $titles[$endpoint] ?? __('החשבון שלי', 'madimz');

    // הדפסה
    echo '<header class="entry-header"><h1 class="entry-title">' . esc_html( $title ) . '</h1></header>';
}

// הוספת עמוד "סוג תשלום" בלוח ההזמנות באיזור האישי.
add_filter( 'woocommerce_my_account_my_orders_columns', function( $columns ) {

    // Add new Payment Method column
    $columns['order-payment'] = __( 'סוג תשלום', 'madimz' );

	// Add payment status column
    $columns['order-status-pay'] = __( 'מצב התשלום', 'madimz' );

    return $columns;
});

add_action( 'woocommerce_my_account_my_orders_column_order-payment', function( $order ) {

    $method = $order->get_payment_method_title();

    if ( $method ) {
        echo esc_html( $method );
    } else {
        echo '-';
    }
});

add_action( 'woocommerce_my_account_my_orders_column_order-status-pay', function( $order ) {

    // Get payment status from order
    $status = $order->get_status(); // example: processing, completed, pending...

    if ( $status ) {
        // Convert WC internal status to readable title
        echo esc_html( wc_get_order_status_name( $status ) );
    } else {
        echo '-';
    }
});

/**
 * Reorder My Account → Orders table columns
 */
add_filter( 'woocommerce_my_account_my_orders_columns', function( $columns ) {

    // בונים את כל העמודות מחדש לפי הסדר הרצוי
    $new_columns = [
        'order-number'     => __( 'מספר הזמנה', 'madimz' ),       // מספר הזמנה
        'order-date'       => __( 'תאריך ושעה', 'madimz' ),       // תאריך
        'order-total'      => __( 'סה"כ לתשלום', 'madimz' ),       // סכום
        'order-payment'    => __( 'סוג תשלום', 'madimz' ),         // סוג תשלום (חדש)
        'order-status-pay' => __( 'מצב התשלום', 'madimz' ),       // סטטוס תשלום (חדש)
        'order-actions'    => __( 'פעולות', 'madimz' ),            // משאירים פעולות
    ];

    return $new_columns;
});


add_filter( 'woocommerce_account_menu_items', function( $items ) {
    unset( $items['downloads'] );
	unset( $items['edit-address'] );  // מסיר כתובות

	if ( isset( $items['edit-account'] ) ) {
        $items['edit-account'] = __( 'עריכת סיסמה', 'madimz' );
    }
    return $items;
});

//להשאיר רק אפשרות של עריכת סיסמא
add_filter( 'woocommerce_save_account_details_required_fields', function( $fields ) {
    unset( $fields['account_first_name'] );
    unset( $fields['account_last_name'] );
    unset( $fields['account_display_name'] );
    unset( $fields['account_email'] );

    return $fields;
});




// הסרת כל השדות הלא רצויים בטופס עריכת החשבון
add_action( 'woocommerce_edit_account_form', function() {
    ?>
    <style>
        /* מסתיר את כל שדות הפרופיל */
        .woocommerce-EditAccountForm .woocommerce-form-row:not(.form-row-wide) {
            display: none !important;
        }

        /* מסתיר: שם פרטי, שם משפחה, תצוגה, אימייל */
        .woocommerce-EditAccountForm input[name="account_first_name"],
        .woocommerce-EditAccountForm input[name="account_last_name"],
        .woocommerce-EditAccountForm input[name="account_display_name"],
        .woocommerce-EditAccountForm input[name="account_email"] {
            display: none !important;
        }

        .woocommerce-EditAccountForm label[for="account_first_name"],
        .woocommerce-EditAccountForm label[for="account_last_name"],
        .woocommerce-EditAccountForm label[for="account_display_name"],
        .woocommerce-EditAccountForm label[for="account_email"] {
            display:none !important;
        }
    </style>
    <?php
});



add_action( 'woocommerce_view_order', 'madimz_custom_view_order_content', 1 );
function madimz_custom_view_order_content( $order_id ) {

    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // מסתיר תוכן ברירת מחדל של ווקומרס
    remove_action( 'woocommerce_view_order', 'woocommerce_order_details_table', 10 );
    remove_action( 'woocommerce_view_order', 'woocommerce_order_again_button', 20 );

    // ------ דוגמת תוכן שלך, אבל עם משתנים דינמיים: ------
    $customer = $order->get_formatted_billing_full_name();
    $order_date = wc_format_datetime( $order->get_date_created(), 'd/m/Y H:i' );
    $phone = $order->get_billing_phone();
    $email = $order->get_billing_email();
    $payment = $order->get_payment_method_title();
    $shipping_method = $order->get_shipping_method();
    $address = $order->get_formatted_billing_address();

    ?>
    
    <div class="view_order_wrapper">
        <p>
            <?php
				the_custom_logo();
			?>
        <p>
			<strong>
				<?php 
				printf(
					/* translators: %s = customer name */
					esc_html__( 'Hello %s,', 'madimz' ),
					esc_html( $customer )
				);
				?>
			</strong>
		</p>
		<p>
			<?php printf(
				__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
				'<mark class="order-number">' . $order->get_order_number() . '</mark>',
				'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>',
				'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>'
			); ?>
		</p>

        <p><?php echo esc_html__( 'פרטיך כפי שהתקבלו:', 'madimz' ); ?></p>

		<p style="text-align: center; font-size: 18px;">
			<?php 
			printf(
				/* translators: 1: phone, 2: email, 3: payment method */
				esc_html__( 'טלפון: %1$s, אימייל: %2$s, שיטת תשלום: %3$s', 'madimz' ),
				esc_html( $phone ),
				esc_html( $email ),
				esc_html( $payment )
			);
			?>
		</p>

		<p>
			<?php 
			printf(
				/* translators: 1: address, 2: shipping method */
				esc_html__( 'כתובת: %1$s, אספקה באמצעות: %2$s', 'madimz' ),
				wp_kses_post( $address ),
				esc_html( $shipping_method )
			);
			?>
		</p>

		<?php 
		$customer_note = $order->get_customer_note();

		if ( $customer_note ) {
			echo '<p class="madimz-customer-note">';
			echo esc_html__( 'הערות להזמנה:', 'madimz' ) . ' ' . esc_html( $customer_note );
			echo '</p>';
		}

		?>

        <?php 
		    // הדפסת טבלת הפריטים הדיפולטיבית
			wc_get_template(
				'order/order-details.php',
				array(
					'order_id' => $order_id,
					'order'    => $order
				)
			); 
		?>

		<p class="madimz-payment-method">
			<?php 
			printf(
				/* translators: %s = payment method */
				esc_html__( 'שיטת תשלום: %s', 'madimz' ),
				esc_html( $payment )
			);
			?>
		</p>

		<p class="madimz-join-us">
			<?php echo esc_html__( 'אם עוד לא הצטרפתם — בואו להיות חברים שלנו ותקבלו עדכונים על כל ההטבות והמבצעים!', 'madimz' ); ?>
			<br>
			<a href="/"><?php echo esc_html__( 'ממש כאן!', 'madimz' ); ?></a>
		</p>

		<br>

		<p class="madimz-available"><?php echo esc_html__( 'זמינים לכל שאלה:', 'madimz' ); ?></p>

		<p class="madimz-phone">
			<?php echo esc_html__( 'טלפון:', 'madimz' ); ?>
			<a href="tel:03-9308140">03-9308140</a>
		</p>

		<p class="madimz-email">
			<?php echo esc_html__( 'מייל:', 'madimz' ); ?>
			<a href="mailto:mz3@zahav.net.il">mz3@zahav.net.il</a>
		</p>

		<br>

		<p class="madimz-thanks"><strong><?php echo esc_html__( 'תודה שקנית אצלנו!', 'madimz' ); ?></strong></p>
		<p class="madimz-company"><?php echo esc_html__( 'מדים זיוה בע״מ', 'madimz' ); ?></p>

     

    </div>

    <?php
}


add_action( 'woocommerce_order_item_meta_start', function( $item_id, $item, $order ) {

    $product = $item->get_product();
    if ( ! $product ) return;

    $image = wp_get_attachment_image_src( $product->get_image_id(), 'thumbnail' );
    if ( ! $image ) return;

    echo '<div class="madimz-order-item-image" style="margin-bottom:8px;">';
    echo '<img src="' . esc_url( $image[0] ) . '" style="width:50px; height:auto;" />';
    echo '</div>';

	$qty       = $item->get_quantity();
    $subtotal  = $item->get_subtotal(); // ללא ריבית
    $unit_price = $qty > 0 ? $subtotal / $qty : 0;

    echo '<div class="madimz-unit-price">';
	echo wc_price( $unit_price ) ;
    //echo sprintf( __( 'מחיר ליחידה: %s', 'madimz' ), wc_price( $unit_price ) );
    echo '</div>';




}, 10, 3 );


add_action( 'woocommerce_account_view-order_endpoint', 'madimz_add_duplicate_cart_button_before_title', 7 );
function madimz_add_duplicate_cart_button_before_title( $order_id ) {

    if ( ! $order_id ) {
        return;
    }

    ?>
    <div id="customer_duplicate" class="madimz-duplicate-wrapper" style="margin-bottom:30px; text-align:center;">

        <h2 class="customer_my_duplicate_order_title">
            <?php echo esc_html__( 'שכפול עגלה', 'madimz' ); ?>
        </h2>

        <a href="<?php echo esc_url( add_query_arg( array(
            'duplicate_order' => $order_id,
            '_wpnonce'        => wp_create_nonce( 'duplicate_order_' . $order_id )
        ), wc_get_cart_url() ) ); ?>" 
        class="button customer-duplicate-order-button"
        onclick="return confirm('<?php echo esc_js( __( 'שים לב — שכפול העגלה ימחק את העגלה הנוכחית ויטעין את פריטי הזמנה זו. האם להמשיך?', 'madimz' ) ); ?>');">

            <?php echo esc_html__( 'שכפל עגלה', 'madimz' ); ?>

        </a>
    </div>
    <?php
}

add_action( 'woocommerce_account_view-order_endpoint', 'madimz_add_order_status_box', 9 );
function madimz_add_order_status_box( $order_id ) {

    if ( ! $order_id ) return;

    $order = wc_get_order( $order_id );
    if ( ! $order ) return;

    // סטטוס בעברית
    $status_label = esc_html__( 'ההזמנה ', 'madimz' ).wc_get_order_status_name( $order->get_status() );

    // לינק לפנייה
    $contact_url = add_query_arg( array(
        'msg'        => 'אשמח לקבל עדכון לגבי הזמנה זו - ' . $order_id,
        'ask_about'  => 'true'
    ), site_url('/contact') );

    ?>
    
    <div class="my_order" style="margin-bottom:30px;">

        <h2 class="my_order_title">
            <?php echo esc_html__( 'עדכון מצב ההזמנה', 'madimz' ); ?>
        </h2>

        <div class="customer_ask_about">
            <?php echo esc_html__( 'לפתיחת פנייה -', 'madimz' ); ?>
            <a href="<?php echo esc_url( $contact_url ); ?>">
                <?php echo 'אשמח לקבל עדכון לגבי הזמנה זו - ' . esc_html( $order_id ); ?>
            </a>
        </div>

        <div class="comment">
            <p><?php echo esc_html( $status_label ); ?></p>
        </div>

    </div>

    <?php
}

//יצירת אימייל על נטישות עגלה
add_filter('woocommerce_email_classes', 'register_abandoned_order_email', 10, 1);
function register_abandoned_order_email($emails) {
    require_once get_stylesheet_directory() . '/custom-emails/class-wc-email-abandoned-order.php';
    $emails['WC_Email_Abandoned_Order'] = new WC_Email_Abandoned_Order();
    return $emails;
}

/**
 * Schedule abandoned order email 15 minutes after order creation
 */
add_action('woocommerce_checkout_order_processed', 'madimz_schedule_abandoned_email');
function madimz_schedule_abandoned_email($order_id) {

    // לא לקבוע פעמיים
    if (! wp_next_scheduled('madimz_send_abandoned_order_email', array($order_id))) {
        wp_schedule_single_event(time() + 15 * 60, 'madimz_send_abandoned_order_email', array($order_id));
    }
}


/**
 * Send abandoned order email only if still pending
 */
add_action('madimz_send_abandoned_order_email', 'madimz_process_abandoned_order');
function madimz_process_abandoned_order($order_id) {

    $order = wc_get_order($order_id);
    if (! $order) return;

    // שולחים רק אם עדיין ממתין לתשלום
    if ($order->get_status() !== 'pending' || $order->get_status() !== 'on-hold') {
        return;
    }

    // שולחים רק אם לא נשלח כבר
    if (get_post_meta($order_id, '_abandoned_email_sent', true)) {
        return;
    }

    // שליחת המייל
    do_action('send_abandoned_order_email', $order_id);

    // סימון שנשלח
    update_post_meta($order_id, '_abandoned_email_sent', time());
}









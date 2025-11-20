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



add_action('after_password_reset', function($user, $new_pass) {
    update_user_meta($user->ID, 'has_changed_password', 1);
}, 10, 2);


// add_action('login_init', function() {

//     // אם אנחנו בדף resetpass אחרי שינוי סיסמה
//     if ( isset($_GET['action']) && $_GET['action'] === 'resetpass' ) {

//         // הפניה ל-my-account
//         wp_safe_redirect( site_url('/my-account/') );
//         exit;
//     }
// });



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





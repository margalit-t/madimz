<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package madimz
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'madimz' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="top-header">
			<div id="site-search">
				<?php get_product_search_form(); ?>
			</div><!-- .site-navigation -->

			<div class="site-branding">
				<?php
					the_custom_logo();
				?>
			</div><!-- .site-branding -->

			<ul class="header-right-icons">
				<!-- Business Login -->
				<li class="account-btn">
					<button type="button" class="header-link red-btn bussiness-btn" aria-label="<?php esc_html_e( 'כניסה לעסקים', 'madimz' ); ?>">
						<?php esc_html_e( 'כניסה לעסקים', 'madimz' ); ?>
					</button>
				</li>

				<!-- My Account / Login -->
				<li class="account-icon login-icon">
					<button type="button" class="header-link" aria-label="<?php esc_html_e( 'login', 'madimz' ); ?>">
						<?php echo inline_svg_with_class('user.svg', '');?>
					</button>
					<?php if(false): ?>
					<?php if(is_user_logged_in()): ?>
						<ul class="dropdown-login">
							<li><a href="<?php echo wc_get_page_permalink( 'myaccount' );?>"><?php esc_html_e( 'My account', 'madimz' ); ?></a></li>
							<li><a href="<?php echo wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );?>"><?php esc_html_e( 'My orders', 'madimz' ); ?></a></li>
							<li><a href="<?php echo esc_url( wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'madimz' ); ?></a></li>
						</ul>
					<?php endif;?>
					<?php endif;?>
				</li>

				<!-- Cart -->
				<li class="account-icon cart-icon">
					<button class="header-link open-mini-cart" aria-label="<?php esc_html_e( 'cart', 'madimz' ); ?>">
						<?php echo inline_svg_with_class('cart.svg', '');?>
						<?php
						$cart_count = WC()->cart->get_cart_contents_count();
						if ( $cart_count > 0 ) : ?>
							<span class="cart-count">(<?php echo esc_html( $cart_count ); ?>)</span>
						<?php endif; ?>
					</button>
				</li>
			</ul><!-- .header-right-icons -->
		</div><!-- .top-header -->

		<nav id="site-navigation" class="main-navigation">
			<!-- <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php //esc_html_e( 'Menu', 'madimz' ); ?></button> -->
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'menu-1',
					// 'menu_id'        => 'primary-menu',
					'menu_class'     => 'primary-menu',
					'container_id'	 => 'menu-main-menu-container',
				)
			);
			?>
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

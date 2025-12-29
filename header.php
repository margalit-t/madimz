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

	<!-- icon WhatsApp sticky in pages -->
	<?php if ( ! is_admin() ) : ?>
		<?php			
			// WhatsApp number
			$wa_number = get_field( 'whatsapp_number_sticky', 'option');
			$wa_message = wp_kses_post( get_field( 'whatsapp_text_message', 'option' ) );
			$wa_message = $wa_message ?? '';
			$wa_link = "https://wa.me/$wa_number?text=$wa_message";
			$wa_img = get_field( 'img_whatsapp_number', 'option' );
		    $small = wp_get_attachment_image_src($wa_img['ID'], 'small'); 
		?>
		<a href="<?php echo esc_url($wa_link); ?>"
			class="whatsapp-float"
			target="_blank"
			rel="noopener"
			aria-label="פנייה ב־WhatsApp">
			<?php if ( $wa_img ): ?>
			 	<img src="<?php echo esc_url( $small[0] ); ?>" alt="icon whatsapp">
			<?php endif; ?>
		</a>
	<?php endif; ?>

	<!-- icon Wishlist sticky in pages -->
	<?php if ( ! is_admin() ) : ?>
		<div class="btn-wishlist-float">
			<a href="<?php echo home_url( '/wishlist/' ); ?>" class="wishlist-float" target="_self" rel="noopener" aria-label="רשימת משאלות - wishlist">
				<?php
					$count = 0;
					if ( class_exists( 'MadimzWishlist' ) ) {
						$count = count( MadimzWishlist::get_wishlist() );
					}
					?>
				<span class="counter"><?php echo esc_html( $count ); ?></span>
				<?php echo inline_svg_with_class('heart-outline.svg', '');?>
			</a>
		</div>
	<?php endif; ?>

<?php wp_body_open(); ?>

<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'madimz' ); ?></a>

	<header id="masthead" class="site-header">
		<div class="top-header">
			<!-- <div id="site-search">
				<?//php get_product_search_form(); ?>
			</div> -->
			
			<div class="header-left-icons">
				
				<!-- Hamburger - for mobile -->
				<button type="button" class="menu-toggle hidden-desktop" aria-label="Open menu" aria-expanded="false">
					<?php echo file_get_contents(get_template_directory_uri() . '/dist/images/menu.svg'); ?>
				</button>

				<!-- search -->
				<div class="search-wrapper">
					<div class="search-icon">
						<?php echo inline_svg_with_class('search.svg', '');?>
					</div>
					<input type="text" id="live-search" placeholder="<?php esc_html_e( 'מה אתם מחפשים?', 'madimz' ); ?>" autocomplete="off">
					<div id="search-results"></div>
				</div><!-- .search-wrapper -->
			</div><!-- .header-left-icons -->

			<div class="site-branding hidden-mobile">
				<?php
					the_custom_logo();
				?>
			</div><!-- .site-branding -->

			<ul class="header-right-icons">
				<!-- shop -->
				<li class="shop-btn">
					<a type="button" class="header-link red-btn" aria-label="<?php esc_html_e( 'כניסה לחנות', 'madimz' ); ?>" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
						<?php esc_html_e( 'כניסה לחנות', 'madimz' ); ?>
					</a>
				</li>

				<div class="site-branding hidden-desktop">
					<?php
						the_custom_logo();
					?>
				</div><!-- .site-branding -->

				<!-- My Account / Login -->
				 <li class="account-icon login-icon">
					<?php if ( is_user_logged_in() ) : ?>
						<a class="header-link" aria-label="<?php esc_html_e( 'החשבון שלי', 'madimz' ); ?>" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
							<?php echo inline_svg_with_class('user.svg', ''); ?>
						</a>
						<?php if(false): ?>
							<?php if(is_user_logged_in()): ?>
								<ul class="dropdown-login">
									<li><a href="<?php echo wc_get_page_permalink( 'myaccount' );?>"><?php esc_html_e( 'My account', 'madimz' ); ?></a></li>
									<li><a href="<?php echo wc_get_endpoint_url( 'orders', '', wc_get_page_permalink( 'myaccount' ) );?>"><?php esc_html_e( 'My orders', 'madimz' ); ?></a></li>
									<li><a href="<?php echo esc_url( wp_logout_url( wc_get_page_permalink( 'myaccount' ) ) ); ?>"><?php esc_html_e( 'Sign out', 'madimz' ); ?></a></li>
								</ul>
							<?php endif;?>
						<?php endif;?>
					<?php else : ?>
						<a class="header-link" aria-label="<?php esc_html_e( 'התחברות', 'madimz' ); ?>" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>">
							<?php echo inline_svg_with_class('user.svg', ''); ?>
						</a>
					<?php endif; ?>
				</li>

				<!-- copmare products -->
				<?php if( class_exists( 'MadimzProductCompare' ) && ( $product_compare_page = MadimzProductCompare::get_compare_list_url() ) ): ?>
					<li class="account-icon compare-icon hidden-mobile">
						<a href="<?php echo esc_attr( $product_compare_page ); ?>" class="header-link">
							<?php echo inline_svg_with_class('compare.svg', '');?>
						</a>
					</li>
				<?php endif; ?>

				<!-- Cart -->
				<li class="account-icon cart-icon">
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="header-link open-mini-cart">
						<?php echo inline_svg_with_class('cart.svg', '');?>
						<?php
						$cart_count = WC()->cart->get_cart_contents_count();
						if ( $cart_count > 0 ) : ?>
							<span class="cart-count"><?php echo esc_html( $cart_count ); ?></span>
						<?php endif; ?>
					</a>
					<?php if(false): ?>
					<button class="header-link open-mini-cart" aria-label="<?php esc_html_e( 'cart', 'madimz' ); ?>">
						<?php echo inline_svg_with_class('cart.svg', '');?>
						<?php
						$cart_count = WC()->cart->get_cart_contents_count();
						if ( $cart_count > 0 ) : ?>
							<span class="cart-count"><?php echo esc_html( $cart_count ); ?></span>
						<?php endif; ?>
					</button>
					<?php endif; ?>
				</li>
			</ul><!-- .header-right-icons -->
		</div><!-- .top-header -->

		<!-- Navigation -->
		<nav id="site-navigation" class="desktop-navigation hidden-mobile">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					// 'menu_id'        => 'primary-menu',
					'menu_class'     => 'primary-menu',
					'container'      => false,
					// 'container_id'	 => 'menu-main-menu-container',
				)
			);
			?>
		</nav><!-- #site-navigation -->

		<!-- Navigation Mobile-->
		<nav class="mobile-navigation hidden-desktop" aria-hidden="true">
			<?php
				wp_nav_menu(
					array(
						'theme_location' => 'mobile-menu',
						// 'menu_id'        => 'primary-menu',
						'menu_class'     => 'mobile-menu',
						'container'      => false,
					)
				);
			?>
		</nav><!-- #site-navigation -->

	</header><!-- #masthead -->

    <?php if ( !is_admin() || !is_front_page() ) : ?>
 
		<div class="madimz-breadcrumb-wrapper">
			<?php
			if ( function_exists( 'woocommerce_breadcrumb' ) ) {
				woocommerce_breadcrumb();
			}
			?>
		</div>
    <?php endif; ?>

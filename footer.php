<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package madimz
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="footer-content">
			<div class="footer-columns">
				<div class="footer-column">
					<?php
						wp_nav_menu(
							array(
								'theme_location' 	=> 'footer-navigation-1',
								'container'	 		=> 'nav',
								'items_wrap' 		=> '<h3>' . esc_html( wp_get_nav_menu_name( 'footer-navigation-1' ) ) . '</h3><ul class="%2$s">%3$s</ul>',
								'menu_class' 		=> 'column',
							)
						);
					?>
				</div>
				<div class="footer-column">
					<?php
						wp_nav_menu(
							array(
								'theme_location' 	=> 'footer-navigation-2',
								'container'	 		=> 'nav',
								'items_wrap' 		=> '<h3>' . esc_html( wp_get_nav_menu_name( 'footer-navigation-2' ) ) . '</h3><ul class="%2$s">%3$s</ul>',
								'menu_class' 		=> 'column',
							)
						);
					?>
				</div>
				<?php if( class_exists('ACF') ): ?>
					<div class="footer-column footer-contact-content">
						<?php if ( $title = get_field('talk_us_title', 'option') ) : ?>
							<h3><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>
						
						<?php if ( $contact_content = get_field('contact_information', 'option') ) : ?> 
							<div class="contact-information"><?php echo wp_kses_post( $contact_content ); ?> </div>
						<?php endif; ?>
					</div>
					<?php if ( have_rows('social_icons', 'option') ) : ?>
						<div class="footer-column footer-social-icons">
							<?php if ( $title = get_field('social_icons_title', 'option') ) : ?>
								<h3><?php echo esc_html($title); ?></h3>
							<?php endif; ?>
							<ul class="social-list">
								<?php while ( have_rows('social_icons', 'option') ): the_row(); ?>
									<?php
										$icon = get_sub_field('icon');
										$url = get_sub_field('url');
									?>

									<?php if ( $icon && $url ): ?>
										<li>
											<a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Social link">
												<img width="100%" height="100%" src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt'] ?: 'Social Icon'); ?>" />
											</a>
										</li>
									<?php endif; ?>
										
								<?php endwhile; ?>
							</ul>
						</div>
					<?php endif; ?>
				<?php endif; ?>

			</div><!-- .footer-columns -->
			<div class="footer-information">
				<?php if ( have_rows('payment_options_icons', 'option') ) : ?>
					<ul class="payment-options-list">
						<?php while ( have_rows('payment_options_icons', 'option') ): the_row(); ?>
							<?php
								$icon = get_sub_field('icon');
							?>
							<?php if ( $icon ): ?>
								<li>
									<img width="100%" height="100%" src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($icon['alt'] ?: 'Payment Options Icon'); ?>" />
								</li>
							<?php endif; ?>

						<?php endwhile; ?>
					</ul>						
				<?php endif; ?>
				<span class="powered"><?php esc_html_e( 'powered by SimplyCT Creative Technology', 'madimz' ); ?></span>
			</div><!-- .footer-information -->
		</div><!-- .footer-content -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>

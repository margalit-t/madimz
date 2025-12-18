<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package madimz
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title"><?php esc_html_e( 'הדף שניסית להגיע אליו כבר לא קיים.', 'madimz' ); ?></h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<p><?php esc_html_e( 'הדף שניסית להגיע אליו כבר לא קיים.', 'madimz' ); ?></p>
				<a href="<?php echo home_url();?>"><?php esc_html_e( 'חזרה לדף הבית', 'madimz' ); ?></a>
			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_template_part('template-parts/form', 'footer');

get_footer();

<?php
    /*
    Template Name: Contact
    */

    get_header();

    $title_contact          = get_field('title_contact');
    $sub_title_contact      = get_field('sub_title_contact');
    $form_id                = get_field('form_id');
    $text_contact_title     = get_field('text_contact_title'); 
    $contact_content        = get_field('contact_information', 'option');
?>

<!-- //Add return-to-product link -->
<?php
    $pid = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

    $product_title = '';
    $product_link  = '';

    if ($pid) {
        $product = wc_get_product($pid);
        if ($product) {
            $product_title = $product->get_name();
            $product_link  = get_permalink($pid);
        }
    }
?>

<div class="contact-wrapper container">
    <div class="right-side contact-form">

        <?php if ($product_title): ?>
            <p class="contact-return-link">
                <a href="<?php echo esc_url($product_link); ?>">
                    <?php echo sprintf(
                        __( 'חזרה ל- %s', 'madimz' ),
                        esc_html( $product_title )
                    ); ?>
                </a>
            </p>
        <?php endif; ?>

        <?php if ( $title_contact && $product_title ) : ?>
            <h2 class="contact-title">
                <?php 
                    echo sprintf(
                        __( '%s - מעוניין לקבל פרטים לגבי %s', 'madimz' ),
                        esc_html( $title_contact ),
                        esc_html( $product_title )
                    );
                ?>
            </h2>
        <?php endif; ?>
        <?php if ( $title_contact && !$product_title ) : ?>
            <h2 class="contact-title">
                <?php echo esc_html( $title_contact ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( $sub_title_contact ) : ?>
            <p class="contact-sub-title">
                <?php echo esc_html( $sub_title_contact ); ?>
            </p>
        <?php endif; ?>

        <?php if( $form_id ) : ?>
            <div id="cf7-product-data"
                data-product-id="<?php echo esc_attr($pid); ?>"
                data-product-title="<?php echo esc_attr($product_title); ?>">
            </div>
            <div class="form-content">
                   <?php echo do_shortcode('[contact-form-7 id="' . $form_id . '" title="Contact"]'); ?>
            </div>
        <?php endif; ?>

    </div>
    <div class="left-side contact">
        <?php if ( $text_contact_title ) : ?>
            <h2 class="contact-title">
                <?php echo esc_html( $text_contact_title ); ?>
            </h2>
        <?php endif; ?>

        <?php if ( $contact_content ) : ?> 
            <div class="desc-contact"><?php echo wp_kses_post( $contact_content ); ?> </div>
        <?php endif; ?>
    </div>
</div>

<?php get_template_part('template-parts/form', 'footer'); ?>
<?php get_footer();  ?>
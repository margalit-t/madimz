<!-- template-parts/slide-footer.php -->

<section id="form_contact" class="section-wrap banner-contact-wrapper">
    <?php $img_bg = get_field( 'background_image','option' ); ?>
    <div class="banner-contact" style="background-image: url('<?php echo esc_url( $img_bg ?: '' ); ?>');">
        <div class="contact-content">
            <h3><?php echo esc_html( get_field( 'contact_title','option' ) ); ?></h3>
            <h4><?php echo wp_kses_post( get_field( 'contact_sub-title','option' ) ); ?></h4>
        </div>
        <div class="contact-form">
            <?php 
            $cf7_form_id = get_field('select_cf7_form','option'); // Get the selected form ID from ACF
            if( $cf7_form_id ) {
                echo do_shortcode('[contact-form-7 id="' . $cf7_form_id . '" title="Contact form banner"]');
            }
            ?>
        </div>
    </div>
</section>
        
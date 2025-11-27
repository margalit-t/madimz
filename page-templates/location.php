<?php
    /*
    Template Name: Location
    */

    get_header();

?>

<div class="location_wrapper">
    <?php
    $title_address = get_field('title_address');
    $address      = get_field('address');
    $waze_link    = get_field('waze_link');
    $waze_img     = get_field('waze_img'); 
    $medium = wp_get_attachment_image_src($waze_img['ID'], 'medium'); 
    $hours_title  = get_field('hours_title');
    $hours        = get_field('hours');
    $embed_map    = get_field('embed_map');
    ?>
    <?php if ( $title_address || $address ) : ?>
        <div class="contact-address">
            <?php if ( $title_address ) : ?>
                <h2 class="contact-address-title">
                    <?php echo esc_html( $title_address ); ?>
                </h2>
            <?php endif; ?>

            <?php if ( $address ) : ?>
                <p class="contact-address-text">
                    <?php echo nl2br( esc_html( $address ) ); ?>
                </p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php if ( $waze_link || $waze_img ) : ?>
        <div class="contact-waze">
            <?php if ( $waze_link ) : ?>
                <a href="<?php echo esc_url( $waze_link ); ?>" target="_blank" rel="noopener">
                    
                    
                    <img src="<?php echo esc_url( $medium[0] ); ?>" alt="Waze">
                    
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
        <?php if ( $hours_title || $hours ) : ?>
        <div class="contact-hours">
            <?php if ( $hours_title ) : ?>
                <h3 class="contact-hours-title">
                    <?php echo esc_html( $hours_title ); ?>
                </h3>
            <?php endif; ?>

            <?php if ( $hours ) : ?>
                <div class="contact-hours-text">
                    <?php
                    // אם את מזינה שורות מרובות — שיהפוך ל־<br>
                    echo nl2br( esc_html( $hours ) );
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>


    <?php if ( $embed_map ) : ?>
        <div class="contact-map">
            <?php
            echo $embed_map;
            ?>
        </div>
    <?php endif; ?>

</div>

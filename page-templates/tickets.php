<?php
    /*
    Template Name: Tickets
    */

    get_header();

?>

<div class="ticket-success">
    <h2>
        <span><?php  echo esc_html( 'פנייתך התקבלה בהצלחה, מספר פנייה - ', 'madimz' ); ?></span>
        <span id="ticket-number"></span>
    </h2>

    <p><?php  echo esc_html( 'אישור פנייה נשלח למייל.', 'madimz' ); ?></p>
    <p><?php  echo esc_html( 'שמחים לעמוד לרשותך, צוות החנות.', 'madimz' ); ?></p>
    
    <a class="back-btn red-btn" href="<?php echo home_url(); ?>"><?php  echo esc_html( 'חזרה לדף הבית', 'madimz' ); ?></a>  
</div>


<?php get_template_part('template-parts/form', 'footer'); ?>
<?php get_footer();  ?>
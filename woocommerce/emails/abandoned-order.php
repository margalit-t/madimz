<?php

use Automattic\WooCommerce\Utilities\FeaturesUtil;

if ( ! defined( 'ABSPATH' ) ) exit;

$order = $order ?? null;
$sent_to_admin = false;
$plain_text = false;
$email_improvements_enabled = FeaturesUtil::feature_is_enabled( 'email_improvements' );
$additional_content = '';

?>


<div dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>" 
     style="direction: rtl; height: auto; padding: 30px; border: 6px solid #2f2483; margin-bottom: 40px;">
	<?php $img = get_option( 'woocommerce_email_header_image' );?>

	<?php if ( $img ) : ?>
		<div style="text-align:center; margin-bottom:25px;">
			<img src="<?php echo esc_url( $img ); ?>" 
				alt="<?php echo esc_attr__( 'לוגו', 'madimz' ); ?>" 
				style="">
		</div>
	<?php endif; ?>

    <p>
        <?php printf( __('שלום %s,', 'madimz'), $order->get_billing_first_name() ); ?>
    </p>

    <p><?php echo __('שמנו לב שהתחלת לבצע הזמנה אצלנו באתר אך לא סיימת.', 'madimz'); ?></p>

    <p><?php echo __('במידה ויש בעיה באתר או שיש לך שאלות לגבי מוצר כלשהו או לגבי זמן אספקה של המוצר — תוכל/י לפנות אלינו!', 'madimz'); ?></p>

    <p>
        <?php echo __('זמינים לכל שאלה:', 'madimz'); ?><br>
        <?php echo __('בטלפון:', 'madimz'); ?> 03-9308140<br>
        <?php echo __('או במייל:', 'madimz'); ?> mz3@zahav.net.il
    </p>

    <p>
        <?php echo __('בברכה,', 'madimz'); ?><br>
        <?php echo __('מדים זיוה בע״מ', 'madimz'); ?>
    </p>

</div>


<!-- 🟢 החלק הסטנדרטי של ווקומרס — EXACT מה שביקשת -->

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Additional content (from email settings)
 */
if ( $additional_content ) {
	echo $email_improvements_enabled ? 
		'<table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td class="email-additional-content">' : '';

	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );

	echo $email_improvements_enabled ? '</td></tr></table>' : '';
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

?>

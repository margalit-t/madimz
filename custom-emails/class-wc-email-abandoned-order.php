<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Email_Abandoned_Order extends WC_Email {

    public function __construct() {

        $this->id          = 'abandoned_order';
        $this->title       = 'הזמנתך טרם הושלמה';
        $this->description = 'מייל הנשלח ללקוחות שלא השלימו הזמנה במצב ממתין לתשלום.';
        $this->heading     = 'התחלת לבצע הזמנה אצלנו באתר';
        $this->subject     = 'הזמנתך באתר מדים זיוה טרם הושלמה';

        $this->template_base  = WC()->template_path();
        $this->template_html  = 'emails/abandoned-order.php';
        $this->template_plain = 'emails/plain/abandoned-order.php';

        add_action('send_abandoned_order_email', array($this, 'trigger'));

        parent::__construct();
        $this->recipient = '';
    }

    public function trigger($order_id) {
        if (!$order_id) return;

        $order = wc_get_order($order_id);
        if (!$order) return;

        $this->object    = $order;
        $this->recipient = $order->get_billing_email();

        if (!$this->is_enabled() || !$this->get_recipient()) return;

        $this->send(
            $this->get_recipient(),
            $this->get_subject(),
            $this->get_content(),
            $this->get_headers(),
            $this->get_attachments()
        );
    }

    public function get_content_html() {
        return wc_get_template_html(
            $this->template_html,
            array('order' => $this->object),
            '',
            ''
        );
    }
}

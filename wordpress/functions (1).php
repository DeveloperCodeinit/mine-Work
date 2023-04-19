<?php

/* --------------------------------------------- WECEELL FUNCTION FILE --------------------------------------------- */
//namespace EasyPost;
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) , 6.2 );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );


function my_enqueue1($hook) {
    // Only add to the edit.php admin page.
    // See WP docs.
    // if ('edit.php' !== $hook) {
    //     return;
    // }
    wp_enqueue_script('my_custom_script1', trailingslashit( get_stylesheet_directory_uri() ) . 'wecell.js',  array('jquery'), 17.79, true );
}

add_action('admin_enqueue_scripts', 'my_enqueue1');
// END ENQUEUE PARENT ACTION

//WOO theme Support
function mytheme_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );
////////////////////////////////////////////////////////////////////////
///// WOo theme Support end
include_once('home_custom_slider.php');

include_once('shortcode.php');
include_once('shortcode1.php');
/////////////////////////////////////////////////////////////
add_filter( 'wp_mail_from', 'my_mail_from' , 999 );
function my_mail_from( $email ) {
return "buy@wecelltrade.com";
}

add_filter( 'wp_mail_from_name', 'my_mail_from_name' , 999 );
function my_mail_from_name( $name ) {
return "WeCellTrade";
}
add_filter('wp_mail', 'ws_add_site_header');
function ws_add_site_header($args) {
    $new_header = array('headers' => 'X-WU-Site: ' . parse_url(get_site_url(), PHP_URL_HOST));
    return wp_parse_args($args, $new_header);
}
//////////////////////////////////////////////////////////////////
add_filter( 'default_checkout_billing_state', 'change_default_checkout_state' );
function change_default_checkout_state() {
        if(!is_user_logged_in()){
            return ''; // state code
        }else{
            $user = wp_get_current_user(); // The current user
            $state = $user->billing_state;
            return $state;
        }
}
////////////////////////////////////////////////////////////////////
add_action( 'woocommerce_after_shipping_rate', 'action_after_shipping_rate', 20, 2 );
function action_after_shipping_rate ( $method, $index ) {
    // Targeting checkout page only:
    if( is_cart() ) return; // Exit on cart page
    //print_r($method);
    if( 'flat_rate:12' === $method->id ) {
        echo __("<ul class='flat_rate' ><li>We will send you a box, packing supplies, and a prepaid return label for your device(s)</li><li>Pack your device(s)</li><li>Drop off at any USPS location or mailbox</li> </ul>");
    }
     
    if( 'wf_easypost_id:First' === $method->id ) {
        echo __("<ul class='easypost_first' style='display:none'><li>Once checkout is complete, we will provide a prepaid USPS label</li><li>Pack your device(s)</li><li>Drop off at any USPS location or mailbox</li></ul>");
    }
    if( 'wf_easypost_id:Priority' === $method->id ) {
        echo __("<ul class='easypost_priority' style='display:none'><li>Once checkout is complete, we will provide a prepaid USPS label</li><li>Pack your device(s)</li><li>Drop off at any USPS location or mailbox</li></ul>");
    }
    if( 'wf_easypost_id:FEDEX_GROUND' === $method->id ) {
        echo __("<ul class='easypost_fedex' style='display:none'><li>Once checkout is complete, we will provide a prepaid FedEx label</li><li>Pack your device(s)</li><li>Drop off at any FedEx location or mailbox</li></ul>");
    }
    if( 'wf_easypost_id:Ground' === $method->id ) {
        echo __("<ul class='easypost_ups' style='display:none'><li>Once checkout is complete, we will provide a prepaid UPS label</li><li>Pack your device(s)</li><li>Drop off at any UPS location or mailbox</li></ul>");
    }
    
}
 
// Limit Woocommerce phone field to 10 digits number

add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

  

function my_custom_checkout_field_process() {

    global $woocommerce;

  

    // Check if set, if its not set add an error. This one is only requite for companies

    if ( ! (preg_match('/^[0-9]{10}$/D', $_POST['billing_phone'] ))){

        wc_add_notice( "Incorrect Phone Number! Please enter valid 10 digits phone number"  ,'error' );

    }

}
/////////////////////////////////////////////////////////////////////
function custom_shop_page_redirect() {
    if( is_shop() ){
        //get_header( 'shop' );
      
        echo do_shortcode('[woo_parent_category]');
    
    //get_footer( 'shop' );
    
    }
}
// add_action( 'wp_head', 'custom_shop_page_redirect' );
// add_filter( 'woocommerce_continue_shopping_redirect', 'bbloomer_change_continue_shopping' );
 
// function bbloomer_change_continue_shopping() {
//    return site_url().'/sell/';
// }

add_filter( 'wc_order_is_editable', 'wc_make_processing_orders_editable', 10, 2 );
function wc_make_processing_orders_editable( $is_editable, $order ) {
   // if ( $order->get_status() == 'processing' ) {
        $is_editable = true;
    //}

    return $is_editable;
}
  add_filter( 'woocommerce_package_rates', 'override_ups_rates' );
function override_ups_rates( $rates ) {
    foreach( $rates as $rate_key => $rate ){
        // Check if the shipping method ID is UPS
         
        //if ( is_admin() ){
            // Set cost to zero
            $rates[$rate_key]->cost = 0;
       // }
        
    }
    return $rates;        
}


add_action('wp_mail_failed', function ($error) {
    wp_die("<pre>".print_r($error, true)."</pre>");
});


// add_filter( 'woocommerce_order_get_items', 'custom_order_get_items', 10, 3 );
// function custom_order_get_items( $items, $order, $types ) {
//     if ( is_admin() && $types == array('shipping') ) {
//         $items = array();
//     }
//     return $items;
// }

add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
  #order_data p.order_number {
      display:none;
    } 
   #order_shipping_line_items td.item_cost {
    display: none;
}
#order_shipping_line_items td.quantity {
    display: none;
}
#order_shipping_line_items td.line_cost {
    display: none;
}
#order_shipping_line_items td.wc-order-edit-line-item {
    display: none;
}
#order_shipping_line_items .view table.display_meta {
    display: none;
}
.wc-order-totals tr:nth-child(2) {
   display: none;
}
#the-list span.description:nth-child(1) {
    display: none;
}
#order_shipping_line_items tr.shipping {
    display: none;
}
#shipping_method label.active {
    width: 39% !important;
    height: fit-content;
}
#the-list span.description {
    display: none;
}
  </style>';
}
// add_filter( 'woocommerce_order_get_items', 'custom_order_get_items', 10, 3 );
// function custom_order_get_items( $items, $order, $types ) {
//     if ( is_admin() && $types == array('shipping') ) {
//          $items = array();
//         // $order->set_shipping_total(0);
//         // $order->save();
//         // $sub_total =  $order->get_subtotal()   ;
//         // $total =  $order->get_total()   ;
//         // $hipping_cost =    $total - $sub_total;
//         // $order->add_fee($hipping_cost);
//          //$order->set_total( $new_total );
    
//     }
//    //
//     return $items;
// }

//Allow plugins to filter the grand total, and sum the cart totals in case of modifications.
// function filter_woocommerce_calculated_total( $total, $cart ) {
//     // Get shipping total
//     $shipping_total = $cart->get_shipping_total();
    
//     return $total - $shipping_total;
// }
// add_filter( 'woocommerce_calculated_total', 'filter_woocommerce_calculated_total', 10, 2 );



// add_action( 'woocommerce_checkout_order_processed', 'send_email1');

// function send_email1($order_id) {
//     $order = wc_get_order($order_id); //<--check this line
//     if(!$order) return;

//     $order_total  =  get_woocommerce_currency_symbol().number_format($order->get_subtotal(),2);
//     $order_no = $order->get_order_number(); 
//     $order_date = wc_format_datetime( $order->get_date_created() ); 
//     $wf_easypost_label = $order->get_meta('wf_easypost_labels');
//     if(isset($wf_easypost_label ) && !empty($wf_easypost_label)) {
//         $email_sent = get_post_meta( $order_no , 'email_sent',true);
//         $shipping_prepaid = get_post_meta( $order_no , 'shipping_prepaid',true);

//         $headers  = "MIME-Version: 1.0" . "\r\n";
//         $headers .= "Content-type: text/html; charset=".get_bloginfo('charset')."" . "\r\n";
//         $headers .= "From: WeCellTrade <buy@wecelltrade.com>" . "\r\n";
//         //$headers .= "Bcc:  buy@wecelltrade.com\r\n";

//         wp_mail("jatin.codeinit@gmail.com", " device order #$order_no",$wf_easypost_label,$headers);
//     }       
// }

add_action('woocommerce_before_thankyou', 'send_email', 111, 1);
function send_email($order_id){ 


    $order = wc_get_order($order_id); //<--check this line
    if(!$order) return;
   		 
        //echo "<pre>";
            $shipping_menthods = $order->get_shipping_methods(); // same thing than $order->get_items('shipping')
        //  echo "<pre>";
        //     print_r($order);
        //     echo "</pre>";
        // Iterating through order shipping items
        foreach( $order->get_items( 'shipping' ) as $item_id => $item ){
            // Get the data in an unprotected array
            $item_data = $item->get_data();

            // $shipping_data_id           = $item_data['id'];
            // $shipping_data_order_id     = $item_data['order_id'];
            // $shipping_data_name         = $item_data['name'];
            // $shipping_data_method_title = $item_data['method_title'];
            $shipping_data_method_id[]    = $item_data['method_id'];
            // $shipping_data_instance_id  = $item_data['instance_id'];
        
        }
        //print_r($item_data);

        // echo "</pre>";
    // Conditional function based on the Order shipping method  
    $shipping_method_id = $shipping_data_method_id;
    $order_total  =  get_woocommerce_currency_symbol().number_format($order->get_subtotal(),2);
    $order_no = $order->get_order_number(); 
    $order_date = wc_format_datetime( $order->get_date_created() ); 
    $wf_easypost_label = $order->get_meta('wf_easypost_labels');
    $email_sent = get_post_meta( $order_no , 'email_sent',true);
    $shipping_prepaid = get_post_meta( $order_no , 'shipping_prepaid',true);

        if(isset($wf_easypost_label) && !empty($wf_easypost_label)){
            
                $tracking_number = $wf_easypost_label[0]['tracking_number'];
                $carrier = $wf_easypost_label[0]['carrier'];
                $url = $wf_easypost_label[0]['url'];
                $link = $wf_easypost_label[0]['link'];
                if($email_sent != 1){
                try{
                require( WP_PLUGIN_DIR .'/twilio/vendor/autoload.php');
                $sid = "AC29b0f8d546b4fe11ddac2ea81e084618"; // Your Account SID from www.twilio.com/console
                $token = "57fd0e6bc8c0037ba62808d297b2d743"; // Your Auth Token from www.twilio.com/console
                $client = new Twilio\Rest\Client($sid, $token);
                

                $order_billing_phone =  '+1'.$order->get_billing_phone(); // '+12018889282' ; //
                $body = "WeCellTrade #".$order->get_order_number().": Thank you for choosing us to trade in your device!";  
                
                $message = $client->messages->create(
                   $order_billing_phone , // Text this number
                   [
                     'from' => '+16208371948', // From a valid Twilio number
                     'body' => $body
                   ]
                 );
                }
                catch(Exception $e) {
                          $e->getCode();
                }

                
                if($shipping_prepaid == 1) { 
                         include_once(trailingslashit( get_stylesheet_directory() ) . 'templates/pre_paid_template.php');
                }  else{
                        include_once(trailingslashit( get_stylesheet_directory() ) . 'templates/selling_template.php');
                }
            }
        }
      
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// 

add_action('wp_footer', 'custom_shipping_js');

function custom_shipping_js() {
    if(is_checkout()) { ?>
        <script type="text/javascript">
            jQuery(document.body).on('updated_checkout', function(){
                var radioValue = jQuery("input[name^='shipping_method']:checked").val();
                var radioValue_id = jQuery("input[name^='shipping_method']:checked").attr('id');

                var howMany = jQuery('ul#shipping_method').children().length;

                jQuery('.woocommerce-shipping-methods').find('.radio').removeClass('active');
                jQuery("li #"+radioValue_id).next().addClass('active');

                console.log(radioValue);
                console.log("count-li",howMany);
                console.log("li #"+radioValue_id);

                if(howMany > 0) {
                    jQuery('ul#shipping_method').children('li').each(function(index, value){
                        var current = jQuery(this);
                        var new_radioValue = current.find("input[name^='shipping_method']").val();
                        console.log("radio-value =",new_radioValue);
                        
                        if(new_radioValue == 'flat_rate:12') {
                            var bgImg = "url(https://wecelltrade.com/wp-content/uploads/images/prepaidbox.png)"

                            // jQuery('label[for=shipping_method_0_flat_rate12]').css({"background-image": "url('" + bgImg + "')"
                            // });
                            jQuery("label[for=shipping_method_0_flat_rate12]").attr('style', 'background-image: ' + bgImg + ' !important; ');
                        }
                        
                        if(new_radioValue == 'wf_easypost_id:First') {
                            var bgImg = "url(https://wecelltrade.com/wp-content/uploads/images/usps.png)"

                            //jQuery('label[for=shipping_method_0_wf_easypost_idfirst]').css({"background-image": "url('" + bgImg + "')"
                            //});
                            jQuery("label[for=shipping_method_0_wf_easypost_idfirst]").attr('style', 'background-image: ' + bgImg + ' !important; ');
                        }

                        if(new_radioValue == 'wf_easypost_id:Priority') {
                            var bgImg = "url(https://wecelltrade.com/wp-content/uploads/images/usps.png)"

                            //jQuery('label[for=shipping_method_0_wf_easypost_idfirst]').css({"background-image": "url('" + bgImg + "')"
                            //});
                            jQuery("label[for=shipping_method_0_wf_easypost_idpriority]").attr('style', 'background-image: ' + bgImg + ' !important; ');
                        }                        

                        if(new_radioValue == 'wf_easypost_id:FEDEX_GROUND') {
                            
                            var bgImg = "url(https://wecelltrade.com/wp-content/uploads/images/fedex.png)"

                            // jQuery('label[for=shipping_method_0_wf_easypost_idfedex_ground]').css({"background-image": "url('" + bgImg + "')"
                            // });
                            jQuery("label[for=shipping_method_0_wf_easypost_idfedex_ground]").attr('style', 'background-image: ' + bgImg + ' !important; ');
                        }

                        if(new_radioValue == 'wf_easypost_id:Ground') {
                            
                            var bgImg = "url(https://wecelltrade.com/wp-content/uploads/images/ups.png)"

                            /*jQuery('label[for=shipping_method_0_wf_easypost_idground]').css({"background-image": "url('" + bgImg + "')"
                            });*/

                            jQuery("label[for=shipping_method_0_wf_easypost_idground]").attr('style', 'background-image: ' + bgImg + ' !important; ');
                        }
                    })                   
                }

                if(radioValue_id == "shipping_method_0_flat_rate12"){
                    jQuery('.flat_rate').show();
                    jQuery('.easypost_first').hide();
                    jQuery('.easypost_priority').hide();
                    jQuery('.easypost_fedex').hide();
                    jQuery('.easypost_ups').hide();
                    jQuery('#shipping_prepaid').val(1);
                }
                if(radioValue_id == "shipping_method_0_wf_easypost_idfirst"){
                    jQuery('.flat_rate').hide();
                    jQuery('.easypost_first').show();
                    jQuery('.easypost_priority').hide();
                    jQuery('.easypost_fedex').hide();
                    jQuery('.easypost_ups').hide();
                    jQuery('#shipping_prepaid').val(2);
                }

                if(radioValue_id == "shipping_method_0_wf_easypost_idfedex_ground"){
                    jQuery('.flat_rate').hide();
                    jQuery('.easypost_first').hide();
                    jQuery('.easypost_priority').hide();
                    jQuery('.easypost_fedex').show();
                    jQuery('.easypost_ups').hide();
                    jQuery('#shipping_prepaid').val(3);
                }

                if(radioValue_id == "shipping_method_0_wf_easypost_idground"){
                    jQuery('.flat_rate').hide();
                    jQuery('.easypost_first').hide();
                    jQuery('.easypost_priority').hide();
                    jQuery('.easypost_fedex').hide();
                    jQuery('.easypost_ups').show();
                    jQuery('#shipping_prepaid').val(4);
                }
      
                if(radioValue_id == "shipping_method_0_wf_easypost_idpriority"){
                    jQuery('.flat_rate').hide();
                    jQuery('.easypost_first').hide();
                    jQuery('.easypost_priority').show();
                    jQuery('.easypost_fedex').hide();
                    jQuery('.easypost_ups').hide();
                    jQuery('#shipping_prepaid').val(5);
                }
            });    

        </script>
    <?php    
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
add_action('wp_ajax_get_payment_types', 'get_payment_types');
add_action('wp_ajax_nopriv_get_payment_types', 'get_payment_types');
        

function get_payment_types() {

    if(isset($_POST['keyword']) && !empty($_POST['keyword']) && isset($_POST['payment_type']) && !empty($_POST['payment_type'])){

        if($_POST['payment_type'] == 'check' || $_POST['payment_type'] == 'e-check'){
            $html = "";
        }else if ($_POST['payment_type'] == 'paypal' || $_POST['payment_type'] == 'zelle' || $_POST['payment_type'] == 'venmo'
                                        || $_POST['payment_type'] == 'cashapp' || $_POST['payment_type'] == 'amazon_gift-card'
        ){
  
           $payment_email =  get_post_meta($_POST['keyword'] ,'payment_email',true);
            $html =  "<strong> Email :</strong> <input type='text' name='payment_email' value='".$payment_email."' />";

            }else if ($_POST['payment_type'] == 'bank-transfer'   ){

                 $transfer_account_no =  get_post_meta($_POST['keyword'] ,'billing_bank-transfer_account_no',true);
                $transfer_routing_no =  get_post_meta($_POST['keyword'] ,'billing_bank-transfer_routing_no',true);
                $transfer_name_on_account =  get_post_meta($_POST['keyword'] ,'billing_bank-transfer_name_on_account',true);


                $html =  " <strong>Account Details</strong> 
                </br></br>
                Account No :
                </br>
                <input type='text' name='billing_bank-transfer_account_no' value='".$transfer_account_no."' />
                </br>
                </br>
                Routing No :
                </br>
                <input type='text' name='billing_bank-transfer_routing_no' value='".$transfer_routing_no."' />
                </br></br>
                Name on Account :
                </br>
                <input type='text' name='billing_bank-transfer_name_on_account' value='".$transfer_name_on_account."' />";

            }

echo $html;

    }

die();

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function get_last_order_id(){
    global $wpdb;
    $statuses = array_keys(wc_get_order_statuses());
    $statuses = implode( "','", $statuses );

    // Getting last Order ID (max value)
    $results = $wpdb->get_col( "
        SELECT MAX(ID) FROM {$wpdb->prefix}posts
        WHERE post_type LIKE 'shop_order'
        AND post_status IN ('$statuses')
    " );
    return reset($results);
}

/*if($_SERVER["REMOTE_ADDR"] == '103.161.99.254') {
   
    add_action('wp_ajax_cancel_unpaid_orders', 'cancel_unpaid_orders');
    add_action('wp_ajax_nopriv_cancel_unpaid_orders', 'cancel_unpaid_orders'); 
}*/

add_action('wc_custom_cancel_unpaid_orders', 'cancel_unpaid_orders');
        
function cancel_unpaid_orders() {
    $days_delay = 28; // <=== SET the delay (number of days to wait before cancelation)

    $one_day    = 24 * 60 * 60;
    $today      = strtotime( date('Y-m-d') );

    // Get unpaid orders (28 days old here)
    $unpaid_orders = (array) wc_get_orders( array(
        'limit'        => -1,
        'status'       => array('prepaid-return', 'on-hold'),
        //'status'       => 'on-hold',
        'date_created' => '<' . ( $today - ($days_delay * $one_day) ),
        //'date_created' => '<' . ( $today ),
    ) );
    /*echo "<pre>";
    print_r($unpaid_orders);
    exit("tettete");*/
    if ( sizeof($unpaid_orders) > 0 ) {

        $cancelled_text = __("The order was cancelled due to no payment from customer.", "woocommerce");

        // Loop through orders
        foreach ( $unpaid_orders as $order ) {
            $order->update_status( 'cancelled', $cancelled_text );
             
            $subject =  "Your order is Cancelled #$order->ID";
            $order_no = $order->ID;
            include( WP_PLUGIN_DIR.'/woo-checkout/templates/order-cancel.php');

            //$ord_id = '25928';
            //$order = wc_get_order($ord_id);
            //$order = wc_get_order($order->ID);

            $wf_easypost_label = $order->get_meta('wf_easypost_labels');
            
            if(isset($wf_easypost_label ) && !empty($wf_easypost_label)) {
                
                $sh_id = $wf_easypost_label[0]['shipment_id'];

                $response_arr = easypost_refund_function($sh_id);
                
                if($response_arr['success'] == true) {
                    $response_arr['Msg'];
                }
            }
            
            $easypost_return_labels = $order->get_meta('wf_easypost_prepaid_return_labels');

            if(isset($easypost_return_labels ) && !empty($easypost_return_labels)) {
        
                $sh1_id = $easypost_return_labels[0]['shipment_id'];

                $response_arr = easypost_refund_function($sh1_id);
                
                if($response_arr['success'] == true) {
                    $response_arr['Msg'];
                }
            }
        }
    }
}

add_action( 'woocommerce_order_status_cancelled', 'wecell_update_order' );

function wecell_update_order($order_id) {
    $orders_data = new WC_Order($order_id);

    $wf_easypost_label = $orders_data->get_meta('wf_easypost_labels');

    $easypost_return_labels = $orders_data->get_meta('wf_easypost_prepaid_return_labels');        
    
    if(isset($wf_easypost_label ) && !empty($wf_easypost_label)) {
        
        $sh_id = $wf_easypost_label[0]['shipment_id'];

        $response_arr = easypost_refund_function($sh_id);
        
        if($response_arr['success'] == true) {
            $response_arr['Msg'];
        }
    }

    if(isset($easypost_return_labels ) && !empty($easypost_return_labels)) {
        
        $sh1_id = $easypost_return_labels[0]['shipment_id'];

        $response_arr = easypost_refund_function($sh1_id);
        
        if($response_arr['success'] == true) {
            $response_arr['Msg'];
        }
    }
}

function easypost_refund_function($sh_id) {
    $easypost_id_settings =  get_option('woocommerce_wf_easypost_id_settings' , true);
    $e_api_key =  $easypost_id_settings['api_key'];
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.easypost.com/v2/shipments/'.$sh_id.'/refund/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Basic '. base64_encode("$e_api_key"),
            //'Authorization: Basic RVpBS2MzN2MyMmMxZDAwMDRlY2M4MTAxZDFlMTk1Y2YxNjE4aGdaUE5QZlY2OGFkN3I5UHphRUloUTo='
        ),
    ));

    $response = curl_exec($curl);

    $err = curl_error($curl);

    curl_close($curl);

    $response_obj = json_decode($response);
    
    if($err) {
        $result['success'] = false;
        $result['Msg'] = 'cURL Error';
        $result['errorDetails'] = $err;
    } else {
        if (isset($response_obj)) {
            $result['success'] = true;
            $result['Msg'] = $response_obj->refund_status;
        } else {
            $result['success'] = true;
            $result['Msg'] = 'Unknown error while refund shipment - Please cross check settings.';
        }
    }
    return $result;
}

add_action( 'woocommerce_after_order_notes', 'add_custom_checkout_hidden_field' );
function add_custom_checkout_hidden_field( $checkout ) {
    if(is_checkout()) { ?>
        <script type="text/javascript">
            jQuery(document.body).on('updated_checkout', function(){
                var radioValue = jQuery("input[name^='shipping_method']:checked").val();
                var radioValue_id = jQuery("input[name^='shipping_method']:checked").attr('id');

                if(radioValue_id == "shipping_method_0_flat_rate12"){
                    
                    var value = jQuery('ul li:nth-child(2)').find("input[name^='shipping_method']").val();
                    jQuery("#shipping_eid").val(value)
                } else {
                    var value = "";
                    jQuery("#shipping_eid").val(value)
                }
            });
        </script>    
    <?php 
    // Output the hidden field
    echo '<div id="user_link_hidden_checkout_field">
            <input type="hidden" class="input-hidden" name="shipping_eid" id="shipping_eid" value="">
        </div>';
    }
}

add_action( 'woocommerce_checkout_create_order_shipping_item', 'action_wc_checkout_create_order_shipping_item', 10, 4 );

function action_wc_checkout_create_order_shipping_item( $item, $package_key, $package, $order ) {

    // Targeting "flat_rate:12" by its instance ID
    if( $item->get_method_id() == 'flat_rate' ) {
        //wf_easypost_id:Priority
        $shipping_str = $_POST['shipping_eid'];
        $shipping_arr = explode(":",$shipping_str);
        if($shipping_arr[1] == 'Priority') {
            $shipping_title = 'Priority Mail (USPS)';
        } else {
            $shipping_title = 'First-Class Mail (USPS)';
        }
        $item->set_method_id( $shipping_arr[0] );
        $item->set_method_title( $shipping_title );
        $item->set_instance_id( 0 );
        $item->set_total( 0 );
    }
}

add_action( 'woocommerce_checkout_update_order_meta', 'wascc_woocommerce_checkout_shipping',30, 1 );

function wascc_woocommerce_checkout_shipping ( $order_id ) {
    
    $order = new WC_Order( $order_id );
    //flat_rate:12
    $shipping_items = $order->get_items('shipping');
    if( isset($_POST['shipping_method'][0]) && !empty($_POST['shipping_method'][0]) ) { 
        $p_shipping_val = $_POST['shipping_method'][0];
        @$p_shipping_arr = explode(":",$p_shipping_val);
        if($p_shipping_arr[0] == 'flat_rate') {
            update_post_meta( $order_id, '_shipping_box', 'pre-paid-box' );
        }
    }
}

add_action( 'woocommerce_order_status_changed', 'cell_ship_function', 100, 4 );

function cell_ship_function( $order_id, $old_status, $new_status, $order ){
    //echo $order_id;

    $myorder    = wc_get_order($order_id);
    
    //$data = $myorder->get_items( 'shipping' );
    
    $shipping_box_value = get_post_meta( $order_id, '_shipping_box', true );
    foreach( $myorder->get_items( 'shipping' ) as $item_id => $item ){
        //echo $shp_box = $item->get_method_title();
        if(isset($shipping_box_value) && !empty($shipping_box_value)) {
            $item->set_method_title( 'Pre-Paid Box' );
            //$item->save();
        }
    }
}

add_action( 'woocommerce_thankyou', 'my_function', 10, 1 );

function my_function($order_id) {
    
    $myorder    = wc_get_order($order_id);
    $shipping_box_value = get_post_meta( $order_id, '_shipping_box', true );
    
    foreach( $myorder->get_items( 'shipping' ) as $item_id => $item ){
        //echo $shp_box = $item->get_method_title();
        if(isset($shipping_box_value) && !empty($shipping_box_value)) {
            $item->set_method_title( 'Pre-Paid Box' );
            //$item->save();
        }
        
    }   
}
//if($_SERVER["REMOTE_ADDR"]=='103.81.94.216'){
    
//}

add_action( 'woocommerce_payment_complete', 'action_payment_complete', 10, 2 );
function action_payment_complete( $order_id, $order ) {
    $order = new WC_Order( $order_id );

    if($order->get_total() == 0.00 && $order->get_status() == 'processing') {
        //echo "inside";
        $order->update_status( 'on-hold' );
    }
    //exit("dsfs33");
}

add_action( 'woocommerce_checkout_order_processed', 'cell_hold');

function cell_hold($order_id) {
    // if pre paid box shipping method select then 2nd shipment create    
    $shipping_box_value = get_post_meta( $order_id, '_shipping_box', true );
    
    if(isset($shipping_box_value) && !empty($shipping_box_value)) {
        cell_regenerate_labels_easypost($order_id);
    }
}

include_once ( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/class-wf-shipping-easypost.php');
include_once ( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/class-wf-legacy.php');

function cell_regenerate_labels_easypost($order_id) {
    $value = array();

    $value = cell_easypost_return_orders_bulk_action($order_id);
}

function cell_easypost_return_orders_bulk_action($post_id) {
    if($post_id) { 
        $value = array();
        $e_settings = get_option('woocommerce_wf_easypost_id_settings', null);
        $easypost_label_details_array    = get_post_meta( $post_id, 'wf_easypost_labels', true );
        if ( empty($easypost_label_details_array) || ($e_settings['enable_return_label'] == 'yes' && $e_settings['enable_auto_return_label'] == 'yes')) {
            $bulk_label = true;
            $error_email = true;
            cell_easypost_generate_packages($post_id);
            cell_easypost_shipment_confirm($post_id,$label_type);
            return;       
        }     
    }
    //exit("Ddfdfd");
}

function cell_easypost_generate_packages($post_id='') {
    /*if($bulk_label != 1)
    {
        $post_id = base64_decode($_GET['wf_easypost_generate_packages']);
    }*/
    $order = cell_load_order($post_id);
    if (!$order)
        return;
    $package_data_array = cell_get_package_data($order);        
    
    update_post_meta( $post_id, '_wf_easypost_prepaid_return_stored_packages', $package_data_array );
    /*if($bulk_label != 1)
    {
        wp_redirect( admin_url( '/post.php?post='.$post_id.'&action=edit') );
        exit;
    }*/
}

function cell_load_order($orderId) {
    if (!class_exists('WC_Order')) {
        return false;
    }
    return ( WC()->version < '2.7.0' ) ? new WC_Order( $orderId ) : new wf_order( $orderId );
}

function cell_get_package_data($order,$return_addon = '') {
    $easypost_settings = get_option('woocommerce_wf_easypost_id_settings', null);
    $easypost_packing_method = isset($easypost_settings['packing_method']) ? $easypost_settings['packing_method'] : 'per_item';
    if($return_addon!=''){
        $easypost_packing_method = 'per_item';
    }
    $package = cell_create_package($order);
    //$wf_easypost = new WF_Easypost();
    
    //if multi-vendor
    if($wf_easypost->vendor_check) { //echo "if";
        $package = apply_filters( 'elex_easypost_filter_label_packages', array($package));
        $package_data_array = array();
        foreach ($package as $key=>$val) {
            $package_data_array_temp = cell_wf_get_api_rate_box_data($val, $easypost_packing_method);
            foreach($package_data_array_temp as $package_count => $package_data){
                $package_data['origin'] = $val['origin']; 
                $package_data_array[] = $package_data;
            }
        }
    } else  {
        //echo "else";
       $package_data_array = cell_wf_get_api_rate_box_data($package, $easypost_packing_method);
    }
    return $package_data_array;
}

/**
* wf_get_api_rate_box_data function.
*
* @access public
*/
function cell_wf_get_api_rate_box_data($package, $packing_method) {
    $packing_method;
    $increment= 0;
    $wf_easypost = new WF_Easypost();
    $requests = $wf_easypost->get_package_requests($package);
    
    $package_data_array = array();
    if ($requests) {
        foreach ($requests as $key => $request) {
            $package_data = array();
            $request_data = $request['request']['Rate'];
            if($packing_method == 'weight_based_packing')
            {
                $package_data['PackedItem'] = !empty($request['request']['packed'][$increment]['items']) ? $request['request']['packed'][$increment]['items'] : '';
                $increment++;
            }
            else
            {
                $package_data['PackedItem'] = !empty($request['request']['packed']) ? $request['request']['packed'] : '';
            }
            // PS: Some of PHP versions doesn't allow to combining below two line of code as one. 
            // id_array must have value at this point. Force setting it to 1 if it is not.
            $package_data['BoxCount'] = isset($request['quantity']) ? $request['quantity'] : 1;
            $package_data['WeightOz'] = isset($request_data['WeightOz']) ? $request_data['WeightOz']/2 : '';
            //$package_data['FromZIPCode'] = isset($request_data['FromZIPCode']) ? $request_data['FromZIPCode'] : '';
            //$package_data['ToZIPCode'] = isset($request_data['ToZIPCode']) ? $request_data['ToZIPCode'] : '';
            // For return Pre-Paid Box
            $package_data['FromZIPCode'] = isset($request_data['ToZIPCode']) ? $request_data['ToZIPCode'] : '';
            $package_data['ToZIPCode'] = isset($request_data['FromZIPCode']) ? $request_data['FromZIPCode'] : '';
            $package_data['ToCountry'] = isset($request_data['ToCountry']) ? $request_data['ToCountry'] : '';
            $package_data['RectangularShaped'] = isset($request_data['RectangularShaped']) ? $request_data['RectangularShaped'] : '';
            $package_data['InsuredValue'] = isset($request_data['InsuredValue']) ? $request_data['InsuredValue'] : '';
            $package_data['ShipDate'] = isset($request_data['ShipDate']) ? $request_data['ShipDate'] : '';
            $package_data['Width'] = isset($request_data['Width']) ? $request_data['Width'] : '';
            $package_data['Length'] = isset($request_data['Length']) ? $request_data['Length'] : '';
            $package_data['Height'] = isset($request_data['Height']) ? $request_data['Height'] : '';
            $package_data['Value'] = isset($request_data['Value']) ? $request_data['Value'] : '';
            $package_data['Girth'] = isset($request_data['Girth']) ? $request_data['Girth'] : '';
            

            $package_data_array[] = $package_data;
        }
    }
    return $package_data_array;
}

function cell_create_package($order) {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    $query_data = isset($parts['query']) ? $parts['query'] : '';
    parse_str($query_data, $query);
    $count = 0;
    $orderItems = $order->get_items();
    $orderId = $order->get_id();
    $orderItems = apply_filters('elex_easypost_order_package', $orderItems, $orderId);
    $shipping_method_title = '';
    foreach( $order->get_items( 'shipping' ) as $shipping_item_obj ){
        
        $shipping_method_title       = $shipping_item_obj->get_method_title();
    }
    if(isset($_GET['product_id'])){
        $return_product_id = array(stripslashes($_GET['product_id']));
        $return_product_id = str_replace(array(']', '[', '"'), '', $return_product_id);
        $return_product_id = explode(",", $return_product_id[0]);
        $return_product_quantity = array(stripslashes($_GET['quantity']));
        $return_product_quantity = str_replace(array(']', '[', '"'), '', $return_product_quantity);
        $return_product_quantity = explode(",", $return_product_quantity[0]);
    }
    foreach ($orderItems as $orderItem) {
        $item_id = $orderItem['variation_id'] ? $orderItem['variation_id'] : $orderItem['product_id'];
        if(isset($query['button_name']) && isset($query['button_name']) && $query['button_name'] == 'return'){
            if(in_array($item_id, $return_product_id))
            {
                $product_data = wc_get_product($item_id);
                $name = $orderItem->get_name();
                $items[$item_id] = array('data' => $product_data, 'quantity' => $return_product_quantity[$count]);
                $count++;
            }
        }
        else
        {
            $product_data = wc_get_product($item_id);
            $name = $orderItem->get_name();
            $items[$item_id] = array('data' => $product_data, 'quantity' => $orderItem['qty'], 'product_id' => $item_id);
        }
    }
    $package['contents'] = $items;
    $package['id']       = $item_id;
    $package['destination'] = array(
        'country' => $order->shipping_country,
        'state' => $order->shipping_state,
        'zip' => $order->shipping_postcode,
        'city' => $order->shipping_city,
        'street1' => $order->shipping_address_1,
        'street2' => $order->shipping_address_2);
    $package['orderId'] = $orderId;
    $package['title'] = $shipping_method_title;
    return $package;
}

function cell_easypost_shipment_confirm($post_id='',$label_type='') {
    $parts = parse_url($_SERVER['REQUEST_URI']);
    $query_data = isset($parts['query']) ? $parts['query'] : '';
    parse_str($query_data, $query);
    
    $bulk_label = true;

    $wfeasypostmsg = '';
    // Load Easypost.com Settings.
    $easypost_settings = get_option('woocommerce_wf_easypost_id_settings', null);

    $api_mode = isset($easypost_settings['api_mode']) ? $easypost_settings['api_mode'] : 'Live';
    if($bulk_label!=1)
    {
        $query_string = explode('|', base64_decode($_GET['wf_easypost_shipment_confirm']));
        $post_id = $query_string[1];
    }
    $wf_easypost_selected_service = isset($_GET['wf_easypost_service']) ? $_GET['wf_easypost_service'] : '';
    if(isset($query['button_name']) && $query['button_name'] == 'return'){
        $order = cell_load_order($post_id);
        if($order->shipping_country==$this->settings['country']) 
        {
            $wf_easypost_return_selected_service = json_encode([$easypost_settings['easypost_default_domestic_return_shipment_service']]);
        }
        else{
            $wf_easypost_return_selected_service = json_encode([$easypost_settings['easypost_default_international_shipment_service']]);
        }
       
        update_post_meta($post_id,'wf_easypost_prepaid_return_selected_service', $wf_easypost_return_selected_service);

    }
    else{ 
        update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $wf_easypost_selected_service);
    }
    
    $selected_flatrate_box = isset($_GET['wf_easypost_flatrate_box']) ? $_GET['wf_easypost_flatrate_box'] : '';
    update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_flat_rate_service', $selected_flatrate_box);
    $order = cell_load_order($post_id);
    if (!$order)
        return;
    $package_data_per_item = array();
    $package_data_array = cell_get_package_data($order);
    $index = 0;
    if($easypost_settings['packing_method'] == 'per_item'){
        foreach ($package_data_array as $key => $value) {
            if($package_data_array[$key]['BoxCount'] > 1){
                for($i = 0;$i < $package_data_array[$key]['BoxCount'];$i++){
                    $package_data_per_item [$index] = $package_data_array[$key];
                    $index++;
                }
            } else {
                $package_data_per_item[$index] = $package_data_array[$key];
                $index++;
            }
        }
        $package_data_array = $package_data_per_item;
    } 
    if(isset($query['button_name'])){
        if($query['button_name'] == 'return'){
            foreach ($package_data_array as $key => $value) {
                if($package_data_array[$key]['BoxCount'] > 1){
                    for($i = 0;$i < $package_data_array[$key]['BoxCount'];$i++){
                        $package_data_per_item [$index] = $package_data_array[$key];
                        $index++;
                    }
                } else {
                    $package_data_per_item[$index] = $package_data_array[$key];
                    $index++;
                }
            }
            $package_data_array = $package_data_per_item;
        }
    }
    $package_data_array = cell_manual_packages($package_data_array); // Filter data with manual packages
    if (empty($package_data_array)) {
        return false;
    }
    // echo "<pre>";
    // print_r($package_data_array);
    // exit("DFsdssd222");
    $easypost_printLabelType = isset($easypost_settings['printLabelType']) ? $easypost_settings['printLabelType'] : 'PNG';
    $easypost_packing_method = isset($easypost_settings['packing_method']) ? $easypost_settings['packing_method'] : 'per_item';

    $message = '';
    $shipment_details = array();

    $shipping_service_data = cell_get_shipping_service_data($order);
    $default_service_type  = $shipping_service_data['shipping_service'];
    $carrier_services_bulk = include( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/data-wf-services.php');
    $bulk_service = array();
    $service_selected = false;
    $carrier_name = '';
    
    foreach($carrier_services_bulk as $service => $code) {
        //For bulk shipment When Customer choose Flate rate service or Free Shipping.
        if($bulk_label == 1) {
            // Bulk action Flat rate label generation 
            $shipping_service_data['shipping_service_name'] = cell_label_generation_flat_service($shipping_service_data['shipping_service_name']);
            if(in_array($shipping_service_data['shipping_service_name'], $code['services']))
            {
                $service_selected = true;
                $bulk_service = $code['services'];
                foreach ($bulk_service as $key => $value) 
                {
                    if($value == $shipping_service_data['shipping_service_name'])
                    {
                        $default_service_type  = $key ;
                        update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $key);
                        $carrier_name = $service;   
                    }
                }      
            }
            elseif($service_selected == false)
            { 
                // For bulk shipment When Customer choose Flate rate service or Free Shipping.
                if($bulk_label == 1)
                { 
                    if($shipping_service_data['shipping_service_name'] === 'Local pickup'){
                        $default_service_type = 'local_pickup';
                    }
                    elseif($order->shipping_country == $easypost_settings['country'])
                    {
                        $default_service_type = $easypost_settings['easypost_default_domestic_shipment_service'];
                        $bulk_service = $code['services'];
                        foreach ($bulk_service as $key => $value) {
                            if($key == $default_service_type)
                            {
                                $default_service_type  = $key ;
                                update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $key);
                                $carrier_name = $service;
                            }
                        }
                    }
                    else
                    {
                        if($easypost_settings['easypost_default_international_shipment_service'] != 'NA')
                        {
                            $default_service_type = $easypost_settings['easypost_default_international_shipment_service'];
                            $bulk_service = $code['services'];
                            foreach ($bulk_service as $key => $value) {
                                if($key == $default_service_type)
                                {
                                    $default_service_type  = $key ;
                                    update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $key);
                                    $carrier_name = $service;
                                }
                            }
                        }
                    }
                }
        
            }   
        }
    }

    if($bulk_label != 1) {
        $carrier_name = array();
       
        foreach($carrier_services_bulk as $service => $code) {
               $service_codes = get_post_meta($order->id, 'wf_easypost_prepaid_return_selected_service', true);
               $decoded_service_array = json_decode($service_codes);
               foreach ($decoded_service_array as $key => $value) {
                   if(array_key_exists( $value, $code['services']))
                   {
                      $carrier_name[] = $service;
                   }
               }
            if(isset($query['button_name'])){
                if($query['button_name'] == 'return')
                {
                    if($easypost_settings['return_address_addon'] == 'manual'){
                        $country = $easypost_settings['return_country_addon'];
                    }else{
                        $country = $easypost_settings['country'];
                    }
                    if($order->shipping_country == $country) {
                        $default_service_type =json_encode([$easypost_settings['easypost_default_domestic_return_shipment_service']]);
                    }
                    else {
                        $default_service_type = json_encode([$easypost_settings['easypost_default_international_return_shipment_service']]);
                    }
                    $bulk_service = $code['services'];
                    foreach ($bulk_service as $key => $value) {
                        if($key == $default_service_type) {
                            $default_service_type  = $key ;
                            update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $key);
                            $carrier_name = $service;
                        }
                    }
                }
            }
        }
    }

    $shipment_details['options']['print_custom_1']  = $order->id;
    $shipment_details['options']['label_format']    =   $easypost_printLabelType;

    // Signature option
    $signature_option = cell_get_package_signature( $order );
    $product_signature_check  = $easypost_settings['signature_option'];
    $product_signature_option = cell_get_package_signature( $order );

    if($product_signature_check == 'yes'){
        $shipment_details['options']['delivery_confirmation']   = 'ADULT_SIGNATURE';
    }
    elseif(!empty($product_signature_option)){
        $shipment_details['options']['delivery_confirmation']   =  $product_signature_option;
    }

    $specialrate = cell_get_special_rates_eligibility( $default_service_type );
    if( !empty($specialrate) ){
       $shipment_details['options']['special_rates_eligibility'] = $specialrate;
    }
    $european_union_countries = array("AT", "BE", "BG", "CY", "CZ", "DE", "DK", "EE", "ES", "FI", "FR", "GR", "HU", "HR", "IE", "IT", "LT", "LU", "LV", "MT", "NL", "PL", "PT", "RO", "SE", "SI", "SK");
    $destination_country = isset($order->shipping_country) ? $order->shipping_country : '';
    $ioss_number  = $easypost_settings['ioss_number'];
    if(in_array($destination_country,$european_union_countries) && !empty($ioss_number)){
        $shipment_details['options']['import_federal_tax_id']   =  $ioss_number;
    }

    $shipping_first_name = $order->shipping_first_name;
    $shipping_last_name = $order->shipping_last_name;
    $shipping_full_name = $shipping_first_name . ' ' . $shipping_last_name;
    if(isset($query['button_name'])){
        if($query['button_name'] == 'return' && $easypost_settings['return_address_addon'] == 'manual') //Addon address for return label.
        {
            $shipment_details['to_address']['name']    = isset($easypost_settings['return_name_addon']) ? $easypost_settings['return_name_addon'] : '';
            $shipment_details['to_address']['company'] = isset($easypost_settings['return_company_addon']) ? $easypost_settings['return_company_addon'] : '';
            $shipment_details['to_address']['street1'] = isset($easypost_settings['return_street1_addon']) ? $easypost_settings['return_street1_addon'] : '';
            $shipment_details['to_address']['street2'] = isset($easypost_settings['return_street2_addon']) ? $easypost_settings['return_street2_addon'] : '';
            $shipment_details['to_address']['city']    = isset($easypost_settings['return_city_addon']) ? $easypost_settings['return_city_addon'] : '';
            $shipment_details['to_address']['state']   = isset($easypost_settings['return_state_addon']) ? $easypost_settings['return_state_addon'] : '';
            $shipment_details['to_address']['zip']     = isset($easypost_settings['return_zip_addon']) ? $easypost_settings['return_zip_addon'] : '';
            $shipment_details['to_address']['email']   = isset($easypost_settings['return_email_addon']) ? $easypost_settings['return_email_addon'] : '';
            $shipment_details['to_address']['phone']   = isset($easypost_settings['return_phone_addon']) ? $easypost_settings['return_phone_addon'] : '';
            $shipment_details['to_address']['country']   = isset($easypost_settings['return_country_addon']) ? $easypost_settings['return_country_addon'] : '';   
        }
        else{
            $shipment_details['to_address']['name']    = isset($easypost_settings['name']) ? $easypost_settings['name'] : '';
            $shipment_details['to_address']['company'] = isset($easypost_settings['company']) ? $easypost_settings['company'] : '';
            $shipment_details['to_address']['street1'] = isset($easypost_settings['street1']) ? $easypost_settings['street1'] : '';
            $shipment_details['to_address']['street2'] = isset($easypost_settings['street2']) ? $easypost_settings['street2'] : '';
            $shipment_details['to_address']['city']    = isset($easypost_settings['city']) ? $easypost_settings['city'] : '';
            $shipment_details['to_address']['state']   = isset($easypost_settings['state']) ? $easypost_settings['state'] : '';
            $shipment_details['to_address']['zip']     = isset($easypost_settings['zip']) ? $easypost_settings['zip'] : '';
            $shipment_details['to_address']['email']   = isset($easypost_settings['email']) ? $easypost_settings['email'] : '';
            $shipment_details['to_address']['phone']   = isset($easypost_settings['phone']) ? $easypost_settings['phone'] : '';
            $shipment_details['to_address']['country']   = isset($easypost_settings['country']) ? $easypost_settings['country'] : '';
        }
    }


    //For bulk  When Customer choose Flate rate service or Free Shipping.
    /*if($bulk_label == 1 && ELEX_EASYPOST_RETURN_ADDON_STATUS && $easypost_settings['enable_auto_return_label'] == 'yes' && $easypost_settings['return_address_addon'] == 'manual'&& isset($easypost_settings['return_street1_addon'] ) && $easypost_settings['return_street1_addon'] != '' )
    { 
        $shipment_details['to_address']['name']    = isset($easypost_settings['return_name_addon']) ? $easypost_settings['return_name_addon'] : '';
        $shipment_details['to_address']['company'] = isset($easypost_settings['return_company_addon']) ? $easypost_settings['return_company_addon'] : '';
        $shipment_details['to_address']['street1'] = isset($easypost_settings['return_street1_addon']) ?$easypost_settings['return_street1_addon'] : '';
        $shipment_details['to_address']['street2'] = isset($easypost_settings['return_street2_addon']) ? $easypost_settings['return_street2_addon'] : '';
        $shipment_details['to_address']['city']    = isset($easypost_settings['return_city_addon']) ? $easypost_settings['return_city_addon'] : '';
        $shipment_details['to_address']['state']   = isset($easypost_settings['return_state_addon']) ? $easypost_settings['return_state_addon'] : '';
        $shipment_details['to_address']['zip']     = isset($easypost_settings['return_zip_addon']) ? $easypost_settings['return_zip_addon'] : '';
        $shipment_details['to_address']['email']   = isset($easypost_settings['return_email_addon']) ? $easypost_settings['return_email_addon'] : '';
        $shipment_details['to_address']['phone']   = isset($easypost_settings['return_phone_addon']) ? $easypost_settings['return_phone_addon'] : '';
        $shipment_details['to_address']['country']   = isset($easypost_settings['return_country_addon']) ? $easypost_settings['return_country_addon'] : '';   
    } else if($bulk_label == 1 ){
        $shipment_details['to_address']['name']    = isset($easypost_settings['name']) ? $easypost_settings['name'] : '';
        $shipment_details['to_address']['company'] = isset($easypost_settings['company']) ? $easypost_settings['company'] : '';
        $shipment_details['to_address']['street1'] = isset($easypost_settings['street1']) ? $easypost_settings['street1'] : '';
        $shipment_details['to_address']['street2'] = isset($easypost_settings['street2']) ? $easypost_settings['street2'] : '';
        $shipment_details['to_address']['city']    = isset($easypost_settings['city']) ? $easypost_settings['city'] : '';
        $shipment_details['to_address']['state']   = isset($easypost_settings['state']) ? $easypost_settings['state'] : '';
        $shipment_details['to_address']['zip']     = isset($easypost_settings['zip']) ? $easypost_settings['zip'] : '';
        $shipment_details['to_address']['email']   = isset($easypost_settings['email']) ? $easypost_settings['email'] : '';
        $shipment_details['to_address']['phone']   = isset($easypost_settings['phone']) ? $easypost_settings['phone'] : '';
        $shipment_details['to_address']['country']   = isset($easypost_settings['country']) ? $easypost_settings['country'] : '';
    }*/
    $shipment_details['to_address']['name'] = isset($shipping_full_name) ? $shipping_full_name : '';
    $shipment_details['to_address']['street1'] = isset($order->shipping_address_1) ? $order->shipping_address_1 : '';
    $shipment_details['to_address']['street2'] = isset($order->shipping_address_2) ? $order->shipping_address_2 : '';
    $shipment_details['to_address']['city'] = isset($order->shipping_city) ? $order->shipping_city : '';
    $shipment_details['to_address']['company'] = isset($order->shipping_company) ? $order->shipping_company : '';
    $shipment_details['to_address']['state'] = isset($order->shipping_state) ? $order->shipping_state : '';
    $shipment_details['to_address']['zip'] = isset($order->shipping_postcode) ? $order->shipping_postcode : '';
    $shipment_details['to_address']['email'] = isset($order->billing_email) ? $order->billing_email : '';
    $shipment_details['to_address']['phone'] = isset($order->billing_phone) ? $order->billing_phone : '';
    $shipment_details['to_address']['country'] = isset($order->shipping_country) ? $order->shipping_country : '';
    
    if(isset($easypost_settings['return_address']) && $easypost_settings['return_address']=='yes' && $label_type !='return' && isset($query['button_name']) && $query['button_name'] != 'return'){            
        $shipment_details['return_address']['name']    = isset($easypost_settings['return_name']) ? $easypost_settings['return_name'] : '';
        $shipment_details['return_address']['company'] = isset($easypost_settings['return_company']) ? $easypost_settings['return_company'] : '';
        $shipment_details['return_address']['street1'] = isset($easypost_settings['return_street1']) ? $easypost_settings['return_street1'] : '';
        $shipment_details['return_address']['street2'] = isset($easypost_settings['return_street2']) ? $easypost_settings['return_street2'] : '';
        $shipment_details['return_address']['city']    = isset($easypost_settings['return_city']) ? $easypost_settings['return_city'] : '';
        $shipment_details['return_address']['state']   = isset($easypost_settings['return_state']) ? $easypost_settings['return_state'] : '';
        $shipment_details['return_address']['zip']     = isset($easypost_settings['return_zip']) ? $easypost_settings['return_zip'] : '';
        $shipment_details['return_address']['email']   = isset($easypost_settings['return_email']) ? $easypost_settings['return_email'] : '';
        $shipment_details['return_address']['phone']   = isset($easypost_settings['return_phone']) ? $easypost_settings['return_phone'] : '';
        $shipment_details['return_address']['country']   = isset($easypost_settings['return_country']) ? $easypost_settings['return_country'] : '';
    }


    //need to find some solution for intnat
    $international = FALSE;
    $eligible_for_customs_details = cell_is_eligible_for_customs_details($shipment_details['to_address']['country'],$shipment_details['from_address']['country'],$shipment_details['from_address']['city']);
    if($eligible_for_customs_details){
        $international = true;
        $custom_line_array = array();   
        $custom_line_array =  cell_elex_check_international($eligible_for_customs_details,$order,$shipment_details);
       //dry_ice
        $dry_ices = cell_get_package_dry_ice($order);
        if($dry_ices == 'yes'){
            $shipment_details['options']['dry_ice']   =  "true";
            $shipment_details['options']['dry_ice_weight']   =   $custom_line['weight'];
        }
        //for International shipping only
        $shipment_details['customs_info']['customs_certify'] = true;
        $shipment_details['customs_info']['customs_signer'] = isset($easypost_settings['customs_signer']) ? $easypost_settings['customs_signer'] : '';
        $shipment_details['customs_info']['contents_type'] = 'merchandise';
        $shipment_details['customs_info']['contents_explanation'] = '';
        $shipment_details['customs_info']['restriction_type'] = 'none';
        $shipment_details['customs_info']['eel_pfc'] = 'NOEEI 30.37(a)';
    }
    if(!class_exists('EasyPost\EasyPost')){
        require_once(WP_PLUGIN_DIR . "/easypost-woocommerce-shipping/easypost.php");
    }
    if( $easypost_settings['api_mode'] == 'Live'){
        \EasyPost\EasyPost::setApiKey($easypost_settings['api_key']);
    }else{
        \EasyPost\EasyPost::setApiKey($easypost_settings['api_test_key']);
        //\EasyPost\EasyPost::setApiKey('EZTKc37c22c1d0004ecc8101d1e195cf1618RXFwEFM99BTdqw1Gn6WbdQ');
    }   
    $easypost_labels = array();
    $index=0;
    $package_count=0;
    $selected_flatrate_box = get_post_meta($post_id, 'wf_easypost_selected_flat_rate_service',true);
    $default_service_type = str_replace('[','',$default_service_type);
    $default_service_type = str_replace(']','',$default_service_type);
    $default_service_type = str_replace('"','',$default_service_type);
    $default_service_type = explode(',', $default_service_type);
    $selected_flatrate_box = str_replace('[','',$selected_flatrate_box);
    $selected_flatrate_box = str_replace(']','',$selected_flatrate_box);
    $selected_flatrate_box = str_replace('"','',$selected_flatrate_box);
    $selected_flatrate_box = explode(',', $selected_flatrate_box);
    $service_count=0;
    $check_ups_service = array(
        "Ground" => "Ground (UPS)","3DaySelect" => "3 Day Select (UPS)","2ndDayAirAM" => "2nd Day Air AM (UPS)","2ndDayAir" => "2nd Day Air (UPS)","NextDayAirSaver" => "Next Day Air Saver (UPS)", "NextDayAirEarlyAM" => "Next Day Air Early AM (UPS)","NextDayAir" => "Next Day Air (UPS)","Express" => "Express (UPS)","Expedited" => "Expedited (UPS)","ExpressPlus" => "Express Plus (UPS)","UPSSaver" => "UPS Saver (UPS)","UPSStandard" => "UPS Standard (UPS)"
    );

    $shipment_details['from_address']['name']    = isset($easypost_settings['name']) ? $easypost_settings['name'] : '';
    $shipment_details['from_address']['company'] = isset($easypost_settings['company']) ? $easypost_settings['company'] : '';
    $shipment_details['from_address']['street1'] = isset($easypost_settings['street1']) ? $easypost_settings['street1'] : '';
    $shipment_details['from_address']['street2'] = isset($easypost_settings['street2']) ? $easypost_settings['street2'] : '';
    $shipment_details['from_address']['city']    = isset($easypost_settings['city']) ? $easypost_settings['city'] : '';
    $shipment_details['from_address']['state']   = isset($easypost_settings['state']) ? $easypost_settings['state'] : '';
    $shipment_details['from_address']['zip']     = isset($easypost_settings['zip']) ? $easypost_settings['zip'] : '';
    $shipment_details['from_address']['email']   = isset($easypost_settings['email']) ? $easypost_settings['email'] : '';
    $shipment_details['from_address']['phone']   = isset($easypost_settings['phone']) ? $easypost_settings['phone'] : '';
    $shipment_details['from_address']['country']   = isset($easypost_settings['country']) ? $easypost_settings['country'] : '';
    $shipment_details['from_address']['residential'] = isset($easypost_settings['show_rates']) && $easypost_settings['show_rates'] == 'residential' ? true : '';

    foreach ($package_data_array as $package_data) {
        
        //For checking ups service to send thirdparty account details.
        
        $ups = false;
        $inc = 0;
        if(is_array($carrier_name) || !empty($carrier_name)){
           if($easypost_settings['elex_shipping_label_size'] != 'label_type'){
               if($carrier_name[$service_count] == 'USPS'){
                    $shipment_details['options']['label_size'] = $easypost_settings['elex_shipping_label_size_usps'];
                }elseif ($carrier_name[$service_count] == 'UPS') {
                    $shipment_details['options']['label_size'] = $easypost_settings['elex_shipping_label_size_ups'];
                }else if($carrier_name[$service_count] == 'FedEx'){
                    $shipment_details['options']['label_size'] = $easypost_settings['elex_shipping_label_size_fedex'];
                }else{
                    $shipment_details['options']['label_size'] = $easypost_settings['elex_shipping_label_size_canadapost'];
                }
            }
        }

        $ups_service = json_decode(stripslashes(html_entity_decode($wf_easypost_selected_service)));
        if(isset($ups_service) && is_array($ups_service) && !empty($ups_service)){
            if(array_key_exists($ups_service[$inc], $check_ups_service))
            {  
                if($carrier_name[0] =='UPS'){
                    $ups = true;
                }else{
                    $ups = false;
                }
            }
        }
        $inc++;
        
        //Multi-Vendor support
        if (isset($package_data['origin']) && get_option('wc_settings_wf_vendor_addon_allow_vedor_api_key') == 'yes') {
            $easypost_api_key = get_user_meta($package_data['origin']['vendor_id'], 'vendor_easypost_api_key', true);
        } else {
              if( $easypost_settings['api_mode'] == 'Live'){
                $easypost_api_key = $easypost_settings['api_key'];
            }else{
                $easypost_api_key = $easypost_settings['api_test_key'];
            }
            
        }
        //Third Party Billing Request options.
        if($easypost_settings['third_party_billing'] == 'yes' && $ups){
            $shipment_details['options']['bill_third_party_account'] =  json_decode(stripslashes(html_entity_decode($_GET['wf_elex_easypost_third_party_billing_api_str'])));
            $shipment_details['options']['bill_third_party_country'] = json_decode(stripslashes(html_entity_decode($_GET['wf_elex_easypost_third_party_billing_country_str'])));
            $shipment_details['options']['bill_third_party_postal_code'] = json_decode(stripslashes(html_entity_decode($_GET['wf_elex_easypost_third_party_billing_zipcode_str'])));
        }
   
        \EasyPost\EasyPost::setApiKey($easypost_api_key);
        //\EasyPost\EasyPost::setApiKey('EZTKc37c22c1d0004ecc8101d1e195cf1618RXFwEFM99BTdqw1Gn6WbdQ');
        
        if(isset($package_data['origin'])) {
            $shipment_details['to_address']['name']    = $package_data['origin']['first_name'];
            $shipment_details['to_address']['company'] = $package_data['origin']['company'];
            $shipment_details['to_address']['street1'] = $package_data['origin']['address_1'];
            $shipment_details['to_address']['street2'] = $package_data['origin']['address_2'];
            $shipment_details['to_address']['city']    = $package_data['origin']['city'];
            $shipment_details['to_address']['state']   = $package_data['origin']['state'];
            $shipment_details['to_address']['zip']     = $package_data['origin']['postcode'];
            $shipment_details['to_address']['email']   = $package_data['origin']['email'];
            $shipment_details['to_address']['phone']   = $package_data['origin']['phone'];
            $shipment_details['to_address']['country']   = $package_data['origin']['country'];
        }

        //Warehouse address as from address
        if(isset($package_data['warehouse_data'])) {
            $warehouse_address = ! empty( get_option('woocommerce_wf_multi_warehouse_settings') ) ? get_option('woocommerce_wf_multi_warehouse_settings') : array();
            if(!empty($warehouse_address)){
                foreach($warehouse_address as $warehouse_boxes => $warehouse_boxes_data){   
                    if( $package_data['warehouse_data'] === $warehouse_boxes_data['address_title']){
                        $shipment_details['to_address']['name']    = $warehouse_boxes_data['origin_name'];
                        $shipment_details['to_address']['company'] = $warehouse_boxes_data['address_title'];
                        $shipment_details['to_address']['street1'] = $warehouse_boxes_data['origin_line_1'];
                        $shipment_details['to_address']['street2'] = $warehouse_boxes_data['origin_line_2'];;
                        $shipment_details['to_address']['city']    =  $warehouse_boxes_data['origin_city'];
                        $shipment_details['to_address']['state']   = $warehouse_boxes_data['origin_state'];
                        $shipment_details['to_address']['zip']     = $warehouse_boxes_data['origin'];
                        $shipment_details['to_address']['email']   = $warehouse_boxes_data['shipper_email'];
                        $shipment_details['to_address']['phone']   = $warehouse_boxes_data['shipper_phone_number'];
                        $shipment_details['to_address']['country']   = $warehouse_boxes_data['country'];
                    }
                }
            }
            $eligible_for_customs_details = cell_is_eligible_for_customs_details($shipment_details['to_address']['country'],$shipment_details['from_address']['country'],$shipment_details['from_address']['city']);

            if( $eligible_for_customs_details){
               $international = true;
               //dry_ice
               $custom_line_array = array();
               $custom_line_array = cell_elex_check_international( $eligible_for_customs_details ,$order,$shipment_details);
               $dry_ices = cell_get_package_dry_ice($order);
               if($dry_ices == 'yes'){
                   $shipment_details['options']['dry_ice']   =  "true";
                   $shipment_details['options']['dry_ice_weight']   =   $custom_line['weight'];
               }
               //for International shipping only
               $shipment_details['customs_info']['customs_certify'] = true;
               $shipment_details['customs_info']['customs_signer'] = isset($easypost_settings['customs_signer']) ? $easypost_settings['customs_signer'] : '';
               $shipment_details['customs_info']['contents_type'] = 'merchandise';
               $shipment_details['customs_info']['contents_explanation'] = '';
               $shipment_details['customs_info']['restriction_type'] = 'none';
               $shipment_details['customs_info']['eel_pfc'] = 'NOEEI 30.37(a)';
            }
        }

        if($easypost_settings['third_party_billing'] == 'yes' && $ups){
            $shipment_details['options']['payment']['type'] = 'THIRD_PARTY';
        } else {
            $shipment_details['options']['payment']['type'] = 'SENDER';   
        }

        // Third Party payment details
        if($easypost_settings['third_party_billing'] == 'yes' && $ups){
            $shipment_details['options']['payment']['account'] = isset($shipment_details['options']['bill_third_party_account'])?$shipment_details['options']['bill_third_party_account'] : '';
            $shipment_details['options']['payment']['country'] = isset($shipment_details['options']['bill_third_party_country'])?$shipment_details['options']['bill_third_party_country'] : '';
            $shipment_details['options']['payment']['postal_code'] = isset($shipment_details['options']['bill_third_party_postal_code'])?$shipment_details['options']['bill_third_party_postal_code'] : '';
        }
        if($vendor_check){
            $default_service = cell_get_multivendor_packages_service($post_id,$package_data,$order,$service_count);
            if($default_service == 'local_pickup'){
                continue;
            }
        } else {
            $default_service = $default_service_type[$service_count];
            if($default_service == 'local_pickup'){
                continue;
            }
        }
        if($easypost_settings['weight_packing_process'] = 'pack_simple') {
            $custom_line['weight'] = $package_data['WeightOz'];
        }
        $tx_id = uniqid('wf_' . $order->id . '_');
        update_post_meta($order->id, 'wf_last_label_prepaid_return_tx_id', $tx_id);
        if(!empty($selected_flatrate_box[$service_count])){
            $selected_flatrate_box[$service_count] = rtrim($selected_flatrate_box[$service_count],'-2');
            $shipment_details['parcel']['predefined_package'] = $selected_flatrate_box[$service_count];
        } else {
            unset($shipment_details['parcel']['predefined_package']);
            $shipment_details['parcel']['length'] = $package_data['Length'];
            $shipment_details['parcel']['width'] = $package_data['Width'];
            $shipment_details['parcel']['height'] = $package_data['Height'];
        }
        //if($bulk_label != 1) {
            $service_count++;
        //}
        //Here we changed weight by 2    
        $shipment_details['parcel']['weight'] = $package_data['WeightOz'];
        $shipment_details['options']['special_rates_eligibility'] = 'USPS.LIBRARYMAIL,USPS.MEDIAMAIL';
        //   $shipment_details['parcel']['predefined_package'] = 'letter';
        if( ( $shipment_details['to_address']['country'] != $shipment_details['from_address']['country'] ) && ($easypost_settings['ex_easypost_duty'] != 'none') ) {
            $shipment_details['options']['incoterm'] = $easypost_settings['ex_easypost_duty'];
        }

        // below lines for International shipping - + customs info
        if ($international) {
            $m = 0;
            $shipment_details['customs_info']['customs_items'] = array();
            $packing_method = isset($easypost_settings['packing_method']) ? $easypost_settings['packing_method'] : 'per_item';
            //if multi-vendor
            if(isset($package_data['origin'])) {
                $index = 0;
            }
            if (!empty($package_data['PackedItem'])) {
                
                for ($m = 0; $m < sizeof($package_data['PackedItem']); $m++) {
                    //In box packing algorithm the individual product details are stored in object named 'meta'
                    if($packing_method == 'weight_based_packing')
                    {//weight based packing don't need any dimentions.
                        $item = isset( $package_data['PackedItem'][$index]->meta ) ? $package_data['PackedItem'][$index]->meta : $package_data['PackedItem'][$index];
                        $index++;
                    }
                    else
                    {
                        $item = isset( $package_data['PackedItem'][$m]->meta ) ? $package_data['PackedItem'][$m]->meta : $package_data['PackedItem'][$m];
                    }
                    $product_id_customs = $item->get_parent_id();
                    $item = cell_load_product($item);

                    if (!empty($easypost_settings['customs_description'])){
                        $prod_title = $easypost_settings['customs_description'];
                    }
                    else{
                        $prod_title = $item->get_title();
                    }
                    $shipment_desc = ( strlen($prod_title) >= 50 ) ? substr($prod_title, 0, 45) . '...' : $prod_title;
                    $shipment_details['customs_info']['customs_items'][$m]['description'] = $shipment_desc;
                    $shipment_details['customs_info']['customs_items'][$m]['quantity'] = 1; //$quantity;
                    $shipment_details['customs_info']['customs_items'][$m]['value'] = $item->get_price();
                    $wf_hs_code = get_post_meta( $item->id, '_wf_hs_code', 1);
                    $product_custom_declared_value = get_post_meta($item->id, '_wf_easypost_custom_declared_value', true);
                    if($product_custom_declared_value){
                        $shipment_details['customs_info']['customs_items'][$m]['value'] = $product_custom_declared_value;
                    }else{
                        $product_custom_declared_value = get_post_meta($product_id_customs, '_wf_easypost_custom_declared_value', true);
                        if($product_custom_declared_value){
                            $shipment_details['customs_info']['customs_items'][$m]['value'] = $product_custom_declared_value;
                        }
                    }

                    if( !empty( $wf_hs_code ) ) {
                        $shipment_details['customs_info']['customs_items'][$m]['hs_tariff_number'] = $wf_hs_code;
                    }
                    if(WC()->version < '3.0'){
                        $weight_to_send = woocommerce_get_weight( $item->weight, 'Oz' );
                    } else {
                        $weight_to_send = wc_get_weight($item->weight, 'Oz');
                    }
                    $shipment_details['customs_info']['customs_items'][$m]['weight'] = $weight_to_send/2;
                    $shipment_details['customs_info']['customs_items'][$m]['origin_country'] = $shipment_details['to_address']['country'];
                }
            } else { //if($this->packing_method == 'per_item'){ // PackedItem will be empty , also each item will be shipped separately
                $shipment_details['customs_info']['customs_items'][0] = $custom_line_array[$package_count];
            }
        }


        /*if($bulk_label == 1) {
            $wf_debug = false ;
        }*/
        
        $elex_ep_status_log = isset($easypost_settings['status_log']) && $easypost_settings['status_log'] == 'yes' ? true : false;
        //$wf_debug = isset($easypost_settings['debug_mode'])  && $easypost_settings == 'yes' ? true : false;
        
        $easypost_services = include( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/data-wf-services.php' );
        
        $wf_insure = isset($easypost_settings['insurance'])  && ($easypost_settings['insurance'] == 'yes' || $easypost_settings['insurance'] == 'optional') ? true : false;
        try{
            try{  
              
            //if(isset($query['button_name'])){
               // if($query['button_name'] == 'return'){
                   $shipment_details['is_return'] = false;   
               // }
           // }
         //   if($label_type == 'return'){
                $shipment_details['is_return'] = false; 
          //  } 
                // echo "<pre>";
                // print_r($shipment_details);
                // exit("sdssds33");
                cell_elex_ep_status_logger($shipment_details,$post_id,'Request',$elex_ep_status_log);
            
                $shipment = \EasyPost\Shipment::create($shipment_details);   
                
                cell_elex_ep_status_logger($shipment_details,$post_id,'Response',$elex_ep_status_log);
                
                //cell_wf_debug("<h3>Debug mode is Enabled. Please Disable it in the <a href='".get_site_url()."/wp-admin/admin.php?page=wc-settings&tab=shipping&section=wf_easypost' tartget='_blank'>settings  page</a> if you do not want to see this.</h3>");
                
                //cell_wf_debug( 'EasyPost CREATE SHIPMENT REQUEST: <pre style="background: rgba(158, 158, 158, 0.30);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment_details, true) . '</pre>' );
            
                //cell_wf_debug( 'EasyPost CREATE SHIPMENT OBJECT: <pre style="background: rgba(158, 158, 158, 0.30);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment, true) . '</pre>' );
                
                $check = "verified";

            } catch (Exception $e) {
                if(!empty($shipment)) {
                    
                    $shipment_obj_array = array(
                        'rate' => $shipment->lowest_rate( array_keys($easypost_services), array($default_service) ),
                    );
                
                    if( $wf_insure && (float)$package_data['InsuredValue'] > 0 ){
                        $shipment_obj_array['insurance'] = $package_data['InsuredValue'];
                    }
                    //cell_wf_debug( '<br><br>EasyPost REQUEST (Buy-shipment): <pre style="background: rgba(158, 158, 158, 0.15);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment_obj_array, true) . "</pre>");
                    
                    //cell_elex_ep_status_logger($shipment_obj_array,$post_id,'Service select Label',$elex_ep_status_log);
                    
                    $response_obj = $shipment->buy($shipment_obj_array);

                    cell_elex_ep_status_logger($response_obj,$post_id,'EasyPost Response But Label',$elex_ep_status_log);
                    
                    if(isset($error_email) && $error_email == true){
                        if(isset($response_obj)){
                            $check = "successfull";
                        } else {
                            $check = "Failed";
                        }
                    }
                    if(isset($error_email) && $check != "verified"){
                        $email_msg = cell_get_failed_shipment_email($post_id);
                        return  $email_msg;
                    }
                    $message .= __('Create shipment failed. ', 'wf-easypost');
                    $message .= $e->getMessage() . ' ';
                    $wfeasypostmsg = 6;
                    update_post_meta($post_id, 'wfeasypostmsg_prepaid_return', $message);
                    
                    //cell_wf_redirect(admin_url('/post.php?post=' . $post_id . '&action=edit&wfeasypostmsg=' . $wfeasypostmsg));
                    echo $message;
                    //exit;
                }
            }
            $srvc = $default_service;
            if($srvc == 'Priority') {
                $srvc = array(
                    $default_service,
                    'First',
                );
            } else {
                $srvc = array(
                    $default_service,
                    'Priority',
                );
            }

            try {
                if(!empty($shipment)) {
                    /*echo $srvc;
                    echo "<pre>";
                    print_r($easypost_services);
                    exit("sdsdsdds22-functiopn");*/
                    $shipment_obj_array = array(
                        'rate' => $shipment->lowest_rate( array_keys($easypost_services), $srvc ),
                    );
          
                    if( $wf_insure && (float)$package_data['InsuredValue'] > 0 ){
                        $shipment_obj_array['insurance'] = $package_data['InsuredValue'];
                    }
                    //cell_wf_debug( '<br><br>EasyPost REQUEST (Buy-shipment): <pre style="background: rgba(158, 158, 158, 0.15);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($shipment_obj_array, true) . "</pre>");

                    cell_elex_ep_status_logger($shipment_obj_array,$post_id,'Service select Label',$elex_ep_status_log);
            
                    $response_obj = $shipment->buy($shipment_obj_array);
                        
                    /*echo "<pre>";
                    print_r($shipment_details);
                    echo "=================";
                    print_r($response_obj);
                    
                    exit("Dcdcxccc22333");*/       
                    cell_elex_ep_status_logger($response_obj,$post_id,'EasyPost Response But Label',$elex_ep_status_log);
                    
                    if(isset($error_email) && $error_email == true) {
                        if(isset($response_obj)){
                            $check = "successfull";
                        } else {
                            $check = "Failed";
                        }
                    }
                } else {
                    //cell_wf_debug( '<pre style="background: rgba(158, 158, 158, 0.15);width: 90%; display: block; margin: auto; padding: 15;"> <center><font size="5">Seems like there is a connection problem. Please check your internet connection </center> </font></pre>' );   
                }
            } catch(Exception $e) {
                if(isset($error_email) && $check == "Failed"){
                    $email_msg= cell_get_failed_shipment_email($post_id);
                    return  $email_msg;
                }
                $carrier_services = include( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/data-wf-services.php' );
                $carrier;
                $carrier_name ;         
                foreach($carrier_services as $service => $code) {
                    if(array_key_exists( $default_service, $code['services'])) {
                        $carrier_name = $service;
                    }
                }
                $message .= __('Something went wrong. ', 'wf-easypost');
                if(empty($e->getMessage())) {
                    $message .=__(' Normally this could happen because of the Wrong API credentials or Unfinished Settings. Double-check your <a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&section=wf_easypost_id') . '">Settings Page</a>. Also, you may refer our <a href="https://elextensions.com/knowledge-base/troubleshooting-elex-easypost-fedex-ups-canada-post-usps-shipping-label-printing-plugin/">Trouble Shooting Document</a>. Please contact our <a href=" https://support.elextensions.com/">support</a> if you are still facing this issue.','wf-easypost');
                } else {
                    $message .= $e->getMessage() . ' ';
                }

                //This error occurs while generating shipping label.
                if($e->ecode == 'SHIPMENT.POSTAGE.FAILURE' && ($carrier_name == 'UPS' || $carrier_name == 'UPSDAP')) {
                    $message .=__('<br>The UPS account tied to the Shipper Number you are using is not yet fully set up. Please contact UPS.','wf-easypost');
                if(isset($error_email)){
                    $email_msg = cell_get_failed_shipment_email($post_id);
                    return  $email_msg;
                }
            } else {
                $wfeasypostmsg = 6;
                update_post_meta($post_id, 'wfeasypostmsg_prepaid_return', $message);
                if(isset($error_email)){
                    $email_msg = cell_get_failed_shipment_email($post_id);
                    return  $email_msg;
                } else { 
                    //cell_wf_redirect(admin_url('/post.php?post=' . $post_id . '&action=edit&wfeasypostmsg=' . $wfeasypostmsg));
                    //echo $message;
                    //exit;
                }
            }
            }
        } catch (Exception $e) {
            $message .= __('Unable to get information at this point of time. ', 'wf-easypost');
            $message .= $e->getMessage() . ' ';
        }

        if (isset($response_obj)) {
            //cell_wf_debug( '<br><br>EasyPost RESPONSE OBJECT(Buy-shipment): <pre style="background: rgba(158, 158, 158, 0.15);width: 90%; display: block; margin: auto; padding: 15;">' . print_r($response_obj, true) . "</pre>");
            //$easypost_authenticator   = ( string ) $response_obj->Authenticator;
            $label_url = (string) $response_obj->postage_label->label_url;
            $tracking_link=(string)$response_obj->tracker->public_url;
            $carrier_selected = (string) $response_obj->selected_rate->carrier;
            $form_url = (string) isset($response_obj->forms[0]->form_url) ? $response_obj->forms[0]->form_url : '';
            $zip_code_label =(string) isset($response_obj->to_address->zip) ? $response_obj->to_address->zip : '';
            $warehouse =(string) isset($response_obj->to_address->company) ? $response_obj->to_address->company : '';
            if (!empty($label_url)) {
                $easypost_label = array();
                $easypost_label['url'] = $label_url;
                $easypost_label['commercial_invoice_url'] = isset($form_url) ? $form_url : '';
                $easypost_label['tracking_number'] = (string) $response_obj->tracking_code;
                $easypost_label['integrator_txn_id'] = isset($shipment_details['IntegratorTxID']) ? $shipment_details['IntegratorTxID'] : ''; //(string) $response_obj->reference;
                $easypost_label['easypost_tx_id'] = (string) $response_obj->tracker->id;
                $easypost_label['shipment_id'] = $shipment->id;
                $easypost_label['zip_code'] = $zip_code_label;
                $easypost_label['warehouse'] = $warehouse;
                $easypost_label['order_date'] = date('Y-m-d',strtotime((string) $response_obj->updated_at));
                $easypost_label['carrier'] = $carrier_selected;
                $easypost_label['link']=$tracking_link;
                $easypost_labels[] = $easypost_label;
                if (isset($package_data['origin']) && get_option('wc_settings_wf_vendor_addon_email_labels_to_vendors') == 'yes') {
                    $label_url_html ="<html><body>".$label_url."</body></html>";
                    wp_mail( $package_data['origin']['email'],'Shipment Label - '.$order->id, 'Label '.$label_url_html, '', '');
                } 
            }
        } else {
            $message .= __('Sorry. Something went wrong:', 'wf-easypost') . '<br/>';
        }
        $package_count++;
    }
    if(isset($carrier_selected)){
        switch($carrier_selected) {
            case 'UPSDAP': {
                $carrier = 'upsdap';
                break;
            }
            case 'UPS': {
                $carrier = 'ups';
                break;
            }
            case 'USPS': {
                $carrier = 'united-states-postal-service-usps';
                break;
            }
            case 'FedEx': {
                $carrier = 'fedex';
                break;
            }
            case 'CanadaPost': {
                $carrier = 'canada-post';
                break;
            }
            case 'UPSSurePost': {
                $carrier = 'upssurepost';
                break;
            }
        }   
    }else{
        $carrier = 'united-states-postal-service-usps';
    }
    if(isset($easypost_labels) && !empty($easypost_labels)) {
        //Update post
        /*echo "<pre>";
        print_r($easypost_labels);
        echo "============="."<br/>";
        exit("dcsdssd");*/
        update_post_meta($post_id, 'wf_easypost_prepaid_return_labels', $easypost_labels);

        /*if(isset($query['button_name']) || $label_type == 'return'){
            if($label_type == 'return' || $query['button_name'] == 'return')
            {
                $previous_label = get_post_meta($post_id,'wf_easypost_return_labels',true);
                if(is_array($previous_label)){
                    foreach ($previous_label as $key => $value) {
                        array_push($easypost_labels,$value);
                    }
                }
                elseif(!empty($previous_label)){
                    array_push($easypost_labels,$previous_label);
                }
                update_post_meta($post_id, 'wf_easypost_return_labels', $easypost_labels);
            } else {
                update_post_meta($post_id, 'wf_easypost_labels', $easypost_labels);
            }
        } else {
            update_post_meta($post_id, 'wf_easypost_labels', $easypost_labels);
        }*/
        
        //$return_label = get_post_meta($post_id,'wf_easypost_return_labels',true);
        
        /*if(ELEX_EASYPOST_RETURN_ADDON_STATUS && !empty($return_label)){
           $order_id= $order->get_id();
           $email_return_label= new WF_Auto_Generate_Return_Labels();
           $email_return_label->elex_mail_addon($order_id,$response_obj);
        }*/
        // Auto fill tracking info.
        $shipment_id_cs = '';
        foreach ($easypost_labels as $easypost_label) {
            $shipment_id_cs .= $easypost_label['tracking_number'] . ',';
        }
        // Shipment Tracking (Auto)
        $admin_notice = '';
        try {
            //$admin_notice = EasypostWfTrackingUtil::update_tracking_data( $post_id, $shipment_id_cs, $carrier, WF_Tracking_Admin_EasyPost::SHIPMENT_SOURCE_KEY, WF_Tracking_Admin_EasyPost::SHIPMENT_RESULT_KEY );
                
        } catch ( Exception $e ) {
                $admin_notice = '';
                // Do nothing.
        }
        
        // Shipment Tracking (Auto)
        if( '' != $admin_notice && !$this->wf_debug ) {
            if($bulk_label==1)
            {
                return;
            }

            //WF_Tracking_Admin_EasyPost::display_admin_notification_message( $post_id, $admin_notice );
        } else {
            //Do your plugin's desired redirect.
            $wfeasypostmsg = 2;
            update_post_meta($post_id, 'wfeasypostmsg_prepaid_return', $message);
            //cell_wf_redirect( admin_url('/post.php?post=' . $post_id . '&action=edit&wfeasypostmsg=' . $wfeasypostmsg) );
            //exit;
        }
    
    } else {
        $admin_notice = '';
        //delete_post_meta($post_id, 'wf_easypost_labels');
        //delete_post_meta($post_id,'wf_easypost_return_labels');
    }

    if ('' != $message) {
        $wfeasypostmsg = 2;
        update_post_meta($post_id, 'wfeasypostmsg_prepaid_return', $message);
        //cell_wf_redirect(admin_url('/post.php?post=' . $post_id . '&action=edit&wfeasypostmsg=' . $wfeasypostmsg));
        //exit;
    }

    // Shipment Tracking (Auto)
    if ('' != $admin_notice) {
        if($bulk_label == 1)
        {
            return;
        }
        //WF_Tracking_Admin_Easypost::display_admin_notification_message($post_id, $admin_notice);
    } else {
        /*if($bulk_label == 1)
        {
            return;
        } else {
            $wfeasypostmsg = 1;
            cell_wf_redirect(admin_url('/post.php?post=' . $post_id . '&action=edit&wfeasypostmsg=' . $wfeasypostmsg));
            exit;
        }*/
    }

}

function cell_manual_packages($packages){
    //If manual values not provided
    if(!isset($_GET['weight'])){
        return $packages;
    }
    
    // Get manual values
    $length_arr     =   json_decode(stripslashes(html_entity_decode($_GET["length"])));
    $width_arr      =   json_decode(stripslashes(html_entity_decode($_GET["width"])));
    $height_arr     =   json_decode(stripslashes(html_entity_decode($_GET["height"])));
    $weight_arr     =   json_decode(stripslashes(html_entity_decode($_GET["weight"])));     
    $insurance_arr  =   json_decode(stripslashes(html_entity_decode($_GET["insurance"])));
    if(isset($_GET["wf_easypost_warehouse_box"])){
        $warehouse_data  =   json_decode(stripslashes(html_entity_decode($_GET["wf_easypost_warehouse_box"])));
    }
    

    //If extra values provided, then add it with the package list
    
    $no_of_package_entered  =   count($weight_arr);
    $no_of_packages         =   count($packages);
    
    if($no_of_package_entered > $no_of_packages){ 
        $package_clone  =   current($packages);
        
        if(isset($package_clone['PackedItem'])){ // Everything clone except packed items
            unset($package_clone['PackedItem']);
        }
        
        for($i=$no_of_packages; $i<$no_of_package_entered; $i++){
            $packages[$i]   =   $package_clone;
        }
    }
    
    // Overridding package values
    foreach($packages as $key => $package){
        if(isset($weight_arr[$key])){
        $packages[$key]['WeightOz']      =  $weight_arr[$key];
        $packages[$key]['Length']        =  $length_arr[$key];
        $packages[$key]['Width']         =  $width_arr[$key];
        $packages[$key]['Height']        =  $height_arr[$key];
        $packages[$key]['InsuredValue']  =  isset($insurance_arr[$key]) ? $insurance_arr[$key] : '';
        $packages[$key]['warehouse_data']  =  isset($warehouse_data[$key]) ? $warehouse_data[$key] : '';
        }else{
            unset($packages[$key]);
        }
    }
    
    return $packages;
}

function cell_get_shipping_service_data($order) {

    $shipping_methods = $order->get_shipping_methods();
    if (!$shipping_methods) {
        return false;
    }
   
    $shipping_method = array_shift($shipping_methods);

    $shipping_service_tmp_data = explode(':', $shipping_method['method_id']);
    if(WC()->version < '3.4.0') {
        $shipping_service = $shipping_service_tmp_data[1];
    }
    else
        $shipping_service = $shipping_method['instance_id'];
    
    $wf_easypost_selected_service = '';
    $wf_easypost_selected_service = get_post_meta($order->id, 'wf_easypost_prepaid_return_selected_service', true);
    if ('' != $wf_easypost_selected_service) {
        $shipping_service_data['shipping_method'] = 'wf_easypost_id';
        $shipping_service_data['shipping_service'] = $wf_easypost_selected_service;
        $shipping_service_data['shipping_service_name'] = $shipping_method['name'];
    } else if (!isset($shipping_service_tmp_data[0]) ||
            ( isset($shipping_service_tmp_data[0]) && $shipping_service_tmp_data[0] != WF_EASYPOST_ID  && $shipping_service_tmp_data[0] != 'local_pickup')) {
        $shipping_service_data['shipping_method'] = 'wf_easypost_id';
        $shipping_service_data['shipping_service'] = '';
        $shipping_service_data['shipping_service_name'] = '';
    } else {
        $shipping_service_data['shipping_method'] = $shipping_service_tmp_data[0];
        $shipping_service_data['shipping_service'] = $shipping_service;
        $shipping_service_data['shipping_service_name'] = $shipping_method['name'];
    }
    return $shipping_service_data;
}

// Perferred service selection in order page for flat rate services
function cell_label_generation_flat_service($service_name){
    if($service_name != ''){
        $easypost_settings = get_option('woocommerce_wf_easypost_id_settings', null);
        $mail_international         = '';
        $express_international      = '';
        $priority_mail_flat         = '';
        $priority_mail_flat         = $easypost_settings['flat_rate_boxes_text'];
        $priority_mail_express_flat = $easypost_settings['flat_rate_boxes_express_text'];
        $first_class_mail_flat = $easypost_settings['flat_rate_boxes_first_class_text'];
        $fedex_onerate_flat = $easypost_settings['flat_rate_boxes_fedex_one_rate_text'];
        $service_from_customer      = explode(':', $service_name);
        if(!empty($easypost_settings['flat_rate_boxes_text_international_mail'])){
            $mail_international  = $easypost_settings['flat_rate_boxes_text_international_mail'];
        }
        if(!empty($easypost_settings['flat_rate_boxes_text_international_express'])){
            $express_international  = $easypost_settings['flat_rate_boxes_text_international_express'];
        }
        if(!empty($easypost_settings['flat_rate_boxes_text_first_class_mail_international'])){
            $first_class_international  = $easypost_settings['flat_rate_boxes_text_first_class_mail_international'];
        }
        $easypost_services = include( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/data-wf-services.php' );
        foreach ($easypost_services as $key => $code) {
            foreach ($code['services'] as $key => $code_name) {                     
               if($service_name == $priority_mail_flat ){
                    $service_name = $code['services']['Priority'];
                    break;
                }
                if($service_name == $priority_mail_express_flat){
                    $service_name  = $code['services']['Express']; 
                    break;  
                }
                if($service_name == $first_class_mail_flat){
                    $service_name  = $code['services']['First']; 
                    break;  
                }
                    
                if(strpos($service_name , $fedex_onerate_flat) !== 0){
                    $fedex_flat_service = explode('(',$service_name);
                    if($fedex_flat_service[0] == $key){
                      $service_name  = $code['services'][$key]; 
                      break;  
                    } 
                }
                if(($mail_international == $service_name ) || (isset($service_from_customer[1]) && $service_from_customer[1] == $code_name)){
                    $service_name = $code['services']['PriorityMailInternational'] ;
                    break;
                }
                if(($express_international == $service_name) || (isset($service_from_customer[1]) && $service_from_customer[1] == $code_name)){
                   
                    $service_name = $code['services']['ExpressMailInternational'] ;
                    break;
                }
                if(($first_class_international == $service_name) || (isset($service_from_customer[1]) && $service_from_customer[1] == $code_name)){
                    $service_name = $code['services']['FirstClassMailInternational'] ;
                    break;
                }
            
           }
        }
    }
    return $service_name;
}

function cell_get_package_signature($order){
    $order_items = $order->get_items();
    $higher_signature_option = 0;
    foreach ($order_items as $order_item) {
        $signature = get_post_meta( $order_item['product_id'], '_wf_easypost_signature', 1);

        if( empty($signature) || !is_numeric ( $signature )){
            $signature = 0;
        }
        if( $signature > $higher_signature_option ){
            $higher_signature_option = $signature;
        }
    }
    return $signature_options[$higher_signature_option];
}

function cell_get_special_rates_eligibility($service){
    if( $service == 'MediaMail' ){
        $special_rates = 'USPS.MEDIAMAIL';

    }elseif ($service == 'LibraryMail') {
        $special_rates = 'USPS.LIBRARYMAIL';

    }else{
        $special_rates = false;
    }
    return $special_rates;
}

function cell_is_eligible_for_customs_details($from_country, $to_country, $to_city) {
    $eligible_cities  = array("APO","FPO","DPO");
    if (($from_country != $to_country) || (in_array(strtoupper($to_city), $eligible_cities))) {
        return true;
    }
    else {
        return false;
    }
}

function cell_elex_check_international($eligible_for_customs_details,$order,$shipment_details) {
    if($eligible_for_customs_details){
        $international = TRUE;
        $order_items = $order->get_items();
        $custom_line_array = array();
        foreach ($order_items as $order_item) {
            for ($i=0; $i < $order_item['qty']; $i++) { 
                $product_data = wc_get_product($order_item['variation_id'] ? $order_item['variation_id'] : $order_item['product_id'] );
                $title = $product_data->get_title();
                if(WC()->version < '3.0'){
                   $weight = woocommerce_get_weight($product_data->get_weight(), 'lbs'); 
                }else{
                    $weight = wc_get_weight($product_data->get_weight(), 'lbs');
                }
                $shipment_description = $title;
                if (!empty($easypost_settings['customs_description'])) $shipment_description = $easypost_settings['customs_description'];
                $shipment_description = ( strlen($shipment_description) >= 50 ) ? substr($shipment_description, 0, 45) . '...' : $shipment_description;
                $quantity = $order_item['qty'];
                $value = $order_item['line_subtotal'];


                $custom_line = array();
                $custom_line['description'] = $shipment_description;
                $custom_line['quantity'] = 1;
                $custom_line['value'] = $value/$quantity;
                $custom_line['weight'] = (string) ($weight*16);
                $custom_line['origin_country'] = $shipment_details['to_address']['country'];
                $wf_hs_code = get_post_meta( $order_item['product_id'], '_wf_hs_code', 1);
                if( !empty( $wf_hs_code ) ) {
                    $custom_line['hs_tariff_number'] = $wf_hs_code;
                } 
                if($order_item['variation_id']){
                    $product_id_customs = $order_item['variation_id'];
                }else{
                    $product_id_customs = $order_item['product_id'];
                }
                $product_custom_declared_value = get_post_meta($product_id_customs, '_wf_easypost_custom_declared_value', true);
                if($product_custom_declared_value){
                    $custom_line['value'] = $product_custom_declared_value;
                }else{
                    $product_custom_declared_value = get_post_meta($order_item['product_id'], '_wf_easypost_custom_declared_value', true);
                    if($product_custom_declared_value){
                        $custom_line['value'] = $product_custom_declared_value;
                    }
                }
            }
            $custom_line_array[] = $custom_line;
        }
        
    }
    return $custom_line_array;
}

function cell_get_package_dry_ice($order){
    $order_items = $order->get_items();
    $higher_signature_option = 0;
    foreach ($order_items as $order_item) {
        $dry_ice = get_post_meta( $order_item['product_id'], '_wf_dry_ice_code');
    }
    return $dry_ice;
}

function cell_get_multivendor_packages_service($post_id,$package_data,$order,$service_count){
    $shipping_methods = $order->get_shipping_methods();
    
    if (!$shipping_methods) {
        return false;
    }
    $carrier_services_bulk = include( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/data-wf-services.php' );
    foreach($shipping_methods as $key => $value){
        $vendor_id = $value->get_meta('seller_id');
        if(empty($vendor_id )){
            $vendor_id = $value->get_meta('vendor_id');
        }
        if($vendor_id == $package_data['origin']['vendor_id']){
            
            if($value['name'] == 'Local pickup'){
                return 'local_pickup';
            }
            $service_selected = false;
            
            foreach($carrier_services_bulk as $service => $code) {
                
                if( in_array( $value['name'], $code['services'] ) )
                {
                    $service_selected = true;
                    $bulk_service = $code['services'];
                    foreach ($bulk_service as $key => $service_name) 
                    {
                        if($service_name == $value['name'])
                        {
                            $default_service_type  = $key ;
                            update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $key);
                        }
                    }      
                }
                elseif($service_selected == false){
                    if($order->shipping_country == $easypost_settings['country'])
                    {
                        $default_service_type = $easypost_settings['easypost_default_domestic_shipment_service'];
                        $bulk_service = $code['services'];
                        foreach ($bulk_service as $bulk_servicekey => $bulk_service_value) {
                            
                            if($bulk_servicekey  == $default_service_type)
                            {
                                $default_service_type  = $bulk_servicekey  ;
                                update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $bulk_servicekey );
                            }
                        }
                    } else {
                        if($easypost_settings['easypost_default_international_shipment_service'] != 'NA')
                        {
                            $default_service_type = $easypost_settings['easypost_default_international_shipment_service'];
                            $bulk_service = $code['services'];
                            foreach ($bulk_service as $bulk_servicekey  => $bulk_servicevalue) {
                                if($bulk_servicekey  == $default_service_type)
                                {
                                    $default_service_type  = $bulk_servicekey  ;
                                    update_post_meta($post_id, 'wf_easypost_prepaid_return_selected_service', $bulk_servicekey );
                             
                                }
                            }
                        }
                    }
                }
            }
            return $default_service_type;
        }
    }
}

function cell_load_product( $product ){
    if( !$product ){
        return false;
    }
    if( !class_exists('wf_product') ){
        //include_once('class-wf-legacy.php');
        include_once ( WP_PLUGIN_DIR . '/easypost-woocommerce-shipping/includes/class-wf-legacy.php');
    }
    if($product instanceof wf_product){
        return $product;
    }
    return ( WC()->version < '2.7.0' ) ? $product : new wf_product( $product );
}

//To view the values on Status Log
function cell_elex_ep_status_logger($message = '' , $order_id = '' ,$type = '',$elex_ep_status_log = false){
    if($elex_ep_status_log){
        $log = wc_get_logger();
        $head="<------------------- Easypost Pre-Paid Box Return ".$order_id.$type." ------------------->/n";
        $log_text=$head.print_r((object)$message,true);
        $context = array( 'source' => 'eh_easypost_log'.$order_id );
        $log->log("debug", $log_text,$context);
    }
}

function cell_get_failed_shipment_email($post_id){
    if($error_email == true){
        if(isset($easypost_settings['enable_failed_email']) && $easypost_settings['enable_failed_email'] == 'yes'){
            
            $to = get_option( 'admin_email' );
            $email_subject = $easypost_settings['failed_email_subject'];
            $email_content = $easypost_settings['failed_email_content'];
            wp_mail( $to,$email_subject.' ['.$post_id.']',$email_content, '', '');
            return;
        }
        if(isset($easypost_settings['auto_label_enable_failed_email']) && $easypost_settings['auto_label_enable_failed_email'] == 'yes'){
           
            $to = get_option( 'admin_email' );
            $email_subject = $easypost_settings['auto_label_failed_email_subject'];
            $email_content = $easypost_settings['auto_label_failed_email_content'];
            wp_mail( $to,$email_subject.' ['.$post_id.']',$email_content, '', '');
            return;
        }
        return;
    }
}

function cell_wf_debug( $message ) {
    //if ( $this->wf_debug ) {
        echo($message);         
    //}
    return;
}

function cell_wf_redirect($url){
    //if(!$this->wf_debug){
        //wp_redirect($url);
    //}
}


    add_action("admin_init", "pub_meta_id_sp2");


function pub_meta_id_sp2(){
    add_meta_box("pub_meta_id_sp2", "Shipment2 Deatils", "wpse_pub_meta_fields_sp2",   "shop_order", "normal", "low");
}

function wpse_pub_meta_fields_sp2( $post ) { 
    
    /*$shipping_id = get_post_meta($post->ID , 'shipping_prepaid' , true);
    $shipping_method_array = array(
        '1'=> 'Pre-Paid Box',
        '2'=> 'First-Class Mail (USPS)',
        '3'=> 'FedEx Ground (FedEx)',
        '4'=> 'Ground (UPS)',
        '5'=> 'Priority Mail (USPS)'
        
    );*/

    $easypost_return_labels = get_post_meta($post->ID, 'wf_easypost_prepaid_return_labels', true);
    
    if(isset($easypost_return_labels) && !empty($easypost_return_labels) ) {
        //$shipping_method = $shipping_method_array[$shipping_id];
        echo "<table><tr><td>Shipment2 Deatils:</td><td>WeCellTrade To Customer</td></tr></table>"; 

        foreach ($easypost_return_labels as $easypost_return_label) { ?>
        
            <strong><?php _e('Tracking No:', 'wf-easypost'); ?> </strong><?php echo $easypost_return_label['tracking_number']; ?><br/>
            <a href="<?php echo esc_attr($easypost_return_label['url']); ?>" target="_blank" class="button button-primary tips" data-tip="<?php _e('Print Label ', 'wf-easypost'); ?>">
                <?php if (strstr($easypost_return_label['url'], '.png') || strstr($easypost_return_label['url'], '.pdf') || strstr($easypost_return_label['url'], '.zpl')) : ?> <?php _e('Print Label', 'wf-easypost'); ?>
                <?php else : ?>
                    <?php _e('View Label', 'wf-easypost'); ?>
                <?php endif; ?>
            </a>
            <br/>
            <?php
            if(isset($easypost_return_label['commercial_invoice_url']) && !empty($easypost_return_label['commercial_invoice_url'])){
                ?>
            
            <strong><?php _e('Commercial Invoice:', 'wf-easypost'); ?> </strong><br/>
            <a href="<?php echo esc_attr($easypost_label['commercial_invoice_url']); ?>" target="_blank" class="button button-primary tips" data-tip="<?php _e('Print Label ', 'wf-easypost'); ?>">
                <?php if (strstr($easypost_return_label['commercial_invoice_url'], '.png') || strstr($easypost_return_label['commercial_invoice_url'], '.pdf') || strstr($easypost_return_label['commercial_invoice_url'], '.zpl')) : ?> <?php _e('Print Commercial Invoice', 'wf-easypost'); ?>
                <?php else : ?>
                    <?php _e('View Commercial Invoice', 'wf-easypost'); ?>
                <?php endif; ?>
            </a>
            <br/>
            <?php
            }
        }    
    }
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


// ADDING 2 NEW COLUMNS WITH THEIR TITLES (keeping "Total" and "Actions" columns at the end)
add_filter( 'manage_edit-shop_order_columns', 'custom_shop_order_column', 20 );
function custom_shop_order_column($columns)
{
    $reordered_columns = array();

    // Inserting columns to a specific location
    foreach( $columns as $key => $column){
        $reordered_columns[$key] = $column;
        if( $key ==  'order_status' ){
            // Inserting after "Status" column
            $reordered_columns['shipping_method'] = __( 'Shipping Method','theme_domain');
             
        }
    }
    return $reordered_columns;
}

// Adding custom fields meta data for each new column (example)
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 20, 2 );
function custom_orders_list_column_content( $column, $post_id )
{
    switch ( $column )
    {
        case 'shipping_method' :
            // Get custom post meta data
            $shipping_method_array = array(
                '1'=> 'Pre-Paid Box',
                '2'=> 'First-Class Mail (USPS)',
                '3'=> 'FedEx Ground (FedEx)',
                '4'=> 'Ground (UPS)',
                '5'=> 'Priority Mail (USPS)'
                
            );
            $my_var_one = get_post_meta( $post_id, 'shipping_prepaid', true );
            if(!empty($my_var_one))
                echo $shipping_method_array[$my_var_one];

            // Testing (to be removed) - Empty value case
            else
                echo '<small>(<em>no value</em>)</small>';

            break;

        
    }
}

add_action('init','current_url');
function current_url(){
 
 global $wp;
 
$current_url="https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

$url = array("https://wecelltrade.com/brands/apple/page/4/","https://wecelltrade.com/sell-my-iphone/?product_slug=phones");

    if(in_array($current_url,$url)){
     wp_redirect(home_url());

    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//
//

// Shortcode to output custom PHP in Elementor
function wpc_elementor_shortcode_header( $atts ) {
    $html_content = "<header class=\"banner\" style=\"padding:2% 0!important;\" data-relative-input=\"true\">
	<div class=\"wrap flex-wrapper\">

		<div class=\"content-container fade-in-target\" style=\"transform: translate3d(0px, 0px, 0px); opacity: 1;\">

			<!-- subtitle -->
			
			<!-- title -->
			<h1 style=\"text-transform:inherit !important;\">";
    $html_content.= get_the_title();
    $html_content.= "</h1><div class=\"button-desktop\"> </div> </div> </div> <div id=\"banner-wave-scene\" class=\"fade-in-target-left\" style=\"transform: translate3d(0px, 0px, 0px); transform-style: preserve-3d; backface-visibility: hidden; pointer-events: none; opacity: 1;\"> <div class=\"banner-wave\" data-depth=\"0.4\" style=\"transform: translate3d(56.1px, -12px, 0px); transform-style: preserve-3d; backface-visibility: hidden; position: relative; display: block; left: 0px; top: 0px;\"> <div class=\"svg banner-wave-3\"><svg width=\"1106px\" height=\"664px\" viewBox=\"0 0 1106 664\" preserveAspectRatio=\"none\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> <!-- Generator: Sketch 55.2 (78181) - https://sketchapp.com --> <title>Fill 4</title> <desc>Created with Sketch.</desc> <defs> <linearGradient x1=\"0%\" y1=\"100%\" x2=\"99.4157579%\" y2=\"100%\" id=\"linearGradient-3\"> <stop stop-color=\"#237344\" offset=\"0%\"></stop> <stop stop-color=\"#21bf75\" offset=\"100%\"></stop> </linearGradient> </defs> <g id=\"Homepage-&amp;-Styleguide\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\"> <g id=\"Homepage-/-High-Fidelity-Alt-2\" transform=\"translate(-24.000000, -260.000000)\" fill=\"url(#linearGradient-3)\"> <g id=\"hero-banner\" transform=\"translate(24.000000, 22.000000)\"> <g id=\"Group-10\" transform=\"translate(537.500000, 610.000000) scale(-1, 1) translate(-537.500000, -610.000000) translate(-36.000000, 160.000000)\"> <g id=\"Group\" transform=\"translate(573.500000, 450.000000) scale(-1, 1) translate(-573.500000, -450.000000) \"> <g id=\"Group-12\" transform=\"translate(573.500000, 450.000000) scale(-1, 1) translate(-573.500000, -450.000000) \"> <g id=\"Group-3\" transform=\"translate(2.000000, 1.076610)\"> <path d=\"M0.870954364,741.261405 C0.870954364,741.261405 0.704678875,741.307629 0.704678875,741.29151 C0.685289971,741.166947 11.151665,740.139966 11.3822287,740.115849 C250.9257,713.354044 396.770541,487.18504 396.770541,487.18504 C504.472877,320.166939 457.554393,208.244072 558.958702,125.766022 C642.766153,57.6013984 741.731342,79.6222075 761.694511,84.0637507 C898.434516,114.489326 938.98459,240.450284 1015.11549,223.071993 C1047.88464,215.59171 1094.2607,174.811913 1136.75721,0.659207453 C1136.94409,247.526607 1137.13218,494.394006 1137.32027,741.261405 L0.870954364,741.261405 Z\" id=\"Fill-4\"></path> </g> </g> </g> </g> </g> </g> </g> </svg></div> </div> <div class=\"banner-wave\" data-depth=\"0.3\" style=\"transform: translate3d(42.1px, -9px, 0px); transform-style: preserve-3d; backface-visibility: hidden; position: absolute; display: block; left: 0px; top: 0px;\"> <div class=\"svg banner-wave-2\"><svg width=\"1108px\" height=\"559px\" viewBox=\"0 0 1108 559\" preserveAspectRatio=\"none\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> <!-- Generator: Sketch 55.2 (78181) - https://sketchapp.com --> <title>Fill 7</title> <desc>Created with Sketch.</desc> <defs> <linearGradient x1=\"6.21042573%\" y1=\"71.2424839%\" x2=\"103.711243%\" y2=\"34.263052%\" id=\"linearGradient-2\"> <stop stop-color=\"#309eb7\" offset=\"0%\"></stop> <stop stop-color=\"#309eb7\" offset=\"100%\"></stop> </linearGradient> </defs> <g id=\"Homepage-&amp;-Styleguide\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\"> <g id=\"Homepage-/-High-Fidelity-Alt-2\" transform=\"translate(-24.000000, -365.000000)\" fill=\"url(#linearGradient-2)\"> <g id=\"hero-banner\" transform=\"translate(24.000000, 22.000000)\"> <g id=\"Group-10\" transform=\"translate(537.500000, 610.000000) scale(-1, 1) translate(-537.500000, -610.000000) translate(-36.000000, 160.000000)\"> <g id=\"Group\" transform=\"translate(573.500000, 450.000000) scale(-1, 1) translate(-573.500000, -450.000000) \"> <g id=\"Group-12\" transform=\"translate(573.500000, 450.000000) scale(-1, 1) translate(-573.500000, -450.000000) \"> <g id=\"Group-9\" transform=\"translate(2.704679, 0.362708)\"> <path d=\"M0.295321125,741.637292 C75.7953655,735.875355 158.387588,713.777313 248.071988,675.343168 C382.598589,617.691949 491.207221,506.507457 605.986797,340.417042 C720.766374,174.326627 787.41258,332.181153 952.793905,332.181153 C1063.04812,332.181153 1124.14136,221.775562 1136.07361,0.964379194 C1136.3236,329.839626 1136.51129,576.496061 1136.63669,740.933685 L0.295321125,741.637292 Z\" id=\"Fill-7\"></path> </g> </g> </g> </g> </g> </g> </g> </svg></div> </div> <div class=\"banner-wave\" data-depth=\"0.2\" style=\"transform: translate3d(28.1px, -6px, 0px); transform-style: preserve-3d; backface-visibility: hidden; position: absolute; display: block; left: 0px; top: 0px;\"> <div class=\"svg banner-wave-1\"><svg width=\"1110px\" height=\"409px\" viewBox=\"0 0 1110 409\" preserveAspectRatio=\"none\" version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\" xmlns:xlink=\"http://www.w3.org/1999/xlink\"> <!-- Generator: Sketch 55.2 (78181) - https://sketchapp.com --> <title>Mask</title> <desc>Created with Sketch.</desc> <defs> <linearGradient x1=\"51.5862521%\" y1=\"39.0847531%\" x2=\"50.7337729%\" y2=\"54.2903992%\" id=\"linearGradient-1\"> <stop stop-color=\"#36879b\" offset=\"0%\"></stop> <stop stop-color=\"#36879b\" offset=\"100%\"></stop> </linearGradient> <path d=\"M0.295321125,408.735586 C182.853708,407.260915 311.055351,359.333157 394.97416,316.201044 C479.64101,272.686161 586.820622,198.362338 658.290671,148.803721 C789.996815,57.4748453 832.584235,16.5849573 912.604016,3.72792098 C1004.12205,-10.9784978 1083.98665,20.7188712 1135.41113,48.3669385 L1145.32739,566 C1024.12872,444.714286 915.049924,417.149351 818.090992,483.305195 C672.652593,582.538961 431.357068,518.220779 309.056597,470.441558 C227.522949,438.588745 124.602524,418.020087 0.295321125,408.735586 Z\" id=\"path-2\"></path> </defs> <g id=\"Homepage-&amp;-Styleguide\" stroke=\"none\" stroke-width=\"1\" fill=\"none\" fill-rule=\"evenodd\"> <g id=\"Homepage-/-High-Fidelity-Alt-2\" transform=\"translate(-24.000000, -515.000000)\"> <g id=\"hero-banner\" transform=\"translate(24.000000, 22.000000)\"> <g id=\"Group-10\" transform=\"translate(537.500000, 610.000000) scale(-1, 1) translate(-537.500000, -610.000000) translate(-36.000000, 160.000000)\"> <g id=\"Group\" transform=\"translate(573.500000, 450.000000) scale(-1, 1) translate(-573.500000, -450.000000) \"> <g id=\"Group-12\" transform=\"translate(573.500000, 450.000000) scale(-1, 1) translate(-573.500000, -450.000000) \"> <g id=\"Fill-10\" transform=\"translate(0.704679, 333.264414)\"> <mask id=\"mask-3\" fill=\"white\"> <use xlink:href=\"#path-2\"></use> </mask> <use id=\"Mask\" fill=\"url(#linearGradient-1)\" xlink:href=\"#path-2\"></use> </g> </g> </g> </g> </g> </g> </g> </svg></div> </div> </div> </header>";
    echo $html_content;
}
add_shortcode( 'my_elementor_php_output', 'wpc_elementor_shortcode_header');


//// code for side bar /////
add_shortcode('postdata','postdata_function');

function postdata_function(){
?>
<style>
.accordion {
    background-color: #fff;
    color: #444;
    cursor: pointer;
    padding: 10px;
    width: 100%;
    border: 1px solid #d4d4d4;
    text-align: left;
    outline: none;
    font-size: 15px;
    transition: 0.4s;
}

.accordion .active {
    background-color: #ccc;
}

.accordion:after {
    content: '\002B';
    color: #777;
    font-weight: bold;
    float: right;
    margin-left: 5px;
    top: -25px;
    position: relative;
    text-align: right;
    right: 1%;
}
ul.panel li a {
    color: #000;
}
ul.panel li {
    list-style-type: circle;
    color: #237344;
}
.active:after {
  content: "\2212";
}

.panel {
    padding: 0 34px;
    background-color: white;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.2s ease-out;
    border-left: 1px solid #d4d4d4;
    width: 100%;
    border-right: 1px solid #d4d4d4;
}
.accordion h4 {
    margin-bottom: 0px;
}
</style>
<?php
 global $wpdb;

$limit = 0;
$year_prev = null;
$months = $wpdb->get_results("SELECT  MONTH( post_date ) AS month ,  YEAR( post_date ) AS year, post_title FROM $wpdb->posts WHERE post_status = 'publish' and post_date <= now( ) and post_type = 'post' GROUP BY month , year ORDER BY post_date DESC");

// echo"<pre>";

// print_r($months);
foreach($months as $month) :
        $per_month = $month->month;
       
    $year_current = $month->year;
    
    if ($year_current != $year_prev){
        if ($year_prev != null){?>
          
        <?php } ?>
     
  
    <?php } ?>
   
    
    <div class="accordion"><h4><?php echo date_i18n("F Y", mktime(0, 0, 0, $month->month, 1, $month->year)) ?></h4>
    </div>
    
        <ul class="panel">
    <?php echo get_post_title_by_month_year($per_month );?>
    
    </ul>
    
     
    

<?php $year_prev = $year_current;
  
if(++$limit >= 18) { break; }
  
endforeach; ?>

<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>

<?php

} 

// get data from  table
function get_post_title_by_month_year($month ,$year=null){
    global $wpdb;
    // echo $month;
    // echo "SELECT  MONTH( post_date ) AS month ,  YEAR( post_date ) AS year, post_title, guid FROM $wpdb->posts WHERE post_status = 'publish' and and MONTH(post_date)='$month'  ORDER BY post_date DESC";
  $result =  $wpdb->get_results("SELECT  MONTH( post_date ) AS month ,  YEAR( post_date ) AS year,ID, post_title, guid, post_name FROM $wpdb->posts WHERE post_status = 'publish' and post_type = 'post' and MONTH(post_date)='$month'  ORDER BY post_date DESC");
  $html='';

    //   echo"<pre>";
    //         print_r($result);
            
    foreach($result as $data){
        // $attachment_id = get_post_meta($data->ID , '_thumbnail_id', true);
        // $post_details = get_the_terms($data, 'post_tag');
        
     $category = get_the_permalink($data);
      
     $html .= '
           <li><a href= '.$category.'>'.$data->post_title.'</a>';
           
           
    }
    return $html;
    
}


function generate_rewrite_rules( $wp_rewrite ) {
    $new_rules = array(
        //'(.?.+?)/page/?([0-9]{1,})/?$' => 'index.php?pagename=$matches[1]&paged=$matches[2]',
        //'blog/([^/]+)/?$' => 'index.php?post_type=post&name=$matches[1]',
        'blog/[^/]+/attachment/([^/]+)/?$' => 'index.php?post_type=post&attachment=$matches[1]',
        'blog/[^/]+/attachment/([^/]+)/trackback/?$' => 'index.php?post_type=post&attachment=$matches[1]&tb=1',
        'blog/[^/]+/attachment/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
        'blog/[^/]+/attachment/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
        'blog/[^/]+/attachment/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?post_type=post&attachment=$matches[1]&cpage=$matches[2]',     
        'blog/[^/]+/attachment/([^/]+)/embed/?$' => 'index.php?post_type=post&attachment=$matches[1]&embed=true',
        'blog/[^/]+/embed/([^/]+)/?$' => 'index.php?post_type=post&attachment=$matches[1]&embed=true',
        'blog/([^/]+)/embed/?$' => 'index.php?post_type=post&name=$matches[1]&embed=true',
        'blog/[^/]+/([^/]+)/embed/?$' => 'index.php?post_type=post&attachment=$matches[1]&embed=true',
        'blog/([^/]+)/trackback/?$' => 'index.php?post_type=post&name=$matches[1]&tb=1',
        'blog/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&name=$matches[1]&feed=$matches[2]',
        'blog/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&name=$matches[1]&feed=$matches[2]',
        'blog/page/([0-9]{1,})/?$' => 'index.php?post_type=post&paged=$matches[1]',
		'blog/([^/]+)/?$' => 'index.php?post_type=post&taxonomy=category&category_name=$matches[1]',
        'blog/([^/]+)/page/?([0-9]{1,})/?$' => 'index.php?post_type=post&taxonomy=category&category_name=$matches[1]&paged=$matches[2]',
        'blog/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?post_type=post&name=$matches[1]&cpage=$matches[2]',
        //'blog/([^/]+)(/[0-9]+)?/?$' => 'index.php?post_type=post&name=$matches[1]&page=$matches[2]',
        'blog/[^/]+/([^/]+)/?$' => 'index.php?post_type=post&attachment=$matches[1]',
        'blog/[^/]+/([^/]+)/trackback/?$' => 'index.php?post_type=post&attachment=$matches[1]&tb=1',
        'blog/[^/]+/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
        'blog/[^/]+/([^/]+)/(feed|rdf|rss|rss2|atom)/?$' => 'index.php?post_type=post&attachment=$matches[1]&feed=$matches[2]',
        'blog/[^/]+/([^/]+)/comment-page-([0-9]{1,})/?$' => 'index.php?post_type=post&attachment=$matches[1]&cpage=$matches[2]',
    );
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
    
}
add_action( 'generate_rewrite_rules', 'generate_rewrite_rules' );

function update_post_link( $post_link, $id = 0 ) {
    $post = get_post( $id );
    
    if( is_object( $post ) && $post->post_type == 'post' ) {
        $base_url = "/blog/";
        $category = get_the_category($id);
        if(count($category) > 0){
            $base_url.= $category[0]->slug ."/";
        }
        return home_url( $base_url . $post->post_name );
    }
    return $post_link;
}
add_filter( 'post_link', 'update_post_link', 1, 3 );


function get_breadcrumb() {
     echo '<a href="'.home_url().'" rel="nofollow">Home</a>';
      echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
      echo '<a href="'.site_url().'/blog" rel="nofollow">Blog</a>';
      
    
      if (is_category() || is_single() ) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_category(' &bull; ');
        
    } 

}

add_shortcode('crumb','get_crumb');

function get_crumb(){
    echo '<a href="'.home_url().'" rel="nofollow">Home</a>';
      echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
      echo '<a href="'.site_url().'/blog" rel="nofollow">Blog</a>';
      
    
      if (is_category() || is_single() ) {
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_category(' &bull; ');
        echo "&nbsp;&nbsp;&#187;&nbsp;&nbsp;";
        the_title();
        
    } 
    
}

add_filter( 'get_the_guid', 'wpse17463_get_the_guid' );
function wpse17463_get_the_guid( $guid )
{
    return get_permalink();
}
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

function add_cnd_variations($post_id) {

    $product = wc_get_product( $post_id );
    $p_type = $product->get_type();
    if ($p_type != 'variable') {
        return;
    }

    // Get the Condition Product Attribute
    global $wpdb;
    $attributes = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
        WHERE attribute_name = 'condition'" );

    $position   = 0;  // Auto incremented position value starting at '0'
    $data       = array(); // initialising (empty array)

    // Loop through each exiting product attribute
    foreach( $attributes as $attribute ){
        // Get the correct taxonomy for product attributes
        $taxonomy = 'pa_'.$attribute->attribute_name;
        $attribute_id = $attribute->attribute_id;

        // Get all term Ids values for the current product attribute (array)
        $term_ids = get_terms(array('taxonomy' => $taxonomy, 'fields' => 'ids'));

        // Get an empty instance of the WC_Product_Attribute object
        $product_attribute = new WC_Product_Attribute();

        // Set the related data in the WC_Product_Attribute object
        $product_attribute->set_id( $attribute_id );
        $product_attribute->set_name( $taxonomy );
        $product_attribute->set_options( $term_ids );
        $product_attribute->set_position( $position );
        $product_attribute->set_visible( "1" );
        $product_attribute->set_variation( "1" );

        // Add the product WC_Product_Attribute object in the data array
        $data[$taxonomy] = $product_attribute;

        $position++; // Incrementing position
    }
    // Set the array of WC_Product_Attribute objects in the product
    $product->set_attributes( $data );
    $product->save(); // Save the product

    //create conditions
    $terms = get_terms(array('taxonomy' => 'pa_condition'));
    $variations_ids = $product->get_children();
    $current_conditions = [];
    foreach ($variations_ids as $variation_id) {
        $var_prod = wc_get_product( $variation_id );
        $current_variations[] = $var_prod->attributes['pa_condition'];
    }

    foreach ($terms as $term) {
        if (is_array($current_variations) && in_array($term->slug, $current_variations)) {
            continue;
        }
        $variation = new WC_Product_Variation();
        $variation->set_parent_id($post_id);
        $variation->set_attributes(array(
            'attribute_pa_condition' => $term->slug,
        ));
        $variation->save();
    }
    error_log(print_r($current_variations, true), 3, "/home/grrehder/Documents/guides/debug.log");
    return true;
}

/**
* Rewrite order Metaboxes on Order
*/
if( !function_exists( 'adq_add_meta_boxes' ) ) {
    function adq_add_meta_boxes() {
        add_meta_box('quote-box', 'Variation Options', 'add_variations_quote_metabox_content', 'product', 'side', 'default');
    }
    add_action( 'add_meta_boxes', 'adq_add_meta_boxes' , 25 );
}

function check_request_add_cnd_variations( $post_id, $post ) {
    error_log("aee\n", 3, "/home/grrehder/Documents/guides/debug.log");
    if( isset( $_REQUEST["add_cnd_variations"] ) ) {
        //so this doesn't become a save loop
        remove_action( 'woocommerce_new_product', 'check_request_add_cnd_variations', 100);
        remove_action( 'woocommerce_update_product', 'check_request_add_cnd_variations', 100);
        return add_cnd_variations($post_id);
    }
}
add_action( 'woocommerce_new_product', 'check_request_add_cnd_variations', 100, 2 );
add_action( 'woocommerce_update_product', 'check_request_add_cnd_variations', 100, 2 );

function add_variations_quote_metabox_content($post) {
    $product = wc_get_product( $post->ID );
    $p_type = $product->get_type();
    if ($p_type != 'variable') {
    ?>
    <ul class="variation-actions">
        <li class="wide">
            <span class="add_cnd_variations">Set product type as "Variable" to see options</span>
        </li>
    </ul>
    <?php
    } else {
    ?>
    <ul class="variation-actions">
        <li class="wide">
            <input type="submit" value="Add Conditions Attributes and Variations" name="add_cnd_variations" class="button add_cnd_variations button-primary">
        </li>
    </ul>
    <?php
    }
}


//$current_language = get_locale();


//add category form --

add_filter( 'shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter', 10, 3 );

function custom_shortcode_atts_wpcf7_filter( $out, $pairs, $atts ) {
    $my_attr = 'brand-name';
    if ( isset( $atts[$my_attr] ) ) {
        $out[$my_attr] = $atts[$my_attr];
        
    }
    $my_attr = 'brand-name1';
    if ( isset( $atts[$my_attr] ) ) {
        $out[$my_attr] = $atts[$my_attr];
    }
    $my_attr = 'subject-line';
    if ( isset( $atts[$my_attr] ) ) {
        $out[$my_attr] = $atts[$my_attr];
    }
    return $out;
}

function add_post_featured_image_as_rss_item_enclosure() {
	if ( ! has_post_thumbnail() )
		return;

	$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
	$thumbnail = image_get_intermediate_size($thumbnail_id);

	if ( empty( $thumbnail ) )
        return;
	
    $upload_dir = wp_upload_dir();
	
	printf( 
	    '<enclosure url="%s" type="%s" length="%s" />',
		$thumbnail['url'], 
		strtoupper(str_replace("image/", "", get_post_mime_type( $thumbnail_id ))),
        filesize(path_join($upload_dir['basedir'], $thumbnail['path']))
	);
}

add_action( 'rss2_item', 'add_post_featured_image_as_rss_item_enclosure' );

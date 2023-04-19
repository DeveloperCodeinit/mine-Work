<?php
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
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'hello-elementor','hello-elementor','hello-elementor-theme-style' ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 99 );

// END ENQUEUE PARENT ACTION


/* ----- custom work ----- */


// Shortcode for get category grid
function get_cat_with_parameter($attr){
    $args = shortcode_atts( array(
     
            'count' => 6,
            'orderby' => 'term_id',
            'order' => 'ASC'
 
        ), $attr );

    $taxonomy     = 'product_cat';
    $orderby      =  $args['orderby'];
    $show_count   = 0;      // 1 for yes, 0 for no
    $pad_counts   = 0;      // 1 for yes, 0 for no
    $hierarchical = 1;      // 1 for yes, 0 for no  
    $title        = '';  
    $empty        = 1;
    $order = $args['order'];
    $count = $args['count'];
    $col_width = 'col-lg-3';
    
    
    if($count == 6){
        $row_class = "custom_category_grid large_custom_category_grid";
    }else{
        $row_class = "custom_category_grid";
    }
    $args = array(
             'taxonomy'     => $taxonomy,
             'orderby'      => $orderby,
             'show_count'   => $show_count,
             'pad_counts'   => $pad_counts,
             'hierarchical' => $hierarchical,
             'title_li'     => $title,
             'hide_empty'   => $empty,
             'order' => $order,
    );
    $all_categories = get_categories( $args );
   
   $html = '<div class="row '.$row_class.'">';
   $i = 0;
    foreach ($all_categories as $key => $cat) {
        
        if($cat->category_parent == 0) {
            
            if($order == 'ASC'){
                if($count == 6){
                    if($key > 6){break;}
                       if($key == 3 || $key == 4){
                           $col_width = 'col-lg-5';
                        }else{
                           $col_width = 'col-lg-3';
                        }
                }else{
                    if($key > 3){break;}
                        if($key == 2){
                           $col_width = 'col-lg-5';
                        }else{
                           $col_width = 'col-lg-3';
                        }
                }
            }else{
                if($count == 6){
                    if($key > 5){break;}
                       if($key == 2 || $key == 3){
                           $col_width = 'col-lg-5';

                        }else{
                           $col_width = 'col-lg-3';
                        }
                }else{
                    if($key > 2){break;}
                        if($key == 1){
                           $col_width = 'col-lg-5';
                        }else{
                           $col_width = 'col-lg-3';
                        }
                }
            }
           
            $width = '';
            if($col_width == 'col-lg-5'){
                $width= 'width:47%';
            }
            if($col_width == 'col-lg-3'){
                $width= 'width:25%';
            }
            $category_id = $cat->term_id;   
            $thumbnail_id = get_woocommerce_term_meta( $category_id, 'thumbnail_id', true ); 
            $image = wp_get_attachment_url( $thumbnail_id );   

            if(empty($image)){
                $image = get_site_url()."/wp-content/uploads/2023/02/hero_ct.webp";
            }
            $html .= '<a  href="'. get_term_link($cat->slug, 'product_cat') .'">
                        <div class="'.$col_width.' cat_custom"  style="background-image: url('.$image.');background-size: cover;background-repeat: no-repeat;background-position: center;height:400px;margin:4px;position: relative;'.$width.'">
                                    <div class="cat_content_element cat_custom_content" >
                                           <h3 style="color: #ffffff;text-align: left;line-height: 0px;" class="vc_custom_heading">'. $cat->name .'</h3>
                                            <p style="color: #ffffff;">'. $cat->category_description .'</p>
                                    </div>
                                   <div class="overlay"></div>
                               </div>
                     </a>';
        }       
    }
    $html.= '</div>';
    return $html;
}
 
add_shortcode( 'get_category_grid' , 'get_cat_with_parameter' );



/* -- add limit stock msg -- */
add_action( 'woocommerce_single_product_summary', 'limited_stock_message', 21 );
function limited_stock_message() {
    global $product;

$sold = $product->get_total_sales();

//echo 'Number of units sold: ' . $sold;
    $stock = $product->get_stock_quantity();
    $total_stock = $stock-$sold;
    if ( $total_stock <= 10 && $stock >= 1 ) {
        echo '<p class="stock-limit-meassage" style="color:red;"><span><img src="https://cdn.shopify.com/s/files/1/0704/8665/5296/t/2/assets/fire.png?v=14384662478358297281673290671" width="20" height="20"></span>' . sprintf( __( 'Hurry! Only %s left in stock!', 'woocommerce' ), $total_stock ) . '</p>';
    }
}


// /* -- add text after product price -- */

add_filter( 'woocommerce_get_price_html', 'add_product_price_text', 10, 2 );
function add_product_price_text( $price, $product ) {
    if ( is_product() && $product->is_type( 'simple' ) ) {
        $text = '<span class="price_content" style="color:#1f2b6b;">(Tax included. Shipping calculated at checkout)</span>';
        $price .= ' ' . $text;
    }
    return $price;
}

/* -- code for shipping charges -- */
add_filter('woocommerce_package_rates', 'hide_flat_rate_when_free_shipping_available', 10, 2);
function hide_flat_rate_when_free_shipping_available($rates, $package) {
    $cart_total = WC()->cart->subtotal; // Get cart subtotal
    $min_order_amount = 200; // Minimum order amount for free shipping
    $free_shipping_available = false; // Initialize free shipping availability flag
    
    // Check if free shipping method is available
    foreach ($rates as $rate_id => $rate) {
        if ($rate->method_id === 'free_shipping') {
            $free_shipping_available = true;
            break;
        }
    }
    
    // Hide flat rate method if free shipping is available and cart total is greater than or equal to minimum order amount
    if ($free_shipping_available && $cart_total >= $min_order_amount) {
        foreach ($rates as $rate_id => $rate) {
            if ($rate->method_id === 'flat_rate') {
                unset($rates[$rate_id]);
            }
        }
    }
    
    return $rates;
}



/*  -- chck text of checkout btn  --  */

function woocommerce_button_proceed_to_checkout() {
    
      $new_checkout_url = WC()->cart->get_checkout_url();
       ?>
     <a href="<?php echo $new_checkout_url; ?>" class="checkout-button button alt wc-forward"> 
       
       <?php _e( 'Checkout Securely', 'woocommerce' ); ?>
   </a> 
<?php
}



/*  -- form for custom brand filter  --  */
function werocket_filter_form() {

    global $wpdb;
    $category = get_queried_object();
    $category_name = $category->name;
   

    ?>
    <h3 class="widget-title">Filter by Brand</h3>
    <?php  if(is_product_category()){ 

     $results = $wpdb->get_results($wpdb->prepare("SELECT tm.term_id AS brand_id, ts.name AS brand_name FROM `wp_termmeta` AS tm 
    LEFT JOIN wp_terms AS ts ON tm.term_id= ts.term_id WHERE tm.`meta_value` LIKE '%%%s%%'", $category_name));
    
        ?>
    <form method="get" class="brand_filter_form">
        
        <!-- <label for="werocket_brand">Filter by Werocket Brand:</label> -->
        <select name="brand">
            <option value="">All Brands</option>
            <?php foreach ( $results as $term ) : 
               $terms_brand = get_term_by('term_id', $term->brand_id, 'berocket_brand'); 
                ?>
                <option value="<?php echo esc_attr( $terms_brand->slug ); ?>"<?php if(isset($_GET['brand'] ) && $_GET['brand'] == $terms_brand->slug){ echo "selected"; }?>><?php echo esc_html( $terms_brand->name ); ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <input type="submit" value="Filter" style="float: right;">
    </form>
    <?php
    }else{ 
            
        $terms = get_terms( array(
            'taxonomy' => 'berocket_brand',
            'hide_empty' => false,
        ) );

    ?>
       <ul class="product-brands toggle-block treeview-list treeview widget_product_categories">

        <?php foreach ( $terms as $term ) : ?>
            <li class="cat-item cat-item-130 current-cat"><a href="<?php echo site_url().'/brands/'.esc_attr( $term->slug ); ?>"><?php echo esc_html( $term->name ); ?></a></li>
        <?php endforeach; ?>
        
       </ul>
   <?php }
}
add_shortcode( 'werocket_filter_form' , 'werocket_filter_form' );



/*  -- custom brand filter  --  */
function werocket_filter_products( $query ) {
    if ( ! is_admin() && $query->is_main_query() && is_product_taxonomy() ) {
        $werocket_brand = isset( $_GET['brand'] ) ? sanitize_text_field( $_GET['brand'] ) : '';

        if ( ! empty( $werocket_brand ) ) {
            $query->set( 'tax_query', array(
                array(
                    'taxonomy' => 'berocket_brand',
                    'field' => 'slug',
                    'terms' => $werocket_brand,
                ),
            ) );
        }
    }
}
add_action( 'pre_get_posts', 'werocket_filter_products' );


/* -- add order number in serial -- */
add_filter( 'woocommerce_order_number', 'custom_woocommerce_order_number' );

function custom_woocommerce_order_number( $order_id ) {
    $order = wc_get_order( $order_id );
    $prefix = 'ORDER'; // You can change the prefix to anything you like.
    $new_order_id = sprintf( '%04d', $order_id );
    return $prefix . $new_order_id;
}
if ( function_exists('register_sidebar') ) 
register_sidebar(array(
    'name' => 'Sidebar',
    'before_widget' => '<div class = "widget">',
    'after_widget' => '</div>',
    'before_title' => '<h3>',
    'after_title' => '</h3>',
    )
);


function hello_theme_add_custom_widget_to_sidebar() {
    if ( is_shop() || is_product_category() || is_tax('berocket_brand') || is_cart()) {
        ?>
        <div class="sidebar_shop">
     <?php   dynamic_sidebar( 'sidebar' ); ?>
       </div>
     <?php
    }
}
add_action( 'woocommerce_sidebar', 'hello_theme_add_custom_widget_to_sidebar' );



function view_wishlist_shortcode() {
	  if ( class_exists( 'YITH_WCWL' ) ) {
        $wishlist_count = yith_wcwl_count_all_products();
        //echo '<span class="wishlist-count">' . $wishlist_count . '</span>';
      }
	$button_text = __('', 'woocommerce');
	$icon_class = 'fa fa-heart-o';

	$html = '<a href="'.site_url().'/wishlist" class="button wc-forward">';
	$html .= $button_text . ' <i class="' . $icon_class . '" aria-hidden="true"></i>';
	$html .= '<span class="wishlist-count">' . $wishlist_count . '</span>';
	$html .= '</a>';

	return $html;
}
add_shortcode( 'view_wishlist', 'view_wishlist_shortcode' );


function menu_filter_shortcode(){  ?>
    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/shop' ) ); ?>">
        <label>
            <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Search Products', 'placeholder', 'textdomain' ); ?>" value="<?php echo get_search_query(); ?>" name="product_search" required/>
            <input type="hidden" name="post_type" value="product" />
        </label>
       <?php wp_dropdown_categories( array(
            'show_option_none' => __( 'All Categories', 'textdomain' ),
            'taxonomy'         => 'product_cat',
            'name'             => 'product_cat',
            'orderby'          => 'name',
            'hierarchical'     => true,
            'depth'            => 3,
            'selected'         => (isset($_GET['product_cat'])) ? $_GET['product_cat'] : '',
            'value_field'      => 'term_id',
            'class'            => 'form-control',
            'option_none_value' => '', // set the first option value as empty or null
        ) ); ?>
        <button type="submit" class="search-submit"><?php echo _x( '<i class="fa fa-search"></i>', 'submit button', 'textdomain' ); ?></button>
    </form>
 
<?php }

add_shortcode( 'menu_filter', 'menu_filter_shortcode' );

add_action( 'pre_get_posts', 'custom_shop_filters' );
function custom_shop_filters( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }
    if ( is_shop() ) {
        $meta_query = array();
        if ( isset( $_GET['product_name'] ) && ! empty( $_GET['product_name'] ) ) {
            $meta_query[] = array(
                'key' => '_name',
                'value' => sanitize_text_field( $_GET['product_name'] ),
                'compare' => 'LIKE'
            );
        }
        if ( isset( $_GET['product_cat'] ) && ! empty( $_GET['product_cat'] ) ) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => intval( $_GET['product_cat'] )
            );
            $query->set( 'tax_query', $tax_query );
        }
        if ( isset( $_GET['product_search'] ) && ! empty( $_GET['product_search'] ) ) {
            $query->set( 's', sanitize_text_field( $_GET['product_search'] ) );
            $query->set( 'posts_per_page', 9 );
        }
        if ( ! empty( $meta_query ) ) {
            $query->set( 'meta_query', $meta_query );
        }
    }
}

/* -- checkout sidebar -- */

function payment_content_shortcode() {
	  

	$html = '<div class="payment-content" >
        <div class="security-bg">
        <div class="payment-detail">
        <div class="payment-title">Payment  Security</div>
        <div class="payment-subtitle">Payment methods</div>
        <ul class="payment-icons">
        <li><img class="alignnone size-medium wp-image-8634" src="'.site_url().'/wp-content/uploads/2023/04/discover.png.webp" alt="" width="100" height="50" /></li>
        <li><img class="alignnone size-medium wp-image-8629" src="'.site_url().'/wp-content/uploads/2023/04/visa.png.webp" alt="" width="100" height="50" /></li>
        <li><img class="alignnone size-medium wp-image-8630" src="'.site_url().'/wp-content/uploads/2023/04/dinners-club.png" alt="" width="100" height="50" /></li>
        <li><img class="alignnone size-full wp-image-8631" src="'.site_url().'/wp-content/uploads/2023/04/amex-2.png.webp" alt="" width="100" height="50" /></li>
        <li><img class="alignnone size-medium wp-image-8632" src="'.site_url().'/wp-content/uploads/2023/04/applepay.png.webp" alt="" width="100" height="50" /></li>
        <li><img class="alignnone size-medium wp-image-8633" src="'.site_url().'/wp-content/uploads/2023/04/paypal.png.webp" alt="" width="100" height="50" /></li>
        <li><img class="alignnone size-full wp-image-8628" src="'.site_url().'/wp-content/uploads/2023/04/mastercard.png.webp" alt="" width="100" height="50" /></li>
        </ul>
        <div class="payment-desc">Your payment information is processed securely. We do not store credit card details nor have access to your credit card information.</div>
        </div>
        <div class="security-text">
        <div class="payment-sec-cnt col-sm-12">
        <p><img src="//cdn.shopify.com/s/files/1/0704/8665/5296/files/01_50x50.png?v=1673618848" width="50" height="50" /></p>
        <div class="security-content">
        <div class="security-title">FREE SHIPPING</div>
        <div class="security-subtitle">United Kingdom</div>
        </div>
        </div>
        <div class="payment-sec-cnt col-sm-12">
        <p><img src="//cdn.shopify.com/s/files/1/0704/8665/5296/files/02_50x50.png?v=1673618848" width="50" height="50" /></p>
        <div class="security-content">
        <div class="security-title">BIG SAVING ON</div>
        <div class="security-subtitle">WEEKENDS</div>
        </div>
        </div>
        <div class="payment-sec-cnt col-sm-12">
        <p><img src="//cdn.shopify.com/s/files/1/0704/8665/5296/files/03_50x50.png?v=1673618848" width="50" height="50" /></p>
        <div class="security-content">
        <div class="security-title">24H SUPPORT</div>
        <div class="security-subtitle">CONTACT US</div>
        </div>
        </div>
        </div>
        </div>
        </div>';

	return $html;
}
add_shortcode( 'payment_content', 'payment_content_shortcode' );


add_action( 'woocommerce_after_shop_loop_item_title', 'show_product_name', 5 );

function show_product_name() {
   global $product;
   echo '<h2 class="woocommerce-loop-product__title">' . substr( $product->get_name(), 0, 30 ) . '...</h2>';
  // echo '<a href="' . esc_url( $product->get_permalink() ) . '" class="button add_to_cart_button">' . esc_html( $product->add_to_cart_text() ) . '</a>';
}
add_action( 'wp', 'remove_titles_breadcrumbs' );
function remove_titles_breadcrumbs() {
    if ( ! is_front_page() ) {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
        remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
    }
}



// Add EAN and MPN fields to product data
add_action( 'woocommerce_product_options_sku', 'add_product_ean_mpn_fields' );
function add_product_ean_mpn_fields() {
    global $woocommerce, $post;

    echo '<div class="options_group">';

    // EAN field
    woocommerce_wp_text_input( array(
        'id'                => '_ean',
        'label'             => __( 'EAN', 'woocommerce' ),
        'placeholder'       => '',
        'description'       => __( 'Enter the EAN for this product.', 'woocommerce' ),
        'desc_tip'          => 'true',
        'value'             => get_post_meta( $post->ID, '_ean', true ),
        'custom_attributes' => array(
            'maxlength' => 13,
            'pattern'   => '[0-9]{13}',
        ),
    ) );

    // MPN field
    woocommerce_wp_text_input( array(
        'id'                => '_mpn',
        'label'             => __( 'MPN', 'woocommerce' ),
        'placeholder'       => '',
        'description'       => __( 'Enter the MPN for this product.', 'woocommerce' ),
        'desc_tip'          => 'true',
        'value'             => get_post_meta( $post->ID, '_mpn', true ),
        'custom_attributes' => array(
            'maxlength' => 50,
        ),
    ) );

    echo '</div>';
}

// Save EAN and MPN fields to product meta
add_action( 'woocommerce_process_product_meta', 'save_product_ean_mpn_fields' );
function save_product_ean_mpn_fields( $post_id ) {
    $ean = $_POST['_ean'];
    if( ! empty( $ean ) ) {
        update_post_meta( $post_id, '_ean', esc_attr( $ean ) );
    }

    $mpn = $_POST['_mpn'];
    if( ! empty( $mpn ) ) {
        update_post_meta( $post_id, '_mpn', esc_attr( $mpn ) );
    }
}
// Display EAN and MPN on product page
add_action( 'woocommerce_product_meta_start', 'display_product_ean_mpn', 25 );
function display_product_ean_mpn() {
    global $product;

    $ean = $product->get_meta( '_ean' );
    if( ! empty( $ean ) ) {
        echo '<div class="product-meta"><strong>EAN:</strong> ' . esc_html( $ean ) . '</div>';
    }

    $mpn = $product->get_meta( '_mpn' );
    if( ! empty( $mpn ) ) {
        echo '<div class="product-meta"><strong>MPN:</strong> ' . esc_html( $mpn ) . '</div>';
    }
}


?>

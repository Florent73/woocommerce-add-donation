<?php
/*
Plugin Name: WooCommerce Add Donation
Plugin URI: http://www.restezconnectes.fr/ajouter-un-don-dans-votre-panier-woocommerce/
Description: Le plugin WooCommerce Add Donation vous permet de proposer aux clients de votre boutique WooCommerce la possibilité de faire un don.
Author: Florent Maillefaud
Author URI: http://www.restezconnectes.fr/
Version: 0.1
*/

/*
Change Log
22/12/2014 - Création du Plugin
*/

class woo_add_don {
	
	function __construct(){
		global $wpdb;
		        
        /* Traduction */
        load_plugin_textdomain('wooadddonation', false, dirname( plugin_basename( __FILE__ ) ).'/languages');
        
        /* Ajoute la version dans les options */
        define('WOOADDON_VERSION', '0.1');
        $option['woocommerce-add-donation'] = WOOADDON_VERSION;
        if( !get_option('woocommerce-add-donation_version') ) {
            add_option('woocommerce-add-donation_version', $option);
        } else if ( get_option('woocommerce-add-donation_version') != WOOADDON_VERSION ) {
            update_option('woocommerce-add-donation_version', WOOADDON_VERSION);
        }
        /* Ajout le lien dans l'admin*/
		add_action('admin_menu',array(&$this,'my_admin_menu'));
        
        /* Entre les options par defaut */
        $wooaddon_AdminOptions = array(
        'color_bg' => "#f1f1f1",
        'class_btn' => 'button btn btn-primary',
        'btn_text' => __('Make a donation', 'wooadddonation')
        );
        $getWooAddonSettings = get_option('woocommerce-add-donation_settings');
        if (!empty($getWooAddonSettings)) {
            foreach ($getWooAddonSettings as $key => $option) {
                $wooaddon_AdminOptions[$key] = $option;
            }
        }
        update_option('woocommerce-add-donation_settings', $wooaddon_AdminOptions);
        
        /* Charge les scripts nécessaire*/
        wp_register_script('wooaddon-admin-settings', WP_PLUGIN_URL.'/woocommerce-add-donation/woocommerce-add-donation-scripts.js');
        wp_enqueue_script('wooaddon-admin-settings');	
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'my-script-handle', plugins_url('woocommerce-add-donation-color.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
        
	}

	function my_admin_menu() {
        add_submenu_page( 'woocommerce', __('Add Donation Setting', 'wooadddonation'), __('Add Donation Setting','wooadddonation'), 'manage_options', 'woocommerce-donation', array(
				&$this,
				'homepage'
			));
	}

	function homepage(){ // Update parameters - Mise à jour des paramètres
		$this->install();
		global $wpdb;
		
		/* Update des paramètres */
        if($_POST['action'] == 'update') {
            update_option('woocommerce-add-donation_id', $_POST["woocommerce-add-donation_id"]);
            update_option('woocommerce-add-donation_settings', $_POST["woocommerce-add-donation_settings"]);
            $options_saved = true;
            echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.', 'wooadddonation').'</strong></p></div>';
        }	
        ?>
		
            <!-- TABS OPTIONS -->
            <div id="icon-options-general" class="icon32"><br></div>
            <h2 class="nav-tab-wrapper">
                <a id="wooaddon-menu-general" class="nav-tab nav-tab-active" href="#general" onfocus="this.blur();"><?php _e('General', 'wooadddonation'); ?></a>
                <a id="wooaddon-menu-css" class="nav-tab" href="#css" onfocus="this.blur();"><?php _e('CSS', 'wooadddonation'); ?></a>
                <a id="wooaddon-menu-setup" class="nav-tab" href="#setup" onfocus="this.blur();"><?php _e('Setup', 'wooadddonation'); ?></a>
                <a id="wooaddon-menu-apropos" class="nav-tab" href="#apropos" onfocus="this.blur();"><?php _e('About', 'wooadddonation'); ?></a>
            </h2>
		
		<?php $this->main_form(); 
	}
	
	function main_form(){// display parameters form - Affiche le formulaire de paramètres
		
        global $wpdb;
        // Récupère l'id 
        $getID = get_option('woocommerce-add-donation_id');
        if(get_option('woocommerce-add-donation_settings')) { extract(get_option('woocommerce-add-donation_settings')); }
        $paramMMode = get_option('woocommerce-add-donation_settings');
        ?>
        
		<table class="widefat fixed">
			<tbody>
                <tr>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="update" />

                            <!-- GENERAL -->
                            <div class="wooaddon-menu-general wooaddon-menu-group">
                                <div id="wooaddon-opt-general"  >
                                     <ul>
                                        <li>
                                            <h3><?php _e('ID of donation product:', 'wooadddonation'); ?></h3>
                                            <input type="text" size="6" name="woocommerce-add-donation_id" value="<?php echo stripslashes($getID); ?>" />
                                        </li>
                                        <li>&nbsp;</li>
                                         <li>
                                            <h3><?php _e('Button text:', 'wooadddonation'); ?></h3>
                                             <input type="text" size="16" name="woocommerce-add-donation_settings[btn_text]" value="<?php echo $paramMMode['btn_text']; ?>" />
                                        </li>
                                        <li>&nbsp;</li>
                                        <input type="submit" value="Sauvegarder" >
                                    </ul>
                                </div>
                            </div>
                            <!-- fin options 1 -->

                            <!-- STYLE CSS -->
                            <div class="wooaddon-menu-css wooaddon-menu-group" style="display: none;">
                                <div id="wooaddon-opt-css"  >
                                     <ul>
                                        <li>
                                            <em><?php _e('Class CSS button:', 'wooadddonation'); ?></em><br />
                                                <input type="text" size="36" name="woocommerce-add-donation_settings[class_btn]" value="<?php echo $paramMMode['class_btn']; ?>" />
                                            
                                        </li>
                                        <li>&nbsp;</li>
                                        <li>
                                            <em><?php _e('Background color:', 'wooadddonation'); ?></em><br />
                                            <input type="text" value="<?php echo $paramMMode['color_bg']; ?>" name="woocommerce-add-donation_settings[color_bg]" class="wooaddon-color-field" data-default-color="#f1f1f1" />
                                        </li>
                                        <li>&nbsp;</li>
                                        <input type="submit" value="Sauvegarder" >
                                    </ul>
                                </div>
                            </div>
                            <!-- fin options 2 -->

                            <!-- STYLE SETUP -->
                            <div class="wooaddon-menu-setup wooaddon-menu-group" style="display: none;">
                                <div id="wooaddon-opt-setup"  >
                                     <ul>
                                        <li>
                                            <?php _e('If you do not know how to add a unlisted product:', 'wooadddonation') ; ?><br /><br />
                                            <?php _e('1. Goto Products > Add Product', 'wooadddonation'); ?><br />
                                            <?php _e('2. Set the title <b>"Donation"</b>', 'wooadddonation'); ?><br />
                                            <?php _e('3. On the right hand side above the “Publish” button labelled <b>"catalogue visibility"</b>, choose <b>Hidden</b>', 'wooadddonation'); ?><br />
                                            <?php _e('4. In the Product data section set the <b>Product SKU</b> to something unique e.g. <b>"checkout-donation"</b>, under shipping set the <b>shipping class</b> to <b>"no shipping"</b>, if you have <b>taxes enabled</b> under taxes', 'wooadddonation'); ?><br />
                                            <?php _e('5. select "none" and just in case set the tax class to <b>zero-rate</b>.', 'wooadddonation'); ?><br />
                                            <?php _e('6. Finally under the product data section, in the inventory tab, make sure sold individually is checked, as this will mean that only one donation is allowed in the shopping basket at once.', 'wooadddonation'); ?><br />

                                        </li>
                                        <li>&nbsp;</li>
                                    </ul>
                                </div>
                            </div>
                            <!-- fin options 2 -->
                            
                            <!-- Onglet options 3 -->
                            <div class="wooaddon-menu-apropos wooaddon-menu-group" style="display: none;">
                                <div id="wooaddon-opt-apropos"  >
                                     <ul>

                                        <li>
                                            <?php _e('This plugin has been developed for you for free by <a href="http://www.restezconnectes.fr" target="_blank">Florent Maillefaud</a> from the source code of James Collings (<a href="http://jamescollings.co.uk/" target="_blank">http://jamescollings.co.uk</a>). It is royalty free, you can take it, modify it, distribute it as you see fit. <br /> <br />It would be desirable that I can get feedback on your potential changes to improve this plugin for all.', 'wooadddonation'); ?>
                                        </li>
                                        <li>&nbsp;</li>
                                        <li>
                                            <!-- FAIRE UN DON SUR PAYPAL -->
                                            <div><?php _e('If you want Donate (French Paypal) for my current and future developments:', 'wooadddonation'); ?><br />
                                                <form target="paypal" action="https://www.paypal.com/cgi-bin/webscr" method="post">
                                                    <input type="hidden" name="cmd" value="_s-xclick">
                                                    <input type="hidden" name="hosted_button_id" value="EARH3UKA2S3LW">
                                                    <table>
                                                    <tr><td><input type="hidden" name="on0" value="Don"><?php _e('Make me a gift:', 'wooadddonation'); ?></td></tr><tr><td><select name="os0">
                                                        <option value="Symbolique de">Symbolique de €4,00 EUR</option>
                                                        <option value="Sympa de">Sympa de €10,00 EUR</option>
                                                        <option value="Généreux de">Généreux de €15,00 EUR</option>
                                                    </select> </td></tr>
                                                    </table>
                                                    <input type="hidden" name="currency_code" value="EUR">
                                                    <input type="image" src="https://www.paypalobjects.com/fr_FR/FR/i/btn/btn_cart_LG.gif" border="0" name="submit" alt="PayPal - la solution de paiement en ligne la plus simple et la plus sécurisée !">
                                                    <img alt="" border="0" src="https://www.paypalobjects.com/fr_FR/i/scr/pixel.gif" width="1" height="1">
                                                </form>

                                            </div>
                                            <!-- FIN FAIRE UN DON -->
                                        </li>
                                        <li>&nbsp;</li>
                                    </ul>
                                </div>
                            </div>
                            <!-- fin options 3 -->

                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
        
<?php
	}

	function install() {
		global $wpdb;
		if(get_option('woo_add_don_init')){
			//return;
		}
		
	}

}

//Use from plugin manager
function wooadddon_init() {
	$tw = new woo_add_don;
}
add_action('plugins_loaded','wooadddon_init');

if( !function_exists('wooadddon_currency_symbol') ) {
    function wooadddon_currency_symbol( $currency = '' ) {

        if ( $currency ) {

            switch ( $currency ) {
                case 'BRL' :
                    $currency_symbol = '&#82;&#36;';
                    break;
                case 'AUD' :
                case 'CAD' :
                case 'MXN' :
                case 'NZD' :
                case 'HKD' :
                case 'SGD' :
                case 'USD' :
                    $currency_symbol = '&#36;';
                    break;
                case 'EUR' :
                    $currency_symbol = '&euro;';
                    break;
                case 'CNY' :
                case 'RMB' :
                case 'JPY' :
                    $currency_symbol = '&yen;';
                    break;
                case 'RUB' :
                    $currency_symbol = '&#1088;&#1091;&#1073;.';
                    break;
                case 'KRW' : $currency_symbol = '&#8361;'; break;
                case 'TRY' : $currency_symbol = '&#84;&#76;'; break;
                case 'NOK' : $currency_symbol = '&#107;&#114;'; break;
                case 'ZAR' : $currency_symbol = '&#82;'; break;
                case 'CZK' : $currency_symbol = '&#75;&#269;'; break;
                case 'MYR' : $currency_symbol = '&#82;&#77;'; break;
                case 'DKK' : $currency_symbol = '&#107;&#114;'; break;
                case 'HUF' : $currency_symbol = '&#70;&#116;'; break;
                case 'IDR' : $currency_symbol = 'Rp'; break;
                case 'INR' : $currency_symbol = '&#8377;'; break;
                case 'ILS' : $currency_symbol = '&#8362;'; break;
                case 'PHP' : $currency_symbol = '&#8369;'; break;
                case 'PLN' : $currency_symbol = '&#122;&#322;'; break;
                case 'SEK' : $currency_symbol = '&#107;&#114;'; break;
                case 'CHF' : $currency_symbol = '&#67;&#72;&#70;'; break;
                case 'TWD' : $currency_symbol = '&#78;&#84;&#36;'; break;
                case 'THB' : $currency_symbol = '&#3647;'; break;
                case 'GBP' : $currency_symbol = '&pound;'; break;
                case 'RON' : $currency_symbol = 'lei'; break;
                default    : $currency_symbol = ''; break;
            }
        }

        return $currency_symbol;
    }
}

/* 
 * TEST DONATION 
 * http://jamescollings.co.uk/blog/woocommerce-shopping-cart-donation/
*/
define('DONATE_ID', get_option('woocommerce-add-donation_id')); // set to the id of your previously created donation product

if( !function_exists('wooadddon_donation_exist') ) {
    function wooadddon_donation_exist(){

        global $woocommerce;

        if( sizeof($woocommerce->cart->get_cart()) > 0){

            foreach($woocommerce->cart->get_cart() as $cart_item_key => $values){

                $_product = $values['data'];

                if($_product->id == DONATE_ID)
                    return true;
            }
        }
        return false;
    }
}

if( !function_exists('wooadddon_round_donation') ) {
    function wooadddon_round_donation($total, $value = 10){

         $donation = (ceil($total / $value) * $value) - $total;
         return $donation;
    }
}
add_action('woocommerce_cart_contents','wooadddon_woocommerce_after_cart_table');

if( !function_exists('wooadddon_woocommerce_after_cart_table') ) {
    function wooadddon_woocommerce_after_cart_table(){

        global $woocommerce;
        global $post;
        //$product = wc_get_product( $_product->id );

        $args = array(
                'post_type' => 'product',
                'ID' => $_product->id,
                'posts_per_page' => 1
                );
        $loop = new WP_Query( $args );

        if(get_option('woocommerce-add-donation_settings')) { extract(get_option('woocommerce-add-donation_settings')); }
        $paramMMode = get_option('woocommerce-add-donation_settings');

        //print_r($product);
        $donate = isset($woocommerce->session->wooadddon_donation) ? floatval($woocommerce->session->wooadddon_donation) : 0;

        if(!wooadddon_donation_exist()){
            unset($woocommerce->session->wooadddon_donation);
        }

        // uncomment the next line of code if you wish to round up the order total with the donation e.g. £53 = £7 donation
        // $donate = wooadddon_round_donation($woocommerce->cart->total );

        if(!wooadddon_donation_exist() && $loop->have_posts()){

            echo '<tr class="cart_table_item" style="background-color: '.$paramMMode["color_bg"].';">';

            while ( $loop->have_posts() ) : $loop->the_post();

                if( has_post_thumbnail() ) {
                    echo '<td data-title="Thumbnail" class="text-left product-thumbnail"><div class="image "><a href="#">'.get_the_post_thumbnail($_product->id, array(120,120), array('class' => "attachment-shop_thumbnail")).'</a></div></td>';
                }
                echo '<td data-title="Produit" class="product-name text-left" colspan="2" style="padding:10px;"><h5>'.get_the_title().'</h5>'.get_the_content() .'</td>';
                echo '<td data-title="Remove" class="product-remove text-right"><input type="text" size="4" style="width: 50px;" name="wooadddon_donation" placeholder="'.wooadddon_currency_symbol(get_option('woocommerce_currency')).'" /></td> ';
                echo '<td data-title="Prix" class="product-price text-right" colspan="2"> <input type="submit" name="donate-btn" class="'.$paramMMode['class_btn'].'" value="'.$paramMMode['btn_text'].'"/></td>';
                echo '<td data-title="Total" class="product-subtotal text-right"></td>';

            endwhile;

            echo '</tr>';

        }
    }
}
// capture form data and add basket item
add_action('init','wooadddon_process_donation');
if( !function_exists('wooadddon_process_donation') ) {
    function wooadddon_process_donation(){

        global $woocommerce;

        $donation = isset($_POST['wooadddon_donation']) && !empty($_POST['wooadddon_donation']) ? floatval($_POST['wooadddon_donation']) : false;

        if($donation && isset($_POST['donate-btn'])){

            // add item to basket
            $found = false;

            // add to session
            if($donation >= 0){
                $woocommerce->session->wooadddon_donation = $donation;

                //check if product already in cart
                if( sizeof($woocommerce->cart->get_cart()) > 0){

                    foreach($woocommerce->cart->get_cart() as $cart_item_key=>$values){

                        $_product = $values['data'];

                        if($_product->id == DONATE_ID)
                            $found = true;
                    }

                    // if product not found, add it
                    if(!$found)
                        $woocommerce->cart->add_to_cart(DONATE_ID);
                }else{
                    // if no products in cart, add it
                    $woocommerce->cart->add_to_cart(DONATE_ID);
                }
            }
        }
    }
}

add_filter('woocommerce_get_price', 'wooadddon_get_price',10,2);
if( !function_exists('wooadddon_get_price') ) {
    function wooadddon_get_price($price, $product){

        global $woocommerce;

        if($product->id == DONATE_ID){
            return isset($woocommerce->session->wooadddon_donation) ? floatval($woocommerce->session->wooadddon_donation) : 0;

        }
        return $price;
    }
}


?>

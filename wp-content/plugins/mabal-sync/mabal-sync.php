<?php
/*
 * Plugin Name: Mabal Sync
 * Plugin URI: https://zumlex.com/
 * Description: Mabal Sync Plugin for WooCommerce
 * Author: Zumlex
 * Author URI: https://zumlex.com/
 * Version: 1.1.0
 */

class MabalSync
{
    public $Username = "dengun";
    public $Password = "A9LTbAe6qG";
    public $HostId = "501344756";
    
    public $TABELA_ERP = "BO";
    public $SERIEDOC = "29";
    public $NOMEDOC = "Encomenda de Web Site";
    public $CLIENTE = "1";
    
    function __construct() {
        
        add_action('admin_menu', [$this, 'z_settings_menu']);
        
        //add_action( 'woocommerce_payment_complete', [$this, 'ad_woocommerce_payment_complete'], 10, 1 );
        add_action( 'woocommerce_order_status_completed', [$this, 'ad_woocommerce_order_status_completed'], 10, 1 );
        
        add_action('init', [$this, 'es_test']);
        //add_filter( 'http_request_timeout', [$this, 'z_timeout_extend'] );
        
    }
    
    function z_timeout_extend($time) {
        return 0;
    }
    
    function es_test() {
        $order_id = 8931;
        
        if (@$_GET['_sibtest'])
        {
            $this->sendShippingInfo( $order_id );
            exit;
        }
        
    }
    
    function z_settings_menu() {
        add_submenu_page(
		'options-general.php',
		'Mabal Sync',
		'Mabal Sync',
		'manage_options',
		'z-maba-sync',
		[$this, 'z_maba_settings_callback'] 
        );
    }
    
    function z_maba_settings_callback() {
        include('views/manual_sync.php');
    }
    
    function is_product_saved_already($reference) {
        
        $args = array(
            'post_type'  => 'product',
            'meta_query' => array(
                array(
                    'key'     => 'mabal_reference',
                    'value'   => $reference,
                    'compare' => '=',
                ),
            )
        );
        
        $exists = get_posts($args);
        
        if (count($exists)) return true;
        
        return false;
        
    }
    
    function saveSimpleProduct($mabal_product, $length, $width)
    {

        
        $reference = (string)$mabal_product->REFERENCIA;
        
        
        if (!$this->is_product_saved_already($reference))
        {
            $title = $mabal_product->DESIGNACAO;
            $unit = $mabal_product->UNIDADE;
            $conversion_factor = $mabal_product->FATOR_CONVERSAO;
            $gaw = $mabal_product->VAIWWW;

            $family_code = $mabal_product->CODIGO_FAMILIA;
            $family_name = $mabal_product->NOME_FAMILIA;

            $produto_informacao_de_envio = (string)$mabal_product->TIPO_PARA_DESCONTOS;
            $produto_link_ficha_tecnica = (string)$mabal_product->FICHATEC;
            

            $brand = (string)$mabal_product->USR1;
            $tag1 = (string)$mabal_product->USR3;
            $tag2 = (string)$mabal_product->USR4;
            
            $tags = [];
            if (!empty($tag1)) $tags[] = $tag1;
            if (!empty($tag2)) $tags[] = $tag2;
            
            $usr5 = $mabal_product->USR5;

            $blocked = $mabal_product->BLOQUEADO;

            $image_path = $mabal_product->PATH_IMAGEM;
            $vat = $mabal_product->CODIGO_TABELA_IVA;

            //$price = $mabal_product->TAXA_IVA;
            $price = $mabal_product->PRECO_VENDA4;

            $stock = (int)$mabal_product->EXISTENCIA;
            $amount_cativated = $mabal_product->QUANT_CATIVADA;
            $amount_reception = $mabal_product->QUANT_RECEPCAO;
            $amount_user = $mabal_product->QUANT_UTILIZADOR;
            $amount_enc_supply = $mabal_product->QUANT_ENC_FORN;
            $amount_enc_clie = $mabal_product->QUANT_ENC_CLIE;
            $sale_price1 = $mabal_product->PRECO_VENDA1;
            $vat1_included = $mabal_product->IVA_INCLUIDO_PV1;
            $sale_price2 = $mabal_product->PRECO_VENDA2;
            $vat2_included = $mabal_product->IVA_INCLUIDO_PV2;
            $sale_price3 = $mabal_product->PRECO_VENDA3;
            $vat3_included = $mabal_product->IVA_INCLUIDO_PV3;
            
            $vat4_included = $mabal_product->IVA_INCLUIDO_PV4;
            $sale_price5 = $mabal_product->PRECO_VENDA5;
            $vat5_included = $mabal_product->IVA_INCLUIDO_PV5;
            $has_ecovalue = $mabal_product->TEM_ECOVALOR;
            $eco_value_rate = $mabal_product->TAXA_ECOVALOR;
            $service = $mabal_product->SERVICO;
            $open_date = $mabal_product->DATA_ABERTURA;
            $supplier_number = $mabal_product->NUMERO_FORNECEDOR;
            $supplier_stab = $mabal_product->ESTAB_FORNECEDOR;
            $supplier_name = $mabal_product->NOME_FORNECEDOR;

            $image = (string)$mabal_product->HTTPIMG1;
            $image2 = (string)$mabal_product->HTTPIMG2;
            $image3 = (string)$mabal_product->HTTPIMG3;
            
            $description = (string)$mabal_product->STOBS;
            $name = $mabal_product->DESISITE;
            $date_created = $mabal_product->DATAHORA_CRIACAO;
            $last_sale_price = $mabal_product->ULTIMO_PRECO_VENDA;

            $product = new WC_Product_Simple();
            $product->set_name( $title );
            $product->set_slug( sanitize_title($title) );
            $product->set_regular_price( $price );
            $product->set_short_description( $description );
            $product->set_description( $description );

            if ($image)
            {
                $attachment_id = $this->z_upload_file_by_url($image);
                if ($attachment_id)
                {
                    $product->set_image_id( $attachment_id );
                }
            }
            
            $gallery_images = [];
            
            if ($image2)
            {
                $attachment_id2 = $this->z_upload_file_by_url($image2);
                if ($attachment_id2)
                {
                    $gallery_images[] = $attachment_id2;
                }
            }
            
            if ($image3)
            {
                $attachment_id3 = $this->z_upload_file_by_url($image3);
                if ($attachment_id3)
                {
                    $gallery_images[] = $attachment_id3;
                }
            }
            
            if (count($gallery_images))
            {
                $product->set_gallery_image_ids( $gallery_images );
            }
            
            
            $product->set_sku( strtolower($reference) );
            
            # Stock related;
            # not needed for now
            //if ($stock)
            if (false)
            {
                $product->set_stock_status( 'instock' );
                $product->set_manage_stock( true );
                $product->set_stock_quantity( $stock );
                //$product->set_backorders( 'no' );
                //$prodict->set_low_stock_amount( 2 );
            }
            
            $product->set_sold_individually( true );
            
            # product dimensions;
            //$product->set_weight( 0.5 );
            $product->set_length( $length );
            $product->set_width( $width );
            //$product->set_height( 30 );
            
            # Tax related stuff;
            //$item->set_tax_class('class_one');

            $product->save();

            $product_id = $product->get_id();
            add_post_meta($product_id, "mabal_reference", $reference);
            
            //SET THE PRODUCT CATEGORIES
            if (!empty($family_name))
            {
                wp_set_object_terms($product_id, array($family_name), 'product_cat');
            }
            
            //SET THE PRODUCT TAGS
            if (count($tags))
            {
                wp_set_object_terms($product_id, $tags, 'product_tag');
            }

            # Custom fields;
            update_post_meta($product_id, "produto_informacao_de_envio", $produto_informacao_de_envio);
            update_post_meta($product_id, "produto_link_ficha_tecnica", $produto_link_ficha_tecnica);
            
            
            echo "Product added with SKU: {$reference}<br>";
        }
        else
        {
            echo "Product is already added with SKU: {$reference}<br>";
        }
        
    }
    
    function z_upload_file_by_url( $image_url ) {
        
        //$temp_image_url = strtolower($image_url);

        $args = array(
            //'timeout'     => 0,
            'sslverify' => false,
            'headers' => array(
                'Accept' =>  'application/xml',
                'Authorization' => 'Basic ' . base64_encode( $this->Username . ':' . $this->Password )
            )
        );
        $objResponse = wp_remote_get( $image_url, $args );

        $response = $objResponse['body'];
        
        $txtFileName = time() . ".jpg";
        file_put_contents("../wp-content/uploads/" . $txtFileName, $response);
        $file = home_url() . "/wp-content/uploads/" . $txtFileName;

        $wp_filetype = wp_check_filetype( $txtFileName, null );

        $attachment = array(
          'post_mime_type' => $wp_filetype['type'],
          'post_title' => sanitize_file_name( $txtFileName ),
          'post_content' => '',
          'post_status' => 'inherit'
        );

        $attach_id = wp_insert_attachment( $attachment, $file );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
        wp_update_attachment_metadata( $attach_id, $attach_data );
        
        return $attach_id;

    }
    
    function MabalCheckProductWeightAndSize($reference) {
        
        $url = 'https://mabalgarve.dyndns.org:5342/phc/rest/'.$this->HostId.'/colorsandsizes?product=' . $reference;

        $args = array(
            'sslverify' => false,
            'headers' => array(
                'Accept' =>  'application/xml',
                'Authorization' => 'Basic ' . base64_encode( $MabalSync->Username . ':' . $MabalSync->Password )
            )
        );
        $objResponse = wp_remote_get( $url, $args );

        return $objResponse['body'];
    }
    
    function ad_woocommerce_order_status_completed($order_id) {
        
        $this->sendShippingInfo($order_id);
    }
    
    function sendShippingInfo( $order_id ) {

        $order              = wc_get_order( $order_id );
        
  //      $user_id            = $order->get_user_id();
        $billing_address    = $order->get_address( 'billing' );

        $shipping_address   = $order->get_address( 'shipping' );

//        $user_meta          = get_user_meta($user_id);
        $order_meta         = get_post_meta($order_id);
        $discount_codes     = $order->get_coupon_codes();
        $coupon_amount      = 0;
        $total_no_discount  = 0;

        if (count($discount_codes))
        {
            foreach( $order->get_coupon_codes() as $coupon_code ) 
            {
                // Get the WC_Coupon object
                $coupon = new WC_Coupon($coupon_code);
                //$discount_type = $coupon->get_discount_type(); // Get coupon discount type
                $coupon_amount = $coupon->get_amount(); // Get coupon amount
            }
            $total_no_discount = count( $discount_codes );
        }


        $item_quantity = 0;
        foreach ($order->get_items() as $intItemId => $objItem)
        {
            $product            = $objItem->get_product();
            $product_name       = $product->get_title();
            $txtSku             = $product->get_sku();
            $msntype            = wc_get_order_item_meta( $intItemId, "msntype", true );
            $item_quantity     += $objItem->get_quantity();
        }
        
        $store_address     = get_option( 'woocommerce_store_address' );
        $store_address_2   = get_option( 'woocommerce_store_address_2' );
        $store_city        = get_option( 'woocommerce_store_city' );
        $store_postcode    = get_option( 'woocommerce_store_postcode' );
        $store_raw_country = get_option( 'woocommerce_default_country' );

        $order_total_tax      = $order->get_total_tax();

        $xml_data = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<DOCUMENTO xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
                    . '<TABELA_ERP>'.$this->TABELA_ERP.'</TABELA_ERP>'
                    . '<SERIEDOC>'.$this->SERIEDOC.'</SERIEDOC>'
                    . '<NOMEDOC>'.$this->NOMEDOC.'</NOMEDOC>'
                    . '<CLIENTE>'.$this->CLIENTE.'</CLIENTE>'
                    . '<NOME>'.$billing_address['first_name'].'</NOME>'
                    . '<NOME2>'.$billing_address['last_name'].'</NOME2>'
                    . '<MORADA>'.$billing_address['address_1'].'</MORADA>'
                    . '<LOCALIDADE>'.$billing_address['billing_city'].'</LOCALIDADE>'
                    . '<CODIGO_POSTAL>'.$billing_address['billing_postcode'].'</CODIGO_POSTAL>'
                    . '<EMISSAO>'.$order->order_date.'</EMISSAO>'
                    . '<VENCIMENTO>'.$order->order_date.'</VENCIMENTO>'
                    . '<TOTAL_ILIQUIDO>'.$order_meta['_order_total'][0].'</TOTAL_ILIQUIDO>'
                    . '<TOTAL_DESCONTO_COMERCIAL>'.$order_meta['_order_total'][0].'</TOTAL_DESCONTO_COMERCIAL>'
                    . '<TOTAL_INCIDENCIA>'.$order_meta['_order_total'][0].'</TOTAL_INCIDENCIA>'
                    . '<TOTAL_SEM_DESCONTO_FINANCEIRO>'.$total_no_discount.'</TOTAL_SEM_DESCONTO_FINANCEIRO>'
                    . '<TOTAL_DESCONTO_FINANCEIRO>'.$coupon_amount.'</TOTAL_DESCONTO_FINANCEIRO>'
                    . '<TOTAL_POR_REGULARIZAR>0</TOTAL_POR_REGULARIZAR>'
                    . '<QUANTIDADE_TOTAL>'.$item_quantity.'</QUANTIDADE_TOTAL>'
                    . '<LOCAL_CARGA>'.$store_address.'</LOCAL_CARGA>'
                    . '<MORADA_CARGA>'.$store_address.'</MORADA_CARGA>'
                    . '<LOCALIDADE_CARGA>'.$store_city.'</LOCALIDADE_CARGA>'
                    . '<CODIGO_POSTAL_CARGA>'.$store_postcode.'</CODIGO_POSTAL_CARGA>'
                    . '<LOCAL_DESCARGA>'.$shipping_address['address_1'].'</LOCAL_DESCARGA>'
                    . '<MORADA_DESCARGA>'.$shipping_address['address_1'].'</MORADA_DESCARGA>'
                    . '<LOCALIDADE_DESCARGA>'.$shipping_address['shipping_city'].'</LOCALIDADE_DESCARGA>'
                    . '<CODIGO_POSTAL_DESCARGA>'.$shipping_address['shipping_postcode'].'</CODIGO_POSTAL_DESCARGA>'
                    . '<UTILIZADOR_CRIACAO>'.$billing_address['first_name'].' '.$billing_address['last_name'].'</UTILIZADOR_CRIACAO>'

                    . '<CONTRIBUINTE>'.$order_total_tax.'</CONTRIBUINTE>'
                    . '<TAXAIVA>'.$order_total_tax.'</TAXAIVA>'
                    . '<DESIGNACAO>'.$product_name.'</DESIGNACAO>'
                    . '<CODIGO_PAIS>'.$store_raw_country.'</CODIGO_PAIS>'

                    
                    
                    . '<LINHA></LINHA>'
                . '</DOCUMENTO>';        

        $url = "https://mabalgarve.dyndns.org:5342/phc/rest/". $this->HostId ."/customer/document";

        $args = array(
            'sslverify' => false,
            'headers' => array(
                'Accept' =>  'application/xml',
                'Content-Type' => 'application/xml',
                'Authorization' => 'Basic ' . base64_encode( $this->Username . ':' . $this->Password )
            ),
            "InputXml"=>$xml_data,
            "body" => $xml_data
        );
        $objResponse = wp_remote_post( $url, $args );
        $response = $objResponse['body'];

        write_log("shipping details response: " . json_encode($objResponse) );
        wp_mail("qahmed@zumlex.com", "MABAL shipping output V2", json_encode($objResponse) );
    }
}

global $MabalSync;
$MabalSync = new MabalSync();

if (!function_exists('p_r')){function p_r($s){echo "<pre>";print_r($s);echo "</pre>";}}
if (!function_exists('write_log')){ function write_log ( $log )  { if ( is_array( $log ) || is_object( $log ) ) { error_log( print_r( $log, true ) ); } else { error_log( $log ); }}}
if (!function_exists('clean')){ function clean($string) { $string = str_replace([' ','-'], ['',''], $string); return preg_replace('/[^A-Za-z0-9\-]/', '', $string); } }
<?php
/*
 * Plugin Name: Mabal Sync
 * Plugin URI: https://zumlex.com/
 * Description: Mabal Sync Plugin for WooCommerce
 * Author: Zumlex
 * Author URI: https://zumlex.com/
 * Version: 1.0.0
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
        
    }
    
    function es_test() {
        $order_id = 1965;
        
        if (@$_GET['_sibtest'])
        {
echo "test here" exit;

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
            
            if ($image2)
            {
                $attachment_id2 = $this->z_upload_file_by_url($image2);
                if ($attachment_id2)
                {
                    $gallery_images[] = $attachment_id2;
                }
            }
            
            if (count($gallery_images))
            {
                $product->set_gallery_image_ids( $gallery_images );
            }
            
            
            $product->set_sku( strtolower($reference) );
            
            # Stock related;
            if ($stock)
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
            if (!empty($brand))
            {
                wp_set_object_terms($product_id, array($brand), 'product_cat');
            }
            
            //SET THE PRODUCT TAGS
            if (count($tags))
            {
                wp_set_object_terms($product_id, $tags, 'product_tag');
            }
            
            
            echo "Product added with SKU: {$reference}<br>";
        }
        else
        {
            echo "Product is already added with SKU: {$reference}<br>";
        }
        
    }
    
    function z_upload_file_by_url( $image_url ) {
        
        $temp_image_url = strtolower($image_url);
        $temp_image_url = str_replace(['http://', 'https://'], ['',''], $temp_image_url);
        //$image_url = "https://{$this->Username}:{$this->Password}@{$temp_image_url}";
        
        //$image_url = "https://dengun:A9LTbAe6qG@mabalgarve.dyndns.org:5342/phc/rest/501344756/file/image/jnf/phc/in.20.awc/in.20.awc.jpg";
        $image_url = "https://developers.whmcs.com/img/logo.png";
        
        $upload_dir = wp_upload_dir();

        $image_data = file_get_contents( $image_url );

        //$filename = basename( $image_url );
        
        
        $filename = basename( $image_url );
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = time() . "." . $ext;

        if ( wp_mkdir_p( $upload_dir['path'] ) ) {
          $file = $upload_dir['path'] . '/' . $filename;
        }
        else {
          $file = $upload_dir['basedir'] . '/' . $filename;
        }

        file_put_contents( $file, $image_data );

        $wp_filetype = wp_check_filetype( $filename, null );

        $attachment = array(
          'post_mime_type' => $wp_filetype['type'],
          'post_title' => sanitize_file_name( $filename ),
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
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://mabalgarve.dyndns.org:5342/phc/rest/'.$this->HostId.'/colorsandsizes?product=' . $reference,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/xml',
            'Authorization: Basic ZGVuZ3VuOkE5TFRiQWU2cUc='
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $response = '<?xml version="1.0" encoding="utf-8"?><Response xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"><Result><GRELHAS><GRELHA><REF>00012</REF><CORES><COR><_CustomFields><CustomField><id>imagem</id><value /></CustomField><CustomField><id>vaiwww</id><value>0</value></CustomField><CustomField><id>HTTPIMG</id><value /></CustomField></_CustomFields><CODIGO>Geral</CODIGO><TAMANHOS><TAMANHO><_CustomFields><CustomField><id>vaiwww</id><value>0</value></CustomField><CustomField><id>compmin</id><value>0.000</value></CustomField><CustomField><id>compmax</id><value>0.000</value></CustomField><CustomField><id>largmin</id><value>0.000</value></CustomField><CustomField><id>largmax</id><value>0.000</value></CustomField></_CustomFields><CODIGO>Geral</CODIGO><CBARRAS /><EXISTENCIA_COR_TAMANHO>26.000</EXISTENCIA_COR_TAMANHO><PRECO_VENDA1>0.000000</PRECO_VENDA1><PRECO_VENDA2>0.000000</PRECO_VENDA2><PRECO_VENDA3>0.000000</PRECO_VENDA3><PRECO_VENDA4>0.000000</PRECO_VENDA4><PRECO_VENDA5>0.000000</PRECO_VENDA5><STOCKS><STOCK><COR_LORDEM>0</COR_LORDEM><TAMANHO_LORDEM>0</TAMANHO_LORDEM><EXISTENCIA_COR_TAMANHO>26.000</EXISTENCIA_COR_TAMANHO><ARMAZEM>1</ARMAZEM><ORDEM>0</ORDEM><EXISTENCIA>26.000</EXISTENCIA><QUANT_ENC_CLIE>0.000</QUANT_ENC_CLIE><QUANT_ENC_FORN>0.000</QUANT_ENC_FORN><QUANT_RECEPCAO>0.000</QUANT_RECEPCAO><QUANT_CATIVADA>0.000</QUANT_CATIVADA><BLOQUEADO_SAIDA>0</BLOQUEADO_SAIDA><BLOQUEADO_ENTRADA>0</BLOQUEADO_ENTRADA><PRECO_VENDA1>0.000000</PRECO_VENDA1><PRECO_VENDA2>0.000000</PRECO_VENDA2><PRECO_VENDA3>0.000000</PRECO_VENDA3><PRECO_VENDA4>0.000000</PRECO_VENDA4><PRECO_VENDA5>0.000000</PRECO_VENDA5><PONTO_ENCOMENDA>0.000</PONTO_ENCOMENDA><STOCK_OPTIMO>0.000</STOCK_OPTIMO><STOCK_MINIMO>0.000</STOCK_MINIMO><CONSUMO_MEDIO>0.000</CONSUMO_MEDIO><QUANTIDADE_OPTIMA_ENC>0.000</QUANTIDADE_OPTIMA_ENC><USRDATA>2023-02-17T00:00:00</USRDATA><USRHORA>12:38:59</USRHORA></STOCK><STOCK><COR_LORDEM>0</COR_LORDEM><TAMANHO_LORDEM>0</TAMANHO_LORDEM><EXISTENCIA_COR_TAMANHO>0.000</EXISTENCIA_COR_TAMANHO><ARMAZEM>2</ARMAZEM><ORDEM>0</ORDEM><EXISTENCIA>0.000</EXISTENCIA><QUANT_ENC_CLIE>0.000</QUANT_ENC_CLIE><QUANT_ENC_FORN>0.000</QUANT_ENC_FORN><QUANT_RECEPCAO>0.000</QUANT_RECEPCAO><QUANT_CATIVADA>0.000</QUANT_CATIVADA><BLOQUEADO_SAIDA>0</BLOQUEADO_SAIDA><BLOQUEADO_ENTRADA>0</BLOQUEADO_ENTRADA><PRECO_VENDA1>0.000000</PRECO_VENDA1><PRECO_VENDA2>0.000000</PRECO_VENDA2><PRECO_VENDA3>0.000000</PRECO_VENDA3><PRECO_VENDA4>0.000000</PRECO_VENDA4><PRECO_VENDA5>0.000000</PRECO_VENDA5><PONTO_ENCOMENDA>0.000</PONTO_ENCOMENDA><STOCK_OPTIMO>0.000</STOCK_OPTIMO><STOCK_MINIMO>0.000</STOCK_MINIMO><CONSUMO_MEDIO>0.000</CONSUMO_MEDIO><QUANTIDADE_OPTIMA_ENC>0.000</QUANTIDADE_OPTIMA_ENC><USRDATA>1900-01-01T00:00:00</USRDATA><USRHORA>00:00:00</USRHORA></STOCK></STOCKS></TAMANHO></TAMANHOS></COR></CORES></GRELHA></GRELHAS></Result><ErpStatusCode>OK</ErpStatusCode><Status>OK</Status></Response>';
        
        return $response;
    }
    
    function ad_woocommerce_order_status_completed($order_id) {
        
        $this->sendShippingInfo($order_id);
    }
    
    function sendShippingInfo( $order_id ) {
        
        $order              = wc_get_order( $order_id );
        $user_id            = $order->get_user_id();
        $billing_address    = $order->get_address( 'billing' );
        $shipping_address   = $order->get_address( 'shipping' );
        $user_meta          = get_user_meta($user_id);
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
            $txtSku             = $product->get_sku();
            $msntype            = wc_get_order_item_meta( $intItemId, "msntype", true );
            $item_quantity     += $objItem->get_quantity();
        }
        
        $store_address     = get_option( 'woocommerce_store_address' );
        $store_address_2   = get_option( 'woocommerce_store_address_2' );
        $store_city        = get_option( 'woocommerce_store_city' );
        $store_postcode    = get_option( 'woocommerce_store_postcode' );
        $store_raw_country = get_option( 'woocommerce_default_country' );
        

        $xml_data = '<?xml version="1.0" encoding="UTF-8"?>'
                . '<DOCUMENTOS>'
                . '<DOCUMENTO>'
                    . '<TABELA_ERP>'.$this->TABELA_ERP.'</TABELA_ERP>'
                    . '<SERIEDOC>'.$this->SERIEDOC.'</SERIEDOC>'
                    . '<NOMEDOC>'.$this->NOMEDOC.'</NOMEDOC>'
                    . '<CLIENTE>'.$this->CLIENTE.'</CLIENTE>'
                    . '<NOME>'.$billing_address['first_name'].'</NOME>'
                    . '<NOME2>'.$billing_address['last_name'].'</NOME2>'
                    . '<ESTABELECIMENTO>STORE</ESTABELECIMENTO>'
                    . '<MORADA>'.$billing_address['address_1'].'</MORADA>'
                    . '<LOCALIDADE>'.$billing_address['billing_city'].'</LOCALIDADE>'
                    . '<CODIGO_POSTAL>'.$billing_address['billing_postcode'].'</CODIGO_POSTAL>'
                    . '<EMISSAO>'.$order->order_date.'</EMISSAO>'
                    . '<VENCIMENTO>'.$order->order_date.'</VENCIMENTO>'
                    . '<CONDICOES_PAGAMENTO></CONDICOES_PAGAMENTO>'
                    . '<TOTAL_ILIQUIDO>'.$order_meta['_order_total'][0].'</TOTAL_ILIQUIDO>'
                    . '<TOTAL_DESCONTO_COMERCIAL>'.$order_meta['_order_total'][0].'</TOTAL_DESCONTO_COMERCIAL>'
                    . '<TOTAL_INCIDENCIA>'.$order_meta['_order_total'][0].'</TOTAL_INCIDENCIA>'
                    . '<TOTAL_IVA></TOTAL_INCIDENCIA>'
                    . '<TOTAL_SEM_DESCONTO_FINANCEIRO>'.$total_no_discount.'</TOTAL_SEM_DESCONTO_FINANCEIRO>'
                    . '<TOTAL_DESCONTO_FINANCEIRO>'.$coupon_amount.'</TOTAL_DESCONTO_FINANCEIRO>'
                    . '<TOTAL_DOCUMENTO></TOTAL_DOCUMENTO>'
                    . '<TOTAL_POR_REGULARIZAR></TOTAL_POR_REGULARIZAR>'
                    . '<QUANTIDADE_TOTAL>'.$item_quantity.'</QUANTIDADE_TOTAL>'
                    . '<ZONA></ZONA>'
                    . '<SEGMENTO></SEGMENTO>'
                    . '<CODIGO_VENDEDOR></CODIGO_VENDEDOR>'
                    . '<NOME_VENDEDOR></NOME_VENDEDOR>'
                    . '<CENTRO_CUSTO></CENTRO_CUSTO>'
                    . '<DATAHORA_CARGA></DATAHORA_CARGA>'
                    . '<LOCAL_CARGA></LOCAL_CARGA>'
                    . '<DATAHORA_CARGA></DATAHORA_CARGA>'
                    . '<LOCAL_CARGA>'+$store_address+'</LOCAL_CARGA>'
                    . '<MORADA_CARGA>'+$store_address+'</MORADA_CARGA>'
                    . '<LOCALIDADE_CARGA>'+$store_city+'</LOCALIDADE_CARGA>'
                    . '<CODIGO_POSTAL_CARGA>'+$store_postcode+'</CODIGO_POSTAL_CARGA>'
                    . '<LOCAL_DESCARGA>'+$shipping_address['address_1']+'</LOCAL_DESCARGA>'
                    . '<MORADA_DESCARGA>'+$shipping_address['address_1']+'</MORADA_DESCARGA>'
                    . '<LOCALIDADE_DESCARGA>'+$shipping_address['shipping_city']+'</LOCALIDADE_DESCARGA>'
                    . '<CODIGO_POSTAL_DESCARGA>'+$shipping_address['shipping_postcode']+'</CODIGO_POSTAL_DESCARGA>'
                    . '<TOTAL_MODO_PAGAMENTO_CHEQUE></TOTAL_MODO_PAGAMENTO_CHEQUE>'
                    . '<BANCO_MODO_PAGAMENTO_CHEQUE></BANCO_MODO_PAGAMENTO_CHEQUE>'
                    . '<DATA_MODO_PAGAMENTO_CHEQUE></DATA_MODO_PAGAMENTO_CHEQUE>'
                    . '<NUMERO_MODO_PAGAMENTO_CHEQUE></NUMERO_MODO_PAGAMENTO_CHEQUE>'
                    . '<TIPO_MODO_PAGAMENTO_CHEQUE></TIPO_MODO_PAGAMENTO_CHEQUE>'
                    . '<TIPO_INTERNO></TIPO_INTERNO>'
                    . '<MERCADO></MERCADO>'
                    . '<REFERENCIA_INTERNA></REFERENCIA_INTERNA>'
                    . '<COBRADOR></COBRADOR>'
                    . '<CODIGO_PAIS></CODIGO_PAIS>'
                    . '<DESCRITIVO_PAIS></DESCRITIVO_PAIS>'
                    . '<DATAHORA_CRIACAO></DATAHORA_CRIACAO>'
                    . '<DATAHORA_ALTERACAO></DATAHORA_ALTERACAO>'
                    . '<UTILIZADOR_CRIACAO>'.$billing_address['first_name'].' '.$billing_address['last_name'].'</UTILIZADOR_CRIACAO>'
                    . '<UTILIZADOR_ALTERACAO></UTILIZADOR_ALTERACAO>'
                    . '<TIPODOC_SAFT></TIPODOC_SAFT>'
                    . '<ATCODEID></ATCODEID>'
                    . '<NUMERO_SOFTWARE_CERTIFICADO></NUMERO_SOFTWARE_CERTIFICADO>'
                    . '<ASSINATURA></ASSINATURA>'
                    . '<VERSAO_CHAVE_ASSINATURA></VERSAO_CHAVE_ASSINATURA>'
                    . '<CODIGO_ISENCAO_IMPOSTO></CODIGO_ISENCAO_IMPOSTO>'
                    . '<DESCRICAO_ISENCAO_IMPOSTO></DESCRICAO_ISENCAO_IMPOSTO>'
                    . '<TOTAL_ECOVALOR></TOTAL_ECOVALOR>'
                    . '<TOTAL_MODO_PAGAMENTO_DINHEIRO></TOTAL_MODO_PAGAMENTO_DINHEIRO>'
                    . '<TOTAL_TROCO></TOTAL_TROCO>'
                . '</DOCUMENTO>'
                . '</DOCUMENTOS>';
        
        
        $ch = curl_init("https://mabalgarve.dyndns.org:5342/phc/rest/{$this->HostId}/customer/document");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml; charset=utf-8',
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        
        p_r($output);
    }
}

global $MabalSync;
$MabalSync = new MabalSync();

if (!function_exists('p_r')){function p_r($s){echo "<pre>";print_r($s);echo "</pre>";}}
if (!function_exists('write_log')){ function write_log ( $log )  { if ( is_array( $log ) || is_object( $log ) ) { error_log( print_r( $log, true ) ); } else { error_log( $log ); }}}
if (!function_exists('clean')){ function clean($string) { $string = str_replace([' ','-'], ['',''], $string); return preg_replace('/[^A-Za-z0-9\-]/', '', $string); } }
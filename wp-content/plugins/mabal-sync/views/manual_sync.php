<div class="wrap">
    <h1>Manual Sync</h1>
    
    
    <br><br>
            
            <form action="" method="post">
                <input type="date" name="since" /> <input type="submit" value="Fetch Products" />
            </form>
    
    <?php
    
    //$rates = WC_Tax::get_rates();
    
    
    
    if ($_POST['since'])
    {
        
        global $MabalSync;
        $url = "https://mabalgarve.dyndns.org:5342/phc/rest/". $MabalSync->HostId ."/products?modified-since=" . $_POST['since'] . "T00:00:00&format=xml";
    

        $args = array(
            'sslverify' => false,
            'headers' => array(
                'Accept' =>  'application/xml',
                'Authorization' => 'Basic ' . base64_encode( $MabalSync->Username . ':' . $MabalSync->Password )
            )
        );
        $objResponse = wp_remote_get( $url, $args );

        $response = $objResponse['body'];

        $xml = simplexml_load_string($response);
        if ($xml === false) 
        {
            echo "Failed loading XML: ";
            foreach(libxml_get_errors() as $error) 
            {
                echo "<br>", $error->message;
            }
        } 
        else 
        {

            $products = $xml->Result->ARTIGOS->ARTIGO;
            $loop = 1;
            foreach ($products as $product)
            {
                
                if ($loop > 10) break;
                
                $reference = (string)$product->REFERENCIA;

                $response = $MabalSync->MabalCheckProductWeightAndSize( $reference );
                $xml = simplexml_load_string($response);
                
                $length = $width = '';
                
                $sizes = $xml->Result->GRELHAS->GRELHA->CORES->COR->TAMANHOS->TAMANHO->_CustomFields->CustomField;
                foreach ($sizes as $size)
                {
                    if ($size->id == "compmax")
                    {
                        $length = (float)$size->value;
                    }
                    
                    if ($size->id == "largmax")
                    {
                        $width = (float)$size->value;
                    }
                }
                
                $MabalSync->saveSimpleProduct($product, $length, $width);
                $loop++;

                echo "<hr>";
            }
        }
    }
    
    
    
    ?>
</div>
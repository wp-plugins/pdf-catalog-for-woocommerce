<?php           
if(!class_exists('mPDF')){
	include_once(dirname(__FILE__).'/pdf_data/mpdfxx/mpdf.php');
}

class PDFCatalog {
    public function display() {
        global $product, $woocommerce, $post;
        
        self::load_resources();
                
        if(!empty($_POST['createpdf'])){
            $page_num = self::createPDF(
                explode(',', $_POST['products_id']),
                $_POST['createpdf'],
                $_POST['page_num'],
                $_POST['pdf_length'],
                json_decode(htmlspecialchars_decode(str_replace('\\','',$_POST['post_data'])))
            );
            echo '<div class="pagenum">'.$page_num.'</div>';
            exit();
        }
        
        if(!empty($_POST['getpdf'])){
            if(!empty($_POST['pdfList'])){
                self::concatePDF($_POST['pdfList'], $_POST['key']+1);
            }else{
                self::concatePDF();
            }
            exit();
        }
        
        if(empty($_POST['template'])){
            $_SESSION['warning'] = __('You must select at least one product');
            wp_redirect();
        }
        
        if(is_file(dirname(__FILE__).'/pdf_data/templates/'.$_POST['template']."/settings.xml")){
            $xml_conf = simplexml_load_file(dirname(__FILE__).'/pdf_data/templates/'.$_POST['template']."/settings.xml");
            $product2page = $xml_conf->product2page['value'];
        }else{
            $product2page = 10;
        }
        
        $product2pdf = (floor(100 / $product2page)) * $product2page;
        
        $product_id = array();
        
        if (!empty($_POST['category_id'])) {
        	$categories = array();
        	foreach($_POST['category_id'] as $category_id){
        		$categories[] = (int)$category_id;
        	}
            $args = array( 
                'post_type' => 'product'
            );
            $args['tax_query'] = array(
                array(
                    'taxonomy'  => 'product_cat',
                    'field'     => 'id', 
                    'terms'     => $categories
                )
            );
            // Create the new query
        	$loop = new WP_Query($args);
        	
        	// Get products number
        	$product_count = $loop->post_count;
        	
        	// If results
        	if( $product_count > 0 ) :
        		
        			// Start the loop
        			while ( $loop->have_posts() ) : 
                    
                        $loop->the_post();
                                                
            			$product_id[] = $loop->post->ID;
        		
        			endwhile;
        	endif;
        }else{
            $_SESSION['warning'] = __('You must select at least one product');
            wp_redirect();
        }
        
        $products_id = json_encode(array_chunk($product_id, $product2pdf));
        $post_data = $_POST;
        unset($post_data['category_id'],$post_data['product_id']);
        $post_data = json_encode($post_data);
        $getpdf = 'ovologics/pdf&getpdf=1&token=';
        
        if (file_exists(dirname(__FILE__).'/pdf_list'))
        foreach (glob(dirname(__FILE__).'/pdf_list/*') as $file)
        unlink($file);
        
        $template = 'getpdf.php';
        
        require_once( PDF_CATALOG__PLUGIN_DIR . 'view/template/' . $template);
  	}
    
    
    private function createPDF($product_id, $pdf_num = 1, $page_num = 1, $pdf_length, $post_data){
    	
        	$products = array();
        	foreach($product_id as $product){
        		$products[] = (int)$product;
        	}
        global $product, $woocommerce, $post;
        
        $post_data = (array)$post_data;
        
        $logo = get_header_image();
        $store_name = get_bloginfo('name');
        $address = '';
        
        $orientation = '';
        $margin_top = 0;
        $margin_right = 0;
        $margin_bottom = 0;
        $margin_left = 0;
        $header = '';
        $footer = '';
        $fontdata = array();
        
        $args = array( 
            'post_type' => 'product',
            'post__in' => $products
        );
        
        $loop = new WP_Query( $args );
        
        ob_start();
            include_once dirname(__FILE__).'/pdf_data/templates/'.$post_data['template'].'/template.tpl';
            
            $html = ob_get_contents();

			if (!empty($orientation))
				$orientation = '-'.$orientation;
	
			define('FONTDATA', serialize($fontdata));
			define('TEMPLATE_TTFONTS', dirname(__file__).'/../../libraries/templates/'.$post_data['template'].'/fonts/');
            
            $mpdf = new mPDF('utf-8', 'A4'.$orientation, '', '',$margin_left,$margin_right,$margin_top,$margin_bottom,0,0);
            
            $mpdf->PageNumSubstitutions[] = array('from'=>(1), 'reset'=> $page_num, 'type'=>$page_num, 'suppress'=>false);
            
            $mpdf->SetDisplayMode('fullpage');
            
            $stylesheet = file_get_contents(dirname(__FILE__).'/pdf_data/templates/'.$post_data['template'].'/stylesheet.css');
            $mpdf->WriteHTML($stylesheet,1);
            $mpdf->SetHTMLHeader($header);
            $mpdf->SetHTMLFooter($footer);
            $mpdf->WriteHTML($html);
            
            $page_num = $mpdf->PageNo();
            
            $pdf = $mpdf->Output('', 'S');
            $ob = ob_get_contents(); 
        ob_end_clean();
        
        file_put_contents(dirname(__FILE__).'/pdf_list/page' . sprintf("%04s", $pdf_num) . '.pdf', $pdf);
        return $page_num;
    }
    
    private function concatePDF($pdfList = '', $key = 1){
        global $product, $woocommerce, $post;
        $i=1;
        $memory_limit = ini_get("memory_limit") * 0.6;
        $filesize = 0;
        $margin_top = 0;
        $margin_right = 0;
        $margin_bottom = 0;
        $margin_left = 0;
        $DownloadPath = PDF_CATALOG__PLUGIN_URL;
        $concatePdfPath = PDF_CATALOG__PLUGIN_DIR.'/concatepdf.php';
        $archive_path = '';
        $archiveName = 'PDFCatalog.zip';
        $fileName = 'PDFCatalog.pdf';
        $fileVersion = '?v='.time();
        $json = '';
        
        if(empty($pdfList)){
            $pdfs = scandir(dirname(__FILE__).'/pdf_list');
            unset($pdfs[0],$pdfs[1]);
            
            foreach($pdfs as $pdfi){
                $filesize += number_format(filesize(dirname(__FILE__).'/pdf_list/'.$pdfi) / 1048576, 2);
            }
        }
        
        if(empty($pdfList) && $filesize <= $memory_limit){
            ob_start();
            
            $html = ob_get_contents();
            
            $mpdf = new mPDF('utf-8'); 
            $mpdf->SetImportUse(); 
            foreach($pdfs as $fk=>$f){
                for ($i=1; $i<=$pagecount = $mpdf->SetSourceFile( dirname(__FILE__).'/pdf_list/'.$f ); $i++){
                    if($fk==1){
                        $tplId = $mpdf->ImportPage(1);
                    }
                    $tplId = $mpdf->ImportPage($i);
                    $pgw = $mpdf->tpls[$tplId]['w'];
                    $pgh = $mpdf->tpls[$tplId]['h'];
                    
                    if($pgw > $pgh){
                        $orientation = 'L';
                    }else{
                        $orientation = 'P';
                    }
                    
                    $mpdf->AddPage($orientation); 
                    $mpdf->UseTemplate($tplId); 
                    $mpdf->WriteHTML($html);
                    if($fk==1){
                        break;
                    }
                }
            }
            
            $pdf = $mpdf->Output('', 'S');
            $ob = ob_get_contents(); 
            ob_end_clean();
            
            file_put_contents(dirname(__FILE__).'/' . $fileName, $pdf);
            
            $json .= '<div class="link">'.$DownloadPath.$fileName.$fileVersion.'</div>';
        }else{
            if(empty($pdfList)){
                if(is_file($DownloadPath.$archiveName)){
                    unlink($DownloadPath.$archiveName);
                }
                
                $zip = new ZipArchive;
                $zip->open(dirname(__FILE__).'/'.$archiveName, ZIPARCHIVE::CREATE);
                $zip->addFromString('create', '');
                $zip->close();
                
                if(count($pdfs) > 1){
                    $pdfs = array_chunk($pdfs, floor(count($pdfs) / ($filesize / $memory_limit)));
                }
                
                $json .= '<div class="pdfList">'.$pdfs.'</div>';
            }else{
            
                if(count($pdfList) > 1){
                    $inputFilesList = implode(',', $pdfList);
                }else{
                    $inputFilesList = $pdfList;
                }
                $outputFileName = 'catalog'.$key.'.pdf';
                
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, HTTP_SERVER.$concatePdfPath);
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS,
                    array('outputFileName'=>urlencode($outputFileName), 
                          'archivePath'=>urlencode($archive_path.$archiveName),
                          'inputFilesList'=>$inputFilesList
                    )
                );
                $result = curl_exec($curl);
                if(!$result){
                    $error = curl_error($curl).'('.curl_errno($curl).')';
                    $json .= '<div class="status">'.$error.'</div>';
                }else{
                    $json .= '<div class="status">ok</div>';
                }
                curl_close($curl);
                
                $json .= '<div class="link">'.$DownloadPath.$archiveName.$fileVersion.'</div>';
                
            }
        }
        
        echo $json;
        exit();
    }
    
    private function utf8_substr($string, $offset, $length = null) {
    	// generates E_NOTICE
    	// for PHP4 objects, but not PHP5 objects
    	$string = (string)$string;
    	$offset = (int)$offset;
    	
    	if (!is_null($length)) {
    		$length = (int)$length;
    	}
    	
    	// handle trivial cases
    	if ($length === 0) {
    		return '';
    	}
    	
    	if ($offset < 0 && $length < 0 && $length < $offset) {
    		return '';
    	}
    	
    	// normalise negative offsets (we could use a tail
    	// anchored pattern, but they are horribly slow!)
    	if ($offset < 0) {
    		$strlen = strlen(utf8_decode($string));
    		$offset = $strlen + $offset;
    		
    		if ($offset < 0) {
    			$offset = 0;
    		}
    	}
    	
    	$Op = '';
    	$Lp = '';
    	
    	// establish a pattern for offset, a
    	// non-captured group equal in length to offset
    	if ($offset > 0) {
    		$Ox = (int)($offset / 65535);
    		$Oy = $offset%65535;
    		
    		if ($Ox) {
    			$Op = '(?:.{65535}){' . $Ox . '}';
    		}
    		
    		$Op = '^(?:' . $Op . '.{' . $Oy . '})';
    	} else {
    		$Op = '^';
    	}
    	
    	// establish a pattern for length
    	if (is_null($length)) {
    		$Lp = '(.*)$';
    	} else {
    		if (!isset($strlen)) {
    			$strlen = strlen(utf8_decode($string));
    		}
    		
    		// another trivial case
    		if ($offset > $strlen) {
    			return '';
    		}
    		
    		if ($length > 0) {
    			$length = min($strlen - $offset, $length);
    			
    			$Lx = (int)($length / 65535);
    			$Ly = $length % 65535;
    			
    			// negative length requires a captured group
    			// of length characters
    			if ($Lx) {
    				$Lp = '(?:.{65535}){' . $Lx . '}';
    			}
    			
    			$Lp = '(' . $Lp . '.{' . $Ly . '})';
    		} elseif ($length < 0) {
    			if ($length < ($offset - $strlen)) {
    				return '';
    			}
    			
    			$Lx = (int)((-$length) / 65535);
    			$Ly = (-$length)%65535;
    			
    			// negative length requires ... capture everything
    			// except a group of  -length characters
    			// anchored at the tail-end of the string
    			if ($Lx) {
    				$Lp = '(?:.{65535}){' . $Lx . '}';
    			}
    			
    			$Lp = '(.*)(?:' . $Lp . '.{' . $Ly . '})$';
    		}
    	}
    	
    	if (!preg_match( '#' . $Op . $Lp . '#us', $string, $match)) {
    		return '';
    	}
    	
    	return $match[1];
    	
    }

    private function load_resources() {
        global $woocommerce;
    	wp_register_style( 'getpdf.css', PDF_CATALOG__PLUGIN_URL . 'view/stylesheet/getpdf.css', array(), PDF_CATALOG_VERSION );
    	wp_enqueue_style( 'getpdf.css');
    	wp_register_style( 'jquery-ui-1.8.16.custom.css', PDF_CATALOG__PLUGIN_URL . 'view/stylesheet/ui/themes/ui-lightness/jquery-ui-1.8.16.custom.css', array(), PDF_CATALOG_VERSION );
    	wp_enqueue_style( 'jquery-ui-1.8.16.custom.css');
    
    	wp_register_script( 'jquery-ui-1.8.16.custom.min.js', PDF_CATALOG__PLUGIN_URL . 'view/js/jquery-ui-1.8.16.custom.min.js', array('jquery','postbox'), PDF_CATALOG__PLUGIN_URL );
    	wp_enqueue_script( 'jquery-ui-1.8.16.custom.min.js' );
    	wp_register_script( 'getpdf.js', PDF_CATALOG__PLUGIN_URL . 'view/js/getpdf.js', array('jquery','postbox'), PDF_CATALOG_VERSION );
    	wp_enqueue_script( 'getpdf.js' );
    }
}
?>
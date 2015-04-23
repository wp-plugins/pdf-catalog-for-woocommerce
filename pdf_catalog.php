<?php 
class PDFCatalog {
    public static function display() {
        /**
         * Check if WooCommerce is active
         **/
        if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            echo "Please install WooCommerce";
        }
        
        global $product, $woocommerce, $post;
        
        self::load_resources();
        
        $model = new PDFCatalogModel();
  	 
        $allowed = array('html');
         
        if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
         
            $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);
         
            if(!in_array(strtolower($extension), $allowed)){
                echo '{"status":"error"}';
                exit;
            }
         
            if(move_uploaded_file($_FILES['upl']['tmp_name'], dirname(__FILE__).'/settings.html')){
                echo '{"status":"success"}';
                exit;
            }
        }
  	 
        if(!empty($_POST['get_options'])){
            if(is_file(dirname(__FILE__).'/pdf_data/templates/'.$_POST['get_options'].'/options.html')){
                echo stripcslashes(file_get_contents(dirname(__FILE__).'/pdf_data/templates/'.$_POST['get_options'].'/options.html'));
            }
            exit();
        }
        
        if(!empty($_POST['set_options'])){
            if(is_file(dirname(__FILE__).'/pdf_data/templates/'.$_POST['template'].'/options.html')){
                file_put_contents(dirname(__FILE__).'/pdf_data/templates/'.$_POST['template'].'/options.html', htmlspecialchars_decode($_POST['set_options']));
            }
            exit();
        }
        
        if(!empty($_POST['fileDownload'])){
            header('Set-Cookie: fileDownload=true; path=/');
            header('Content-Disposition: attachment; filename=settings.html');
            echo file_get_contents(dirname(__FILE__).'/settings.html');
            exit();
        }
        
        if(!empty($_POST['set_settings'])){
            file_put_contents(dirname(__FILE__).'/settings.html', htmlspecialchars_decode($_POST['set_settings']));
            exit();
        }
        
        if(!empty($_POST['get_settings'])){
            echo stripcslashes(file_get_contents(dirname(__FILE__).'/settings.html'));
            exit();
        }
        
        if(!empty($_POST['filter'])){
            $meta_query = array();
            $args = array( 
                'post_type' => 'product'
            );
            
            if (isset($_POST['page'])) {
    			$page = $_POST['page'];
                if($page > 0){
                    $args['posts_per_page'] = 10;
                }
    		} else {
    			$page = 1;
    		}
            
            $args['paged'] = $page;
            
            if(!empty($_POST['product_id'])){
            	$products_filter = array();
	        	foreach($product_id as $product){
	        		$products_filter[] = (int)$product;
	        	}
                $args['post__not_in'] = $products_filter;
            }
            
            if(isset($_POST['filter_name'])){
                $args['name'] = $_POST['filter_name'];
            }
            
            if(isset($_POST['filter_status'])){
                $args['post_status'] = $_POST['filter_status'];
            }
            
            if(isset($_POST['filter_sku'])){
                $meta_query[] = array(
                    'key' => '_sku',
                    'value' => $_POST['filter_sku'],
                    'compare' => '='
                );
            }
            
            if(!empty($_POST['stock_status'])){
                $meta_query[] = array(
                    'key' => '_stock_status',
                    'value' => $_POST['stock_status'],
                    'compare' => '='
                );
            }
            
            if(!empty($_POST['price_for']) && !empty($_POST['price_do'])){
                $meta_query[] = array(
                    'key' => '_regular_price',
                    'value' => $_POST['price_for'],
                    'compare' => '>=',
                    'type' => 'NUMERIC'
                );
                $meta_query[] = array(
                    'key' => '_regular_price',
                    'value' => $_POST['price_do'],
                    'compare' => '<=',
                    'type' => 'NUMERIC'
                );
            }
            
            if(!empty($meta_query)){
                $args['meta_query'] = array($meta_query);
            }
            
            if(!empty($_POST['category'])){
                $args['tax_query'] = array(
                    array(
                        'taxonomy'  => 'product_cat',
                        'field'     => 'id', 
                        'terms'     => $_POST['category']
                    )
                );
            }
        
            // Create the new query
        	$loop = new WP_Query( $args );
        	
        	// Get products number
        	$product_count = $loop->post_count;
        	
        	// If results
        	if( $product_count > 0 ) :
        		
        			// Start the loop
        			while ( $loop->have_posts() ) : 
                    
                        $loop->the_post();
                    
                        $product = new WC_Product($loop->post->ID);
                        
                        if ( $product->is_in_stock() ) {
        					$stock_status = '<mark class="instock">' . __( 'In stock', 'woocommerce' ) . '</mark>';
        				} else {
        					$stock_status = '<mark class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</mark>';
        				}
        				
        				if (has_post_thumbnail( $loop->post->ID )) 
        					$image = get_the_post_thumbnail($loop->post->ID, array(60,60)); 
        				else 
        					$image = '<img src="'.$woocommerce->plugin_url().'/assets/images/placeholder.png" alt="" width="60px" height="60px" />';
                            
            			$products[] = array(
            				'product_id'   => $loop->post->ID,
            				'name'         => $post->post_title,
            				'price'        => $product->get_price_html(),
            				'sku'          => get_post_meta($loop->post->ID, '_sku', true),
            				'image'        => $image,
            				'quantity'     => $product->get_stock_quantity(),
            				'stock_status' => $stock_status,
            				'status'       => get_post_status($result->ID),
            				'selected'     => isset($_POST['product_id']) && in_array($result->ID, $_POST['product_id'])
            			);
        		
        			endwhile;
        	endif;
            
            $template = 'product_list.php';
            
            require_once( PDF_CATALOG__PLUGIN_DIR . 'view/template/' . $template);
            exit();
		}
        
        if(!empty($_SESSION['warning'])){
            $error = $_SESSION['warning'];
            $_SESSION['warning'] = '';
        }
        
        $categories = $model->getCategories(0);
        
        $min_max = $model->getMinMaxPrice();
        $min = floor($min_max[0]->min);
        $max = ceil($min_max[0]->max);
        
        $templates = scandir(dirname(__FILE__).'/pdf_data/templates');
        unset($templates[0],$templates[1]);
        
        $template = 'pdfcatalog.php';
        
        require_once( PDF_CATALOG__PLUGIN_DIR . 'view/template/' . $template);
  	}

    private function load_resources() {
        global $woocommerce;
    	wp_register_style( 'pdfcatalog.css', PDF_CATALOG__PLUGIN_URL . 'view/stylesheet/pdfcatalog.css', array(), PDF_CATALOG_VERSION );
    	wp_enqueue_style( 'pdfcatalog.css');
    	wp_register_style( 'tip-twitter.css', PDF_CATALOG__PLUGIN_URL . 'view/stylesheet/tip-twitter.css', array(), PDF_CATALOG_VERSION );
    	wp_enqueue_style( 'tip-twitter.css');
    	
    	wp_register_script( 'pdfcatalog.js', PDF_CATALOG__PLUGIN_URL . 'view/js/pdfcatalog.js', array('jquery','postbox'), PDF_CATALOG_VERSION );
    	wp_enqueue_script( 'pdfcatalog.js' );
    }
}
?>
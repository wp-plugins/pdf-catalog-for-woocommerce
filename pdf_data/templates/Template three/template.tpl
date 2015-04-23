<?php
$margin_top = 5;$margin_right = 0;$margin_bottom = 16;$margin_left = 6;
$footer = '<div style="background: #008348; font-family: Arial; font-size: 12px; height: 28px; font-weight: bold; text-align: center;margin-left:-22px;">
    <div style="text-align: left; color: #fff; margin: 20px 0 0 40px; font-size: 12px; padding:15px 5px 0px 5px; width: 300px;">'.$_SERVER['SERVER_NAME'].'</div>
    <div style="color: #fff; margin: -25px auto auto auto; font-size: 24px; padding: 5px; width: 58px;">{PAGENO}</div>
</div>';
?>

<!-- <tocpagebreak toc-preHTML="&lt;h2&gt;CONTENTS&lt;/h2&gt;" links="1" toc-bookmarkText="Contents" resetpagenum="1" pagenumstyle="1" 
odd-header-name="html_myHTMLHeaderOdd" odd-header-value="1" even-header-name="html_myHTMLHeaderEven" even-header-value="1" odd-footer-name="myFooter2Odd" odd-footer-value="1" even-footer-name="myFooter2Even" even-footer-value="1" />
 -->
<body>
<?php $product_count = $loop->post_count; ?>

<?php if( $product_count > 0 ){ ?>
    <?php while ( $loop->have_posts() ){ ?>
        <?php $loop->the_post(); ?>
        <?php $product = new WC_Product($loop->post->ID); ?>
        
        <div class="product">
            <tocentry name="" content="<?php echo $post->post_title; ?>" />
            <div class="name">
                <?php echo strip_tags(self::utf8_substr(html_entity_decode($post->post_title, ENT_QUOTES, 'UTF-8'), 0, 20)); ?>
            </div>
    		<div class="image">
                <?php if (has_post_thumbnail( $loop->post->ID )){ ?>
            		<?php echo get_the_post_thumbnail($loop->post->ID, array(120,120)); ?>
            	<?php }else{ ?>
            		<img src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/placeholder.png" alt="" style="max-width: 200px; max-height: 120px;" />
                <?php } ?>
    		</div>
            
            <?php $special = $product->get_sale_price(); ?>
            <?php if(!empty($special)){ ?>
                <div class="special">
                    <div><?php echo wc_price($special); ?></div>
                </div>
                <div class="price">
                    <div><s><?php echo wc_price($product->get_regular_price()); ?></s></div>
                </div>
            <?php }else{ ?>
                <div class="special none">
                    <div></div>
                </div>
                <div class="price">
                    <div><?php echo wc_price($product->get_regular_price()); ?></div>
                </div>
            <?php } ?>
    		<div class="model">
    			<?php echo __('SKU').': '.get_post_meta($loop->post->ID, '_sku', true); ?>				
    		</div>
        </div>
    <?php } ?>
<?php } ?>
</body>
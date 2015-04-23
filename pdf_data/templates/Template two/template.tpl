<?php
$header = '
<div style="height: 40px;background: #5ab6fb; font-size: 24px; font-weight: bold; padding: 12px 40px 6px 40px; border-bottom: 3px solid #000;">
    '.$store_name.'
</div>
';?>
<?php
$footer = '<div align="right" style="background: #5ab6fb; font-size: 12px; height: 40px; font-weight: bold; text-align: center;">
<div style="margin-top: 0px; background: #000; height: 3px;"></div>
    <div style="background: #000; color: #fff; margin-top: -15px; position: absolute; margin-left: 40px; font-size: 12px; padding: 5px; width: 200px;">'.$_SERVER['SERVER_NAME'].'</div>
    <div style="background: #000; color: #fff; margin-top: -25px; margin-left: 720px; font-size: 12px; padding: 5px; width: 28px;">{PAGENO}</div>
</div>';
?>

<!-- <tocpagebreak toc-preHTML="&lt;h2&gt;CONTENTS&lt;/h2&gt;" links="1" toc-bookmarkText="Contents" resetpagenum="1" pagenumstyle="1" 
odd-header-name="html_myHTMLHeaderOdd" odd-header-value="1" even-header-name="html_myHTMLHeaderEven" even-header-value="1" odd-footer-name="myFooter2Odd" odd-footer-value="1" even-footer-name="myFooter2Even" even-footer-value="1" />
 -->
 
<?php $product_count = $loop->post_count; ?>

<?php if( $product_count > 0 ){ ?>
    <?php while ( $loop->have_posts() ){ ?>
        <?php $loop->the_post(); ?>
        <?php $product = new WC_Product($loop->post->ID); ?>
        
        <div class="product">
            <tocentry name="" content="<?php echo $post->post_title; ?>" />
            <div class="name">
                <?php echo strip_tags(self::utf8_substr(html_entity_decode($post->post_title, ENT_QUOTES, 'UTF-8'), 0, 30)); ?>
            </div>
    		<div class="image">
                <?php if (has_post_thumbnail( $loop->post->ID )){ ?>
            		<?php echo get_the_post_thumbnail($loop->post->ID, array(200,200)); ?>
            	<?php }else{ ?>
            		<img src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/placeholder.png" alt="" style="max-width: 200px; max-height: 200px;" />
                <?php } ?>
    		</div>
            <?php $special = $product->get_sale_price(); ?>
            <?php if(!empty($special)){ ?>
                
                <div class="special">
                    <div style="margin-top: -32px;"><?php echo wc_price($special); ?></div>
                </div>
                <div class="price">
                    <div style="margin-top: -32px;"><s><?php echo wc_price($product->get_regular_price()); ?></s></div>
                </div>
            <?php }else{ ?>
                <div class="price">
                    <div style="margin-top: -32px;"><?php echo wc_price($product->get_regular_price()); ?></div>
                </div>
            <?php } ?>
            <div class="description">
                <?php echo strip_tags(self::utf8_substr(html_entity_decode($post->post_content, ENT_QUOTES, 'UTF-8'), 0, 350)); ?>
            </div>
        </div>
    <?php } ?>
<?php } ?>
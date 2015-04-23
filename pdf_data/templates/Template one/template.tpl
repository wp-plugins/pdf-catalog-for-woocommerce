<?php
$header = '
<div style="height: 40px;background: #fff; padding: 15px 40px 0 40px;">
    <img style="height: 25px;" src="'.$logo.'" />
</div>
';?>
<?php
$footer = '<div align="right" style="font-size: 12px;padding: 0 20px 10px 0;">{PAGENO}/{nb}</div>';
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
            <div class="description">
                <?php echo strip_tags(self::utf8_substr(html_entity_decode($post->post_content, ENT_QUOTES, 'UTF-8'), 0, 750)); ?>
            </div>
            <div class="name">
                <?php echo strip_tags(self::utf8_substr(html_entity_decode($post->post_title, ENT_QUOTES, 'UTF-8'), 0, 30)); ?>
            </div>
    		<div class="image">
                <?php if (has_post_thumbnail( $loop->post->ID )){ ?>
            		<?php echo get_the_post_thumbnail($loop->post->ID, array(240,240)); ?>
            	<?php }else{ ?>
            		<img src="<?php echo $woocommerce->plugin_url(); ?>/assets/images/placeholder.png" alt="" style="max-width: 240px; max-height: 240px;" />
                <?php } ?>
    		</div>
            <?php $special = $product->get_sale_price(); ?>
            <?php if(!empty($special)){ ?>
                <div class="price">
                    <img src="../wp-content/plugins/pdfcatalog/pdf_data/templates/Template one/img/price_bg.png"/>
                    <div style="margin-top: -32px;"><s><?php echo wc_price($product->get_regular_price()); ?></s></div>
                </div>
                <div class="special">
                    <img src="../wp-content/plugins/pdfcatalog/pdf_data/templates/Template one/img/new_price_bg.png"/>
                    <div style="margin-top: -32px;"><?php echo wc_price($special); ?></div>
                </div>
            <?php }else{ ?>
                <div class="price">
                    <img src="../wp-content/plugins/pdfcatalog/pdf_data/templates/Template one/img/price_bg.png"/>
                    <div style="margin-top: -32px;"><?php echo wc_price($product->get_regular_price()); ?></div>
                </div>
            <?php } ?>
            <div class="info">
                <div class="manufacturer"><?php echo __('SKU').' '.get_post_meta($loop->post->ID, '_sku', true); ?></div>
                <div class="code"></div>
            </div>
        </div>
    <?php } ?>
<?php } ?>
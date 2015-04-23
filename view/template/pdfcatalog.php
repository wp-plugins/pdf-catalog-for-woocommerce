<div id="content">
  <?php if (!empty($error)){ ?>
  <div class="warning"><?php echo $error; ?></div>
  <?php } ?>
  <div class="wrap">
    <div class="heading">
      <h2><?php _e(PDF_CATALOG_PLUGIN_NAME); ?></h2>
      <div class="buttons">
        <a onclick="sendData();" class="button"><?php _e('Get PDF'); ?></a>
        <div class="clear"></div>
      </div>
    </div>
    <div class="content">
        <div class="content_categories_list">
            <div class="header header_title">
                <input id="cat" type="checkbox" /><?php _e('Categories'); ?>
            </div>
            <div class="categories_list scrollbox">
                <?php $class = 'odd'; ?>
                <?php foreach ($categories as $category) { ?>
                    <?php $class = ($class == 'alternate' ? 'odd' : 'alternate'); ?>                            
                    <div class="<?php echo $class; ?>">
                        <?php echo $category['parents']; ?>
                        <input type="checkbox" name="category_id[]" value="<?php echo $category['category_id']; ?>" <?php if($category['product_quantity'] == 0){ echo 'disabled="disabled"'; } ?> />
                        <?php echo $category['name']; ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="div_template_list">
            <div class="title_template">
                <?php _e('Templates'); ?>
            </div>
            <div class="template_list scrollbox">
            <?php $counter = 1; ?>
            <?php foreach($templates as $template){ ?>
                <div class="templates" >
                    <div class="template_name">
                        <input type="radio" name="template" value="<?php echo $template; ?>" id="<?php echo $template; ?>" <?php if($counter == 1){ ?>checked="checked"<?php } ?> />
                        <label for="<?php echo $template; ?>"> <?php echo $template; ?> </label>
                    </div>
                    <label for="<?php echo $template; ?>">
                        <img src="<?php echo PDF_CATALOG__PLUGIN_URL; ?>/pdf_data/templates/<?php echo $template; ?>/icon.png" alt="" title="" /><br/>
                    </label>                            
                </div>
                <?php $counter++; ?>
            <?php } ?>
            </div>
        </div>
        <div id="overlay">
            <div class="dialog" style="margin-top: -90px; margin-left: -140px;">
                <div class="dialog_close"></div>
                <div class="dialog_content">
                    <div class="dialog_title"><?php _e('Template settings'); ?>:</div>
                    <div class="template_options"></div>
                    <div style="float: left;margin: 22px 0 0 26px;"><input type="checkbox" name="" id="show_before"/><label for="show_before"> Show before Generating PDF</label></div>
                    <div class="buttons" style="float: right; margin: 20px 30px 20px 0px;"><a onclick="setOptions();closeDialog();" class="button"><?php _e('Save'); ?></a></div>
                </div>
            </div>
        </div>
        <input type="hidden" name="min" value="<?php echo $min; ?>" />
        <input type="hidden" name="max" value="<?php echo $max; ?>" />
        <input type="hidden" name="data_type" value="categories" />
        <input type="hidden" name="action" value="<?php menu_page_url('pdfcatalog'); ?>" />
        <input type="hidden" name="template_options_open_help" value="<?php _e('Click Here to Open Settings for'); ?>" />
        <input type="hidden" name="template_options_not_available_help" value="<?php _e('Settings Not Available for'); ?>" />
        <form id="send_data" method="post" target="_blank" style="display: none;"></form>
    </div>
    <div class="clear"></div>
    <span style="text-align: right; display: block; color: #9C9C9C;">PDF Catalog v: <?php echo PDF_CATALOG_VERSION; ?></span>
  </div>
</div>

<div id="overlay_to_upload">
    <div class="dialog_to_upload" style="margin-top: -90px; margin-left: -140px;">
        <div class="dialog_close_to_upload" onclick="closeUploadDialog()"></div>
        <div class="dialog_content">
            <form id="upload" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
                <div id="drop">
                    Drop Here
     
                    <a>Browse</a>
                    <input type="file" name="upl" multiple />
                </div>
     
                <ul>
                </ul>
     
            </form>
        </div>
    </div>
</div>

<script type="text/javascript"><!--
jQuery('#tabs a').tabs(); 
//--></script>
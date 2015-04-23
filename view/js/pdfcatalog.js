jQuery(function() {
    jQuery('.button_more_menu').on('click', function(){
        jQuery('.more_menu').slideDown("slow");
    });
    jQuery('.more_menu').hover(function(){},function(){
        jQuery('.more_menu').slideUp("slow");
    });
    
    jQuery('.save_to_file').on('click', function(){
        setSettings();
    });
    
    jQuery('.load_from_file').on('click', function(){
        openUploadDialog();
    });
    
    jQuery('body').on('click', '.dialog_close', function(){
        jQuery('#overlay').fadeOut(200);
        window.onmousewheel = document.onmousewheel = window.onscroll = document.onscroll = function (e) {return true;};
    });
    
    jQuery('body').on('change', 'input[name="template"]', function(){
        jQuery('input[name="template"]').attr("checked", false);
        jQuery(this).attr("checked", "checked");
        return false;
    });
    
    jQuery('body').on('click', '#cat', function(){
        if(jQuery(this).attr('checked')){
            jQuery('div.categories_list :checkbox').each(function(){
                if(!jQuery(this).attr('disabled')){
                    jQuery(this).attr('checked', true);
                }
            })
        }else{
            jQuery('div.categories_list :checkbox').each(function(){
                if(!jQuery(this).attr('disabled')){
                    jQuery(this).attr('checked', false);
                }
            })
        }
    });
    
    jQuery('body').on('click', '.categories_list input[type="checkbox"]', function(){
        if(jQuery(this).attr('checked')){
            jQuery(this).attr('checked', true);
        }else{
            jQuery(this).attr('checked', false);
        }
    });
    
    jQuery('body').on('change', 'select', function(){
        var op = jQuery('select[name="'+jQuery(this).attr('name')+'"]'+" :selected");
        jQuery('select[name="'+jQuery(this).attr('name')+'"] option').attr("selected", false);
        op.attr("selected", "selected");
    });
    jQuery('body').on('keyup', 'input[type="text"]', function(){
        var input_val = jQuery(this).val();
        jQuery(this).attr("value", input_val);
    });
    jQuery('body').on('change', 'input[type="radio"]', function(){
        jQuery('input[name="'+jQuery(this).attr('name')+'"]').attr("checked", false);
        jQuery(this).attr("checked", "checked");
    });
    jQuery('body').on('change', 'input[type="checkbox"]', function(){
        if(jQuery(this).attr('checked')){
            jQuery(this).attr("checked", "checked");
        }else{
            jQuery(this).attr("checked", false);
        }
    });
});

function sendData(){
    var ver = 0;
    var error = '';
    jQuery('div#message').remove();
    
    jQuery('div.categories_list :checkbox').each(function(){
        if(jQuery(this).attr('checked')){
            ver++;
        }
    });
    
    jQuery('#send_data').html(jQuery('div.categories_list').html());
    error = 'Please select some categories to proceed!';
    
    if(ver == 0){
        var content = '<div id="message" class="error"><p>'+error+'</p></div>';
        jQuery('#content > div.wrap h2').after(content);
    }else{
        jQuery('#send_data').append('<input type="hidden" name="template" value="'+jQuery('input[name="template"]:radio:checked').val()+'" />');
        jQuery('#send_data').append('<input type="hidden" name="pdf" value="1" />');
        jQuery('#send_data').submit();
    }
}
function getSettings(){
    jQuery.ajax({
        url: ajaxurl,
		type: 'post',
		data: 'action=pdfcatalog&get_settings=1',
		dataType: 'html',
		success: function(html) {
            jQuery('.content').html(html);
		}
	});
};
function setSettings(){
    var content = jQuery('.content').html();
    jQuery.ajax({
		type: 'post',
		data: 'set_settings=' + content,
		dataType: 'html',
		success: function(html) {
		  jQuery.fileDownload(ajaxurl, {
            failMessageHtml: "There was a problem generating your report, please try again.",
            httpMethod: "POST",
            data: 'action=pdfcatalog&fileDownload=1'
          });
		},
        error: function (request, status, error) {
            alert(request.responseText);
        }
	});
};
function openDialog(){
    jQuery('#overlay').fadeIn(200);
    jQuery('.dialog').css({
        'margin-top':'-'+(jQuery('.dialog').height()/2)+'px',
        'margin-left':'-'+(jQuery('.dialog').width()/2)+'px'
    });
    window.onmousewheel = document.onmousewheel = window.onscroll = document.onscroll = function (e) {return false;};
}
function closeDialog(){
        jQuery('#overlay').fadeOut(200);
        window.onmousewheel = document.onmousewheel = window.onscroll = document.onscroll = function (e) {return true;};
}

function openUploadDialog(){
    jQuery('#overlay_to_upload').fadeIn(200);
    jQuery('.dialog_to_upload').css({
        'margin-top':'-'+(jQuery('.dialog_to_upload').height()/2)+'px',
        'margin-left':'-'+(jQuery('.dialog_to_upload').width()/2)+'px'
    });
    window.onmousewheel = document.onmousewheel = window.onscroll = document.onscroll = function (e) {return false;};
}
function closeUploadDialog(){
        jQuery('#overlay_to_upload').fadeOut(200);
        window.onmousewheel = document.onmousewheel = window.onscroll = document.onscroll = function (e) {return true;};
}

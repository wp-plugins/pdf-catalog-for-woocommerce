jQuery(function() {
    var progressbar = jQuery( "#progressbar" ),
      progressLabel = jQuery( ".progress-label" );
 
    progressbar.progressbar({
      value: false,
      change: function() {
        progressLabel.text( progressbar.progressbar( "value" ) + "%" );
      },
      complete: function() {
        progressLabel.text( "Please wait, forming a link!" );
      }
    });
    
    var procent = 100 / products_id.length;
    var jsonPostData = JSON.stringify(post_data);
    var pageNum = 1;
    var pdfList = '';
    
    processCreatePDF(0);
    
    function processCreatePDF(index){
        var num = index + 1;
        jQuery.ajax({
    		type: 'post',
    		data: 'createpdf=' + num + '&products_id=' + products_id[index] + '&post_data=' + jsonPostData + '&page_num=' + pageNum + '&pdf_length=' + products_id.length + '&pdf=1',
    		dataType: 'html',
    		success: function(page_num) {
                page_num = jQuery(page_num).find('div.pagenum').text(); 		  
                pageNum = pageNum + parseInt(page_num);
                progressbar.progressbar( "value", Math.round(procent * num));
                if(num < products_id.length){
                    processCreatePDF(index+1);
                }else{
                    jQuery.ajax({
                		type: 'post',
                        data: 'getpdf=1',
                		dataType: 'html',
                		success: function(html) {
                		  if(jQuery(html).find('div.link').text()){
                            window.location = jQuery(html).find('div.link').text();
                          }else{
                            pdfList = jQuery(html).find('div.pdfList').text();
                            processConcatePDF(0);
                          }
                		}
                	});
                }
    		}
    	});
    }
    
    function processConcatePDF(index){
        jQuery.ajax({
            data: 'pdfList=' + pdfList[index] + '&key=' + index + '&getpdf=1',
    		type: 'post',
    		dataType: 'html',
    		success: function(html) {
              index = index+1;
    		  progressbar.progressbar( "value", Math.round((100 / pdfList.length) * index));
    		  if(index < pdfList.length){
                processConcatePDF(index);
              }else{
                jQuery('#progressbar').after('<a href="'+jQuery(html).find('div.link').text()+'" class="button" target="blank">Download</a>');
                //window.location = jQuery(html).find('div.link').text();
              }
    		}
    	});
    }
});

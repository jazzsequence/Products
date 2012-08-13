jQuery(document).ready(function() {
 
    jQuery('.upload_button').click(function() {
         targetfield = jQuery(this).prev('.product_upload');
         tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
         return false;
    });
 
    window.send_to_editor = function(html) {
         imgurl = jQuery('img',html).attr('src');
         jQuery(targetfield).val(imgurl);
         tb_remove();
    }
 
});

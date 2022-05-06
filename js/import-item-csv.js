jQuery(document).ready(function(){
	
	var button_exist = false;
	var oldXHR = window.XMLHttpRequest;

	function newXHR() {
		var realXHR = new oldXHR();
		realXHR.addEventListener("readystatechange", function() {
			
			var path = realXHR.responseURL;
			if(path == "")return;
			
			var url_string = path; //window.location.href
			var url = new URL(url_string);
			var c = url.searchParams.get("action");
			// console.log(c);
			if(c == "stm_lms_get_curriculum_v2" && button_exist == false){
				if(realXHR.readyState==1){
				// console.log('server connection established');
				}
				if(realXHR.readyState==2){
					// console.log('request received');
				}
				if(realXHR.readyState==3){
					// console.log('processing request');
				}
				if(realXHR.readyState==4){
					// console.log('request finished and response is ready');
					
					setTimeout(function(){
						jQuery("div#stm_courses_curriculum .stm_lms_curriculum_v2 > .sections > .section").each(function(){

					
							var section_count = jQuery(this).find(".section_count").text();
							section_count = section_count.replace(/[^0-9\.]/g, '');
						
							jQuery(this).find(".add_items").prepend("<button class='open-import-item' data-id="+section_count+">Import Item</button>")
							
							
							
						})
					}, 1000);
					
					button_exist = true;
					
					
				}
			}
			if(c == "stm_save_questions" && button_exist == false){
				if(realXHR.readyState==1){
				// console.log('server connection established');
				}
				if(realXHR.readyState==2){
					// console.log('request received');
				}
				if(realXHR.readyState==3){
					// console.log('processing request');
				}
				if(realXHR.readyState==4){
					// console.log('request finished and response is ready');
					
					setTimeout(function(){
						jQuery(".stm_lms_questions_v2 > .add_items").prepend("<button class='open-import-item-question'>Import Item</button>")
					}, 1000);
					
					button_exist = true;
					
					
				}
			}
			
		}, false);
		return realXHR;
	}
	window.XMLHttpRequest = newXHR;
	
/* 	
	
	jQuery( document ).ajaxStop(function(event, xhr, settings) {
		console.log(settings);
		if(button_exist == false){
			
			
		}
	}) */
	
	jQuery("body").append(`
<div id="dialog-container-import-lms-item" class="hide-import-lesson">
    <div id="dialogForm">
      
	<div class="modal-dialog">
            <div class="modal-content">
			 
				
                <div class="modal-header">
                    <h5 class="modal-title">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    
           
                <div class="input-row">
                    <label class="col-md-4 control-label">Choose CSV File</label> 
					<form class="form-horizontal" 
				action="`+importlmsAjax.ajaxurl_lms+`"
				method="post"
                name="frmCSVImport" id="frmCSVImport"
                enctype="multipart/form-data">
					<input type="file" name="upload_csv" id="file" accept=".csv">
					<input type="hidden" name="action" value="import_csv_lms_cours_lesson">
					  <input type="hidden" name="id" value="`+importlmsAjax.lms_id+`">
					  <input type="hidden" name="curriculum_ids" value="">
					  <input type="hidden" name="section_num" value="">
                    <button type="submit" id="submit" name="import" value="import" class="btn-submit">Import</button>
                    <br />
					 </form>
					  
                </div>
          

                </div>
                
				
            </div>
        </div>
    </div>
</div>`)
	
	
	jQuery('body').on('click' , '.open-import-item-question' , function(event){
		event.preventDefault();
		jQuery('#dialog-container-import-lms-item ').removeClass("hide-import-lesson")
		
		jQuery('#dialog-container-import-lms-item .modal-title').html("Import Question") 
		 jQuery('#dialog-container-import-lms-item input[name="curriculum_ids"]').val(null);
		 
		 jQuery('#dialog-container-import-lms-item input[name="section_num"]').val(null);
		 jQuery('#dialog-container-import-lms-item input[name="action"]').val('import_csv_lms_course_question');
		 
		 
	})
		
	jQuery('body').on('click' , '.open-import-item' , function(event){
		event.preventDefault();
		jQuery('#dialog-container-import-lms-item ').removeClass("hide-import-lesson")
		var data_id = jQuery(this).data("id");
		var section_curriculum_ids = jQuery('#section_curriculum-curriculum').val();
		
		
		jQuery('#dialog-container-import-lms-item .modal-title').html("Section "  +data_id) 
		 jQuery('#dialog-container-import-lms-item input[name="curriculum_ids"]').val(section_curriculum_ids);
		 
		 jQuery('#dialog-container-import-lms-item input[name="section_num"]').val(data_id);
		 
		 
		 
	})
	
	jQuery('body').on('click' , 'button.close' , function(event){
		event.preventDefault();
		jQuery('#dialog-container-import-lms-item ').addClass("hide-import-lesson")
	})
	
	
})


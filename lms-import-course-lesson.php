<?php
    /*
    Plugin Name: MasterStudy LMS Import Course and Lesson
    Plugin URI: #
    Description: MasterStudy LMS Import course and lesson
    Version: 1.0
    Author: Neroshin
    Author URI: # 
    */
	
	   // add_action("init" , function(){
		// if(/* $_POST['post_type'] == 'stm-quizzes' || $_POST['post_type'] == 'stm-lessons'|| */ $_POST['post_type'] == 'stm-courses'){
			// echo "<pre>";
			
			// echo "<pre>";
			// print_r($_POST);
			// die(); 
		// }
	// });  
	
	
	 add_action('admin_menu', 'thelms_import_menu_csv_entry' , 10);

    function thelms_import_menu_csv_entry(){
     
		  add_menu_page( 
        __( 'Import LMS =', 'textdomain' ),
        'Import LMS Course',
        'manage_options',
        'import_lms_course',
        'helms_import_csv_entry_plugin_admin_page',
    ); 
	 
		
		/* add_submenu_page(
        'import_lms_course',
        __( 'Import Lesson', 'textdomain' ),
        __( 'Import Lesson', 'textdomain' ),
        'manage_options',
        'import_lms_lesson',
        'helms_import_csv_entry_plugin_admin_page',
		100
    );  */

   }
	
    function helms_import_csv_entry_plugin_admin_page(){
		ob_start();
    
		include(plugin_dir_path( __FILE__ ) . 'templates/th-csv-entry-template.php');
		
		$output = ob_get_clean();
		
		echo $output; 
	}
	
	function wp_theitem_import_csv_scripts() {
		
		/* echo get_post_type();
		die(); */
		if ( 'stm-courses' == get_post_type() || 'stm-quizzes' == get_post_type()  ) {
			wp_register_script( 'theitem_import_csv', plugins_url('/js/import-item-csv.js', __FILE__ ), array(), filemtime(plugin_dir_path( __FILE__ ) . "/js/import-item-csv.js"));
			
			 wp_localize_script( 'theitem_import_csv', 'importlmsAjax', array( 'ajaxurl_lms' => admin_url('admin-post.php') , 'lms_id' => $_GET['post']??""));   
			 
			
			wp_enqueue_script( 'theitem_import_csv' );
			
			wp_enqueue_style('main-styles',plugins_url('/css/style.css', __FILE__ ), array(), filemtime(plugin_dir_path( __FILE__ ) . "/css/style.css"), false);
		}
		
	}
	add_action( 'admin_enqueue_scripts', 'wp_theitem_import_csv_scripts' );
	
	
	add_action( 'admin_post_nopriv_import_csv_lms_course', 'import_csv_lms_course' );
	add_action( 'admin_post_import_csv_lms_course', 'import_csv_lms_course' );
	
	function import_csv_lms_course() {
	
		// echo "fasdf";	
		
		
		if (isset($_POST['import'])) {
			$original = ini_get("auto_detect_line_endings");
			ini_set("auto_detect_line_endings", true);
			$handle = fopen($_FILES['upload_csv']['tmp_name'], "r");
			ini_set("auto_detect_line_endings", $original);
			echo "<pre>"; 
			
			$flag = true;
			$column = array_map('nestedMetakey', (array)fgetcsv($handle));
			// print_r($column);
			
			$current_user = (array)wp_get_current_user();
		
			if(!isset($current_user['ID']))return;
			$row = 0;
			$current_user_id = $current_user['ID'];
			
			
			
			do {
				  if($flag) { $flag = false; continue; }
				  usleep(2); 
				  /* echo "<pre>"; 
				  print_r($line);
				  echo "</pre>"; */  
				  
				$post_type = "stm-courses";  
				
				
				$course_title = array_search('course-title', $column);
				$course_content = array_search('course-content', $column);
				$course_category = array_search('course-category', $column);
				$course_featured_image_id = array_search('course-featured-image-id', $column);
				$curriculum = array_search('curriculum', $column);
				$course_status = array_search('course-status-(publish/draft)', $column);
				$one_time_purchase = array_search('one-time-purchase', $column);
				$price = array_search('price', $column);
				$sale_price = array_search('sale-price', $column);
				$sale_price_dates_start = array_search('sale-price-dates-start', $column);
				$sale_price_dates_end = array_search('sale-price-dates-end', $column);
				$faq_question = array_search('faq-question', $column);
				$faq_answer = array_search('faq-answer', $column);
				$featured = array_search('featured', $column);
				$views = array_search('views', $column);
				$level = array_search('level', $column);
				$current_students = array_search('current-students', $column);
				$duration_info = array_search('duration-info', $column);
				$video_duration = array_search('video-duration', $column);
				$enterprise_price = array_search('enterprise-price', $column);
				$membership = array_search('membership', $column);
				$time_limit = array_search('time-limit', $column);
				$course_expiration_day = array_search('course-expiration-(days)', $column);
				$prerequisites = array_search('prerequisites', $column);
				$prerequisite_passing_level = array_search('prerequisite-passing-level', $column);
				$announcement = array_search('announcement', $column);
				$publish_date = array_search('publish_date', $column);
				$course_certificate = array_search('course-certificate', $column);
				$author = array_search('author', $column);
				  
				
				$item_title = $line[$course_title];
				
				$faq_question_obj = array_filter(preg_split( '/\([0-9]+\)./', $line[$faq_question] ) ,  'strlen');
				$faq_answer_obj = array_filter(preg_split( '/\([0-9]+\)./', $line[$faq_answer] ) ,  'strlen');
				  
				 // print_r($faq_question_obj);
				 $faq = array();
				 
				 
				
				/* echo $line[$sale_price_dates_start];
				echo $line[$sale_price_dates_end];
				 */
				$date = $line[$sale_price_dates_start] . " ~ " . $line[$sale_price_dates_end];
					$sale_price_dates = $line[$sale_price_dates_start] . " , " . $line[$sale_price_dates_end] ;
					
				$sale_price_dates_start = new DateTime($line[$sale_price_dates_start], new DateTimeZone( 'America/New_York' ) );;
				
				$sale_price_dates_end = new DateTime($line[$sale_price_dates_end], new DateTimeZone( 'America/New_York' ) );;
				
				$sale_price_dates = $sale_price_dates_start->getTimestamp().'000' ." , ".$sale_price_dates_end->getTimestamp().'000' ;
				
			
			
 
		 	/*  echo $sale_price_dates;
			
			 	 die();   */
				if(!empty($faq_question_obj)){
					foreach($faq_question_obj as $key => $value){
						$faq[$key]['question'] = trim($value);
						$faq[$key]['answer'] = trim($faq_answer_obj[$key]);
					}
				}
				
				$curriculum_sections = implode(",", preg_split( '/\r\n|\r|\n/', $line[$curriculum] ));
				
				
				/*    echo "<pre>";  
				 print_r( sanitize_text_field($curriculum_sections) ); 
				  echo "</pre>";   
				 
				  die();  */
				 
				 if ( empty( sanitize_text_field( $post_type ) ) ) {
						wp_send_json(
							array(
								'error'   => true,
								'message' => esc_html__( 'Post Type is required', 'masterstudy-lms-learning-management-system' ),
							)
						);
					}

					// Check if data passed
					if ( empty( $item_title ) ) {
						wp_send_json(
							array(
								'error'   => true,
								'message' => esc_html__( 'Title is required', 'masterstudy-lms-learning-management-system' ),
							)
						);
					}

					$category_ids = null; // Question categories
					$post_type    = sanitize_text_field( $post_type );
					$title        = sanitize_text_field( urldecode( $item_title ) );
					
				    

					$result   = array();
					$is_front = (bool) ( ! empty( $_GET['is_front'] ) ) ? sanitize_text_field( $_GET['is_front'] ) : false;
					$item     = array(
						'post_type'   => $post_type,
						'post_title'  => html_entity_decode( $title ),
						'post_status' => "publish",
					);

				
				
				
					
					$result['id'] = wp_insert_post( $item );

						
					
					update_post_meta( $result['id'], 'curriculum', $curriculum_sections );
					
					update_post_meta( $result['id'], 'content', $line[$course_content ]);
					
					update_post_meta( $result['id'], 'faq', json_encode($faq) );
					update_post_meta( $result['id'], 'level',  $line[$level]);
					update_post_meta( $result['id'], '_thumbnail_id',  $line[$course_featured_image_id]);
					update_post_meta( $result['id'], 'featured',  $line[$featured]);
					update_post_meta( $result['id'], 'views',  $line[$views]);
					update_post_meta( $result['id'], 'current_students',  $line[$current_students]);
					update_post_meta( $result['id'], 'duration_info',  $line[$duration_info]);
					update_post_meta( $result['id'], 'video_duration',  $line[$video_duration]);
					update_post_meta( $result['id'], 'price',  $line[$price]);
					update_post_meta( $result['id'], 'sale_price',  $line[$sale_price]);
					
					
					// update_post_meta( $result['id'], 'date',  $date);
					update_post_meta( $result['id'], 'sale_price_dates',  $sale_price_dates);
					update_post_meta( $result['id'], 'sale_price_dates_start',  $sale_price_dates_start->getTimestamp().'000' );
					
					update_post_meta( $result['id'], 'sale_price_dates_end',   $sale_price_dates_end->getTimestamp().'000');
					
					
					
					update_post_meta( $result['id'], 'enterprise_price',  $line[$enterprise_price]);
					update_post_meta( $result['id'], 'not_membership',  $line[$membership]);
					update_post_meta( $result['id'], 'expiration_course',  $line[$time_limit]);
					update_post_meta( $result['id'], 'end_time',  $line[$course_expiration_day]);
					update_post_meta( $result['id'], 'prerequisites',  $line[$prerequisites]);
					update_post_meta( $result['id'], 'prerequisite_passing_level',  $line[$prerequisite_passing_level]);
					update_post_meta( $result['id'], 'announcement',  $line[$announcement]);
					
					update_post_meta( $result['id'], 'publish_date',  $line[$publish_date]);
					update_post_meta( $result['id'], 'course_certificate',  $line[$course_certificate]);
					update_post_meta( $result['id'], 'author',  $line[$author]);
					 
						
					
					


					

				
				 
				 
				 
				 
					 $row++;
			
					
				  unset($line);
				  flush();
				  ob_flush(); 
		} while (($line = fgetcsv($handle)) !== false && $row < 100);
		
		

			fclose($handle);
		} 
		echo "<meta http-equiv='refresh' content='0;url=/wp-admin/edit.php?post_type=stm-courses'>";	
	
		// die();
	}
	add_action( 'admin_post_nopriv_import_csv_lms_course_question', 'import_csv_lms_course_question' );
	add_action( 'admin_post_import_csv_lms_course_question', 'import_csv_lms_course_question' );
	
	function import_csv_lms_course_question() {
		$row = 0;
		$available_post_types       = array( 'stm-questions' );		
		if (isset($_POST['import'])) {
			$original = ini_get("auto_detect_line_endings");
			ini_set("auto_detect_line_endings", true);
			$handle = fopen($_FILES['upload_csv']['tmp_name'], "r");
			ini_set("auto_detect_line_endings", $original);
			// echo "<pre>"; 
			
			$flag = true;
			$column = array_map('nestedMetakey', (array)fgetcsv($handle));
			// print_r($column);
			$curriculum_meta_save = null;
			
			$quiz_id = $_POST['id'];	
			$questions_ids = get_post_meta( $quiz_id, 'questions', true );
			$questions_ids = explode( ',', $questions_ids );
			
			if(empty($questions_ids))$questions_ids = array();
			
			
			/*  print_r($questions_ids);
			die();  */
			do {
				  if($flag) { $flag = false; continue; }
				  usleep(2); 
				
				    
				  $item_title = array_search('title', $column);
				  $answers = array_search('answers', $column);
				  $question_type = array_search('question-type', $column);
				  $correct_key = array_search('correct', $column);
				  $post_type = array_search('post-type', $column);
				  $question_result_explanation = array_search('question-result-explanation', $column);
					
					
					
				  $post_type = $line[$post_type];
				  $item_title = $line[$item_title];	 
					
					
				  $answers_obj = array_filter(preg_split( '/\([0-9]+\)./', $line[$answers] ), 'strlen');
				  $correct = null;
				
				  
				   
				   $answer_correct = array();
				  
				  if(is_array($answers_obj)){
					
					
					
					 switch ($line[$question_type]) {
					  case "item_match":
					  
						// echo $line[$question_type];
					  
						$correct = array_filter(preg_split( '/\([0-9]+\)./', $line[$correct_key] ), 'strlen');
						
						$keyzero = 0 ;
						 foreach($answers_obj as $key => $value){
						   $answer_correct[$keyzero]['text'] = $correct[$key];
						   $answer_correct[$keyzero]['isTrue'] = 0;
						   $answer_correct[$keyzero]['question'] = rtrim($value);
						   $keyzero++;
						 }
						break;
					  case "image_match":
						$correct = array_filter(preg_split( '/\([0-9]+\)./', $line[$correct_key] ), 'strlen');
						
						$keyzero = 0 ;
						 foreach($answers_obj as $key => $value){
							 
							$regex = '/https?\:\/\/[^\" ]+/i';
							$question_image = $value;
							$text_image = $correct[$key];
							
							$text_question = array_values(array_filter(array_map('trim' , preg_split($regex, $value)), 'strlen'));
							
							$text =  array_values(array_filter(array_map('trim' ,preg_split($regex, $correct[$key])), 'strlen'));
							
							/* print_r( $text_image);
							die(); */
							preg_match($regex, $question_image, $question_image);
							preg_match($regex, $text_image, $text_image);
							//echo ;
							
							$answer_correct[$keyzero]['isTrue'] = 0;

							// Description condition
							if( !empty($text)){
								$answer_correct[$keyzero]['text'] = ltrim($text[0], '-');
							}
							if( !empty($text)){
								$answer_correct[$keyzero]['question'] = ltrim($text_question[0], '-');
							}
						    
							
							// Images condition
						    if( !empty($question_image) && filter_var(rtrim($question_image[0]), FILTER_VALIDATE_URL)){
								
							
								$question_image = wp_insert_attachment_from_url($question_image[0]);
								
								$answer_correct[$keyzero]['question_image'] = $question_image;
								/* echo $question_image[0];
								print_r($question_image);
								die(); */
							 }
							if( !empty($text_image) &&  filter_var(rtrim($text_image[0]), FILTER_VALIDATE_URL)){
								
								$text_image = wp_insert_attachment_from_url($text_image[0]);
								
								/* echo $text_image[0];
								print_r($text_image);
								die(); */
								$answer_correct[$keyzero]['text_image'] =$text_image;
							} 
						  
						   $keyzero++;
						 }
						/*  echo "<pre>";
						 print_r($answer_correct);
						 die(); */
						 
						break;
					  default:
					  
						// echo $line[$question_type];
					  
						$correct = explode( ',', $line[$correct_key] );
						
						 $keyzero = 0 ;
						  foreach($answers_obj as $key => $value){
							   $answer_correct[$keyzero]['text'] = rtrim($value);
							   if(in_array($key , $correct)){
								   $answer_correct[$keyzero]['isTrue'] = 1;
							   }else{
								    $answer_correct[$keyzero]['isTrue'] = 0;
							   }
							   
							   
							    $keyzero++;
						  }
						
					}
				  $answer_correct = array_filter($answer_correct);
				  
				 /*  print_r($answer_correct )
					 die(); */
					  // Check if data passed
					if ( empty( sanitize_text_field( $post_type ) ) ) {
						wp_send_json(
							array(
								'error'   => true,
								'message' => esc_html__( 'Post Type is required', 'masterstudy-lms-learning-management-system' ),
							)
						);
					}

					// Check if data passed
					if ( empty( $item_title ) ) {
						wp_send_json(
							array(
								'error'   => true,
								'message' => esc_html__( 'Title is required', 'masterstudy-lms-learning-management-system' ),
							)
						);
					}

					$category_ids = null; // Question categories
					$post_type    = sanitize_text_field( $post_type );
					$title        = sanitize_text_field( urldecode( $item_title ) );
					
				

					// Check if available post type
					if ( ! in_array( $post_type, $available_post_types ) ) {
						wp_send_json(
							array(
								'error'   => true,
								'message' => esc_html__( 'Wrong post type', 'masterstudy-lms-learning-management-system' ),
							)
						);
					}

					$result   = array();
					$is_front = (bool) ( ! empty( $_GET['is_front'] ) ) ? sanitize_text_field( $_GET['is_front'] ) : false;
					$item     = array(
						'post_type'   => $post_type,
						'post_title'  => html_entity_decode( $title ),
						'post_status' => "publish",
					);

				
					
					  $result['id'] = wp_insert_post( $item );

						
					 if( $post_type == 'stm-questions'){
						update_post_meta( $result['id'], 'question_explanation', $line[$question_result_explanation] );
						update_post_meta( $result['id'], 'type', $line[$question_type] );
						update_post_meta( $result['id'], 'answers', $answer_correct);
						
						
					}
					

					 // add question category if was sent 
					if ( null !== $category_ids ) {
						wp_set_object_terms( $result['id'], $category_ids, 'stm_lms_question_taxonomy' );
					}

					do_action(
						'stm_lms_item_added',
						array(
							'id'    => $result['id'],
							'front' => $is_front,
						)
					);

					$result['categories'] = wp_get_post_terms( $result['id'], 'stm_lms_question_taxonomy' );
					$result['is_edit']    = false;
					$result['title']      = html_entity_decode( get_the_title( $result['id'] ) );
					$result['post_type']  = $post_type;
					$result['edit_link']  = html_entity_decode( get_edit_post_link( $result['id'] ) );

					$result = apply_filters( 'stm_lms_wpcfto_create_question', $result, array( $post_type ) );

					do_action(
						'stm_lms_item_question_added',
						array(
							'id'    => $result['id'],
							'front' => $is_front,
						)
					);
					 
				
				 }
				 
				$questions_ids[] =  $result['id']; 
				 
				  
			} while (($line = fgetcsv($handle)) !== false && $row < 50);
			fclose($handle);
			
			update_post_meta( $quiz_id , 'questions', implode(",", $questions_ids) );
			
			
			// die();
		}
		
	
			echo "<meta http-equiv='refresh' content='0;url=/wp-admin/post.php?post=".$quiz_id."&action=edit'>";
	}
	
	
	add_action( 'admin_post_nopriv_import_csv_lms_cours_lesson', 'import_csv_lms_cours_lesson' );
	add_action( 'admin_post_import_csv_lms_cours_lesson', 'import_csv_lms_cours_lesson' );
	
	function import_csv_lms_cours_lesson() {
	
		$row = 0;
		$available_post_types       = array( 'stm-lessons', 'stm-quizzes' );		
		if (isset($_POST['import'])) {
			$original = ini_get("auto_detect_line_endings");
			ini_set("auto_detect_line_endings", true);
			$handle = fopen($_FILES['upload_csv']['tmp_name'], "r");
			ini_set("auto_detect_line_endings", $original);
			// echo "<pre>"; 
			
			$flag = true;
			$column = array_map('nestedMetakey', (array)fgetcsv($handle));
			// print_r($column);
			$curriculum_meta_save = null;
			
			$course_id = $_POST['id'];	
			$curriculum_ids = get_post_meta( $course_id, 'curriculum', true );
			$curriculum_ids  = str_replace(',,',',',$curriculum_ids);
			// $curriculum_ids  = ",7482,7483,7484,Ser,7486,7487,7488,7489,";
			$get_section_num = ($_POST['section_num'] - 1);
			
			/* 	echo $curriculum_ids;
				echo "<br>"; */
				$ids  = $curriculum_ids;
				/* echo $ids;
				die(); */
				
			
				$curriculum = STM_LMS_Lesson::create_sections( explode( ',', $ids ) );
				
				$curriculum_object =  array_values( $curriculum );
			/* 
			print_r($curriculum_object);
		die(); */
			
			
			do {
				  if($flag) { $flag = false; continue; }
				  usleep(2); 
				  /*  echo "<pre>"; 
				  print_r($line);
				  echo "</pre>";  */
				  
				  $item_title = array_search('item-title', $column);
				  $post_type = array_search('post-type', $column);
				  $status = array_search('status', $column);
				  $lesson_type = array_search('lesson-type', $column);
				  $lesson_type = array_search('lesson-type', $column);
				  $duration = array_search('duration', $column);
				  $duration_measure = array_search('duration-measure', $column);
				  $lesson_preview = array_search('lesson-preview', $column);
				  $frontend_description = array_search('frontend-description', $column);
				  $lesson_video_url = array_search('lesson-video-url', $column);
				  $quiz_style = array_search('quiz-style', $column);
				  $show_correct_answer = array_search('show-correct-answer', $column);
				  $passing_grade = array_search('passing-grade', $column);
				  $points_total_cut_after_re_take = array_search('points-total-cut-after-re-take', $column);
				  $randomize_questions = array_search('randomize-questions', $column);
				  
				 
				$post_type = $line[$post_type];
				$item_title = $line[$item_title];	 
			
				// echo $item_title;
				// check_ajax_referer( 'stm_curriculum_create_item', 'nonce' );

				// Check if data passed
				if ( empty( sanitize_text_field( $post_type ) ) ) {
					wp_send_json(
						array(
							'error'   => true,
							'message' => esc_html__( 'Post Type is required', 'masterstudy-lms-learning-management-system' ),
						)
					);
				}

				// Check if data passed
				if ( empty( $item_title ) ) {
					wp_send_json(
						array(
							'error'   => true,
							'message' => esc_html__( 'Title is required', 'masterstudy-lms-learning-management-system' ),
						)
					);
				}

				$category_ids = null; // Question categories
				$post_type    = sanitize_text_field( $post_type );
				$title        = sanitize_text_field( urldecode( $item_title ) );
				
			

				// Check if available post type
				if ( ! in_array( $post_type, $available_post_types ) ) {
					wp_send_json(
						array(
							'error'   => true,
							'message' => esc_html__( 'Wrong post type', 'masterstudy-lms-learning-management-system' ),
						)
					);
				}

				$result   = array();
				$is_front = (bool) ( ! empty( $_GET['is_front'] ) ) ? sanitize_text_field( $_GET['is_front'] ) : false;
				$item     = array(
					'post_type'   => $post_type,
					'post_title'  => html_entity_decode( $title ),
					'post_status' => "publish",
				);

			
				/*  echo $line[$lesson_type]; echo "<br>";
				 echo $line[$quiz_style]; echo "<br>"; */
			/* 	echo $line[$duration]; echo "<br>";
				echo $line[$lesson_preview]; echo "<br>";
				echo $line[$frontend_description]; echo "<br>";
				echo $line[$lesson_video_url]; echo "<br>";
				
				echo $line[$duration_measure]; echo "<br>";
				echo $line[$show_correct_answer]; echo "<br>";
				echo $line[$passing_grade]; echo "<br>";
				echo $line[$passing_grade]; echo "<br>";
				echo $line[$points_total_cut_after_re_take]; echo "<br>";
				echo $line[$randomize_questions]; */
			// die();
			 
				
				 $result['id'] = wp_insert_post( $item );

					
				 if( $post_type == 'stm-lessons'){
					update_post_meta( $result['id'], 'type', $line[$lesson_type] );
					update_post_meta( $result['id'], 'duration', $line[$duration] );
					update_post_meta( $result['id'], 'preview', $line[$lesson_preview] );
					update_post_meta( $result['id'], 'lesson_excerpt', $line[$frontend_description] );
					update_post_meta( $result['id'], 'lesson_video_url', $line[$lesson_video_url] );
				}
				if( $post_type == 'stm-quizzes'){
					update_post_meta( $result['id'], 'quiz_style', $line[$quiz_style] );
					update_post_meta( $result['id'], 'lesson_excerpt', $line[$frontend_description] );
					update_post_meta( $result['id'], 'duration', $line[$duration] );
					update_post_meta( $result['id'], 'duration_measure', $line[$duration_measure] );
					update_post_meta( $result['id'], 'correct_answer', $line[$show_correct_answer] );
					update_post_meta( $result['id'], 'passing_grade', $line[$passing_grade] );
					update_post_meta( $result['id'], 're_take_cut', $line[$points_total_cut_after_re_take] );
					update_post_meta( $result['id'], 'random_questions', $line[$randomize_questions] );
			
				} 

				 // add question category if was sent 
				if ( null !== $category_ids ) {
					wp_set_object_terms( $result['id'], $category_ids, 'stm_lms_question_taxonomy' );
				}

				do_action(
					'stm_lms_item_added',
					array(
						'id'    => $result['id'],
						'front' => $is_front,
					)
				);

				$result['categories'] = wp_get_post_terms( $result['id'], 'stm_lms_question_taxonomy' );
				$result['is_edit']    = false;
				$result['title']      = html_entity_decode( get_the_title( $result['id'] ) );
				$result['post_type']  = $post_type;
				$result['edit_link']  = html_entity_decode( get_edit_post_link( $result['id'] ) );

				$result = apply_filters( 'stm_lms_wpcfto_create_question', $result, array( $post_type ) );

				do_action(
					'stm_lms_item_question_added',
					array(
						'id'    => $result['id'],
						'front' => $is_front,
					)
				);
				
				
				
				
				// print_r($_POST);
				// print_r($result);
				
				
			
				
				$curriculum_object[$get_section_num]['items'][] =  $result['id']; 
				
				 
				
				
				
				
				
					
					
					   
					$row++;
				  unset($line);
				  flush();
				  ob_flush(); 
		} while (($line = fgetcsv($handle)) !== false && $row < 50);
		
		fclose($handle);
		
		echo "<pre>";
		// print_r($curriculum_object);
		$data_merge_title_obj = array();
		
	 	foreach($curriculum_object as $data){
			
			
			/* if(empty($data['title']))$curriculum_meta_save .= ",";
			
			if(empty(implode(",", $data['items']))) */
			// $curriculum_meta_save .=   ",";
			
			
			/* if(!empty($data['title'])){
				$curriculum_meta_save .= ",".$data['title'] ;
			}
			else{
				// $curriculum_meta_save .= ",";
			}
			
			
			if(!empty(implode(",", $data['items']))){
				$curriculum_meta_save .=   ",".implode(",", $data['items']);
			}
			 */
			array_unshift($data['items'], $data['title']);
			
			foreach($data['items'] as $data){
				$data_merge_title_obj[] = $data;
			}
			
		}
		
		// print_r($data_merge_title_obj);
		update_post_meta( $course_id, 'curriculum', implode(",", $data_merge_title_obj) );
		// echo $curriculum_meta_save; 
		
		
		// die();
		
		
		
		
	} 
	
	
		echo "<meta http-equiv='refresh' content='0;url=/wp-admin/post.php?post=".$course_id."&action=edit'>";
}
	
	
/**
 * Get the column of spreadsheet CSV to array filter.
 *
 * @param  string   $value       The array column of csv
 * @return String                The new key of column that filter as array key for finding value
 */
function nestedMetakey($value) {
	if (is_array($value)) {
		return array_map('nestedLowercase', $value);
	}

	return preg_replace('/\s+/', '-', ltrim(strtolower($value)));
}

/**
 * Insert an attachment from a URL address.
 *
 * @param  string   $url            The URL address.
 * @param  int|null $parent_post_id The parent post ID (Optional).
 * @return int|false                The attachment ID on success. False on failure.
 */
function wp_insert_attachment_from_url( $url, $parent_post_id = null ) {

	if ( ! class_exists( 'WP_Http' ) ) {
		require_once ABSPATH . WPINC . '/class-http.php';
	}

	$http     = new WP_Http();
	$response = $http->request( $url );
	if ( 200 !== $response['response']['code'] ) {
		return false;
	}

	$upload = wp_upload_bits( basename( $url ), null, $response['body'] );
	if ( ! empty( $upload['error'] ) ) {
		return false;
	}

	$file_path        = $upload['file'];
	$file_name        = basename( $file_path );
	$file_type        = wp_check_filetype( $file_name, null );
	$attachment_title = sanitize_file_name( pathinfo( $file_name, PATHINFO_FILENAME ) );
	$wp_upload_dir    = wp_upload_dir();

	$post_info = array(
		'guid'           => $wp_upload_dir['url'] . '/' . $file_name,
		'post_mime_type' => $file_type['type'],
		'post_title'     => $attachment_title,
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Create the attachment.
	$attach_id = wp_insert_attachment( $post_info, $file_path, $parent_post_id );

	// Include image.php.
	require_once ABSPATH . 'wp-admin/includes/image.php';

	// Generate the attachment metadata.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );

	// Assign metadata to attachment.
	wp_update_attachment_metadata( $attach_id, $attach_data );
	
	// echo "<pre>";
	$upload_dir = wp_upload_dir();
	// print_r($attach_data);
	
	$path = null;
	if(isset($attach_data['file']))$path = $upload_dir['baseurl'] ."/".$attach_data['file'];
	// die();
	return array('id' => $attach_id , 'url' => $path  );

}	
?>
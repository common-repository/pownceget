<?php
/*
Plugin Name: PownceGet
Plugin URI: http://fushji.netsons.org/?page_id=9
Description: Add a sidebar widget to display Pownce notes 
Version: 0.1
Author: Antonio Perrone
Author URI: http://fushji.netsons.org/?page_id=9
License: GPL
*/

function widget_Pownce_init() {

	if ( !function_exists('register_sidebar_widget') )
		return;

	function widget_Pownce($args) {

		extract($args);

		$title = get_option('title');
		$user = get_option('user');
		$limit = get_option('limit');
		
		$doc = new DOMDocument();
  		$doc->load( 'http://api.pownce.com/2.0/users/fushji.xml' );
  
		$notes = $doc->getElementsByTagName( "user" );
		foreach ($notes as $note){
		  	$profiles = $note->getElementsByTagName("profile_photo_urls");
  			foreach ($profiles as $profile)
  				$tiny = $profile->getElementsByTagName("tiny_photo_url")->item(0)->nodeValue;
		}
		
		$url = 'http://api.pownce.com/2.0/note_lists/'.$user.'.xml?limit='.$limit;			
		
		echo '<div id="div">'
              .$before_title.'<img src="'.$tiny.'"/>'.$title.$after_title;
		echo '<ul id="list">';
		
		$docx = new DOMDocument();
  		$docx->load( $url );
  
		$notes = $docx->getElementsByTagName( "note" );
  		foreach( $notes as $note ) {
  			$bodies = $note->getElementsByTagName( "body" );
		  	$body = $bodies->item(0)->nodeValue;
			$links = $note->getElementsByTagName( "permalink" );
		  	$link = $links->item(0)->nodeValue;
		  	$times = $note->getElementsByTagName( "display_since" );
		  	$time = $times->item(0)->nodeValue;
			echo '<li><a href="'.$link.'">'.$body.'</a><br>'.$time.'</li>';
		}	
	
		echo '</ul></div>';
	}

	// Settings form
	function widget_Pownce_control() {

		
        // form posted?
		if ( $_POST['Pownce-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			
			update_option('title', $_POST['Pownce-title']);
			update_option('user', $_POST['Pownce-account']);
			update_option('limit', $_POST['Pownce-limit']);		
		}

		$title = get_option('title');
		$user = get_option('user');
		$limit = get_option('limit');

		// The form fields
		echo '<p style="text-align:right;">
				<label for="Pownce-account">' . __('Account:') . '
				<input style="width: 200px;" id="Pownce-account" name="Pownce-account" type="text" value="'.$user.'" />
				</label></p>';
		echo '<p style="text-align:right;">
				<label for="Pownce-title">' . __('Title:') . '
				<input style="width: 200px;" id="Pownce-title" name="Pownce-title" type="text" value="'.$title.'" />
				</label></p>';
		echo '<p style="text-align:right;">
				<label for="Pownce-limit">' . __('Show:') . '
				<input style="width: 200px;" id="Pownce-limit" name="Pownce-limit" type="text" value="'.$limit.'" />
				</label></p>';
		echo '<input type="hidden" id="Pownce-submit" name="Pownce-submit" value="1" />';
	}


	// Register widget for use
	register_sidebar_widget(array('Pownce', 'widgets'), 'widget_Pownce');

	register_widget_control(array('Pownce', 'widgets'), 'widget_Pownce_control', 300, 200);
}

// Run code and init
add_action('widgets_init', 'widget_Pownce_init');

?>
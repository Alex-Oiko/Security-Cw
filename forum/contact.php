<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;
regenerate_session();
/* Above page-specific */
$document->header("Contact");

/* ================= Page Specific ====================== */

//Include contact functions
require_once("./functions/core/contact.php");

// Send message
if (isset($_POST['subject']) && isset($_POST['message'])) {
	if(check_tokens($_POST['token'],$_SESSION['token'])==1){
	
	//Empty
	if ($_POST['subject'] == "" || $_POST['message'] == "") {
		fatal_user_error("One or more contact fields were left blank.", "Please try again");
	}
	//Make safe
	$subject = make_safe("text",$_POST['subject']);
	$message = make_safe("text",$_POST['message']);

	//Send
	send_message($subject,$message,$core->get('contact_email'),$user->get('user_email'));
	$document->append_template("simple_template",array('title'=>"Contact",'text'=>"Your message has been sent. 
		Thankyou for using the contact form"));
	}
	else{
		fatal_user_error("Something went wrong.","You were not redirected here correctly. Please try again");
	}
}
// Display contact form
else { 
	contact_form(); 
}

/* End page-specific */
$document->footer();
$document->output();

?>

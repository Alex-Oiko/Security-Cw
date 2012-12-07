<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;
regenerate_session();
/* Above page-specific */
$document->header("User");

/* ================= Page Specific ====================== */

//Include user functions
require_once("./functions/core/user.php");

//Handle validation and input:


//Save profile
if (isset($_POST['user_id'])) {
	//Check for missing data
	if (!isset($_POST['user_id']) || !isset($_POST['user_email']) || !isset($_POST['user_homepage']) || !isset($_POST['user_bio'])) {
		fatal_user_error("Please fill in all the required form components","Incomplete data submitted");
	}
	if($_POST['token']==$_SESSION['token']){
	//Setup user
	$id = make_safe("int",$_POST['user_id']);
	$thisuser = new User($core);
	if (!$thisuser->load_from_userid($id,false)) { 	fatal_user_error("Invalid user","Unable to edit invalid user"); }
	
	//Validate email and homepage
	if (!validate("email",$_POST['user_email'])) { 
		fatal_user_error("Invalid email address","Please ensure you have provided a valid email address"); }
	
	//Set user homepage
	$update['user_homepage'] = $_POST['user_homepage'];	

	//Set user type
	if (isset($_POST['user_type'])) { $update['user_type'] = $_POST['user_type']; }

	//Set uesr email
	$update['user_email'] = make_safe("text",$_POST['user_email']);

	//Set user bio
	$update['user_bio'] = make_safe("text",$_POST['user_bio']);

	//Image upload
	$update['user_picture'] = "";
	}
	else{
		fatal_user_error("Something went wrong", "You were not redirected here correctly. Please try again");

	}
	//New picture URL
	if (isset($_POST['user_imageurl']) && $_POST['user_imageurl'] != "") { 
		if($_POST['token']==$_SESSION['token']){
		if (!validate("url",$_POST['user_imageurl'])) {
			fatal_user_error("Invalid image URL specified","Please go back and try again");
		}
		$fileinfo = getimagesize($_POST['user_imageurl']);
		#checks if the file is an image by applying the getimagesize() function and checking the file extension to see if it matches the accepted formats. The size is also checked
		if(!empty($fileinfo) && preg_match("/(png$ || jpg$ || jpeg$ || gif$)/",basename($_POST['user_imageurl'])) && $_FILES['user_imagefile']['size']<1024^2){
			$update['user_picture'] = make_safe("url",$_POST['user_imageurl']);
		}
		
		else{
				fatal_user_error('The image you have uploaded has a problem','Either it is not in the right format, or it is not a picture');
		}
		}
		else{
				fatal_user_error("Something went wrong", "You were not redirected here correctly. Please try again");

		}
	//New picture upload
	} else if (isset($_FILES['user_imagefile']['type']) && $_FILES['user_imagefile']['name'] != "") {
			if($_POST['token']==$_SESSION['token']){
			#same as before
			$file_info = getimagesize($_FILES['user_imagefile']['tmp_name']);
			if(!empty($file_info) && preg_match("/(png$ || jpg$ || jpeg$ || gif$)/",basename($_FILES['user_imagefile']['name'])) && $_FILES['user_imagefile']['size']<1024^2){
				$update['user_picture'] = $core->do_upload('user_imagefile'); 
			}
			else{
				fatal_user_error('The image you have uploaded has a problem','Either it is not in the right format, or it is not a picture or it is too big');
			}
			}
			else{
				fatal_user_error("Something went wrong", "You were not redirected here correctly. Please try again");
			}
	//No picture change
	} else { unset($update['user_picture']); }

	//Validate password
	if (isset($_POST['user_password1']) && isset($_POST['user_password2'])) {
		if($_POST['token']==$_SESSION['token']){
		if ($_POST['user_password1'] != "" && $_POST['user_password2'] != "") {
			//Check for matching first and second confirm passwords
			if ($_POST['user_password1'] != $_POST['user_password2']) {
				fatal_user_error("Non-matching passwords","The password and the confirmation do not match");
			}
			$dbp = $db->db_prefix;
			//Compare passwords and prepare new password
			$username=$thisuser->get("user_name");
			$salt = $db->query("SELECT salt FROM {$dbp}user WHERE user_name='$username'");
			$salt=$salt->fetch_assoc();
			$salt=$salt['salt'];
			
			$current_password = $thisuser->get("user_password");
			$current_password2 = make_safe("text",$_POST['user_passwordcurrent']);
			if(strlen($current_password2)<8){
				fatal_user_error("New password has too few characters");
			}
			
			$new_password = make_safe("text",$_POST['user_password1']);
			
			//Check for valid password or be an admin			
			if ($current_password == crypt($current_password2,$salt) || $user->is_admin()) {
				$new_password=crypt($new_password,$salt);
				$update['user_password'] = $new_password;
			} else {
				fatal_user_error("Invalid password","The current password you entered was incorrect");	
			}
			}
			}
			else{
				fatal_user_error("Something went wrong", "You were not redirected here correctly. Please try again");

			}
	}

	//Update the user
	$updateduser = update_profile($id,$update);
	show_profile($updateduser);	
}

//Admin edit any profile
else if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == "edit") {
	if($_POST['token']==$_SESSION['token']){
	//Check if admin
	if (!$user->is_admin()) { fatal_user_error("Access denied","You do not have permission to edit this profile"); }

	//Validate input
	$id = make_safe("int",$_GET['id']);
	if (!is_numeric($id)) { fatal_user_error("Invalid user ID specified","Please try again"); }

	//Check if user exists
	$profile = new User($core);
	if (!$profile->load_from_userid($id)) { fatal_user_error("No such user","No user exists with the ID specified"); }	

	//If user exists, edit profile
	edit_profile($profile);
	}
	else{
				fatal_user_error("Something went wrong", "You were not redirected here correctly. Please try again");
	}
}

//View profile
else if (isset($_GET['id'])) {
	//Validate input
	$id = make_safe("int",$_GET['id']);
	if (!is_numeric($id)) { fatal_user_error("Invalid user ID specified","Please try again"); }
	
	//Check if user exists
	$profile = new User($core);
	if (!$profile->load_from_userid($id)) { fatal_user_error("No such user","No user exists with the ID specified"); }

	//If user exists, display profile
	show_profile($profile);
} 

//Edit current profile
else {
	edit_profile($user);
}

/* End page-specific */
$document->footer();
$document->output();


?>

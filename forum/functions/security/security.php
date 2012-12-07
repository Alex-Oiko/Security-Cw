<?php


require('CaptchasDotNet.php');
//Ideally, put any security related functions in this file!
/* Perform validation on text given a type */
function validate($type,$text) {
		switch ($type) {
			//Validation of plain numbers
			case "int":
				return is_numeric($text);
				break;
			//Validation of letters and numbers only
			case "alphanumeric":
				return preg_match("/^[a-zA-Z0-9]*$/",$text);
				break;
			//Validation of emails
			case "email":
				return filter_var($text, FILTER_VALIDATE_EMAIL);
				break;
			//Validation of plain URLs
			case "url":
				return filter_var($text, FILTER_VALIDATE_URL);
				break;
			//Invalid validation
			default:
				fatal_error("Invalid validation type: $type");
		}
	}

/* Take user input and make it safe based on its type */
function make_safe($type,$text) {

	//We should really get around to make some kind of function to make input safe
	//But we'll never get around to it
	if($type = "int" && is_numeric($text)){
		return $text;
	}

	else if($type="text"){
		$text = htmlspecialchars($text);#To prevent XSS
		#$text = mysql_real_escape_string($text);
		return $text;
	}
	else fatal_user_error("Something went wrong wiht your input :/");


}
// Regenerate the session. This function is called in the /function/core/login.php file
function regenerate_session(){
	if(!isset($SESSION['initiated'])){
		session_regenerate_id();
		$_SESSION['initiated']=true;
	}
}

// Computes the salt that is going to be kept in the databse and used to hash the passwords
function compute_salt(){
	$chars='ABCDEFGHIJKLMNOPQRSTVUWXYZabcdefghijklmnopqrstvuwxyz0123456789';
	$salt='';
	for($i=0;$i<10;$i++){
	$salt.=$chars[rand(0,62)];
	}
	return $salt;
}

// Creates a token and adds it to the session. This function is called everytime a form is completed
function make_token(){ 
	$token = sha1(uniqid(rand(),1));
	return $token;
 
}
// This funciton checks the token from the post command and the session token
function check_tokens($post,$session){
	if(isset($post) && $post==$session){
		return 1;
	}
	else return 0;
}

?>

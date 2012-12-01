<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;
regenerate_session();
//check if the user is logged in in order to display the member list
if($user->get('user_type')==0){
	fatal_user_error("You are not authorised to view this page","You need to be registered and logged in to view the member list");
}
else{
	$document->header("User List");

	/* ================= Page Specific ====================== */
	require_once('./functions/core/members.php');

	//Include forum functions
	$userlist['userlist'] = user_list();
	$userlist['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
	$userlist['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / Members</p>';
	
	//Show forums
	$document->append_template("forum_members",$userlist);

	/* End page-specific */
	$document->footer();
	$document->output();
}
?>

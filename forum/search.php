<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;
regenerate_session();
//Check if the user is logged in in order to display the search page
if($user->get('user_type')==0){#check the user_type
	fatal_user_error("You are not authorised to view this page","You need to be registered and logged in to view this page");
}
else{
/* Above page-specific */
	$document->header("Search");

/* ================= Page Specific ====================== */
	require_once('./functions/forum/forms.php');
	require_once('./functions/forum/display.php');

	if (!isset($_GET['do'])) {
	//Show search form
		$search['heading'] = "Search";
		$search['description'] = "Search for posts on the form";
		$search['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
		$search['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
		$search['breadcrumb'] .= 'Search </p>';
		$search['content'] = search_form();
		$document->append_template("forum_form",$search);
	}	 
	else {
	//Do search
		if(check_tokens($_POST['token'],$_SESSION['token'])){
		$text = make_safe("text",$_POST['text']);
		$search['heading'] = "Search Results";
		$search['description'] = "These are the results to your search for \"$text\"";
		$search['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
		$search['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
		$search['breadcrumb'] .= 'Search Results </p>';
		$search['content'] = search_results($text);
		$document->append_template("forum_form",$search);
		}
		else{
			fatal_user_error("You were redirected incorrectly here");
		}
	}


	/* End page-specific */
	$document->footer();
	$document->output();
}
?>

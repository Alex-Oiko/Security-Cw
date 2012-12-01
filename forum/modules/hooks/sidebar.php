<?php

function do_sidebar() {
	/* Hook called page */
	global $core, $document, $user, $db;
 	$captchas = new CaptchasDotNet ('helolex', 'xhaFwUp5l2KsCSTqKjUltUepX2e807    KcPrAL1Iin',
           '/tmp/captchasnet-random-strings','3600',
           'abcdefghkmnopqrstuvwxyz','6',
           '240','80','000088');
	$random = $captchas->random();
	$img = $captchas->image();

	
	$captcha = "<input type='hidden' name='random' value='$random'/>$img<a href='javascript:captchas_image_reload('captchas.net')' height='50' width='50'>Reload Image</a>";
	$query = $core->db->make_query("blocks");
	$query->set_order("block_order ASC");
	$result = $query->execute();

	$blockhtml = "";
	while ($db_block = $result->fetch_assoc()) {
		$block['title'] = $db_block['block_title'];
		$block['content'] = $db_block['block_content'];

		//If PHP block, evaluate
		if ($db_block['block_php']) { 
			ob_start();
			eval($db_block['block_content']);
			$block['content'] = ob_get_contents();
			ob_end_clean();		
		}
		if ($block['content'] != "") { $blockhtml .= $document->get_template("block",$block); }	
	}

	//Generate sidebar template
	$variables['blocks'] = $blockhtml;
	$variables['captcha']=$captcha;
	$document->append_template("sidebar",$variables);
}

?>

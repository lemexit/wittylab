<?php 
/**
 * ====================================================================================
 *
 *
 * @author Emrul (https://lemexit.com)
 * @link https://lemexit.com 
 * @license https://lemexit.com/license
 * @package PremiumMediaScript
 * @subpackage API Handler
 */

	if(!file_exists("includes/Config.php")){
		header("Location: install.php");
		exit;
	}
	include("includes/Config.php");
	// Run the app
	$app->run();	
?>
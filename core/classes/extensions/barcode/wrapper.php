<?php 

/*
	AUTHOR: Walter Cattebeke
	DATE: 08-July-2004
	EMAIL: cachiweb@telesurf.com.py
	LICENSE: This code is free. You can use it and/or modify it.
		I only want to be mentioned and notified if you intend to do either.

	Please read the notes.txt !
*/

/*
	This file is required when you need to embed a generated barcode into an IMG tag. Sort of a wrapper.
	Just put something like this into your HTML code
	<IMG SRC="wrapper.php?p_text=cachique&p_xDim=1&p_w2n=3&p_invert=F&p_charHeight=50" ALT="">
	See index.php for an exmple.
*/	

	require("../classes/extensions/barcode/core.php");
	
	// default values
	if (!isset($p_text)){
		$p_text = "123";
	} else {
		$p_text = rawurldecode($p_text);
	}


	if (!isset($p_bcType)){
		$p_bcType = 2;
	} 
	if (!isset($p_xDim)){
		$p_xDim = 1;
	} 
	if (!isset($p_w2n)){
		$p_w2n = 3;
	} 
	if (!isset($p_charGap)){
		$p_charGap = $p_xDim;
	} 
	if (!isset($p_invert)){
		$p_invert = "N";
	}
	if (!isset($p_charHeight)){
		$p_charHeight = 50;
	} 
	if (!isset($p_type)){
		$p_type = 1;
	} 
	if (!isset($p_label)){
		$p_label = "N";
	} 
	if (!isset($p_rotAngle)){
		$p_rotAngle = 0;
	} 
	if (!isset($p_toFile)){
		$p_toFile = "N";
	} 
	if (!isset($p_fileName)){
		$p_fileName = "code39";
	} 
	$p_checkDigit = "N";
	
	if ($p_invert == "N"){
		$p_inverted = FALSE;
	} else {
		$p_inverted = TRUE;
	}
	if ($p_toFile == "N"){
		$p_2File = FALSE;
	} else {
		$p_2File = TRUE;
	}
	if ($p_label == "N"){
		$p_textLabel = FALSE;
	} else {
		$p_textLabel = TRUE;
	}
	if ($p_checkDigit == "N"){
		$p_ck = FALSE;
	} else {
		$p_ck = TRUE;
	}

	barCode(
		$p_bcType,
		$p_text,
		$p_xDim,
		$p_w2n,
		$p_charGap,
		$p_inverted,
		$p_charHeight,
		$p_type,
		$p_textLabel,
		$p_rotAngle,
		$p_ck,
		$p_2File,
		$p_fileName);

?>

<?php 
/*
	AUTHOR: Walter Cattebeke
	DATE: 08-July-2004
	EMAIL: cachiweb@telesurf.com.py
	LICENSE: This code is free. You can use it and/or modify it.
		I only want to be mentioned and notified if you intend to do either.

	Please read the notes.txt !
*/

	require("../classes/extensions/barcode/defs.inc");

/*
	Core function. It draws a barcode representation of a string passed as parameter. Support for
	some image formats. Some formats work, others don't.
	Image manipulation through GD libraries.
*/

	function barCode(
		$p_barcodeType, // Type of barcode to be generated
		$p_origText, // Text to be generated as barcode
		$p_xDim, // smallest ellement width
		$p_w2n, // wide to narrow factor
		$p_charGap, // Intercharacter gap width. usually the same as xDim
		$p_invert, // Whether or not invert starting bar colors 
		$p_charHeight, // height in pixels of a single character
		$p_imgType, // image type output
		$p_drawLabel, // Whether or not include a text label below barcode
		$p_rotationAngle, // Barcode Image rotation angle 
		$p_check, // Whether or not include check digit 
		$p_toFile, // Whether or not write to file
		$p_fileName // File name to use in case of writing to file
	) {

		if ($p_rotationAngle < BC_ROTATE_0 || $p_rotationAngle > BC_ROTATE_270){
			$p_rotationAngle = BC_ROTATE_0;
		}
		$p_rotationAngle = $p_rotationAngle * 90;

		$font = 5; // font type. GD dependent

		$p_w2n = checkWideToNarrow($p_barcodeType, $p_w2n);
		$p_charGap = checkCharGap($p_barcodeType, $p_charGap);
		$p_check = checkCheckDigit($p_barcodeType, $p_check);
		$quietZone = 10 * $p_xDim; // safe white zone before and after the barcode

		if ($p_check) {
			$textCheck = getCheckDigit($p_barcodeType, $p_origText);
		}
		else {
			$textCheck = $p_origText;
		}
		$text2bar = getBarcodeText($p_barcodeType, $textCheck); // format text 
		$charCount = getCharCount($p_barcodeType, $text2bar); // number of symbols

		// image height & width
		$imgWidth = getBarcodeLength($p_barcodeType, $text2bar, $p_xDim, $p_w2n, $quietZone, $p_charGap);
		$imgHeight = $p_charHeight ;

		$hMidHeight = floor($p_charHeight / 2);
		$hTrackWidth = floor($p_charHeight / 4);
		if (($p_charHeight - $hTrackWidth) % 2 != 0){
			$hTrackWidth = $hTrackWidth + 1;
		}
		$hAscWidth = floor(($p_charHeight - $hTrackWidth) / 2);

		if ($p_drawLabel) { // increase image height when adding label
			$imgHeight = $imgHeight + imagefontheight($font);
		}

		$extraWidth = imagefontwidth($font) * strlen($p_origText) - $imgWidth;
		if ($extraWidth > 0) {
			$quietZone = $quietZone + $extraWidth / 2 + 1;
			$imgWidth = getBarcodeLength($p_barcodeType, $text2bar, $p_xDim, $p_w2n, $quietZone, $p_charGap);
		}

		$im = @imagecreate($imgWidth, $imgHeight)
			or die("Cannot Initialize new GD image stream");

		$xPos = $quietZone; // starting bar X position
		$bgColor = imagecolorallocate($im, 255, 255, 255); // white background
		$blackColor = imagecolorallocate($im, 0, 0, 0);
		$whiteColor = imagecolorallocate($im, 255, 255, 255);

		$black = !$p_invert; // what color is the first bar?

//var_dump($text2bar);

		for($j=0;$j<$charCount;$j++){ // traverse string
			$currChar = getSpec($p_barcodeType, $text2bar, $j); // get symbol spec.
			for ($i=0;$i<strlen($currChar);$i++) { // traverse symbol spec.
				if ($black){ // what color is next bar?
					$barColor = $blackColor;
				}
				else {
					$barColor = $whiteColor;
				}
				if ($currChar[$i] == "n"){ // draw a narrow bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = 0;
					$yPos1 = $p_charHeight - 1;
				} elseif ($currChar[$i] == "w") { // draw a wide bar
					$xPos1 = $xPos + $p_xDim * $p_w2n - 1;
					$yPos = 0;
					$yPos1 = $p_charHeight - 1;
				} elseif ($currChar[$i] == "1") { // draw a narrow black bar
					$xPos1 = $xPos + $p_xDim - 1;
					$barColor = $p_invert?$whiteColor:$blackColor;
					$yPos = 0;
					$yPos1 = $p_charHeight - 1;
				} elseif ($currChar[$i] == "0") { // draw a narrow white space
					$xPos1 = $xPos + $p_xDim - 1;
					$barColor = $p_invert?$blackColor:$whiteColor;
					$yPos = 0;
					$yPos1 = $p_charHeight - 1;
				} elseif ($currChar[$i] == "f") { // draw a full vertical bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = 0;
					$yPos1 = $p_charHeight - 1;
				} elseif ($currChar[$i] == "u") { // draw a mid upper vertical bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = 0;
					$yPos1 = $hMidHeight - 1;
				} elseif ($currChar[$i] == "l") { // draw a mid lower vertical bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = $hMidHeight;
					$yPos1 = $p_charHeight - 1;
				} elseif ($currChar[$i] == "t") { // draw a track only vertical bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = $hAscWidth;
					$yPos1 = $hAscWidth + $hTrackWidth - 1;
				} elseif ($currChar[$i] == "a") { // draw a track & ascender vertical bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = 0;
					$yPos1 = $hAscWidth + $hTrackWidth - 1;
				} elseif ($currChar[$i] == "d") { // draw a track & descender vertical bar
					$xPos1 = $xPos + $p_xDim - 1;
					$yPos = $hAscWidth;
					$yPos1 = $p_charHeight - 1;
				}
				if ($currChar[$i] != " ") {
					imagefilledrectangle($im, $xPos , $yPos, $xPos1, $yPos1, $barColor);
					$black = !$black;
					$xPos = $xPos1 + 1;
				}
			}
			// draw intercharacter gap if gap lenght > 0
			if ($j < $charCount - 1 && $p_charGap > 0) { // do not draw last gap
				if ($black){ // it is supposed to be always false but you never know
					$barColor = $blackColor;
				}
				else {
					$barColor = $whiteColor;
				}
				$xPos1 = $xPos + $p_charGap - 1;
				$yPos = 0;
				$yPos1 = $p_charHeight - 1;
				imagefilledrectangle($im, $xPos, $yPos, $xPos1, $yPos1, $barColor);
				$black = !$black;
				$xPos = $xPos1 + 1;
			}

		}
		if ($p_drawLabel) { // draw text label
			$imgTextWidth = imagefontwidth($font) * strlen($p_origText);
			$xText = ($imgWidth - $imgTextWidth) / 2;
			imagestring($im, $font, $xText, $p_charHeight, $p_origText, $blackColor );
		}

		$functionName = barcodeImgFunction($p_imgType); // get php image output function

		if ($p_toFile){
			$fileExt = barcodeFileExt($p_imgType); // get file extension
			$functionName(imagerotate($im, $p_rotationAngle, $whiteColor), $p_fileName . "." . $fileExt); // Automatic image type output
		} else {
			$headerContent = barcodeHeaderContent($p_imgType); // get header type
			header("Content-type: $headerContent"); // Automatic content type output
			$functionName(imagerotate($im, $p_rotationAngle, $whiteColor)); // Automatic image type output
		}
		imagedestroy($im); // free image resource
	}
	
	// uncoment below for direct testing this file
	// will not work as a module if left uncommented !!!!

	// Writing to file "test" a PNG barcode image of "1234567890" with label
	// barCode(BC_TYPE_CODE39, "1234567890", 1, 3, 1, FALSE, 100, BC_IMG_TYPE_PNG, TRUE, BC_ROTATE_0, TRUE, TRUE, "test");
?>

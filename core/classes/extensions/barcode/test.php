<?php 

	require("defs.inc");


	echo "<pre>\n";

	$type = BC_TYPE_POSTNET;
	$xDim = 1;
	$gap = $xDim;
	$w2n = 3;
	$qz = 10 * $xDim;
	$check = false;
	$check = checkCheckDigit($type, $check);
	$tOrig = "801221905";
	if ($check){
		$txtCK = getCheckDigit($type, $tOrig);
	}
	else {
		$txtCK = $tOrig;
	}	
	$text = getBarcodeText($type, $txtCK);
	$w2n = checkWideToNarrow($type, $w2n);
	$gap = checkCharGap($type, $gap);
	$iwide = getBarcodeLength($type, $text, $xDim, $w2n, $qz, $gap);
	$len = getCharCount($type, $text);
	echo  "TEXT: '$tOrig'<br>";
	echo  "TEXTCK: '$txtCK'<br>";
	echo  "TEXT2BAR: '$text'<br>";
	echo "XDIM: $xDim<br>";
	echo "W2N: $w2n<br>";
	echo "GAP: $gap<br>";
	echo "QZ: $qz<br>";
	echo "IMG WIDTH: $iwide<br>";
	echo "LEN: $len<br>";
	for ($i=0; $i<$len; $i++){
		echo "'" . getSpec($type, $text, $i) . "'<br>";
	}
	echo "</pre>\n";

?>
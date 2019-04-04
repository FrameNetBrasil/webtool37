<?php 
/*
	AUTHOR: Walter Cattebeke
	DATE: 08-July-2004
	EMAIL: cachiweb@telesurf.com.py
	LICENSE: This code is free. You can use it and/or modify it.
		I only want to be mentioned and notified if you intend to do either.

	Please read the notes.txt !
*/

	require("core.php");
?>


<?php if ($p_submit == "CONVERT") {?>

<?php 
	if (!isset($p_text)){
		$p_textEnc = "1234567890";
	} else {
		$p_textEnc = rawurlencode($p_text);
	}

	if (!isset($p_xDim)){
		$p_xDim = 1;
	}
	if (!isset($p_w2n)){
		$p_w2n = 3;
	}
	if (!isset($p_charHeight)){
		$p_charHeight = 50;
	}
	
	$p_charGap = $p_xDim;


	$msg="";
	if (!safeStr($p_bcType, $p_text)){
		$msg = "Non-Valid characters inside string";
	}

	$dest = "wrapper.php?p_bcType=$p_bcType&p_text=$p_textEnc" . 
		"&p_xDim=$p_xDim&p_w2n=$p_w2n&p_charGap=$p_charGap&p_invert=$p_invert&p_charHeight=$p_charHeight" .
		"&p_type=$p_type&p_label=$p_label&p_rotAngle=$p_rotAngle&p_checkDigit=$p_checkDigit"
?>



<HTML>
<HEAD>
<TITLE>Barcode Result Page V 1.1 - Cachique</TITLE>
</HEAD>
<BODY>
<h2 align=center>Bar Code Result<br>
<IMG SRC="wrapper.php?p_text=BAR%20CODE%20RESULT" ALT="">
</h2>
<TABLE border=1>
<TR>
	<TD>Text</TD>
	<TD align=center><?php echo $p_text; ?></TD>
</TR>
<TR>
	<TD colspan=2 align=center bgcolor=cyan>
	<br>
	<?php if ($msg != ""){ ?>
		<?php echo $msg; ?>
	<?php } else { ?>
		&nbsp;
		<IMG SRC="<?php echo $dest;?>" ALT="<?php echo strtoupper($p_text); ?>">
		&nbsp;
	<?php } ?>
	<br>
	<br>
	</TD>
</TR>
<TR>
	<TD colspan=2 align=center>
		<a href="javascript: history.go(-1)">Back</a>
	</TD>
</TR>
</TABLE>
</BODY>
</HTML>



<?php } else { ?>

<HTML>
<HEAD>
<TITLE>Barcode Test Page V 1.1 - Cachique</TITLE>
</HEAD>
<BODY>
<h2 align=center>Bar Code Test<br>
<IMG SRC="wrapper.php?p_text=BAR%20CODE%20TEST" ALT="">
</h2>
<FORM METHOD=POST ACTION="index.php">
<TABLE border=1>
<TR>
	<TD>Text</TD>
	<TD><INPUT TYPE="text" NAME="p_text" VALUE="1234567890" maxlength="20" size=25></TD>
</TR>
<TR>
	<TD>Barcode Type</TD>
	<TD>
		<SELECT NAME="p_bcType">
			<OPTION value="1" SELECTED>Code 39</OPTION>
			<OPTION value="2">Interleave 25</OPTION>
			<OPTION value="3">Standard 25</OPTION>
			<OPTION value="4">Code 93</OPTION>
			<OPTION value="5">Royal Mail 4-State</OPTION>
			<OPTION value="6">PostNet</OPTION>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD>Bar Width</TD>
	<TD>
		<SELECT NAME="p_xDim">
			<OPTION value="1">Small</OPTION>
			<OPTION value="2" SELECTED>Medium</OPTION>
			<OPTION value="3">Large</OPTION>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD>Bar Height</TD>
	<TD>
		<SELECT NAME="p_charHeight">
			<OPTION value="50">Small</OPTION>
			<OPTION value="100" SELECTED>Medium</OPTION>
			<OPTION value="150">Large</OPTION>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD>Wide to Narrow</TD>
	<TD>
		<INPUT TYPE="radio" NAME="p_w2n" value="2">x2
		<INPUT TYPE="radio" NAME="p_w2n" value="3" CHECKED>x3
	</TD>
</TR>
<TR>
	<TD>Image Type</TD>
	<TD>
		<SELECT NAME="p_type">
			<OPTION value="1" SELECTED>Png</OPTION>
			<OPTION value="2">Jpg</OPTION>
			<?php if (function_exists("imagegif")) { ?>
				<OPTION value="3">Gif</OPTION>
			<?php } ?>
			<OPTION value="4">Wbmp</OPTION>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD>Label?</TD>
	<TD>
		<INPUT TYPE="radio" NAME="p_label" value="Y" CHECKED>Yes
		<INPUT TYPE="radio" NAME="p_label" value="N">No
	</TD>
</TR>
<TR>
	<TD>Inverted?</TD>
	<TD>
		<INPUT TYPE="radio" NAME="p_invert" value="Y">Yes
		<INPUT TYPE="radio" NAME="p_invert" value="N" CHECKED>No
	</TD>
</TR>
<TR>
	<TD>Check Digit?</TD>
	<TD>
		<INPUT TYPE="radio" NAME="p_checkDigit" value="Y">Yes
		<INPUT TYPE="radio" NAME="p_checkDigit" value="N" CHECKED>No
	</TD>
</TR>
<TR>
	<TD>Rotate?</TD>
	<TD><SELECT NAME="p_rotAngle">
			<OPTION value="0" SELECTED>No</OPTION>
			<OPTION value="1">90 Deg.</OPTION>
			<OPTION value="2">180 Deg.</OPTION>
			<OPTION value="3">270 Deg.</OPTION>
		</SELECT>
	</TD>
</TR>
<TR>
	<TD colspan=2 align=center>
		<INPUT TYPE="submit" NAME="p_submit" value="CONVERT" >
		<INPUT TYPE="reset" NAME="p_reset" value="RESET">
	</TD>
</TR>
</TABLE>
</FORM>
<h5>Read The <a href="notes.txt">Notes</a> file!!!</h5>
</BODY>
</HTML>

<?php } ?>


<?php 
    $MIOLO->uses('extensions/barcode/core.php');

class MBarcode extends MControl
{
	public	$p_bcType; // Type of barcode to be generated
	public	$p_text; // Text to be generated as barcode
    public  $p_textLabel;
	public	$p_xDim; // smallest ellement width
	public	$p_w2n; // wide to narrow factor
	public	$p_charGap; // Intercharacter gap width. usually the same as xDim
	public	$p_invert; // Whether or not invert starting bar colors 
    public  $p_inverted;
	public	$p_charHeight; // height in pixels of a single character
	public	$p_type; // image type output
	public	$p_label; // Whether or not include a text label below barcode
	public	$p_rotAngle; // Barcode Image rotation angle 
	public	$p_checkDigit; // Whether or not include check digit 
    public  $p_ck;
	public	$p_toFile; // Whether or not write to file
    public  $p_2File;
	public	$p_fileName; // File name to use in case of writing to file
	

    function __construct($p_text = "123",$p_bcType = 1,$p_xDim = 2,$p_w2n = 2,$p_charGap = 2,$p_invert = "N",
		$p_charHeight = 50,$p_type = 1,$p_label = "N",$p_rotAngle = 0,$p_checkDigit="N",$p_toFile = "N",$p_fileName = "code39")
    {

		$this->p_text = rawurldecode($p_text);
		$this->p_bcType = 1;
		$this->p_xDim = 2;
		$this->p_w2n = 2;
		$this->p_charGap = $this->p_xDim;
		$this->p_invert = "N";
		$this->p_charHeight = 50;
		$this->p_type = 1;
		$this->p_label = "N";
		$this->p_rotAngle = 0;
		$this->p_toFile = "N";
		$this->p_fileName = "code39";
    	if ($this->p_invert == "N"){
	    	$this->p_inverted = FALSE;
    	} else {
    		$this->p_inverted = TRUE;
	    }
    	if ($this->p_toFile == "N"){
	    	$this->p_2File = FALSE;
    	} else {
    		$this->p_2File = TRUE;
    	}
    	if ($this->p_label == "N"){
    		$this->p_textLabel = FALSE;
    	} else {
    		$this->p_textLabel = TRUE;
    	}
    	if ($this->p_checkDigit == "N"){
    		$this->p_ck = FALSE;
    	} else {
    		$this->p_ck = TRUE;
    	}
}

function generate()
{

	    return barCode(
		$this->p_bcType,
		$this->p_text,
		$this->p_xDim,
		$this->p_w2n,
		$this->p_charGap,
		$this->p_inverted,
		$this->p_charHeight,
		$this->p_type,
		$this->p_textLabel,
		$this->p_rotAngle,
		$this->p_ck,
		$this->p_2File,
		$this->p_fileName);
}
}
?>
<?php

// Checking for gd and Freetype support 
if (!function_exists('gd_info'))
  die('No <a href="http://ua.php.net/manual/en/ref.image.php">gd</a> support in PHP.');

$gd = gd_info();
if ($gd["FreeType Support"] == false)
  die('No FreeType support in gd</a>');

// If you're sure about gd & freetype support
// -- comment out this block 



// directory for caching generated images 
// should be writable 
//$cache_dir = 'E:/Apache2/htdocs/gd'; // DO NOT FORGET TO CHANGE


// testing cache dir 
// remove these lines if you're sure 
// that your cache dir is really writable to PHP scripts
$tf = $cache_dir.'/'.md5(rand()).".test";
$f = @fopen($tf, "w");
if ($f == false)
    die("Fatal error! {$cache_dir} is not writable. Set 'chmod 777 {$cache_dir}' 
           or something like this");
fclose($f);
unlink($tf);
// testing cache dir END 




// full path to preferred TTF font 
// you could change this to be HTTP parameter 
$font = $ttf_dir .'/'. $ttfFont; // DO NOT FORGET TO CHANGE


// md5 secret 
$md5_secret_key = 'miologdtext'; // DO NOT FORGET TO CHANGE


// md5 sign of parameters
$auth_sign = isset($auth)?$auth:'';

$name = md5($qs);
// checking for cached file:
$cache_file = $cache_dir.'/'.$name.'.png';

if (file_exists($cache_file))
{
  // if cached file exists
  // output it and quit
  header("Content-type: image/png");
  readfile($cache_file);
  exit();
}

// no cached file exists 


// input parameters: text and size 
$text = isset($text)?$text:'default';



// basic sign checking: 
$computed_sign = md5($md5_secret_key.$text.$font_size);
// computing md5 sum of concatenation 
// of secret key and parameters

// hmac-based alrorithm would fit this case more 
// but for real-world purpose md5 of concatenation
// is enought 

if ($computed_sign != $auth_sign)
  die('Auth failed'); // auth error, provided sign is invalid 


if ($font_size == 0) $font_size = 30;
// 


// getting bounding box 
$bbox = imagettfbbox($font_size, 0, $font, $text);
// imagettfbbox returns very strange results 
// so transforming them to plain width and height 

$size_w = abs($bbox[2] - $bbox[0]) + 5;
// width: right corner X - left corner X

$size_h = abs($bbox[7] - $bbox[1]);
// height: top Y - bottom Y

// This is a lower-left corner 
// but imagettfbbox() sets (0,0) point
// inside bounding box
// so we shifting lower-left corner
$x = -abs($bbox[0]) + 5; 
$y = $size_h - abs($bbox[1]);

$im = imagecreatetruecolor($size_w, $size_h);
// creating image

$back = imagecolorallocate($im, $bcolor[0], $bcolor[1], $bcolor[2]); // background color
$fore = imagecolorallocate($im, $fcolor[0], $fcolor[1], $fcolor[2]); // foreground color

imagefilledrectangle($im, 0, 0, $size_w - 1, $size_h - 1, $back);
// filling with background color

imagettftext($im, $font_size, 0, $x, $y, $fore, $font, $text);
// rendering text

imagepng($im, $cache_file); // outputing PNG image to file cache 

imagedestroy($im); // destroy image 


// sending data from cache file 
header("Content-type: image/png");
readfile($cache_file);

?>
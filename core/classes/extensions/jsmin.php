<?php

$EOF = 999;
$fp = NULL;
$theA = 0;
$theB = 0;

$theLookahead = $EOF;

/* isAlphanum -- return true if the character is a letter, digit, underscore,
        dollar sign, or non-ASCII character.
*/
function /* int */ isAlphanum($cc)
{
    return (($cc >= ord('a') && $cc <= ord('z')) || ($cc >= ord('0') && $cc <= ord('9')) ||
        ($cc >= ord('A') && $cc <= ord('Z')) || $cc == ord('_') || $cc == ord('$') || $cc == ord("\\") ||
        $cc > 126);
}


/* get -- return the next character from fp. Watch out for lookahead. If
        the character is a control character, translate it to a space or
        linefeed.
*/
function /* int */  get()
{
    global $fp, $theLookahead;
    global $theA, $theB, $EOF;

    $c = $theLookahead;
    $theLookahead = $EOF;
    if ($c == $EOF) {
    $f = fgets( $fp, 2);
    //echo("get returned '$f' fp = $fp<br>");
    if (feof($fp))
    {
        $f = $EOF;
    }
    else
    {
        $c = ord($f);
    }
    }
    if ($c >= ord(" ") || $c == ord("\n") || $c == $EOF) {
        return $c;
    }
    if ($c == ord("\r")) {
        return ord("\n");
    }
    return ord(" ");
}

/* peek -- get the next character without getting it.
*/
function /* int */  peek()
{
    global $theLookahead;
    $theLookahead = get();
    return $theLookahead;
}


/* next -- get the next character, excluding comments. peek() is used to see
        if a '/' is followed by a '/' or '*'.
*/
function /* int */ _next()
{
    global $theA, $theB, $EOF;
    $c = get();
    if  ($c == ord("/")) {
        switch (peek()) {
    case ord("/"):
            for (;;) {
                $c = get();
                if ($c <= ord("\n")) {
                    return $c;
                }
            }
    case ord("*"):
            get();
            for (;;) {
                switch (get()) {
        case ord("*"):
                    if (peek() == ord("/")) {
                        get();
                        return ord(" ");
                    }
                    break;
                case $EOF:
                    printf("Error: JSMIN Unterminated comment.\n");
                    exit(1);
                }
            }
        default:
            return $c;
        }
    }
    return $c;
}

/* put -- write $v to the client. could be changed to output to a file.
*/

function put($v)
{
    printf("%c", $v);
}
/* action -- do something! What you do is determined by the argument:
        1   Output A. Copy B to A. Get the next B.
        2   Copy B to A. Get the next B. (Delete A).
        3   Get the next B. (Delete B).
   action treats a string as a single character. Wow!
   action recognizes a regular expression if it is preceded by ( or , or =.
*/

function /* void */ action(/* int */ $d)
{
    global $theA, $theB, $EOF;
    switch ($d) {
    case 1:
        put($theA);
    case 2:
        $theA = $theB;
        if ($theA == ord("\'") || $theA == ord('"')) {
            for (;;) {
        put($theA);
                $theA = get();
                if ($theA == $theB) {
                    break;
                }
                if (ord($theA) <= ord("\n")) {
                    printf("Error: JSMIN unterminated string literal: %c\n", $theA);
                    exit(1);
                }
                if ($theA == ord("\\")) {
            put($theA);
                    $theA = get();
                }
            }
        }
    case 3:
        $theB = _next();
        if ($theB == ord("/") && ($theA == ord("(") || $theA == ord(",") || $theA == ord("="))) {
            put($theA);
            put($theB);
            for (;;) {
                $theA = get();
                if ($theA == ord("/")) {
                    break;
                } else if ($theA == ord("\\")) {
                    put($theA);
                    $theA = get();
                } else if ($theA <= ord("\n")) {
                    printf("Error: JSMIN unterminated Regular Expression literal.\n", $theA);
                    exit(1);
                }
        put($theA);
            }
            $theB = _next();
        }
    }
}

/* jsmin -- Copy the input to the output, deleting the characters which are
        insignificant to JavaScript. Comments will be removed. Tabs will be
        replaced with spaces. Carriage returns will be replaced with linefeeds.
        Most spaces and linefeeds will be removed.
*/

function jsmin($infile)
{
    global $theA, $theB, $EOF;
    global $fp;

    $fp = fopen( $infile, "rb");

    $theA = ord("\n");
    action(3);
    while ($theA != $EOF) {
        switch ($theA) {
    case ord(" "):
            if (isAlphanum($theB)) {
                action(1);
            } else {
                action(2);
            }
            break;
    case ord("\n"):
            switch ($theB) {
        case ord('{'):
        case ord('['):
        case ord('('):
        case ord('+'):
        case ord('-'):
                action(1);
                break;
        case ord(' '):
                action(3);
                break;
            default:
                if (isAlphanum($theB)) {
                    action(1);
                } else {
                    action(2);
                }
            }
            break;
        default:
            switch ($theB) {
        case ord(' '):
                if (isAlphanum($theA)) {
                    action(1);
                    break;
                }
                action(3);
                break;
        case ord("\n"):
                switch ($theA) {
        case ord('}'):
        case ord(']'):
        case ord(')'):
        case ord('+'):
        case ord('-'):
        case ord('"'):
        case ord('\''):
                    action(1);
                    break;
                default:
                    if (isAlphanum($theA)) {
                        action(1);
                    } else {
                        action(3);
                    }
                }
                break;
            default:
                action(1);
                break;
            }
        }
    }
    fclose($fp);
}

//echo $argv[1];

//jsmin( $argv[1] );

//$file = file_get_contents("php://stdin");
jsmin("php://stdin");

?>
<?php

$ch = curl_init("https://stream.watsonplatform.net/speech-to-text/api/v1/recognize?end_of_phrase_silence_time=1.0&split_transcript_at_phrase_end=true&speaker_labels=true");
$audioFile = "C:/wamp64/www/webtool/apps/webtool/files/multimodal/Audio_Store/audio/26686339032ced46b594bd0208e44463b24647aa.flac";

// send a file
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,2);
curl_setopt($ch, CURLOPT_USERPWD, 'apikey' . ":" . '0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls');
curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    array(
        'file' => '@' . realpath($audioFile),
    ));

// output the response
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
var_dump(curl_exec($ch));
if(curl_error($ch)) {
    var_dump(curl_error($ch));
}

// close the session
curl_close($ch);

/*

curl -X POST -u "apikey:0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls" --header "Content-Type: audio/flac"  --header "Transfer-Encoding: chunked"  --data-binary @C:/wamp64/www/webtool/apps/webtool/files/multimodal/Audio_Store/audio/26686339032ced46b594bd0208e44463b24647aa.flac  "https://stream.watsonplatform.net/speech-to-text/api/v1/recognize?end_of_phrase_silence_time=1.0&split_transcript_at_phrase_end=true&speaker_labels=true"


 */

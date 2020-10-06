<?php
/**
 * Script executado a partir de services/DocumentMMService.php
 * Parâmetros: {$video_path} {$idDocument} {$idLanguage} {$idUser} {$email}
 *
 *
 */

$dirScript = dirname(__FILE__);
include $dirScript . "/offline.php";
require_once($dirScript . '/../vendor/autoload.php');
include $dirScript . "/../services/EmailService.php";

use thiagoalessio\TesseractOCR\TesseractOCR;
use GuzzleHttp\Client;

$app = 'webtool';
$db = 'webtool';

$videoFile = $argv[1];
$idDocument = $argv[2];
$idLanguage = $argv[3];
$idUser = $argv[4];
$email = $argv[5];

try {

    $configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
    Manager::loadConf($configFile);
    Manager::setConf('logs.level', 2);
    Manager::setConf('logs.port', 9999);
    Manager::setConf('fnbr.db', $db);
    Manager::setConf('options.lang', $idLanguage);

/*
    // preprocess the video
    $config = [
        'dataPath' => '/var/www/html/apps/webtool/files/multimodal/',
        'ffmpeg.binaries' => 'ffmpeg',//'/var/www/html/core/support/charon/bin/ffmpeg',
        'ffprobe.binaries' => 'ffprobe',//'/var/www/html/core/support/charon/bin/ffprobe',
    ];
    $dataPath = $config['dataPath'];
    $logger = null;
    $ffmpeg = FFMpeg\FFMpeg::create([
        //'ffmpeg.binaries' => $config['ffmpeg.binaries'],
        //'ffprobe.binaries' => $config['ffprobe.binaries'],
        'timeout' => 3600, // The timeout for the underlying process
        'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
    ], @$logger);
    $document = new fnbr\models\Document($idDocument);
    // using getID3
    $getID3 = new getID3;
    $file = $getID3->analyze($videoFile);
    $width = $file['video']['resolution_x'];
    $height = $file['video']['resolution_y'];
    $size = "small";
    if ($width > 240 and $height > 180) {
        $size = "large";
    }
    $shaName = basename($videoFile, '.mp4');
    mdump($size);
    mdump($shaName);
    $path = $dataPath . "Images_Store/thumbs/$size/";
    $name = "{$shaName}.jpeg";
    mdump("calling FFMpeg");
    mdump($videoFile);
    $video = $ffmpeg->open($videoFile);
    $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(5))->save($path . $name);
    // Set the formats
    $output_format = new FFMpeg\Format\Audio\Flac(); // Here you choose your output format
    $output_format->setAudioCodec("flac");
    $audioPath = $dataPath . "Audio_Store/audio/";
    $audioFile = $audioPath . $shaName . ".flac";
    mdump("saving audio " . $audioFile);
    $video->save($output_format, $audioFile);
    mdump('x');
    mdump("calling Watson");
    $client = new \GuzzleHttp\Client([
        'base_uri' => 'https://stream.watsonplatform.net/'
    ]);

    $audio = fopen($audioFile, 'r');
    $resp = $client->request('POST', 'speech-to-text/api/v1/recognize?end_of_phrase_silence_time=1.0&split_transcript_at_phrase_end=true&speaker_labels=true', [
        'auth' => ['apikey', '0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls'],
        'headers' => [
            'Content-Type' => 'audio/flac',
        ],
        'body' => $audio
    ]);

    $transcript = $resp->getBody();
    $transcriptPath = $dataPath . "Text_Store/transcripts/";
    $transcriptFile = $transcriptPath . $shaName . ".txt";
    //$myfile = fopen($target_file1, "w");
    //fwrite($myfile, $transcript);
    //fclose($myfile);
    file_put_contents($transcriptFile, $transcript);

    mdump("Audio Transcripts generated.");

    $ffprobe = FFMpeg\FFProbe::create([
        //'ffmpeg.binaries' => $config['ffmpeg.binaries'],
        //'ffprobe.binaries' => $config['ffprobe.binaries'],
        'timeout' => 3600, // The timeout for the underlying process
        'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
    ], @$logger);

    $subtitlesPath = $dataPath . "Text_Store/subtitles/";
    $subtitlesFile = $subtitlesPath . $shaName . ".srt";
    $dur = $ffprobe
        ->streams($videoFile)
        ->videos()
        ->first()
        ->get('duration');

    $fr = $ffprobe
        ->streams($videoFile)
        ->videos()
        ->first()
        ->get('r_frame_rate');

    $dur = floor($dur) * 60;
    $fr = round($fr) / 1000;
    $n = round($dur / $fr);
    $fr = round($dur / $n);
    $framerate = '1/' . $fr;

    $mp4Format = new FFMpeg\Format\Video\X264('libmp3lame', 'libx264');

    $val = "";

    if ($n < 100)
        $val = "02";
    elseif ($n < 1000)
        $val = "03";
    else
        $val = "06";

    $dir = "/tmp/{$shaName}";

    if (is_dir($dir)) {
        rrmdir($dir);
    }
    mkdir($dir, 0777);

    $cmd = $config['ffmpeg.binaries'] . " -i {$videoFile} -vf fps=1/5 {$dir}/img%{$val}d.jpg";
    exec($cmd);

    mdump("going to Tesseract");

    $files = array_diff(scandir($dir), ['..', '.']);

    $subtitlesFile = fopen($dataPath . "Text_Store/subtitles/{$shaName}.srt", "w");
    asort($files);
    foreach ($files as $file) {
        $full_path = $dir . '/' . $file;
        $tesseract = new TesseractOCR($full_path);
        $text = $tesseract->run();
        fwrite($subtitlesFile , $text);
    }
    fclose($subtitlesFile);

    mdump("Subtitles extracted.\r\n");

    //Decode JSON
    $json = file_get_contents($transcriptFile);
    $json_data = json_decode($json, true);
    $results = $json_data["results"];
    $parsed_transcript = [];
    $i = -1;
    foreach ($results as $key => $value) {
        $i = $i + 1;
        $det1 = $results[$key];
        $alternatives = $det1["alternatives"];
        $det2 = $alternatives[0];
        $transcript = $det2["transcript"];
        $timestamps = $det2["timestamps"];
        $num = count($timestamps);
        $start_time = $timestamps[0][1];
        $end_time = $timestamps[$num - 1][2];
        $parsed_transcript[$i][0] = $start_time;
        $parsed_transcript[$i][1] = $transcript;
        $parsed_transcript[$i][2] = $end_time;
    }
    $subtitles = file_get_contents($dataPath . "./Text_Store/subtitles/{$shaName}.srt");
    $subtitles = str_replace("\n", " ", $subtitles);
    $subtitles = str_replace("‘", "'", $subtitles);
    $sub_ar = explode(" ", $subtitles);
    $combinedFileName = $dataPath . "Text_Store/combined/{$shaName}.txt";
    $combined_file = fopen($combinedFileName, "w");
    foreach ($parsed_transcript as $key => $value) {
        $tr = $parsed_transcript[$key][1];
        $tr_ar = explode(' ', $tr);
        $cnt = count($tr_ar);
        mdump($tr_ar);
        mdump($key);

        for ($x = 0; $x <= $cnt - 2; $x++) {
            $flag = 0;
            $cnt1 = count($sub_ar);

            for ($y = 0; $y <= $cnt1 - 2; $y++) {
                if ($tr_ar[$x] === strtolower($sub_ar[$y]) && $tr_ar[$x + 1] === strtolower($sub_ar[$y + 1]) && $tr_ar[$x + 2] === strtolower($sub_ar[$y + 2])) {
                    $first = $tr_ar[$x];
                    $val = 0;
                    for ($k = $x; $k <= $cnt - 2; $k++) {
                        if ($tr_ar[$k] === $sub_ar[$y + $k - $x] || $tr_ar[$k + 1] === $sub_ar[$y + $k - $x + 1] || $tr_ar[$k] === $sub_ar[$y + $k - $x + 1]) {
                            if ($tr_ar[$k] === $sub_ar[$y + $k - $x + 1]) {
                                $inserted = array($sub_ar[$y + $k - $x]);

                                array_splice($tr_ar, $k, 0, $inserted);

                            } else
                                $tr_ar[$k] = $sub_ar[$y + $k - $x];
                        } else {
                            $val = 1;
                            break;
                        }

                        if ($tr_ar[$k] === $tr_ar[$k + 1])
                            unset($arr1[$k]);
                    }
                    if ($val === 1)
                        $tr_ar[$k] = $sub_ar[$y + $k - $x];
                    else {
                        $tr_ar[$k] = $sub_ar[$y + $k - $x + 1];
                    }

                    $flag = 1;
                    break;
                }
            }
            if ($flag === 1)
                break;
        }

        list($sec, $ms) = explode('.', $parsed_transcript[$key][0]);
        $parsed_transcript[$key][3] = gmdate("H:i:s", $sec) . '.' . substr($ms . '000', 0, 3);
        list($sec, $ms) = explode('.', $parsed_transcript[$key][2]);
        $parsed_transcript[$key][4] = gmdate("H:i:s", $sec) . '.' . substr($ms . '000', 0, 3);
        //fwrite($combined_file, $parsed_transcript[$key][0] . "\n" . $parsed_transcript[$key][1] . "\n" . $parsed_transcript[$key][2] . "\n\n");
        fwrite($combined_file, $parsed_transcript[$key][3] . "|" . $parsed_transcript[$key][4] . "|" . $parsed_transcript[$key][1] . "\n");
    }

    mdump("Alignments Done.\r\n");

    $documentMM = new fnbr\models\DocumentMM();
    $documentMM->getByIdDocument($idDocument);
    $visualPath = $videoFile;
    $dataMM = (object)[
        'audioPath' => $audioFile,
        'visualPath' => $visualPath,
        'alignPath' => $combinedFileName,
        'idDocument' => $idDocument
    ];
    $documentMM->setData($dataMM);
    $documentMM->saveMM();

    $dataVideo = (object)[
        'idLanguage' => $idLanguage,
        'idDocument' => $idDocument
    ];
    $document->uploadMultimodalText($dataVideo, $combinedFileName);

    //$sql = "insert into $pathtable (audioPath,visualPath,alignPath,idDocument) values ('$target_file2','$target_file','$p',$id)";
    //if ($con->query($sql) === TRUE) {
    //    echo nl2br("New record created successfully\r\n");
    //} else {
    //    echo nl2br("Error: " . $sql . "<br>" . $con->error . "\r\n");
    // }
    mdump("Youtube Video Download finished! Now check the file\r\n");

    //echo nl2br("Return to Home Page\r\n");
*/
    charon('frames', $videoFile);


    $emailService = new EmailService();
    $emailService->sendSystemEmail($email, 'Webtool: upload Video MM', "The video {$videoFile} was processed.<br>FNBr Webtool Team");

} catch (Exception $e) {
    mdump($e->getMessage());
}


function charon($action, $videoFile) {
    $videoURL = str_replace("/var/www/html", "http://server3.framenetbr.ufjf.br:8201", $videoFile);
    mdump($videoURL);
    $client = new Client([
        // Base URI is used with relative requests
        'base_uri' => 'http://200.17.70.211:13652',
        // You can set any number of default request options.
        'timeout'  => 300.0,
    ]);
    try {
        $response = $client->request('post', 'frames', [
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ],
            'json' => [
                'url_video' => $videoURL,
            ]
        ]);
        $body = json_decode($response->getBody());
        mdump($body);
        return $body;
    } catch (Exception $e) {
        echo $e->getMessage()  . "\n";
        return '';
    }
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);

        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (filetype($dir . '/' . $object) == 'dir') {
                    rrmdir($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }

        reset($objects);
        rmdir($dir);
    }
}

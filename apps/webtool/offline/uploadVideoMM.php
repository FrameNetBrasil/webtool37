<?php
/**
 * Script executado a partir de services/DocumentMMService.php
 * Parâmetros: {$video_path} {$idDocument} {$idLanguage} {$idUser} {$email}
 *
 * see also: https://ricecooker.readthedocs.io/en/latest/video_compression.html
 */

/*
Automated conversion

ffmpeg -i inputfile.mp4 \
  -b:a 32k -ac 1 \
  -vf scale="'w=-2:h=trunc(min(ih,480)/2)*2'" \
  -crf 23 \
  -profile:v baseline -level 3.0 -preset slow -v error -strict -2 -stats \
  -y outputfile.mp4

This command takes the inputfile.mp4 and outputs the file outputfile.mp4 that has the following transformations applied to it:
Limits the audio codec to 32k/sec
Scale the video to max-height of 480 pixels
Compress the video with CRF of 23 (constant rate factor)
*/

use thiagoalessio\TesseractOCR\TesseractOCR;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Multimodal
{
    public $videoFile;
    public $audioFile;
    public $transcriptFile;
    public $combinedFile;
    public $idDocument;
    public $idLanguage;
    public $idUser;
    public $email;
    public $dataPath;
    public $videoSize;
    public $videoFileOriginal;
    public $video;
    public $ffmpegConfig;

    public $testingPhase;

    public function __construct($parameters)
    {
        $this->videoFile = $parameters[1]; // shaNameOriginal
        $this->idDocument = $parameters[2];
        $this->idLanguage = $parameters[3];
        $this->idUser = $parameters[4];
        $this->email = $parameters[5];
        $this->dataPath = '/var/www/html/apps/webtool/files/multimodal/';
        $this->videoSize = 'small';

        $this->testingPhase = 2;
    }

    public function process()
    {

        if (($this->testingPhase == 1) || ($this->testingPhase == 2) || ($this->testingPhase == 3)) {
            $this->videoPreprocess();
            $this->getFrames();
            $this->getAudio();
            $this->speechToText();
            $this->alignment();
            $this->saveToDatabase();
        }
        if ($this->testingPhase == 3) {
            $this->charon('frames', $this->videoFile);

        }
        $emailService = new EmailService();
        //$emailService->sendSystemEmail($email, 'Webtool: upload Video MM', "The video {$videoFile} was processed.<br>FNBr Webtool Team");
        mdump('finished!!');

    }

    public function videoPreprocess()
    {
        // preprocess the video
        $this->ffmpegConfig = $config = [
            'dataPath' => $this->dataPath,
            'ffmpeg.binaries' => 'ffmpeg', // '/var/www/html/core/support/charon/bin/ffmpeg',
            'ffprobe.binaries' => 'ffprobe',//'/var/www/html/core/support/charon/bin/ffprobe',
        ];
        $logger = null;
        // video attributes
        var_dump($config);
        $ffprobe = FFMpeg\FFProbe::create([
            'ffmpeg.binaries' => $config['ffmpeg.binaries'],
            'ffprobe.binaries' => $config['ffprobe.binaries'],
            'timeout' => 3600, // The timeout for the underlying process
            'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
        ], @$logger);

        $first = $ffprobe
            ->streams($this->videoFile)
            ->videos()
            ->first();
        $duration = $first->get('duration');

        mdump('duration 1 :' . $duration);

        $frameRate = $first->get('r_frame_rate');
        mdump('framerate 1 :' . $frameRate);

        $duration = floor($duration) * 60;
        mdump('duration 2 :' . $duration);
        $frameRate = round($frameRate) / 1000;
        mdump('framerate 2 :' . $frameRate);
        $n = round($duration / $frameRate);
        mdump('n :' . $n);
        $frameRate = round($duration / $n);
        mdump('framerate 3 :' . $frameRate);
        $frameRate = '1/' . $frameRate;
        mdump('framerate 4 :' . $frameRate);

        //mdump($first->getDimensions());
        // using getID3
        $getID3 = new getID3;
        $file = $getID3->analyze($this->videoFile);
        $width = $file['video']['resolution_x'];
        $height = $file['video']['resolution_y'];
        $this->videoSize = 'small';
        if ($width > 240 and $height > 180) {
            $this->videoSize = "large";
        }
        mdump('width = ' . $width);
        mdump('height = ' . $height);

        // video compression
        $this->ffmpeg = FFMpeg\FFMpeg::create([
            'ffmpeg.binaries' => $config['ffmpeg.binaries'],
            'ffprobe.binaries' => $config['ffprobe.binaries'],
            'timeout' => 3600, // The timeout for the underlying process
            'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
        ], @$logger);
        $this->videoFileOriginal = $this->videoFile;
        $videoFile = str_replace("_original", "", $this->videoFile);
        if (!file_exists($videoFile)) {
            $newWidth = floor(((480 / $height) * $width) / 2) * 2;
            $originalVideo = $this->ffmpeg->open($this->videoFileOriginal);
            $originalVideo
                ->filters()
                ->resize(new FFMpeg\Coordinate\Dimension($newWidth, 480), FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_SCALE_HEIGHT, true)
                ->synchronize();
            $originalVideo
                ->save(new FFMpeg\Format\Video\X264('copy'), $videoFile);
            mdump('compressed video file saved');
        }

    }

    public function getFrames()
    {
        // getting frame
        $shaName = basename($this->videoFile, '.mp4');
        $path = $this->dataPath . "Images_Store/thumbs/{$this->videoSize}/";
        $name = "{$shaName}.jpeg";
        $this->video = $this->ffmpeg->open($this->videoFileOriginal);
        $this->video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(5))->save($path . $name);
    }

    public function getAudio()
    {
        // Set the formats
        $shaName = basename($this->videoFile, '.mp4');
        $output_format = new FFMpeg\Format\Audio\Flac(); // Here you choose your output format
        $output_format->setAudioCodec("flac");
        $audioPath = $this->dataPath . "Audio_Store/audio/";
        $this->audioFile = $audioPath . $shaName . ".flac";
        if (!file_exists($this->audioFile)) {
            mdump("saving audio " . $this->audioFile);
            $this->video->save($output_format, $this->audioFile);
        }

    }

    public function speechToText()
    {
        /*
 * Calling watson
 */
        if (($this->testingPhase == 2) || ($this->testingPhase == 3)) {
            $shaName = basename($this->videoFile, '.mp4');
            $transcriptPath = $this->dataPath . "Text_Store/transcripts/";
            $this->transcriptFile = $transcriptPath . $shaName . ".txt";

            if (!file_exists($this->transcriptFile)) {
                mdump("calling Watson");
                $audio = fopen($this->audioFile, 'r');
                $client = new \GuzzleHttp\Client([
                    'base_uri' => 'https://stream.watsonplatform.net/',
                ]);
                $response = $client->request(
                    'POST',
                    'speech-to-text/api/v1/recognize?end_of_phrase_silence_time=0.3&split_transcript_at_phrase_end=true&speaker_labels=true&model=pt-BR_NarrowbandModel',
                    [
                        //'auth' => ['apikey', '0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls'],
                        'auth' => ['apikey', "jHVAXaIqW_Zj7iPA8HzNk2Mf-qnROtm5ZQ7IOJyX9Zb1"],
                        //'auth' => ['apikey', "jrdLqCqvqz9JU8Eu8Ls7c40_uXTmCFrb3iWbLk77KgvJ"],
                        'headers' => [
                            'Content-Type' => 'audio/flac',
                        ],
                        'body' => $audio,
                        //'debug' => true,
                        //'verify' => false,
                        //'curl.options' =>[ 'CURLOPT_BUFFERSIZE' =>'120000L'],
                        //'timeout' => 3000
                    ]
                );

                $transcript = $response->getBody();
                //$myfile = fopen($target_file1, "w");
                //fwrite($myfile, $transcript);
                //fclose($myfile);
                file_put_contents($this->transcriptFile, $transcript);
            }

            mdump("Audio Transcripts generated.");


        }

    }

    public function tesseract() {
        mdump("going to Tesseract");
        $shaName = basename($this->videoFile, '.mp4');
        $subtitlesPath = $this->dataPath . "Text_Store/subtitles/";
        $subtitlesFile = $subtitlesPath . $shaName . ".srt";
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
            $this->rrmdir($dir);
        }
        mkdir($dir, 0777);

        $cmd = $this->ffmpegConfig['ffmpeg.binaries'] . " -i {$this->videoFile} -vf fps=1/5 {$dir}/img%{$val}d.jpg";
        exec($cmd);

        $files = array_diff(scandir($dir), ['..', '.']);

        $subtitlesFile = fopen($this->dataPath . "Text_Store/subtitles/{$shaName}.srt", "w");
        asort($files);
        foreach ($files as $file) {
            $full_path = $dir . '/' . $file;
            $tesseract = new TesseractOCR($full_path);
            $text = $tesseract->run();
            fwrite($subtitlesFile, $text);
        }
        fclose($subtitlesFile);

        mdump("Subtitles extracted.\r\n");

    }

    public function alignment()
    {
        //Decode JSON
        $shaName = basename($this->videoFile, '.mp4');
        $json = file_get_contents($this->transcriptFile);
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
        $subtitles = file_get_contents($this->dataPath . "./Text_Store/subtitles/{$shaName}.srt");
        $subtitles = str_replace("\n", " ", $subtitles);
        $subtitles = str_replace("‘", "'", $subtitles);
        $sub_ar = explode(" ", $subtitles);
        $this->combinedFile = $this->dataPath . "Text_Store/combined/{$shaName}.txt";
        $combined_file = fopen($this->combinedFile, "w");
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

    }

    public function saveToDatabase()
    {
        $documentMM = new fnbr\models\DocumentMM();
        $documentMM->getByIdDocument($this->idDocument);
        $visualPath = $this->videoFile;
        $dataMM = (object)[
            'audioPath' => $this->audioFile,
            'visualPath' => $visualPath,
            'alignPath' => $this->combinedFile,
            'idDocument' => $this->idDocument
        ];
        $documentMM->setData($dataMM);
        $documentMM->saveMM();

        $dataVideo = (object)[
            'idLanguage' => $this->idLanguage,
            'idDocument' => $this->idDocument
        ];
        //$document->uploadMultimodalText($dataVideo, $combinedFileName);

        //$sql = "insert into $pathtable (audioPath,visualPath,alignPath,idDocument) values ('$target_file2','$target_file','$p',$id)";
        //if ($con->query($sql) === TRUE) {
        //    echo nl2br("New record created successfully\r\n");
        //} else {
        //    echo nl2br("Error: " . $sql . "<br>" . $con->error . "\r\n");
        // }

        mdump("Youtube Video Download finished! Now check the file\r\n");
    }

    public function charon($action, $videoFile)
    {
        $videoURL = str_replace("/var/www/html", "http://server3.framenetbr.ufjf.br:8201", $videoFile);
        mdump($videoURL);
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://200.17.70.211:13652',
            // You can set any number of default request options.
            'timeout' => 300.0,
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
            echo $e->getMessage() . "\n";
            return '';
        }
    }

    public function rrmdir($dir)
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

    public function sendFileAction($audioFile)
    {
        $filename = $audioFile;
        $filesize = filesize($filename);
        //$boundary  = '----iCEBrkUploaderBoundary' . uniqid();

        $fileout = str_replace('.flac', '.chunked', $audioFile);
        $fo = fopen($fileout, 'w');
        $fh = fopen($filename, 'r');
        $chunkSize = 1024 * 1000;
        rewind($fh); // probably not necessary
        while (!feof($fh)) {
            $pos = ftell($fh);
            $chunk = fread($fh, $chunkSize);
            fwrite($fo, sprintf("%x\r\n", strlen($chunk)));
            fwrite($fo, $chunk);
            fwrite($fo, "\r\n");
        }
        fwrite($fo, "0\r\n\r\n");
        fclose($fo);
        $fi = fopen($fileout, 'r');

        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://stream.watsonplatform.net/',

        ]);

        $request = $client->request(
            'POST',
            'speech-to-text/api/v1/recognize?end_of_phrase_silence_time=1.0&split_transcript_at_phrase_end=true&speaker_labels=true',
            [
                'auth' => ['apikey', '0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls'],
                'headers' => [
                    'Content-Type' => 'audio/flac',
                    'Transfer-Encoding' => 'chunked',
                ],
                'debug' => true,
                'verify' => false,
                'body' => $fi
            ]
        );
        $transcript = $request->getBody();
        mdump($transcript);


        /*
        rewind($fh); // probably not necessary
        while (! feof($fh)) {
            $pos   = ftell($fh);
            $chunk = fread($fh, $chunkSize);
            $calc  = $pos + strlen($chunk)-1;

            // Not sure if this is needed.
            //if (ftell($fh) > $chunkSize) {
            //    $pos++;
            //}

            $request = $client->request(
                'POST',
                'speech-to-text/api/v1/recognize?end_of_phrase_silence_time=1.0&split_transcript_at_phrase_end=true&speaker_labels=true',
                [
                    'auth' => ['apikey', '0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls'],
                    'headers' => [
                        'Content-Type' => 'audio/flac',
                        'Transfer-Encoding'   => 'chunked',
                    ],
                    'debug'   => true,
                    'verify'  => false,
                    'body' => $chunk
                ]
            );
            $transcript = $request->getBody();
            mdump($transcript);
        }
        */

    }


}

$app = 'webtool';
$db = 'webtool';

$dirScript = dirname(__FILE__);
include $dirScript . "/offline.php";
require_once($dirScript . '/../vendor/autoload.php');
include $dirScript . "/../services/EmailService.php";

$configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9998);
Manager::setConf('fnbr.db', $db);

try {
    $mm = new Multimodal($argv);
    $mm->process();
} catch (Exception $e) {
    mdump($e->getMessage());
}


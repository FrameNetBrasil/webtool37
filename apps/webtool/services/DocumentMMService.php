<?php

use thiagoalessio\TesseractOCR\TesseractOCR;

error_reporting(0);

class DocumentMMService extends MService
{

    public function uploadVideo($dataVideo)
    {
        mdump($dataVideo);
        $config = [
            'dataPath' => '/var/www/html/apps/webtool/files/multimodal/',
            'ffmpeg.binaries' => '/var/www/html/core/support/charon/bin/ffmpeg',
            'ffprobe.binaries' => '/var/www/html/core/support/charon/bin/ffprobe',
        ];
        $dataPath = $config['dataPath'];
        if ($dataVideo->webfile != '') {
            $ffmpeg = FFMpeg\FFMpeg::create([
                'ffmpeg.binaries' => $config['ffmpeg.binaries'],
                'ffprobe.binaries' => $config['ffprobe.binaries'],
                'timeout' => 3600, // The timeout for the underlying process
                'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
            ], @$logger);
            $url = $dataVideo->webfile;

            parse_str(parse_url($url, PHP_URL_QUERY), $vars);
            $vid = $vars['v'];

            if ($vid) {
                parse_str(file_get_contents("http://youtube.com/get_video_info?video_id=" . $vid), $info); //decode the data

                $videoData = json_decode($info['player_response'], true);
                $videoDetails = $videoData['videoDetails'];
                $streamingData = $videoData['streamingData'];
                $streamingDataFormats = $streamingData['formats'];
//set video title
                $video_title = $videoDetails["title"];
                $video = $streamingDataFormats[1]['url'];
                $shaval = sha1($video_title);

                $fileName = $shaval . '.mp4';
                $target_dir = $dataPath . "Video_Store/full/";
                $target_file = $target_dir . $fileName;

                $uploadOk = 1;

                /*
                if ($result) {
                    while ($row = mysqli_fetch_array($result)) {
                        if ($row["$pathcolumn"] == $target_file) {
                            echo nl2br("Sorry, file already exists.\r\n");
                            $uploadOk = 0;
                            break;
                        }
                    }
                }

                */

                mdump("upload ok");
                if ($uploadOk == 1) {
                    $document = new fnbr\models\Document($dataVideo->idDocument);
                    //$query1 = "select idDocument from document where entry='$d'";
                    //$result1 = mysqli_query($con, $query1);
                    //$id = 0;
                    //while ($row = mysqli_fetch_array($result1)) {
                    //    $id = $row["idDocument"];
                    //    break;
                    //}

                    file_put_contents($target_file, fopen($video, 'r'));

                    $getID3 = new getID3;
                    $file = $getID3->analyze($target_file);

                    $width = $file['video']['resolution_x'];
                    $height = $file['video']['resolution_y'];

                    $size = "small";
                    if ($width > 240 and $height > 180) {
                        $size = "large";
                    }

                    $path = $dataPath . "Images_Store/thumbs/$size/";
                    $name = "$shaval.jpeg";

                    $video = $ffmpeg->open($target_file);
                    mdump("calling FFMpeg");

                    $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(5))->save($path . $name);
// Set the formats
                    $output_format = new FFMpeg\Format\Audio\Flac(); // Here you choose your output format
                    $output_format->setAudioCodec("flac");
                    $target_dir2 = $dataPath . "Audio_Store/audio/";
                    $target_file2 = $target_dir2 . $shaval . ".flac";
                    $video->save($output_format, $target_file2);

                    mdump("calling Watson");
                    $client = new \GuzzleHttp\Client([
                        'base_uri' => 'https://stream.watsonplatform.net/'
                    ]);

                    $audio = fopen($target_file2, 'r');
                    $resp = $client->request('POST', 'speech-to-text/api/v1/recognize?end_of_phrase_silence_time=1.0&split_transcript_at_phrase_end=true&speaker_labels=true', [
                        'auth' => ['apikey', '0J34Y-yMVfdnaZpxdEwc8c-FoRPrpeTXcOOsxYM6lLls'],
                        'headers' => [
                            'Content-Type' => 'audio/flac',
                        ],
                        'body' => $audio
                    ]);

                    $transcript = $resp->getBody();
                    $target_dir1 = $dataPath . "Text_Store/transcripts/";
                    $target_file1 = $target_dir1 . $shaval . ".txt";
                    $myfile = fopen($target_file1, "w");
                    fwrite($myfile, $transcript);
                    fclose($myfile);

                    mdump("Audio Transcripts generated.\r\n");

                    $ffprobe = FFMpeg\FFProbe::create([
                        'ffmpeg.binaries' => $config['ffmpeg.binaries'],
                        'ffprobe.binaries' => $config['ffprobe.binaries'],
                        'timeout' => 3600, // The timeout for the underlying process
                        'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
                    ], @$logger);

                    $target_dir3 = $dataPath . "Text_Store/subtitles/";
                    $target_file3 = $target_dir3 . $shaval . ".srt";
                    $dur = $ffprobe
                        ->streams($target_file)
                        ->videos()
                        ->first()
                        ->get('duration');

                    $fr = $ffprobe
                        ->streams($target_file)
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

                    $dir = "/tmp/{$shaval}";

                    if (is_dir($dir)) {
                        $this->rrmdir($dir);
                    }
                    mkdir($dir, 0777);

                    $cmd = $config['ffmpeg.binaries'] . " -i {$target_file} -vf fps=1/5 {$dir}/img%{$val}d.jpg";
                    exec($cmd);

                    mdump("going to Tesseract");

                    $files = array_diff(scandir($dir), ['..', '.']);

                    $subtitle_file = fopen($dataPath . "Text_Store/subtitles/{$shaval}.srt", "w");
                    asort($files);
                    foreach ($files as $file) {
                        $full_path = $dir . '/' . $file;
                        $tesseract = new TesseractOCR($full_path);
                        $text = $tesseract->run();
                        fwrite($subtitle_file, $text);
                    }
                    fclose($subtitle_file);

                    mdump("Subtitles extracted.\r\n");

                    $json = file_get_contents($target_file1);

//Decode JSON
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


                    $subtitles = file_get_contents($dataPath . "./Text_Store/subtitles/{$shaval}.srt");
                    $subtitles = str_replace("\n", " ", $subtitles);
                    $subtitles = str_replace("‘", "'", $subtitles);

                    $sub_ar = explode(" ", $subtitles);
                    $combinedFileName = $dataPath . "Text_Store/combined/{$shaval}.txt";
                    $combined_file = fopen($combinedFileName, "w");


                    foreach ($parsed_transcript as $key => $value) {
                        $tr = $parsed_transcript[$key][1];
                        $tr_ar = explode(' ', $tr);
                        $cnt = count($tr_ar);

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

                        $parsed_transcript[$key][1] = implode(' ', $tr_ar);

                        $date = DateTime::createFromFormat('!s.v', sprintf("%06.3f", $parsed_transcript[$key][0]));
                        $parsed_transcript[$key][3] =  $date->format("H:i:s.v");
                        $date = DateTime::createFromFormat('!s.v', sprintf("%06.3f", $parsed_transcript[$key][2]));
                        $parsed_transcript[$key][4] =  $date->format("H:i:s.v");
                        //mdump($parsed_transcript[$key]);
                        //fwrite($combined_file, $parsed_transcript[$key][0] . "\n" . $parsed_transcript[$key][1] . "\n" . $parsed_transcript[$key][2] . "\n\n");
                        fwrite($combined_file, $parsed_transcript[$key][3] . "|" . $parsed_transcript[$key][4] . "|" . $parsed_transcript[$key][1] . "\n");
                    }

                    mdump("Alignments Done.\r\n");

                    $documentMM = new fnbr\models\DocumentMM();
                    $documentMM->getByIdDocument($dataVideo->idDocument);

                    $visualPath = str_replace("/var/www/html","",$target_file);

                    $dataMM = (object)[
                        'audioPath' => $target_file2,
                        'visualPath' => $visualPath,
                        'alignPath' => $combinedFileName,
                        'idDocument' => $dataVideo->idDocument
                    ];
                    $documentMM->setData($dataMM);
                    $documentMM->saveMM();

                    $document->uploadMultimodalText($dataVideo, $combinedFileName);

                    //$sql = "insert into $pathtable (audioPath,visualPath,alignPath,idDocument) values ('$target_file2','$target_file','$p',$id)";
                    //if ($con->query($sql) === TRUE) {
                    //    echo nl2br("New record created successfully\r\n");
                    //} else {
                    //    echo nl2br("Error: " . $sql . "<br>" . $con->error . "\r\n");
                    // }
                    mdump("Youtube Video Download finished! Now check the file\r\n");

                    //echo nl2br("Return to Home Page\r\n");
                } else {
                    mdump("There was a problem in downloading the file.\r\n");
                    mdump("Return to Home Page\r\n");
                }
            }
        }
        return '';
    }

    public function rrmdir($dir)
    {
        if (is_dir($dir))
        {
            $objects = scandir($dir);

            foreach ($objects as $object)
            {
                if ($object != '.' && $object != '..')
                {
                    if (filetype($dir.'/'.$object) == 'dir') {rrmdir($dir.'/'.$object);}
                    else {unlink($dir.'/'.$object);}
                }
            }

            reset($objects);
            rmdir($dir);
        }
    }

}

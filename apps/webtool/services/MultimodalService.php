<?php

error_reporting(0);

use fnbr;
use Manager;
use MService;
use YouTube\YouTubeDownloader;
use YouTube\Exception\YouTubeException;

class MultimodalService extends MService
{

    public function listCorpusMM($data = '', $idLanguage = '')
    {
        $corpus = new fnbr\models\Corpus();
        $filter = (object)['corpus' => $data->corpus, 'document' => $data->document, 'idLanguage' => $idLanguage];
        $corpora = $corpus->listByFilter($filter)->asQuery()->getResult();

        $documentMM = new fnbr\models\DocumentMM();
        $corpora = $documentMM->listCorpus()->asQuery()->getResult();

        $result = array();
        foreach ($corpora as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idCorpus'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listCorpusMultimodal($corpusName = '', $idLanguage = '')
    {
        $corpus = new fnbr\models\Corpus();
        $filter = (object)['corpus' => $corpusName, 'idLanguage' => $idLanguage];
        $corpora = $corpus->listMultimodalByFilter($filter)->asQuery()->chunkResult('idCorpus', 'name');
        $result = array();
        foreach ($corpora as $idCorpus => $name) {
            $node = array();
            $node['id'] = 'c' . $idCorpus;
            $node['text'] = $name;
            $node['state'] = 'closed';
            $result[] = $node;
        }
        $data = (object)[
            'id' => 'root',
            'state' => 'open',
            'text' => 'Corpus',
            'children' => $result
        ];
        return json_encode([$data]);
    }

    public function listCorpusDocumentMultimodal($idCorpus)
    {
        $doc = new fnbr\models\DocumentMM();
        $docs = $doc->listByCorpus($idCorpus);//->asQuery()->getResult();
        foreach ($docs as $doc) {
            if ($doc['idDocumentMM']) {
                $node = array();
                $node['id'] = 'd' . $doc['idDocumentMM'];
                $node['text'] = $doc['name'] . ' [' . $doc['quant'] . ']';
                $node['state'] = 'open';
                if (str_contains(strtolower($doc['name']), 'flickr30k')) {
                    $node['flickr30k'] = 1;
                } else {
                    $node['flickr30k'] = 0;
                }
                $result[] = $node;
            }
        }
        return json_encode($result);
    }

    public function listDocumentsMM($idCorpus)
    {
        $documentMM = new fnbr\models\DocumentMM();
        $docs = $documentMM->listByCorpus($idCorpus)->asQuery()->getResult();
        foreach ($docs as $row) {
            $idDocument = $row['idDocument'];
            $documentMM->getByIdDocument($idDocument);
            $node = [];
            $node['id'] = 'd' . $documentMM->getIdDocumentMM();
            $node['text'] = $row['name'];// . ' [' . $row['quant'] . ']';
            $node['state'] = 'open';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }


    public function listAnnotationSetMultimodal($idDocumentMM, $sortable = NULL)
    {
        $documentMM = new fnbr\models\DocumentMM();
        $documentMM->getById($idDocumentMM);
        $document = new fnbr\models\Document($documentMM->getIdDocument());
        $sentences = $documentMM->listSentenceMM();
        /*
        $idSubCorpus = $document->getRelatedSubCorpusMultimodal();
        if ($idSubCorpus == '') {
            $data = new \stdClass();
            $document->createSubCorpusMultimodalText($data);

            foreach ($sentences as $sentence) {
                $data->idSentence = $sentence['idSentence'];
                $data->idSentenceMM = $sentence['idSentenceMM'];
                $document->createAnnotationMultimodalText($data);
            }
            $idSubCorpus = $document->getRelatedSubCorpus();
        }
        $vas = new fnbr\models\ViewAnnotationSet();
        $as = $vas->listSentencesForDocumentMM($idDocumentMM, $idSubCorpus, $sortable);//->asQuery()->getResult();
        */
        $vas = new fnbr\models\ViewAnnotationSet();
        $as = $vas->listSentencesForDocumentMM($idDocumentMM, $sortable);//->asQuery()->getResult();

        $annotation = $vas->listFECEByIdDocumentMM($idDocumentMM);
        $result = array();
        foreach ($sentences as $sentence) {
            $node = array();
            $node['idAnnotationSet'] = $as[$sentence['idSentence']];
            $node['idSentenceMM'] = $sentence['idSentenceMM'];
            //$node['text'] = $sentence['text'];
            $node['startTimestamp'] = $sentence['startTimestamp'];
            $node['endTimestamp'] = $sentence['endTimestamp'];
            $node['startFrame'] = (int)($sentence['startTimestamp'] * 25);
            $node['endFrame'] = (int)($sentence['endTimestamp'] * 25);
            if ($annotation[$sentence['idSentence']]) {
                $node['text'] = $this->decorateSentence($sentence['text'], $annotation[$sentence['idSentence']]);
            } else {
                $targets = $vas->listTargetBySentence($sentence['idSentence']);
                mdump($targets);
                $node['text'] = $this->decorateSentence($sentence['text'], $targets);
            }
            $node['status'] = '';
            $node['rgbBg'] = '';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function listSentencesForDynamic($idDocumentMM, $sortable = NULL)
    {
        $documentMM = new fnbr\models\DocumentMM();
        $documentMM->getById($idDocumentMM);
        $dynamicSentenceMM = new fnbr\models\DynamicSentenceMM();
        $sentences = $dynamicSentenceMM->listSentenceByDocument($documentMM->getIdDocument());//$documentMM->listDynamicSentenceMM();
        $vas = new fnbr\models\ViewAnnotationSet();
        $as = $vas->listSentencesForDocumentMM($idDocumentMM, $sortable);
        $annotation = $vas->listFECEByIdDocumentMM($idDocumentMM);
        $result = [];
        foreach ($sentences as $sentence) {
            $row = [];
            $row['idAnnotationSet'] = $as[$sentence['idSentence']];
            $row['idSentenceMM'] = $sentence['idSentenceMM'];
            $row['startTimestamp'] = $sentence['startTimestamp'];
            $row['endTimestamp'] = $sentence['endTimestamp'];
            $row['startFrame'] = (int)($sentence['startTime'] * 25);
            $row['endFrame'] = (int)($sentence['endTime'] * 25);
            $row['start'] = $row['startFrame'] . ' [' . $sentence['startTime'] . 's]';
            $row['end'] = $row['endFrame'] . ' [' . $sentence['endTime'] . 's]';
            if ($annotation[$sentence['idSentence']]) {
                $row['text'] = $this->decorateSentence($sentence['text'], $annotation[$sentence['idSentence']]);
            } else {
                $targets = $vas->listTargetBySentence($sentence['idSentence']);
                $row['text'] = $this->decorateSentence($sentence['text'], $targets);
            }
            $row['status'] = '';
            $row['rgbBg'] = '';
            $result[] = $row;
        }
        return json_encode($result);
    }

    public function decorateSentence($sentence, $labels)
    {
        $decorated = "";
        $ni = "";
        $i = 0;
        foreach ($labels as $label) {
            $style = 'background-color:#' . $label['rgbBg'] . ';color:#' . $label['rgbFg'] . ';';
            if ($label['startChar'] >= 0) {
                $title = ($label['frameName'] != '') ? " title='{$label['frameName']}' " : '';
                $decorated .= mb_substr($sentence, $i, $label['startChar'] - $i);
                $decorated .= "<span {$title} style='{$style}'>" . mb_substr($sentence, $label['startChar'], $label['endChar'] - $label['startChar'] + 1) . "</span>";
                $i = $label['endChar'] + 1;
            } else { // null instantiation
                $ni .= "<span style='{$style}'>" . $label['instantiationType'] . "</span> " . $decorated;
            }
        }
        $decorated = $ni . $decorated . mb_substr($sentence, $i);
        return $decorated;
    }

    /**
     * @param $dataVideo {idLanguage, webfile, localfile}
     *
     * 1. get the file
     * 1.1. if (localfile) then upload_file to Video_Store
     * 1.2. if (webfile) then download_file to Video_Store
     * 2. run offline uploadVideoMM to preprocess the video
     *
     */
    public function uploadVideo($idDocumentMM, $dataVideo, $idLanguage)
    {
        try {
            mdump($dataVideo);
            $config = [
                //'dataPath' => '/var/www/html/apps/webtool/files/multimodal/'
                //'dataPath' => '/home/framenetbr/devel/fnbr/charon_docker_maestro/apps/webtool/files/multimodal/'
                'dataPath' => Manager::getAppPath() . '/files/multimodal/'
            ];
            mdump($config);
            $dataPath = $config['dataPath'];
            if ($dataVideo->webfile != '') {
                $url = $dataVideo->webfile;
                $originalFile = $url;
                parse_str(parse_url($url, PHP_URL_QUERY), $vars);
                $vid = $vars['v'];
                mdump($vid);
                if ($vid) {
                    //parse_str(file_get_contents("http://youtube.com/get_video_info?video_id=" . $vid), $info); //decode the data
                    //mdump($this->getVideoInfo($vid));
                    //$videoData = json_decode($info['player_response'], true);
                    $videoData = json_decode($this->getVideoInfo($vid));
                    $videoDetails = $videoData->videoDetails;
                    $streamingData = $videoData->streamingData;
                    $streamingDataFormats = $streamingData->formats;
                    mdump($streamingDataFormats[1]->signatureCipher);
                    $array = [];
                    parse_str($streamingDataFormats[1]->signatureCipher, $array);
                    mdump($array['url']);
                    $videoTitle = $videoDetails->title;
                    mdump('title = ' . $videoTitle);
//                $videoUrl = $streamingDataFormats[1]->url;
                    $videoUrl = $array['url'];
                    $sha1Name = sha1($videoTitle);
                    $shaNameOriginal = $sha1Name . '_original';
                    $fileName = $shaNameOriginal . '.mp4';
                    $targetDir = $dataPath . "Video_Store/full/";
                    $targetFile = $targetDir . $fileName;
                    mdump('url = ' . $videoUrl);
                    //file_put_contents($targetFile, fopen($videoUrl, 'r'));
                    //file_put_contents($targetFile, fopen($url, 'r'));
                    mdump('target file = ' . $targetFile);
                    //$this->downloadFile($videoUrl, $targetFile);
                    $this->downloadFile($url, $targetFile);
                }
            } else {
                mdump($dataVideo->localfile);
                list($name, $extension) = explode('.', $dataVideo->localfile['name']);
                $originalFile = $dataVideo->localfile['name'];
                $videoTitle = $name;
                $sha1Name = sha1($name);
                $shaNameOriginal = $sha1Name . '_original';
                $fileName = $shaNameOriginal . '.mp4';
                $targetDir = $dataPath . "Video_Store/full/";
                $targetFile = $targetDir . $fileName;
                mdump($targetFile);
                mdump($dataVideo->localfile["tmp_name"]);
                move_uploaded_file($dataVideo->localfile["tmp_name"], $targetFile);

//            list($name, $extension) = explode('.', $dataVideo->localfile->getName());
//            $originalFile = $dataVideo->localfile->getName();
//            $videoTitle = $name;
//            $sha1Name = sha1($name);
//            $shaNameOriginal = $sha1Name . '_original';
//            $fileName = $shaNameOriginal . '.mp4';
//            $targetDir = $dataPath . "Video_Store/full/";
//            $targetFile = $targetDir . $fileName;
//            mdump($targetFile);
//            mdump($dataVideo->localfile->getTmpName());
//            move_uploaded_file($dataVideo->localfile->getTmpName(), $targetFile);
            }
            //$user = fnbr\models\Base::getCurrentUser();
            $videoFile = str_replace('_original', '', $targetFile);
            $documentMM = new fnbr\models\DocumentMM($idDocumentMM);
            $data = $documentMM->getData();
            $data->title = $videoTitle;
            $data->originalFile = $originalFile;
            $data->sha1Name = $sha1Name;
            $data->videoPath = $videoFile;//$targetFile;
            $data->idLanguage = $idLanguage;
            mdump($data);
            $documentMM->saveMMData($data);
            //$offline = '"' . addslashes(realpath(Manager::getAppPath() . "/offline/uploadVideoMM.php")) . '" ' . "{$targetFile} {$dataVideo->idDocument} {$dataVideo->idLanguage} {$user->getIdUser()} {$user->getEmail()}";
            //mdump("php {$offline} > /dev/null &");
            //exec("php {$offline} > /dev/null &");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function preprocess($idDocumentMM, $preprocess, $idUser)
    {
        $documentMM = new fnbr\models\DocumentMM($idDocumentMM);
        $param = [
            'video' => $preprocess->video ? 1 : 0,
            'audio' => $preprocess->audio ? 1 : 0,
            's2t' => $preprocess->s2t ? 1 : 0,
            'frames' => $preprocess->frames ? 1 : 0,
            'cc' => $preprocess->cc ? 1 : 0,
            'yolo' => $preprocess->yolo ? 1 : 0,
        ];
        $p = base64_encode(json_encode($param));
        $offline = '"' . addslashes(realpath(Manager::getAppPath() . "/offline/multimodal/videoPreprocess.php")) . '" ' . "{$idDocumentMM} {$idUser} {$p}";
        mdump("php {$offline} > /dev/null &");
        exec("php {$offline} > /dev/null &");

    }

    public function renderVideo($idDocumentMM)
    {
        $idUser = 0;
        $offline = '"' . addslashes(realpath(Manager::getAppPath() . "/offline/multimodal/renderVideo.php")) . '" ' . "{$idDocumentMM} {$idUser}";
        mdump("php {$offline} > /dev/null &");
        exec("php {$offline} > /dev/null &");
    }

    function getVideoInfo($video_id)
    {

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://www.youtube.com/youtubei/v1/player?key=AIzaSyAO_FJ2SlqU8Q4STEHLGCilw_Y9_11qcW8');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{  "context": {    "client": {      "hl": "en",      "clientName": "WEB",      "clientVersion": "2.20210721.00.00",      "clientFormFactor": "UNKNOWN_FORM_FACTOR",   "clientScreen": "WATCH",      "mainAppWebInfo": {        "graftUrl": "/watch?v=' . $video_id . '",           }    },    "user": {      "lockedSafetyMode": false    },    "request": {      "useSsl": true,      "internalExperimentFlags": [],      "consistencyTokenJars": []    }  },  "videoId": "' . $video_id . '",  "playbackContext": {    "contentPlaybackContext": {        "vis": 0,      "splay": false,      "autoCaptionsDefaultOn": false,      "autonavState": "STATE_NONE",      "html5Preference": "HTML5_PREF_WANTS",      "lactMilliseconds": "-1"    }  },  "racyCheckOk": false,  "contentCheckOk": false}');
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);
        return $result;

    }

    private function downloadFile($url, $path)
    {
        try {
            $param = [
                'url' => $url,
                'path' => $path,
            ];
            $p = base64_encode(json_encode($param));
            $offline = '"' . addslashes(realpath(Manager::getAppPath() . "/offline/multimodal/downloadVideo.php")) . '" ' . "{$p}";
            mdump("php {$offline} > /dev/null &");
            exec("php {$offline} > /dev/null &");

//            $youtube = new YouTubeDownloader();
//
//                $downloadOptions = $youtube->getDownloadLinks("https://www.youtube.com/watch?v=aqz-KE-bpKQ");
//
//                if ($downloadOptions->getAllFormats()) {
//                    $url = $downloadOptions->getFirstCombinedFormat()->url;
//                    mdump('===='. $url);
//                    file_put_contents($path, file_get_contents($url));
//                } else {
//                    //echo 'No links found';
//                    throw new \Exception('Download video file has failed!');
//                }


            //

//            $newfname = $path;
//            $i = 0;
//            $file = fopen($url, 'rb');
//            if ($file) {
//                unlink($newfname);
//                $newf = fopen($newfname, 'wb');
//                if ($newf) {
//                    while (!feof($file)) {
//                        mdump('writing...' . $i++);
//                        //fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
//                        fwrite($newf, fread($file, 1024 * 1024), 1024 * 1024);
//                    }
//                }
//            }
//            if ($file) {
//                fclose($file);
//            }
//            if ($newf) {
//                fclose($newf);
//            }
//            //
//            ini_set('max_execution_time', 0);
//            $options = array(
//                CURLOPT_FILE => is_resource($path) ? $path : fopen($path, 'wb'),
//                CURLOPT_FOLLOWLOCATION => true,
//                CURLOPT_URL => $url,
//                CURLOPT_FAILONERROR => true, // HTTP code > 400 will throw curl error
//                CURLOPT_BUFFERSIZE => 1024 * 1024
//            );
//
//            $ch = curl_init();
//            curl_setopt_array($ch, $options);
//            $return = curl_exec($ch);
//            mdump('== return = ' . ($return ? 'true' : 'false'));
//            if ($return === false) {
//                throw new \Exception('Download video file has failed!');
//            }
//            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function getPreprocessingStatus($idDocumentMM)
    {
        $result = new \stdClass();
        $documentMM = new fnbr\models\DocumentMM($idDocumentMM);
        $document = new fnbr\models\Document($documentMM->getIdDocument());
        $result->document = $document->getData();
        $result->documentMM = $documentMM->getData();
        $sha1Name = $documentMM->getSHA1Name();
        //$dataPath = '/var/www/html/apps/webtool/files/multimodal/';
        $dataPath = '/home/framenetbr/devel/fnbr/charon_docker_maestro/apps/webtool/files/multimodal/';
        $result->videoPreprocessed = (file_exists($documentMM->getVideoPath()));
        $audioFile = $dataPath . 'Audio_Store/audio/' . $sha1Name . '.flac';
        $result->audioCreated = (file_exists($audioFile));
        $s2tFile = $dataPath . 'Text_Store/transcripts/' . $sha1Name . '_' . $documentMM->getIdLanguage() . '.json';
        $result->s2tDone = (file_exists($s2tFile));
        $framesPath = $dataPath . "Video_Frames/" . $sha1Name;
        if (file_exists($framesPath)) {
            $images = array_diff(scandir($framesPath), ['..', '.']);
        } else {
            $images = [];
        }
        $result->extractedFrames = count($images);
        $objectMM = new fnbr\models\ObjectMM();
        $result->yoloObjects = count($objectMM->listByFilter((object)[
            'idDocumentMM' => $idDocumentMM,
            'origin' => 1
        ])->asQuery()->getResult());
        $wordMM = new fnbr\models\WordMM();
        $result->ccSentences = count($wordMM->listByFilter((object)[
            'idDocumentMM' => $idDocumentMM,
            'origin' => 1
        ])->asQuery()->getResult());
        return $result;
    }


}

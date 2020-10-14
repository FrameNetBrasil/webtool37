<?php

error_reporting(0);

class DocumentMMService extends MService
{
    /**
     * @param $dataVideo {idLanguage, webfile, localfile}
     *
     * 1. get the file
     * 1.1. if (localfile) then upload_file to Video_Store
     * 1.2. if (webfile) then download_file to Video_Store
     * 2. run offline uploadVideoMM to preprocess the video
     *
     */
    public function uploadVideo($dataVideo)
    {
        $config = [
            'dataPath' => '/var/www/html/apps/webtool/files/multimodal/'
        ];
        $dataPath = $config['dataPath'];
        if ($dataVideo->webfile != '') {
            $url = $dataVideo->webfile;
            parse_str(parse_url($url, PHP_URL_QUERY), $vars);
            $vid = $vars['v'];
            if ($vid) {
                parse_str(file_get_contents("http://youtube.com/get_video_info?video_id=" . $vid), $info); //decode the data
                $videoData = json_decode($info['player_response'], true);
                $videoDetails = $videoData['videoDetails'];
                $streamingData = $videoData['streamingData'];
                $streamingDataFormats = $streamingData['formats'];
                $videoTitle = $videoDetails["title"];
                $videoUrl = $streamingDataFormats[1]['url'];
                $shaNameOriginal = sha1($videoTitle) . '_original';
                $fileName = $shaNameOriginal . '.mp4';
                $targetDir = $dataPath . "Video_Store/full/";
                $targetFile = $targetDir . $fileName;
                file_put_contents($targetFile, fopen($videoUrl, 'r'));
            }
        } else {
            list($name, $extension) = explode('.', $dataVideo->localfile->getName());
            $shaNameOriginal = sha1($name) . '_original';
            $fileName = $shaNameOriginal . '.' . $extension;
            $targetDir = $dataPath . "Video_Store/full/";
            $targetFile = $targetDir . $fileName;
            file_put_contents($targetFile, file_get_contents($dataVideo->localfile->getTmpName()));
        }
        $user = fnbr\models\Base::getCurrentUser();
        $offline = '"' . addslashes(realpath(Manager::getAppPath() . "/offline/uploadVideoMM.php")) . '" ' . "{$targetFile} {$dataVideo->idDocument} {$dataVideo->idLanguage} {$user->getIdUser()} {$user->getEmail()}";
        mdump("php {$offline} > /dev/null &");
        exec("php {$offline} > /dev/null &");

    }



}

<?php

use App\Models\RelationModel;
use App\Models\FrameModel;
use App\Models\FrameTopModel;

$baseDir = realpath(dirname(dirname(dirname(dirname(__FILE__)))));

//ini_set("error_reporting", "E_ALL & ~E_NOTICE & ~E_STRICT");
ini_set("display_errors", "1");
ini_set("log_errors", "1");
ini_set("error_log", "php_error.log");
ini_set("session.save_path", "{$baseDir}/var/sessions");

require $baseDir . '/vendor/autoload.php';
set_error_handler('errorHandler');

try {
    Orkester\Manager::init();
    $db = Orkester\Manager::getPersistence('fnbr');
    $relationModel = new RelationModel($db);
    $frameModel = new FrameModel($db);
    $dbConstrual = Orkester\Manager::getPersistence('construal');
    $frameTopModel = new FrameTopModel($dbConstrual);
    // obtem os frames da borda (não tem filhos por herança, perspectiva ou subframe
    $borderFrames = [];
    foreach ($frameTopModel as $idFrame => $frame) {
        $frameEntry = $frame->get('frameBase');
        $children = $relationModel->listChildrenForFrame($frameEntry);
        if (count($children) == 0) {
            $borderFrames[] = $frameEntry;
        }
    }
    $score = [];
    foreach ($borderFrames as $borderFrame) {
        $score[$borderFrame] = 1.0;
    }
    foreach ($borderFrames as $i => $borderFrame) {
        print_r($i . ' - ' . $borderFrame . "\n");
        $entries = [$borderFrame];
        do {
            $j = 0;
            $next = [];
            foreach ($entries as $entry) {
                $scoreAnt = $score[$entry];
                $parents = $relationModel->listParentForFrame($entry);
                $j = $j + 4;
                foreach ($parents as $parent) {
                    $parentFrame = $parent['entry'];
                    //print_r(str_repeat(' ', $j) . $parentFrame . "\n");
                    if (!isset($score[$parentFrame])) {
                        $score[$parentFrame] = 0.0;
                    }
                    $sta = -1 * ($score[$parentFrame] + $scoreAnt);
                    $o = (1 - exp(25 * $sta)) / (1 + exp(2 * $sta));
                    $score[$parentFrame] = $o * 0.8;
                    $next[] = $parentFrame;
                }
            }
            $entries = $next;
        } while (count($entries) > 0);
    }
    print_r(count($score));

    foreach($score as $frameEntry => $scoreFrame) {
        $frameTopModel = new FrameTopModel($dbConstrual);
        $frameTopModel->addCondition('frameBase', '=', $frameEntry);
        foreach($frameTopModel as $f) {
            $f->set('score', $scoreFrame);
            $f->save();
        }
    }


    /*


        $md5 = [];
        foreach($frameTopModel as $idFrame => $frame) {
            $handled = [];
            $tops = [];
            $frameEntry = $frame->get('frameBase');
            //print_r('frame = ' . $frameEntry . "\n");
            $entries = [$frameEntry];
            do {
                //print_r($entries);
                $next = [];
                $tops = $entries;
                foreach($entries as $entry) {
                    if (!isset($handled[$entry])) {
                        //print_r($entry . "\n");
                        $parents = $relationModel->listParentForFrame($entry);
                        foreach ($parents as $parent) {
                            $next[] = $parent['entry'];
                        }
                        $handled[$entry] = true;
                    }
                }
                $entries = $next;
            } while(count($entries) > 0);
            foreach($tops as $top) {
                $x = md5($frameEntry . $top);
                //print_r($frameEntry . ' - '. $top . "\n");
                if (!isset($md5[$x])) {
                    $frameTopModel->insert([
                        'frameBase' => $frameEntry,
                        'frameTop' => $top
                    ]);
                    $md5[$x] = true;
                }
            }
        }
    */
} catch (Exception $e) {
    print_r($e->getMessage());
}


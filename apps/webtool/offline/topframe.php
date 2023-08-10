<?php
include "offline.php";

$app = 'webtool';
$db = 'webtool';

$configFile = Manager::getHome() . "/apps/{$app}/conf/conf.php";
Manager::loadConf($configFile);
Manager::setConf('logs.level', 2);
Manager::setConf('logs.port', 9998);

Manager::setConf('fnbr.db', $db);

try {

    $db = Manager::getDatabase('webtool');
    $transaction = $db->beginTransaction();
    $cmd = <<<HERE
    delete from topframe where (idTopFrame > 0)
HERE;
    $db->executeCommand($cmd);

    $cmd = <<<HERE
    select idFrame, entry, idEntity from frame
HERE;
    $result = $db->getQueryCommand($cmd)->getResult();
    foreach($result as $row) {
        $handled = [];
        $tops = [];
        $frameEntry = $row['entry'];
        $entries = [$frameEntry];
        do {
            $next = [];
            $tops = $entries;
            foreach($entries as $entry) {
                if (!isset($handled[$entry])) {
                    $cmd = <<<HERE
    select f1.idFrame, f1.entry, f1.idEntity
    from view_relation r 
    join frame f1 on (f1.idEntity = r.idEntity1)
    join frame f2 on (f2.idEntity = r.idEntity2)
    where (r.relationType in ('rel_inheritance','rel_perspective_on'))
    and (f2.entry = '{$entry}')

HERE;
                    $parents = $db->getQueryCommand($cmd)->getResult();
                    foreach ($parents as $parent) {
                        $next[] = $parent['entry'];
                    }
                    $handled[$entry] = true;
                }
            }
//            print_r($next);
            $entries = $next;
        } while(count($entries) > 0);
        $md5 = [];
//        print_r($tops);
        foreach($tops as $top) {
            $x = md5($frameEntry . $top);
            if (!isset($md5[$x])) {
                if (($top == 'frm_event')
                || ($top == 'frm_relation')
                    || ($top == 'frm_state')
                    || ($top == 'frm_entity')
                    || ($top == 'frm_attribute')
                ) {
                    $category = $top;
                } else {
                    $category = "indefinite";
                }

                print_r($frameEntry . ' - '. $top . ' - ' . $category . "\n");

                $cmd = <<<HERE
    insert into topframe(frameBase, frameTop, frameCategory)
    values ('{$frameEntry}','{$top}','{$category}')
HERE;
                print_r($cmd);
                $db->executeCommand($cmd);
                $md5[$x] = true;
            }
        }
    }
    $transaction->commit();

} catch (Exception $e) {
    print_r($e->getMessage());
}


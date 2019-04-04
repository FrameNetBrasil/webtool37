<?php

class GraphicalController extends MController
{

    private $idLanguage;

    public function init()
    {
        Manager::checkLogin(false);
        $this->idLanguage = Manager::getSession()->idLanguage;
        $languages = \fnbr\models\Base::languages();
        $msgDir = Manager::getAppPath('conf/report');
        Manager::$msg->file = 'messages.' . $languages[$this->idLanguage] . '.php';
        Manager::$msg->addMessages($msgDir);
        Manager::setConf('theme.template','annotation');
    }

    public function annotationtarget()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        Manager::getSession()->idDomain = $this->data->idDomain;
        $as = new \fnbr\models\ViewAnnotationSet();
        $result = $as->listCountTargetInTextByLanguage();
        $max = 0;
        foreach($result as $i => $r) {
            $max = ($r['n'] > $max) ? $r['n'] : $max;
        }
        foreach($result as $i => $r) {
            $result[$i]['log'] = ($r['n']/$max) * 100;
        }
        foreach($result as $i => $r) {
            $result[$i]['n'] = substr($result[$i]['n'] * 100, 0, 4) . '%';
            $result[$i]['percent'] = (int) ($r['log']) ;
        }

        $this->data->resultTargets = $result;

        $this->render();
    }
    public function annotationlu()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        Manager::getSession()->idDomain = $this->data->idDomain;
        $as = new \fnbr\models\ViewAnnotationSet();
        $result = $as->listLUCountByLanguage();
        $max = 0;
        foreach($result as $i => $r) {
            $max = ($r['n'] > $max) ? $r['n'] : $max;
        }
        foreach($result as $i => $r) {
            $result[$i]['log'] = ($r['n']/$max) * 100;//(int)(log(($r['n']/$max) * 1000));
        }
        foreach($result as $i => $r) {
            $result[$i]['percent'] = (int) ($r['log']) ;
        }

        $this->data->resultLUs = $result;

        $this->render();
    }
    public function annotationas()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        Manager::getSession()->idDomain = $this->data->idDomain;
        $as = new \fnbr\models\ViewAnnotationSet();
        $result = $as->listASCountByLanguage();
        $max = 0;
        foreach($result as $i => $r) {
            $max = ($r['n'] > $max) ? $r['n'] : $max;
        }
        foreach($result as $i => $r) {
            $result[$i]['log'] = ($r['n']/$max) * 100;//(int)(log(($r['n']/$max) * 1000));
        }
        foreach($result as $i => $r) {
            $result[$i]['percent'] = (int) ($r['log']) ;
        }

        $this->data->resultASs = $result;

        $this->render();
    }

}

<?php

class LemmaController extends MController
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
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        Manager::getSession()->idDomain = $this->data->idDomain;
        $this->render();
    }

    public function lemmaTree()
    {
        $this->data->idDomain = Manager::getSession()->idDomain;
        $report = Manager::getAppService('reportlemma');
        if ($this->data->id == '') {
            $children = $report->listLemmas($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Lemmas',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'f') {
            $json = $report->listLUs(substr($this->data->id, 1), $this->idLanguage);
        }
        $this->renderJson($json);
    }
    
    public function showLemma() {
        $idLemma = $this->data->id;
        $report = Manager::getAppService('reportlemma');
        $lemma = new fnbr\models\Lemma($idLemma);
        $this->render();
    }
    
}

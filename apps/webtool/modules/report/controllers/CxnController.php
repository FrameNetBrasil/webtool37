<?php





class CxnController extends MController
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
        $this->render();
    }

    public function cxnTree()
    {
        $report = Manager::getAppService('reportcxn');
        if ($this->data->id == '') {
            $children = $report->listCxns($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Constructions',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'f') {
            $json = $report->listCEs(substr($this->data->id, 1), $this->idLanguage);
        }
        $this->renderJson($json);
    }
    
    public function showCxn() {
        $idConstruction = $this->data->id;
        $report = Manager::getAppService('reportcxn');
        $cxn = new fnbr\models\Construction($idConstruction);
        $this->data->cxn->entry = $cxn->getEntryObject();
        $this->data->cxn->idEntity = $cxn->getIdEntity();
        $this->data->ce = $report->getCEData($idConstruction);
        $this->data->cxn->entry->description = $report->decorate($this->data->cxn->entry->description, $this->data->ce['styles']);
        $this->data->relations = $report->getRelations($cxn);
        $this->data->constraints = $report->listConstraintsEvokesCX($cxn, $this->idLanguage);
        $this->data->constraintsCE = $report->listConstraintsEvokesCE($this->data->ce, $this->idLanguage);
        mdump('=================================');
        mdump($this->data->constraintsCE);
        $this->render();
    }
    
}

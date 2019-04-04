<?php

class LUController extends MController
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

    public function showLU() {
        $idLU = $this->data->id;
        $report = Manager::getAppService('reportlu');
        $lu = new fnbr\models\LU();
        $lu->getById($idLU);
        $this->data->lu = $lu->getData();
        $frame = new fnbr\models\Frame($lu->getIdFrame());
        $this->data->lu->frameName = $frame->getName();
        $result = $report->getFERealizations($lu);
        $this->data->realizations = $result['realizations'];
        $this->data->fes = $result['fes'];
        $this->data->vp = $result['vp'];
        $this->data->vpfe = $result['vpfe'];
        $this->data->maxCountFE = $result['maxCountFE'];
        $this->data->patterns = $result['patterns'];
        $this->data->realizationAS = $result['realizationAS'];
        $this->data->feAS = MUtil::php2js($result['feAS']);
        $this->data->patternFEAS = MUtil::php2js($result['patternFEAS']);
        $this->data->patternAS = MUtil::php2js($result['patternAS']);

        $report = Manager::getAppService('reportframe');
        $this->data->fe = $report->getFEData($frame->getIdFrame());
        $this->data->feIcon = [
            "cty_core" => "fa fa-circle",
            "cty_peripheral" => "fa fa-dot-circle-o",
            "cty_extra-thematic" => "fa fa-circle-o",
            "cty_core-unexpressed" => "fa fa-circle-o"
        ];
        $this->render();
    }


}

<?php

class QualiaController extends MController
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

    public function showStructure() {
        $typeInstance = new fnbr\models\TypeInstance();
        $this->data->qualiaType = $typeInstance->gridDataAsJson($typeInstance->listQualiaType(), true);
        $this->render();
    }

    public function showRelations() {
        $typeInstance = new fnbr\models\TypeInstance();
        $this->data->qualiaType = $typeInstance->gridDataAsJson($typeInstance->listQualiaType(), true);
        $this->render();
    }

}

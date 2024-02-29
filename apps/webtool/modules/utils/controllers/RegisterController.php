<?php

class RegisterController extends MController
{

    private $idLanguage;

    public function init()
    {
        //parent::init();
        $this->idLanguage = Manager::getConf('fnbr.lang');
    }

    public function formRegisterLexWf()
    {
        $language = new fnbr\models\Language(); 
        $this->data->languages = $language->listForCombo()->asQuery()->chunkResult('idLanguage','language');
        $this->data->action = '@utils/register/registerLexWf';
        $this->render();
    }
    
    public function registerLexWf(){
        try {
            $rows = explode("\n",$this->data->pairs);
            $model = new fnbr\models\Lexeme();
            $model->registerLexemeWordform($this->data, $rows);
            $this->renderPrompt('information','Wordforms loaded successfully.');
        } catch (EMException $e) {
            $this->renderPrompt('error',$e->getMessage());
        }
    }

    public function formRegisterLemma()
    {
        $language = new fnbr\models\Language();
        $this->data->languages = $language->listAll()->asQuery()->chunkResult('idLanguage', 'language');
        $this->data->action = '@utils/register/registerLemma';
        $this->render();
    }

    public function registerLemma()
    {
        try {
            $rows = explode("\n",$this->data->pairs);
            $model = new fnbr\models\Lemma();
            $model->registerLemma($this->data, $rows);
            $this->renderPrompt('information','Lemma(s) registered successfully.');
        } catch (EMException $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
}

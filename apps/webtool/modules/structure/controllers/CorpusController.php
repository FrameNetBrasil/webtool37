<?php

class CorpusController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = Manager::getSession()->idLanguage;
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->render();
    }

    public function corpusTree()
    {
        $structure = Manager::getAppService('structurecorpus');
        if ($this->data->id == '') {
            $children = $structure->listCorpus($this->data, $this->idLanguage);
            $data = (object) [
                        'id' => 'root',
                        'state' => 'open',
                        'text' => 'Corpus',
                        'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'c') {
            $json = json_encode($structure->listDocuments(substr($this->data->id, 1), $this->idLanguage));
        }
        $this->renderJson($json);
    }

    public function formNewCorpus()
    {
        $this->data->save = "@structure/corpus/newCorpus|formNewCorpus";
        $this->data->close = "!$('#formNewCorpus_dialog').dialog('close');";
        $this->data->title = _M('new fnbr\models\Corpus');
        $this->render();
    }

    public function formUpdateCorpus()
    {
        $model = new fnbr\models\Corpus($this->data->id);
        $this->data->object = $model->getData();
        $this->data->object->entry = strtolower(str_replace('crp_','',$this->data->object->entry));
        $this->data->save = "@structure/corpus/updateCorpus|formUpdateCorpus";
        $this->data->close = "!$('#formUpdateCorpus_dialog').dialog('close');";
        $this->data->title = 'Corpus: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function formNewDocument()
    {
        $this->data->idCorpus = $this->data->id;
        $model = new fnbr\models\Corpus($this->data->idCorpus);
        $this->data->corpus = $model->getEntry() . '  [' . $model->getName() . ']';
        $this->data->save = "@structure/corpus/newDocument|formNewDocument";
        $this->data->close = "!$('#formNewDocument_dialog').dialog('close');";
        $this->data->title = _M('new fnbr\models\Document');
        $this->render();
    }

    public function formUpdateDocument()
    {
        $model = new fnbr\models\Document($this->data->id);
        $this->data->object = $model->getData();
        $this->data->save = "@structure/corpus/updateDocument|formUpdateDocument";
        $this->data->close = "!$('#formUpdateDocument_dialog').dialog('close');";
        $this->data->title = 'Document: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function newCorpus()
    {
        try {
            $model = new fnbr\models\Corpus();
            $this->data->corpus->entry = 'crp_' . strtolower(str_replace('crp_','',$this->data->corpus->entry));
            $model->setData($this->data->corpus);
            $model->save($this->data->corpus);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->corpus->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function updateCorpus()
    {
        try {
            $model = new fnbr\models\Corpus($this->data->corpus->idCorpus);
            $this->data->corpus->entry = 'crp_' . strtolower(str_replace('crp_','',$this->data->corpus->entry));
            $model->updateEntry($this->data->corpus->entry);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->corpus->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function newDocument()
    {
        try {
            $model = new fnbr\models\Document();
            $this->data->document->entry = 'doc_' . $this->data->document->entry;
            $model->setData($this->data->document);
            $model->save($this->data->document);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->document->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function updateDocument()
    {
        try {
            $model = new fnbr\models\Document($this->data->document->idDocument);
            $model->updateEntry($this->data->document->entry);
            $model->setData($this->data->document);
            $model->save($this->data->document);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->document->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}

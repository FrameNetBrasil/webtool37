<?php





class WFLexController extends MController
{
    public function main()
    {
        $this->data->query = Manager::getAppURL('', 'admin/wflex/gridData');
        $this->render();
    }
    
    public function gridData()
    {
        $model = new fnbr\models\ViewWfLexemeLemma();
        $criteria = $model->listByFilter($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }
    
    public function formObject()
    {
        $model = new fnbr\models\ViewWfLexemeLemma($this->data->id);
        $this->data->forUpdate = ($this->data->id != '');
        $this->data->object = $model->getData();
        $this->data->title = $this->data->forUpdate ? $model->getDescription() : _M("New");
        $this->data->save = "@admin/wflex/save/" . $model->getId() . '|formObject';
        $this->data->delete = "@admin/wflex/delete/" . $model->getId() . '|formObject';
        $this->render();
    }

    public function save()
    {
        try {
            $model = new fnbr\models\ViewWfLexemeLemma();
            //$this->data->genre->entry = 'gen_' . $this->data->genre->entry;
            $model->setData($this->data->genre);
            $model->save();
            $this->renderPrompt('information', 'OK', "editEntry('{$this->data->genre->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
    
}

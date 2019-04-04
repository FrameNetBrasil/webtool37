<?php





class TypeController extends MController
{
    public function main()
    {
        $this->data->query = Manager::getAppURL('', 'admin/type/gridData');
        $this->render();
    }
    
    public function gridData()
    {
        $model = new fnbr\models\Type();
        $criteria = $model->listByFilter($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }
    
    public function formObject()
    {
        $model = new fnbr\models\Type($this->data->id);
        $this->data->forUpdate = ($this->data->id != '');
        $this->data->object = $model->getData();
        $this->data->title = $this->data->forUpdate ? $model->getDescription() : _M("new fnbr\models\Type");
        $this->data->save = "@admin/type/save/" . $model->getId() . '|formObject';
        $this->data->delete = "@admin/type/delete/" . $model->getId() . '|formObject';
        $this->render();
    }

    public function save()
    {
        try {
            $model = new fnbr\models\Type();
            $this->data->type->entry = 'typ_' . $this->data->type->entry;
            $model->setData($this->data->type);
            $model->save();
            $this->renderPrompt('information', 'OK', "editEntry('{$this->data->type->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
    
}

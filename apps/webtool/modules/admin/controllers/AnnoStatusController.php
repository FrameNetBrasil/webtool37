<?php





class AnnoStatusController extends MController
{
    public function main()
    {
        $this->data->query = Manager::getAppURL('', 'admin/annostatus/gridData');
        $this->render();
    }
    
    public function gridData()
    {
        $model = new fnbr\models\TypeInstance();
        $criteria = $model->listAnnotationStatus($this->data->filter);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }
    /*
    public function main()
    {
        $model = new fnbr\models\TypeInstance();
        $annostatus = $model->listAnnotationStatus($this->data->filter)->asQuery()->getResult(\FETCH_ASSOC);;
        $data = [];
        foreach($annostatus as $as) {
            $style = 'background-color:#' . $as['rgbBg'] . ';color:#' . $as['rgbFg'] . ';';
            $decorated = "<span style='{$style}'>" . $as['name'] . "</span>";            
            $data[] = (object) [
                'idColor' => $as['idColor'],
                'decorated' => $decorated,
                'entry' => $as['entry'],
                'name' => $as['name']
            ];
        }
        $this->data->data = json_encode($data);
        $this->render();
    }
    
     * 
     */
    public function formColor()
    {
        $model = new fnbr\models\TypeInstance($this->data->id);
        $this->data->title = $model->getEntry() . ':: Color';
        $this->data->idColor = $model->getIdColor();
        $this->data->save = "@admin/annostatus/saveColor/" . $model->getId() . '|formColor';
        $this->render();
    }

    public function saveColor()
    {
        try {
            $model = new fnbr\models\TypeInstance($this->data->idAnnotationStatus);
            $model->setIdColor($this->data->idColor);
            $model->save();
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $model = new fnbr\models\Person($this->data->id);
            $model->delete();
            $go = "!$('#formObject_dialog').dialog('close');";
            $this->renderPrompt('information', _M("Record [%s] removed.", $model->getDescription()), $go);
        } catch (\Exception $e) {
            $this->renderPrompt('error', _M("Deletion denied."));
        }
    }

    
}

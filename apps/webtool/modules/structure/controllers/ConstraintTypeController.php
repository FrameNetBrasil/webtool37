<?php


class ConstraintTypeController extends MController
{

    private $idLanguage;

    public function init()
    {
        parent::init();
        $this->idLanguage = \Manager::getSession()->idLanguage;
    }

    public function main()
    {
        $this->data->isMaster = Manager::checkAccess('MASTER', A_EXECUTE) ? 'true' : 'false';
        $this->render();
    }

    public function constraintTypeTree()
    {
        $structure = Manager::getAppService('StructureConstraintType');
        if ($this->data->id == '') {
            $children = $structure->listConstraints($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'text' => 'Constraint_Types',
                'children' => $children
            ];
            $json = json_encode([$data]);
        }
        $this->renderJson($json);
    }

    public function newConstraintType()
    {
        try {
            $model = new fnbr\models\ConstraintType();
            $model->save($this->data->ct);
            $this->renderJson(json_encode(['success' => true]));
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function updateConstraintType()
    {
        try {
            $model = new fnbr\models\ConstraintType($this->data->ct->idConstraintType);
            $model->save($this->data->ct);
            $this->renderJson(json_encode(['success' => true]));
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function deleteConstraintType()
    {
        try {
            $model = new fnbr\models\ConstraintType($this->data->id);
            if ($model->hasInstances()) {
                $this->renderPrompt('error', "ConstraintType has instances.");
            } else {
                $model->delete();
                $this->renderPrompt('information', "ConstraintType removed.", "structure.reload();");
            }
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}

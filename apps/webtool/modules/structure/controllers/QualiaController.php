<?php

class QualiaController extends MController
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
        $this->data->isAnno = 'false';
        $typeInstance = new fnbr\models\TypeInstance();
        $this->data->qualiaType = $typeInstance->gridDataAsJson($typeInstance->listQualiaType(), true);
        $this->render();
    }

    public function frameTree()
    {
        $structure = Manager::getAppService('structureframe');
        if ($this->data->id == '') {
            $children = $structure->listFrames($this->data, $this->idLanguage);
            $data = (object)[
                'id' => 'root',
                'state' => 'open',
                'iconCls' => 'icon-blank fa fa-sitemap fa16px entity_frame',
                'text' => 'Frames for Qualia',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } else if ($this->data->id == 'root') {
            $children = $structure->listFrames($this->data, $this->idLanguage);
            $json = json_encode($children);
        } elseif ($this->data->id{0} == 'f') {
            $json = $structure->listFEsLUsQualias(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'q') {
            $json = $structure->listQualiaFEs(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'l') {
            $model = new fnbr\models\Qualia();
            $constraints = $model->listLUQualia(substr($this->data->id, 1));
            foreach ($constraints as $constraint) {
                $node = [];
                $node['id'] = 'y' . $constraint['idConstraint'];
                $node['text'] = $constraint['name'] . ' [' . $constraint['relation'] . ']';
                $node['state'] = 'closed';
                $node['iconCls'] = 'icon-blank fa-icon fa fa-crosshairs';
                $result[] = $node;
            }
            $json = json_encode($result);
        }
        $this->renderJson($json);
    }


    public function formQualiaFormal()
    {
        $this->data->title = 'Formal Qualia';
        $this->data->qualiaType = 'qla_formal';
        $this->data->idFrame = $this->data->id;
        $frame = new fnbr\models\Frame($this->data->idFrame);
        $this->data->frame = $frame->getEntry() . '  [' . $frame->getName() . ']';
        $this->data->close = "!$('#formQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newQualia|formQualia";
        $this->render('formQualia');
    }

    public function formQualiaAgentive()
    {
        $this->data->title = 'Agentive Qualia';
        $this->data->qualiaType = 'qla_agentive';
        $this->data->idFrame = $this->data->id;
        $frame = new fnbr\models\Frame($this->data->idFrame);
        $this->data->frame = $frame->getEntry() . '  [' . $frame->getName() . ']';
        $this->data->close = "!$('#formQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newQualia|formQualia";
        $this->render('formQualia');
    }

    public function formQualiaTelic()
    {
        $this->data->title = 'Telic Qualia';
        $this->data->qualiaType = 'qla_telic';
        $this->data->idFrame = $this->data->id;
        $frame = new fnbr\models\Frame($this->data->idFrame);
        $this->data->frame = $frame->getEntry() . '  [' . $frame->getName() . ']';
        $this->data->close = "!$('#formQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newQualia|formQualia";
        $this->render('formQualia');
    }

    public function formQualiaConstitutive()
    {
        $this->data->title = 'Constitutive Qualia';
        $this->data->qualiaType = 'qla_constitutive';
        $this->data->idFrame = $this->data->id;
        $frame = new fnbr\models\Frame($this->data->idFrame);
        $this->data->frame = $frame->getEntry() . '  [' . $frame->getName() . ']';
        $this->data->close = "!$('#formQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newQualia|formQualia";
        $this->render('formQualia');
    }

    public function newQualia()
    {
        try {
            $model = new fnbr\models\Qualia();
            $model->saveData($this->data->qualia);
            $this->renderPrompt('ok', 'Qualia created.',"!$('#formQualia_dialog').dialog('close');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formDeleteQualia()
    {
        $ok = "^structure/qualia/deleteQualia/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Atenção: A estrutura qualia será removida! Continua?', $ok);
    }

    public function deleteQualia()
    {
        try {
            $model = new fnbr\models\Qualia($this->data->id);
            $model->delete();
            $this->renderPrompt('information', 'OK', "!structure.reloadParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }

    }

    public function formLUQualiaFormal()
    {
        $this->data->title = 'LU Formal Qualia';
        $this->data->qualiaType = 'qla_formal';
        $this->data->idLU = $this->data->id;
        $LU = new fnbr\models\LU($this->data->idLU);
        $this->data->lu = 'LU1: ' . $LU->getFullName();
        $this->data->close = "!$('#formLUQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newLUQualia|formLUQualia";
        $this->render('formLUQualia');
    }

    public function formLUQualiaAgentive()
    {
        $this->data->title = 'LU Agentive Qualia';
        $this->data->qualiaType = 'qla_agentive';
        $this->data->idLU = $this->data->id;
        $LU = new fnbr\models\LU($this->data->idLU);
        $this->data->lu = 'LU1: ' . $LU->getFullName();
        $this->data->close = "!$('#formLUQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newLUQualia|formLUQualia";
        $this->render('formLUQualia');
    }

    public function formLUQualiaTelic()
    {
        $this->data->title = 'LU Telic Qualia';
        $this->data->qualiaType = 'qla_telic';
        $this->data->idLU = $this->data->id;
        $LU = new fnbr\models\LU($this->data->idLU);
        $this->data->lu = 'LU1: ' . $LU->getFullName();
        $this->data->close = "!$('#formLUQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newLUQualia|formLUQualia";
        $this->render('formLUQualia');
    }

    public function formLUQualiaConstitutive()
    {
        $this->data->title = 'LU Constitutive Qualia';
        $this->data->qualiaType = 'qla_constitutive';
        $this->data->idLU = $this->data->id;
        $LU = new fnbr\models\LU($this->data->idLU);
        $this->data->lu = 'LU1: ' . $LU->getFullName();
        $this->data->close = "!$('#formLUQualia_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/newLUQualia|formLUQualia";
        $this->render('formLUQualia');
    }

    public function newLUQualia()
    {
        try {
            $model = new fnbr\models\Qualia();
            $model->saveRelation($this->data->qualia);
            $this->renderPrompt('ok', 'Qualia Relation created.',"!$('#formLUQualia_dialog').dialog('close');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formDeleteQualiaRelation()
    {
        $ok = "^structure/qualia/deleteQualiaRelation/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Atenção: A relação qualia será removida! Continua?', $ok);
    }

    public function deleteQualiaRelation()
    {
        try {
            $model = new fnbr\models\Qualia();
            $model->deleteRelation($this->data->id);
            $this->renderPrompt('information', 'OK', "!structure.reloadParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }

    }

    public function formChangeQualiaStructure()
    {
        $this->data->title = 'LU Qualia Structure';
        $this->data->idEntityRelation = $this->data->id;
        $relation = new fnbr\models\EntityRelation($this->data->idEntityRelation);
        $qualia = new fnbr\models\Qualia();
        $this->data->qualiaType = $qualia->getTypeFromRelation($relation);
        $this->data->relationType = 'Relation Type: ' . substr($this->data->qualiaType, 4, 20);
        $LU1 = new fnbr\models\LU();
        $LU1->getByIdEntity($relation->getIdEntity1());
        $this->data->lu1 = 'LU1: ' . $LU1->getFullName();
        $LU2 = new fnbr\models\LU();
        $LU2->getByIdEntity($relation->getIdEntity2());
        $this->data->lu2 = 'LU2: ' . $LU2->getFullName();
        $this->data->close = "!$('#formChangeQualiaStructure_dialog').dialog('close');";
        $this->data->save = "@structure/qualia/changeQualiaStructure|formChangeQualiaStructure";
        $this->render();
    }

    public function changeQualiaStructure()
    {
        try {
            $qualia = new fnbr\models\Qualia($this->data->idQualia);
            $qualia->updateRelation($this->data->idEntityRelation);
            $this->renderPrompt('information', 'OK', "!structure.reloadParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }

    }

    public function dialogChangeQualiaStructure()
    {
        $this->data->title = 'LU Qualia Structure';
        $this->data->idEntityRelation = $this->data->id;
        $relation = new fnbr\models\EntityRelation($this->data->idEntityRelation);
        $qualia = new fnbr\models\Qualia();
        $this->data->qualiaType = $qualia->getTypeFromRelation($relation);
        $this->data->relationType = 'Relation Type: ' . substr($this->data->qualiaType, 4, 20);
        $LU1 = new fnbr\models\LU();
        $LU1->getByIdEntity($relation->getIdEntity1());
        $this->data->lu1 = 'LU1: ' . $LU1->getFullName();
        $LU2 = new fnbr\models\LU();
        $LU2->getByIdEntity($relation->getIdEntity2());
        $this->data->lu2 = 'LU2: ' . $LU2->getFullName();
        $this->data->close = "!$('#dialogChangeQualiaStructure_dialog').dialog('close'); $('#qualiaRelationGrid').datagrid('reload');";
        $this->data->save = "@structure/qualia/changeQualiaStructureDialog|dialogChangeQualiaStructure";
        $this->render();
    }

    public function changeQualiaStructureDialog()
    {
        try {
            $qualia = new fnbr\models\Qualia($this->data->idQualia);
            $qualia->updateRelation($this->data->idEntityRelation);
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }

    }

}

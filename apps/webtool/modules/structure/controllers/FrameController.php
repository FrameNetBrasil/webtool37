<?php

class FrameController extends MController
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
        $this->data->isAnno = Manager::checkAccess('ANNO', A_EXECUTE) ? 'true' : 'false';
        $domain = new fnbr\models\Domain();
        $this->data->domain = $domain->gridDataAsJson($domain->listForSelection(), true);
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
                'text' => 'Frames',
                'children' => $children
            ];
            $json = json_encode([$data]);
        } elseif ($this->data->id{0} == 'f') {
            $json = $structure->listFEsLUs(substr($this->data->id, 1), $this->idLanguage);
        } elseif ($this->data->id{0} == 'l') {
            $json = $structure->listLUSubCorpusConstraints(substr($this->data->id, 1));
        } elseif ($this->data->id{0} == 'e') {
            $json = $structure->listConstraintsFE(substr($this->data->id, 1));
        }
        $this->renderJson($json);
    }

    public function formNewFrame()
    {
        $this->data->title = _M('new Frame');
        $this->render();
    }

    public function newFrame()
    {
        try {
            $model = new fnbr\models\Frame();
            $this->data->frame->entry = 'frm_' . strtolower(str_replace('frm_', '', $this->data->frame->entry));
            $model->setData($this->data->frame);
            $inheritsFromBase = ($this->data->inheritsFromBase == 'on');
            $relations = $model->createNew($this->data->frame, $inheritsFromBase);
            $this->renderResponse('ok', 'Frame created.');
        } catch (\Exception $e) {
            $this->renderResponse('error', $e->getMessage());
        }
    }

    public function formUpdateFrame()
    {
        $model = new fnbr\models\Frame($this->data->id);
        $this->data->object = $model->getData();
        $this->data->title = 'Frame: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function updateFrame()
    {
        try {
            $model = new fnbr\models\Frame($this->data->frame->idFrame);
            $model->updateEntry($this->data->frame->entry);
            $this->renderResponse('ok', 'Frame updated.');
        } catch (\Exception $e) {
            $this->renderResponse('error', $e->getMessage());
        }
    }

    public function formDeleteFrame()
    {
        $ok = ">structure/frame/deleteFrame/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Atenção: O Frame e todos os FrameElements serão removidos! Continua?', $ok);
    }

    public function deleteFrame()
    {
        try {
            $structure = Manager::getAppService('structureframe');
            $structure->deleteFrame($this->data->id);
            $this->renderResponse('information', 'OK', "!structure.reloadFrame();");
        } catch (\Exception $e) {
            $this->renderResponse('error', $e->getMessage());
        }

    }

    public function formFrameSemanticType()
    {
        $model = new fnbr\models\Frame($this->data->id);
        $this->data->object = $model->getData();
        $this->data->idEntity = $model->getIdEntity();
        $this->data->form = "formFrameSemanticType";
        $this->data->close = "!$('#formFrameSemanticType_dialog').dialog('close');";
        $this->data->title = 'Frame: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function formFrameDomain()
    {
        $model = new fnbr\models\Frame($this->data->id);
        $this->data->object = $model->getData();
        $this->data->idFrame = $model->getIdFrame();
        $this->data->form = "formFrameDomain";
        $this->data->close = "!$('#formFrameDomain_dialog').dialog('close');";
        $this->data->title = 'Frame: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function formFrameStatus()
    {
        $model = new fnbr\models\Frame($this->data->id);
        $this->data->object = $model->getData();
        $this->data->idFrame = $model->getIdFrame();
        $this->data->form = "formFrameStatus";
        $this->data->close = "!$('#formFrameStatus_dialog').dialog('close');";
        $this->data->save = "@structure/frame/updateFrameStatus|formFrameStatus";
        $this->data->title = 'Frame: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function formNewFrameRelations()
    {
        $this->data->save = "@structure/frame/newFrameRelatios|formNewFrameRelations";
        $this->data->close = "!$('#formNewFrameRelations_dialog').dialog('close');";
        $this->data->title = _M('new Frame Relations');
        $this->render();
    }

    public function formNewFrameElement()
    {
        $this->data->idFrame = $this->data->id;
        $model = new fnbr\models\Frame($this->data->idFrame);
        $this->data->frame = $model->getEntry() . '  [' . $model->getName() . ']';
        $this->data->save = "@structure/frame/newFrameElement|formNewFrameElement";
        $this->data->close = "!$('#formNewFrameElement_dialog').dialog('close');";
        $this->data->title = _M('new FrameElement');
        $this->render();
    }

    public function newFrameElement()
    {
        try {
            $model = new fnbr\models\FrameElement();
            $this->data->frameelement->entry = 'fe_' . $this->data->frameelement->entry;
            $model->setData($this->data->frameelement);
            $model->save($this->data->frameelement);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->frameelement->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


    public function formUpdateFrameElement()
    {
        $model = new fnbr\models\FrameElement($this->data->id);
        $this->data->object = $model->getData();
        $this->data->save = "@structure/frame/updateFrameElement|formUpdateFrameElement";
        $this->data->close = "!$('#formUpdateFrameElement_dialog').dialog('close');";
        $this->data->title = 'FrameElement: ' . $model->getEntry() . '  [' . $model->getName() . ']';
        $this->render();
    }

    public function updateFrameElement()
    {
        try {
            $model = new fnbr\models\FrameElement($this->data->frameelement->idFrameElement);
            $model->updateEntry($this->data->frameelement->entry);
            $model->setData($this->data->frameelement);
            $model->save($this->data->frameelement);
            $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->frameelement->entry}');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formDeleteFrameElement()
    {
        $ok = "^structure/frame/deleteFrameElement/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Warning: FrameElement will be removed! Continue?', $ok);
    }

    public function deleteFrameElement()
    {
        try {
            $model = new fnbr\models\FrameElement($this->data->id);
            $model->safeDelete();
            $this->renderPrompt('information', 'FrameElement removed.', "!structure.reloadFrame();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formAddConstraintFE()
    {
        $this->data->idFrameElement = $this->data->id;
        $model = new fnbr\models\FrameElement($this->data->idFrameElement);
        $frame = $model->getFrame();
        $this->data->idFrame = $frame->getIdFrame();
        $this->data->fe = 'FE: ' . $frame->getName() . '.' . $model->getName();
        $this->data->save = "@structure/frame/addConstraintFE|formAddConstraintFE";
        $this->data->close = "!$('#formAddConstraintFE_dialog').dialog('close');";
        $this->data->title = _M('Add Constraint');
        $this->render();
    }

    public function addConstraintFE()
    {
        try {
            $structure = Manager::getAppService('structureframe');
            $structure->addConstraintsFE($this->data);
            $this->renderPrompt('information', 'Constraint added.');
        } catch (\Exception $e) {
            $this->renderPrompt('error', "Add Constraint failed.");
        }
    }

    public function formDeleteConstraintFE()
    {
        $structure = Manager::getAppService('structureconstraints');
        list($type, $idEntityFE, $idEntityConstraint) = explode('_', $this->data->id);
        $hasChild = $structure->constraintHasChild($idEntityConstraint);
        if (!$hasChild) {
            $ok = "^structure/frame/deleteConstraintFE/" . $this->data->id;
            $this->renderPrompt('confirmation', 'Warning: Constraint will be deleted! Continue?', $ok);
        } else {
            $this->renderPrompt('error', "This constraint has children; it can't be deleted!");
        }
    }

    public function deleteConstraintFE()
    {
        try {
            list($type, $idEntityFE, $idEntityConstraint) = explode('_', $this->data->id);
            $model = fnbr\models\ConstraintType::create($idEntityConstraint);
            if ($type == 'fr') {
                $model->delete();
            } elseif ($type == 'fe') {
                $model->deleteConstraintMetonymyFEFE($idEntityFE, $idEntityConstraint);
            } elseif ($type == 'lu') {
                $model->deleteConstraintMetonymyFELU($idEntityFE, $idEntityConstraint);
            }
            $this->renderPrompt('information', 'Constraint deleted.', "!structure.reloadFrameParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', "Delete Constraint failed.", "!structure.reloadFrame();");
        }
    }

    public function formNewLU()
    {
        $this->data->idFrame = $this->data->id;
        $this->data->idLanguage = $this->idLanguage;
        $model = new fnbr\models\Frame($this->data->idFrame);
        $this->data->frame = 'Frame:  ' . $model->getEntry() . '  [' . $model->getName() . ']';
        //$model = new fnbr\models\Lemma();
        //$this->data->query = Manager::getAppURL('', 'structure/frame/gridSearchLemmaData');
        $this->data->save = "@structure/frame/newLU|formNewLU";
        $this->data->close = "!$('#formNewLU_dialog').dialog('close');";
        $this->data->title = _M('new LU');
        $this->render();
    }

    public function newLU()
    {
        try {
            if ($this->data->lu->idLanguage == '') {
                throw new \Exception('Language not informed.');
            }
            $lemma = new fnbr\models\Lemma($this->data->lu->idLemma);
            $this->data->lu->name = $lemma->getName();
            $lu = new fnbr\models\LU();
            $this->data->lu->active = '1';
            $lu->save($this->data->lu);
            $frame = fnbr\models\Frame::create($this->data->lu->idFrame);
            fnbr\models\Base::createEntityRelation($lu->getIdEntity(), 'rel_evokes', $frame->getIdEntity());
            $updateLU = "!manager.doAction('@" . Manager::getApp() . "/structure/frame/formUpdateLU/{$lu->getId()}|formNewLU');";
            $this->renderPrompt('information', 'OK, LU created; go to edition.', $updateLU . "$('#formNewLU_dialog').dialog('close');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


    public function formUpdateLU()
    {
        $model = new fnbr\models\LU($this->data->id);
        $this->data->object = $model->getData();
        $this->data->idFrame = $this->data->object->idFrame; // for lookupFE
        $this->data->save = "@structure/frame/updateLU|formUpdateLU";
        //$this->data->close = "!$('#formUpdateLU_dialog').dialog('close');";
        $this->data->close = "!$('#formUpdateLU_dialog').dialog('close');";
        $this->data->title = 'LU:  ' . $model->getFullName();
        $this->render();
    }

    public function updateLU()
    {
        try {
            $model = new fnbr\models\LU($this->data->lu->idLU);
            //$model->setData();
            $model->save($this->data->lu);
            $this->renderPrompt('information', 'OK');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formDeleteLU()
    {
        $ok = "^structure/frame/deleteLU/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Warning: LU will be removed! Continue?', $ok);
    }

    public function deleteLU()
    {
        try {
            $model = new fnbr\models\LU($this->data->id);
            $model->delete();
            $this->renderPrompt('information', 'LU removed.', "!structure.reloadFrameParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


    public function formDeleteSubCorpus()
    {
        $ok = "^structure/frame/deleteSubCorpus/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Warning: SubCorpus will be removed! Continue?', $ok);
    }


    public function deleteSubCorpus()
    {
        try {
            $model = new fnbr\models\SubCorpus($this->data->id);
            if ($model->hasAnnotationSet()) {
                $ok = "^structure/frame/confDeleteSubCorpus/" . $this->data->id;
                $this->renderPrompt('confirmation', 'Warning: Related AnnotationSets will be removed! Continue?', $ok);
            } else {
                $model->delete();
                $this->renderPrompt('information', 'SubCorpus removed.', "!structure.reloadFrameParent();");
            }
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function confDeleteSubCorpus()
    {
        try {
            $model = new fnbr\models\SubCorpus($this->data->id);
            $model->deleteForced();
            $this->renderPrompt('information', 'SubCorpus removed.', "!structure.reloadFrameParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formAddConstraintLU()
    {
        $this->data->idLU = $this->data->id;
        $model = new fnbr\models\LU($this->data->idLU);
        $this->data->lu = 'LU: ' . $model->getName();
        $this->data->save = "@structure/frame/addConstraintLU|formAddConstraintLU";
        $this->data->close = "!$('#formAddConstraintLU_dialog').dialog('close');";
        $this->data->title = _M('LU - Add Constraint');
        $this->render();
    }

    public function addConstraintLU()
    {
        try {
            $structure = Manager::getAppService('structureframe');
            $structure->addConstraintsLU($this->data);
            $this->renderPrompt('information', 'Constraint added.');
        } catch (\Exception $e) {
            $this->renderPrompt('error', "Add Constraint failed.");
        }
    }

    public function formDeleteConstraintLU()
    {
        $structure = Manager::getAppService('structureconstraints');
        $ok = "^structure/frame/deleteConstraintLU/" . $this->data->id;
        $this->renderPrompt('confirmation', 'Warning: Constraint will be deleted! Continue?', $ok);
    }

    public function deleteConstraintLU()
    {
        try {
            list($idLU, $idEntityConstraint) = explode('_', $this->data->id);
            $model = new fnbr\models\ConstraintType();
            $model->deleteConstraintLU($idLU, $idEntityConstraint);
            $this->renderPrompt('information', 'Constraint deleted.', "!structure.reloadFrameParent();");
        } catch (\Exception $e) {
            $this->renderPrompt('error', "Delete Constraint failed.", "!structure.reloadFrame();");
        }
    }

    public function gridSearchLemmaData()
    {
        $model = new fnbr\models\Lemma();
        $lemma = str_replace('+', ' ', $this->data->lemma);
        $criteria = $model->listForSearch($lemma);
        $this->renderJSON($model->gridDataAsJSON($criteria));
    }

    public function formNewLemma()
    {
        if ($this->data->lemma == '') {
            $this->renderPrompt('error', 'No lemma informed.');
        } elseif ($this->data->idLanguage == '') {
            $this->renderPrompt('error', 'No language informed.');
        } else if (strpos($this->data->lemma, '.') === false) {
            $this->renderPrompt('error', 'Wrong format for Lemma.');
        } else {
            $this->data->save = "!saveLemma();";
            $this->data->close = "!$('#formNewLemma_dialog').dialog('close')";
            $dataService = Manager::getAppService('data');
            $this->data->POS = $dataService->getPOS();
            $lemmaPOS = strtoupper(substr($this->data->lemma, strpos($this->data->lemma, '.') + 1));
            $this->data->idPOS = array_search($lemmaPOS, $this->data->POS);
            $this->data->language = $dataService->getLanguage()[$this->data->idLanguage];
            $this->render();
        }
    }

    /*
    public function newLemma()
    {
        try {
            $dataService = Manager::getAppService('data');
            $this->data->POS = $dataService->getPOS();
            $model = new fnbr\models\Lemma();
            $idLU = $model->saveForLU($this->data);
            $url = Manager::getURL("structure/frame/formUpdateLU") . "/" . $idLU;
            $this->renderPrompt('information', 'OK', "!$('#formNewLemma_dialog').dialog('close'); structure.reloadFrame();manager.doGet('{$url}','structureCenterPane');");
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }
    */


    public function formNewLexeme()
    {
        $this->data->lexeme = $this->data->id;
        if ($this->data->lexeme == '') {
            $this->renderPrompt('error', 'No lexeme informed.');
        } elseif ($this->data->lemma->idLanguage == '') {
            $this->renderPrompt('error', 'No language informed.');
        } else {
            $this->data->save = "@structure/frame/newLexeme|formNewLexeme";
            $this->data->close = "!$('#formNewLexeme_dialog').dialog('close')";
            $dataService = Manager::getAppService('data');
            $this->data->language = $dataService->getLanguage()[$this->data->lemma->idLanguage];
            $this->data->pos = $dataService->getPOS();
            $this->render();
        }
    }

    public function newLexeme()
    {
        try {
            if ($this->data->lexeme->idPOS == '') {
                throw new \Exception('No POS informed.');
            } else {
                $model = new fnbr\models\Lexeme();
                $model->save($this->data->lexeme);
                $this->renderPrompt('information', 'OK', "!$('#formNewLexeme_dialog').dialog('close'); $('#gridLexema{$this->data->lexeme->name}').datagrid('reload');");
            }
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }


    public function createTemplate()
    {
        try {
            $this->data->idFrame = $this->data->id;
            $model = new fnbr\models\Template();
            $model->createFromFrame($this->data->idFrame);
            $this->renderPrompt('information', 'Template [' . $model->getName() . '] was created.');
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function newFrameRelations()
    {
        try {
            $model = new fnbr\models\Frame();
            $this->data->frame->entry = 'frm_' . strtolower(str_replace('frm_', '', $this->data->frame->entry));
            $model->setData($this->data->frame);
            $inheritsFromBase = ($this->data->inheritsFromBase == 'on');
            $relations = $model->createNew($this->data->frame, $inheritsFromBase);
            if ((count($relations['direct'])) || (count($relations['inverse']))) {
                $this->renderPrompt('information', 'Frame created.', "structure.editRelations('{$this->data->frame->entry}');");
            } else {
                //$this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->frame->entry}');");
                $this->renderPrompt('information', 'OK', "structure.editEntry('{$this->data->frame->entry}','formNewFrame')");
                $this->renderPrompt('information', 'OK');
            }
        } catch (\Exception $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

    public function formImportWS()
    {
        $this->data->languages = fnbr\models\Base::languages();
        $this->data->idLU = $this->data->id;
        $this->data->tags = array('N' => 'Não', 'S' => 'Sim');
        $this->data->message = _M("Importação do arquivo do WordSketch<br>com nome do documento informado em cada linha.<br>Os documentos já devem estar cadastrados no sistema.");
        $this->data->save = "@structure/frame/importWS|formImportWS";
        $this->data->close = "!$('#formImportWS_dialog').dialog('close')";
        $this->render();
    }

    public function importWS()
    {
        try {
            $files = MUtil::parseFiles('uploadFile');
            $model = new fnbr\models\Corpus($this->data->idCorpus);
            if ($this->data->tags == 'N') {
                $result = $model->uploadSentences($this->data, $files[0]);
            } else {
                $result = $model->uploadSentencesPenn($this->data, $files[0]);
            }
            $this->renderPrompt('information', 'OK');
        } catch (EMException $e) {
            $this->renderPrompt('error', $e->getMessage());
        }
    }

}

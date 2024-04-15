<?php

namespace fnbr\models;

class DynamicObjectMM extends map\DynamicObjectMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(),
            'converters' => array()
        );
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idObjectMM');
        if ($filter->idDocumentMM) {
            $criteria->where("idDocumentMM = {$filter->idDocumentMM}");
        }
        if ($filter->status) {
            $criteria->where("status = {$filter->status}");
        }
        if ($filter->origin) {
            $criteria->where("origin = {$filter->origin}");
        }
        return $criteria;
    }

    public function getObjectsByDocument($idDocument)
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $viewFrameElement = new ViewFrameElement();
        $lu = new LU();
        $criteria = $this->getCriteria();
        $criteria->select("idDynamicObjectMM as idObjectMM, 
            startFrame, endFrame, 
            startTime, endTime, 
            status, origin, 
            idLU, '' as lu, idFrameElement, '' as idFrame, '' as frame, '' as idFE, '' as fe, '' as color"
        );
        $criteria->where("idDocument = {$idDocument}");
        $criteria->orderBy('startTime,endTime');
        $objects = $criteria->asQuery()->getResult();
        $oMM = [];
        foreach ($objects as $object) {
            //mdump($object);
            if ($object['idFrameElement']) {
                $feCriteria = $viewFrameElement->getCriteria();
                $feCriteria->setAssociationAlias('frame.entries', 'frameEntries');
                $feCriteria->select('idFrame, frameEntries.name as frame, idFrameElement as idFE, entries.name as fe, color.rgbBg as color');
                $feCriteria->where("frameEntries.idLanguage = {$idLanguage}");
                $feCriteria->where("entries.idLanguage = {$idLanguage}");
                $feCriteria->where("idFrameElement = {$object['idFrameElement']}");
                $fe = $feCriteria->asQuery()->getResult()[0];
                $object['idFrame'] = $fe['idFrame'];
                $object['frame'] = $fe['frame'];
                $object['idFE'] = $fe['idFE'];
                $object['fe'] = $fe['fe'];
                $object['color'] = $fe['color'];
            }
            if ($object['idLU']) {
                $lu->getById($object['idLU']);
                //$object['lu'] = $lu->getName();
                $object['lu'] = $lu->getFullName();
            }
            $oMM[] = $object;
        }
        $objects = [];
        $objectFrameMM = new DynamicBBoxMM();
        foreach ($oMM as $i => $object) {
            $idObjectMM = $object['idObjectMM'];
            $framesList = $objectFrameMM->listByObjectMM($idObjectMM)->asQuery()->getResult();
            $object['frames'] = $framesList;
            $object['idObject'] = $i + 1;
            $object['idObjectClone'] = $object['idObject'];
            $object['hidden'] = false;
            $objects[] = (object)$object;
        }
        return $objects;
    }

    public function updateObject($data)
    {
        if ($data->idObjectMM != -1) {
            $this->getById($data->idObjectMM);
        }
        $documentMM = new DocumentMM($data->idDocumentMM);
        $objectFrameMM = new DynamicBBoxMM();
        $transaction = $this->beginTransaction();
        try {
            $object = (object)[
                'startTime' => $data->startTime,
                'endTime' => $data->endTime,
                'startFrame' => $data->startFrame,
                'endFrame' => $data->endFrame,
                'idDocument' => $documentMM->getIdDocument(),
                'status' => ($data->idFrameElement > 0) ? 1 : 0,
                'origin' => $data->origin ?: '2',
                'idFrameElement' => $data->idFrameElement,
                'idLU' => $data->idLU,
            ];
            mdump($this->getData());
            $this->save($object);
            Timeline::addTimeline("dynamicobjectmm", $this->getId(), "S");
            $objectFrameMM->putFrames($this->idDynamicObjectMM, $data->frames);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateObjectData($data) {
        if ($data->idObjectMM != -1) {
            $this->getById($data->idObjectMM);
        }
        $transaction = $this->beginTransaction();
        try {
            $object = (object)[
                'startTime' => $data->startTime,
                'endTime' => $data->endTime,
                'startFrame' => $data->startFrame,
                'endFrame' => $data->endFrame,
                'idDocumentMM' => $data->idDocumentMM,
                'status' => ($data->idFrameElement > 0) ? 1 : 0,
                'origin' => $data->origin ?: '2',
                'idFrameElement' => $data->idFrameElement,
                'idLU' => $data->idLU,
            ];
            mdump($this->getData());
            $this->save($object);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
    public function deleteObjects($idToDelete)
    {
        $transaction = $this->beginTransaction();
        try {
            $objectFrameMM = new DynamicBBoxMM();
            $deleteCriteria = $objectFrameMM->getDeleteCriteria();
            $deleteCriteria->where('idDynamicObjectMM', 'IN', $idToDelete);
            $deleteCriteria->delete();
            $deleteCriteria = $this->getDeleteCriteria();
            $deleteCriteria->where('idDynamicObjectMM', 'IN', $idToDelete);
            $deleteCriteria->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function deleteObjectFrame($idToDelete)
    {
        $transaction = $this->beginTransaction();
        try {
            $objectFrameMM = new DynamicBBoxMM();
            $deleteCriteria = $objectFrameMM->getDeleteCriteria();
            $deleteCriteria->where('idDynamicBBoxMM', '=', $idToDelete);
            $deleteCriteria->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /*
    public function putObjects($data) {
        $objectFrameMM = new ObjectFrameMM();
        $idAnnotationSetMM = $data->idAnnotationSetMM;
        $transaction = $this->beginTransaction();
        try {
            $selectCriteria = $this->getCriteria()->select('idObjectMM')->where("idAnnotationSetMM = {$idAnnotationSetMM}");
            $deleteFrameCriteria = $objectFrameMM->getDeleteCriteria();
            $deleteFrameCriteria->where("idObjectMM", "IN" , $selectCriteria);
            $deleteFrameCriteria->delete();
            $deleteCriteria = $this->getDeleteCriteria();
            $deleteCriteria->where("idAnnotationSetMM = {$idAnnotationSetMM}");
            $deleteCriteria->delete();
            foreach($data->objects as $object) {
                $this->setPersistent(false);
                $object->idAnnotationSetMM = $data->idAnnotationSetMM;
                mdump($object);
                if ($object->idFrameElement <= 0) {
                    $object->idFrameElement = '';
                    $object->status = 0;
                } else {
                    $object->status = 1;
                }
                $this->save($object);
                $objectFrameMM->putFrames($this->idObjectMM, $object->frames);
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
    */


    public function save($data = null)
    {
        $transaction = $this->beginTransaction();
        try {
            $this->setData($data);
            parent::save();
            Timeline::addTimeline("dynamicobjectmm", $this->getId(), "S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function getByIdFlickr30k($idFlickr30k)
    {
        $criteria = $this->getCriteria();
        $criteria->where("idFlickr30k", '=', $idFlickr30k);
        $this->retrieveFromCriteria($criteria);
    }


}

<?php
namespace fnbr\models;

class ObjectMM extends map\ObjectMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }

    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idObjectMM');
        if ($filter->idAnnotationSetMM){
            $criteria->where("idAnnotationSetMM = {$filter->idAnnotationSetMM}");
        }
        return $criteria;
    }

    public function listByAnnotationSetMM($idAnnotationSetMM){
        $idLanguage = \Manager::getSession()->idLanguage;
        $viewFrameElement = new ViewFrameElement();
        /*
        $viewFrameElement = new ViewFrameElement();
        $feCriteria = $viewFrameElement->getCriteria();
        $feCriteria->setAssociationAlias('frame.entries', 'frameEntries');
        $feCriteria->select('idFrame, frameEntries.name as frame, idFrameElement as idFE, entries.name as fe, color.rgbBg as color, objectmm.idObjectMM, objectmm.startFrame, objectmm.endFrame, objectmm.name');
        $feCriteria->join("viewframeelement","objectmm","(viewframeelement.idFrameElement = objectmm.idFrameElement)",'left');
        $feCriteria->where("frameEntries.idLanguage = {$idLanguage}");
        $feCriteria->where("entries.idLanguage = {$idLanguage}");
        $feCriteria->where("objectmm.idAnnotationSetMM = {$idAnnotationSetMM}");
        $feCriteria->orderBy('objectmm.startFrame');
        */
        $criteria = $this->getCriteria();
        $criteria->select("idObjectMM, startFrame, endFrame, name, status, idFrameElement, '' as idFrame, '' as frame, '' as idFE, '' as fe, '' as color");
        $criteria->where("idAnnotationSetMM = {$idAnnotationSetMM}");
        $criteria->orderBy('startFrame');
        $objects = $criteria->asQuery()->getResult();
        $oMM = [];
        foreach($objects as $object) {
            mdump($object);
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
            $oMM[] = $object;
        }
        return $oMM;
    }

    public function listFramesByObjectMM($idObjectMM){
        $objectFrameMM = new ObjectFrameMM();
        $criteria = $objectFrameMM->getCriteria()
            ->select('idObjectFrameMM, frameNumber, x, y, width, height, blocked')
            ->where("idObjectMM = {$idObjectMM}")
            ->orderBy('frameNumber');
        return $criteria;
    }

    public function getObjectsAsJSON($idAnnotationSetMM) {
        $objectsList = $this->listByAnnotationSetMM($idAnnotationSetMM);// $this->listByAnnotationSetMM($idAnnotationSetMM)->asQuery()->getResult();
        $objects = [];
        foreach($objectsList as $object) {
            $idObjectMM = $object['idObjectMM'];
            $framesList = $this->listFramesByObjectMM($idObjectMM)->asQuery()->getResult();
            $object['frames'] = $framesList;
            $objects[] = (object)$object;
        }
        return json_encode($objects);
    }


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


    public function save($data)
    {
        $transaction = $this->beginTransaction();
        try {
            $this->setData($data);
            parent::save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    
    
}

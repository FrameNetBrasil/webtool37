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
        $viewFrameElement = new ViewFrameElement();
        $feCriteria = $viewFrameElement->getCriteria()
            ->select('idFrame, frame.entries.name as frame, idFrameElement, entries.name, colors.rgbBg');
        $criteria = $this->getCriteria()
            ->select('viewframeelement.idFrame, viewframeelement.frame, viewframeelement.idFrameElement as idFE, viewframeelement.name as fe, startFrame, endFrame')
            ->orderBy('startFrame');
        $criteria->joinCriteria($feCriteria,"viewframeelement.idFrameElement = idFrameElement");
        $criteria->where("idAnnotationSetMM = {$idAnnotationSetMM}");
        return $criteria;
    }

    public function putObjects($data) {
        $transaction = $this->beginTransaction();
        try {
            $idAnnotationSetMM = $data->idAnnotationSetMM;
            $deleteCriteria = $this->getDeleteCriteria();
            $deleteCriteria->where("idAnnotationSetMM = {$idAnnotationSetMM}");
            $deleteCriteria->delete();
            foreach($data->objects as $object) {
                $this->setPersistent(false);
                $object->idAnnotationSetMM = $data->idAnnotationSetMM;
                $this->save($object);
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

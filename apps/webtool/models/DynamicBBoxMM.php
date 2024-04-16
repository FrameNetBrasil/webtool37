<?php
namespace fnbr\models;

class DynamicBBoxMM extends map\DynamicBBoxMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }


    public function listByObjectMM($idDynamicObjectMM){
        $criteria = $this->getCriteria()
            ->select('idDynamicBBoxMM as idObjectFrameMM, frameNumber, frameTime, x, y, width, height, blocked')
            ->where("idDynamicObjectMM = {$idDynamicObjectMM}")
            ->orderBy('frameNumber');
        return $criteria;
    }

    public function putFrames($idObjectMM, $frames) {
        $transaction = $this->beginTransaction();
        try {
            $deleteCriteria = $this->getDeleteCriteria();
            $deleteCriteria->where("idDynamicObjectMM = {$idObjectMM}");
            $deleteCriteria->delete();
            foreach($frames as $row) {
                $frame = (object)$row;
                $this->setPersistent(false);
                $frame->idDynamicObjectMM = $idObjectMM;
                $this->setData($frame);
                parent::save();
                Timeline::addTimeline("dynamicobjectframemm",$this->getId(),"S");
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }


    public function save($data = null)
    {
        $transaction = $this->beginTransaction();
        try {
            $this->setData($data);
            parent::save();
            Timeline::addTimeline("dynamicobjectframemm",$this->getId(),"S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    
    
}

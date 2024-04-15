<?php
namespace fnbr\models;

class StaticBBoxMM extends map\StaticBBoxMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }

    public function listByObjectMM($idStaticObjectMM){
        $criteria = $this->getCriteria()
            ->select('idStaticBBoxMM, x, y, width, height')
            ->where("idStaticObjectMM = {$idStaticObjectMM}")
            ->orderBy('idStaticBBoxMM');
        return $criteria;
    }

    /*

    public function listByObjectMM($idObjectMM){
        $criteria = $this->getCriteria()
            ->select('idObjectFrameMM, frameNumber, frameTime, x, y, width, height,frameNumber, frameTime, x, y, width, height, blocked')
            ->where("idObjectMM = {$idObjectMM}")
            ->orderBy('frameNumber');
        return $criteria;
    }

    public function putFrames($idObjectMM, $frames) {
        $transaction = $this->beginTransaction();
        try {
            $deleteCriteria = $this->getDeleteCriteria();
            $deleteCriteria->where("idObjectMM = {$idObjectMM}");
            $deleteCriteria->delete();
            foreach($frames as $row) {
                $frame = (object)$row;
                $this->setPersistent(false);
                $frame->idObjectMM = $idObjectMM;
                $this->setData($frame);
                parent::save();
                Timeline::addTimeline("objectframemm",$this->getId(),"S");
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
            Timeline::addTimeline("objectframemm",$this->getId(),"S");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }
*/
    
    
}

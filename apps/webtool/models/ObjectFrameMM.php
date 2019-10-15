<?php
namespace fnbr\models;

class ObjectFrameMM extends map\ObjectFrameMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }

    public function putFrames($idObjectMM, $frames) {
        $transaction = $this->beginTransaction();
        try {
            foreach($frames as $frame) {
                $this->setPersistent(false);
                $frame->idObjectMM = $idObjectMM;
                $this->save($frame);
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

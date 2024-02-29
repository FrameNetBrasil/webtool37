<?php

namespace fnbr\models;

class StaticObjectSentenceMM extends map\StaticObjectSentenceMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
            ),
            'converters' => array()
        );
    }

    public function getAnnotation($idStaticSentenceMM) {
        $staticAnnotationMM = new StaticAnnotationMM();
        $criteria = $staticAnnotationMM->getCriteria();
        $objects = $criteria
            ->select('idStaticAnnotationMM,idLU,idFrameElement,staticobjectsentencemm.name,staticobjectsentencemm.startWord,staticobjectsentencemm.endWord,staticobjectsentencemm.idStaticObjectMM,staticobjectsentencemm.idStaticObjectSentenceMM')
            ->where('staticobjectsentencemm.idStaticSentenceMM', '=', $idStaticSentenceMM)
            ->where('staticobjectsentencemm.idStaticObjectMM <> -1')
            ->orderBy('staticobjectsentencemm.idStaticObjectMM')
            ->asQuery()->getResult();
        $lu = new LU();
        $result = [];
        foreach($objects as $object) {
            if ($object['idLU']) {
                $lu->getById($object['idLU']);
                $object['lu'] = $lu->getName();
            }
            $result[] = (object)$object;
        }
        return $result;
    }

    public function updateAnnotation($data) {
        if ($data->idStaticObjectSentenceMM != -1) {
            try {
                $staticAnnotationMM = new StaticAnnotationMM();
                $staticAnnotationMM->getAnnotationByObjectSentence($data->idStaticObjectSentenceMM);
                $object = (object)[
                    'idFrameElement' => $data->idFrameElement,
                    'idLemma' => $data->idLemma_name,
                ];
                $staticAnnotationMM->setData($object);
                $staticAnnotationMM->save();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }

    public function deleteAnnotation($toDelete) {
        $transaction = $this->beginTransaction();
        try {
            $deleteCriteria = $this->getDeleteCriteria();
            $deleteCriteria->where('idObjectSentenceMM', 'IN', $toDelete);
            $deleteCriteria->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateObject($data) {
        if ($data->idStaticObjectSentenceMM != -1) {
            try {
                $staticAnnotationMM = new StaticAnnotationMM();
                $staticAnnotationMM->getAnnotationByObjectSentence($data->idStaticObjectSentenceMM);
                $object = (object)[
                    'idFrameElement' => $data->idFrameElement,
                    'idLemma' => $data->idLemma,
                ];
                $staticAnnotationMM->setData($object);
                $staticAnnotationMM->save();
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }
    }


}

<?php

namespace fnbr\models;

class UserAnnotation extends map\UserAnnotationMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'idUser' => array('notnull'),
                'idSentenceStart' => array('notnull'),
                'idSentenceEnd' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function listCorpusByUser($idUser) {
        $criteria = $this->getCriteria()
            ->select('sentenceStart.documents.idCorpus')
            ->where("idUser = {$idUser}");
        return array_column($criteria
            ->asQuery()
            ->getResult(), 'idCorpus');
    }

    public function listDocumentByUser($idUser) {
        $criteria = $this->getCriteria()
            ->select('sentenceStart.documents.idDocument')
            ->where("idUser = {$idUser}");
        return array_column($criteria
            ->asQuery()
            ->getResult(), 'idDocument');
    }

    public function listSentenceByUser($idUser, $idDocument) {
        $cmd = <<<HERE
select s.idSentence
from userannotation ua
join sentence s on ((s.idSentence >= ua.idSentenceStart) and (s.idSentence <= ua.idSentenceEnd))
join document_sentence ds on (s.idSentence = ds.idSentence)
where (ds.idDocument = {$idDocument})
and (ua.idUser = {$idUser})

HERE;
        return array_column($this->getDb()->getQueryCommand($cmd)->getResult(), 'idSentence');
    }

}

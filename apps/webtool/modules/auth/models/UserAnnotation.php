<?php

namespace fnbr\auth\models;

use fnbr\models\Base;

class UserAnnotation extends map\UserAnnotationMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'idUser' => array('notnull'),
                'idDocument' => array('notnull'),
                'idSentence' => array('notnull')
            ),
            'converters' => array()
        );
    }

    public function getAnnotationsByUser($idUser): string
    {
        $userAnnotations =  $this->getCriteria()->select('idUserAnnotation')->where('idUser', '=', $idUser)->asQuery()->getResult();
        $userAnnotationStr = json_encode($userAnnotations);
        $userAnnotationStr = preg_replace("/[^0-9,]/", '', $userAnnotationStr);
        if(!empty($userAnnotationStr))
            return $userAnnotationStr;
        return "";
    }
    public function getIdSentencesStart($idUserAnnotations): string
    {
        $idUserAnnotations = explode(',',$idUserAnnotations);

        $idSentencesStart = array();
        foreach($idUserAnnotations as $idUserAnnotation)
        {
            array_push($idSentencesStart, $this->getCriteria()->select('idSentenceStart')->where('idUserAnnotation', '=', $idUserAnnotation)->asQuery()->getResult());
        }

        $idSentenceStart = array();

        foreach ($idSentencesStart as [$item, $value])
        {
            array_push($idSentenceStart, $item);
        }

        $idSentenceStartStr = json_encode($idSentenceStart);

        return $idSentenceStartStr = preg_replace("/[^0-9,]/", '', $idSentenceStartStr);

    }

    public function getIdSentencesEnd($idUserAnnotations): string
    {

        $idUserAnnotations = explode(',', $idUserAnnotations);

        $idSentencesEnd = array();
        foreach ($idUserAnnotations as $idUserAnnotation) {
            array_push($idSentencesEnd, $this->getCriteria()->select('idSentenceEnd')->where('idUserAnnotation', '=', $idUserAnnotation)->asQuery()->getResult());
        }

        $idSentenceEnd = array();
        $idSentenceEndStr = '';

        foreach ($idSentencesEnd as [$item, $value]) {
            array_push($idSentenceEnd, $item);
        }
        $idSentenceEndStr = json_encode($idSentenceEnd);

        return $idSentenceEndStr = preg_replace("/[^0-9,]/", '', $idSentenceEndStr);

    }

    public function getIdDocumentByAnnotation($idUserAnnotations): string
    {
        $idUserAnnotations = explode(',', $idUserAnnotations);

        $idDocuments = array();
        foreach ($idUserAnnotations as $idUserAnnotation) {
            array_push($idDocuments, $this->getCriteria()->select('idDocument')->where('idUserAnnotation', '=', $idUserAnnotation)->asQuery()->getResult());
        }

        $idDocument = array();
        $idDocumentStr = '';
        $idDocumentStrAux = '';

        foreach ($idDocuments as [$item, $value]) {
            array_push($idDocument, $item);
        }

        $idDocumentStr = json_encode($idDocument);

        preg_match_all("/\d+/", $idDocumentStr, $idDocumentStrAux);

        if (!empty($idDocumentStrAux[0])) {
            $numbersOnly = implode(",", $idDocumentStrAux[0]);
            return $numbersOnly;
        }
        return "Document ID not found";
    }

}



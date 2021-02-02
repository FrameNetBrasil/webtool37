<?php
/**
 * 
 *
 * @category   Maestro
 * @package    UFJF
 *  @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version    
 * @since      
 */

namespace fnbr\models;

class SentenceMM extends map\SentenceMMMap {

    public static function config() {
        return array(
            'log' => array(  ),
            'validators' => array(
            ),
            'converters' => array()
        );
    }
    
    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idSentenceMM');
        if ($filter->idSentence){
            $criteria->where("idSentenceMM LIKE '{$filter->idSentenceMM}%'");
        }
        return $criteria;
    }

    public function getDocumentData()
    {
        $document = $this->getSentence()->getParagraph()->getDocument();
        $documentMM = $document->getDocumentMM()[0];
        $data = (object)[
            'idDocumentMM' => $documentMM->getId(),
            'idDocument' => $document->getId(),
            'videoTitle' => $document->getName(),
            //'videoPath' => \Manager::getAppFileURL('', 'files/multimodal/videos/' . $documentMM->getVisualPath(), true),
            'videoPath' => \Manager::getBaseURL() . $documentMM->getVisualPath(),
            //'framesPath' => str_replace('.mp4', '', \Manager::getBaseURL() . '/apps/webtool/files/multimodal/videoframes/' . $documentMM->getVisualPath()),
        ];
        return $data;
    }

    public function getObjectsAsJSON() {
        $idLanguage = \Manager::getSession()->idLanguage;
        $viewFrameElement = new ViewFrameElement();
        $criteria = $this->getCriteria();
        $criteria->select("annotationmm.idAnnotationMM, annotationmm.idObjectMM, 
        annotationmm.objectmm.startFrame, annotationmm.objectmm.endFrame, annotationmm.objectmm.name, annotationmm.objectmm.status, 
        annotationmm.idFrameElement, '' as idFrame, '' as frame, '' as idFE, '' as fe, '' as color");
        $criteria->where("idSentenceMM = {$this->getId()}");
        $criteria->orderBy('annotationmm.objectmm.startFrame');
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
        $objects = [];
        $objectFrameMM = new ObjectFrameMM();
        foreach($oMM as $object) {
            $idObjectMM = $object['idObjectMM'];
            $framesList = $objectFrameMM->listByObjectMM($idObjectMM)->asQuery()->getResult();
            $object['frames'] = $framesList;
            $objects[] = (object)$object;
        }
        return json_encode($objects);
    }




}
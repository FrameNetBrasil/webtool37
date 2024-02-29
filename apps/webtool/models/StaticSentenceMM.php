<?php
/**
 *
 *
 * @category   Maestro
 * @package    UFJF
 * @subpackage fnbr
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

namespace fnbr\models;

class StaticSentenceMM extends map\StaticSentenceMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(),
            'converters' => array()
        );
    }

    public function listByDocument($idDocument)
    {
        $document = new Document();
        $document->getById($idDocument);
        $sentences = $document->listSentence()->chunkResult('idSentence', 'text');
        $idSentences = array_keys($sentences);
        if (!empty($idSentences)) {
            $listIdSentence = implode(',', $idSentences);
        } else {
            $listIdSentence = '0';
        }
        $cmd = <<<HERE

select count(*)
FROM staticsentencemm 

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->getResult();
        $cmd = <<<HERE

select smm.idStaticSentenceMM, imm.name as image, smm.idSentence, count(*) as n, count(amm.idFrameElement) as i
FROM staticsentencemm smm
join imagemm imm on (smm.idImagemm = imm.idImagemm)
join staticobjectsentencemm osmm on (smm.idStaticSentencemm = osmm.idStaticSentencemm)
left join staticannotationmm amm on (osmm.idStaticObjectSentencemm = amm.idStaticObjectSentencemm)
left join staticobjectmm omm on (osmm.idStaticObjectMM = omm.idStaticObjectMM)
left join staticbboxmm bbmm on (omm.idStaticObjectMM = bbmm.idStaticObjectMM)
where (smm.idSentence IN ({$listIdSentence}))
and (smm.idDocument = {$idDocument})
and ((osmm.name is null) or (osmm.name <> 'notvisual'))
and (( omm.idStaticObjectMM is null ) or ((omm.idStaticObjectMM is not null) && (bbmm.idStaticObjectMM is not null)))
group by smm.idStaticSentenceMM, imm.name, smm.idSentence
order by 1

HERE;
        $result = $this->getDb()->getQueryCommand($cmd)->getResult();
        foreach ($result as $i => $row) {
            $result[$i]['text'] = $sentences[$row['idSentence']];
            if ($result[$i]['i'] == 0) {
                $result[$i]['status'] = 'red';
            } else if ($result[$i]['i'] < $result[$i]['n']) {
                $result[$i]['status'] = 'yellow';
            } else {
                $result[$i]['status'] = 'green';
            }
        }
        return $result;
    }

    public function getObjectsForAnnotationImage()
    {
        $idLanguage = \Manager::getSession()->idLanguage;
        $viewFrameElement = new ViewFrameElement();
        $lu = new LU();

        $criteria = $this->getCriteria();
        $criteria->select("staticobjectsentencemm.idStaticObjectSentenceMM, staticobjectsentencemm.idStaticObjectMM, staticobjectsentencemm.name,  
        staticobjectsentencemm.staticannotationmm.idFrameElement, '' as idFrame, '' as frame, '' as idFE, '' as fe, '' as color,staticobjectsentencemm.staticannotationmm.idLU, '' as lu");
        $criteria->where("staticobjectsentencemm.idStaticSentenceMM = {$this->getId()}");
        $criteria->where("staticobjectsentencemm.idStaticObjectMM <> -1");
        $criteria->orderBy('staticobjectsentencemm.staticobjectmm.idFlickr30kEntitiesChain');
        $objects = $criteria->asQuery()->getResult();


        $oMM = [];
        foreach ($objects as $object) {
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
                $object['lu'] = $lu->getName();
            }
            $oMM[] = $object;
        }
        $objects = [];
        $staticBBoxMM = new StaticBBoxMM();
        foreach ($oMM as $object) {
            $idStaticObjectMM = $object['idStaticObjectMM'];
            $framesList = $staticBBoxMM->listByObjectMM($idStaticObjectMM)->asQuery()->getResult();
            $object['frames'] = $framesList;
            $objects[] = (object)$object;
        }
        return $objects;
    }

    /*
    public function listByFilter($filter){
        $criteria = $this->getCriteria()->select('*')->orderBy('idSentenceMM');
        if ($filter->idSentence){
            $criteria->where("idSentenceMM LIKE '{$filter->idSentenceMM}%'");
        }
        if ($filter->idDocumentMM){
            $criteria->where("idDocumentMM = {$filter->idDocumentMM}");
        }
        if ($filter->origin){
            $criteria->where("origin = {$filter->origin}");
        }
        return $criteria;
    }

    public function getDocumentData()
    {
        $sentence = new Sentence();
        $sentence->getById($this->getIdSentence());
        $document = $sentence->getParagraph()->getDocument();
        $documentMM = new DocumentMM();
        $documentMM->getByIdDocument($document->getIdDocument());
        $data = (object)[
            'idDocumentMM' => $documentMM->getId(),
            'idDocument' => $document->getId(),
            'videoTitle' => $documentMM->getTitle(),
            //'videoPath' => \Manager::getAppFileURL('', 'files/multimodal/videos/' . $documentMM->getVisualPath(), true),
            'videoPath' => \Manager::getBaseURL() . str_replace('/var/www/html','',$documentMM->getVideoPath()),
            //'framesPath' => str_replace('.mp4', '', \Manager::getBaseURL() . '/apps/webtool/files/multimodal/videoframes/' . $documentMM->getVisualPath()),
            'videoWidth' => $documentMM->getVideoWidth(),
            'videoHeight' => $documentMM->getVideoHeight(),
        ];
        return $data;
    }


    public function getSentenceObjects() {
        $criteria = $this->getCriteria();
        $criteria->select("objectsentencemm.idObjectSentenceMM, 
        objectsentencemm.name, objectsentencemm.startChar as startWord, objectsentencemm.endChar as endWord");
        $criteria->where("objectsentencemm.idSentenceMM = {$this->getId()}");
        $criteria->orderBy('objectsentencemm.startChar');
        $objects = $criteria->asQuery()->getResult();
        return $objects;
    }

    public function getObjects() {
        $idLanguage = \Manager::getSession()->idLanguage;
        $viewFrameElement = new ViewFrameElement();
        $criteria = $this->getCriteria();
        $criteria->select("objectsentencemm.idObjectSentenceMM,objectsentencemm.objectmm.idObjectMM, objectsentencemm.objectmm.name,  
        objectsentencemm.objectmm.status, objectsentencemm.objectmm.origin, objectsentencemm.objectmm.idFlickr30k,
        objectsentencemm.objectmm.idFrameElement, '' as idFrame, '' as frame, '' as idFE, '' as fe, '' as color");
        $criteria->where("objectsentencemm.idSentenceMM = {$this->getId()}");
        $criteria->orderBy('objectsentencemm.objectmm.idFlickr30k');
        $objects = $criteria->asQuery()->getResult();
        $oMM = [];
        foreach($objects as $object) {
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
        return $objects;
    }

    */

}
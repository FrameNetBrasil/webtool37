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

class AnnotationSetMM extends map\AnnotationSetMMMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(),
            'converters' => array()
        );
    }

    public function getDocumentData()
    {
        $document = $this->getSentenceMM()->getSentence()->getParagraph()->getDocument();
        $documentMM = $document->getDocumentMM()[0];
        $data = (object)[
            'idDocumentMM' => $documentMM->getId(),
            'idDocument' => $document->getId(),
            'videoTitle' => $document->getName(),
            'videoPath' => \Manager::getBaseURL() . '/apps/webtool/files/multimodal/videos/' . $documentMM->getVisualPath()
        ];
        return $data;
    }

    public function save($data)
    {
        $this->setData($data);
        parent::save();
    }

}

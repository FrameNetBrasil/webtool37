<?php
/**
 * @category   Maestro
 * @package    UFJF
 * @subpackage fnbr
 * @copyright  Copyright (c) 2003-2013 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version
 * @since
 */

namespace fnbr\models\map;

class StaticObjectMMMap extends \MBusinessModel
{


    public static function ORMMap()
    {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'staticobjectmm',
            'attributes' => array(
                'idStaticObjectMM' => array('column' => 'idStaticObjectMM', 'key' => 'primary', 'idgenerator' => 'identity', 'type' => 'integer'),
                'scene' => array('column' => 'scene', 'type' => 'integer'),
                'nobndbox' => array('column' => 'nobndbox', 'type' => 'integer'),
                'idFlickr30kEntitiesChain' => array('column' => 'idFlickr30kEntitiesChain', 'type' => 'integer'),
            ),
            'associations' => array(
                'staticbboxmm' => array('toClass' => 'fnbr\models\StaticBBoxMM', 'cardinality' => 'oneToMany', 'keys' => 'idStaticObjectMM:idStaticObjectMM'),
            )
        );
    }

    protected $idStaticObjectMM;
    protected $scene;
    protected $nobdnbox;
    protected $idFlickr30kEntitiesChain;
    /**
     * Associations
     */
    protected $staticbboxmm;


    /**
     * Getters/Setters
     */
    public function getIdStaticObjectMM()
    {
        return $this->idStaticObjectMM;
    }

    public function setIdStaticObjectMM($value)
    {
        $this->idStaticObjectMM = $value;
    }

    public function getScene()
    {
        return $this->scene;
    }

    public function setScene($value)
    {
        $this->scene = $value;
    }

    public function getNoBndBox()
    {
        return $this->nobndbox;
    }

    public function setNoBndBox($value)
    {
        $this->nobndbox = $value;
    }

    public function getEndFrame()
    {
        return $this->endFrame;
    }

    public function getIdFlickr30kEntitiesChain()
    {
        return $this->idFlickr30kEntitiesChain;
    }

    public function setIdFlickr30kEntitiesChain($value)
    {
        $this->idFlickr30kEntitiesChain = $value;
    }

    /**
     *
     * @return Association
     */
    public function getStaticBBoxMM()
    {
        if (is_null($this->staticbboxmm)) {
            $this->retrieveAssociation("staticbboxmm");
        }
        return $this->staticbboxmm;
    }

    /**
     *
     * @param Association $value
     */
    public function setStaticBBoxMM($value)
    {
        $this->staticbboxmm = $value;
    }

}

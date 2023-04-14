<?php
namespace fnbr\models\map;

class OriginMMMap extends \MBusinessModel {

    
    public static function ORMMap() {

        return array(
            'class' => \get_called_class(),
            'database' => \Manager::getConf('fnbr.db'),
            'table' => 'originmm',
            'attributes' => array(
                'idOriginMM' => array('column' => 'idOriginMM','key' => 'primary','idgenerator' => 'identity','type' => 'integer'),
                'origin' => array('column' => 'origin','type' => 'string'),
            ),
            'associations' => array(
            )
        );
    }
    
    /**
     * 
     * @var integer 
     */
    protected $idOrigin;
    /**
     * 
     * @var string 
     */
    protected $origin;

    /**
     * Getters/Setters
     */
    public function getIdOrigin() {
        return $this->idOrigin;
    }

    public function setIdOrigin($value) {
        $this->idOrigin = $value;
    }

    public function getOrigin() {
        return $this->origin;
    }

    public function setOrigin($value) {
        $this->origin = $value;
    }




}
// end - wizard

?>
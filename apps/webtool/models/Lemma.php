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

use Maestro\Types\MFile;

class Lemma extends map\LemmaMap
{

    public static function config()
    {
        return array(
            'log' => array(),
            'validators' => array(
                'name' => array('notnull'),
                'timeline' => array('notnull'),
                'idPOS' => array('notnull'),
            ),
            'converters' => array()
        );
    }

    public function getDescription()
    {
        return $this->getIdLemma();
    }

    public function getIdEntity()
    {
        $idEntity = parent::getIdEntity();
        if ($idEntity == '') {
            if ($this->idLemma != '') {
                $entity = new Entity();
                $alias = 'lemma_' . $this->name . '_' . $this->idLemma;
                $entity->getByAlias($alias);
                if ($entity->getIdEntity()) {
                    throw new \Exception("This Lemma already exists!.");
                } else {
                    $entity->setAlias($alias);
                    $entity->setType('LM');
                    $entity->save();
                    $idEntity = $entity->getId();
                    $this->setIdEntity($idEntity);
                    if ($this->isPersistent()) {
                        parent::save();
                    }
                }
            }
        }
        return $idEntity;
    }

    public function getByName($name)
    {
        $criteria = $this->getCriteria()->select("idLemma, name");
        $criteria->where("name = lower('{$name}')");
        return $criteria;
    }

    public function getByNameIdLanguage($name, $idLanguage)
    {
        $criteria = $this->getCriteria()->select("idLemma, name");
        $criteria->where("(name = lower('{$name}')) and (idLanguage = {$idLanguage})");
        $this->retrieveFromCriteria($criteria);
    }

    public function listByFilter($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('idLemma');
        if ($filter->idLemma) {
            $criteria->where("idLemma LIKE '{$filter->idLemma}%'");
        }
        if ($filter->lemma) {
            $criteria->where("name = '{$filter->lemma}'");
        }
        return $criteria;
    }

    public function listForSearch($lemma = '')
    {
        $criteria = $this->getCriteria()->select("idLemma, concat(name,'  [',language.language,']') as fullname")->orderBy('name');
        $criteria->where("name = '{$lemma}'");
        return $criteria;
    }

    public function listForLookup($lemma = '', $idLanguage = '1')
    {
        $criteria = $this->getCriteria()->select("idLemma, concat(name,'  [',language.language,']') as fullname")->orderBy('name');
        $criteria->where("name LIKE '{$lemma}%'");
        $criteria->where("idLanguage = {$idLanguage}");
        return $criteria;
    }

    public function listForTree($filter)
    {
        $criteria = $this->getCriteria()->select('*')->orderBy('name');
        if ($filter->idLemma) {
            $criteria->where("idLemma = {$filter->idLemma}");
        }
        if ($filter->lexeme) {
            $criteria->where("lexemeentries.lexeme.name LIKE '{$filter->lexeme}%'");
        } else if ($filter->lemma) {
            $criteria->where("name LIKE '{$filter->lemma}%'");
        }
        if ($filter->idLanguage) {
            $criteria->where("idLanguage = {$filter->idLanguage}");
        }
        return $criteria;
    }

    public function listLexemes($idLemma)
    {
        $criteria = $this->getCriteria()->select('lexemeentries.idLexemeEntry,lexemeentries.lexeme.idLexeme,lexemeentries.lexeme.name,lexemeentries.lexeme.pos.POS,lexemeentries.lexemeOrder,lexemeentries.headWord,lexemeentries.breakBefore')->orderBy('name,lexemeentries.lexemeOrder');
        $criteria->where("idLemma = {$idLemma}");
        return $criteria;
    }

    public function setTimeline()
    {
        $timeline = 'lem_' . md5($this->getName() . $this->getIdPOS() . $this->getIdLanguage());
        parent::setTimeLine(Base::newTimeLine($timeline, 'S'));
    }

    public function hasLU()
    {
        return (count($this->getLus()) > 0);
    }

    public function addLexemeEntry($data)
    {
        $lexemeEntry = new LexemeEntry();
        $lexemeEntry->setIdLemma($data->idLemma);
        $lexemeEntry->setPersistent(false);
        $lexemeEntry->setIdLexeme($data->idLexeme);
        $lexemeEntry->setBreakBefore((boolean)$data->breakBefore ? '1' : '0');
        $lexemeEntry->setHeadWord((boolean)$data->headWord ? '1' : '0');
        $lexemeEntry->setLexemeOrder($data->lexemeOrder);
        $lexemeEntry->save();
    }

    public function saveForLU($data)
    {
        try {
            $transaction = $this->beginTransaction();

            $lu = new LU();
            $data->idLemma = $this->getId();
            $data->active = '1';
            $data->name = $this->getName();
            print_r("save lu\n");
            $lu->save($data);
            $frame = Frame::create($data->idFrame);
            print_r("save relation\n");
            Base::createEntityRelation($lu->getIdEntity(), 'rel_evokes', $frame->getIdEntity());

            $transaction->commit();
            return $lu->getId();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveData($data)
    {
        try {
            $transaction = $this->beginTransaction();
            if (($p = strpos($data->name, '.')) !== false) {
                $POS = substr($data->name, $p + 1);
                $pos = new POS();
                $pos->getByPOS($POS);
                $data->idPOS = $pos->getIdPOS();
                $this->setData($data);
                $this->setTimeline();
                mdump('======'.$this->idLemma);
                parent::save();
                $this->getIdEntity();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function save($data)
    {
        try {
            $transaction = $this->beginTransaction();
            $this->setData($data->lemma);
            $this->setTimeline();
            parent::save();
            $this->getIdEntity();
            $lexemeEntry = new LexemeEntry();
            $lexemeEntry->setIdLemma($this->getId());
            $order = 1;
            foreach ($data->lexeme as $lexeme => $array) {
                $lexemeEntry->setPersistent(false);
                $lexemeEntry->setIdLexeme($array['id']);
                $lexemeEntry->setBreakBefore($array['breakBefore'] ?: '0');
                $lexemeEntry->setHeadWord(($lexeme == $data->lemma->headWord) ? '1' : '0');
                $lexemeEntry->setLexemeOrder($order++);
                $lexemeEntry->save();
            }
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $transaction = $this->beginTransaction();
            $idEntity = $this->getIdEntity();
            Base::entityTimelineDelete($idEntity);
            parent::delete();
            $entity = new Entity($idEntity);
            $entity->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Upload de MWE+POS em texto simples (MWE POS)
     *
     * MEW: <wordform>.<POS>.<headWord>.<breakBefore>
     *
     * POS: N, A, NUM, V, ART, PRON, ADV, PREP, SCON, CCON
     * headWord: 0 | 1
     * breakBefore: 0 | 1
     *
     * Parâmetro data informa: idLanguage
     * @param type $data
     * @param type $file
     */
    public function uploadMWE($data, $file)
    {
        $modelLexeme = new Lexeme();
        $modelWordform = new Wordform();
        $fileResult = str_replace(' ', '_', $file->getName()) . '_results.txt';
        $idLanguage = $data->idLanguage;
        $pos = new POS();
        $POS = $pos->listAll()->asQuery()->chunkResult('POS', 'idPOS');
        $transaction = $this->beginTransaction();
        try {
            $lineNum = 0;
            $rows = file($file->getTmpName());
            foreach ($rows as $row) {
                $lineNum++;
                $row = trim($row);
                if (($row == '') || (substr($row, 0, 2) == "//")) {
                    continue;
                }
                mdump($row);
                $msgFail = '';
                $fields = explode(' ', $row);
                $n = count($fields) - 1;
                $idPOS = $POS[$fields[$n]];
                mdump($fields[$n] . ' - ' . $idPOS);
                if ($idPOS != '') {
                    $ok = true;
                    $dataLemma = new \StdClass();
                    for ($i = 0; $i < $n; $i++) {
                        $field = $fields[$i];
                        $mwe = explode('.', $field);
                        mdump($mwe);
                        $idPOSLexeme = $POS[$mwe[1]];
                        if ($idPOSLexeme != '') {
                            $wordform = str_replace("'", "\'", $mwe[0]);
                            $wf = $modelWordform->getCriteria()->select('idLexeme')
                                ->where("(form = '{$wordform}') and (lexeme.idPOS = {$idPOSLexeme}) and (lexeme.idLanguage = {$idLanguage})")->asQuery()->getResult();
                            $idLexeme = $wf[0]['idLexeme'];
                            if ($idLexeme != '') {
                                if ($mwe[2] == '1') {
                                    $dataLemma->lemma->headWord = $mwe[0];
                                }
                                $dataLemma->lexeme[$mwe[0]] = [
                                    'id' => $idLexeme,
                                    'breakBefore' => (int)$mwe[3]
                                ];
                                $dataLemma->lemma->name .= $mwe[0] . ' ';
                            } else {
                                $ok = false;
                                $msgFail .= ' no lexeme for ' . $mwe[0];
                                //$this->setPersistent(false);
                                //$this->setData((object)['name' => $mwe[0], 'idLanguage' => $idLanguage, 'idPOS' => $idPOSLexeme]);
                                //parent::save();
                                //$idLexeme = $this->getId();
                            }
                        } else {
                            $ok = false;
                            $msgFail .= ' no idPOSLexeme for ' . $mwe[1];
                        }
                    }
                    if ($ok) {
                        // create lemma
                        $name = trim($dataLemma->lemma->name) . '.' . strtolower($fields[$n]);
                        $lemma = $this->getCriteria()->select('idLemma')
                            ->where("(name = '{$name}') and (idPOS = {$idPOS}) and (idLanguage = {$idLanguage})")->asQuery()->getResult();
                        if ($lemma[0]['idLemma'] == '') {
                            $dataLemma->lemma->name = $name;
                            $dataLemma->lemma->idPOS = $idPOS;
                            $dataLemma->lemma->idLanguage = $idLanguage;
                            $lemma = new Lemma();
                            $lemma->save($dataLemma);
                            //mdump($dataLemma);
                            $result[] = 'registered: ' . $row . ' as ' . "'{$name}'";
                        } else {
                            $result[] = 'existent: ' . $row . ' as ' . "'{$name}'";
                        }
                    } else {
                        $result[] = 'failed: ' . $row . ' msg: ' . $msgFail;
                    }
                } else {
                    $result[] = 'failed: ' . $row . ' msg: no idPOS for ' . $fields[$n];
                }
            }
            $output = implode("\r\n", $result);
            $mfile = MFile::file("\xEF\xBB\xBF" . $output, false, $fileResult);
            $transaction->commit();
        } catch (\EModelException $e) {
            // rollback da transação em caso de algum erro
            $transaction->rollback();
            throw new EModelException($e->getMessage() . ' LineNum: ' . $lineNum);
        }
        return $mfile;
    }

}

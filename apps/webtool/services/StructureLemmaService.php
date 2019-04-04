<?php

use fnbr\models\Base;

class StructureLemmaService extends MService
{

    public function listLemmas($data, $idLanguage = '')
    {
        $lemma = new fnbr\models\Lemma();
        $name = $data->lemma ?: '-';
        $lexeme = $data->lexeme;
        $filter = (object) ['lemma' => $name, 'lexeme' => $lexeme, 'idLanguage' => $idLanguage];
        $lemmas = $lemma->listForTree($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($lemmas as $row) {
            $node = array();
            $node['id'] = 'l' . $row['idLemma'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa fa-square fa16px entity_lemma';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listLemmaLexeme($idLemma, $idLanguage)
    {
        $result = array();
        $lemma = new fnbr\models\Lemma();
        $lexemes = $lemma->listLexemes($idLemma)->asQuery()->getResult();
        foreach ($lexemes as $lexeme) {
            $node = array();
            $node['id'] = 'x' . $lexeme['idLexemeEntry'];
            $order = ' [' . $lexeme['lexemeOrder'] . ']';
            $head = $lexeme['headWord'] ? ' [hw]' : '';
            $break = $lexeme['breakBeforeheadWord'] ? ' [bb]' : '';
            $node['text'] = $lexeme['name'] . '.' . strtolower($lexeme['POS']) . $order . $head . $break;
            $node['state'] = 'closed';
            $node['iconCls'] = 'icon-blank fa-icon far fa-snowflake';
            $result[] = $node;
        }
        return json_encode($result);
    }

    public function deleteLemma($idLemma)
    {
        mdump('deleteLemma ' . $idLemma);
        $lemma = new fnbr\models\Lemma($idLemma);
        $transaction = $lemma->beginTransaction();
        try {
            if ($lemma->hasLU()) {
                throw new Exception('O lemma tem LU associadas.');
            }
            $lexemeEntries = $lemma->getLexemeentries();
            foreach($lexemeEntries as $lexemeEntry) {
                $lexemeEntry->delete();
            }
            $lemma->delete();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }

    public function addLexemeEntry($data) {
        $lemma = new fnbr\models\Lemma();
        $transaction = $lemma->beginTransaction();
        try {
            $lemma->addLexemeEntry($data->lexeme);
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            throw new \exception($e->getMessage());
        }
    }

}

<?php

class StructureGenreService extends MService
{

    public function listAll($data = '', $idLanguage = '')
    {
        $gt = new fnbr\models\GenreType();
        $rows = $gt->listAll()->asQuery()->getResult();
        $result = array();
        foreach ($rows as $row) {
            $node = array();
            $node['id'] = 't' . $row['idGenreType'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listGenreByGenreType($idGenreType, $idLanguage)
    {
        $result = array();
        $genre = new fnbr\models\Genre();
        $filter = (object)['idGenreType' => $idGenreType];
        $gns = $genre->listByFilter($filter)->asQuery()->getResult();
        foreach ($gns as $gn) {
            $node = array();
            $node['id'] = 'g' . $gn['idGenre'];
            $node['text'] = $gn['name'];
            $node['state'] = 'closed';
            $node['entry'] = $gn['entry'];
            $node['iconCls'] = 'icon-blank fa-icon fas fa-book';
            $result[] = $node;
        }
        return json_encode($result);
    }


}

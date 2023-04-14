<?php

class ReportQualiaService extends MService
{

    public function listQualias($data, $idLanguage = '')
    {
        $frame = new fnbr\models\ViewFrame();
        $filter = (object) ['lu' => $data->lu, 'fe' => $data->fe, 'frame' => $data->frame, 'idDomain' => $data->idDomain, 'idLanguage' => $idLanguage];
        $frames = $frame->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($frames as $row) {
            if (strpos($row['name'], '#') === false) {
                $node = array();
                $node['id'] = 'f' . $row['idFrame'];
                $node['text'] = $row['name'];
                $node['state'] = 'closed';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
        }
        return $result;
    }

}

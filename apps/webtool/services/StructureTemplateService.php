<?php



use Maestro\Services\Exception\ERunTimeException;

class StructureTemplateService extends MService
{

    public function listTemplates($data, $idLanguage = '')
    {
        $template = new fnbr\models\Template();
        $filter = (object) ['fe' => $data->fe, 'template' => $data->template, 'idLanguage' => $idLanguage];
        $templates = $template->listByFilter($filter)->asQuery()->getResult(\FETCH_ASSOC);
        $result = array();
        foreach ($templates as $row) {
            $node = array();
            $node['id'] = 't' . $row['idTemplate'];
            $node['text'] = $row['name'] . ($row['frameName'] ? '  [frm: '.$row['frameName'].']' : '');
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listFEs($idTemplate, $idLanguage)
    {
        $result = array();
        $icon = [
            "cty_core" => "fa fa-circle", 
            "cty_peripheral" => "fa fa-dot-circle-o", 
            "cty_extra-thematic" => "fa fa-circle-o",
            "cty_core-unexpressed" => "fa fa-circle-o"
        ];
        $template = new fnbr\models\Template($idTemplate);
        $fes = $template->listFE()->asQuery()->getResult();
        foreach ($fes as $fe) {
            $node = array();
            $node['id'] = 'e' . $fe['idFrameElement'];
            $style = 'background-color:#' . $fe['rgbBg'] . ';color:#' . $fe['rgbFg'] . ';';            
            $node['text'] = "<span style='{$style}'>" . $fe['name'] . "</span>";
            $node['state'] = 'open';
            $node['entry'] = $fe['entry'];
            $node['iconCls'] = 'icon-blank fa-icon ' . $icon[$fe['coreType']];
            $result[] = $node;
        }
        return json_encode($result);
    }
    
    public function deleteTemplate($idTemplate) {
        // template has relations?
        $template = new fnbr\models\Template($idTemplate);
        $n = $template->listTemplatedFrames()->asQuery()->count();
        $m = $template->getBaseFrame()->asQuery()->count();
        if (($n + $m) > 0) {
            throw new fnbr\models\ERunTimeException("Template has relations. Deletion denied.");
        } else {
            $template->delete();
        }
    }


}

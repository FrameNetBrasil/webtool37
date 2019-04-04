<?php



class StructureCorpusService extends MService
{

    public function listCorpus($data = '', $idLanguage = '')
    {
        $corpus = new fnbr\models\Corpus();
        $filter = (object) ['corpus' => $data->corpus, 'document' => $data->document, 'idLanguage' => $idLanguage];
        $corpora = $corpus->listByFilter($filter)->asQuery()->getResult();
        $result = array();
        foreach ($corpora as $row) {
            $node = array();
            $node['id'] = 'c' . $row['idCorpus'];
            $node['text'] = $row['name'];
            $node['state'] = 'closed';
            $node['entry'] = $row['entry'];
            $result[] = $node;
        }
        return $result;
    }

    public function listDocuments($idCorpus)
    {
        $document = new fnbr\models\Document();
        $docs = $document->listByCorpus($idCorpus)->asQuery()->getResult();
        foreach ($docs as $row) {
            if ($row['idDocument']) {
                $node = array();
                $node['id'] = 'd' . $row['idDocument'];
                $node['text'] = $row['name'] . ' [' . $row['quant'] . ']';
                $node['state'] = 'open';
                $node['entry'] = $row['entry'];
                $result[] = $node;
            }
        }
        return $result;
    }

}

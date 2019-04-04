<?php

namespace UDomino;

use ORM\Service\C5Service;

class UDService
{
    protected $UDominoService;

    public function setUDominoService(C5Service $UDominoService)
    {
        $this->UDominoService = $UDominoService;
    }

    public function loadModeltoDB($modelFile)
    {
        $this->UDominoService->clear();
        $idSentence = 0;
        $handle = @fopen($modelFile, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $line = trim($buffer);
                if ($line != '') {
                    try {
                        if ($line{0} == '#') {
                            $parts = explode(' ', $line);
                            if ($parts[1] == 'sent_id') {
                                $id = $parts[3];
                            }
                            if ($parts[1] == 'text') {
                                $sentence = str_replace('# text = ', '', $line);
                                $sentence = str_replace("'", "\\'", $sentence);
                                ++$idSentence;
                                $this->UDominoService->createSentence($idSentence, $id, $sentence);
                            }
                        } else {
                            $columns = explode("\t", $line);
                            if (strpos($columns[0], '-') === false) {
                                $form = str_replace("\\", "\\\\", $columns[1]);
                                $form = str_replace("'", "\\'", $form);
                                $form = str_replace("\/", "\\/'", $form);
                                $ud = [
                                    'id' => $columns[0],
                                    'form' => $form,
                                    'lemma' => $columns[2],
                                    'upos' => $columns[3],
                                    'xpos' => $columns[4],
                                    'feats' => $columns[5],
                                    'head' => $columns[6],
                                    'deprel' => $columns[7],
                                    'deps' => $columns[8],
                                    'misc' => $columns[9],
                                ];
                                $this->UDominoService->createCONLLU($idSentence, $ud);
                            }
                        }
                    } catch (\Exception $e) {
                        print_r($e->getMessage());
                    }
                }
            }
            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }

    }

    public function getUDStructure($idSentence)
    {
        $lines = $this->UDominoService->getCONLLUFromSentence($idSentence);
        $ud = [];
        foreach ($lines as $line) {
            $ud[$line['id']] = [
                'id' => $line['id'],
                'lemma' => $line['lemma'],
                'pos' => $line['upos'],
                'rel' => $line['deprel'],
                'head' => $line['head'],
            ];
        }
        return $ud;


    }

    public function getUD($sentence)
    {
        $ud = [];
        $result = $this->process($sentence);
        $lines = explode("\n", $result->result);
        foreach ($lines as $line) {
            if (trim($line) != '') {
                if ($line{0} != '#') {
                    $columns = explode("\t", $line);
                    $ud[] = [
                        'id' => $columns[0],
                        'word' => $columns[1],
                        'pos' => $columns[3],
                        'ud' => $columns[7],
                        'deps' => $columns[6]
                    ];
                }
            }
        }
        return $ud;
    }

    public function getWordPOSBySentence($sentence)
    {
        $wordpos = [];
        $ud = $this->getUD($sentence);
        foreach ($ud as $line) {
            if (!strpos($line['id'], '-')) {
                $wordpos[] = [
                    'word' => $line['word'],
                    'pos' => $line['pos']
                ];
            }
        }
        return $wordpos;
    }


}
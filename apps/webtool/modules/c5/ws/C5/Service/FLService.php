<?php

namespace UDomino;

require_once "infra/Analyzer.php";

class FLService
{
    public $service;

    public function __construct() {
        $this->service = new \analyzer("localhost:50005");
    }

    public function process($sentence)
    {

        try {
            $response = $this->service->analyze_text($sentence);
            return $response;
        } catch (Exception $e) {
            $this->dump($e->getMessage());
            return '';
        }
    }

    public function getFL($sentence)
    {
        $fl = [];
        $result = $this->process($sentence);
        print_r($result);
        $lines = explode("\n", $result);
        foreach ($lines as $line) {
            if (trim($line) != '') {
                if ($line{0} != '#') {
                    $pattern = '/(\s+)/i';
                    $replacement = "\t";
                    $line = preg_replace($pattern, $replacement, $line);
                    $columns = explode("\t", $line);
                    $fl[] = [
                        'id' => $columns[0],
                        'word' => $columns[1],
                        'lemma' => strtolower($columns[2]),
                        'pos' => $columns[4]
                    ];
                }
            }
        }
        return $fl;
    }

    public function getWordPOSBySentence($sentence)
    {
        $wordpos = [];
        $fl = $this->getFL($sentence);
        foreach ($fl as $line) {
            $wordpos[] = [
                'word' => $line['word'],
                'pos' => $line['pos']
            ];
        }
        return $wordpos;
    }


}
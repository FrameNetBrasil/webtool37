<?php
namespace UDomino;

use GuzzleHttp\Client;

class UDPipeService
{

    public function process($sentence)
    {

        $client = new Client([
            //'base_uri' => 'http://server2.framenetbr.ufjf.br:8090',
            'base_uri' => 'http://127.0.0.1:8080',
            'timeout'  => 2.0,
        ]);

        try {
            $response = $client->request('get', 'process', [
                'headers' => [
                    //'Accept' => 'application/hal+json',
                    'Accept' => 'application/text',
                ],
                'query' => [
                    'tokenizer' => 'tokenizer',
                    'tagger' => 'tagger',
                    'parser' => 'parser',
                    'output' => 'conllu',
                    'data' => $sentence
                ]
            ]);

            $body = json_decode($response->getBody());
            return $body;
        } catch (Exception $e) {
            $this->dump($e->getMessage());
            return '';
        }
    }

    public function getUD($sentence) {
        $ud = [];
        $result = $this->process($sentence);
        $lines = explode("\n", $result->result);
        foreach($lines as $line) {
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

    public function getWordPOSBySentence($sentence) {
        $wordpos = [];
        $ud = $this->getUD($sentence);
        foreach($ud as $line) {
            if (!strpos($line['id'],'-')) {
                $wordpos[] = [
                    'word' => $line['word'],
                    'pos' => $line['pos']
                ];
            }
        }
        return $wordpos;
    }


}
<?php
/* Copyright [2011, 2013, 2017] da Universidade Federal de Juiz de Fora
 * Este arquivo é parte do programa Framework Maestro.
 * O Framework Maestro é um software livre; você pode redistribuí-lo e/ou
 * modificá-lo dentro dos termos da Licença Pública Geral GNU como publicada
 * pela Fundação do Software Livre (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil,
 * mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer
 * MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a Licença Pública Geral GNU/GPL
 * em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título
 * "LICENCA.txt", junto com este programa, se não, acesse o Portal do Software
 * Público Brasileiro no endereço www.softwarepublico.gov.br ou escreva para a
 * Fundação do Software Livre(FSF) Inc., 51 Franklin St, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

/**
 * MAjax.
 * Tratamento das respostas às requisições Ajax. Define um objeto base (que pode
 * ser composto por outros objetos) e gera a resposta (Texto, XML ou JSON) a partir
 * deste objeto.
 */
class MAjax
{

    /**
     * Versão do XML.
     * @var string
     */
    public $version = '1.0';

    /**
     * Tipo da resposta (TXT, HTML, JSON, OBJECT, E4X, XML).
     * @var string
     */
    public $responseType;

    /**
     * Array com objetos internos.
     * @var array
     */
    public $composites = array();

    private $data;

    /**
     * Define a codificação de caracteres usada na geração da resposta.
     * @var string
     */
    public $inputEncoding;

    public function __construct($inputEncoding = 'UTF-8')
    {
        $this->data = '';
        $this->setEncoding($inputEncoding);
        //$this->setResponseType($_REQUEST['ajaxResponseType'] ?: (Manager::getContext()->getResultFormat() ?: 'TXT'));
        $this->setResponseType(Manager::getContext()->getResultFormat() ?: 'TXT');
    }

    public function initialize($inputEncoding = 'UTF-8')
    {
        $this->setEncoding($inputEncoding);
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    /**
     * Retorna a resposta formatada de acordo com o tipo definido em $responseType.
     * @return mixed
     */
    public function returnData()
    {
        $charset = MAjaxTransformer::findOutputCharset($this->getEncoding());
        switch ($this->responseType) {

            case 'TXT':
            case 'HTML':
                header('Content-type: text/plain; charset=' . $charset);
                $data = MAjaxTransformer::toString($this);
                return $data;
                break;

            case 'JSON':
            case 'OBJECT':
                $data = MAjaxTransformer::toJSON($this);
                $header = 'Content-type: application/json; ';
                if (Manager::getContext()->isFileUpload()) {
                    $newdata = "{\"base64\":\"" . base64_encode($data) . "\"}";
                    $data = "<html><body><textarea>$newdata</textarea></body></html>";
                    $header = 'Content-type: text/html; ';
                }
                header($header . 'charset=' . $charset);
                return $data;
                break;

            case 'E4X':
            case 'XML':
                header('Content-type:  text/xml; charset=' . $charset);
                $data = '<?xml version="1.0" encoding="' . $charset . '"?>'
                    . MAjaxTransformer::toXML($this);
                return $data;
                break;

            default:
                return 'ERROR: invalid response type \'' . $this->responseType . '\'';
        }
    }

    /**
     * Retorna a resposta JSON, quando os dados em $this->base->data já foram definidos neste formato.
     * @return string
     */
    public function returnJSON()
    {
        $data = $this->getData();
        $header = 'Content-type: application/json; ';
        header($header . 'charset=UTF-8');
        return $data;
    }

    public function setEncoding($encoding)
    {
        $this->inputEncoding = strtoupper((string)$encoding);
    }

    public function getEncoding()
    {
        return $this->inputEncoding;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function addNode($nodeName, $id = '')
    {
        $composites = count($this->composites);
        $this->composites[$composites] = new MAjax($this->inputEncoding);
        $this->composites[$composites]->setName($nodename);
        $this->composites[$composites]->setAttribute('id', $id);
    }

    public function getResponseType()
    {
        return $this->responseType;
    }

    public function setResponseType($value)
    {
        if (isset($value)) {
            $this->responseType = htmlentities(strip_tags(strtoupper((string)$value)));
        }
    }

    public function isEmpty()
    {
        return (count($this->composites) == 0) && ($this->data == '');
    }

}


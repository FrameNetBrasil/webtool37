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

class MMailer extends MService
{
    /**
     *
     * @param stdClass $params
     * @return \PHPMailer
     */
    public static function getMailer($params = null)
    {
        $mailer = self::getDecoratedMailer();

        $mailer->IsSMTP(); // telling the class to use SMTP
        $mailer->Host = \Manager::getConf('mailer.smtpServer'); // SMTP server
        $mailer->From = \Manager::getConf('mailer.smtpFrom');
        $mailer->FromName = \Manager::getConf('mailer.smtpFromName');
        $mailer->CharSet = 'utf-8';
        $mailer->WordWrap = 100;

        if ($params !== null) {
            // Preenche os parametros do mailer. Ver atributos publicos da classe PHPMailer
            self::copyPublicAttributes($params, $mailer);
            $mailer->isHTML($params->isHTML);

            self::__AddAddress($params->to, $mailer);
            self::__AddCC($params->cc, $mailer);
            self::__AddBCC($params->bcc, $mailer);
            self::__AddReplyTo($params->ReplyTo, $mailer);
        }

        return $mailer;
    }

    // Preenche os destinatários
    protected static function __AddAddress($to, $mailer)
    {
        foreach (self::emailListToArray($to) as $address) {
            $mailer->AddAddress($address);
        }
    }

    // Preenche os destinatários com copia
    protected static function __AddCC($cc, $mailer)
    {
        foreach (self::emailListToArray($cc) as $address) {
            $mailer->AddCC($address);
        }
    }

    // Preenche os destinatários com copia oculta
    protected static function __AddBCC($bcc, $mailer)
    {
        foreach (self::emailListToArray($bcc) as $address) {
            $mailer->AddBCC($address);
        }
    }

    // Preenche os enderecos de resposta
    protected static function __AddReplyTo($ReplyTo, $mailer)
    {
        foreach (self::emailListToArray($ReplyTo) as $address) {
            $mailer->AddReplyTo($address);
        }
    }

    protected static function copyPublicAttributes($from, $to)
    {
        $publicFromAttributes = get_object_vars($from);
        $publicToAttributes = $to->getAttributesFromInner();

        $commonPublicAttributes = array_intersect_key($publicFromAttributes, $publicToAttributes);

        foreach ($commonPublicAttributes as $attributeName => $attributeValue) {
            $to->$attributeName = $attributeValue;
        }
    }


    protected static function hasReceivers($params)
    {
        return !(empty($params->to) && empty($params->cc) && empty($params->bcc));
    }


    protected static function emailListToArray($emailList)
    {
        return (is_array($emailList)) ? $emailList : explode(',', $emailList);
    }


    public static function send($params = null)
    {
        $mailer = self::getMailer($params);
        return $mailer->send();
    }

    /**
     * Adiciona um decorador à classe PHPMailer de maneira para checar, antes de cada envio,
     * se existe um e-mail padrão para envio (modo desenvolvimento).
     */
    private static function getDecoratedMailer()
    {
        $dec = new MSimpleDecorator(new \PHPMailer());

        $callback = function ($mailer) {
            if (\Manager::DEV() && !empty(\Manager::getConf('mailer.smtpTo'))) {
                $mailer->ClearAddresses();
                $mailer->ClearCCs();
                $mailer->ClearBCCs();
                $mailer->AddAddress(\Manager::getConf('mailer.smtpTo'));
            }
        };

        $dec->addPreCommand($callback, 'send');

        return $dec;
    }

}
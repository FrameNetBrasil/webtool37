<?php

use PHPMailer\PHPMailer\PHPMailer;

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

        $mailer->SMTPDebug = 2;
        $mailer->IsSMTP(); // telling the class to use SMTP
        $mailer->Host = \Manager::getConf('mailer.smtpServer'); // SMTP server
        $mailer->From = \Manager::getConf('mailer.smtpFrom');
        $mailer->FromName = \Manager::getConf('mailer.smtpFromName');
        $mailer->CharSet = 'utf-8';
        $mailer->WordWrap = 100;
var_dump($mailer->Host);
        $mailer->SMTPAuth   = \Manager::getConf('mailer.smtpAuth');                                   // Enable SMTP authentication
        $mailer->Username   = \Manager::getConf('mailer.smtpFrom');                     // SMTP username
        $mailer->Password   = \Manager::getConf('mailer.smtpPass');                               // SMTP password
        //$mailer->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
        $mailer->Port       = 587;



        if ($params !== null) {
            // Preenche os parametros do mailer. Ver atributos publicos da classe PHPMailer
            self::copyPublicAttributes($params, $mailer);
            $mailer->isHTML($params->isHTML);

            self::__AddAddress($params->To, $mailer);
            self::__AddCC($params->CC, $mailer);
            self::__AddBCC($params->BCC, $mailer);
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
        $dec = new MSimpleDecorator(new PHPMailer());

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
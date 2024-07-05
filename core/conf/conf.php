<?php
return [
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Framework Maestro3',
    // preloading 'log' component
    'preload' => [
        'log'
    ],
    'theme' => [
        'name' => 'default',
        'template' => 'index'
    ],
    'options' => [
		'http' => 'https',
        'startup' => 'webtool',
        'dbsession' => false,
        'debug' => true,
        'charset' => 'UTF-8',
        'timezone' => "America/Sao_Paulo",
        'separatorDate' => '/',
        'formatDate' => 'd/m/Y',
        'formatTimestamp' => 'd/m/Y H:i:s',
        'csv' => ';',
        'mode' => 'DEV',
        'painter' => 'EasyUI',
        'dispatch' => 'index.php',
        'varPath' => sys_get_temp_dir(),
        'language' => 'pt_br',
        'locale' => array("pt_BR.utf8", "ptb") // linux: check installed locales - "locale -a"
    ],
    'mad' => [ // overrided by specific apps
        'module' => "common",
        'access' => "acesso",
        'group' => "grupo",
        'log' => "log",
        'session' => "sessao",
        'transaction' => "transacao",
        'user' => "usuario"
    ],
    'login' => [
        'module' => "",
        'class' => "MAuthDbMd5",
        'check' => false,
        'shared' => true,
        'auto' => false
    ],
    'session' => [
        'handler' => "file",
        'timeout' => "60",
        'exception' => false,
        'check' => true
    ],
    'logs' => [
        //'path' => sys_get_temp_dir() . '/log',
        'path' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '../var/log',
        'level' => 0,
        'handler' => "file",
        'peer' => $_ENV['TRACE_HOST'],
        //'strict' => '',
        'port' => $_ENV['TRACE_PORT'],
        'errorCodes' => [
            E_ERROR,
            E_WARNING,
            E_PARSE,
            E_RECOVERABLE_ERROR,
            E_USER_ERROR,
            E_COMPILE_ERROR,
            E_CORE_ERROR
        ],
    ],
    'cache' => [
        'type' => "php", // php, java, apc, memcache
        'memcache' => [
            'host' => "127.0.0.1",
            'port' => "11211",
            'default.ttl' => 0
        ],
        'apc' => [
            'default.ttl' => 0
        ]
    ],
    'mailer' => [
        'smtpServer' => 'localhost',
        'smtpFrom' => 'maestro@maestro.org',
        'smtpFromName' => 'Framework Maestro',
        'smtpAuthUser' => '',
        'smtpAuthPass' => '',
    ],
    'extensions' => [
    ],

];

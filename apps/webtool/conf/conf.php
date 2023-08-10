<?php

return [
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'FrameNet Brasil Webtool',
    'import' => [
        'modules' => [
            'auth'
        ]
    ],
    'preload' => [
        'log'
    ],
    'options' => [
        'fetchStyle' => \FETCH_ASSOC,
        'language' => 'en',
        'templateEngine' => 'latte',
        'defaultPassword' => 'default',
        'pageTitle' => 'Webtool 3.7',
        'mainTitle' => 'FrameNet Brasil Webtool 3.7 [github]',
        'baseURL' => 'http://localhost/webtool',
        'http' => 'http',
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
        'locale' => array("pt_BR.utf8", "ptb")
    ],
    'ui' => [
        'actions' => 'actions.php',
    ],
    'login' => [
        'handler' => 'maestro',
        'AUTH0_CLIENT_ID' => '',
        'AUTH0_DOMAIN' => 'framenetbr.auth0.com',
        'AUTH0_CLIENT_SECRET' => '',
        'AUTH0_CALLBACK_URL' => 'http://x/auth0/callback.php',
        'logout' => 'https://framenetbr.auth0.com/v2/logout?returnTo=',
    ],
    'session' => [
        'handler' => "file",
        'timeout' => "60",
        'exception' => false,
        'check' => true
    ],
    'logs' => [
        'path' => sys_get_temp_dir() . '/log',
        'level' => 2,
        'handler' => "socket",
        'peer' => 'localhost',
        'port' => $_ENV["TRACE_PORT"],
    ],
    'theme' => [
        'name' => 'webtool',
        'template' => 'index'
    ],
    'mailer' => [
        'smtpServer' => 'smtp.gmail.com',
        'smtpFrom' => 'x@gmail.com',
        'smtpFromName' => 'Project Name',
        'smtpTo' => '',
        'smtpAuth' => true,
        'smtpPass' => '',
        'smtpPort' => 587,
    ],
    'mad' => [
        'namespace' => "fnbr\\auth",
        'module' => "auth",
        'access' => "Access",
        'group' => "Group",
        'log' => "Log",
        'session' => "Session",
        'transaction' => "Transaction",
        'user' => "User"
    ],
    'filters' => [
        'db' => 'fnbr',
        'session' => 'session',
    ],
    'fnbr' => [
        'db' => 'webtool',
        'color' => [
            'rel_causative_of' => '#FFD700',
            'rel_inchoative_of' => '#DAA520',
            'rel_inheritance' => '#FF0000',
            'rel_perspective_on' => '#FFB6C1',
            'rel_precedes' => '#000000',
            'rel_see_also' => '#000000',
            'rel_subframe' => '#0000FF',
            'rel_using' => '#008000',
            'rel_coreset' => '#0000FF',
            'rel_excludes' => '#FF0000',
            'rel_requires' => '#008000',
            'rel_evokes' => '#0000FF',
            'rel_inheritance_cxn' => '#FF0000',
            'rel_elementof' => '#000000',
            'rel_hassemtype' => '#000000',
            'rel_formal_qualia' => '#092834',
            'rel_constitutive_qualia' => '#66B032',
            'rel_agentive_qualia' => '#347B98',
            'rel_telic_qualia' => '#B2D732',
            'rel_qualia_formal' => '#092834',
            'rel_qualia_constitutive' => '#66B032',
            'rel_qualia_agentive' => '#347B98',
            'rel_qualia_telic' => '#B2D732',
            'rel_constraint_frame' => '#F5DEB3',
            'rel_standsfor' => '#808000',
            'rel_inhibits' => '#FFFF00',
            'con_cxn' => '#000000',
            'con_element' => '#000000',
            'con_lu' => '#000000',
            'con_lexeme' => '#000000',
            'con_lemma' => '#000000',
            'con_udrelation' => '#000000',
            'con_udfeature' => '#000000',
        ],
        'beginnerLayers' => [
            '1'
        ]
    ],
    'db' => [
        'webtool' => [
            'driver' => 'pdo_mysql',
            'host' => $_ENV['DB_HOST'] . ':' . $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASS'],
            'formatDate' => '%e/%m/%Y',
            'formatDateWhere' => '%Y/%m/%e',
            'formatTime' => '%T',
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_bin',
            'sequence' => [
                'table' => 'Sequence',
                'name' => 'Name',
                'value' => 'Value'
            ],
            'configurationClass' => 'Doctrine\DBAL\Configuration',
        ]
    ],
];

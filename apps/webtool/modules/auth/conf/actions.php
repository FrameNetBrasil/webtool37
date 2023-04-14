<?php
/**
 * @category   Maestro
 * @package    UFJF
 * @subpackage 
 * @copyright  Copyright (c) 2003-2012 UFJF (http://www.ufjf.br)
 * @license    http://siga.ufjf.br/license
 * @version    
 * @since      
 */

// wizard - code section created by Wizard Module - 03/02/2015 15:25:07

return array(
    'auth' => array('auth', 'auth/main/main', 'authIconForm', '', A_ACCESS, array(
        'Person' => array('Person', 'auth/Person/main', 'authIconForm', '', A_ACCESS, array()),
        'User' => array('User', 'auth/User/main', 'authIconForm', '', A_ACCESS, array()),
        'Group' => array('Group', 'auth/Group/main', 'authIconForm', '', A_ACCESS, array()),
        'Transaction' => array('Transaction', 'auth/Transaction/main', 'authIconForm', '', A_ACCESS, array()),
        'Access' => array('Access', 'auth/Access/main', 'authIconForm', '', A_ACCESS, array()),
        'Log' => array('Log', 'auth/Log/main', 'authIconForm', '', A_ACCESS, array()),
    ))

);

// end - wizard

?>
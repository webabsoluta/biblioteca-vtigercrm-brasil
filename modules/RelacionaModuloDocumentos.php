<?php

/* 
 * n order to associate documents to a module we need to also include the function call "get_attachments", this can also be used for any module associations you want to create with a custom list generation function.
 */

require_once 'vtlib/Vtiger/Module.php';
$module=Vtiger_Module::getInstance('YourModule');
$module->setRelatedList(Vtiger_Module::getInstance('Documents'), 'Documents',Array('ADD','SELECT'), 'get_attachments');
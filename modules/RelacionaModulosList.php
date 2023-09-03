<?php

/* 
 * This is a simple relation with ADD/SELECT for adding one modules records to another.
 * The example given below describes the way of creating a relation between a Payslip and Accounts module:
 */

include_once('vtlib/Vtiger/Module.php');
$moduleInstance = Vtiger_Module::getInstance('Leads');
$accountsModule = Vtiger_Module::getInstance('new_module');
$relationLabel  = 'New_modules';
$moduleInstance->setRelatedList(
      $accountsModule, $relationLabel, Array('ADD') //you can do select also Array('ADD','SELECT')
);

echo "Feito";


// Remover o Relacionamento
//$moduleInstance->unsetRelatedList($targetModuleInstance);

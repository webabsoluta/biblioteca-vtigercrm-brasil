<?php

/* 
 * Criar módulo pelo VTlib
 */

// Include the required files:
require_once('vtlib/Vtiger/Menu.php');
require_once('vtlib/Vtiger/Module.php');
require_once('vtlib/Vtiger/Block.php');
require_once('vtlib/Vtiger/Field.php');

// Initiate a new module class:
$moduleInstance = new Vtiger_Module();
$moduleInstance->name = 'MODULENAME';
$moduleInstance->save();
$moduleInstance->initTables();

// Assign the module to a menu position:

$menuInstance = Vtiger_Menu::getInstance('Tools');
$menuInstance->addModule($moduleInstance);

// Create your modules field blocks:

$blockInstance = new Vtiger_Block();
$blockInstance->label = 'LBL_MODULENAME_INFORMATION';
$moduleInstance->addBlock($blockInstance);

// Create your modules fields and assign them to blocks:

// this is an example of a name type field (text)
$fieldInstance1 = new Vtiger_Field();
$fieldInstance1->name = 'FIELDNAME';
$fieldInstance1->table = 'vtiger_MODULENAME';
$fieldInstance1->column = 'FIELDNAME';
$fieldInstance1->columntype = 'VARCHAR(255)';
$fieldInstance1->uitype = 2;
$fieldInstance1->typeofdata = 'V~M';
$fieldInstance1->masseditable = 0;
$blockInstance->addField($fieldInstance1);

// this is an example of a custom order number field
$fieldInstance2 = new Vtiger_Field();
$fieldInstance2->name = 'fieldname';
$fieldInstance2->label = 'LBL_ENTRY_NR';
$fieldInstance2->table = 'vtiger_MODULENAME';
$fieldInstance2->column = 'entrynr';
$fieldInstance2->columntype = 'VARCHAR(100)';
$fieldInstance2->uitype = 4;
$fieldInstance2->typeofdata = 'V~O';
$blockInstance->addField($fieldInstance2);

// Add the required fields for vtiger_crmentity table

// Three fields that should be in every module - createdtime, assigned_to and modifiedtime
$fieldInstance3 = new Vtiger_Field();
$fieldInstance3->name = 'assigned_user_id';
$fieldInstance3->label = 'Assigned To';
$fieldInstance3->table = 'vtiger_crmentity';
$fieldInstance3->column = 'smownerid';
$fieldInstance3->uitype = 53;
$fieldInstance3->typeofdata = 'V~M';
$blockInstance->addField($fieldInstance3);

$fieldInstance4 = new Vtiger_Field();
$fieldInstance4->name = 'CreatedTime';
$fieldInstance4->label= 'Created Time';
$fieldInstance4->table = 'vtiger_crmentity';
$fieldInstance4->column = 'createdtime';
$fieldInstance4->uitype = 70;
$fieldInstance4->typeofdata = 'T~O';
$fieldInstance4->displaytype= 2;
$blockInstance->addField($fieldInstance4);

$fieldInstance5 = new Vtiger_Field();
$fieldInstance5->name = 'ModifiedTime';
$fieldInstance5->label= 'Modified Time';
$fieldInstance5->table = 'vtiger_crmentity';
$fieldInstance5->column = 'modifiedtime';
$fieldInstance5->uitype = 70;
$fieldInstance5->typeofdata = 'T~O';
$fieldInstance5->displaytype= 2;
$blockInstance->addField($fieldInstance5);

// Set the auto numbering and identifier fields
$entity = new CRMEntity();
$entity->setModuleSeqNumber("configure",$moduleInstance->name,"CUSTOMNUMBERINGPREFIX",1);

// Set the identifier field
$moduleInstance->setEntityIdentifier($fieldInstance1);

// Create default listview filter

$filter1 = new Vtiger_Filter();
$filter1->name = 'All';
$filter1->isdefault = true;
$moduleInstance->addFilter($filter1);

// Add fields to the filter created
$filter1->addField($fieldInstance5);
$filter1->addField($fieldInstance4);
$filter1->addField($fieldInstance3);
$filter1->addField($fieldInstance2);
$filter1->addField($fieldInstance1);

// Initiate the last settings for the module
$moduleInstance->setDefaultSharing('Private');
$moduleInstance->disableTools('Export','Import','Merge');

$moduleInstance->initWebservice();
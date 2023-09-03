<?php

/* 
 * Campo de Lista de Opções com estados brasileiros
 */

// Estado de Faturamento
$MODULENAME = 'Accounts';
$moduleInstance = Vtiger_Module::getInstance($MODULENAME);
$block1=Vtiger_Block::getInstance('LBL_ADDRESS_INFORMATION',$moduleInstance);
$field5 = new Vtiger_Field();
$field5->name = 'bill_state';
$field5->label = 'Estado Faturamento';
$field5->table = 'vtiger_accountbillads';
$field5->column = 'bill_state';
$field5->columntype = 'VARCHAR(4)';
$field5->uitype = 16;
$field5->typeofdata = 'V~O';
$field5->sequence = 7;
$field5->setPicklistValues( Array ('AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PR','PB','PA','PE','PI','RJ','RN','RS','RO','RR','SC','SE','SP','TO') );
$block1->addField($field5);
$moduleInstance->setEntityIdentifier($field5);
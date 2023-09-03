<?php

/* 
 * Adicionar Link para Ação na Visão de Edição
 */

include_once('vtlib/Vtiger/Module.php');
$moduleInstance = Vtiger_Module::getInstance('Accounts');
$moduleInstance->addLink('DETAILVIEWBASIC', 'Emitir Boleto', 'emitir_boleto.php');


/* 
 * Adicionar Link para Ação na Lista de Registros
 */



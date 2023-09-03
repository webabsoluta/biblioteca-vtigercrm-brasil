<?php

/* 
 * Apaga Campo do VTiger
 */

$field = Vtiger_Field::getInstance ( 28 ); //Field Id. - Encontra na tabela vtiger_fields
if ($field) {
$field->delete ();
}
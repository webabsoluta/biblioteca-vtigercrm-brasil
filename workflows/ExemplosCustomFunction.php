<?php

/*
  Workflows Invoke Custom Function example Method Function file (myExample.inc)
  Put this file in the modules/Workflow/ directory. If the directory does
   not exist, then just create it
  See the myExample_REGISTER.php file for more information.
*/


/* function Contacts_sendCustomerPortalLoginDetails($entityData){
        $adb = PearDatabase::getInstance();
        $moduleName = $entityData->getModuleName();
        $wsId = $entityData->getId();
        $parts = explode('x', $wsId);
        $entityId = $parts[1];
//        $entityDelta = new VTEntityDelta();
//        $portalChanged = $entityDelta->hasChanged($moduleName, $entityId, 'portal');
//        $email = $entityData->get('email');

//        if ($entityData->get('portal') == 'on' || $entityData->get('portal') == '1') {
                $sql = "SELECT id, user_name, user_password, isactive FROM vtiger_portalinfo WHERE id=?";
                $result = $adb->pquery($sql, array($entityId));
  
//                $insert = false;
//                if($adb->num_rows($result) == 0){
//                        $insert = true;
//                }else{
                        $dbusername = $adb->query_result($result,0,'user_name');
                        $isactive = $adb->query_result($result,0,'isactive');
/*                        if($email == $dbusername && $isactive == 1 && !$entityData->isNew()){
                                $update = false;
                        } else if($entityData->get('portal') == 'on' ||  $entityData->get('portal') == '1'){
                                $sql = "UPDATE vtiger_portalinfo SET user_name=?, isactive=? WHERE id=?";
                                $adb->pquery($sql, array($email, 1, $entityId));
                                $update = true;
                        } else {
                                $sql = "UPDATE vtiger_portalinfo SET user_name=?, isactive=? WHERE id=?";
                                $adb->pquery($sql, array($email, 0, $entityId));
                                $update = false;
                        } */
//                }
/*               $password = makeRandomPassword();
                $enc_password = Vtiger_Functions::generateEncryptedPassword($password);
                if ($insert == true) {
                        $sql = "INSERT INTO vtiger_portalinfo(id,user_name,user_password,cryptmode,type,isactive) VALUES(?,?,?,?,?,?)";
                        $params = array($entityId, $email, $enc_password, 'CRYPT', 'C', 1);
                        $adb->pquery($sql, $params);
                }
                if ($update == true && $portalChanged == true) {
                        $sql = "UPDATE vtiger_portalinfo SET user_password=?, cryptmode=? WHERE id=?";
                        $params = array($enc_password, 'CRYPT', $entityId);
                        $adb->pquery($sql, $params);
                }
                if (($insert == true || ($update = true && $portalChanged == true)) && $entityData->get('emailoptout') == 0) {
                        global $current_user,$HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;
                        require_once("modules/Emails/mail.php");
                        $emailData = Contacts::getPortalEmailContents($entityData,$password,'LoginDetails');
                        $subject = $emailData['subject'];
            if(empty($subject)) {
                $subject = 'Customer Portal Login Details';
            }
                        $contents = $emailData['body'];
            $contents= decode_html(getMergedDescription($contents, $entityId, 'Contacts'));
            if(empty($contents)) {
                                require_once 'config.inc.php';
                                global $PORTAL_URL;
                $contents = 'LoginDetails';
                $contents .= "<br><br> User ID : ".$entityData->get('email');
                $contents .= "<br> Password: ".$password;
                                $portalURL = vtranslate('Please ',$moduleName).'<a href="'.$PORTAL_URL.'" style="font-family:Arial, Helvetica, sans-serif;font-size:13px;">'.  vtranslate('click here', $moduleName).'</a>';
                                $contents .= "<br>".$portalURL;
            }
            $subject=  decode_html(getMergedDescription($subject, $entityId,'Contacts'));
                        send_mail('Contacts', $entityData->get('email'), $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject, $contents,'','','','','',true);
                } 
//        } else {
                $sql = "UPDATE vtiger_portalinfo SET user_name=?,isactive=0 WHERE id=?";
                $adb->pquery($sql, array($email, $entityId));
//        }
}
*/
                
function editaSaldo($entityData){

    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1]; 

    $sql = "SELECT valor_pagamento AS valor FROM vtiger_pagamentos WHERE pagamentosid=?";
    $result = $adb->pquery($sql, array($entityId));
    
    $valor = $adb->query_result($result,0,'valor');
    
    $sql = "UPDATE vtiger_pagamentos SET saldo_pagamento=? WHERE pagamentosid=?";
    $adb->pquery($sql, array($valor, $entityId));
    
}

function repetePagamentos($entityData){

     global $current_user, $adb, $log;
    
// Recupera o ID    
    $adb = PearDatabase::getInstance();
    $moduleName = $entityData->getModuleName();
    $wsId = $entityData->getId();
    $parts = explode('x', $wsId);
    $entityId = $parts[1];

// Consulta   
    $sql = "SELECT pagamentosid AS id, vencimento AS vencimento, categoria_pagamento AS categoria, recorrencia AS recorrencia, subcategoria_pagamento AS subcategoria, frequencia AS frequencia, valor_pagamento AS valor, centro_custo AS centrocusto, conta AS conta, relacionado_pedido AS relacionado, emissor AS emissor, tipo_pagamento AS tipo, ref_pagamento AS referencia, numero_repeticoes AS no_repeticoes FROM vtiger_pagamentos WHERE vtiger_pagamentos.pagamentosid=?";
    $result = $adb->pquery($sql, array($entityId));

    $event_date = $adb->query_result($result,0,'vencimento');
    $event_repetition_type = $adb->query_result($result,0,'frequencia');
    $event_no_repetition = $adb->query_result($result,0,'no_repeticoes');
    $event_infinito = $adb->query_result($result,0,'recorrencia');
    $infinito_limite = " +5 years";
    $referencia = $adb->query_result($result,0,'referencia');
    $categoria = $adb->query_result($result,0,'categoria');
    $subcategoria = $adb->query_result($result,0,'subcategoria');
    $valor = $adb->query_result($result,0,'valor');
    $tipo = $adb->query_result($result,0,'tipo');
    $conta = $adb->query_result($result,0,'conta');
    $centrocusto = $adb->query_result($result,0,'centrocusto');
    $emissor = $adb->query_result($result,0,'emissor');
    $relacionado = $adb->query_result($result,0,'relacionado');
    
// Identifica a frequencia
$date_calculation = "";

switch ($event_repetition_type) {
    case "Diária":
    $date_calculation = " +1 day";
    break;
case "Semanal":
    $date_calculation = " +1 week";
    break;
case "Mensal":
    $date_calculation = " +1 month";
    break;
case "Trimestral":
    $date_calculation = " +3 months";
    break;
case "Semestral":
    $date_calculation = " +6 months";
    break;
case "Anual":
    $date_calculation = " +1 year";
    break;    
default:
    $date_calculation = "none";
}


$query0 = "SELECT accesskey FROM `vtiger_users` WHERE `id` = 1";
    $result0 = $adb->pquery($query0);
        $accesskey = $adb->query_result($result0,0,'accesskey');

$sql1 = "SELECT id FROM `vtiger_ws_entity` WHERE `name` LIKE 'ContasPagamento'";   
    $result1 = $adb->pquery($sql1);
        $module_conta = $adb->query_result($result1,0,'id');

$sql2 = "SELECT id FROM `vtiger_ws_entity` WHERE `name` LIKE 'EmpresaFaturar'";        
    $result2 = $adb->pquery($sql2);
        $module_empresa = $adb->query_result($result2,0,'id');          
    
$sql3 = "SELECT id FROM `vtiger_ws_entity` WHERE `name` LIKE 'Invoice'";   
    $result3 = $adb->pquery($sql3);
        $module_invoice = $adb->query_result($result3,0,'id');    

$sql4 = "SELECT id FROM `vtiger_ws_entity` WHERE `name` LIKE 'PurchaseOrder'";   
    $result4 = $adb->pquery($sql4);
        $module_po = $adb->query_result($result4,0,'id');    
       
$sql5 = "SELECT id FROM `vtiger_ws_entity` WHERE `name` LIKE 'Accounts'";   
    $result5 = $adb->pquery($sql5);
        $module_accounts = $adb->query_result($result5,0,'id');    
       
$sql6 = "SELECT id FROM `vtiger_ws_entity` WHERE `name` LIKE 'Vendors'";   
    $result6 = $adb->pquery($sql6);
        $module_vendors = $adb->query_result($result6,0,'id');    
        
$day = strtotime($event_date);

$rel_conta = $module_conta . 'x' . $conta;
$rel_empresa = $module_empresa . 'x' . $centrocusto;

if ($tipo == "Entrada"){
    $rel_emissor = $module_accounts . 'x' . $emissor;
    $rel_pedido = $module_invoice . 'x' . $relacionado;
}
if ($tipo == "Saída"){
    $rel_emissor = $module_vendors . 'x' . $emissor;
    $rel_pedido = $module_po . 'x' . $relacionado;
}

include_once 'include/Webservices/Create.php';

if ($event_infinito == "Ilimitada") {

$i = 1;    
$to = strtotime(date('Y-m-d', $day) . $infinito_limite);
        
        while( $day < $to ){
            
            $i++;
            $day = strtotime(date("Y-m-d", $day) . $date_calculation);
            $vencimento = date("Y-m-d", $day);
            
                $data = array(
                    'vencimento' => $vencimento,       
                    'categoria_pagamento' => $categoria, 
//                  'recorrencia' => 'Ilimitada',        
                    'subcategoria_pagamento' => $subcategoria,
                    'frequencia' => $event_repetition_type,
                    'valor_pagamento' => $valor,             
                    'ref_pagamento' => $referencia . ' #' . $i,
                    'tipo_pagamento' => $tipo,
                    'emissor' => $rel_emissor,
                    'relacionado_pedido' => $rel_pedido,
                    'conta' => $rel_conta,
                    'centro_custo' => $rel_empresa,
                    'assigned_user_id' => '1'
                );
                vtws_create('Pagamentos', $data, $current_user);
        }
}

if ($event_infinito == "Limitada") {

$i = 1;

    while( $i < $event_no_repetition ){
    
    $i++;
    $day = strtotime(date('d-m-Y', $day) . $date_calculation);
    $vencimento = date("Y-m-d", $day);

                $data = array(
                    'vencimento' => $vencimento,       
                    'categoria_pagamento' => $categoria, 
//                  'recorrencia' => 'Limitada',        
                    'subcategoria_pagamento' => $subcategoria,
                    'frequencia' => $event_repetition_type,
                    'valor_pagamento' => $valor,             
                    'ref_pagamento' => $referencia . ' #' . $i . ' de ' . $event_no_repetition,
                    'tipo_pagamento' => $tipo,
                    'emissor' => $rel_emissor,
                    'relacionado_pedido' => $rel_pedido,
                    'conta' => $rel_conta,
                    'centro_custo' => $rel_empresa,
                    'assigned_user_id' => '1'
                );
   		vtws_create('Pagamentos', $data, $current_user);
    }     
} 
}


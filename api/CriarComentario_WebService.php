<?php

/* 
 * Cria um registro de comentário relacioando a uma conta 
 * Use para criar comentários em qualquer módulo do VTiger para documentar ações de integrações
 */

include_once 'vtwsclib/Vtiger/WSClient.php';
$client = new Vtiger_WSClient('http://testevtiger6.vtiger6.com');
$client->doLogin('admin', 'accesskey');

// Encontra o ID da Conta pelo nome
$organizations = $client->doQuery(
    "SELECT id FROM Accounts WHERE accountname='Web Absoluta'"
);

// ID do usuário que vai criar o comentário
$user_id = '1';

if ($organizations) {
    $parentRecord = $organizations[0];
    $data = array(
        'commentcontent' => 'Novo Teste de Comentario pelo WS',
        'related_to' => $parentRecord['id'], // (moduleId x recordId)
        'assigned_user_id' => $user_id
    );

    $opportunity = $client->doCreate('ModComments', $data);
    if ($opportunity) {
        print_r($opportunity);
    } else {
        echo $client->getLastError();
    }
} else {
    echo "Account no found";
}

<?php

$projeto_id = $_POST['projeto_id'];

// Inserindo Comprovante como Documento 

// Carrega Banco de Dados via mysql_select_db
    $conexao = mysql_connect($servername, $username, $password) or die (mysql_error());
    mysql_select_db($dbname);

// Funçao para Pegar Nº da Semana
function week_of_month($date) {
    $date_parts = explode('-', $date);
    $date_parts[2] = '01';
    $first_of_month = implode('-', $date_parts);
    $day_of_first = date('N', strtotime($first_of_month));
    $day_of_month = date('j', strtotime($date));
    return floor(($day_of_first + $day_of_month - 1) / 7) + 1;
}

// Carrega o arquivo vindo do Post
        $files = $_FILES['c226'];

// Identifica o Diretório a ser usado        
	$directory = 'storage/'.date("Y").'/'.date("F").'/week'.week_of_month(date('Y-m-d')).'/';
       
		if(!file_exists($directory))
		{
			mkdir($directory, 0777, true);
			chmod($directory, 0777);
		}

			$qdocid=mysql_query("SELECT MAX(crmid)+1 as id FROM vtiger_crmentity");
			$rdocid=mysql_fetch_object($qdocid);
			$docid=$rdocid->id;
			mysql_query("UPDATE vtiger_crmentity_seq SET ID='$docid'");

			$qattid=mysql_query("SELECT MAX(crmid)+1 as id FROM vtiger_crmentity");
			$rattid=mysql_fetch_object($qattid);
			$attid=$rdocid->id;
			$attid = $attid + 1;  // Correção no código original
			mysql_query("UPDATE vtiger_crmentity_seq SET ID='$attid'");

			$valor = utf8_decode($files['name']) ;
			$valor = str_replace(" ","_",preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($valor))));
			$arquivo=$attid.'_'.$valor;
        
                move_uploaded_file($files['tmp_name'], $directory . $arquivo) or
                        die("<p>Erro durante a manipula&ccedil;&atilde;o do arquivo '$arquivo'</p>".'<p><a href="'.$_SERVER["PHP_SELF"].'">Voltar</a></p>');

		if(filetype($directory . $arquivo))

		{
			$path_parts = $directory . $arquivo;
			$filesize = filesize($directory . $arquivo);
                        $filetype = $files['type'];
                        $filename = $files['name'];
                        $filename = 'Comprovante Pagamento_' . $filename;
			
                        $qdoccod=mysql_query("SELECT CONCAT(PREFIX,CUR_ID) AS COD FROM vtiger_modentity_num WHERE SEMODULE='Documents' AND ACTIVE=1");
			$rdoccod=mysql_fetch_object($qdoccod);
			mysql_query("UPDATE vtiger_modentity_num SET CUR_ID=CUR_ID+1 WHERE semodule='Documents'");

			//Atualiza Tabela crmentity com Documents 
 			mysql_query("INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted, label)
				VALUES ('$docid', '1', '1', '1', 'Documents', '', '".date('Y-m-d H:i')."', '".date('Y-m-d H:i')."', NULL, NULL, '0', '1', '0', '".$filename."')");
			
                        //Atualiza Tabela Notes
			mysql_query("INSERT INTO vtiger_notes (notesid,note_no, title ,filename,folderid, filetype,filelocationtype, filestatus, filesize)
					VALUES ('$docid','$rdoccod->COD', '".$valor."', '".$valor."',2, '".$filetype."','I','1','$filesize')") or die(mysql_error());

			//Atualiza Tabela crmentity com Attchment
 			mysql_query("INSERT INTO vtiger_crmentity (crmid, smcreatorid, smownerid, modifiedby, setype, description, createdtime, modifiedtime, viewedtime, status, version, presence, deleted)
				VALUES ('$attid', '1', '1', '1', 'Documents Attachment', '', '".date('Y-m-d H:i')."', '".date('Y-m-d H:i')."', NULL, NULL, '0', '1', '0')");
			
                        //Atualiza Tabela Attachmenta
			mysql_query("INSERT INTO vtiger_attachments (attachmentsid, name ,description, type, path, subject)
					VALUES ('$attid', '".$valor."','Documento enviado On-Line - ".$valor."', '".$filetype."','$directory','Documento enviado On-Line')") or die(mysql_error());

			//Atualiza Tabela SeeAttachmentsRel para fazer relacionanemtno entre Doc e Att
			mysql_query("INSERT INTO vtiger_seattachmentsrel (crmid,attachmentsid)
					VALUES ('$docid','$attid')") or die(mysql_error());


			//Atualiza tabela SeNotesRel para fazer o relacionanmento entre o Document e o Registro
			mysql_query("INSERT INTO vtiger_senotesrel (crmid,notesid)
					VALUES ('$projeto_id','$docid')") or die(mysql_error());				
		}


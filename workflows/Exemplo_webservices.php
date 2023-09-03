<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * This method is registered with workflow as custom function to be invoked via Task.
 * @param $entity Instance of class VTWorkflowEntity will be passed when workflow is being executed.
 */
function Project_WorkflowTask_AutoCreateFn($entity) {

	global $current_user, $adb, $log;

	$log->debug("Entering Custom Workflow Function: Create Project from (".$entity->get('moduleName').")...");
	$log->info("Trigerring Potential is: ".$entity->get('id').".");

	include_once 'include/utils/CommonUtils.php';
	include_once 'include/database/PearDatabase.php';

	// Get the user of the last modifier (or owner) of the entity that triggered this workflow 
	$id = explode("x", $entity->get('id'));
	$potid = $id[1];	
	
	$log->info("Querying for user id (".$potid.")...");
	$query = "SELECT smcreatorid, modifiedby FROM vtiger_crmentity WHERE crmid = ?";
	$result = $adb->pquery($query,array($potid));
	
	$modby = $adb->query_result($result,0,"modifiedby");	
	$creby = $adb->query_result($result,0,"smcreatorid");
	
	// If you want the "username" use function getUserName($id)
	if ($modby == 0 ) {
		// Entity not been modified so use creator's ID
		$uname = getUserFullName($creby);
	} else {
		$uname = getUserFullName($modby);
	}

	// Build the information for the new Project
	$name = $entity->get('potentialname');
	$description = "This Project was automatically created when Potential No. "
				 . $entity->get('potential_no') . " was changed to " 
				 . $entity->get('sales_stage') . " by " . $uname . ".\n\n" 
				 . $entity->get('description');
	$budget = $entity->get('amount');
	$related_to = $entity->get('related_to');
	$assigned_to = $entity->get('assigned_user_id');

	// Just get the current date
	$date = date("Y/m/d");
	
	// Assemble the necessary Project fields 	
	$parameters = array( 
		'projectname' => $name,
		'startdate' => $date,
		'targetenddate' => $date,
		'actualenddate' => $date,
		'description' => $description,
		'projectstatus' => '--none--',
		'projecttype' => '--none--',
		'progress' => '--none--',
		'assigned_user_id' => $assigned_to,		
		'targetbudget' => $budget,
		'linktoaccountscontacts' => $related_to
	);

	// Create the new Project
	include_once 'include/Webservices/Create.php';
	$log->info("Creating new Project: ".$parameters."...");
	vtws_create('Project', $parameters, $current_user);
	$log->debug("Exit Custom Workflow Function...");
}
?>
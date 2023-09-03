<?php

/*
  Workflows Invoke Custom Function example Method Function file (myExample.inc)
  Put this file in the modules/Workflow/ directory. If the directory does
   not exist, then just create it
  See the myExample_REGISTER.php file for more information.
*/

require_once 'include/utils/utils.php';
require 'modules/com_vtiger_workflow/VTEntityMethodManager.inc';
$emm = new VTEntityMethodManager($adb); 

//$emm->addEntityMethod("Module Name","Label", "Path to file" , "Method Name" );
$emm->addEntityMethod("Invoice", "Update Inventory", "include/InventoryHandler.php", "handleInventoryProductRel");

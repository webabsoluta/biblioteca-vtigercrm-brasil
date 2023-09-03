<?php

/* 
 * To Using the events manager we can add custom functionality to run at one of the possible event triggers. The following are some very brief notes compiled from the official documentation.

These triggers are (more may exist but these are those which I have seen used):

vtiger.entity.beforesave
- This event is fired before an entity is saved. You will be passed a VTEntityData object representing the saved entity. It should be noted that new objects will not have an id. You can modify the contents of $entityData, this will be reflected in the save operation, some care should be taken as it is possible to corrupt the entity.
vtiger.entity.aftersave
- This event is fired after an entity is saved
vtiger.entity.beforesave.modifiable
- This is fired before the beforesave event. Use this event if you want to change the contents of the entity object before saving it.
vtiger.entity.aftersave.final
- This is fired after the beforesave event. This can be used to get the exact content of the entity before saveing it.
vtiger.entity.afterrestore vtiger.entity.beforedelete
- I could not find any documentation for these final two event types.

 ***> Firstly we must register our new event handler.
*/

$eveman = new VTEventsManager($adb);
  
 void registerHandler ($forEvent, $path, $className, [$condition])  
 
$eveman->registerHandler('vtiger.entity.aftersave', 'modules/EventHandlers/HandlerExample.inc', 'HandlerExample');

 
 // Now you can do something as simple as the following example to ensure that your script is run after an entity has been saved.

 class HandlerExample extends VTEventHandler {
   public function handleEvent($eventName, $entityData) {
     switch ($eventName) {
       case 'vtiger.entity.aftersave':
         $this->aftersave($entityData);
         break;
     }
   }
   private function aftersave($entityData) {
     // Your custom aftersave functionality
   }
 }change this license header, choose License Headers in Project Properties.


         //Segundo Exemplo: The event handler parameter will have the information about the record mode as depicted below:
         
class MyModuleEventHandler extends VTEventHandler {
    function handleEvent($eventName, $data) {
        if ($data->isNew()) {
            // Create of new record
        } else {
            // Edit of older record
        }
    }
}
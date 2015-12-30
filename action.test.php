<?php
#----------------------------------------------------------------------
# Module: Notifier - a communications module
# Action: test - send a test message
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

if(!($this->CheckPermission('ModifyNotifierProperties')
  || $this->CheckPermission('SeeNotifierProperties'))) exit;

/*
TODO determine channel from $params e.g. type of entered destination
construct test message & related parameters
get relevant comm class
get result from sending
report channel and result
*/

$this->Redirect($id,'defaultadmin',$returnid,
	array('activetab'=>'test','message'=>'TESTING NOT YET IMPLEMENTED'));

?>

<?php
#----------------------------------------------------------------------
# Module: Notifier - a message-sender module
# Method: uninstall
#----------------------------------------------------------------------
# See file Notifier.module.php for full details of copyright, licence, etc.
#----------------------------------------------------------------------

$dict = NewDataDictionary($db);
$tblname = cms_db_prefix().'module_tell_tweeter';
$sql = $dict->DropTableSQL($tblname);
$dict->ExecuteSQLArray($sql);

$this->RemovePreference();

$this->RemovePermission('SeeNotifierProperties');
$this->RemovePermission('ModifyNotifierProperties');

<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}
Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'Wdcalender',
	array(
		'EventCalender' => 'list, show, new, create, edit, update, delete,compact',	
	),
	array(
		'EventCalender' => 'list, create, update, delete,compact',	
	)
);

?>
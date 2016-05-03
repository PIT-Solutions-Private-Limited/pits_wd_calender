<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Pits.' . $_EXTKEY,
	'Pitswdcalender',
	array(
		'EventCalender' => 'list, show, new, create, edit, update, delete,compact',
		
	),
	// non-cacheable actions
	array(
		'EventCalender' => 'list, create, update, delete,compact',
		
	)
);

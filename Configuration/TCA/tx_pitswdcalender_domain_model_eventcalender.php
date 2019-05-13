<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Abin Sabu <abin.s@pitsolutions.com>, PITS
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

return array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:pits_wd_calender/Resources/Private/Language/locallang_db.xml:tx_eventcalender',
		'label' => 'wd_subject',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,
		'origUid' => 't3_origuid',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => '',
		/*'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
								'Configuration/TCA/EventCalender.php',*/
		'iconfile' =>  'EXT:pits_wd_calender/Resources/Public/Icons/eventcalender.gif'
	),
	'interface' => array(
		'showRecordFieldList' => 'sys_language_uid,l10n_parent,l10n_diffsource,hidden,evt_timestamp,evt_name,wd_subject,
    wd_lat_long'
		),
	'types' => array(
		'0' => array('showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,wd_subject,wd_starttime,
    wd_endtime,wd_lat_long,wd_description,wd_location')
		),
	'palettes' => array(
		'1' => array('showitem' => ''),
		),
	'columns' => array(
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple'
                    ]
                ],
                'default' => 0,
            ],
        ],
		'l10n_parent' => array(
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
			'config' => array(
				'type' => 'select',
				'items' => array(
		array('', 0),
		),
				'foreign_table' => 'tx_pitswdcalender_domain_model_eventcalender',
				'foreign_table_where' => 'AND tx_pitswdcalender_domain_model_eventcalender.pid=###CURRENT_PID### AND
         tx_pitswdcalender_domain_model_eventcalender.sys_language_uid IN (-1,0)',
		),
		),
		'l10n_diffsource' => array(
			'config' => array(
				'type' => 'passthrough',
		),
		),
		't3ver_label' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array(
				'type' => 'input',
				'size' => 30,
				'max' => 255,
		)
		),
		'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
		'starttime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
		),
		),
		),
		'endtime' => array(
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config' => array(
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
				'range' => array(
					'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
		),
		),
		),
		'wd_subject' => array(		
			'label' => 'LLL:EXT:pits_wd_calender/Resources/Private/Language/locallang.xml:tx_pitswdcalender.wd_subject',		
			'config' => array(
				'type'  =>'input',
				'eval'  =>  'required',
				'size' => '30',
			)
		),
		'wd_starttime' => array(
			'label' => 'LLL:EXT:pits_wd_calender/Resources/Private/Language/locallang.xml:tx_pitswdcalender.wd_starttime',
			'config' => array(
				'type' => 'input',
				'eval' => 'required,datetime',
				'size' => '30',
			)
		),
		'wd_endtime' => array(		
			'label' => 'LLL:EXT:pits_wd_calender/Resources/Private/Language/locallang.xml:tx_pitswdcalender.wd_endtime',			
			'config' => array(
				'type' => 'input',
				'eval' => 'required,datetime',
				'size' => '30',
			)
		),
		'wd_description' => array(
			'label' => 'LLL:EXT:pits_wd_calender/Resources/Private/Language/locallang.xml:tx_pitswdcalender.wd_description',
			'config' => array(
				'type' => 'text',
				'size' => '30',
			)
		),
		'wd_lat_long' => Array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:pits_wd_calender/Resources/Private/Language/locallang.xml:tx_pitswdcalender.wd_lat_long',	
			'config' => Array (
				'type' => 'input',
				//'userFunc' => \Pits\PitsWdCalender\Userfunc\GooglePicker::class . '->renderForm',
				'size' => '80',
				'eval' => 'trim',
				'wizards' => Array (
					'_PADDING' => 2,
					'geo_popup' => Array (
						'type' => 'popup',
						'title' => 'PIT Map GEO Selector',
						'icon' => 'EXT:pits_wd_calender/geo_popup.gif',
						/*'script' => 'EXT:pits_wd_calender/google_selector.php',*/
						'module' => array(
							'name' => 'wizard_GooglePicker',						
						),
						'JSopenParams' => 'height=600,width=800,status=0,menubar=0,scrollbars=1'
					),
				),
			),
		),
	),
);

?>
				
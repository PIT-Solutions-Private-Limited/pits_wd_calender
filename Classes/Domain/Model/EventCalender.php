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
/**
 *
 *
 * @package wd_calender2
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_PitsWdCalender_Domain_Model_EventCalender extends Tx_Extbase_DomainObject_AbstractEntity {
	public function createEvent( $eventData = array() ){
		$lat_long = $eventData['formValues']['evt_lat'].';'.$eventData['formValues']['evt_long'].';'.$eventData['formValues']['evt_loc'];
		$ret = array();
		try{
			$GLOBALS['TYPO3_DB']->store_lastBuiltQuery=1;
			$insertArray = array( 'wd_subject' => $eventData['formValues']['evt_name'],
'wd_starttime' => $this->js2PhpTime($eventData['formValues']['evt_sdate']), 
'wd_endtime' => $this->js2PhpTime($eventData['formValues']['evt_edate']), 
'pid' =>$GLOBALS['TSFE']->id,
'tstamp' => time(),
'wd_description' => $eventData['formValues']['evt_desc'],
'wd_lat_long' => $lat_long,
			);
			$resp= $GLOBALS['TYPO3_DB']->exec_INSERTquery( 'tx_pitswdcalender_domain_model_eventcalender', $insertArray);
			if($resp==FALSE){
				$ret['IsSuccess'] = FALSE;
				$ret['Msg'] = "MySql Error!";
			}else{
				$ret['IsSuccess'] = TRUE;
				$ret['Msg'] = 'add success';
				$ret['Data'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
			}
		}catch(Exception $e){
			$ret['IsSuccess'] = FALSE;
			$ret['Msg'] = $e->getMessage();
		}
		return $ret;
	}
	public function js2PhpTime($jsdate) {
		if(preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches)==1){
			$ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
		}else if(preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches)==1){
			$ret = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
		}
		return $ret;
	}
	public function getAllEvents($pId){
		$whereClause = 'pid='.$pId;
		$orderByClause = '';
		$limitClause = '';
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,pid,wd_subject,wd_starttime,wd_endtime,wd_lat_long', 'tx_pitswdcalender_domain_model_eventcalender', $whereClause, '', $orderByClause, $limitClause );
		foreach($result as $key=>$value){
			$result[$key]['wd_subject'] = ucwords( $value['wd_subject'] );
$result[$key]['wd_starttime']=date('F j, Y,g:i a', $value['wd_starttime']);
			$result[$key]['wd_endtime'] = date( ',F j, Y,g:i a', $value['wd_endtime']);
			$lat_long = explode(';', $value['wd_lat_long']);
			$lat = $lat_long[0];
			$long = $lat_long[1];
			$result[$key]['wd_latitude'] = $lat;
			$result[$key]['wd_longitude'] = $long;
		}
		return json_encode($result);
	}
	public function viewEvent($uId){
		$whereClause = 'uid='.$uId;
		$orderByClause = '';
		$limitClause = '';
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,pid,wd_subject,wd_starttime,wd_endtime,wd_description,wd_lat_long', 'tx_pitswdcalender_domain_model_eventcalender', $whereClause, '', $orderByClause, $limitClause );
		foreach($result as $key=>$value){
			if($value['wd_description'] == ''){
				$result[$key]['wd_description'] = 'No Description Found!';
			}
$result[$key]['wd_starttime']=date('F j, Y,g:i a', $value['wd_starttime']);
			$result[$key]['wd_endtime'] = date('F j, Y,g:i a', $value['wd_endtime']);
			$location = explode(';', $value['wd_lat_long']);
			$location_name = $location[2];
			$result[$key]['wd_loc_name'] = $location_name;
		}
		return $result;
	}
	/**
	 * This method of the Plugin will supply the contents for the COMPACT VIEW.
	 *
	 * @return string to the marker in the main function
	 */
	public function compactData(){
		$GLOBALS['TYPO3_DB']->store_lastBuiltQuery=1;
		try{
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows( 'uid,pid,wd_subject,wd_starttime,wd_endtime,wd_description,wd_lat_long', 'tx_pitswdcalender_domain_model_eventcalender');
			$ret = '[';
			$separator = '';
			foreach($result as $key=>$value){
				$ret = $ret.$separator;
$ret.= '{ "date": "'.($value['wd_starttime']*1000).'", "type": "'.$value['wd_subject'].'", "title": "'.$value['wd_subject'].'", "description": "'.$value['wd_description'].'", "url": "'.$value['uid'].'" }';
				$separator = ',';
			}
			$ret.=']';
		}catch(Exception $e){
			$ret['error'] = $e->getMessage();
		}
		return $ret;
	}
}
?>

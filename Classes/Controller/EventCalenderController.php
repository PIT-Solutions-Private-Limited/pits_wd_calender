<?php	/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Abin Sabu <abin.s@pitsolutions.com>,PITS
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
 * @license http://www.gnu.org/licenses/gpl.html
 * GNU General Public License, version 3 or later
 *
 */
class Tx_PitsWdCalender_Controller_EventCalenderController extends Tx_Extbase_MVC_Controller_ActionController {
	/**
	 * eventCalenderRepository
	 *
	 * @var Tx_PitsWdCalender_Domain_Repository_EventCalenderRepository
	 */
	protected $eventCalenderRepository;
	protected $eventCalenderModel;

	public function __construct(){
		$this->eventCalenderModel = new Tx_PitsWdCalender_Domain_Model_EventCalender();
	}
	/**
	 * action list
	 *
	 * @return void
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 */
	public function listAction() {
		$flexformValues = $this->settings;
		$view_type = $flexformValues['view_select'];
		$cObjData = $this->configurationManager->getContentObject();
		$conf = array(
'parameter' => $GLOBALS['TSFE']->id,
'additionalParams' => '&tx_pitswdcalender_wdcalender[action]=',
'useCashHash' => TRUE,
'returnLast' => 'url'
		);
		$url = $cObjData->typoLink('', $conf);
		$ajax_url = $url;
		$events = $this->eventCalenderModel->getAllEvents($GLOBALS['TSFE']->id);
		switch($view_type){
			case 1:
				$mapData['compact'] = 1;
				break;
			case 2:
				$location = explode(',', $flexformValues['def_marker_pos']);
				$location_lat = $location[0];
				$location_log = $location[1];
				$mapData['apiKey'] = $flexformValues['api_key'];
				$mapData['map_width'] = $flexformValues['map_width'];
				$mapData['map_height'] = $flexformValues['map_height'];
				$mapData['lat'] = $location_lat;
				$mapData['log'] = $location_log;
				$mapData['events'] = $events;
				$mapData['markerView'] = TRUE;
				break;
			case 3:
				$location = explode(',', $flexformValues['def_marker_pos']);
				$mapData['map_width'] = $flexformValues['map_width'];
				$mapData['map_height'] = $flexformValues['map_height'];
				$location_lat = $location[0];
				$location_log = $location[1];
				$mapData['apiKey'] = $flexformValues['api_key'];
				$mapData['lat'] = $location_lat;
				$mapData['log'] = $location_log;
				$mapData['events'] = $events;
				$mapData['markerView'] = FALSE;
				break;
		}
		$this->view->assign('events' , html_entity_decode($events));
		$this->view->assign('uid' , $ajax_url);
		$this->view->assign('mapData' , $mapData);
		$this->view->assign('extensionPathJs' , t3lib_extMgm::siteRelPath('pits_wd_calender').'Resources/Public/js/');
		$this->view->assign('extensionPathCss' , t3lib_extMgm::siteRelPath('pits_wd_calender').'Resources/Public/css/');
	}
	/**
	 * action show
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 * @return void
	 */
	public function showAction() {
		$event = $this->eventCalenderModel->viewEvent( $_POST['formValues']);
		$this->view->assign('eventName' , ucwords($event[0]['wd_subject']));
		$this->view->assign('eventLocation' , $event[0]['wd_loc_name']);
		$this->view->assign('eventSdate' , $event[0]['wd_starttime']);
		$this->view->assign('eventEdate' , $event[0]['wd_endtime']);
		$this->view->assign('eventDesc' , ucwords($event[0]['wd_description']));
		echo $this->view->render();
		exit;
	}
	/**
	 * action new
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 * @dontvalidate $newEventCalender
	 * @return void
	 */
	public function compactAction() {
		$msg = $this->eventCalenderModel->compactData();
		echo $msg;
		exit();
	}
	/**
	 * action create
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 * @return void
	 */
	public function createAction() {
		$msg = $this->eventCalenderModel->createEvent( $_POST );
		echo 'Event Sucessfully Saved!';
		exit;
	}
	/**
	 * action edit
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 * @return void
	 */
	public function editAction(Tx_PitsWdCalender_Domain_Model_EventCalender $eventCalender) {
		$this->view->assign('eventCalender', $eventCalender);
	}
	/**
	 * action update
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 * @return void
	 */
	public function updateAction(Tx_PitsWdCalender_Domain_Model_EventCalender $eventCalender) {
		$this->eventCalenderRepository->update($eventCalender);
		$this->flashMessageContainer->add('Your EventCalender was updated.');
		$this->redirect('list');
	}
	/**
	 * action delete
	 * @param Tx_PitsWdCalender_Domain_Model_EventCalender
	 * @return void
	 */
	public function deleteAction(Tx_PitsWdCalender_Domain_Model_EventCalender $eventCalender) {
		$this->eventCalenderRepository->remove($eventCalender);
		$this->flashMessageContainer->add('Your EventCalender was removed.');
		$this->redirect('list');
	}
	/**
	 * injectEventCalenderRepository
	 * @param
	 * Tx_PitsWdCalender_Domain_Repository_EventCalenderRepository
	 * $EventCalenderRepository
	 * @return void
	 */
	public function injectEventCalenderRepository(Tx_PitsWdCalender_Domain_Repository_EventCalenderRepository $eventCalenderRepository) {
		$this->eventCalenderRepository = $eventCalenderRepository;
	}
}
?>
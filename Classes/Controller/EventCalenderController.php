<?php
namespace PITS\PitsWdCalender\Controller;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/* * *************************************************************
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
 * ************************************************************* */

/**
 *
 *
 * @package wd_calender2
 * @license http://www.gnu.org/licenses/gpl.html
 * GNU General Public License, version 3 or later
 *
 */
class EventCalenderController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * eventCalenderRepository
     *
     * @var \PITS\PitsWdCalender\Domain\Repository\EventCalenderRepository
     * @inject
     */
    protected $eventCalenderRepository = null;

    /**
     * eventCaltenderModel
     *
     * @var \PITS\PitsWdCalender\Domain\Model\EventCalender
     */
    protected $eventCalenderModel = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager
     * @inject
     */
    protected $persistenceManager;

    /**
     * constructor function
     */
    public function __construct()
    {
        $this->eventCalenderModel = new \PITS\PitsWdCalender\Domain\Model\EventCalender();
    }

    /**
     * action list
     *
     * @return void
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     */
    public function listAction()
    {
        $flexformValues = $this->settings;
        $view_type = $flexformValues['view_select'];
        $events = $this->eventCalenderRepository->getAllEvents($GLOBALS['TSFE']->id);
        $location = explode(',', $flexformValues['def_marker_pos']);

        switch ($view_type) {
            case 1:
                $mapData['compact'] = 1;
                break;
            case 2:
                $mapData = [
                    'apiKey' => isset($flexformValues['api_key']) ? $flexformValues['api_key'] : null,
                    'map_width' => $flexformValues['map_width'],
                    'map_height' => $flexformValues['map_height'],
                    'lat' => isset($location[0]) ? $location[0] : null,
                    'log' => isset($location[1]) ? $location[1] : null,
                    'events' => $events,
                    'markerView' => TRUE
                ];
                break;
            case 3:
                $mapData = [
                    'apiKey' => isset($flexformValues['api_key']) ? $flexformValues['api_key'] : null,
                    'map_width' => $flexformValues['map_width'],
                    'map_height' => $flexformValues['map_height'],
                    'lat' => isset($location[0]) ? $location[0] : null,
                    'log' => isset($location[1]) ? $location[1] : null,
                    'events' => $events,
                    'markerView' => FALSE
                ];
                break;
        }
        $this->view->assign(
            'events', 
            html_entity_decode($events)
        );
        $this->view->assign(
            'mapData', 
            $mapData
        );
        $this->view->assign(
            'extensionPathJs', 
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('pits_wd_calender') . 'Resources/Public/js/'
        );
        $this->view->assign(
            'extensionPathCss', 
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('pits_wd_calender') . 'Resources/Public/css/'
        );
    }

    /**
     * action show
     * 
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     * @return void
     */
    public function showAction()
    {
        $eventId = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('eventId');
        $event = $this->eventCalenderRepository->viewEvent($eventId);
        $this->view->assign('eventName', ucwords($event[0]['wd_subject']));
        $this->view->assign('eventLocation', $event[0]['wd_loc_name']);
        $this->view->assign('eventSdate', $event[0]['wd_starttime']);
        $this->view->assign('eventEdate', $event[0]['wd_endtime']);
        $this->view->assign('eventDesc', ucwords($event[0]['wd_description']));
        echo $this->view->render();
        exit;
    }

    /**
     * action new
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     * @return void
     */
    public function compactAction()
    {
        $msg = $this->eventCalenderRepository->compactData();
        echo $msg;
        exit();
    }

    /**
     * action create
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     * @return void
     */
    public function createAction(\PITS\PitsWdCalender\Domain\Model\EventCalender $newEventCalender)
    {
        $request = $this->request->getArguments();
        $lat_long = $request['evt_lat'] . ';' . $request['evt_long'] . ';' . $request['evt_loc'];
        $startDate = $this->js2PhpTime($newEventCalender->getWdStarttime());
        $endDate = $this->js2PhpTime($newEventCalender->getWdEndtime());
        $newEventCalender->setWdLatLong($lat_long);
        $newEventCalender->setWdStarttime($startDate);
        $newEventCalender->setWdEndtime($endDate);
        $this->eventCalenderRepository->add($newEventCalender);
        $this->persistenceManager->persistAll();

        echo 'Event Sucessfully Saved!';
        exit;
    }

    /**
     * action edit
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     * @return void
     */
    public function editAction(\PITS\PitsWdCalender\Domain\Model\EventCalender $eventCalender)
    {
        $this->view->assign('eventCalender', $eventCalender);
    }

    /**
     * action update
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     * @return void
     */
    public function updateAction(\PITS\PitsWdCalender\Domain\Model\EventCalender $eventCalender)
    {
        $this->eventCalenderRepository->update($eventCalender);
        $this->flashMessageContainer->add('Your EventCalender was updated.');
        $this->redirect('list');
    }

    /**
     * action delete
     * @param \PITS\PitsWdCalender\Domain\Model\EventCalender
     * @return void
     */
    public function deleteAction(\PITS\PitsWdCalender\Domain\Model\EventCalender $eventCalender)
    {
        $this->eventCalenderRepository->remove($eventCalender);
        $this->flashMessageContainer->add('Your EventCalender was removed.');
        $this->redirect('list');
    }

    public function js2PhpTime($jsdate)
    {
        if (preg_match('@(\d+)/(\d+)/(\d+)\s+(\d+):(\d+)@', $jsdate, $matches) == 1) {
            $ret = mktime($matches[4], $matches[5], 0, $matches[1], $matches[2], $matches[3]);
        } else if (preg_match('@(\d+)/(\d+)/(\d+)@', $jsdate, $matches) == 1) {
            $ret = mktime(0, 0, 0, $matches[1], $matches[2], $matches[3]);
        }
        return $ret;
    }
}

?>

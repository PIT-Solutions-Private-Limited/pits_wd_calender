<?php
namespace Pits\PitsWdCalender\Domain\Repository;

/* * *************************************************************
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
 * ************************************************************* */

/**
 *
 *
 * @package wd_calender2
 * @license http://www.gnu.org/licenses/gpl.html 
 * GNU General Public License, version 3 or later
 *
 */
class EventCalenderRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{

    /**
     * This method of the Plugin will give the list of events
     * @return array $list
     */
    public function getAllEvents($pId)
    {
        $orderByClause = '';
        $limitClause = '';
        $query = $this->createQuery();
        $query->matching(
            $query->equals('pid', $pId), $query->equals('deleted', 0), $query->equals('hidden', 0)
        );
        $result = $query->execute()->toArray();
        $list = [];
        foreach ($result as $key => $value) {
            $list[$key]['uid'] = $value->getUid();
            $list[$key]['pid'] = $value->getPid();
            $list[$key]['wd_subject'] = ucwords($value->getWdSubject());
            $list[$key]['wd_starttime'] = date('F j, Y,g:i a', $value->getWdStarttime());
            $list[$key]['wd_endtime'] = date(',F j, Y,g:i a', $value->getWdEndtime());
            $list[$key]['wd_lat_long'] = $value->getWdLatLong();
            $lat_long = explode(';', $value->getWdLatLong());
            $lat = $lat_long[0];
            $long = $lat_long[1];
            $list[$key]['wd_latitude'] = $lat;
            $list[$key]['wd_longitude'] = $long;
        }

        return json_encode($list);
    }

    /**
     * This method of the Plugin will supply the single ebvent details
     *
     * @return array list
     */
    public function viewEvent($uId)
    {
        $orderByClause = '';
        $limitClause = '';
        $query = $this->createQuery();
        $query->matching(
            $query->equals('uid', $uId)
        );
        $result = $query->execute()->toArray();
        $list = [];
        foreach ($result as $key => $value) {
            $list[$key]['uid'] = $value->getUid();
            $list[$key]['pid'] = $value->getPid();
            $list[$key]['wd_subject'] = ucwords($value->getWdSubject());
            if ($value->getWdDescription() == '') {
                $list[$key]['wd_description'] = 'No Description Found!';
            } else {
                $list[$key]['wd_description'] = $value->getWdDescription();
            }

            $list[$key]['wd_starttime'] = date('F j, Y,g:i a', $value->getWdStarttime());
            $list[$key]['wd_endtime'] = date('F j, Y,g:i a', $value->getWdEndtime());
            $location = explode(';', $value->getWdLatLong());
            $location_name = $location[2];
            $list[$key]['wd_loc_name'] = $location_name;
        }

        return $list;
    }

    /**
     * This method of the Plugin will supply the contents for the COMPACT VIEW.
     *
     * @return string to the marker in the main function
     */
    public function compactData()
    {
        $GLOBALS['TYPO3_DB']->store_lastBuiltQuery = 1;
        try {
            $query = $this->createQuery();
            $query->matching(
                $query->equals('deleted', 0), $query->equals('hidden', 0)
            );
            $result = $query->execute()->toArray();
            $ret = '[';
            $separator = '';
            foreach ($result as $key => $value) {
                $ret = $ret . $separator;
                $ret .= '{ "date": "' . ($value->getWdStarttime() * 1000) . '", "type": "' . $value->getWdSubject() . '", "title": "' . $value->getWdSubject() . '", "description": "' . $value->getWdDescription() . '", "url": "' . $value->getUid() . '" }';
                $separator = ',';
            }
            $ret .= ']';
        } catch (Exception $e) {
            $ret['error'] = $e->getMessage();
        }
        return $ret;
    }
}

?>
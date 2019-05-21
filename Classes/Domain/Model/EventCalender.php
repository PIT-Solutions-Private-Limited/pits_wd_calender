<?php
namespace PITS\PitsWdCalender\Domain\Model;
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
class EventCalender extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {
	/**
     * wdSubject
     *
     * @var string
     * @validate NotEmpty
     */
    protected $wdSubject = '';

    /**
     * wdStarttime
     *
     * @var string
     * @validate NotEmpty
     */
    protected $wdStarttime = '';

    /**
     * wdEndtime
     *
     * @var string
     * @validate NotEmpty
     */
    protected $wdEndtime = '';

    /**
     * wdDescription
     *
     * @var string
     * @validate NotEmpty
     */
    protected $wdDescription = '';

    /**
     * wdLatLong
     *
     * @var string
     */
    protected $wdLatLong = '';

    /**
     * Returns the wdSubject
     *
     * @return string $wdSubject
     */
    public function getWdSubject()
    {
        return $this->wdSubject;
    }

    /**
     * Sets the wdSubject
     *
     * @param string $wdSubject
     * @return void
     */
    public function setWdSubject($wdSubject)
    {
        $this->wdSubject = $wdSubject;
    }

    /**
     * Returns the wdStarttime
     *
     * @return string $wdStarttime
     */
    public function getWdStarttime()
    {
        return $this->wdStarttime;
    }

    /**
     * Sets the wdStarttime
     *
     * @param string $wdStarttime
     * @return void
     */
    public function setWdStarttime($wdStarttime)
    {
        $this->wdStarttime = $wdStarttime;
    }

    /**
     * Returns the wdEndtime
     *
     * @return string $wdEndtime
     */
    public function getWdEndtime()
    {
        return $this->wdEndtime;
    }

    /**
     * Sets the wdEndtime
     *
     * @param string $wdEndtime
     * @return void
     */
    public function setWdEndtime($wdEndtime)
    {
        $this->wdEndtime = $wdEndtime;
    }

    /**
     * Returns the wdDescription
     *
     * @return string $wdDescription
     */
    public function getWdDescription()
    {
        return $this->wdDescription;
    }

    /**
     * Sets the wdDescription
     *
     * @param string $wdDescription
     * @return void
     */
    public function setWdDescription($wdDescription)
    {
        $this->wdDescription = $wdDescription;
    }

    /**
     * Returns the wdLatLong
     *
     * @return string $wdLatLong
     */
    public function getWdLatLong()
    {
        return $this->wdLatLong;
    }

    /**
     * Sets the wdLatLong
     *
     * @param string $wdLatLong
     * @return void
     */
    public function setWdLatLong($wdLatLong)
    {
        $this->wdLatLong = $wdLatLong;
    }
	
}
?>

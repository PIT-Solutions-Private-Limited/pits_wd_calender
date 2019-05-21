<?php
namespace PITS\PitsWdCalender\Controller\Wizard;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Template\DocumentTemplate;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Backend\Controller\Wizard\AbstractWizardController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Script Class for GooglePicker wizard
 *
 * Unused with new renderType "inputGooglePicker" since v8.
 *
 * @deprecated since TYPO3 v8, will be removed in TYPO3 v9
 */
class GooglepickerController extends AbstractWizardController
{
    /**
     * Wizard parameters, coming from FormEngine linking to the wizard.
     *
     * @var array
     */
    public $wizardParameters;

    /**
     * Value of the current color picked.
     *
     * @var string
     */
    public $locationValue;

    /**
     * Serialized functions for changing the field...
     * Necessary to call when the value is transferred to the FormEngine since the form might
     * need to do internal processing. Otherwise the value is simply not be saved.
     *
     * @var string
     */
    public $fieldChangeFunc;

    /**
     * @var string
     */
    protected $fieldChangeFuncHash;

    /**
     * Form name (from opener script)
     *
     * @var string
     */
    public $fieldName;

    /**
     * Field name (from opener script)
     *
     * @var string
     */
    public $formName;

    /**
     * ID of element in opener script for which to set color.
     *
     * @var string
     */
    public $md5ID;

    /**
     * Internal: If FALSE, a frameset is rendered, if TRUE the content of the picker script.
     *
     * @var int
     */
    public $showPicker;

    /**
     * @var string
     */
    public $pickerImage = '';

    /**
     * Document template object
     *
     * @var DocumentTemplate
     */
    public $doc;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
	public $P;

    /**
     * @var string
     */
	public $currGeoDat;

    /**
     * Constructor
     *
     * @deprecated since TYPO3 v8, will be removed in TYPO3 v9
     */
    public function __construct()
    {
        GeneralUtility::logDeprecatedFunction();
        parent::__construct();
        $this->getLanguageService()->includeLLFile('EXT:pits_wd_calender/locallang_wizard.xml');
        $GLOBALS['SOBE'] = $this;
        $this->init();
    }

    /**
     * Initialises the Class
     */
    protected function init()
    {
        // Setting GET vars (used in frameset script):
        $this->wizardParameters = GeneralUtility::_GP('P');

        // Setting GET vars (used in colorpicker script):
        $this->locationValue = GeneralUtility::_GP('currentValue');
        $this->fieldChangeFunc = GeneralUtility::_GP('fieldChangeFunc');
        $this->fieldChangeFuncHash = GeneralUtility::_GP('fieldChangeFuncHash');
        $this->fieldName = GeneralUtility::_GP('fieldName');
        $this->formName = GeneralUtility::_GP('formName');
        $this->md5ID = GeneralUtility::_GP('md5ID');
        $this->exampleImg = GeneralUtility::_GP('exampleImg');

        $update = [];
        if ($this->areFieldChangeFunctionsValid()) {
            // Setting field-change functions:
            $fieldChangeFuncArr = unserialize($this->fieldChangeFunc);
            unset($fieldChangeFuncArr['alert']);
            foreach ($fieldChangeFuncArr as $v) {
                $update[] = 'parent.opener.' . $v;
            }
        }
            
        $this->currGeoDat = $this->getGeoDat( $this->locationValue );
                
        if(empty($this->currGeoDat) || empty($this->locationValue)) {
            $this->currGeoDat['lat'] = '9.664';
            $this->currGeoDat['lng'] = '76.470';
        }

        // Initialize document object:
        $this->doc = GeneralUtility::makeInstance(DocumentTemplate::class);
        $this->getPageRenderer()->loadRequireJsModule(
            'TYPO3/CMS/PitsWdCalender/Googlepicker',
            'function(Googlepicker) {
				Googlepicker.setFieldChangeFunctions({
					fieldChangeFunctions: function() {'
                        . implode('', $update) .
                    '}
				});
			}'
        );

        $this->doc->docType = 'xhtml_trans';
        $this->doc->bodyTagAdditions = ' onload="initialize()" ';
        $this->doc->inDocStyles = '
            body {
                padding: 0px;
                margin: 0px;
                height: 100%;
                width: 100%;
            }

            #formContainer {
                padding: 5px 10px;
                background-color: '.$this->doc->bgColor2.'
            }
            #formContainer strong {
                color: #ffffff;
            }
            #formContainer table {
                width: 100%;
            }
        ';
        $this->doc->JScode = $this->doc->wrapScriptTags('
                
                var map = null;
                var geocoder = null;

                function initialize() {
                    var map = new google.maps.Map(document.getElementById("map_canvas"), {
                        zoom: 5,
                        center: new google.maps.LatLng("'.$this->currGeoDat['lat'].'","'.$this->currGeoDat['lng'].'"),
                        mapTypeId: google.maps.MapTypeId.ROADMAP,
                    });
                    marker = new google.maps.Marker({                           
                       position: new google.maps.LatLng("'.$this->currGeoDat['lat'].'","'.$this->currGeoDat['lng'].'"),
                       draggable: true,
                       map: map,
                    });
                    
                    google.maps.event.addListener(marker, "dragstart", function(evt){
                        infowindow.close();
                    });
                    
                    var infowindow = new google.maps.InfoWindow({
                        content: "Latitude:'.$this->currGeoDat['lat'].'<br>Longitude:'.$this->currGeoDat['lng'].'"
                    });
                    infowindow.open(map,marker);
                    google.maps.event.addListener(marker, "dragend", function(evt){
                        var infowindow = new google.maps.InfoWindow({
                            content: "Latitude:" + evt.latLng.lat().toFixed(3) +
                                "<br>Longitude:" +  evt.latLng.lng().toFixed(3)
                            });
                        infowindow.open(map,marker);
                        getLocationName(evt.latLng.lat(),evt.latLng.lng());
                    });

                    var input = document.getElementById("pac-input");

                    var autocomplete = new google.maps.places.Autocomplete(input);
                    autocomplete.bindTo("bounds", map);

                    var infowindow = new google.maps.InfoWindow();
                    var marker = new google.maps.Marker({
                        map: map
                    });
                    marker.addListener("click", function() {
                      infowindow.open(map, marker);
                    });

                    google.maps.event.addListener(autocomplete, "place_changed", function() {
                        var place = autocomplete.getPlace();
                        if (!place.geometry) {
                            return;
                        }

                        if (place.geometry.viewport) {
                            map.fitBounds(place.geometry.viewport);
                        } else {
                            map.setCenter(place.geometry.location);
                            map.setZoom(17);
                        }

                        // Set the position of the marker using the place ID and location.
                        marker.setPlace({
                            placeId: place.place_id,
                            location: place.geometry.location
                        });
                        marker.setVisible(true);


                        var infowindow = new google.maps.InfoWindow({
                            content: "Location:" + place.geometry.location +"<br>" 
                        });
                        infowindow.open(map, marker);

                        var locationValue = String(place.geometry.location).substr(1).slice(0, -1);
                        var geocode = locationValue.split(",");
                        var lat = parseFloat(geocode[0]).toFixed(3);
                        var lng = parseFloat(geocode[1]).toFixed(3);
                        document.getElementById("newlgeodata").value = lat+";"+lng+";"+place.formatted_address;
                                              
                    });

                }   

                function getLocationName(evt_lat,evt_long) {
                    var location_name
                    var geocoder = new google.maps.Geocoder();
                    var lat = evt_lat;
                    var lng = evt_long;
                    var latlng = new google.maps.LatLng(lat, lng);
                        geocoder.geocode({"latLng": latlng}, function(results, status) {
                            if (status == google.maps.GeocoderStatus.OK) {
                                if (results[1]) {
                                 var location_name = results[1].formatted_address; 
                                 document.lgeodatform.lgeodat.value = lat+";"+lng+";"+location_name;
                                }
                            } 
                            else {
                                alert("Geocoder failed due to: " + status);
                            }
                        });
                 
                   return location_name;
                  }
                  

        ');
        // Start page:
        $this->content .= $this->doc->startPage($this->getLanguageService()->getLL('geoselector_title'));
    }

    /**
     * Injects the request object for the current request or subrequest
     * As this controller goes only through the main() method, it is rather simple for now
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function mainAction(ServerRequestInterface $request, ResponseInterface $response)
    {   
        $this->main();

        $this->content .= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);

        $response->getBody()->write($this->content);
        return $response;
    }

    /**
     * Main Method, rendering either GooglePicker or frameset depending on ->showPicker
     */
    public function main()
    {
        // Show frameset by default:
        if (!GeneralUtility::_GP('showPicker')) {
            $this->frameSet();
        } else {
            // Putting together the items into a form:
            
                $content = '
                <div id="formContainer">
                    <table id="formTable" cellpadding="0" cellspacing="0">
                        <tr>
                           
                            <td width="90" align="right">
                                <form action="' . htmlspecialchars(BackendUtility::getModuleUrl('wizard_GooglePicker')) . '"  name="lgeodatform" id="lgeodatform"  method="post">
                                    <input id="pac-input" class="controls" type="text" placeholder="Search Box">
                                    <input type="hidden" name="lgeodat" id="newlgeodata" value="popupVAl" />
                                    <input value="set_data" id="savedata" type="submit">

                                    <!-- Hidden fields with values that has to be kept constant -->
                                    <input type="hidden" name="showPicker" value="1" />
                                  
                                    <input type="hidden" name="fieldChangeFuncHash" value="' . htmlspecialchars($this->fieldChangeFuncHash) . '" />
                                    <input type="hidden" name="currentValue" value="' . htmlspecialchars($this->currentValue) . '" />
                                    <input type="hidden" name="fieldName" value="' . htmlspecialchars($this->fieldName) . '" />
                                    <input type="hidden" name="formName" value="' . htmlspecialchars($this->formName) . '" />
                                    <input type="hidden" name="md5ID" value="' . htmlspecialchars($this->md5ID) . '" />
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
                ';
                $content .= '<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAO-GVAAnVyHikuja71B17mWIQU-wZ0iE8&libraries=places" type="text/javascript"></script><div id="map_canvas" style="width: 100%; height: 570px;"></div>';

            $this->content .= '<h2>' . htmlspecialchars($this->getLanguageService()->getLL('geoselector_title')) . '</h2>';
            $this->content .= $content;
        }
    }

    /**
     * Returns the sourcecode to the browser
     *
     * @return void
     * @deprecated since TYPO3 CMS 7, will be removed in TYPO3 CMS 8, use mainAction() instead
     */
    public function printContent()
    {
        GeneralUtility::logDeprecatedFunction();
        $this->content .= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);
        echo $this->content;
    }

    /**
    * Returns the latitude and longitude values from current data
    *
    *
    */
    public function getGeoDat($geoCurrentData) 
    {
        $currData = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $geoCurrentData );        
        if( is_array($currData) && ($currData[0] && $currData[1]) ) {
            $returnArray =  array( 'lat' => $currData[0], 'lng' => $currData[1] );
        } else {
            $returnArray = array( 'lat' => 0, 'lng' => 0 );
        }
        return $returnArray;
    }

    /**
     * Returns a frameset so our JavaScript Reference isn't lost
     * Took some brains to figure this one out ;-)
     * If Peter wouldn't have been I would've gone insane...
     */
    public function frameSet()
    {   
        $this->getDocumentTemplate()->JScode = $this->getDocumentTemplate()->wrapScriptTags('
                if (!window.opener) {
                    alert("ERROR: Sorry, no link to main window... Closing");
                    close();
                }
        ');
        $this->getDocumentTemplate()->startPage($this->getLanguageService()->getLL('geoselector_title'));

        // URL for the inner main frame:
        $url = BackendUtility::getModuleUrl(
            'wizard_GooglePicker',
            [
                'showPicker' => 1,
                'currentValue' => $this->wizardParameters['currentValue'],
                'fieldName' => $this->wizardParameters['itemName'],
                'formName' => $this->wizardParameters['formName'],
                'exampleImg' => $this->wizardParameters['exampleImg'],
                'md5ID' => $this->wizardParameters['md5ID'],
                'fieldChangeFunc' => serialize($this->wizardParameters['fieldChangeFunc']),
                'fieldChangeFuncHash' => $this->wizardParameters['fieldChangeFuncHash'],
            ]
        );
        $this->content = $this->getPageRenderer()->render(PageRenderer::PART_HEADER) . '
			<frameset rows="*,1" framespacing="0" frameborder="0" border="0">
				<frame name="content" src="' . htmlspecialchars($url) . '" marginwidth="0" marginheight="0" frameborder="0" scrolling="auto" noresize="noresize" />
			</frameset>
		';
    }

    
    /**
     * Determines whether submitted field change functions are valid
     * and are coming from the system and not from an external abuse.
     *
     * @return bool Whether the submitted field change functions are valid
     */
    protected function areFieldChangeFunctionsValid()
    {
        return $this->fieldChangeFunc && $this->fieldChangeFuncHash && $this->fieldChangeFuncHash === GeneralUtility::hmac($this->fieldChangeFunc);
    }

    /**
     * @return PageRenderer
     */
    protected function getPageRenderer()
    {
        return GeneralUtility::makeInstance(PageRenderer::class);
    }
}

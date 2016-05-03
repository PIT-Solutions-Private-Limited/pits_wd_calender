<?php
if (!preg_match('/typo3conf/', $_SERVER['PHP_SELF'])) {
  $BACK_PATH = '../../';
  define('TYPO3_MOD_PATH', 'ext/pits_wd_calender/');
} else {
  $BACK_PATH = '../../../typo3/';
  define('TYPO3_MOD_PATH', '../typo3conf/ext/pits_wd_calender/');
}

include_once ($BACK_PATH.'init.php');
$LANG->includeLLFile('EXT:pits_wd_calender/locallang_wizard.xml');

class ql_googlemap_selector {
    
    public $P;
    public $currGeoDat;
    public $geoValue;
    public $fieldChangeFunc;
    public $fieldName;
    public $formName;
    public $md5ID;
    public $conf;
    public $apiKey;
    public $doc;
    public $content;

    public function loadTS($pageUid) {
        $sysPageObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_pageSelect');
        $rootLine = $sysPageObj->getRootLine($pageUid);
        $TSObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_tsparser_ext');
        $TSObj->tt_track = 0;
        $TSObj->init();
        $TSObj->runThroughTemplates($rootLine);
        $TSObj->generateConfig();
        //$this->conf = $TSObj->setup['plugin.']['tx_pitgooglemap_pi1.'];
    }

    public function getApiKey($conf) {
        $currHost = $this->getHost( $_SERVER['HTTP_HOST'] );
        $apiArr = array();
        $apiConfArr = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $conf['apiKey'] );

        if( is_array($apiArr) ) {
            foreach ($apiConfArr as $key => $val) {
                $apiEntry = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('|', $val );
                $apiArr[$this->getHost($apiEntry[0])] = $apiEntry[1];
            }
            $this->apiKey = $apiArr[$currHost];
        }
    }

    public function getHost($host) {
        $currHost = str_replace( 'http://', '', $host );
        $currHost = str_replace( 'https://', '', $currHost );

        return $currHost;
    }

    public function getGeoDat($deoData) {
        $currData = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(';', $deoData );
        if($this->conf['defaultLatLng'] && !$deoData ) {
            $currData = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('|', $this->conf['defaultLatLng']);
        }
        if( is_array($currData) && ($currData[0] && $currData[1]) ) {
            $returnArray =  array( 'lat' => $currData[0], 'lng' => $currData[1] );
        } else {
            $returnArray = array( 'lat' => 0, 'lng' => 0 );
        }
        return $returnArray;
    }

    public  function main() {
        global $LANG, $BACK_PATH;

        // Setting GET vars (used in frameset script):
        $this->P = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('P' , 1);
        $this->loadTS($this->P['pid']);

        // Set API Key
                //function desabled or can be enabled when need of upgrardtion
         //$this->getApiKey($this->conf);
                 $this->apiKey = 'AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM';

        // Set start GEO data
                //function desabled or can be enabled when need of upgrardtion
        //$this->currGeoDat = $this->getGeoDat( $this->P["currentValue"] );
                 $this->currGeoDat['lat'] = '9.664';
                 $this->currGeoDat['lng'] = '76.470';
        // Setting GET vars:
        $this->geoValue = $this->P['geoValue'];
        $this->fieldChangeFunc = $this->P['fieldChangeFunc'];
        $this->fieldName = $this->P['itemName'];
        $this->formName = $this->P['formName'];
        $this->md5ID = $this->P['md5ID'];
        $this->exampleImg = $this->P['exampleImg'];

        // Setting field-change functions:
        $fieldChangeFuncArr = $this->fieldChangeFunc;
        $update = '';
        if (is_array($fieldChangeFuncArr))  {
            unset($fieldChangeFuncArr['alert']);
            foreach($fieldChangeFuncArr as $v)  {
                $update.= '
                parent.opener.'.$v;
            }
        }

        // Initialize document object:
        $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('template');
        $this->doc->backPath = $BACK_PATH;
        $this->doc->docType = 'xhtml_trans';
        $this->doc->bodyTagAdditions = ' onload="initialize()" onunload="GUnload()"';
        $this->doc->JScodeLibArray = array('<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=true" type="text/javascript"></script>');
        $this->doc->JScode = $this->doc->wrapScriptTags('

            function checkReference()   {   //
                if (parent.opener && parent.opener.document && parent.opener.document.'.$this->P['formName'].' && parent.opener.document.'.$this->P['formName'].'["'.$this->P['itemName'].'"])  {
                    return parent.opener.document.'.$this->P['formName'].'["'.$this->P['itemName'].'"];
                } else {
                    close();
                }
            }
            function updateValueInMainForm(input)   {   //
                var field = checkReference();
                if (field)  {
                    field.value = input;
                        window.opener.'.$this->P['fieldChangeFunc']['typo3form.fieldGet'].'
                        window.opener.'.$this->P['fieldChangeFunc']['TBE_EDITOR_fieldChanged'].'
                }
            }
            function setValue(input)    {   //
                updateValueInMainForm(input);
                close();
                return false;
            }

            var map = null;
            var geocoder = null;

            function initialize() {
                        var map = new google.maps.Map(document.getElementById("map_canvas"), {
                             zoom: 5,
                             center: new google.maps.LatLng("9.664","76.470"),
                             mapTypeId: google.maps.MapTypeId.ROADMAP,
                             });
                             marker = new google.maps.Marker({
                               position: new google.maps.LatLng("9.664","76.470"),
                               draggable: true,
                               map: map,
                            });
                           google.maps.event.addListener(marker, "dragstart", function(evt){
                    map.closeInfoWindow();
                });
                           var infowindow = new google.maps.InfoWindow({
                                  content: "Latitude:9.664<br>Longitude:76.470"
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
            }   
                    function getLocationName(evt_lat,evt_long){
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
        
        // Start page:
        $this->content.=$this->doc->startPage($LANG->getLL('geoselector_title'));

        $content = '
        <div id="formContainer">
            <table id="formTable" cellpadding="0" cellspacing="0">
                <tr>
                    <td><form id="addressSearch" action="#" onsubmit="showAddress(this.address.value); return false"><strong>'.$LANG->getLL('search_address').':</strong> <input type="text" size="35" name="address" value="" /><input type="submit" value="'.$LANG->getLL('search_address_ok').'" /></form></td>
                    <td width="90" align="right"><form action="" name="lgeodatform" id="lgeodatform"><input type="hidden" name="lgeodat" value="popupVAl" /><input value="'.$LANG->getLL('set_data').'" onclick="setValue(document.lgeodatform.lgeodat.value);" type="submit"></form></td>
                </tr>
            </table>
        </div>
        ';
        $content .= '<div id="map_canvas" style="width: 100%; height: 570px;"></div>';

        // If the save/close button is clicked, then close:
        if(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('save_close')) {
            $content.=$this->doc->wrapScriptTags('
                setValue(\''.$this->geoValue.'\');
                parent.close();
            ');
        }

        // Output:
        $this->content.=$this->doc->section('' , $content, 0 , 1);
    }

    public function printContent()  {
        $this->content.= $this->doc->endPage();
        $this->content = $this->doc->insertStylesAndJS($this->content);
        echo $this->content;
    }
}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pits_wd_calender/geo_selector.php'])   {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pits_wd_calender/geo_selector.php']);
}

// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('ql_googlemap_selector');
$SOBE->main();
$SOBE->printContent();
?>

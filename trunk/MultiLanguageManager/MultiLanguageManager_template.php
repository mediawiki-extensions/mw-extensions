<?php
/*
 * Created on 27 mai 2007
 *
 * http://www.art122-5.net
 * Marc Despland 
 * 
 * This is the default template for this extension
 * All the skin extends the MultiLanguageManager_template
 * 
 * Documentation about installation, how to use or modification could be found at :
 * http://www.art122-5.net/index.php/MultiLanguageManager_Extension
 *
 * Copyright (C) 2007  Marc Despland
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA
 */
 
 /**
  * sample css
.popup{
	text-decoration: underline;
	position: relative;
}
		
.popup:hover{
	background-color: transparent;
	text-decoration: none;
}
	

		
div.cadre_popup{
	visibility: hidden;
	position: absolute;
	z-index: 10;
	text-decoration: none;
	top: 0px;
	left: 0px;
	width:200px;
	background-color: white;
	color: black;
	border-collapse: collapse;
	border: 1px solid #aaa;
	padding: 0 .8em .3em .5em;
}

#language_popup {
	//top: #popupanchor.top;
	z-index: 100;
}
		
span:hover.popup div.cadre_popup{
	top: 0px;
	left: 0px;
	visibility: visible;
}
		
span .popup div:hover.cadre_popup{
	visibility: visible;
}

.languageimage	{
	border:0;
	text-decoration: none;
}
a.language {
	text-decoration: none;
}

a.popup {
	text-decoration: none;
}
span.popup {
	text-decoration: none;
}
.languageError {
	color:#EE1111;
}

  */
 
 
 require_once( dirname(__FILE__) . "/MultiLanguageManager_setup.php" );
 class MultiLanguageManager_template {
 	protected $class_icon;
 	protected $class_image;
 	protected $class_popup;
 	protected $class_cadre_popup;
 	protected $class_direct_link;
 	protected $class_headline;
 	protected $class_language_info;
 	protected $class_result;
 	protected $class_error;
 	
 	protected $id_cadre_popup;

	protected $availableLanguageFlag;
	protected $vailableLanguageFlagIcon;
	protected $imageDirectory;
	protected $imageUrl;
	

 	function __construct() {
 		global $wgScriptPath;
 		$this->class_icon="languageimage";
 		$this->class_image="languageimage";
 		$this->class_popup="popup";
 		$this->class_cadre_popup="cadre_popup";
 		$this->class_direct_link="language";
 		$this->class_headline="mw-headline";
 		$this->class_language_info="displayLanguageInfo";
 		$this->class_result="languageResult";
 		$this->class_error="languageError";
 	
 		$this->id_cadre_popup="language_popup";

		$this->availableLanguageFlag=array(
	 		'en' => 'en.png',
	 		'fr' => 'fr.png',
	 		'de' => 'de.png',
	 		'it' => 'it.png',
	 		'nl' => 'ne.png',
			'ro' => 'ro.png',
			'ar' => 'ar.png'
			);
		$this->availableLanguageFlagIcon=array(
	 		'en' => 'en.png',
	 		'fr' => 'fr.png',
	 		'de' => 'de.png',
	 		'it' => 'it.png',
	 		'nl' => 'ne.png',
			'ro' => 'ro.png',
			'ar' => 'ar.png');
		$this->imageDirectory=dirname(__FILE__). "/images/";
		$this->imageUrl= $wgScriptPath."/extensions/MultiLanguageManager/images/"; 		
 	}
 	public static function escapeAttribute($value) {
 		$replaceArray = array(
				'\'' => '\\\'',
				'\"' => '\\"');
		return strtr( $value, $replaceArray );
 	}
 	
	/**************************************************************************
	 * General functions
	 */
	
	/**
	 * Display a language icon
	 * @param languageCode The language code
	 */	
	public function  displayIcon($language_code) {
		global $mgAvailableLanguage;
		
		if (array_key_exists($language_code,$this->availableLanguageFlagIcon)) {
			return ("<img src='".$this->imageUrl.$this->availableLanguageFlagIcon[$language_code]."' class='".$this->class_icon."' alt='".wfMsg($mgAvailableLanguage[$language_code])."'/>");
		} else {
			return "";
		}
	}
	
	/**
	 * Display a fatal error
	 * @param error The error key 
	 */	
	public function  displayError($error) {
		global $wgOut;
		$wgOut->showFatalError( wfMsgHtml($error) );
		return "";
	}

	/**************************************************************************
	 * The Language Chooser
	 */

	/**
	 * Create the popup used with the language chooser
	 * @param visibleLinkText The visible part of the link
	 * @param contentLinkText The content of the popup
	 */
	public function displayLanguageLinkPopup($visibleLinkText,$contentLinkText) {
		$output="";
		$output.="<!--[if lt IE 7]><span class='".$this->class_popup."' onMouseOver=\"document.getElementById('".$this->id_cadre_popup."').style.visibility = 'visible';\"  onmouseout=\"document.getElementById('".$this->id_cadre_popup."').style.visibility = 'hidden';\"><![endif]-->";
		$output.="<span class='".$this->class_popup."' id='popup_anchor'>" . $visibleLinkText;
	    $output.="<!--[if lt IE 7]><span  onmouseout=\"document.getElementById('".$this->id_cadre_popup."').style.visibility = 'hidden';\"><![endif]-->";
		$output.="<div class='".$this->class_cadre_popup."' id='".$this->id_cadre_popup."' >";
	    $output.=$contentLinkText;
		$output.="</div>";
		$output.="<!--[if lt IE 7]></span><![endif]-->";
	    $output.="</span>";
		$output.="<!--[if lt IE 7]></span><![endif]-->";
		return $output;
	}
	/**
	 * Create the link to the tranlated page for the language chooser
	 * @param displayLanguage The visible part of the link
	 * @param translationId the Id of the targeted article
	 */
	public function displayDirectPageLink($displayLanguage,$translationId) {
		$title= Title::newFromId($translationId);
		if (get_class($title)!="Title") return "";
		$output="<a class='".$this->class_direct_link."' href='".$title->getLocalUrl()."'>".$displayLanguage."</a>";
		return $output;
	}
	
	/**
	 * Create the visual part of a language element used with teh language chooser
	 * @param language_code the language code
	 */
	public function displayLanguageElement($language_code) {
		global $mgAvailableLanguage;

		if (array_key_exists($language_code,$this->availableLanguageFlag)) {
				return ("<img src='".$this->imageUrl.$this->availableLanguageFlag[$language_code]."' class='".$this->class_image."' alt='".wfMsg($mgAvailableLanguage[$language_code])."'/>" . wfMsg($mgAvailableLanguage[$language_code]));
		} else {
			if (array_key_exists($language_code,$mgAvailableLanguage)) {
				return wfMsg($mgAvailableLanguage[$language_code]);
			} else {
				return "";
			}
		}	
	}
		
	/**
	 * Create the list of available translation (menu), used by the language chooser
	 * @param languageCode The language code
	 * @param translationList The list of article id
	 */	
	public function displayListTranslation($languageCode,$translationList) {
		$displayIcon=$this->displayIcon($languageCode);
		$output="<ul>";
		foreach($translationList as $id) {
			$title= Title::newFromId($id);
			if ($title->exists()) {
				$output .="<li><a href=\"".$title->getLocalUrl()."\" target='_parent' >".$displayIcon." ".$title->getText()."</a></li>";
			} 
		}
		$output.="</ul>";
		return ($output);
	}
	/**************************************************************************
	 * The detail Language page
	 */
	
	/**
	 * Display the choose language section of the detail language page
	 * @param language the language code of the page
	 * @param targetid The article id
	 * @param allowed A boolean indicates if the use is allowed to change the language information
	 * @param hasDep A boolean indicates that th page is the translation or is translated by others  
	 */	
	public function displayChooseLanguage($language,$targetid ,$allowed,$hasDep) {
		global $mgAvailableLanguage;
		global $wgContLanguageCode;
		global $wgRequest;
		$title=Title::newFromId($targetid);
		$output="<h1><span class=\"".$this->class_headline."\">".wfMsgHtml('multilanguagemanager_languageofthepage'). " : ".$title->getText(). "</span></h1>\n";
		
		if (($allowed) && (! $hasDep)) {
			$cible=Title::newFromText($wgRequest->getVal( 'cible' ));
			$output.="<p>";
			//form
			$output.="<form>";
			$output.="<input type=hidden name='title' value=\"".$wgRequest->getVal( 'title' )."\">\n";
			$output.="<input type=hidden name='cible' value=\"".htmlspecialchars($wgRequest->getVal( 'cible' ))."\">\n";
			$output.="<input type=hidden name='source' value=\"".$targetid."\">\n";
			$output.="<input type=hidden name='mode' value='setLanguage'>\n";
			$output.="<p>";
			foreach($mgAvailableLanguage as $key => $value) {
				$output.="<input type=\"radio\" name=\"lang\" value=\"".$key."\";";
				if ($key==$language) $output.=" checked";
				$output.=">".$this->displayIcon($key)." ".wfMsg($value) ."</input><br>\n";
			}
			$output.="</p>";
			$output.="<input type='submit'value='".wfMsgHtml('multilanguagemanager_changelanguage')."'/>\n";
			$output.="</form>";
			//close
			$output.="</p>\n";
		} else {
			$output.="<p>";
			$output.=wfMsgHtml('multilanguagemanager_articlelanguage').$this->displayIcon($language)." ".wfMsg($mgAvailableLanguage[$language]) ."<br>\n";
			$output.="</p>";
			if ($allowed) {
				$output.="<p>".wfMsgHtml('multilanguagemanager_removedependence')."</p>\n";
			} else {
				$output.="<p>".wfMsgHtml('multilanguagemanager_notallowed')."</p>\n";
			}
		}
		return $output;
	}
	/**
	 * Display the "It is translated by" section of the detail language page
	 * @param list the list of article id
	 * @param pageid The article id
	 * @param allowed A boolean indicates if the use is allowed to change the language information
	 */	
	public function displayItIsTranslatedBy($list,$pageid ,$allowed) {
		global $wgRequest;
		$output="<h1><span class=\"".$this->class_headline."\">".wfMsgHtml('multilanguagemanager_itistranlatedby'). " : ". "</span></h1>\n";
		$output.="<p><table class=\"".$this->class_language_info."\">";
		foreach($list as $element) {
			$translate=Title::newFromId($element['pageid']);
			if ((get_class($translate)=="Title") && ($translate->exists())) {
				$output.="<tr><td>".$this->displayIcon($element['lang'])."</td>";
				$output.="<td><a href=\"".$translate->getLocalUrl()."\" title=\"".$translate->getText()."\">".$translate->getText()."</a></td>";
				if ($allowed) {
					//form
					$cible=Title::newFromText($wgRequest->getVal( 'cible' ));
					$output.="<td><form>";
					$output.="<input type=hidden name='title' value=\"".$wgRequest->getVal( 'title' )."\">\n";
					$output.="<input type=hidden name='cible' value=\"".htmlspecialchars($wgRequest->getVal( 'cible' ))."\">\n";
					$output.="<input type=hidden name='source' value=\"".$pageid."\">";
					$output.="<input type=hidden name='translate' value='".$element['pageid']."'>";
					$output.="<input type=hidden name='mode' value='deleteTranslation'>";
					$output.="<input type='submit'value='".wfMsgHtml('delete')."'/>";
					$output.="</form></td>";
				}
				$output.="</tr>\n";
			} 
		}
		$output.="</table></p>\n";
		if ($allowed) {	
			$cible=Title::newFromText($wgRequest->getVal( 'cible' ));
			$output.="<p>";
			$output.="<form>";
			$output.="<input type=hidden name='title' value='".$wgRequest->getVal( 'title' )."'>\n";
			$output.="<input type=hidden name='cible' value=\"".htmlspecialchars($wgRequest->getVal( 'cible' ))."\">\n";
			$output.="<input type=hidden name='source' value='".$pageid."'>";
			$output.="<input type=hidden name='translate' value=''>";
			$output.="<input type=hidden name='mode' value='addTranslation'>";
			$output.=wfMsgHtml('multilanguagemanager_articletitle')." : <input type=text name='articletitle' value='' size='50'>";
			$output.="<input type='submit'value='".wfMsgHtml('multilanguagemanager_add')."'/>";
			$output.="</form>";
			$output.="</p>\n";
		}
		return $output;
	} 
	/**
	 * Display the "It translates" section of the detail language page
	 * @param list the list of article id
	 * @param pageid The article id
	 * @param allowed A boolean indicates if the use is allowed to change the language information
	 */	
	public function displayItTranslates($list,$pageid ,$allowed) {
		global $wgContLanguageCode;
		global $wgRequest;
		$output="<h1><span class=\"".$this->class_headline."\">".wfMsgHtml('multilanguagemanager_ittranlates'). " : ". "</span></h1>\n";
		$output.="<p><table class=\"".$this->class_language_info."\">\n";
		foreach($list as $element) {
			$translate=Title::newFromId($element);
			if ((get_class($translate)=="Title") && ($translate->exists())) {
				$output.="<tr><td>".$this->displayIcon($wgContLanguageCode)."</td>";
				$output.="<td><a href=\"".$translate->getLocalUrl()."\" title=\"".$translate->getText()."\">".$translate->getText()."</a></td>";
				if ($allowed) {
					//form
					$cible=Title::newFromText($wgRequest->getVal( 'cible' ));
					$output.="<td><form>";
					$output.="<input type=hidden name='title' value='".$wgRequest->getVal( 'title' )."'>\n";
					$output.="<input type=hidden name='cible' value=\"".htmlspecialchars($wgRequest->getVal( 'cible' ))."\">\n";
					$output.="<input type=hidden name='source' value='".$element."'>";
					$output.="<input type=hidden name='translate' value='".$pageid."'>";
					$output.="<input type=hidden name='mode' value='deleteTranslation'>";
					$output.="<input type='submit'value='".wfMsgHtml('delete')."'/>";
					$output.="</form></td>";
				}
				$output.="</tr>\n";
			} 
		}
		$output.="</table></p>\n";
		if ($allowed) {	
			$cible=Title::newFromText($wgRequest->getVal( 'cible' ));
			$output.="<p>";
			$output.="<form>";
			$output.="<input type=hidden name='title' value='".$wgRequest->getVal( 'title' )."'>\n";
			$output.="<input type=hidden name='cible' value=\"".htmlspecialchars($wgRequest->getVal( 'cible' ))."\">\n";
			$output.="<input type=hidden name='source' value=''>";
			$output.="<input type=hidden name='translate' value='".$pageid."'>";
			$output.="<input type=hidden name='mode' value='addTranslation'>";
			$output.=wfMsgHtml('multilanguagemanager_articletitle')." : <input type=text name='articletitle' value='' size='50'>";
			$output.="<input type='submit'value='".wfMsgHtml('multilanguagemanager_add')."'/>";
			$output.="</form>";
			$output.="</p>\n";
		}
		return $output;
	}
	
	/**
	 * Display the language policy section of the detail language page
	 */	
	public function displayLanguagePolicy() {
		global $mgAvailableLanguage;
		global $wgContLanguageCode;
		$output="<h1><span class=\"".$this->class_headline."\">".wfMsgHtml('multilanguagemanager_languagepolicytitle'). " : ". "</span></h1>\n";
		$output.="<p>".wfMsgHtml('multilanguagemanager_languagepolicydefault'). wfMsg($mgAvailableLanguage[$wgContLanguageCode]) ."</p>\n";
		$output.="<p>".wfMsgHtml('multilanguagemanager_languagepolicysupported')."<ul>";
		foreach($mgAvailableLanguage as $code => $value) {
			if ($code!=$wgContLanguageCode) {
				$output.="<li>".$this->displayIcon($code)."&nbsp;".wfMsg($value)."</li>";
			}
		} 
		$output.="</ul></p>\n";
		$output.="<p>".wfMsgHtml('multilanguagemanager_languagepolicynotice') . "</p>\n";
		return $output;
	}
	/**
	 * Display the result section of the detail language page
	 */	
	public function displayExecuteResult($data) {
		$output="<h1><span class=\"".$this->class_headline."\">".wfMsgHtml('multilanguagemanager_success'). " : ". "</span></h1>\n";
		$output.="<p class=\"".$this->class_result."\">".wfMsgHtml($data)."</p>";	
		return($output);
	}
	/**
	 * Display the error section of the detail language page
	 */	
	public function displayExecuteError($data) {
		$output="<h1><span class=\"".$this->class_headline."\">".wfMsgHtml('multilanguagemanager_error'). " : ". "</span></h1>\n";
		$output.="<p class=\"".$this->class_error."\">".wfMsgHtml($data)."</p>";	
		return($output);
	}
}
 
?>
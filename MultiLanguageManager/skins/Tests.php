<?php
/*
 * Created on 27 mai 2007
 *
 * http://www.art122-5.net
 * Marc Despland 
 * 
 * A special skin used for testing
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
 
   class MultiLanguageManager_displaytest extends MultiLanguageManager_template {
 	
 	function __construct() {
 		parent::__construct();
 	}
	//This function create a link with th popup div
	public function displayLanguageLinkPopup($visibleLinkText,$contentLinkTest) {
		return "displayLanguageLinkPopup(" . $visibleLinkText.",".$contentLinkTest.")";
	}
	
	public function displayLanguageElement($language_code) {
		return "displayLanguageElement(".$language_code.")";
	}
	
	
	public function displayDirectPageLink($displayLanguage,$translationId) {
		return "displayDirectPageLink(".$displayLanguage.",".$translationId.")";
	}
	
	public function displayListTranslation($languageCode,$translationList) {
		$output ="displayListTranslation(".$languageCode.",(";
		foreach($translationList as $key) $output.=$key. " ";
		return $output."))";
	}
	
	public function  displayIcon($languageCode) {
		return "displayIcon(".$languageCode.")";
	}
	
	public function  displayError($error) {
		return "displayError(".$error.")";
	}
	public function displayChooseLanguage($language,$targetid ,$allowed,$hasDep) {
		
		return "displayChooseLanguage(".$language.",".$targetid.",".$this->strbool($allowed).",".$this->strbool($hasDep).")";
	}
	public function displayItIsTranslatedBy($tbs,$pageid ,$allowed) {
		return "displayItIsTranslatedBy(result,".$pageid .",".$this->strbool($allowed).")";
	} 
	public function displayItTranslates($tbs,$pageid ,$allowed) {
		return "displayItTranslates(result,".$pageid .",".$this->strbool($allowed).")";	
	}
	
	public function displayLanguagePolicy() {
		return "displayLanguagePolicy()";
	}
	
	private function strbool($b) {
		if ($b) return "TRUE";
		return "FALSE";
	}
	
	public function displayExecuteResult($data) {	
		return($data);
	}
	public function displayExecuteError($data) {
		return($data);
	}
	
}
 
?>

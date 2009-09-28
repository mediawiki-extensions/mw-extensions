<?php
/*
 * Created on 27 mai 2007
 *
 * http://www.art122-5.net
 * Marc Despland 
 * 
 * This is the main controller of the multi-language system. 
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
 *
 */

 		
/**
 * Try to load the curent skin for this extension
 */
wfSuppressWarnings(); 
include_once dirname(__FILE__).MultiLanguageManager_controller::MultiLanguageManager_getSkinFile();
wfRestoreWarnings();


/**
 * Definition of the controller class
 */	
class MultiLanguageManager_controller {
	private $dbw;
	private $dbr;
	private $db_page_translation,$db_page, $db_page_language;
	/**
	 * The rendering object (skin)
	 */
	private $display=NULL;
	/**
	 * Constructor
	 */
	function __construct() {
		global $wgUser;
		$this->dbw = wfGetDB( DB_MASTER );
		$this->dbr = wfGetDB( DB_SLAVE );
		//$this->db_page=$this->dbw->tableName('page');
		$this->db_page_translation=$this->dbr->tableName('page_translation');
		$this->db_page_language  = $this->dbr->tableName('page_language');
		if (class_exists("MultiLanguageManager_display")) {
			$this->display = new MultiLanguageManager_display();
		} else {
			$this->display = new MultiLanguageManager_template();
		}	
	}		
	/**
	 * This function is used to change the display object used for the rendering
	 * It is only used to the tests this class
	 */
	public function setDisplay($viewTemplate) {
		if (is_subclass_of($viewTemplate,"MultiLanguageManager_template")) {
			$this->display=$viewTemplate;
		}
	}
	
	/**************************************************************************
	 * The public functions
	 */
	/**
	 * Execute the set language request
	 * @param source The id of the article
	 * @param lang The language code
	 * @return The HTML response
	 */
 	public function executeSetLanguageRequest($source, $lang){
 		if (!is_string($lang)) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if (! $this->checkLanguage($lang)) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if (!is_numeric($source)) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		$pageTitle=Title::newFromId($source);
 		if (get_class($pageTitle)!="Title") return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if (!$pageTitle->exists()) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		$oldLanguage=$this->getPageLanguage($source);
 		if ($oldLanguage==$lang) return $this->display->displayExecuteResult('multilanguagemanager_languageisset');
 		$this->removeLanguageSetting($source);
 		$this->addLanguageSetting($source,$lang);	
 		return $this->display->displayExecuteResult('multilanguagemanager_languageisset');		
 	}
 	
	/**
	 * Execute the delete translation request
	 * This function only delete the information "translate" is a translation of "source". It didn't 
	 * delete any article.
	 * @param source The id of the source article (default language : $wgContLanguageCode)
	 * @param translate The id of the translate article (not default language)
	 * @return The HTML response
	 */
 	public function executeDeleteTranslationRequest($source, $translate) {
 		global $wgContLanguageCode;
 		if (!is_numeric($source)) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if (!is_numeric($translate)) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
  		$this->removeTranslation($source,$translate);
  		return $this->display->displayExecuteResult('multilanguagemanager_translationdeleted');
 	}

	/**
	 * Execute the add translation request
	 * This function only add the information "translate" is a translation of "source". It didn't 
	 * create any article.
	 * 
	 * If you want to add "source" is transleted by "title" call executeAddTranslationRequest(  $source, "" , $title)
	 * If you want to add "translate" translets  "title" call executeAddTranslationRequest(  "", $translate , $title)
	 * 
	 * @param source The id of the source article (default language : $wgContLanguageCode)
	 * @param translate The id of the translate article (not default language)
	 * @param title The title text of the target article 
	 * @return The HTML response
	 */
 	public function executeAddTranslationRequest(  $source, $translate, $title) {
 		global $wgContLanguageCode;
 		
 		if ((!is_numeric($source)) && (!is_numeric($translate))) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if ((is_numeric($source)) && (is_numeric($translate))) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if (is_numeric($source)) {
 			$pageid=$source;
 			$target="translate";
 		} else {
 			$pageid=$translate;
 			$target="source";		
 		}
 		$pageTitle=Title::newFromId($pageid);
 		if (get_class($pageTitle)!="Title") return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		if (!$pageTitle->exists()) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
  		
 		if (!is_string($title)) return $this->display->displayExecuteError('multilanguagemanager_invalidrequest');
 		$targetTitle=Title::newFromText($title);
 		if (get_class($targetTitle)!="Title") return $this->display->displayExecuteError('multilanguagemanager_invalidtitle');
 		if (!$targetTitle->exists()) return $this->display->displayExecuteError('multilanguagemanager_invalidtitle');
		//so now we have a good target article title
		// but what about the language ?
		//we don' have to check it now, addTranslation do it

		if ($target=="translate") {
			$error=$this->addTranslation($pageid,$targetTitle->getArticleID());
		} else {
			$error=$this->addTranslation($targetTitle->getArticleID(),$pageid);
		}
		switch ($error) {
			case -1:
				return $this->display->displayExecuteError('multilanguagemanager_sourcenotdefault');
			break;
			case -2:
				return $this->display->displayExecuteError('multilanguagemanager_translatedefault');
			break;
		}		
		return $this->display->displayExecuteResult('multilanguagemanager_translationadded');
 	}

	/**
	 * Execute the remove language settings request
	 * This function remove all the available language informations for pageId
	 *    - The language of pageid
	 *    - The pageid "is the translate of" data
	 *    -  The pageid "is translated by" data
	 * 
	 * @param pageId The id of the article 
	 */
	public function executeRemoveLanguageSettingsRequest($pageId) {
		if (!is_numeric($pageId)) return;
		$this->removeLanguageSetting($pageId);
	}

	/**
	 * Check that the lang_code is a valide language
	 * @param lang_code The language code
	 * @return true if the language is valid
	 */
	public function checkLanguage($lang_code) {
		global $mgAvailableLanguage;
		if (!is_string($lang_code)) return FALSE;
		return array_key_exists($lang_code,$mgAvailableLanguage);	
	}
	
	/**
	 * Returns the language code associated with page pageid
	 * If an error occurs or if this page can't be found, it returns the default 
	 * language of the wiki
	 * @param pageid The Id of the article
	 * @return the language code for the article
	 */
	public function getPageLanguage($pageid) {
 	  	global $wgContLanguageCode;
 	  	$lang=$wgContLanguageCode;
 	  	if (is_numeric($pageid)) {
	 	  	// we search the page id in the translate field
			$SQL="SELECT lang FROM ! WHERE page_id=?";
			$tbs=$this->dbr->safeQuery($SQL,$this->db_page_language,$pageid);
			if ($this->dbr->numRows($tbs)==1) {
				//we find the page
				//and the winner is ...
				if ($row = $this->dbr->fetchObject($tbs)) {
					if ($this->checkLanguage($row->lang)) {
						//Language is a vaild code, return it!
						$lang=$row->lang;
					}
				} 
			}
		}
		return($lang);
	}


	/**
	 * Returns an array with the article id that translate 
	 * or is the translation of teh given article in the given language
	 * @param myPageTitle The Title object of the article
	 * @param searchedLanguage The searched language
	 * @return The list of the article id
	 */
	public function availableTranslation($myPageTitle,$searchedLanguage) {
		global $wgContLanguageCode;
		//So we control the parameters
		if (get_class($myPageTitle)!="Title") return array();
		if (!$myPageTitle->exists()) return array();
		$sourcePageId=$myPageTitle->getArticleID();
		$sourceLanguage=$this->getPageLanguage($sourcePageId);
		if (($sourceLanguage==$searchedLanguage) || (!$this->checkLanguage($searchedLanguage))) return array();
		
				
		if ($searchedLanguage==$wgContLanguageCode) {
			//The search page is in the default language, we have to select the page 
			//witch the translate field is pageid
			$SQL="SELECT source page FROM ! WHERE translate=? ORDER BY page";
			$tbs=$this->dbr->safeQuery($SQL,$this->db_page_translation,$sourcePageId);
		} else {
			if ($sourceLanguage==$wgContLanguageCode) {
				//The source page is in the default language
				//We have to search a translate page in the good language
				$SQL="SELECT pt.translate page FROM ! pt INNER JOIN ! pl ON pt.translate=pl.page_id WHERE pt.source=? AND pl.lang=?  ORDER BY page";
			    $tbs=$this->dbr->safeQuery($SQL,$this->db_page_translation, $this->db_page_language,$sourcePageId,$searchedLanguage);	
			} else {
				//The source page isn't in the default language and the searched page too
				//Those syntaxe should work on Mysql and Oracle ... for the other ... i hope
				//alias sp for source page, cp for cible page
				$SQL="SELECT cp.translate page FROM ! sp INNER JOIN ! cp ON sp.source=cp.source INNER JOIN ! pl ON cp.translate=pl.page_id WHERE sp.translate=? AND pl.lang=?  ORDER BY page";
				$tbs=$this->dbr->safeQuery($SQL,$this->db_page_translation,$this->db_page_translation, $this->db_page_language,$sourcePageId,$searchedLanguage);
			}
		}
		//so now all the page we search are in the $tbs
		$listPage=array();
		$i=0;
		while ($row = $this->dbr->fetchObject($tbs)) {
			//is this page exists ?
			$title=Title::newFromId($row->page);
			if ((get_class($title)=="Title") && ($title->exists())) {
				$listPage[$i]=$row->page;
				$i++;
			} else {
				//We delete it from the language database !
				$SQL="DELETE FROM ! WHERE translate=? or source=?";
				$this->dbw->safeQuery($SQL,$this->db_page_translation,$row->page,$row->page);
				$SQL="DELETE FROM ! WHERE page_id=?";
				$this->dbw->safeQuery($SQL,$this->db_page_language,$row->page);
			}
		}
		//And we are sure that all those pages exists !!
		return($listPage);
	}


	/**********************************************************************************
	 * Manage how the different display elements are create.
	 * those functions are free of the presentaton
	 */
	 
	 
	/**
	 * Create the language chooser for the given article
	 * This is the user interface used to redirect the user to the avalable translation 
	 * @param myPageTitle The Title object of the article
	 * @param searchedLanguage The searched language
	 * @return The HTML code to insert in the MediaWiki template
	 */
	public function displaySelectLanguage($myPageTitle ) {
		global $mgAvailableLanguage;
		global $wgUser;
		global $wgContLang;
		$saveContLang=$wgContLang;
		MultiLanguageManager::loadMessages();
		if( get_class( $myPageTitle ) !="Title") return "";
		//so first we have to check that this page exists in an other language
 	  	//witch is the language of this page 
 	  	$myLanguage=$this->getPageLanguage($myPageTitle->getArticleID());
 	  	$output="";
 	  	foreach($mgAvailableLanguage as $key => $value) {
 	  		if ($key != $myLanguage) {
 	  			$translationList=$this->availableTranslation($myPageTitle,$key);
 	  			//We have the list of the available translation in the key language
 	  			//next step depends of the number of translation
 	  			$nbTranslation=count($translationList);	  			
 	  			$displayLanguage=$this->display->displayLanguageElement($key);
 	  			switch ($nbTranslation) {
 	  				case 0 :
 	  					//$output.=$this->display->displayLanguageLinkPopup($displayLanguage,wfMsgHtml('multilanguagemanager_notranslation',wfMsgHtml($value)));
						// why to show that we do not have a translation ?
 	  				break;
 	  				case 1 : 
 	  					//Only one translation avalaible, good job
 	  					//So in this case we go to the page without displayed anything 
 	  					$output.=$this->display->displayDirectPageLink($displayLanguage,$translationList[0]) . '<br>';
 	  				break;
 	  				default :
 	  					//Argh a lot of tranlation
 	  					//We display them in a normal list (like left menu)
 	  					$objectContent=$this->display->displayListTranslation($key,$translationList);
 	  					$output.=$this->display->displayLanguageLinkPopup($displayLanguage,$objectContent);
 	  				break;
 	  			}
 	  		}
 	  	}
 	  	$wgContLang=$saveContLang;
 	  	return $output;
	}
 	/**
	 * Create teh detail Language page content 
	 * @param specialTitlePageText The special title text for this page
	 * @return The HTML code to insert in the MediaWiki Special:MultiLanguageManager page
	 */
 	public function createDetailLanguagePage($pageTitleText) {
		global $wgContLanguageCode;
		global $wgUser;
		global $mgLanguagePermisionsKey;
		$output="";
		$output.=$this->display->displayLanguagePolicy();
		$allowed=FALSE;
		if (get_class($wgUser)=="User") {
			$allowed=$wgUser->isAllowed($mgLanguagePermisionsKey);
		}
		if ($pageTitleText!="") {
			$targetPage=Title::newFromText($pageTitleText);
			if (get_class($targetPage)!="Title") return $this->display->displayError("multilanguagemanager_articleinvalid");
			if (!$targetPage->exists()) return $this->display->displayError("multilanguagemanager_notfound");

			$language=$this->getPageLanguage($targetPage->getArticleID());
			$list=array();
			if ($language==$wgContLanguageCode) {
				//search the page that translate it
				$SQL="SELECT pl.page_id page_id, pl.lang lang FROM ! pl INNER JOIN ! pt ON pl.page_id=pt.translate WHERE pt.source=?";
			    $tbs=$this->dbr->safeQuery($SQL,$this->db_page_language,$this->db_page_translation, $targetPage->getArticleID());
			    $i=0;
			    while ($row = $this->dbr->fetchObject($tbs)) {
			    	$list[$i]=array('pageid'=>$row->page_id, 'lang' => $row->lang);
			    	$i++;
			    }
				
			} else {
				//display : this page is the translation of	
				$SQL="SELECT pt.source page_id FROM ! pt WHERE pt.translate=?";
			    $tbs=$this->dbr->safeQuery($SQL,$this->db_page_translation, $targetPage->getArticleID());	
			    $i=0;
			    while ($row = $this->dbr->fetchObject($tbs)) {
			    	$list[$i]=$row->page_id;
			    	$i++;
			    }
			}
			$output.=$this->display->displayChooseLanguage($language,$targetPage->getArticleID() ,$allowed, ($this->dbw->numRows($tbs)>0));
			if ($language==$wgContLanguageCode) {
				$output.=$this->display->displayItIsTranslatedBy($list,$targetPage->getArticleID() ,$allowed);
			} else {
				$output.=$this->display->displayItTranslates($list,$targetPage->getArticleID() ,$allowed);
			}
			
		}
		return($output);
	}
 	
	/**************************************************************************************
	 * Private function, only this class could call them, so their we know that their 
	 * parameters are valid, and we don't have to check them again
	 */
		
	/**
	 * This function update or add the language for the page
	 * @param pageid The id of the Article
	 * @param languageCode The language code
	 */
	private function addLanguageSetting($pageId,$languageCode) {
		global $wgContLanguageCode;
		if ($languageCode==$wgContLanguageCode) {
			//So we don't have to any information in database
			//For security we will deleted all the data for this page
			$SQL="DELETE FROM ! WHERE page_id=?";
			$tbs=$this->dbw->safeQuery($SQL,$this->db_page_language,$pageId);
			//now delete any translation where we are the translate page
			//a page in the default language can only be a source page
			$SQL="DELETE FROM ! WHERE translate=?";
			$tbs=$this->dbw->safeQuery($SQL,$this->db_page_translation,$pageId);
		} else {
			//So we are not in the default language so the pageid can't be 
			//a source of a translation
			$SQL="DELETE FROM ! WHERE source=?";
			$tbs=$this->dbw->safeQuery($SQL,$this->db_page_translation,$pageId);
			//So we try to add the page with insert ignore
			$SQL="INSERT IGNORE INTO ! (page_id, lang) VALUES (?, ?)";
			$tbs=$this->dbw->safeQuery($SQL,$this->db_page_language,$pageId,$languageCode);
			//We now check the number of affected row to is there is a problem
			if( $this->dbw->affectedRows() == 0 ) {
				$SQL="UPDATE ! SET lang=? WHERE page_id=?";
				$tbs=$this->dbw->safeQuery($SQL,$this->db_page_language,$languageCode,$pageId);
			}
		}
	}

	/**
	 * This function remove the language settings for the page
	 * @param pageid The id of the Article
	 */
	private function removeLanguageSetting($pageId) {
		global $wgContLanguageCode;	
		$SQL="DELETE FROM ! WHERE page_id=?";
		$tbs=$this->dbw->safeQuery($SQL,$this->db_page_language,$pageId);
		$SQL="DELETE FROM ! WHERE translate=? or source=?";
		$tbs=$this->dbw->safeQuery($SQL,$this->db_page_translation,$pageId,$pageId);
	}
	
	/**
	 * Defines that $translate is a translation of $source
	 * @param sourceid The source id  
	 * @param translateid The translate id 
	 * @return The error code : 
	 *     * 0  : OK
	 *     * -1 : source isn't in the default language
	 *     * -2 : translate is in the default language
	 */
	private function addTranslation($sourceid, $translateid) {
		
		//So we check that source is in the default language
		$SQL="SELECT lang page FROM ! WHERE page_id=?";
		$tbs=$this->dbw->safeQuery($SQL,$this->db_page_language,$sourceid);
		if ($this->dbw->numRows($tbs)>0) return -1; //source isn't in the default language'

		$SQL="SELECT lang page FROM ! WHERE page_id=?";
		$tbs=$this->dbw->safeQuery($SQL,$this->db_page_language,$translateid);
		if ($this->dbw->numRows($tbs)==0) return -2; //translate is in the default language

		//So now we can insert a row :)
		//If insert failed, the declaration already exists ... it doesn't matter
		$SQL="INSERT IGNORE INTO ! (source, translate) VALUES (?,?)";
		$tbs=$this->dbw->safeQuery($SQL,$this->db_page_translation,$sourceid,$translateid);
		return 0;
	}
	/**
	 * Cancel that $translate is a translation of $source
	 * @param sourceid The source id  
	 * @param translateid The translate id 
	 */	
	private function removeTranslation($sourceid, $translateid) {
		
		//So we check that source is in the default language
		$SQL="DELETE FROM ! WHERE source=? and translate=?";
		$tbs=$this->dbw->safeQuery($SQL,$this->db_page_translation,$sourceid,$translateid);
	}
	
	/**
	 * Decode the special page name and return the target page name.
	 * Special:MultiLanguageManager/Accueil => Accueil
	 * A media wiki function exists, but it doesn't do exactly the same thing.  
	 * @param specialTitleText The special page name 
	 */	
	private function getTargetPageName($specialTitleText) {
		$pageTitleText="";
		$i=strpos($specialTitleText,"/");
		if (($i!==FALSE) && ($i+1<strlen($specialTitleText))) {
			//There is a page name
			$pageTitleText=substr($specialTitleText,$i+1);
		}
		return ($pageTitleText);
	}
	
	
	/***************************************************************************************
	 * Static functions
	 */
	 
	/**
	 * Define the include file for the skin
	 */
	public static function MultiLanguageManager_getSkinFile() {
		global $wgUser,$wgDefaultSkin;
		$skinname=ucfirst($wgDefaultSkin);
		if (get_class($wgUser)=="User") {
			$skinClass=$wgUser->getSkin();
			if (is_subclass_of ($skinClass,"Skin")) {
				$skinname=ucfirst($skinClass->getSkinName());
			}
		}
		if ($skinname=="") $skinname=ucfirst($wgDefaultSkin);
		return (DIRECTORY_SEPARATOR."skins".DIRECTORY_SEPARATOR.$skinname.".php");
	}
	
	public static function setUILanguage($lang) {
		global $wgLanguageCode,$wgLang,$wgLangClass;
		global $mgVersion;
		if ($mgVersion<10800) {
			
			$wgLangClass = 'Language' . str_replace( '-', '_', ucfirst( $lang ) );
			$wgLang = setupLangObj( $wgLangClass );
			$wgLang->initEncoding();
			$wgLanguageCode = $wgLang->getCode();
		} else {
			$wgLang=Language::factory($lang);
			$wgLanguageCode = $wgLang->getCode();
			
		}
	}
	
}

?>
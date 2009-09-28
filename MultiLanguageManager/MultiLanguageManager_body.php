<?php
/*
 * Created on 27 mai 2007
 *
 * http://www.art122-5.net
 * Marc Despland 
 * 
 * This is the Special:MultiLanguageManager Page
 * 
 * Documentation about installation, how to use or modification could be found at :
 * http://www.art122-5.net/index.php/MultiLanguageManager_Extension
 *
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
if (!isset($mgVersion)) {
	$mgVersion=wikiEncodedVersion();
}
if ($mgVersion<10700) {
	if (!class_exists('SpecialPage')) {
		require_once dirname(__FILE__).'/../../includes/SpecialPage.php';
	}
}
class MultiLanguageManager extends SpecialPage {
	protected static $messagesLoaded = false;
	protected static $controller=NULL;

	/**
	 * The constructor
	 */	
	function MultiLanguageManager() {
		SpecialPage::SpecialPage("MultiLanguageManager");
	}
	/**
	 * For compatibility with version < 1.9.0
	 */
	static function escapeFragmentForURL( $fragment ) {
		global $mgVersion;
		if ($mgVersion<10900) {
			$fragment = str_replace( ' ', '_', $fragment );
			$fragment = urlencode( Sanitizer::decodeCharReferences( $fragment ) );
			$replaceArray = array(
				'%3A' => ':',
				'%2F' => '/',
				'%' => '.'
			
			);
			return strtr( $fragment, $replaceArray );
		} else {
			return Title::escapeFragmentForURL( $fragment );
		}
	}

	/**
	 * Add the language action to the list of content actions
	 */
	public static function addLanguageAction(&$content_actions) {
		global $wgTitle;
		if ($wgTitle->getArticleId()) {
			$specialTitle = Title::newFromText("Special:MultiLanguageManager");
			if (get_class($specialTitle)!="Title") return;
			$content_actions['language'] = array(
				'class' => false,
				'text' => wfMsg('multilanguagemanager_language'),
				'href' => $specialTitle->getLocalUrl("cible=".wfUrlencode( $wgTitle->getPrefixedDBkey()))
			);
		}
	}

	/**
	 * execute the special page
	 */
	public function execute( $par ) {
		global $wgUser, $wgOut;
		global $wgRequest;
		global $mgLanguagePermisionsKey;
		//needed with 1.6.x version 
		$wgOut->setArticleFlag(false);
		
		$mode=$wgRequest->getVal( 'mode' );
		$controller=new MultiLanguageManager_controller();
		if ($wgUser->isAllowed($mgLanguagePermisionsKey)) {
			switch($mode) {
				case "deleteTranslation":
					$source=$wgRequest->getVal( 'source' );
					$translate=$wgRequest->getVal( 'translate' );
					$wgOut->addHTML($controller->executeDeleteTranslationRequest($source, $translate));				
				break;
				case "addTranslation":
					$source=$wgRequest->getVal( 'source' );
					$translate=$wgRequest->getVal( 'translate' );
					$articletitle=$wgRequest->getVal( 'articletitle' );
					$wgOut->addHTML($controller->executeAddTranslationRequest($source, $translate,$articletitle));				
				break;
				case "setLanguage":
					$source=$wgRequest->getVal( 'source' );
					$lang=$wgRequest->getVal( 'lang' );
					$wgOut->addHTML($controller->executeSetLanguageRequest($source, $lang));				
				break;
			}
		}
		
		$wgOut->addHTML($controller->createDetailLanguagePage($wgRequest->getVal( 'cible' )));
        	
	}
    
    
    /**
	 * Load the internationalization for this extension
	 */
	public static function loadMessages() {
        static $messagesLoaded = false;
        global $wgMessageCache;
        global $mgVersion;
        global $wgLanguageCode, $wgContLanguageCode;
        if (!is_object($wgMessageCache)) return;
        if ( $messagesLoaded ) return;
        $messagesLoaded = true;
        require( dirname( __FILE__ ) . '/MultiLanguageManager.i18n.php' );
       
        if ($mgVersion<10700) {
        	
        	if (array_key_exists($wgLanguageCode, $allMessages)) {
				$wgMessageCache->addMessages( $allMessages[$wgLanguageCode]);
        	} else {
        		$wgMessageCache->addMessages( $allMessages[$wgContLanguageCode]);
        	}
        } else {
	        foreach ( $allMessages as $lang => $langMessages ) {
	                $wgMessageCache->addMessages( $langMessages, $lang );
	        }
        }
    }
    
}
?>
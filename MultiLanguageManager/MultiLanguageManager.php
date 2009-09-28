<?php
/*
 * Created on 27 mai 2007
 *
 * http://www.art122-5.net
 * Marc Despland 
 * 
 * This file is the main entry point of the Multi Language Manager extension
 * To Install this extension, you have to include this file in your LocalSettings.php
 * require_once( "$IP/extensions/MultiLanguageManager/MultiLanguageManager.php" );
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
# Not a valid entry point, skip unless MEDIAWIKI is defined
if (!defined('MEDIAWIKI')) {
        echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "$IP/extensions/MultiLanguageManager/MultiLanguageManager.php" );
EOT;
        exit( 1 );
}
//The wiki version x.y.z becomes the number xxyyzz
if (! function_exists('wikiEncodedVersion')) {
	function wikiEncodedVersion() {
    	global $wgVersion;
    	$versionelts=explode(".",$wgVersion);
    	$version=0;
    	for($i=0;$i<3;$i++) {
    		$version=$version*(100);
    		if (is_numeric($versionelts[$i])) {
    			$version+=$versionelts[$i];
    		}
    	}
    	return($version);
    }
}


$mgVersion=wikiEncodedVersion();

define('ART122-5 MULTILANGUEMANAGER',1);
/**
 * This is the configuration file for this extension
 */
require_once( dirname(__FILE__) . "/MultiLanguageManager_setup.php" );

/**
 * The autoload class
 */
$wgAutoloadClasses['MultiLanguageManager'] = dirname(__FILE__) . '/MultiLanguageManager_body.php';
$wgAutoloadClasses['MultiLanguageManager_controller'] = dirname(__FILE__) . '/MultiLanguageManager_controller.php';
$wgAutoloadClasses['MultiLanguageManager_template'] = dirname(__FILE__) . '/MultiLanguageManager_template.php';

/**
 * The Credits Extension
 */

if ($mgVersion<10700) {
	if (! function_exists('__autoload')) {
		function __autoload($class_name) {
			global $wgAutoloadClasses;
			if (array_key_exists($class_name, $wgAutoloadClasses)) {
				require_once $wgAutoloadClasses[$class_name];
			}
		}
	}
	if (!class_exists('SpecialPage')) {
		require_once dirname(__FILE__).'/../../includes/SpecialPage.php';
	}

	$manager=new MultiLanguageManager();
	SpecialPage::addPage($manager) ;
} else {
	$wgSpecialPages['MultiLanguageManager']		= 'MultiLanguageManager';
}


$wgExtensionFunctions[] = "wfMultiLanguageManagerExtension";

function wfMultiLanguageManagerExtension() {
	MultiLanguageManager::loadMessages();
}


/**
 * Register the Hooks used by this extension
 */
$wgHooks['LoadAllMessages'][]					= 'mlLoadMessages';					//1.8.0
$wgHooks['SkinTemplateContentActions'][]		= 'mlAddLanguageAction';			//1.5.0
//$wgHooks['BeforePageDisplay'][] 				= 'mlChangeLanguage'; 				//1.7.0
$wgHooks['ArticleDeleteComplete'][] 			= 'mlCleanLanguageOnArticleDelete';	//1.4.0
$wgHooks['SkinTemplateOutputPageBeforeExec'][] 	= 'mlGenerateSideBar';				//1.10.0
$wgHooks['ParserBeforeInternalParse'][]  		= 'mlSetLangParse';					//1.5.0

/**
 * Connected user can choose their language, guest not
 * So we select the language of the page
 */
function mlChangeLanguage($out) {
    global $wgLang;
    global $wgUser;
    global $wgLanguageCode;
	global $wgContLanguageCode;
	$page=Title::newFromText($out->getPageTitle());
	if (get_class($page)=="Title") {
		$i=$page->getArticleID();
		$controller=new MultiLanguageManager_controller();
		MultiLanguageManager_controller::setUILanguage($controller->getPageLanguage($i));
	} 
	return(true);
}


function mlSetLangParse(&$parser, &$text, &$strip_state) {	
	global $wgLang;
    global $wgUser;
    global $wgLanguageCode;
	global $wgContLanguageCode;
	$page=$parser->getTitle();
	if (get_class($page)=="Title") {
		$i=$page->getArticleID();
		$controller=new MultiLanguageManager_controller();
		MultiLanguageManager_controller::setUILanguage($controller->getPageLanguage($i));
	}
	return(true);
}

/**
 * Load the internazionalization messages
 */
function mlLoadMessages() {
	MultiLanguageManager::loadMessages();
	return(true);
}

/**
 * Add the language action to the content actions
 */
function mlAddLanguageAction(&$content_actions) {
	MultiLanguageManager::addLanguageAction($content_actions);
	return(true);
}
/**
 * The article will no longer exists, so we clan the language table
 */
function mlCleanLanguageOnArticleDelete(&$article, &$user, &$reason) {
	$controller=new MultiLanguageManager_controller();
	$controller->executeRemoveLanguageSettingsRequest($article->getID());
	return(true);
}


function mlGenerateSideBar(&$skin,&$tpl) {
	global $wgLanguageCode;		
	$lines = explode( "\n", wfMsgForContent( 'sidebar/'.$wgLanguageCode ) );
	foreach ($lines as $line) {
		if (strpos($line, '*') !== 0)
			continue;
		if (strpos($line, '**') !== 0) {
			$line = trim($line, '* ');
			$heading = $line;
		} else {
			if (strpos($line, '|') !== false) { // sanity check
				$line = explode( '|' , trim($line, '* '), 2 );					
				$link = wfMsgForContent( $line[0] );					
				if ($link == '-')
					continue;
				if (wfEmptyMsg($line[1], $text = wfMsg($line[1])))
					$text = $line[1];
				if (wfEmptyMsg($line[0], $link))
					$link = $line[0];

				if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $link ) ) {
					$href = $link;
				} else {
					$title = Title::newFromText( $link );
					if ( $title ) {
						$title = $title->fixSpecialName();
						$href = $title->getLocalURL();
					} else {
						$href = 'INVALID-TITLE';
					}
				}
				$bar[$heading][] = array(
					'text' => $text,
					'href' => $href,
					'id' => 'n-' . strtr($line[1], ' ', '-'),
					'active' => false
				);
				$tpl->set( 'sidebar', $bar );
			} else { continue; }
		}
	}
	return(true);
}
	

/**
 * The Credits Extension
 */
$wgExtensionCredits['parserhook'][] = array(
       'name' => 'Multi Language Manager',
       'version' => '1.0.1',
       'author' => 'Marc Despland',
       'url' => 'http://www.art122-5.net/index.php/MultiLanguageManager_Extension',
       'description' => 'Mediawiki Multi Language Manager'
);

?>
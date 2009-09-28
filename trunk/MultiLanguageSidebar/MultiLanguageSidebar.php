<?php
/*
 * Created on 29 janv. 2008
 *
 * http://www.art122-5.net
 * Marc Despland 
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
require_once( "$IP/extensions/MultiLanguageSidebar/MultiLanguageSidebar.php" );
EOT;
        exit( 1 );
}

$wgExtensionFunctions[] = "wfMultiLanguageSidebarExtension";

function wfMultiLanguageSidebarExtension() {
	mlsLoadMessages();
	return true;
}


/**
 * Register the Hooks used by this extension
 */
$wgHooks['LoadAllMessages'][]				= 'mlsLoadMessages';					//1.8.0

function mlsLoadMessages() {
	static $messagesLoaded = false;
	global $wgMessageCache;
	global $mgVersion;
	global $wgLanguageCode, $wgContLanguageCode;
	if (!is_object($wgMessageCache)) return true;
	if ( $messagesLoaded ) return true;
	$messagesLoaded = true;
	require( dirname( __FILE__ ) . '/MultiLanguageSidebar.i18n.php' );
	foreach ( $allMessages as $lang => $langMessages ) {
		$wgMessageCache->addMessages( $langMessages, $lang );
	}
	return(true);
}

/**
 * Define the url key of the sidebar
 */
$wgForceUIMsgAsContentMsg[]='presentation-url';
$wgForceUIMsgAsContentMsg[]='teaching-url';
$wgForceUIMsgAsContentMsg[]='team-url';
$wgForceUIMsgAsContentMsg[]='research-url';
$wgForceUIMsgAsContentMsg[]='publication-url';
$wgForceUIMsgAsContentMsg[]='memories-url';
$wgForceUIMsgAsContentMsg[]='contact-url';
/**
 * The Credits Extension
 */
$wgExtensionCredits['parserhook'][] = array(
       'name' => 'Multi Language Sidebarr',
       'version' => '1.0.0',
       'author' => 'Marc Despland',
       'url' => 'http://www.art122-5.net/index.php',
       'description' => 'Mediawiki Multi Language Sidebar'
);
?>
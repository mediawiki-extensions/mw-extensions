<?php
/*
 * Created on 3 juin 2007
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
include_once( dirname(__FILE__).'/includes/WebStart.php' );
if (defined(MEDIAWIKI)) {
	# Initialize MediaWiki base class
	require_once( dirname(__FILE__).'/includes/Wiki.php' );
	$mediaWiki = new MediaWiki();
} else {
	define( 'MEDIAWIKI', true );

	# Load up some global defines.
	require_once( './includes/Defines.php' );
	# Include this site setttings
	require_once( './LocalSettings.php' );
	# Prepare MediaWiki
	require_once( 'includes/Setup.php' );

	# Initialize MediaWiki base class
	require_once( "includes/Wiki.php" );
	$mediaWiki = new MediaWiki();
}

function createTableLanguage() {
	global $wgDBprefix;
	global $wgDBTableOptions;
	global $wgDBtype;
	$option="";
	if ($wgDBtype == "mysql") $option=$wgDBTableOptions;
	$creation_script="  
CREATE TABLE ".$wgDBprefix."page_language (
  page_id int(8) NOT NULL,
  lang varchar(3) NOT NULL,
  PRIMARY KEY  (page_id)
) ".$option.";";
return $creation_script;
}

function createTableTranslation() {
	global $wgDBprefix;
	global $wgDBTableOptions;
	global $wgDBtype;
	$option="";
	if ($wgDBtype == "mysql") $option=$wgDBTableOptions;
	$creation_script="  
CREATE TABLE ".$wgDBprefix."page_translation (
  `source` int(8) NOT NULL,
  translate int(8) NOT NULL,
  UNIQUE KEY source_2 (`source`,translate),
  KEY `source` (`source`),
  KEY translate (translate)
) ".$option.";";
return $creation_script;
}


if (isset($_GET['create'])) {
	$dbw = wfGetDB( DB_MASTER );
	$dbw->safeQuery(createTableLanguage());
	$dbw->safeQuery(createTableTranslation());
}

?>
<html>
	<head>
		<title>MultiLanguage Manager</title>
	</head>
	<body>
	<h1>MultiLanguage Manager for MediaWiki</h1>
	<p>To install this extension you have to create two tables on the MediaWiki Database.</p>
	<p>This script uses your configuration settings to create the sql script you can use to 
	create the tables. If the default wiki database user can create the table, you can use 
	this script to do it, otherwise use your favorite tools to do it</p>
	
	<p>
  		<center>
	  		<textarea name="sql" rows=15" cols="50">
	  			<?php 
	  				echo createTableLanguage();
					echo createTableTranslation();
				 ?>
	  		</textarea><br><br>
	
		  	<form>
		  		<input type=hidden name="create" value="">
		  		<input type=submit value="Try to create the tables ?">
		  	</form>
  			<?php
  			if (isset($_GET['create'])) {
  			?>
	  		<textarea name="sql" rows=5" cols="50">
				<?php 
				if ($dbw->lastError()=='') {
					echo "Success";
				}else {
					echo $dbw->lastError();
				}?>

	  		</textarea><br><br>
  			
			<?php
  			}
  			?>
  	
  		</center>
  	</p>
    
	</body>
</html>

<?php
/*
 * Created on 27 mai 2007
 *
 * http://www.art122-5.net
 * Marc Despland 
 * 
 * The Art 122-5 skin
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

 class MultiLanguageManager_display extends MultiLanguageManager_template {
 	
 	function __construct() {
 		parent::__construct();
 	}
	//This function create a link with th popup div
	public function displayLanguageLinkPopup($visibleLinkText,$contentLinkTest) {
		$output="";
		$output.="<!--[if lt IE 7]><span class='popup' onMouseOver=\"document.getElementById('".$this->id_cadre_popup."').style.visibility = 'visible';\" onmouseout=\"document.getElementById('".$this->id_cadre_popup."').style.visibility = 'hidden';\" ><![endif]-->";
		$output.="<span class='".$this->class_popup."'>" . $visibleLinkText;
	    $output.="<!--[if lt IE 7]><span  onmouseout=\"document.getElementById('".$this->id_cadre_popup."').style.visibility = 'hidden';\"><![endif]-->";
		$output.="    <div class='cadre_exterieur' id='".$this->id_cadre_popup."' >";
	    $output.="      <div class='cadre_milieu'>";
	    $output.="        <div class='cadre_interieur'>";
	    $output.="          <div class='lcd'>";
	    $output.="        	  ".$contentLinkTest;
	    $output.="          </div>";
	    $output.="        </div>";
	    $output.="      </div>";
		$output.="  </div>";
		$output.="<!--[if lt IE 7]></span><![endif]-->";
	    $output.="</span>\n";
		$output.="<!--[if lt IE 7]></span><![endif]-->";
		return $output;
	}	
		
 }
?>
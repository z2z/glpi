<?php
/*
 
  ----------------------------------------------------------------------
GLPI - Gestionnaire libre de parc informatique
 Copyright (C) 2002 by the INDEPNET Development Team.
 Bazile Lebeau, baaz@indepnet.net - Jean-Mathieu Dol�ans, jmd@indepnet.net
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------
 Based on:
IRMA, Information Resource-Management and Administration
Christian Bauer, turin@incubus.de 

 ----------------------------------------------------------------------
 LICENSE

This file is part of GLPI.

    GLPI is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    GLPI is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with GLPI; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 ----------------------------------------------------------------------
 Original Author of file:
 Purpose of file:
 ----------------------------------------------------------------------
*/
 

include ("_relpos.php");
include ($phproot . "/glpi/includes.php");
include ($phproot . "/glpi/includes_computers.php");
include ($phproot . "/glpi/includes_networking.php");
include ($phproot . "/glpi/includes_monitors.php");
include ($phproot . "/glpi/includes_printers.php");
include ($phproot . "/glpi/includes_tracking.php");
include ($phproot . "/glpi/includes_software.php");
include ($phproot . "/glpi/includes_peripherals.php");

if(isset($_GET)) $tab = $_GET;
if(empty($tab) && isset($_POST)) $tab = $_POST;
if(!isset($tab["ID"])) $tab["ID"] = "";

if (isset($tab["add"])) {
	checkAuthentication("admin");
	addComputer($tab);
	logEvent(0, "computers", 4, "inventory", $_SESSION["glpiname"]." added ".$tab["name"].".");
	header("Location: $_SERVER[HTTP_REFERER]");
} else if (isset($tab["delete"])) {
	checkAuthentication("admin");
	deleteComputer($tab);
	logEvent($tab["ID"], "computers", 4, "inventory", $_SESSION["glpiname"]." deleted item.");
	header("Location: ".$cfg_install["root"]."/computers/");
} else if (isset($tab["update"])) {
	if(empty($tab["show"])) $tab["show"] = "";
	if(empty($tab["contains"])) $tab["contains"] = "";
	checkAuthentication("admin");
	updateComputer($tab);
	logEvent($tab["ID"], "computers", 4, "inventory", $_SESSION["glpiname"]."updated item.");
	commonHeader("Computers",$_SERVER["PHP_SELF"]);
	showComputerForm(0,$_SERVER["PHP_SELF"],$tab["ID"]);
	showPorts($tab["ID"], 1);
	showPortsAdd($tab["ID"],1);
	showConnections($tab["ID"]);
	showSoftwareInstalled($tab["ID"]);
	showJobListForItem($_SESSION["glpiname"],$tab["ID"]);
	showOldJobListForItem($_SESSION["glpiname"],$tab["ID"]);
	commonFooter();
} 
else if (isset($tab["disconnect"]))
{
	checkAuthentication("admin");
	Disconnect($tab["eID"],$tab["device_type"]);
	logEvent($tab["cID"], "computers", 5, "inventory", $_SESSION["glpiname"]." disconnected item.");
	header("Location: ".$_SERVER["PHP_SELF"]."?ID=".$tab["cID"]);
}
else if(isset($tab["connect"])&&isset($tab["device_type"]))
{
	if($tab["connect"]==1)
	{
		checkAuthentication("admin");
		commonHeader("Computers",$_SERVER["PHP_SELF"]);
		showConnectSearch($_SERVER["PHP_SELF"],$tab["ID"],$tab["device_type"]);
		commonFooter();
	}	 
	else if($tab["connect"]==2)
	{
		checkAuthentication("admin");
		commonHeader("Computers",$_SERVER["PHP_SELF"]);
		listConnectElement($_SERVER["PHP_SELF"],$tab);
		commonFooter();
	} 
	else if($tab["connect"]==3)
	{
		checkAuthentication("admin");
		Connect($_SERVER["PHP_SELF"],$tab["ID"],$tab["cID"],$tab["device_type"]);
		logEvent($tab["cID"], "computers", 5, "inventory", $_SESSION["glpiname"] ." connected item.");
		header("Location: ".$_SERVER["PHP_SELF"]."?ID=".$tab["cID"]);
	}
} else {

	checkAuthentication("normal");
	commonHeader("Computers",$_SERVER["PHP_SELF"]);
	if (isset($tab["withtemplate"]))
	{
		showComputerForm($tab["withtemplate"],$_SERVER["PHP_SELF"],$tab["ID"]);
	}
	else
	{
	if ($_SESSION["glpitype"]=="admin"&&isset($tab["delete_inter"])&&!empty($tab["todel"])){
		$j=new Job;
		foreach ($tab["todel"] as $key => $val){
			if ($val==1) $j->deleteInDB($key);
			}
		}

		if (showComputerForm(0,$_SERVER["PHP_SELF"],$tab["ID"])) {
			showPorts($tab["ID"], 1);
			showPortsAdd($tab["ID"],1);
			showConnections($tab["ID"]);
			showSoftwareInstalled($tab["ID"]);
			showJobListForItem($_SESSION["glpiname"],$tab["ID"]);
			showOldJobListForItem($_SESSION["glpiname"],$tab["ID"]);
			
		}
	}
	commonFooter();
}


?>

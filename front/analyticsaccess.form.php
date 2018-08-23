<?php

include_once ("../../../inc/includes.php");

Html::header('Analyticsaccess', $_SERVER['PHP_SELF'], "tools", "PluginAnalyticsAnalyticsaccess", "analytics");

$Analyticsaccess = new PluginAnalyticsAnalyticsaccess();


if (!isset($_REQUEST["id"])) {
   $_REQUEST["id"] = "";
}

if (isset($_REQUEST["update"])) {
   $Analyticsaccess->check($_REQUEST['id'], UPDATE);
   $Analyticsaccess->update($_REQUEST);
   Html::back();

} else if (isset($_REQUEST["add"])) {
   $Analyticsaccess->check($_REQUEST['id'], UPDATE);
   $Analyticsaccess->add($_REQUEST);
   Html::back();

} else {

	$Analyticsaccess->showForm($_GET['id']);
}


Html::footer();

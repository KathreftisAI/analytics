<?php

include_once ("../../../inc/includes.php");

Html::header('Sync', $_SERVER['PHP_SELF'], "tools", "PluginAnalyticsAnalyticssync", "analytics");

$Analyticssync = new PluginAnalyticsAnalyticssync();

$Analyticssync->showForm($_GET['id']);

Html::footer();

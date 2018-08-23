<?php

include_once ("../../../inc/includes.php");

Html::header('Sync', $_SERVER['PHP_SELF'], "tools", "PluginAnalyticsAnalyticssync", "analytics");

if (Session::haveRightsOr('config', [READ, UPDATE])) {
	Search::show('PluginAnalyticsAnalyticssync');
}

Html::footer();
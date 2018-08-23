<?php
/*
 -------------------------------------------------------------------------
 analytics plugin for GLPI
 Copyright (C) 2018 by the Unotech Software.

 https://github.com/Unotechsoftware/analytics
 -------------------------------------------------------------------------

 LICENSE

 This file is part of analytics.

 analytics is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 analytics is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with analytics. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 */

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_analytics_install() {

      // To be called for each task the plugin manage
   // task in class
  CronTask::Register('PluginAnalyticsAnalyticssync', 'Analyticssync', DAY_TIMESTAMP, ['param' => 50]);

  global $DB;

  //to create config table if doesn't exitst
  $table = 'glpi_plugin_analytics_configs';
  if (!$DB->tableExists($table)) { //not installed


	$query = "CREATE TABLE `".$table."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`url` varchar(255) NOT NULL,
				`key` varchar(255) NOT NULL,
        `useremail` varchar(255) NOT NULL,
        `password` varchar(255) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

     $DB->queryOrDie($query, __('Error in creating glpi_plugin_analytics_configs', 'analytics').
                             "<br>".$DB->error());


     $query = "INSERT INTO `glpi_plugin_analytics_configs` VALUES (1,'http://localhost:3000','88df7780bf5e97fef6d5425b86c7a332e82fb5baf6856dd2254654546345' , 'admin@email.com' ,'password');";

     $DB->queryOrDie($query, __('Error during update glpi_plugin_analytics_configs', 'analytics').
                             "<br>" . $DB->error());

  }

  //to create config table if doesn't exitst
  $table = 'glpi_plugin_analytics_analyticssyncs';
  if (!$DB->tableExists($table)) { //not installed

    $query = "CREATE TABLE  `".$table."` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `itemtype` varchar(45) NOT NULL,
    `itemid` varchar(45) NOT NULL,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    $DB->queryOrDie($query, __('Error in creating glpi_plugin_analytics_sync', 'analytics').
                             "<br>".$DB->error());

  }

    //to create config table if doesn't exitst
  $table = 'glpi_plugin_analytics_analyticsaccesses';
  if (!$DB->tableExists($table)) { //not installed

    $query = "CREATE TABLE  `".$table."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groups_id` int(11) NOT NULL,
  `profiles_id` int(11) NOT NULL,
  `syncid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

    $DB->queryOrDie($query, __('Error in creating glpi_plugin_analytics_analyticsaccesses', 'analytics').
                             "<br>".$DB->error());

  }



   return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_analytics_uninstall() {

    global $DB;

	CronTask::unregister('PluginAnalyticsAnalyticssync');

	if ($DB->tableExists('glpi_plugin_analytics_configs')) { //not installed

	 $query = "DROP TABLE `glpi_plugin_analytics_configs`";
	 $DB->queryOrDie($query, $DB->error());
	}


  if ($DB->tableExists('glpi_plugin_analytics_analyticssyncs')) { //not installed

   $query = "DROP TABLE `glpi_plugin_analytics_analyticssyncs`";
   $DB->queryOrDie($query, $DB->error());
  }

  if ($DB->tableExists('glpi_plugin_analytics_analyticsaccesses')) { //not installed

   $query = "DROP TABLE `glpi_plugin_analytics_analyticsaccesses`";
   $DB->queryOrDie($query, $DB->error());
  }


   return true;
}

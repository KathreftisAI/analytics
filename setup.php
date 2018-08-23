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

define('PLUGIN_ANALYTICS_VERSION', '1.0.0');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_analytics() {
   global $PLUGIN_HOOKS;

   $PLUGIN_HOOKS['csrf_compliant']['analytics'] = true;

   $PLUGIN_HOOKS["menu_toadd"]['analytics'] = array('plugins'  => 'PluginAnalyticsConfig',
                                                   'tools'  => 'PluginAnalyticsAnalyticssync');
   
   $PLUGIN_HOOKS['config_page']['analytics'] = '/plugins/analytics/front/dashboard.php';

   if (Session::haveRightsOr("config", [READ, UPDATE])) {

      Plugin::registerClass('PluginAnalyticsConfig', ['addtabon' => 'Config']);

      $PLUGIN_HOOKS['config_page']['analytics'] = 'front/config.form.php';
   }

}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_analytics() {
   return [
      'name'           => 'analytics',
      'version'        => PLUGIN_ANALYTICS_VERSION,
      'author'         => '<a href="http://www.unotechsoft.com">Unotech Software</a>',
      'license'        => '',
      'homepage'       => '',
      'requirements'   => [
         'glpi' => [
            'min' => '9.2',
            'dev' => true
         ]
      ]
   ];
}

/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_analytics_check_prerequisites() {
   // Strict version check (could be less strict, or could allow various version)
   if (version_compare(GLPI_VERSION, '9.2', 'lt')) {
      if (method_exists('Plugin', 'messageIncompatible')) {
         echo Plugin::messageIncompatible('core', '9.2');
      } else {
         echo "This plugin requires GLPI >= 9.2";
      }
      return false;
   }
   return true;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_analytics_check_config($verbose = false) {
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      echo __('Installed / not configured', 'analytics');
   }
   return false;
}

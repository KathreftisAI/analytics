
<?php

class PluginAnalyticsConfig extends CommonDBTM {
	

   static private $_instance = NULL;
   static $rightname         = 'config';


   static function canCreate() {
      return Session::haveRight('config', UPDATE);
   }


   static function canView() {
      return Session::haveRight('config', READ);
   }


   static function getTypeName($nb=0) {
       return $LANG['analytics']['config']['setup'];
   }


   function getName($with_comment=0) {
      return __('Analytics', 'analytics');
   }


  function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
    if (!$withtemplate) {
       if ($item->getType() == 'Config') {
          return __('Analytics');
       }
    }
    return '';
  }


      /**
    * Singleton for the unique config record
    */
   static function getInstance() {

      if (!isset(self::$_instance)) {
         self::$_instance = new self();
         if (!self::$_instance->getFromDB(1)) {
            self::$_instance->getEmpty();
         }
      }
      return self::$_instance;
   }




   /**
    * @see CommonGLPI::getMenuName()
   **/
   static function getMenuName() {
      return __('Analytics');
   }
   
   
   /**
    *  @see CommonGLPI::getMenuContent()
    *
    *  @since version 0.5.6
   **/
   static function getMenuContent() {
   	global $CFG_GLPI;
   
   	$menu = array();

      $menu['title']   = __('Analytics','Analytics');
      $menu['page']    = '/plugins/analytics/front/dashboard.php';
 
   	return $menu;
   }	

    static function getConfigData() {

        $config = self::getInstance();
        return $config->fields ;
    }


       /**
    * Summary of showConfigForm
    * @return boolean
    */
   static function showFormAnalytics() {
      global $LANG, $CFG_GLPI;

      $config = self::getInstance();
      if (!Session::haveRight("config", UPDATE)) {
         return false;
      }

      $analyticsSync = new PluginAnalyticsAnalyticssync();

      echo "<form name='form' action=\"".Toolbox::getItemTypeFormURL('PluginAnalyticsConfig')."\" method='post'>";
      echo "<div class='center' id='tabsbody'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='4'>" . __('Analytics setup') . "</th></tr>";
      echo "<tr><td >" . __('Analytics URL :') . "</td>";
      echo "<td colspan='3'>";
      echo "<input type='text' name='url'  value='".$config->fields['url']."'>";
      echo "</td></tr>";
      echo "<tr><td >" . __('Analytics Secret Key :') . "</td>";
      echo "<td colspan='3'>";
      echo "<input type='text' name='key'  value='".$config->fields['key']."'>";
      echo "</td></tr>";
      echo "<tr><td >" . __('Analytics UserEmail :') . "</td>";
      echo "<td colspan='3'>";
      echo "<input type='text' name='useremail'  value='".$config->fields['useremail']."'>";
      echo "</td></tr>";
      echo "<tr><td >" . __('Analytics Password :') . "</td>";
      echo "<td colspan='3'>";
      echo "<input type='password' autocomplete='new-password' name='password'  value='".Toolbox::decrypt($config->fields['password'], GLPIKEY)."'>";
      echo "</td></tr>";
      echo "<tr class='tab_bg_2'>";
      echo "<td colspan='4' class='center'>";
      echo "<input type='hidden' name='id' class='submit' value='1'>";
      echo "<input type='submit' name='update' class='submit' value=\""._sx('button', 'Save')."\">";
      echo "</td></tr>";
      echo "</table></div>";
      $analyticsSync->testconnection();
      Html::closeForm();

  }


   static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {

      if ($item->getType()=='Config') {
          $config = new self();
          $config->showFormAnalytics();
      }
      return true;
   }

  function post_updateItem($history = 1) {

    if (in_array('useremail', $this->updates) || in_array('password', $this->updates ) || in_array('url', $this->updates )) {
      //unset session id to force login
      unset($_SESSION['analytics']['session_token'] );
    }
  }



}
?>   

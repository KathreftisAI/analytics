<?php

if (!defined('GLPI_ROOT')) {
    die("Sorry. You can't access directly to this file");
}

/**
 * process short summary.
 *
 * process description.
 *
 * @version 1.0
 * @author MoronO
 */
class PluginAnalyticsAnalyticsaccess extends CommonDBTM {

 
   static function canCreate() {
      return Session::haveRight('config', UPDATE);
   }


   static function canView() {
      return Session::haveRightsOr('config', [READ, UPDATE]);
   }

   static function canUpdate( ) {
      return Session::haveRight('config', UPDATE);
   }

   function canUpdateItem() {
      return Session::haveRight('config', UPDATE);
   }


   function maybeDeleted() {
      return false;
   }

  static function canPurge() {
    return Session::haveRight('config', UPDATE);
  }


  static function getTypeName($nb=0) {
      global $LANG;

      return 'Analytics Access';
  }

   
    /**
     * Summary of getFromDB
     * @param mixed $ID id of needed object
     * @return mixed object if found, else false
     */
   function getFromDB($ID) {
      global $DB;
      // Search for object in database and fills 'fields'

      // != 0 because 0 is consider as empty
      if (strlen($ID)==0) {
         return false;
      }

      $query = "SELECT *
                FROM `".$this->getTable()."`
                WHERE `".$this->getIndexName()."` = '".$ID."'";

      if (($result = $DB->query($query))  && $DB->numrows($result)==1) {
             $this->fields = $DB->fetch_assoc($result);
             $this->post_getFromDB();

             return true;
      }

      return false;
   }

  
   function showForm ($ID,$name, $options=array('candel'=>false)) {
      global $DB, $CFG_GLPI, $LANG;

      if (!Session::haveRight("config", UPDATE)) {
         return false;
      }

      $rand=mt_rand();

      $this->fields['name']=$name;
      $this->fields['id']=$ID;
      $this->showFormHeader($options);
      echo "<tr class='tab_bg_1'>";
      echo "<td>".__("Name")."&nbsp;:</td><td>";
      Html::autocompletionTextField($this, "name");
      echo "</td>";
      echo "</tr>";


      echo "<div class='firstbloc'>";
      echo "<form name='analyticsaccess_form$rand' id='analyticsaccess_form$rand' method='post' action='";
      echo Toolbox::getItemTypeFormURL(__CLASS__)."'>";
      echo "<table class='tab_cadre_fixe'>";
      echo "<tr class='tab_bg_1'><th colspan='6'>Authorizations
            </tr>";
      echo "<tr class='tab_bg_2'><td class='center'>";
      echo "<input type='hidden' name='syncid' value=".$ID.">";
      echo "</td><td class='center'>".Group::getTypeName(1)."</td><td>";
      Group::Dropdown();
      echo "</td><td class='center'>".Profile::getTypeName(1)."</td><td>";
      Profile::Dropdown();

      echo "</td><td class='center'>";
      echo "<input type='submit' name='add' value=\""._sx('button', 'Add')."\" class='submit'>";
      echo "</td></tr>";

      echo "</table>";
      Html::closeForm();
      echo "</div>";


      $query = "SELECT glpi_plugin_analytics_analyticsaccesses.id as id ,glpi_groups.name as groupname, glpi_profiles.name as profilename
FROM glpi_plugin_analytics_analyticsaccesses
  LEFT JOIN glpi_groups ON glpi_plugin_analytics_analyticsaccesses.groups_id = glpi_groups.id
  LEFT JOIN glpi_profiles ON glpi_plugin_analytics_analyticsaccesses.profiles_id = glpi_profiles.id
 where glpi_plugin_analytics_analyticsaccesses.syncid =".$ID;

      $result = $DB->query($query);
      $num = $DB->numrows($result);
      echo "<div class='spaced'>";
      Html::openMassiveActionsForm('mass'.__CLASS__.$rand);

     $massiveactionparams = array('num_displayed' => $num,
                       'container'     => 'mass'.__CLASS__.$rand);
     Html::showMassiveActions($massiveactionparams);


      if ($num > 0) {
        echo "<table class='tab_cadre_fixehov'>";
        $header_begin  = "<tr>";
        $header_top    = '';
        $header_bottom = '';
        $header_end    = '';

        $header_begin  .= "<th>";
        $header_top    .= Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
        $header_bottom .= Html::getCheckAllAsCheckbox('mass'.__CLASS__.$rand);
        $header_end    .= "</th>";
        $header_end .= "<th>"._n('Group', 'Groups', Session::getPluralNumber());
        $header_end .= "<th>"._n('profile', 'profiles', Session::getPluralNumber())."</th>";
        $header_end .= "</th></tr>";
        echo $header_begin.$header_top.$header_end;

         while ($data = $DB->fetch_assoc($result)) {
            echo "<tr class='tab_bg_1'>";
            echo "<td width='10'>";          
            Html::showMassiveActionCheckBox(__CLASS__, $data["id"]);
            echo "</td>";
            echo "<td>";

            echo $data["groupname"]."</a>";
            echo "</td>";
            echo "<td>".$data["profilename"]."</td>";
            echo "</tr>";
         }
         echo $header_begin.$header_bottom.$header_end;
         echo "</table>";
      } else {
         echo "<table class='tab_cadre_fixe'>";
         echo "<tr><th>".__('No item found')."</th></tr>";
         echo "</table>\n";
      }

     $massiveactionparams['ontop'] = false;
     Html::showMassiveActions($massiveactionparams);

      Html::closeForm();
      echo "</div>";



   }


}



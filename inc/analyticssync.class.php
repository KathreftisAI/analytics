<?php

// Class of the defined type
class PluginAnalyticsAnalyticssync extends CommonDBTM {





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



   static function getTypeName($nb=0) {
      global $LANG;

      return 'Analytics Access';
   }



   /**
    * Give localized information about 1 task
    *
    * @param $name of the task
    *
    * @return array of strings
    */
   static function cronInfo($name) {
      switch ($name) {
         case 'Analyticssync' :
            return ['description' => __('To sync analytics dashboards and questions', 'Analyticssync'),
                    'parameter'   => __('Cron parameter for Sync', 'Analyticssync')];
      }
      return [];
   }
   /**
    * Execute 1 task manage by the plugin
    *
    * @param $task Object of CronTask class for log / stat
    *
    * @return interger
    *    >0 : done
    *    <0 : to be run again (not finished)
    *     0 : nothing to do
    */
   static function cronAnalyticssync($task) {

      $task->log("Sync log message from class");
      $r = mt_rand(0, $task->fields['param']);
      self::synchronise();
      usleep(1000000+$r*1000);
      $task->setVolume($r);
      return 1;
   }

   //get session and synchronise question and dashboard
   static function synchronise()
   {
     $config = new PluginAnalyticsConfig();
     $config_data = $config->getConfigData();

     //get sessionid
     if (!isset( $_SESSION['analytics']['session_token'] )) {
       self::getAnalyticsSession($config_data);
     }else{
       $valid=self::validatesession($config_data);
       //if session is not valid then create new session
       if (!$valid) {
         self::getAnalyticsSession($config_data);
       }
     }

     //synchronise dashboard
     self::syncDashboardList($config_data);
     //synchoronise question
     self::syncQuestionList($config_data);

   }

   //get api curl request
   static function getAPI($url, $header)
   {
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
      $result = json_decode(curl_exec($ch));
      curl_close($ch);
      return $result;
   }

   //post api curl request
   static function postAPI($url, $data, $header)
   {
         
     $data_string = json_encode($data);

      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
      $result = json_decode(curl_exec($ch));
      curl_close($ch);
      return $result;
   }

   //get analytics session id using admin useremail and password
   static function getAnalyticsSession($config_data){

    $url= $config_data['url'].'/api/session';
    $data = array("username" => $config_data['useremail'], "password" => Toolbox::decrypt($config_data['password'], GLPIKEY));
    $header = array('Content-Type: application/json');
    $result = self::postAPI($url,$data,$header);

    if (isset($result->id)) {
      $_SESSION['analytics']['session_token'] = $result->id;
    }
   }


    //validate session if session is invalid then create new session
   static function validatesession($config_data)
   {  
      global $DB;
      
      //if session is not set return false
      if (empty($_SESSION['analytics']['session_token'])) {
        return false;
      }

      $header = array('X-Metabase-Session:'. $_SESSION['analytics']['session_token'],
                      'Content-Type: application/json');
      $url= $config_data['url'].'/api/user/current';
      $result = self::getAPI($url, $header);
      //if session is valid return true else false
      if (!empty($result->email)) {
        return true;
      }
      return false;
   }


    //test connection to validate username and password
   static function testconnection()
   {  
      $config = new PluginAnalyticsConfig();
      $config_data = $config->getConfigData();

       //if session not stored then login and validate 
       if (!isset( $_SESSION['analytics']['session_token'] )) {
          self::testvalidation($config_data);
       }
       //if session is stored then validate if failed then login and validate
       else{
         $valid=self::validatesession($config_data);
         if (!$valid) {
            self::testvalidation($config_data);
         }else{
            echo "<font color='green'> Successful connection</font>";
         }
       }
   }

   static function testvalidation($config_data){

      self::getAnalyticsSession($config_data);
      $valid=self::validatesession($config_data);
      if (!$valid) {
       echo "<font color='red'> Invalid config params</font>";
      }else{
        echo "<font color='green'> Successful connection</font>";
      }

   }     

   //synchronise embeded dashboard from analytics to table
   static function syncDashboardList($config_data)
   {  
      global $DB;
      $header = array('X-Metabase-Session:'.  $_SESSION['analytics']['session_token'] ,
                      'Content-Type: application/json');
      $url= $config_data['url'].'/api/dashboard/embeddable';
      $result = self::getAPI($url, $header);
      $embeddeddashboards = [];
      $sync = new self();

      foreach ($result as $key => $value) {

        //push embdded dashoard ids
        array_push($embeddeddashboards,  $value->id );

        //query to check question record exist
        $query= "SELECT id , name FROM glpi_plugin_analytics_analyticssyncs WHERE itemtype = 'dashboard' AND  itemid =".$value->id.";";

        $resultdata = $DB->query($query);
        $syncdata = $DB->fetch_assoc($resultdata);
        //if question record doen't exist than insert question
        if (!isset($syncdata['id'])) {
          //insert embeded question in table
         
          $input = [ 'itemtype' => 'dashboard',
                      'itemid' =>  $value->id ,
                      'name' => $value->name];
          $sync->add($input);
        }

        if ($syncdata['name'] !== $value->name) {
            //update embeded dashboard name in table
          $input = [  'id' =>  $syncdata['id'] ,
                      'name' => $value->name];
          $sync->update($input);
        }

      }

      //to delete unembedded dashboard
      $query="SELECT id,itemid FROM glpi_plugin_analytics_analyticssyncs WHERE itemtype = 'dashboard'";

      $result = $DB->query($query);

      while ($row = $DB->fetch_assoc($result)) {
        if (!in_array($row['itemid'], $embeddeddashboards)) {
          $input = [  'id' =>  $row['id'] ] ;
          $sync->delete($input);
          self::deleteAnalyticsAccess( $row['id']);
        }
      }


   }

   //synchronise embeded question from analytics to table
   static function syncQuestionList($config_data)
   {
      global $DB;

      $header = array('X-Metabase-Session:'. $_SESSION['analytics']['session_token'] ,
                      'Content-Type: application/json');
      $url= $config_data['url'].'/api/card/embeddable';
      $result = self::getAPI($url, $header);
      $embeddedquestions = [];
      $sync = new self();


      foreach ($result as $key => $value) {

        //push embdded question ids
        array_push($embeddedquestions,  $value->id );


        //query to check question record exist
        $query= "SELECT id , name FROM glpi_plugin_analytics_analyticssyncs WHERE itemtype = 'question' AND  itemid =".$value->id.";";
        $resultdata = $DB->query($query);
        $syncdata = $DB->fetch_assoc($resultdata);
        //if question record doen't exist than insert question

        if (!isset($syncdata['id'])) {
          //insert embeded question in table
          $input = [ 'itemtype' => 'question',
                      'itemid' =>  $value->id ,
                      'name' => $value->name];
          $sync->add($input);
        
        }

        if ($syncdata['name'] !== $value->name) {
            //update embeded question name in table
          $input = [  'id' =>  $syncdata['id'] ,
                      'name' => $value->name];
          $sync->update($input);
        }     
      }

      //to delete unembedded questions
      $query="SELECT id ,itemid FROM glpi_plugin_analytics_analyticssyncs WHERE itemtype = 'question'";

      $result = $DB->query($query);

      while ($row = $DB->fetch_assoc($result)) {
        if (!in_array($row['itemid'], $embeddedquestions)) {
          $input = [  'id' =>  $row['id'] ] ;
          $sync->delete($input);
          self::deleteAnalyticsAccess( $row['id']);
        }
      }
   }

   //get embeded dashboard and question list
   static function getSyncList()
   {
     global $DB;
     $groupIds='';
     $profileIds='';
     $groupCounts= count($_SESSION['glpigroups']);
     $profileCounts = count(array_keys($_SESSION['glpiprofiles']));

     //creating string of group ids 
     foreach ($_SESSION['glpigroups'] as $key => $value) {
       $groupIds .= $value;
       if ($key < ($groupCounts-1)){
        $groupIds .= ',';
       }
     }
     //creating string of profile ids
     foreach (array_keys($_SESSION['glpiprofiles']) as $key => $value) {
       $profileIds .= $value;
        if ($key < ($profileCounts -1)){
        $profileIds .= ',';
       }
     }
     //if there is no group than pass 0 as id for query
     if ($groupCounts == 0) {
       $groupIds='0';
     }
      //if there is no profile than pass 0 as id for query
     if ($profileCounts == 0) {
       $profileIds='0';
     }


     $query = 'SELECT name,itemtype,itemid FROM glpi_plugin_analytics_analyticssyncs AS async WHERE
              id IN (SELECT syncid FROM glpi_plugin_analytics_analyticsaccesses WHERE
                      groups_id IN ('.$groupIds.') OR profiles_id IN ('.$profileIds.'))';

     $result = [];
     foreach ($DB->request($query) as $list) {
          //if itemtype is dashboard then replace id to did-itemid and if it is question than qid-id for differentiating between dashboard and question 
         if ($list['itemtype'] == 'dashboard') {
             $result['did-'.$list['itemid']] = $list['name'];
          } else {
            $result['qid-'.$list['itemid']] = $list['name'];
          }      
      }
      return $result;
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




   /**
   * Summary of getSearchOptions
   * @return mixed
   */
   function getSearchOptions() {
      global $LANG;

      $tab = array();

      $tab['common'] = 'Analytics';

      $tab[1]['table']         = 'glpi_plugin_analytics_analyticssyncs';
      $tab[1]['field']         = 'name';
      $tab[1]['name']          = __('Name');
      $tab[1]['datatype']      = 'itemlink';
      $tab[1]['itemlink_type'] = $this->getType();

    

      return $tab;
   }



    function showForm ($ID, $options=array('candel'=>false)) {
      global $DB, $CFG_GLPI, $LANG;

      if (!Session::haveRight("config", UPDATE)) {
         return false;
      }

      self::getFromDB($ID);

      $Analyticsaccess = new PluginAnalyticsAnalyticsaccess();
      $Analyticsaccess->showForm($ID,$this->fields['name']);

   }


      //delete analytics accesses related to syncid
   static function deleteAnalyticsAccess($ID)
   {  
       global $DB;

      $query = "DELETE FROM glpi_plugin_analytics_analyticsaccesses where syncid =".$ID;
      $DB->query($query);

   }


}
?>

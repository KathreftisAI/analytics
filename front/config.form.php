
<?php

include ( "../../../inc/includes.php");

$config = new PluginAnalyticsConfig();

if (isset($_POST["update"])) {
   $config->check($_POST['id'], UPDATE);

   //encrypt password
   $_POST['password']=Toolbox::encrypt($_POST['password'], GLPIKEY);

   // save
   $config->update($_POST);


   Html::back();

} else if (isset($_POST["refresh"])) {
   $config->refresh($_POST); // used to refresh process list, task category list
   Html::back();
}

Html::redirect($CFG_GLPI["root_doc"]."/front/config.form.php?forcetab=".
             urlencode('PluginAnalyticsConfig$1'));


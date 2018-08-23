<?php include '../metabase-int/vendor/autoload.php';


require '../../../inc/includes.php';

Html::header(
    __('Analytics', 'Analytics'), $_SERVER["PHP_SELF"],
    "plugins", "PluginAnalyticsConfig", "client"
);

$config = new PluginAnalyticsConfig();
$config_data = $config->getConfigData();

// The url of the metabase installation
$metabaseUrl = $config_data['url'];
// The secret embed key from the admin settings screen
$metabaseKey = $config_data['key'];

// Any parameters to pass
$params = ['param' => ''];
$metabase = new \Metabase\Embed($metabaseUrl, $metabaseKey);
// Generate the HTML to create an iframe with the embedded dashboard



echo '<script type="text/javascript">

	function loadMetaData (){
		$arr = window.location.href.split("?");
		var dropdownValue = String($("#dropdown_analytics34").val());
		if(dropdownValue){
			//to see type is dashboard or question
			var type = dropdownValue.substr(0 , 3);
			var id = dropdownValue.substr(4 , dropdownValue.length );
			if(type == "did"){
				//load dashboard
				window.location.href = $arr[0]+"?&did="+id;
			}else if(type == "qid"){
				//load question
				window.location.href = $arr[0]+"?&qid="+id;
			}else{
				window.location.href = $arr[0];
			}			
		}
	}
	</script>';




//preselcted dropdown value
$dropdown_value;

if (!empty($_GET['did'])) {
	$dropdown_value = 'did-'.+$_GET['did'];
}elseif (!empty($_GET['qid'])) {
	$dropdown_value = 'qid-'.+$_GET['qid'];
} else {
	$dropdown_value = '';
}


$sync = new PluginAnalyticsAnalyticssync();
$tab = $sync->getSyncList();



$options  = array('value' => 1,
				  'rand' => 34,
				  'on_change' => 'loadMetaData();',
				  'display_emptychoice' => true,
				  'value' => $dropdown_value );

echo '<label>Analytics:&nbsp;&nbsp;</label>';

Dropdown::showFromArray('analytics', $tab ,$options);


echo "<div class='spacer10'></div>";


if (!empty($_GET['did']) && empty($_GET['qid'])) {
	echo $metabase->dashboardIframe((int)$_GET['did'], $params);
}


if (!empty($_GET['qid']) && empty($_GET['did'])) {
	echo $metabase->questionIFrame((int)$_GET['qid'], $params);
}


Html::footer();

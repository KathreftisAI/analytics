# analytics

This plugin integrates Metabase with GLPI.

**Features:**
  * Fetch Metabase dashboards and questions and sync with GLPI.
  * Access Management for dashboards and questions on the basis of Groups or Profiles
  * Integrate Meatabase dashboards and questions into GLPI

**Requirement**
  * Minimum GLPI Version : 9.2.4
  * This plugin requires instance of   [Metabase](https://www.metabase.com/) with existing Admin User and Embedding in other Applications Enabled.

## Installation

  * To connect Metabase to GLPI first install Analytics plugin in Setup → Plugins → Install → Enable. 

  *   Go to Setup → General → click on Analytics tab → configure Metabase url , secret key ,useremail and password as shown in below image:
  
![Analytics Config](https://github.com/Unotechsoftware/analytics/blob/master/screenshot/GLPI_analytics_config.png)


  * Go to Setup → Automatic Actions → Analyticssync → Execute action as shown below:
  
![analytics cron](https://github.com/Unotechsoftware/analytics/blob/master/screenshot/GLPI_analytics_cron.png)

   
  * Go to Tools → Analytics Access as shown below:
  
![Access List](https://github.com/Unotechsoftware/analytics/blob/master/screenshot/GLPI_Analytics_access_list.png)

    
  * Click on any dashboard/question as shown in above image select group or profiles to whom you want to display particular dashboard/question add them as displayed below:
  
![access](https://github.com/Unotechsoftware/analytics/blob/master/screenshot/GLPI_Analytics_access.png)

	 
From above image we can see that "Ticket Master Data" dashboard/question is visble to users of test Group and Super-admin profile.

 * Go to Plugins → Analytics select dashboard/question as shown bleow:
 
![dashboard](https://github.com/Unotechsoftware/analytics/blob/master/screenshot/GLPI_analytics_Dashboard.png)


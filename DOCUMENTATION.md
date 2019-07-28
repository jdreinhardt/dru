DOCUMENTATION
=============

### Installation
DRU runs onto of a standard LAMP stack. (If running on Windows a standard XAMPP install will work for most features. The log export SQL under the Admin page would need to be updated for a Windows file structure.) When intstalling under Linux the following will be dependecies:
* Apache v2.4
* PHP v5.4 and the following modules
    * php-ldap
    * php-mbstring
    * php-mysqlnd (not php-mysql)
    * php-pdo
    * php-soap
    * php-xml
* MariaDB/MySQL v5.5
* cURL

Newer versions may work, but have not been tested, so results may vary.

To start you will need to install <code>dru_scaffold.sql</code> file into MySQL instance. This will create the necessary tables and default settings needed to get the rest of the system configured.

In the webroot folder place the entire <code>diva</code> folder. Update the SQL login credentials to match the instance in <code>/diva/connection/sql_conn.php</code>. Ensure that the entire <code>diva</code> directory is owned by the web server user. Create a <code>logs</code> folder inside of the <code>diva</code> folder and <code>chmod 777 logs/</code> to ensure mysql has rights to write logs to the folder. DRU should now be ready to use. Navigate to <code>http://<SYSTEM_URL>/diva</code> and you should be presented with a login screen. The default admin username and password can be found in the file <code>/diva/admin/adminUser.php</code> These credentials are granted full access, but can be updated to whatever you decide or disabled entirely from the administration page if desired.

In order to maintain an active session with the DIVA system it is recommended to add a cron job to the server hosting the application to automatically renew the API session key. The default API expiration is ~15 minutes after last use, so the cron should be in 15 minute increments for best results. <code>*/15 * * * * curl http://\<dru server\>/diva/admin/getSessionCode.php</code> 

### Configuration
After installation and logging in the user settings should be configured. Select <code>Admin</code> from the navigation bar. On the left side a number of sections should be visible. 

#### Dashboard
Not yet implemented. Will include usage statistics of the system.

#### Features
A series of toggles to turn specific features on and off for the users. Anyone who is an admin will always have rights to features even if disabled. This is also where you can disable the local admin credentials.

#### Restore Paths
Configurations of the available restore locations to the users. Location Name can be any unique string. The Location Path must be a valid UNC path, but must not be unique. 

Group Restore Restrictions is not yet implemented, but will be able to restrict User Groups to specific Restore Locations.

#### Access Control
Group Management is where you can specify the Group Name to be authorized. User Management allows for specifc users to be granted permissions. If a user is under both User and Group Management the User settings will be used.
* Access - authorizes access the system. 
* Details - authorizes access to view the details view from a search result
* Restore - authorizes access to restore content if Allow Restores is an enabled feature
* Archive - authorizes access to archive content if Allow Archives is an enabled feature
* Admin - authorizes full access to the system

#### Active Directory
Specify the configurations to connect to the local AD system for user authentication.

#### DIVA URL
Specify the SOAP API Endpoint and WSDL location. Example versions are installed as part of the <code>dru_scaffold.sql</code>, so you can update those for simpliest results.

#### Logs
Exports the usage logs to a CSV and initates a download to the local machine. 

#### Troubleshooting
Options to resolve common issues in the system. When experiencing sesion id errors use Reset DIVA API Key. The others are for use of cleaning out older entries in the database.

After all settings are configured to desired settings DRU should be ready to use.
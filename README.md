DRU README
===========

DRU (DIVA Resource for Users) is an Oracle DIVArchive resource tool that allows users to interact with the system without the Control UI or other heavy utilities. 

DRU has been writing as an extension of the DIVA SOAP API already supplied with the install of DIVA. Currently no object data is sourced outside of the DIVA database. DRU allows for searches against objects in the database including a detailed view of the object, csv exports of all objects added to the database in a specified timeframe, and restore objects to predefined locations.

----------

Features
----------

### Search
Search is the default and primary usage of DRU. Anyone using the system will be able to search without having to login. Searches are performed against the object title and are case-sensitive. Wildcards are supported in the search, but are not required. If no wildcard is found at the head or tail of the search query they will be automatically appended.

Results are displayed in a table that is sorted by default according to the archive date. All dates and times are UTC. Each column in the table can be sorted through selected the column header. If the Archive ID is selected it will open a new tab containing all the details of the selected object. The details page also includes a searchable list of all files inside of the object. 

### Archive
Currently not available.

### Reports
Rerports is a function that was built to support an external list of objects added to the DIVA system. The export allows a date range to be selected with the oldest date being no more than 90 days previous. After the form is submitted a CSV will be created and a download initiated to the users local machine. The resulting CSV is unsorted, but does contains the ingest time in UTC. All file sizes listed have been normalized to megabytes. 

### Dashboard
Currently not available.

### Restore
Restore allows for the selected object to be restored from the DIVArchive system to a predefined list of destinations. This feature requires that the user be signed in to DRU. All restores can be blocked by an administrator even if the user would normally have access. This can be done in the Admin panel. After a restore has been initiated it is possible to view the status on the page linked. The page URL will allow you to get back to the status at anytime, but is not stored anywhere inside of DRU, and must be managed by the user.

### Admin
The Admin page is only visible to logged in administrators. It features a number of configurations that can be adjusted to allow the system to work in the desired way. For more details on what can be configured, and how they interact with the rest of the system please refer to the documentation.

----------

Current Version: 1.2.0

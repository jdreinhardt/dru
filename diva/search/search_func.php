<?php 

    function parseToTable($response) {
        $recordsFound = 0;
                
        if (sizeof($response->return->objectDetailsList->objectInfos) > 0) {

            tableHeader();

            if (sizeof($response->return->objectDetailsList->objectInfos) == 1) {
                $recordsFound++;

                $obj = $response->return->objectDetailsList->objectInfos;
                $objName = $obj->objectSummary->objectName;
                $objCategory = $obj->objectSummary->objectCategory;
                $objSize = number_format(round(($obj->objectSize / 1024 ), 2), 2, '.', ',');
                $archiveDate = date("d-M-Y H:i:s", substr($obj->archivingDate, 0, 10));
        
                $projectFiles = isProject($obj->filesList);

                tableItem($objName, $objCategory, $objSize, $archiveDate, $projectFiles);
            } else {
                foreach ($response->return->objectDetailsList->objectInfos as $obj) {
                    if (strpos($obj->objectSummary->objectCategory, 'avid') !== FALSE) {
                        //Skip all records coming from the 'avid' Object Categories. This should keep results cleaner
                        continue;
                    } else {
                        $recordsFound++;

                        $objName = $obj->objectSummary->objectName;
                        $objCategory = $obj->objectSummary->objectCategory;
                        $objSize = number_format(round(($obj->objectSize / 1024 ), 2), 2, '.', ',');
                        $archiveDate = date("d-M-Y H:i:s", substr($obj->archivingDate, 0, 10));
                        $projectFiles = isProject($obj->filesList);

                        tableItem($objName, $objCategory, $objSize, $archiveDate, $projectFiles);
                    }
                    
                }
            }
        }
        return $recordsFound;
    }

    function tableHeader() { ?>
        <div align="center" style="width: 100%;">
            <table align='center'>
                <colgroup>
                    <col width="53%">
                    <col width="12%">
                    <col width="5%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>
            <thead>
                <th id="colaid">Archive ID</th>
                <th id="colcat">DIVA Category</th>
                <th id="colsiz">File Size (in MB)</th>
                <th id="coldat">Archive Date</th>
                <th id="colsd">Project Package</th>
            </thead>
    <?php }

    function tableItem($name, $category, $size, $date, $project) {
        echo "\t\t\t<tr>
            \n\t\t\t\t<td>
            <a href=\"details/index.php?archiveID=" . urlencode($name) . "&objectCategory=" . urlencode($category) . "\" class=\"list\" target=\"_blank\">"
            . $name . "</a></td>\n\t\t\t\t<td>" 
            . $category . "</td>\n\t\t\t\t<td>" 
            . $size . "</td>\n\t\t\t\t<td>" 
            . $date . "</td>\n\t\t\t\t<td>" 
            . $project . "</td>\n\t\t\t</tr>\n";
    }

    function isProject($obj) {
        if (is_array($obj)) {
            $projectFiles = "Y (" . sizeof($obj) . " files)";
        } else {
            if (strpos($obj, 'ComponentContainer') !== FALSE) {
                $projectFiles = "Y (Complex)";
            } else {
                $projectFiles = "N";
            }
        }
        return $projectFiles;
    }
?>
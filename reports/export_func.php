<?php   
    function queryDIVA($epochStart, $epochEnd, $fileName, $apiKey) { //returns path of file to download
        ini_set('max_execution_time', 300);
    
        $resultsArray = curlLoopDIVA($epochStart, $epochEnd, $apiKey);

        $file = writeFile($fileName, $resultsArray);
        $filePath = "/diva/reports/" . $fileName;
        return $filePath;
    }

    function writeFile($outFile, $dataArray) {
        $file = fopen($outFile, "w+");
        $count = count($dataArray);
        for ($i=0; $i<$count; $i++) {
            fwrite($file, $dataArray[$i] . PHP_EOL);
        }
        fclose($file);
        return $file;
    }

    function curlLoopDIVA($epochStart, $epochEnd, $apiKey) {
        include_once ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/read_config.php');
        
        $maxSize = '1000';
        $isFirstTime = 'true';
        $listPositions = '<xsd:listPosition></xsd:listPosition>';

        // No longer needed, but keeping so that I don't have to determine the curl headers in the future.
        //$curlHeader = 'curl -v -X POST --header "Content-Type: text/xml --data ';
        $curlEndPoint = $divaInfo['diva_endpoint'];

        $resultsArray = [];

        $done = FALSE;
        while ( !$done ) {

            $objNames = [];
            $objCategories = [];
            $objSources = [];
            $objSizes = [];
            $archiveDates = [];
            $epochDates = [];

            $curlData = '<soapenv:Envelope 
                xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
                xmlns:xsd="http://interaction.api.ws.diva.fpdigital.com/xsd">
                <soapenv:Header/>
                    <soapenv:Body>
                        <xsd:getObjectDetailsList>
                        <xsd:sessionCode>' . $apiKey . '</xsd:sessionCode>
                        <xsd:isFirstTime>' . $isFirstTime . '</xsd:isFirstTime>
                        <xsd:initialTime>' . $epochStart . '</xsd:initialTime>
                        <xsd:listType>1</xsd:listType>
                        <xsd:objectsListType>2</xsd:objectsListType>'
                        . $listPositions . 
                        '<xsd:maxListSize>' . $maxSize . '</xsd:maxListSize>
                        <xsd:objectName>*</xsd:objectName>
                        <xsd:objectCategory>*</xsd:objectCategory>
                        <xsd:mediaName>*</xsd:mediaName>
                        <xsd:levelOfDetail>2</xsd:levelOfDetail>
                        </xsd:getObjectDetailsList>
                    </soapenv:Body>
                </soapenv:Envelope>';

            $curlRequest = curl_init();
            curl_setopt($curlRequest, CURLOPT_URL, $curlEndPoint);
            curl_setopt($curlRequest, CURLOPT_POST, 1);
            curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curlRequest, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($curlRequest, CURLOPT_POSTFIELDS, $curlData);
            $response = curl_exec($curlRequest);
            curl_close($curlRequest);

            $doc = new DOMDocument();
            $doc->loadXML($response);

            $numObjects = $doc->getElementsByTagName('objectName');

            if ( $numObjects->length != 0) {
                $objects = $doc->getElementsByTagName('objectName');
                foreach ($objects as $obj) {
                    array_push($objNames, $obj->nodeValue);
                }
                $objects = $doc->getElementsByTagName('objectCategory');
                foreach ($objects as $obj) {
                    array_push($objCategories, $obj->nodeValue);
                }
                $objects = $doc->getElementsByTagName('objectSource');
                foreach ($objects as $obj) {
                    array_push($objSources, $obj->nodeValue);
                }
                $objects = $doc->getElementsByTagName('objectSize');
                foreach ($objects as $obj) {
                    $objSize = number_format(round(($obj->nodeValue / 1024 ), 2), 2, '.', '');
                    array_push($objSizes, $objSize);
                }
                $objects = $doc->getElementsByTagName('archivingDate');
                foreach ($objects as $obj) {
                    $objDate = date("d-M-Y H:i:s", substr($obj->nodeValue, 0, 10));
                    array_push($archiveDates, $objDate);
                    array_push($epochDates, $obj->nodeValue);
                }

                $resultString = parseItemReturn($objNames, $objCategories, $objSources, $objSizes, $archiveDates, $epochDates, $epochEnd);
                if ( is_string($resultString) ) {
                    echo $resultString;
                } else {
                    foreach ($resultString as $result) {
                        array_push($resultsArray, $result);
                    }
                }

                $done = FALSE;
                //prep next loop
                $isFirstTime = 'false';
                $listPositions = "";
                $objects = $doc->getElementsByTagName('listPosition');
                foreach ($objects as $obj) {
                    $listPositions .= '<xsd:listPosition>' . $obj->nodeValue . '</xsd:listPosition>';
                }
            } else {
                $done = TRUE;
            }
        }
        return $resultsArray;
    }

    function parseItemReturn($objNames, $objCategories, $objSources, $objSizes, $archiveDates, $epochDates, $epochEnd) {
        if (count($objNames) < 1) {
            return "";
        } else {
            $objArray = [];
            $count = count($objNames);
            for ( $i=0; $i<$count; $i++ ) {
                if (strpos($objCategories[$i], 'avid') !== FALSE ) {
                    continue;
                } else {
                    if ( $epochDates[$i] > $epochEnd ) {
                        continue;
                    } else {
                        //Converts all file names to ASCII to prevent UTF-8 characters in file name
                        $tempName = $objNames[$i];
                        $encoding = mb_detect_encoding( $tempName, "auto" );
                        $target = str_replace( "?", "[question_mark]", $tempName );
                        $target = mb_convert_encoding( $target, "ASCII", $encoding);
                        $target = str_replace( "?", "", $target );
                        $target = str_replace( "[question_mark]", "?", $target );

                        $tmp = "\"" . $target . "\",\"" . $objCategories[$i] . "\"," . $objSizes[$i] . "," . $archiveDates[$i] . "," . $objSources[$i];
                        array_push($objArray, $tmp);
                    }
                }
            }
            if (count($objArray) < 1) {
                return "";
            } else {
                return $objArray;
            }
        }
    }
?>
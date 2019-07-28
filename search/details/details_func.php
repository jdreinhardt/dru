<?php
    function build_tree($path_list) {
        $path_tree = array();
        foreach ($path_list as $path) {
            $list = split('\\\\', $path);
            $last_dir = &$path_tree;
            foreach ($list as $dir) {
                $last_dir =& $last_dir[$dir];
            }
            $last_dir['__title'] = $list[sizeof($list)-1];
        }
        return $path_tree;
    }

    function build_list($tree, $prefix = '') {
        $ul = '';
        foreach ($tree as $key => $value) {
            $li = '';
            if (is_array($value)) {
                if (array_key_exists('__title', $value)) {
                    $li .= $value['__title'];
                } else {
                    $li .= "$prefix$key";
                }
                $li .= build_list($value, '');
                $ul .= strlen($li) ? "<li>$li</li>" : '';
            }
        }
        return strlen($ul) ? "<ul>$ul</ul>" : '';
    }

    function queryDIVAComplex($objName, $objCategory, $apiKey) {
        include ($_SERVER['DOCUMENT_ROOT'] . '/diva' . '/connection/soap_conn.php');

        $startIndex = 1;
        $batchSize = 1000;
        $listType = 2; //returns both files and folders

        $fileList = [];

        $done = FALSE;
        while( !$done ) {

            $requestParams = array(
                'sessionCode' => $apiKey,
                'objectName' => $objName,
                'objectCategory' => $objCategory,
                'startIndex' => $startIndex,
                'batchSize' => $batchSize,
                'listType' => $listType,
                'options' => '',
            );

            $response = $client->getFilesAndFolders($requestParams);
            $obj = $response->return->divaFilesAndFolders;
            $startIndex = $obj->nextStartIndex;

            $fileObjs = $obj->fileAndFolderList;
            foreach ($fileObjs as $file) {
                array_push($fileList, $file->fileOrFolderName);
            }

            if ($startIndex < 0) {
                $done = TRUE;
            }
        }
        echo "Total files: " . count($fileList);
        return $fileList;
    }
?>
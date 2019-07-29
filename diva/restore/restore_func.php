<?php
    function restoreOptions($archiveID, $objectCategory, $restoreArrayNames, $restoreArrayPaths) { ?>
        <div align="center" style="width: 100%;">
            <form id="restore" action="index.php" method="get" onsubmit="validate();">
                <input type="hidden" name="archiveID" value="<?php echo $archiveID ?>"/>
                <h3>Object Title (Archive ID)</h3>
                <p><?php echo $archiveID ?></p>
                <input type="hidden" name="objectCategory" value="<?= $objectCategory ?>"/>
                <h3>Object Category</h3>
                <p><?php echo $objectCategory ?></p>
                <h3>Restore Location</h3>
                <select name="restorePath"> <?php
                    for ($i=0; $i<count($restoreArrayNames);$i++) {
                        $item = $restoreArrayNames[$i];
                        echo '<option value="' . $restoreArrayPaths[$i] . '" title="' . $restoreArrayPaths[$i] . '">' . $item . '</option>';
                    } ?>
                </select><br><p></p><p></p>

                <div style="width:50%; margins: 0 auto; text-align: center;">
                <div style="display:inline-block;" id="submit">
                <p><input type="submit" onsubmit="validate();" value="Submit"></p>
                </div>

                <div id="buttonreplacement" style="display:none;">
                <img src="http://<?= $_SERVER['SERVER_NAME'] . '/diva' ?>/assets/images/loading.gif" width="5%" height="5%" alt=\"loading...\">
                </div>
                <div>
            </form>
        </div> <?php
    } 

?>
<?php
    function button($name, $configs) { 
        $value = $configs[$name];
        ?>
        <div style="display: inline-block; float: right; margin-top: 30px; width: 110px;">
            <div class="onoffswitch">
                <input type="checkbox" name="<?= $name ?>" class="onoffswitch-checkbox" id="<?= $name ?>" 
                    <?php if ($value == 'true') { echo "checked"; } ?> onchange="apply(this)"  >
                <label class="onoffswitch-label" for="<?= $name ?>">
                    <span class="onoffswitch-inner"></span>
                    <span class="onoffswitch-switch"></span>
                </label>
            </div>
        </div> <?php
    }

    function configParameter($name, $description, $keyname, $configs) { ?>
        <div align="center" style="width: 90%;">
            <div style="display: inline-block; width: 80%; float: left; text-align: left;">
                <h3><?= $name ?></h3>
                <p><?= $description ?></p>
            </div>
            <?php button($keyname, $configs) ?>
        </div> <?php
    }

?>
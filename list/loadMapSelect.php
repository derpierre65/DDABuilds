<div class="form-group">
    <label for="mapselect">Map:</label>
    <input class="form-control" id="mapselect" list="maps" name="maps">
    <datalist id="maps">
        <?php
        if ($default) {
            echo '<option value="0">Any</option>';
        }

        $maps = Maps::getAllMaps($oDBH);
        foreach ($maps as $map) {
            $mapId = $map->getID();
            $mapName = $map->getData('name');

            echo '<option value="' . $mapId . '">' . $mapName . '</option>';
        }
        ?>
    </datalist>
</div>
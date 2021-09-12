<?php $edit = isset($tur) ?>
<div class="row">
    <div class="col-2"></div>
    <div class="col-8">
        <?php
            if ($edit) {
                echo '<h3 style="text-align: center;">Rediger<br /><small>'.$tur['num_id'].' - '.$tur["name"].'</small></h3>';
            } else {
                echo '<h3 style="text-align: center;">Legg til tur</h3>';
            }
        ?>
        <div class="white-table">
            <form id="add_form" action="" method="POST">
                <table>
                    <tr>
                        <td style='width: 132px;'><strong>Navn:</strong>
                        <td><input type="text" name="name" style="width: 100%;" class="input_edit" <?php if ($edit) {echo 'value="'.$tur['name'].'"';}?> required/>
                        <?php if (isset($error["name"])) {echo "<span class='add_form_error'>".$error["name"]."</span>";} ?></td>
                    </tr>
                    <tr>
                        <td><strong>Tags:</strong></td>
                        <td>
                            <div class="tag_container">
                            <?php
                            $options = ['sort' => ['name' => 1]];
                            $tags = $collection_meta->find(["type" => "tag"], $options);
                            if ($edit) {
                                $tur_tags = bson_array_to_php_array($tur['tags']);
                            }
                            foreach($tags as $tag) {
                                echo "<div class=\"tag_item\">";
                                echo "<input type='checkbox' name=\"tags[]\" value=\"" .$tag['name']. '"';
                                if ($edit && in_array($tag['name'], $tur_tags)) {
                                    echo "checked";
                                }
                                echo ">".$tag['name'];
                                echo "</div>";
                            }
                            ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Beskrivelse:</strong></td>
                        <td><textarea name="description" form="add_form" class="textarea_edit input_edit"><?php if ($edit) {echo $tur['description'];}?></textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Si på starten:</strong></td>
                        <td><textarea name="notes_start" form="add_form" class="textarea_edit input_edit"><?php if ($edit) {echo $tur['notes_start'];}?></textarea></td>
                    <tr>
                        <td><strong>Fokuspunkt:</strong></td>
                        <td><textarea name="focus" form="add_form" class="textarea_edit input_edit"><?php if ($edit) {echo $tur['focus'];}?></textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Youtube-link:</strong></td>
                        <td><input type="text" name="youtube" style="width: 100%;" class="input_edit" placeholder="a youtube link like: https://www.youtube.com/watch?v=dQw4w9WgXcQ" <?php if ($edit) {echo 'value="https://www.youtube.com/watch?v='.$tur['youtube'].'"';}?>/>
                        <?php if (isset($error["youtube"])) {echo "<br /><span class='add_form_error'>".$error["youtube"]."</span>";} ?></td>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Undervisningstid:</strong></td>
                        <td><input type="text" name="time" class="input_edit" <?php if ($edit) {echo 'value="'.$tur['time'].'"';}?>/> minutter</td>
                    </tr>
                    <tr>
                        <td><strong>Passende bpm:</strong></td>
                        <td><input type="text" name="bpm" class="input_edit" <?php if ($edit) {echo 'value="'.$tur['bpm'].'"';}?>/></td>
                    </tr>
                    <tr>
                        <td><strong>Bolk:</strong></td>
                        <td>
                            <div class="bolk_container">
                                <?php
                                $bolks = $collection_meta->find(["type" => "bolk"]);
                                if ($edit) {
                                    $tur_bolks = bson_array_to_php_array($tur['bolknb']);
                                }
                                foreach($bolks as $bolk) {
                                    echo "<div class=\"bolk_item\">";
                                    echo "<input type='checkbox' name=\"bolknb[]\" value=\"" .$bolk['name']. '"';
                                    if ($edit && in_array($bolk['name'], $tur_bolks)) {
                                        echo "checked";
                                    }
                                    echo ">".$bolk['name'];
                                    echo "</div>";
                                }
                                ?>
                            </div>

                        </td>
                    </tr>
                    <tr>
                        <td><strong>Kommentarer:</strong></td>
                        <td><textarea name="notes" class="textarea_edit input_edit"><?php if ($edit) {echo $tur['notes'];}?></textarea></td>
                    </tr>
                    <tr style='display: none'>
                        <td><strong>Deling (?):</strong></td>
                        <td>
                            <input type="radio" name="deling" value="ja" <?php if ($edit && $tur['deling'] == 'ja') {echo "checked"; }?>/> ja,&nbsp;&nbsp;
                            <input type="radio" name="deling" value="nei" <?php if ($edit && $tur['deling'] == 'nei') {echo "checked"; }?>/> nei,&nbsp;&nbsp;
                            <input type="radio" name="deling" value="not_spesified" <?php if ($edit == FALSE || ($tur['deling'] != 'ja' && $tur['deling'] != 'nei')) {echo "checked"; }?> /> ikke spesifisert
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><input type="text" name="status" class="input_edit" <?php if ($edit) {echo 'value="'.$tur['status'].'"';}?>/></td>
                    </tr>
                    <tr>
                        <td><strong>Helgekurs:</strong></td>
                        <td><input type="text" name="helgekurs" class="input_edit" <?php if ($edit) {echo 'value="'.$tur['helgekurs'].'"';}?>/></td>
                    </tr>
                    <tr>
                        <td><strong>Forbedringer:</strong></td>
                        <td><textarea name="improvements" class="textarea_edit input_edit"><?php if ($edit) {echo $tur['improvements'];}?></textarea></td>
                    </tr>
                    <tr>
                        <td><strong>Nivå:</strong></td>
                        <td>
                            <select name="level">
                                <option value="grunnleggende" <?php if ($edit && $tur['level'] == 'grunnleggende') {echo "selected"; }?>>grunnleggende</option>
                                <option value="enkelt" <?php if ($edit && $tur['level'] == 'enkelt') {echo "selected"; }?>>enkelt</option>
                                <option value="middels" <?php if ($edit && $tur['level'] == 'middels') {echo "selected"; }?>>middels</option>
                                <option value="vanskelig" <?php if ($edit && $tur['level'] == 'vanskelig') {echo "selected"; }?>>vanskelig</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Egnet for:</strong></td>
                        <td>
                            <div class="suitable_container">
                            <?php
                            $suitables = $collection_meta->find(["type" => "suitable"], ['sort' => ['name' => 1]]);
                            if ($edit) {
                                $tur_suitable = bson_array_to_php_array($tur['suitable']);
                            }
                            foreach($suitables as $suitable) {
                                echo "<div class=\"suitable_item\">";
                                echo "<input type='checkbox' name=\"suitables[]\" value=\"" .$suitable['name']. '"';
                                if ($edit && in_array($suitable['name'], $tur_suitable)) {
                                    echo "checked";
                                }
                                echo ">".$suitable['name'];
                                echo "</div>";
                            }
                            ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <?php
                        if ($edit) {
                            echo "<td><strong>Dato undervist:</strong></td>";

                            $tur_dato = bson_array_to_php_array($tur['dato_undervist']);
                            $datoer = "";
                            foreach($tur_dato as $dato) {
                                $d = $dato->toDateTime();
                                if ($datoer == "") {
                                    $datoer = $d->format("Y-m-d");
                                } else {
                                    $datoer = $datoer . "," . $d->format("Y-m-d");
                                }
                            }
                        echo '<td><input type="text" name="date" class="input_edit" value="'.$datoer.'"> (kommaseparert liste over datoer)</td>';
                        } else {
                            echo '<td><strong>Sist undervist:</strong></td>';
                            echo '<td><input type="date" name="date" class="input_edit" value='.date("Y-m-d").' placeholder="yyyy-mm-dd">';
                            echo '(<input type="checkbox" name="date_none" value="1">ikke undervist ennå)</td>';
                        }
                        ?>
                    </tr>
                    <tr>
                        <?php
                        echo '<td><strong>Relaterte turer:</strong></td>';
                        if ($edit) {
                            $tur_related = bson_array_to_php_array($tur['related']);
                            $related_mun_ids = $collection->find(
                                [
                                    '_id' => [
                                        '$in' => $tur_related
                                    ]
                                ]
                            );
                            $related_list = "";
                            foreach ($related_mun_ids as $doc) {
                                if ($related_list == "") {
                                    $related_list = $doc['num_id'];
                                } else {
                                    $related_list = $related_list . ", " . $doc['num_id'];
                                }
                            }
                            echo '<td><input type="text" name="related" class="input_edit" value="'.$related_list.'" placeholder="f.eks: 1, 5, 223, ..." /> (kommaseparert liste over nummer til relaterte turer)</td>';
                        } else {
                            echo '<td><input type="text" name="related" class="input_edit" placeholder="f.eks: 1, 5, 223, ..." /> (kommaseparert liste over nummer til relaterte turer)</td>';
                        }
                        ?>
                    </tr>
                    <tr>
                        <td></td>
                        <?php
                        if ($edit) {
                            echo '<td><input style="float: right;" type="submit" name="submit" value="lagre" />';
                        } else {
                            echo '<td style="float: right;"><input type="submit" name="submit" value="legg til tur" /></td>';
                        }
                        ?>
                    </tr>
                </table>
            </form>
        </div>
        <div style='margin-top: 2em;'>
            <?php
            if ($edit) {
                echo "Slett turen permanent (kan ikke angres): [<a style='color: red;' href='delete.php?id=" . $id . "' onclick=\"return confirm('Er du sikker på at du vil slette: " . $tur['num_id'] . " - " . $tur['name'] . "?');\">slett</a>]";
            }
            ?>
        </div>
    </div>
    <div class="col-2"></div>
</div>

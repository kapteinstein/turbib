<?php
$name = NULL;
$description = NULL;
$notes_start = NULL;
$focus = NULL;
$youtube = NULL;
$time = NULL;
$bpm = NULL;
$bolknb = NULL;
$notes = NULL;
$deling = NULL;
$status = NULL;
$helgekurs = NULL;
$improvements = NULL;
$level = NULL;

$tags = NULL;
$sist_undervist = NULL;
$date_none = NULL;
$related_final = [];
$suitable = NULL;

if (isset($_POST['name'])) { $name = $_POST['name']; }
if (isset($_POST['description'])) { $description = $_POST['description']; }
if (isset($_POST['notes_start'])) { $notes_start = $_POST['notes_start']; }
if (isset($_POST['focus'])) { $focus = $_POST['focus']; }
if (isset($_POST['youtube'])) { $youtube = $_POST['youtube']; }
if (isset($_POST['time'])) { $time = $_POST['time']; }
if (isset($_POST['bpm'])) { $bpm = $_POST['bpm']; }
if (isset($_POST['bolknb'])) { $bolknb = $_POST['bolknb']; }
if (isset($_POST['notes'])) { $notes = $_POST['notes']; }
if (isset($_POST['deling'])) { $deling = $_POST['deling']; }
if (isset($_POST['status'])) { $status = $_POST['status']; }
if (isset($_POST['helgekurs'])) { $helgekurs = $_POST['helgekurs']; }
if (isset($_POST['improvements'])) { $improvements = $_POST['improvements']; }
if (isset($_POST['level'])) { $level = $_POST['level']; }

if (isset($_POST['tags'])) { $tags = $_POST['tags']; }
if (isset($_POST['date'])) { $last_teached = $_POST['date']; }
if (isset($_POST['date_none'])) { $date_none = $_POST['date_none']; }
if (isset($_POST['suitables'])) { $suitable = $_POST['suitables']; }

if (isset($_POST['related'])) {
    $related_final = [];
    $related_raw = $_POST['related'];
    $related = str_replace(",", " ", $related_raw);
    $related = explode(" ", $related);
    $related = array_map('trim', $related);
    $related = array_values(array_filter($related));
    foreach ($related as $value) {
        $id = $collection->findOne(['num_id' => (int) $value]);
        array_push($related_final, $id["_id"]);
    }
}

if (isset($_POST['date'])) {
    $date_final = [];
    $date_raw = $_POST['date'];
    $date = str_replace(",", " ", $date_raw);
    $date = explode(" ", $date);
    $date = array_map('trim', $date);
    $date = array_values(array_filter($date));
    sort($date);
    if (sizeof($date) != 0) {
        $sist_undervist = new MongoDB\BSON\UTCDateTime(new DateTime(max($date)));
        foreach ($date as $value) {
            $bson_date = new MongoDB\BSON\UTCDateTime(new DateTime($value));
            array_push($date_final, $bson_date);
        }
    }
}



$youtube_token = explode("?v=", $youtube);
$youtube = substr(end($youtube_token), 0, 11);

$deling = ($deling != "ja" && $deling != "nei") ? NULL : $deling;

$query = [];
$query['name'] = $name;
$query['tags'] = $tags;
$query['description'] = $description;
$query['notes_start'] = $notes_start;
$query['focus'] = $focus;
$query['youtube'] = $youtube;
$query['time'] = $time;
$query['bpm'] = $bpm;
$query['bolknb'] = $bolknb;
$query['notes'] = $notes;
$query['deling'] = $deling;
$query['status'] = $status;
$query['helgekurs'] = $helgekurs;
$query['improvements'] = $improvements;
$query['level'] = $level;
$query['suitable'] = $suitable;
$query['related'] = $related_final;
$query['dato_undervist'] = array();
$query['sist_undervist'] = $sist_undervist;

$ikke_undervist = (sizeof($date) == 0) || ($date_none == 1);
if ($ikke_undervist == FALSE) {
    $query['dato_undervist'] = $date_final;
}

$error = [];
if ($name == "") {
    $error["name"] = "må ha et navn";
}

/* buggy
if (strlen($youtube) > 0 && strlen($youtube) != 11) {
    $error["youtube"] = "er link korrekt?";
}
*/

<?php
    require_once('../include/session.php');
    include_once('../include/db.php');

    $id = isset($_GET['id']) ? $_GET['id'] : NULL;
    $bid = NULL;
    $tur = NULL;
    
    if ($id != NULL) {
        try {
            $bid = new MongoDB\BSON\ObjectId("$id");
        } catch (\Throwable $th) {
            $bid = NULL;
        }
    }


    if ($bid != NULL) {
        try {
            $tur = $collection->findOne(['_id' => new MongoDB\BSON\ObjectId("$id")]);
        } catch (\Throwable $th) {
            $tur = NULL;
        }
    }


    if ($id != NULL && $tur == NULL) {
        header("Location: /?id=". $id);
        die();
    }


    $options = ['sort' => ['_id' => -1]];
    if ($id == NULL && $tur == NULL) {
        $logs = $collection_logs->find([], $options);
        $logs_count = $collection_logs->count([], $options);       
    } else {
        $logs = $collection_logs->find(['tur_id' => $bid], $options);
        $logs_count = $collection_logs->count(['tur_id' => $bid], $options);
    }
?>

<?php
    $title = "Turbibliotek :: info";
    include('../snippets/head.php');
?>

<body>
<div class="container">
    <?php include('../snippets/header.php'); ?>

    <div class="row">
        <div class="col-1"></div>
        <div class="col-10 gray-table">
        <?php 
            if ($tur != NULL) {
                echo '<h3 style="text-align: center;">Logg<br /><small>'.$tur['num_id'].' - '.$tur["name"].'</small></h3>';
            } else {
                echo '<h3 style="text-align: center;">Logg<br /><small>Alle</small></h3>';
            }
        ?>
        <?php
            if ($logs_count > 0) {
                echo '<table>';
                echo '<tr>';
                echo '  <th>Dato</th>';
                echo '  <th>Tur</th>';
                echo '  <th>Navn</th>';
                echo '  <th>Info</th>';
                echo '</tr>';

                foreach ($logs as $entry) {
                    $user = $collection_user->findOne(['_id' => $entry['user_id']]);
                    $tur_entry = $collection->findOne(['_id' => $entry['tur_id']]);

                    if ($tur_entry != NULL) {
                        $name = $tur_entry['num_id'].' - '.$tur_entry['name'];
                        $name = strlen($name) > 50 ? substr($name,0,50)."..." : $name;
                    } else {
                        $name = "<span style='color: lightgray'>tur slettet</span>";
                    } 

                    echo '<tr>';
                    echo '  <td>'.date("Y-M-d", $entry['_id']->getTimestamp()).'</td>';
                    if ($tur_entry != NULL) {
                        echo '  <td><a href="/log.php?id='.$entry['tur_id'].'">'.$name.'</td>';
                    } else {
                        echo '  <td>'.$name.'</td>';
                    }
                    if ($user == NULL) {
                        echo '<td><span style="text-align: center;color: lightgray"><small>bruker slettet</small><span></td>';
                    } else {
                        echo '  <td>'.$user['name'].'</td>';
                    }
                    echo '  <td>'.$entry['type'].'</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<div style="text-align: center;"><i>Loggen er tom</i></div>';
            }
        ?>
        <?php 
            if ($tur != NULL && $id != NULL) {
                echo '<div style="text-align: center; margin-top: 4em;">';
                echo '  [<a href="/log.php">Vis alle</a>]';
                echo '  &nbsp;&nbsp;[<a href="/?id='.$id.'">Gå til tur</a>]';
                echo '</div>';
            }
        ?>
        </div>
        <div class="col-1"></div>
    </div>
</div>

<div style="height: 100px;"></div>

</body>
</html>

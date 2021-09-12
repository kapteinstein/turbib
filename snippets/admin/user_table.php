<?php
    if ($_SERVER['REQUEST_URI'] == '/admin/user_table.php') {  // should remove this outside root
        http_response_code(404);
        die();
    }
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == FALSE) {
        http_response_code(404);
        die();
    }
?>

<div class="row">
    <div class="col-12 gray-table">
        <h3>Oversikt over aktive brukere</h3>
        <table>
        <tr>
            <th>Email</th>
            <th>Navn</th>
            <th>Admin</th>
            <th style="width: 12ch;">Opprettet</th>
            <th style="width: 12ch;">Sist login</th>
            <th style="width: 20ch;">Verktøy</th>
        </tr>
        <?php
        $users = $collection_user->find(['active' => 1, 'registered' => 1]);
        foreach ($users as $user) {
            echo '<tr>';
            echo '<td>'.$user['email'].'</td>';
            echo '<td>'.$user['name'].'</td>';
            if ($user['is_admin'] == 1) {
                echo '<td><i>ja</i></td>';
            } else {
                echo '<td></td>';
            }
            echo '<td>'.date("Y-M-d", $user['_id']->getTimestamp()).'</td>';
            if ($user['last_login'] != NULL) {
                echo '<td>'.$user['last_login']->toDateTime()->format('Y-M-d').'</td>';
            } else {
                echo '<td style="text-align: center;color: lightgray"><small>n/a</small></td>';
            }
            echo '<td style="text-align: center;"><small>';
            if ($user['_id'] != $_SESSION['user_id']) {
                echo '[<a href=admin_toggle_disable.php?user_id='.$user['_id'].'>disable</a>] ';
                echo '[<a href=admin_toggle_admin.php?user_id='.$user['_id'].'>toggle admin</a>]';
            } else {
                echo '<span style="color: lightgray">n/a</span>';
            }
            echo '<small></td>';
            echo '</tr>';
        }
        ?>
        </table>
    </div>
</div>

<div style="height: 2em;"></div>

<div class="row">
    <div class="col-12 gray-table">
        <h3>Oversikt over deaktiverte brukere</h3>
        <p>Deaktiverte brukere har ikke mulighet til å logge inn men vil fortsatt eksistere i systemet.
        Dette kan være hensiktsmessig mtp loggføring av endringer på turer, eller dersom brukeren har tatt
        pause fra Swing, men vil komme tilbake senere. Deaktiverte brukere kan slettes permanent fra systemet.</p>

        <?php
        $users_count = $collection_user->count(['active' => 0, 'registered' => 1]);
        $users = $collection_user->find(['active' => 0, 'registered' => 1]);
        if ($users_count > 0) {
            echo '<table>';
            echo '<tr>';
            echo '    <th>Email</th>';
            echo '    <th>Navn</th>';
            echo '    <th>Admin</th>';
            echo '    <th style="width: 12ch;">Opprettet</th>';
            echo '    <th style="width: 12ch;">Sist login</th>';
            echo '    <th style="width: 20ch;">Verktøy</th>';
            echo '</tr>';
            foreach ($users as $user) {
                echo '<tr>';
                echo '<td>'.$user['email'].'</td>';
                echo '<td>'.$user['name'].'</td>';
                if ($user['is_admin'] == 1) {
                    echo '<td><i>ja</i></td>';
                } else {
                    echo '<td></td>';
                }
                echo '<td>'.date("Y-M-d", $user['_id']->getTimestamp()).'</td>';
                if ($user['last_login'] != NULL) {
                    echo '<td>'.$user['last_login']->toDateTime()->format('Y-M-d').'</td>';
                } else {
                    echo '<td style="text-align: center;color: lightgray"><small>n/a</small></td>';
                }
                echo '<td style="text-align: center;"><small>';
                if ($user['_id'] != $_SESSION['user_id']) {
                    echo '[<a href=admin_toggle_disable.php?user_id='.$user['_id'].'>enable</a>] ';
                    echo '[<a href=admin_delete_user.php?user_id='.$user['_id'].'>delete</a>] ';
                } else {
                    echo '<span style="color: lightgray">n/a</span>';
                }
                echo '<small></td>';
                echo '</tr>';
            }
        } else {
            echo '<i>ingen er deaktivert</i>';
        }
        ?>
        </table>
    </div>
</div>

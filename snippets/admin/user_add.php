<?php
    if ($_SERVER['REQUEST_URI'] == '/admin/user_add.php') {  // should remove this outside root
        http_response_code(404);
        die();
    }

    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] == FALSE) {
        http_response_code(404);
        die();
    }



?>

<div class="row">
    <div class="col-6">
        <h3>Legg til bruker</h3>
        <p>En e-mail vil bli sendt til den nye brukeren med en link for registrering.
        Admin ordnes etter at brukeren er registrert.</p>

        <form action="admin_user_add_post.php" method="post" autocomplete="off">
            <table style="padding: 0.5em; border: 1px solid #e7e7e7;">
                <tr>
                    <td>Email:</td>
                    <td><input style="width: 32ch;" type="email" name="new_user_email" placeholder="email" /></td>
                </tr>
                <tr>
                    <td>
                    <?php
                        if (isset($_SESSION['admin:register:status']) && $_SESSION['admin:register:status'] == 'error') {
                            echo "<span style='color: red'>" . $_SESSION['admin:register:status_msg'] . "</span>";
                            unset($_SESSION['admin:register:status']);
                            unset($_SESSION['sadmin:register:tatus_msg']);
                        } else if (isset($_SESSION['admin:register:status']) && $_SESSION['admin:register:status'] == 'success') {
                            echo "<span style='color: darkgreen'>" . $_SESSION['admin:register:status_msg'] . "</span>";
                            unset($_SESSION['admin:register:status']);
                            unset($_SESSION['sadmin:register:tatus_msg']);
                        }
                    ?>
                    </td>
                    <td style="float: right;"><input type="submit" name="submit" value="Legg til" /></td>
                </tr>
            </table>
        </form>

    </div>
    <div class="col-6 gray-table">
        <h3>Pending</h3>
        <p>Oversikt over brukere som har mottat en invitasjon til å registrere seg,
        men som ikke har benyttet seg av denne ennå.</p>

        <?php
        $users_count = $collection_user->count(['active' => 0, 'registered' => 0]);
        $users = $collection_user->find(['active' => 0, 'registered' => 0]);

        if ($users_count > 0) {
            echo '<table>';
            echo '<tr>';
            echo '<th>email</th>';
            echo '<th>opprettet</th>';
            echo '<th>verktøy</th>';
            echo '</tr>';
            foreach ($users as $user) {
                echo '<tr>';
                echo '<td>'.$user['email'].'</td>';
                echo '<td>'.date("Y-M-d", $user['_id']->getTimestamp()).'</td>';
                echo '<td style="text-align: center;"><small>';
                echo '[<a href=admin_delete_user.php?user_id='.$user['_id'].'>delete</a>] ';
                echo '<small></td>';
                echo '</tr>';
            }
        } else {
            echo '<i>ingen er pending</i>';
        }
        ?>
        </table>
    </div>
</div>

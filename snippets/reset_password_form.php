<form action="/reset_password.php" method="post" autocomplete="disabled">
    <input type="text" name="key" value=<?php echo '"'.$key.'"'; ?> hidden readonly>
    <table style="padding: 0.5em; margin: auto; margin-top: 1em; border: 1px dotted #333;">
        <tr>
            <td>Email:</td>
            <td><input style="width: 100%" name="username" type="email" value=<?php echo '"'.$user['email'].'"'; ?> readonly/></td>
        </tr>
        <tr>
            <td>Passord:</td>
            <td><input style="width: 32ch" autocomplete="disabled" type='password' name='new_passwd_1' placeholder='passord' /></td>
        </tr>
        <tr>
            <td>Gjenta passord:</td>
            <td><input style="width: 32ch" autocomplete="disabled" type='password' name='new_passwd_2' placeholder='passord' /></td>
        </tr>
        <tr>
            <td>
            <?php
                if (isset($_SESSION['status']) && $_SESSION['status'] == 'error') {
                    echo "<span style='color: red'>" . $_SESSION['status_msg'] . "</span>";
                    unset($_SESSION['status']);
                    unset($_SESSION['status_msg']);
                } else if (isset($_SESSION['status']) && $_SESSION['status'] == 'success') {
                    echo "<span style='color: darkgreen'>" . $_SESSION['status_msg'] . "</span>";
                    unset($_SESSION['status']);
                    unset($_SESSION['status_msg']);
                }
            ?>
            </td>
            <td style="text-align: right"><input type="submit" name="submit" value="reset passord"></td>
        </tr>
    </table>
</form>
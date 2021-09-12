<form action="/reset_password.php" method="post" autocomplete="disabled">
    <table style="padding: 0.5em; margin: auto; margin-top: 1em; border: 1px dotted #333;">
        <tr>
            <td>Email:</td>
            <td><input style="width: 32ch" autocomplete="disabled" type='email' name='email' placeholder='email' /></td>
        </tr>
        <tr>
            <td>
            <?php
                if (isset($_SESSION['reset:status']) && $_SESSION['reset:status'] == 'success') {
                    echo "<span style='color: darkgreen'>" . $_SESSION['reset:status_msg'] . "</span>";
                    unset($_SESSION['reset:status']);
                    unset($_SESSION['reset:status_msg']);
                }
            ?>
            </td>
            <td style="text-align: right"><input type="submit" name="submit" value="reset"></td>
        </tr>
    </table>
</form>
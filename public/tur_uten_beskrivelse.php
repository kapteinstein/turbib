<?php
include_once("../include/session.php");
include_once("../include/db.php");
?>

<?php
$turer = $collection->find(
    [
        "description" => [
            '$in' => [
                "",
                NULL
            ]
        ]
    ],
    [
        "project" => [
            "_id" => 1,
            "num_id" => 1,
            "name" => 1,
        ]
    ]
);
?>


<?php
  $title = "Turbibliotek :: tur uten beskrivelse";
  include('../snippets/head.php');
?>
<body>
<div class="container">
    <?php include('../snippets/header.php'); ?>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8 gray-table">
            <h3 style="text-align: center">Turer uten beskrivelse</h3>
            <p>Disse turene har ikke en registrert beskrivelse i databasen.
                Legg gjerne inn en beskrivelse på turen for senere referanse.</p>
            <table>
            <?php
                foreach ($turer as $tur) {
                    $id = (string) $tur['_id'];
                    echo '<tr><td><small>[<a href="edit.php?id='.$id.'">rediger</a>]</small></td>';
                    echo '<td style="text-align: right">'.$tur['num_id'].'</td>';
                    echo '<td><a  style="text-decoration: none;" href="/?id='.$id.'">'. $tur['name'].'</a></td></tr>';
                }
            ?>
            </table>
        </div>
        <div class="col-2"></div>
    </div>
</div>
<div style="height: 100px;"></div>
</body>
</html>

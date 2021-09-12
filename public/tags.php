<?php
    require_once('../include/session.php');
    require_once('../include/db.php');

    $title = "Turbibliotek :: Tags";
    include('../snippets/head.php');
?>

<body>

<div class="container">
    <?php include('../snippets/header.php'); ?>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <h3 style="text-align: center;">Liste over tags</h3>
            <p>Her er en oversikt over alle tags som ligger i systemet.
            På sikt er tanken at det skal være mulig å legge til og endre tags som er lagret i databasen.
            Klikk på 'vis turer' for å se turer med den tagen.</p>
            <div class="white-table">
                <table>
                <?php
                    $tag_objs = $collection_meta->find(["type" => "tag"]);
                    $tags = [];
                    foreach ($tag_objs as $tag) {
                        array_push($tags, $tag['name']);
                    }
                    sort($tags);
                    $counter = sizeof($tags);
                    $len = ceil(sizeof($tags)/2);
                    for ($i=0; $i < $len; $i++) {
                        echo '<tr>';
                        echo '<td style="text-align: right"><small>[<a href="/?t='.$tags[$i].'&tm=any">vis turer</a>]</small></td>';
                        echo '<td>'.$tags[$i].'</td>';
                        $counter--;
                        if ($counter != 0) {
                            echo '<td style="text-align: right"><small>[<a href="/?t='.$tags[$i+$len].'&tm=any">vis turer</a>]</small></td>';
                            echo '<td>'.$tags[$i+$len].'</td>';
                        } else {
                            echo '<td></td><td></td>';
                        }
                        echo '</tr>';
                        $counter--;
                    }
                ?>
                </table>
            </div>
        </div>
        <div class="col-2"></div>
    </div>
</div>

<div style="height: 100px;"></div>


</body>
</html>
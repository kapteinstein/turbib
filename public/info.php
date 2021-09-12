<?php
include_once("../include/session.php");
?>

<?php
  $title = "Turbibliotek :: info";
  include('../snippets/head.php');
?>
<body>

<div class="container">
    <?php include('../snippets/header.php'); ?>
    <div class="row">
        <div class="col-2"></div>
        <div class="col-8">
            <h3 style="text-align: center;">Info</h3>
            <p>Dersom skrivten på denne sider er for liten (eller for stor) er det mulig å zoome inn/ut med kombinasjonen <code>CTRL+</code> og <code>CTRL&#8722;</code> på Windows. På MacOS er det tilsvarende med <code>&#8984;+</code> og <code>&#8984;&#8722;</code>.</p>
            <p>Tror mye skal funke. Databasen er oppdatert automatisk fram til mars 2019. Turer senere enn det må legges inn manuelt. Kjører PHP med MongoDB på egen server (enn så lenge). Sikkerheten på siden er tillitsbasert som vil si at dersom man har tilgang har man gangske store friheter hva angår redigering. Husk på det.</p>
            <p>Forbedringer, forslag til endringer og rapportering av feil kan sendes til Erik Liodden.</p>

            <p>Liste over: <a href="tur_uten_video.php">turer uten video</a><br />
            Liste over: <a href="tur_uten_beskrivelse.php">turer uten beskrivelse</a><br />
            Liste over: <a href="tur_uten_level.php">turer uten registrert vanskelighetsgrad</a></p>
            <h4 style="text-align: center;">Changelog</h4>
<pre>
<?php readfile("../CHANGELOG"); ?>
</pre>
        </div>
        <div class="col-2"></div>
    </div>
</div>

<div style="height: 100px;"></div>


</body>
</html>

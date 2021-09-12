<?php
  require_once('../include/session.php');
  include_once('../include/db.php');

  include_once('../include/func.php');



  $level = isset($_GET['l']) ? $_GET['l'] : "any";
  $bolk  = isset($_GET['b']) ? $_GET['b'] : "any";
  $suitable = isset($_GET['su']) ? $_GET['su'] : "any";
  $date_before = isset($_GET['db']) ? $_GET['db'] : date("Y-m-d");
  $date_after = isset($_GET['da']) ? $_GET['da'] : "2000-01-01";
  $hide_no_dates = isset($_GET['snd']) ? $_GET['snd'] : FALSE;

  // search on name
  $name_search = "";
  if (isset($_GET['s'])) {
    $name_search = trim($_GET['s']);
  }

  // search on tag
  $tag_search = [];
  $tags_raw = "";
  if (isset($_GET['t'])) {
    $tags_raw = $_GET['t'];
    $tags = str_replace(",", " ", $tags_raw);
    $tags = explode(" ", $tags);
    $tags = array_map('trim', $tags);
    $tags = array_values(array_filter($tags));
    foreach($tags as $tag) {
      array_push($tag_search, new MongoDB\BSON\Regex($tag, 'i'));
    }
  }

  // search description
  $q_search = "";
  if (isset($_GET['q'])) {
    $q_search = trim($_GET['q']);
  }

  $tag_mode = "";
  if (isset($_GET['tm'])) {
    if ($_GET['tm'] == "any" || $_GET['tm'] == "all") {
      $tag_mode = $_GET['tm'];
    }
  }





  $query = [];
  if (is_numeric($name_search)) {
    $query['num_id'] = intval($name_search);
  } else {
    $query['name'] = new MongoDB\BSON\Regex($name_search, 'i');
  }
  if ($tag_mode == "all" && sizeof($tag_search) > 0) {
    $query['tags'] = array('$all' => $tag_search);
  } else if ($tag_mode == "any" && sizeof($tag_search) > 0) {
    $query['tags'] = array('$in' => $tag_search);
  }
  if ($q_search != "") {
    $query['$text'] = array('$search' => $q_search);
  }
  if ($level != "any") {
    $query['level'] = $level;
  }
  if ($bolk != "any") {
    $query['bolknb'] = new MongoDB\BSON\Regex($bolk, 'i');
  }
  if ($suitable != "any") {
    $query['suitable'] = array('$in' => array($suitable));
  }



  try {
    $date_before = new DateTime($date_before);
  } catch (\Throwable $th) {
    $date_before = new DateTime(date("Y-m-d"));
  }

  try {
    $date_after = new DateTime($date_after);
  } catch (\Throwable $th) {
    $date_after = new DateTime("2000-01-01");
  }

  $date_before->modify('+1 day');
  $date_before = new MongoDB\BSON\UTCDateTime($date_before);
  $date_after  = new MongoDB\BSON\UTCDateTime($date_after);
  if ($hide_no_dates == FALSE) {
    $query['$or'] = [
            [
              'sist_undervist' => NULL
            ],
            [
              'sist_undervist' => [
                '$gte' => $date_after,
                '$lt' => $date_before,
              ]
            ]
          ];
  } else {
    $query['sist_undervist'] = [
        '$gte' => $date_after,
        '$lt'  => $date_before,
      ];
  }



  $page = isset($_GET["page"]) ? $_GET["page"] : 1;
  $limit = 20;
  $skip = ($page-1) * $limit;

  $options = [
    'projection' => [
        '_id'  => 1,
        'name' => 1,
        'tags' => 1,
        'num_id' => 1,
    ],
    'skip' => $skip,
    'limit' => $limit,
  ];

  if (array_key_exists('$text', $query)) {
    $options['sort'] = array('score' => array('$meta' => "textScore"));
  }

  $tur_list = $collection->find($query, $options);
  $count = $collection->count($query);

  $id = isset($_GET['id']) ? $_GET['id'] : NULL;
  try {
    $bid = new MongoDB\BSON\ObjectId("$id");
  } catch (\Throwable $th) {
    // id invalid
    $bid = NULL;
  }

  if ($id == NULL) {
    // id is not set at all, choose first tur in list
    $tur_details = $collection->findOne($query);
  } elseif ($bid != NULL) {
    $tur_details = $collection->findOne(['_id' => $bid]);
  } else {
    $tur_details = NULL;
  }

  if ($tur_details != NULL) {
    $id = $tur_details['_id'];
  }

?>

<?php
  $title = "Turbibliotek";
  include('../snippets/head.php');
?>
<body>
<div class="container">
  <?php include('../snippets/header.php'); ?>
  <div class="row">
    <div class="col-8">
      <div class="row">
        <div class="col-12">
          <form action="" autocomplete="off" class="search_form">
            <table>
            <tr>
              <td>Navn:</td>
              <td>
                <input type="text" id="name_search" name='s' <?php if (isset($_GET['s']) && trim($_GET['s']) != "") { echo "value=\"" . trim($_GET['s']) . "\""; } ?> placeholder="Søk etter navn..." />
              </td>
              <td>Nivå:</td>
              <td>
                <select name="l">
                  <option value="any" <?php if ($level == "any") echo "selected";?>>----</option>
                  <option value="grunnleggende" <?php if ($level == "grunnleggende") echo "selected";?>>grunnleggende</option>
                  <option value="enkelt" <?php if ($level == "enkelt") echo "selected";?>>enkelt</option>
                  <option value="middels" <?php if ($level == "middels") echo "selected";?>>middels</option>
                  <option value="vanskelig" <?php if ($level == "vanskelig") echo "selected";?>>vanskelig</option>
                </select>
              </td>

            </tr>
            <!--tr><td>test:</td><td><input type="range" name="minBpsInput" id="minBpsId" value="24" min="1" max="100" oninput="minBpsOutputId.value = minBpsId.value"></td><td><output name="minBpsOutput" id="minBpsOutputId">24</output></td></tr-->
            <tr>
              <td>Desc:</td>
              <td>
                <input type="text" id="description_search" name='q' <?php if (isset($_GET['q']) && trim($_GET['q']) != "") { echo "value=\"" . trim($_GET['q']) . "\""; } ?> placeholder="Søk i beskrivelse..." />
              </td>
              <td>Bolk:</td>
              <td>
                <select name="b">
                  <option value="any" <?php if ($level == "any") echo "selected";?>>----</option>
                  <?php
                    $bolks = $collection_meta->find(["type" => "bolk"], ["sort" => ["name" => 1]]);
                    foreach($bolks as $doc) {
                      if ($bolk == $doc["name"]) {
                        echo '<option value="'.$doc["name"].'" selected>'.$doc["name"].'</option>';
                      } else {
                        echo '<option value="'.$doc["name"].'">'.$doc["name"].'</option>';
                      }
                    }
                  ?>
                  </select>
              </td>

            </tr>
            <tr>
              <td>Tag:</td>
              <td style="padding-right: 1.5em;">
                <input type="text" id="tag_search" name='t' <?php if (isset($_GET['t']) && trim($_GET['t']) != "") { echo "value=\"" . trim($_GET['t']) . "\""; } ?> placeholder="Liste av tags..." />

                <input type="radio" name="tm" value="all" <?php if (isset($_GET['tm']) && $_GET['tm'] == "all"){ echo "checked";} ?> /> og&nbsp;&nbsp;
                <input type="radio" name="tm" value="any" <?php if (!isset($_GET['tm']) || $_GET['tm'] != "all"){ echo "checked";} ?> /> eller
              </td>
              <td>
                Undervist før:
              </td>
              <td>
              <input type="date" name="db" <?php if (isset($_GET['db']) && trim($_GET['db']) != "") { echo "value=\"" . $date_before->toDateTime()->modify('-1 day')->format("Y-m-d") . "\""; } else { echo 'value="' . date("Y-m-d") . '"';} ?> placeholder="yyyy-mm-dd" />
              </td>
            </tr>
            <tr>
            <td>Egnet:</td>
              <td>
                <select name="su">
                  <option value="any" <?php if ($suitable == "any") echo "selected";?>>----</option>
                  <?php
                    $suitables = $collection_meta->find(["type" => "suitable"], ["sort" => ["name" => 1]]);
                    foreach($suitables as $doc) {
                      if ($suitable == $doc["name"]) {
                        echo '<option value="'.$doc["name"].'" selected>'.$doc["name"].'</option>';
                      } else {
                        echo '<option value="'.$doc["name"].'">'.$doc["name"].'</option>';
                      }
                    }
                  ?>
                  </select>
              </td>
              <td>
                Undervist etter:
              </td>
              <td>
                <input type="date" name="da" <?php if (isset($_GET['da']) && trim($_GET['da']) != "") { echo "value=\"" . $date_after->toDateTime()->format("Y-m-d") . "\""; } else { echo 'value="2000-01-01"';} ?> placeholder="yyyy-mm-dd" />
              </td>
            </tr>
            <tr>
            <td></td>
              <td>
                <input type="submit" value="Søk" />
                <a href="/">reset</a>
              </td>
              <td>Skjul ukjent dato:
              </td>
              <td>
                <input type="checkbox" name="snd" value="1" <?php if ($hide_no_dates != FALSE) {echo "checked";} ?> />
            </tr>
            </table>
          </form>
          <!--hr style="border: 1px dotted #333; border-style: none none dotted; " /-->
        </div>
      </div>
      <div class="row">
        <div class="col-12">

          <?php
          if ($tur_details != NULL) {
            echo '<h4 style="margin-bottom: 1em;text-align: center;">'.$tur_details['num_id'].' - '.$tur_details["name"].'</h4>';
            echo '<div class="gray-table">';
              echo '<table>';

                //echo "<tr><td style='width: 132px;'><strong>Navn:</strong></td>";
                //echo "<td>" . $tur_details['name'] . "</td></tr>";

                echo "<tr><td style='width: 132px'><strong>Tags:</strong></td>";
                echo "<td>" . bson_array_to_str_list($tur_details['tags']) . "</td></tr>";

                echo "<tr><td><strong>Beskrivelse:</strong></td>";
                echo "<td>" . create_html_string($tur_details['description']) . "</td></tr>";

                echo "<tr><td><strong>Si på starten:</strong></td>";
                echo "<td>" . create_html_string($tur_details['notes_start']) . "</td></tr>";

                echo "<tr><td><strong>Fokuspunkt:</strong></td>";
                echo "<td>" . create_html_string($tur_details['focus']) . "</td></tr>";

                echo "<tr><td><strong>Youtube-link:</strong></td>";
                //echo "<td><a href=\"https://www.youtube.com/watch?v=". $tur_details['youtube'] . '" target="_blank">' . $tur_details['youtube'] . "</a></td></tr>";
            ?>
                <td>
                <div class="youtube-video-place" data-yt-url="https://www.youtube.com/embed/<?php echo $tur_details['youtube']; ?>?rel=0&showinfo=0&autoplay=1">
                  <span class="play-youtube-video"><?php echo $tur_details['youtube']; ?></span>
                </div>
                </td>
            <?php
                echo "<tr><td><strong>Undervisning:</strong></td>";
                echo "<td>" . $tur_details['time'];
                if ($tur_details['time'] != NULL) { echo " minutter"; }
                echo "</td></tr>";

                echo "<tr><td><strong>Passende bpm:</strong></td>";
                echo "<td>" . $tur_details['bpm'] . "</td></tr>";

                echo "<tr><td><strong>Bolk:</strong></td>";
                echo "<td>" . bson_array_to_str_list($tur_details['bolknb']) . "</td></tr>";

                echo "<tr><td><strong>Kommentarer:</strong></td>";
                echo "<td>" . create_html_string($tur_details['notes']) . "</td></tr>";

                //echo "<tr><td><strong>Deling (?):</strong></td>";
                //echo "<td>" . $tur_details['deling'] . "</td></tr>";

                echo "<tr><td><strong>Status:</strong></td>";
                echo "<td>" . $tur_details['status'] . "</td></tr>";

                echo "<tr><td><strong>Helgekurs:</strong></td>";
                echo "<td>" . $tur_details['helgekurs'] . "</td></tr>";

                echo "<tr><td><strong>Forbedringer:</strong></td>";
                echo "<td>" . create_html_string($tur_details['improvements']) . "</td></tr>";

                echo "<tr><td><strong>Nivå:</strong></td>";
                echo "<td>" . $tur_details['level'] . "</td></tr>";

                echo "<tr><td><strong>Dato undervist:</strong></td>";
                echo "<td>";
                if ($tur_details['dato_undervist']) {
                  foreach($tur_details['dato_undervist'] as $date) {
                    echo "<code>" . $date->toDateTime()->format('Y-M-d') . "</code><br />";
                  }
                }
                echo "</td></tr>";

                echo "<tr><td><strong>Egnet for:</strong></td>";
                echo "<td>" . str_replace(", ", "<br />", bson_array_to_str_list($tur_details['suitable'])) . "</td></tr>";

                echo "<tr><td><strong>Relaterte turer:</strong></td>";
                echo "<td>";
                if ($tur_details['related']) {
                  $related = $collection->find(
                    [
                      '_id' => array('$in' => $tur_details['related'])
                    ],
                    [
                      'projection' => [
                        '_id'  => 1,
                        'name' => 1,
                        'num_id' => 1
                    ],
                    'limit' => 5000,
                    ]
                  );
                  foreach($related as $other_tur) {
                    echo "<a href='/?id=" . $other_tur['_id'] . "'>" . $other_tur['num_id'] . ": " . $other_tur['name'] . "</a><br />";
                  }
                }
                echo "</td></tr>";
              echo "</table>";
            echo "</div>";
            echo "<div class='toolbox'>";
              echo "[<a href='edit_comment.php?id=" . $id . "'>kommentar</a>]&nbsp;&nbsp;&nbsp;&nbsp;";
              echo "[<a href='undervist_idag.php?id=" . $id . "'>undervist idag</a>]&nbsp;&nbsp;&nbsp;&nbsp;";
              echo "[<a href='edit.php?id=" . $id . "'>rediger</a>]&nbsp;&nbsp;&nbsp;&nbsp;";
              echo "[<a href='log.php?id=" . $id . "'>logg</a>]";
            echo "</div>";
          } else {
            echo "<div class='toolbox'>";
              echo "&macr;\_(ツ)_/&macr";
            echo "</div>";
          }
          ?>
        </div>
      </div>
    </div>
    <div class="col-4">
      <table>
        <tr>
          <td>Side:</td>
          <td>
            <div class="pager_container">
            <?php
              $query = $_GET;
              for ($i=1; $i < $count/$limit + 1; $i++) {
                $query['page'] = $i;
                $query_result = http_build_query($query);
                $class = ($i == $page) ? " id='active_page'" : "";
                echo '<div class="pager_item"><a'.$class.' href="'.$_SERVER['PHP_SELF'].'?'.$query_result.'">'.sprintf("%02d", $i).'</a></div>';
              }
            ?>
            </div>
          </td>
        </tr>
      </table>

      <hr style="border: 1px solid #e7e7e7; border-style: none none solid;" />

      <table id="tur_list">
        </tr>
        <?php
          foreach($tur_list as $tur) {
            $queryasdf = $_GET;
            $queryasdf["id"] = (string) $tur['_id'];
            $query_result = http_build_query($queryasdf);
            $class = ($tur["_id"] == $tur_details["_id"]) ? " id='active_tur'" : "";
            echo '<tr>';
            echo '<td>' . sprintf("%03d", $tur['num_id']) . "</td>";
            echo '<td'.$class.'><a href="'.$_SERVER['PHP_SELF'].'?'.$query_result.'">' . $tur['name']. "</a><br />";
            echo '<small><i>' . bson_array_to_str_list($tur['tags']) . '</i></small>';
            echo '</td></tr>';
          }
        ?>
      </table>
    </div>
  </div>
</div>

<div style="height: 100px;"></div>

<script>
    var video_wrapper = $('.youtube-video-place');
    //  Check to see if youtube wrapper exists
    if(video_wrapper.length){
        // If user clicks on the video wrapper load the video.
        $('.play-youtube-video').on('click', function(){
        /* Dynamically inject the iframe on demand of the user.
        Pull the youtube url from the data attribute on the wrapper element. */
        video_wrapper.html('<iframe allowfullscreen frameborder="0" class="youtube-frame" src="' + video_wrapper.data('yt-url') + '"></iframe>');
        });
    }
    </script>
</body>
</html>

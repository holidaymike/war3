<html> 
<head> 
<title>Game Detail</title> 
</head> 
<body>
<?
  require_once "util.php";
  $con = mysql_connect("localhost", "mikeMike", "1234");
  if (!$con) {
    print("Cannot connect to database");
    return;
  }
  if (!mysql_select_db('war3')) {
    print("Cannot connect to db war3");
    return;
  }

  if (isset($_GET['id'])) {
    $id = mysql_real_escape_string($_GET['id']);
  } else {
    $id = mysql_real_escape_string($_POST['id']);
  }
  $isAdmin = true; //(isset($_GET['admin']) && $_GET['admin'] == 'abc123');
  $canEdit = (isset($_POST['admin']) && $_POST['admin'] == 'xyz890');

  if ($canEdit) {
    $isAdmin = true;
    if (isset($_POST['rating'])) {
      $rating = mysql_real_escape_string($_POST['rating']);
      $query = "UPDATE game SET rating=$rating WHERE id=$id";
    } else if (isset($_POST['bid'])) {
      $bid = mysql_real_escape_string($_POST['bid']);
      $type = mysql_real_escape_string($_POST['type']);
      $start = toSec(mysql_real_escape_string($_POST['start']));
      $end = toSec(mysql_real_escape_string($_POST['end']));
      $action = mysql_real_escape_string($_POST['action']);
      $adv = mysql_real_escape_string($_POST['adv']);
      $decisive = isset($_POST['decisive'])? 1 : 0;
      if ($bid == 'new') {
        $query = "INSERT INTO battle (game_id, type, start, end, advantage, decisive) VALUES ($id, $type, $start, $end, $adv, $decisive)";
      } else if ($action == "Update") {
        $query = "UPDATE battle SET type=$type, start=$start, end=$end, advantage=$adv, decisive=$decisive WHERE id=$bid";
      } else if ($action == "Remove") {
        $query = "DELETE FROM battle WHERE id=$bid";
      }
    } else if (isset($_POST['details'])) {
      $details = mysql_real_escape_string($_POST['details']);
      $query = "UPDATE game SET detail='$details' WHERE id=".$id;
    }
    if (mysql_query($query)) print("Edit successful");
    else print("Edit failed");
  }

  $query = "SELECT * FROM game where id=".mysql_real_escape_string($id);
  $result = mysql_query($query);
  if (!$result) {
    print("Something is wrong :(");
    return;
  }

  $row = mysql_fetch_assoc($result);
?>
<h3>Replay details</h3>
<div>Filename: <a href=""><?= $row['name'] ?></a></div>
<div>Map: <?= $row['map'] ?></div>
<div>Version: <?= sprintf("1.%02d", $row['version']) ?></div>
<div>Type: <?= gameType2Str($row['type']) ?></div>
<?
  $len = floor($row['length']/1000);
  $lenStr = sprintf("%02dmin%02dsec", floor($len/60), $len%60);
  $desc = $row['detail'];
?>
<div>Length: <?= $lenStr ?></div>
<div>Date: <?= $row['created'] ?></div>
<br>
<div>Winning Team:</div>
<?
  $query2 = "SELECT * FROM player where gameid=".$row['id'];
  $result2 = mysql_query($query2);
  if (!$result2) {
    print("Something is wrong :(");
    return;
  }
  $player = array();
  while($row2 = mysql_fetch_assoc($result2)) {
    $player[] = $row2;
  }
?>
<? foreach($player as $p): ?>
  <? if ($p['team'] > 4 && $p['apm'] == 0) continue; ?>
  <? if ($p['team'] == $row['winner']): ?>
    <div><?= $p['name']." (".race2Str($p['race']).") apm=".$p['apm'] ?></div>
  <? endif ?>
<? endforeach ?>
<br>
<div>Losing Team:</div>
<? foreach($player as $p): ?>
  <? if ($p['team'] > 4 && $p['apm'] == 0) continue; ?>
  <? if ($p['team'] != $row['winner']): ?>
    <div><?= $p['name']." (".race2Str($p['race']).") apm=".$p['apm'] ?></div>
  <? endif ?>
<? endforeach ?>
<br><br>
<form method="post">
<input type="hidden" name="id" value="<?= $id ?>" />
Rating:
<select name="rating">
  <option value=0 <?= ($row['rating'] == 0)? "selected" : ""?>>Not Yet Rated</option>
  <option value=1 <?= ($row['rating'] == 1)? "selected" : ""?>>One Sided</option>
  <option value=2 <?= ($row['rating'] == 2)? "selected" : ""?>>Ma Ma Hu Hu</option>
  <option value=3 <?= ($row['rating'] == 3)? "selected" : ""?>>Competitive</option>
  <option value=4 <?= ($row['rating'] == 4)? "selected" : ""?>>Must See</option>
</select>
<? if ($isAdmin): ?>
<input type="hidden" name="admin" value="xyz890" />
<input type="submit" value="Edit" />
<? endif ?>
</form>
<?
  mysql_close();
?>
</body>

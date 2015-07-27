<html> 
<head> 
<title>mikeMike's Warcraft replays</title> 
<link rel="stylesheet" type="text/css" href="war3.css" />
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
  
  $friends = array();
  $total = 0;
  $totalTotalApm = 0;
  $totalData = array();
  foreach(FriendChecker::$friends as $friend => $alias) {
    $friends[$friend] = array();
    
    $query = "SELECT * FROM player WHERE apm > 0 AND name IN ('".implode("','", $alias)."')";
    $result = mysql_query($query);
    if (!$result) {
      print("Something is wrong :(");
      return;
    }
    
    $totalApm = 0;
    $count = 0;
    while($row = mysql_fetch_assoc($result)) {
      if ($row['isRandom']) {
      	$race = 'Random';
      } else {
      	$race = race2Str($row['race']);
      }
      $friends[$friend][$race]++;
      $totalApm += $row['apm'];
      $count++;

      $totalData[$race]++;
      $totalTotalApm += $row['apm'];
      $total++;
    }
    $friends[$friend]['apm'] = round($totalApm / $count);
    $friends[$friend]['count'] = $count;
  }
  $totalData['apm'] = round($totalTotalApm / $total);
  $totalData['count'] = $total;
  $friends['Total'] = $totalData;

  $orderBy = "created";
  if (isset($_GET['orderBy'])) {
    $orderBy = mysql_real_escape_string($_GET['orderBy']);
  }
  $aesc = false;
  if (isset($_GET['aesc'])) {
    $aesc = true;
  }
  if (isset($_GET['filterType'])) {
    $filterType = mysql_real_escape_string($_GET['filterType']);
    $filter = mysql_real_escape_string($_GET['filter']);
  }
  if (isset($_GET['friend'])) {
    $fr = mysql_real_escape_string($_GET['friend']);
    $friendStr = implode("','",FriendChecker::$friends[$fr]);
  }

  if (isset($friendStr)) {
    $query = "SELECT game.* FROM game, player WHERE game.id = player.gameid AND player.apm > 0 AND player.name IN ('$friendStr') order by ".$orderBy.($aesc? "" : " desc");
  } else if (isset($filterType)) {
    $query = "SELECT * FROM game WHERE $filterType='$filter' order by ".$orderBy.($aesc? "" : " desc");
  } else {
    $query = "SELECT * FROM game order by ".$orderBy.($aesc? "" : " desc");
  }

  $result = mysql_query($query);
  if (!$result) {
    print("Something is wrong :(");
    return;
  }
?>
<h1>mikeMike's Warcraft replays</h1>
<table border="1">
<tr><td>name</td><td class="num">num</td><td class="num">apm</td><td class="race">human</td><td class="race">orc</td><td class="race">elf</td><td class="race">undead</td><td class="race">random</td></tr>
<? foreach($friends as $friend => $data): ?>
<tr>
  <td><a href="/war3/?friend=<?=$friend?>"><?= $friend ?></a></td>
  <td class="num"><?= $data['count'] ?></td>
  <td class="num"><?= $data['apm'] ?></td>
  <td class="race"><?= round($data['Human']*100/$data['count']) ?>%</td>
  <td class="race"><?= round($data['Orc']*100/$data['count']) ?>%</td>
  <td class="race"><?= round($data['Night Elf']*100/$data['count']) ?>%</td>
  <td class="race"><?= round($data['Undead']*100/$data['count']) ?>%</td>
  <td class="race"><?= round($data['Random']*100/$data['count']) ?>%</td>
</tr>
<? endforeach ?>
</table>
<p>
<? if (isset($friendStr)): ?>
<?= "Total number of replays for $fr: ".mysql_num_rows($result); ?>
<? else: ?>
<?= "Total number of replays: ".mysql_num_rows($result); ?>
<? endif ?>
</p>
<table border="1">
<tr>
<td><a href="/war3/<?=(($aesc || $orderBy!='created')? '' : '?aesc=1')?>">date</a></td>
<td>version</td>
<td><a href="/war3/?orderBy=type<?=(($aesc || $orderBy!='type')? '' : '&aesc=1')?>">type</a></td>
<td><a href="/war3/?orderBy=length<?=(($aesc || $orderBy!='length')? '' : '&aesc=1')?>">length</a></td>
<td><a href="/war3/?orderBy=map<?=(($aesc || $orderBy!='map')? '' : '&aesc=1')?>">map</a></td>
<td><a href="/war3/?orderBy=teamavg<?=(($aesc || $orderBy!='teamavg')? '' : '&aesc=1')?>">team apm</a></td>
<td><a href="/war3/?orderBy=oppavg<?=(($aesc || $orderBy!='oppavg')? '' : '&aesc=1')?>">opp apm</a></td>
<td><a href="/war3/?orderBy=rating<?=(($aesc || $orderBy!='rating')? '' : '&aesc=1')?>">rating</td>
</tr>

<? while($row = mysql_fetch_assoc($result)): ?>
<? 
  $type = gameType2Str($row['type']);
  $len = floor($row['length']/1000); 
  $lenStr = sprintf("%02d:%02d", floor($len/60), $len%60);
  $map = parseMap($row['map']);
?>
<tr>
  <td><a href="/war3/detail.php?id=<?= $row['id']?>"><?= $row['created'] ?></a></td>
  <td><?= $row['version'] ?></td>
  <td><?= $type ?></td>
  <td><?= $lenStr ?></td>
  <td><?= $map ?></td>
  <td><?= $row['teamavg'] ?></td>
  <td><?= $row['oppavg'] ?></td>
  <td><?= $row['rating'] ?></td>
</tr>
<? endwhile; ?> 
</body>
<? mysql_close(); ?>

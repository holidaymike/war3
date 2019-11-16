<?php
//include 'w3g-julas-2.4/w3g-julas.php';
include 'game.php';

//$filename = '/Users/michael/war3/WarcraftReplays/1.18/2v2fun1.w3g';
//$filename = 'WarcraftReplays/1.05/random6.w3g';
$filename = './WarcraftReplays/1.06/4v4win59.w3g';

// $replay = new replay($filename);
//
// print_r($replay);

date_default_timezone_set('America/Los_Angeles');
$game = new game($filename);
$gameJson = json_encode($game);

print_r($gameJson);
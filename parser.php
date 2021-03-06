<?php 

include 'game.php';

//set default timezone
date_default_timezone_set('America/Los_Angeles');

//open DB connections
// $con = mysql_connect("127.0.0.1","mikeMike","1234");
// if (!$con) {
// 	die('Could not connect: ' . mysql_error());
// }
// mysql_select_db("war3", $con);

$dirPath = './War3/';

function walkDirectory($dirPath, &$games) {
    $dh = opendir($dirPath);
    while (($file = readdir($dh)) !== false) {
    	if ($file[0] == '.') continue;

    	$filename = $dirPath.$file;
    	if (is_dir($filename)) {
    	    walkDirectory($filename.'/', $games);
    	    continue;
    	}
    	$pathParts = pathinfo($filename);
    	if ($pathParts['extension'] != "w3g") {
    	    continue;
    	}

        $game = new game($filename);
    	echo "file=".$filename." date=".$game->created."\n";
    	$games[] = $game;
    }
    closedir($dh);
}

$games = array();
walkDirectory($dirPath, $games);

echo "len=".count($games);

$filedata = json_encode($games);
file_put_contents ("games.log" , $filedata);

// $dh = opendir($dirPath);
// $counter = 0;
// while (($file = readdir($dh)) !== false) {
// 	if ($file[0] == '.') continue;
// 	if (!is_dir($dirPath.$file)) continue;
//
// 	echo "directory=$file\n";
//
// 	//ok, go into the directory
// 	$dh2 = opendir($dirPath.$file);
// 	while (($warfile = readdir($dh2)) !== false) {
// 		if ($warfile[0] == '.') continue;
//
// 		$filename = $dirPath.$file.'/'.$warfile;
// 		if (is_dir($filename)) continue;
//
// 		echo "$counter: file=".$filename."\n";
// 		$counter++;
//
// 		$game = new game($filename);
// 		//$game->insert($con);
// 		//print_r(json_encode($game));
// 	}
// 	closedir($dh2);
// }
// closedir($dh);

//mysql_close($con);
?>
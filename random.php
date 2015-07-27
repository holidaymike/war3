<html> 
<head> 
<title>Random Game</title> 
</head> 
<body>
<?
  $con = mysql_connect("localhost", "mikeMike", "1234");
  if (!$con) {
    print("Cannot connect to database");
    return;
  }
  if (!mysql_select_db('war3')) {
    print("Cannot connect to db war3");
    return;
  }
  $query = "SELECT * FROM game where rating=0 order by rand(now()) limit 1";
  $result = mysql_query($query);
  
  $row = mysql_fetch_assoc($result);
?>
<h1><a href="/war3/detail.php?id=<?= $row['id'] ?>&admin=abc123"><?= '['.$row['version'].'] '.$row['name']?></a></h1>
</body>

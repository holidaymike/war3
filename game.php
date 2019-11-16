<?php
include 'w3g-julas-2.4/w3g-julas.php';
include 'util.php';
include 'player.php';

class game implements JsonSerializable {
	public $id, $name, $type, $len, $map, $ver, $isTFT, $numPlayer, $numTeam, $winner, $created, $rating, $teamavg, $oppavg;
	public $players;

	public function __construct($data) {
		if (is_string($data)) {
			$this->initFromFilename($data);
		}
	}
	
	protected function initFromFilename($filename) {
		$replay = new replay($filename);
		
		unset($replay->teams[12]); //destroy team observers
		
		$this->name = basename($filename);
		$this->len = $replay->header['length'];
		$this->map = $replay->game['map'];
		$pos = strrpos($this->map,"\\");
		if ($pos !== false) {
			$this->map = substr($this->map, $pos+1);
		}
		$this->ver = $replay->header['major_v'];
		$this->isTFT = ($replay->header['ident'] == 'W3XP');
		$this->numTeam = count($replay->teams);
		$this->winner = $replay->game['winner_team'];
		$ftime = filemtime($filename);
		$this->created = date('Y-m-d H:i:s', $ftime);
		
		$myTeam = -1;
		//parse each player
		foreach($replay->teams as $team) {
			foreach($team as $player) {
				$p = new player($player, $this);
				$this->players[] = $p;
				
				//print("playerName=".$p->name."\n");
				if (FriendChecker::isMike($p->name)) {
					$myTeam = $p->team;
					//print("myTeam=$myTeam\n");
				}
			}
		}
		
		$this->numPlayer = count($this->players);
		$this->type = $this->getType($replay);
		
		$totalTeam = 0;
		$totalOpp = 0;
		$teamCount = 0;
		$oppCount = 0;
		
		foreach($this->players as $player) {
			if ($player->team == $myTeam) {
				$totalTeam += $player->apm;
				$teamCount++;
			} else {
				$totalOpp += $player->apm;
				$oppCount++;
			}
		}
		$this->teamavg = ($teamCount == 0)? 0 : $totalTeam / $teamCount;
		$this->oppavg = ($oppCount == 0)? 0: $totalOpp / $oppCount;
	}
	
	protected function getType($replay) {
		if ($replay->game['type'] == 'Custom game') {
			if ($this->numTeam > 2) return 10;	//ffa
			if ($this->numPlayer == 2) return 11; //1v1
			if ($this->numPlayer == 4) return 12; //2v2
			if ($this->numPlayer == 6) return 13; //3v3
			if ($this->numPlayer == 8) return 14; //4v4
			return 30;	//unknown
		}
		if ($this->numPlayer == 2) return 1; //1v1
		if ($this->numPlayer == 4) {
			if ($this->isAT($replay)) return 2;
			else return 22;
		}
		if ($this->numPlayer == 6) {
			if ($this->isAT($replay)) return 3;
			else return 23;
		}
		if ($this->numPlayer == 8) {
			if ($this->isAT($replay)) return 4;
			else return 24;
		}
		return 30; //unknown
	}
	
	protected function isAT($replay) {
		foreach ($replay->teams as $team) {
			foreach($team as $player) {
				if (FriendChecker::isFriend($player['name'], false)) return true;
			}
		}
		return false;
	}
	
	public function insert($con) {
		if (!mysql_query($this->getInsertQuery(), $con)) {
			die('Could not insert: ' . mysql_error());
		}
		$this->id = mysql_insert_id($con);
		
		foreach($this->players as $player) {
			$player->setGameId($this->id);
			$player->insert($con);
		}
	}
	
	protected function getInsertQuery() {
		$queryStr = "INSERT INTO game VALUES ('0','".
			$this->name."','".
			$this->type."','".
			$this->len."','".
			mysql_real_escape_string($this->map)
			."','".
			$this->ver."','".
			$this->isTFT."','".
			$this->numPlayer."','".
			$this->numTeam."','".
			$this->winner."','".
			$this->created."', '0', '". 
			$this->teamavg."','".
			$this->oppavg."')";
		return $queryStr;
	}

    // From JsonSerializable
    public function jsonSerialize(){
        return ['name' => $this->name,
                'type' => $this->type,
                'len' => $this->len,
                'map' => $this->map,
                'version' => $this->ver,
                'isTFT' => $this->isTFT,
                'numPlayer' => $this->numPlayer,
                'winner' => $this->winner,
                'created' => $this->created,
                'teamavg' => $this->teamavg,
                'oppavg' => $this->oppavg,
                'players' => $this->players];
    }
}	
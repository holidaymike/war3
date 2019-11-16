<?php

class player implements JsonSerializable {
	public $id, $name, $race, $isRandom, $apm, $gameId, $team;
	
	public static $raceToInt = array(
		'Human' => 1,
		'Orc' => 2,
		'NightElf' => 3,
		'Undead' => 4
	);

	public function __construct($data, $game) {
		if (is_array($data)) {
			$this->initFromArray($data, $game);
		}
	}
	
	protected function initFromArray($data, $game) {
		$this->name = $data['name'];
		if ($data['race'] == 'Random') {
			$this->isRandom = true;
			$this->race = self::$raceToInt[$data['race_detected']];
		} else {
			$this->isRandom = false;
			$this->race = self::$raceToInt[$data['race']];
		}
		$this->apm = (int) round($data['actions'] / ($game->len / 60000));
		$this->gameId = $game->id;
		$this->team = $data['team'];
	}
	
	public function setGameId($id) {
		$this->gameId = $id;
	}
	
	public function insert($con) {
		if (!mysql_query($this->getInsertQuery(), $con)) {
			die('Could not insert: ' . mysql_error());
		}
		$this->id = mysql_insert_id($con);
	}
	
	protected function getInsertQuery() {
		$queryStr = "INSERT INTO player VALUES ('0','".
			$this->name."','".
			$this->race."','".
			$this->isRandom."','".
			$this->apm."','".
			$this->gameId."','".
			$this->team."')";
		return $queryStr;
	}

    // From JsonSerializable
    public function jsonSerialize(){
        return ['name' => $this->name,
                'race' => $this->race,
                'isRandom' => $this->isRandom,
                'apm' => $this->apm,
                'team' => $this->team];
    }
}
	
	
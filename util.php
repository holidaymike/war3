<?php

function toSec($str) {
  $arr = explode(":", $str);
  return $arr[0] * 60 + $arr[1];
}

function fromSec($sec) {
  return sprintf("%02d:%02d", floor($sec/60), $sec%60);  
}

function adv2Str($type) {
  if ($type == 0) return "Even";
  if ($type == 1) return "Us";
  if ($type == 2) return "Them";
  return "Unknown";
}

function battleType2Str($type) {
  if ($type == 1) return "Harassment";
  if ($type == 2) return "Skirmish";
  if ($type == 3) return "All out war";
  return "Unknown";
}

function parseMap($str) {
  list($dir, $map, $ext) = split("[).]", $str);
  $ret = preg_replace('/(\w+)([A-Z])/U', '\\1 \\2', $map);
  if ($ext == "w3x") $ret .= " (TFT)";
  else $ret .= " (ROC)";
  return $ret;
}

function race2Str($race) {
  if ($race == 1) return "Human";
  if ($race == 2) return "Orc";
  if ($race == 3) return "Night Elf";
  if ($race == 4) return "Undead";
  if ($race == 5) return "Random";
  return "Unknown";
}

function gameType2Str($type) {
  if ($type == 1) return "AT 1v1";
  if ($type == 2) return "AT 2v2";
  if ($type == 3) return "AT 3v3";
  if ($type == 4) return "AT 4v4";
  if ($type == 10) return "Custom FFA";
  if ($type == 11) return "Custom 1v1";
  if ($type == 12) return "Custom 2v2";
  if ($type == 13) return "Custom 3v3";
  if ($type == 14) return "Custom 4v4";
  if ($type == 22) return "RT 2v2";
  if ($type == 23) return "RT 3v3";
  if ($type == 24) return "RT 4v4";
  return "Unknown";
}

class FriendChecker {
  public static $friends = array(
    'mikeMike' => array('mikeMike', 'mikeMan', 'mikeChim', 'warMike', 'testMike'),
    't123' => array('t123', 't124', 'bluegrass', 'everElff'),
    'z_jia' => array('z_jia', 'rocketsrocks'), 
    'wingman' => array('wingman', 'wingwar', 'wingdog', 'WingRider'),
    'wale18' => array('wale18', 'wale19', 'wale', 'wale20', 'wale21'),
    'onioncraft' => array('onioncraft'),
    'jc123' => array('jc123', 'jlinks'),
    'fitzban' => array('fitzban'),
    'jbong' => array('jbong'),
    'chaotic_dreamer' => array('chaotic_dreamer'),
    'prue' => array('prue'),
    'slinetz' => array('slinetz'),
  );

  public static function belongsTo($alias, $name) {
    if (isset(self::$friends[$name])) {
      return in_array($alias, self::$friends[$name]);
    }
    return false;
  }

  public static function isFriend($name, $includeMike = true) {
    foreach(self::$friends as $friend => $names) {
    	if ($friend == 'mikeMike' && !$includeMike) continue;
    	if (in_array($name, $names)) return true;
    }
    return false;
  }
  
  public static function isMike($name) {
	return in_array($name, self::$friends['mikeMike']);
  }
}


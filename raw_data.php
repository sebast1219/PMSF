<?php

$_GET['ville'] = $_POST['ville'];
$ville = $_GET['ville'];
$timing['start'] = microtime(true);
include('config/config.php');
include(dirname(__FILE__).'/config.php');
include(dirname(__FILE__)."/Entities/User.php");
ini_set('memory_limit', '2048M');
global $map, $fork;

/* Location Settings */
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}
if(!in_array($ville,array("CHOLET"))) {
	if(!isset($_SESSION['User'])) {
		redirect('/login.php');exit();
	}
	$User = unserialize($_SESSION['User']);
	$memberLimitTimestamp = strtotime($User->getMemberLimitTime());
	if($memberLimitTimestamp < time()) {
		redirect('/login.php');exit();
	}
	if(!$User->checkUniqueUser()) {
		die();
	} else {
		$User->ping();
	}
}

$now = new DateTime();
$now->sub(new DateInterval('PT20S'));

$d = array();

$d["timestamp"] = $now->getTimestamp();

$swLat = !empty($_POST['swLat']) ? $_POST['swLat'] : 0;
$neLng = !empty($_POST['neLng']) ? $_POST['neLng'] : 0;
$swLng = !empty($_POST['swLng']) ? $_POST['swLng'] : 0;
$neLat = !empty($_POST['neLat']) ? $_POST['neLat'] : 0;
$oSwLat = !empty($_POST['oSwLat']) ? $_POST['oSwLat'] : 0;
$oSwLng = !empty($_POST['oSwLng']) ? $_POST['oSwLng'] : 0;
$oNeLat = !empty($_POST['oNeLat']) ? $_POST['oNeLat'] : 0;
$oNeLng = !empty($_POST['oNeLng']) ? $_POST['oNeLng'] : 0;
$luredonly = !empty($_POST['luredonly']) ? $_POST['luredonly'] : false;
$lastpokemon = !empty($_POST['lastpokemon']) ? $_POST['lastpokemon'] : false;
$lastgyms = !empty($_POST['lastgyms']) ? $_POST['lastgyms'] : false;
$lastpokestops = !empty($_POST['lastpokestops']) ? $_POST['lastpokestops'] : false;
$lastlocs = !empty($_POST['lastslocs']) ? $_POST['lastslocs'] : false;
$lastspawns = !empty($_POST['lastspawns']) ? $_POST['lastspawns'] : false;
$d["lastpokestops"] = !empty($_POST['pokestops']) ? $_POST['pokestops'] : false;
$d["lastgyms"] = !empty($_POST['gyms']) ? $_POST['gyms'] : false;
$d["lastslocs"] = !empty($_POST['scanned']) ? $_POST['scanned'] : false;
$d["lastspawns"] = !empty($_POST['spawnpoints']) ? $_POST['spawnpoints'] : false;
$d["lastpokemon"] = !empty($_POST['pokemon']) ? $_POST['pokemon'] : false;
$hideMinPerfection = !empty($_POST['hideMinPerfection']) ? $_POST['hideMinPerfection'] : 0;
$hideMinLevel = !empty($_POST['hideMinLevel']) ? $_POST['hideMinLevel'] : 0;

$timestamp = !empty($_POST['timestamp']) ? $_POST['timestamp'] : 0;

$useragent = $_SERVER['HTTP_USER_AGENT'];
if (empty($swLat) || empty($swLng) || empty($neLat) || empty($neLng) || preg_match("/curl|libcurl/", $useragent)) {
    http_response_code(400);
    die();
}
if ($maxLatLng > 0 && ((($neLat - $swLat) > $maxLatLng) || (($neLng - $swLng) > $maxLatLng))) {
    http_response_code(400);
    die();
}

if (!validateToken($_POST['token'])) {
    http_response_code(400);
    die();
}

// init map
if ($map == "monocle") {
    if ($fork == "asner") {
        $scanner = new \Scanner\Monocle_Asner();
    } elseif ($fork == "monkey") {
        $scanner = new \Scanner\Monocle_Monkey();
    } else {
        $scanner = new \Scanner\Monocle();
    }
} elseif ($map == "rm") {
    if ($fork == "sloppy") {
        $scanner = new \Scanner\RocketMap_Sloppy();
    } else {
        $scanner = new \Scanner\RocketMap();
    }
}

$newarea = false;
if (($oSwLng < $swLng) && ($oSwLat < $swLat) && ($oNeLat > $neLat) && ($oNeLng > $neLng)) {
    $newarea = false;
} elseif (($oSwLat != $swLat) && ($oSwLng != $swLng) && ($oNeLat != $neLat) && ($oNeLng != $neLng)) {
    $newarea = true;
} else {
    $newarea = false;
}

$d["oSwLat"] = $swLat;
$d["oSwLng"] = $swLng;
$d["oNeLat"] = $neLat;
$d["oNeLng"] = $neLng;

$ids = array();
$eids = array();
$neids = array();
$reids = array();
$debug['1_before_functions'] = microtime(true) - $timing['start'];

global $noPokemon;
if (!$noPokemon) {
    if ($d["lastpokemon"] == "true") {
        if ($lastpokemon != 'true') {
            $d["pokemons"] = $scanner->get_active($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["pokemons"] = $scanner->get_active($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["pokemons"] = $scanner->get_active($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }

        if (!empty($_POST['eids']) || $hideMinPerfection != 0 || $hideMinLevel != 0) {
            $eids = explode(",", $_POST['eids']);
			if (!empty($_POST['neids'])) {
				$neids = explode(",", $_POST['neids']);				
			}

            foreach ($d['pokemons'] as $elementKey => $element) {
				if(($element['individual_attack'] != "" && $hideMinPerfection > (($element['individual_attack'] + $element['individual_attack'] + $element['individual_attack']) / 45 * 100)) || ($element['level'] != "" && $hideMinLevel > $element['level'])) {
					if(!in_array($element['pokemon_id'],$neids)) {
						unset($d['pokemons'][$elementKey]);
					}
				} else if(in_array($element['pokemon_id'], $eids)) {
					if(!in_array($element['pokemon_id'],$neids)) {
						unset($d['pokemons'][$elementKey]);
					}
				}
                // foreach ($element as $valueKey => $value) {
                    // if ($valueKey == 'pokemon_id') {
                        // if (in_array($value, $eids)) {
                            //delete this particular object from the $array
                            // unset($d['pokemons'][$elementKey]);
                        // }
                    // }
                // }
            }
        }

        if (!empty($_POST['reids'])) {
            $reids = explode(",", $_POST['reids']);

            $d["pokemons"] = array_merge($d["pokemons"], $scanner->get_active_by_id($reids, $swLat, $swLng, $neLat, $neLng));

            $d["reids"] = !empty($_POST['reids']) ? $reids : null;
        }
    }
}
$debug['2_after_pokemon'] = microtime(true) - $timing['start'];

global $noPokestops;
if (!$noPokestops) {
    if ($d["lastpokestops"] == "true") {
        if ($lastpokestops != "true") {
            $d["pokestops"] = $scanner->get_stops($swLat, $swLng, $neLat, $neLng, 0, 0, 0, 0, 0, $luredonly);
        } else {
            if ($newarea) {
                $d["pokestops"] = $scanner->get_stops($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng, $luredonly);
            } else {
                $d["pokestops"] = $scanner->get_stops($swLat, $swLng, $neLat, $neLng, $timestamp, 0, 0, 0, 0, $luredonly);
            }
        }
    }
}
$debug['3_after_pokestops'] = microtime(true) - $timing['start'];

global $noGyms, $noRaids;
if (!$noGyms || !$noRaids) {
    if ($d["lastgyms"] == "true") {
        if ($lastgyms != "true") {
            $d["gyms"] = $scanner->get_gyms($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["gyms"] = $scanner->get_gyms($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["gyms"] = $scanner->get_gyms($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}
$debug['4_after_gyms'] = microtime(true) - $timing['start'];

global $noSpawnPoints;
if (!$noSpawnPoints) {
    if ($d["lastspawns"] == "true") {
        if ($lastspawns != "true") {
            $d["spawnpoints"] = $scanner->get_spawnpoints($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["spawnpoints"] = $scanner->get_spawnpoints($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["spawnpoints"] = $scanner->get_spawnpoints($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}
$debug['5_after_spawnpoints'] = microtime(true) - $timing['start'];

global $noScannedLocations;
if (!$noScannedLocations) {
    if ($d["lastslocs"] == "true") {
        if ($lastlocs != "true") {
            $d["scanned"] = $scanner->get_recent($swLat, $swLng, $neLat, $neLng);
        } else {
            if ($newarea) {
                $d["scanned"] = $scanner->get_recent($swLat, $swLng, $neLat, $neLng, 0, $oSwLat, $oSwLng, $oNeLat, $oNeLng);
            } else {
                $d["scanned"] = $scanner->get_recent($swLat, $swLng, $neLat, $neLng, $timestamp);
            }
        }
    }
}
$debug['6_after_recent'] = microtime(true) - $timing['start'];

$d['token'] = refreshCsrfToken();
$debug['7_end'] = microtime(true) - $timing['start'];

if ($enableDebug == true) {
    foreach ($debug as $k => $v) {
        header("X-Debug-Time-" . $k . ": " . $v);
    }
}

$jaysson = json_encode($d);
echo $jaysson;

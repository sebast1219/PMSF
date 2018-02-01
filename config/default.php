<?php

//======================================================================
// DO NOT EDIT THIS FILE!
//======================================================================

//======================================================================
// PMSF - DEFAULT CONFIG FILE
// https://github.com/Glennmen/PMSF
//======================================================================
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
require_once(__DIR__ . '/../utils.php');

$libs[] = "Scanner.php";
$libs[] = "Monocle.php";
$libs[] = "Monocle_Asner.php";
$libs[] = "Monocle_Monkey.php";
$libs[] = "RocketMap.php";
$libs[] = "RocketMap_Sloppy.php";

// Include libraries
foreach ($libs as $file) {
    include(__DIR__ . '/../lib/' . $file);
}
setSessionCsrfToken();

//-----------------------------------------------------
// MAP SETTINGS
//-----------------------------------------------------

/* Location Settings */

$startingLat = 37.7749295;                                          // Starting latitude
$startingLng = -122.4194155;                                        // Starting longitude

/* Anti scrape Settings */

$maxLatLng = 1;                                                     // Max latitude and longitude size (1 = ~110km, 0 to disable)
$maxZoomOut = 0;                                                    // Max zoom out level (11 ~= $maxLatLng = 1, 0 to disable, lower = the further you can zoom out)
$enableCsrf = false;                                                // Don't disable this unless you know why you need to :)
$sessionLifetime = 43200;                                           // Session lifetime, in seconds

/* Map Title + Language */

$title = "PMSF Glennmen";                                           // Title to display in title bar
$locale = "en";                                                     // Display language

/* Google Maps Key */

$gmapsKey = "";                                                     // Google Maps API Key

/* Google Analytics */

$gAnalyticsId = "";                                                 // "" for empty, "UA-XXXXX-Y" add your Google Analytics tracking ID

/* Piwik Analytics */

$piwikUrl = "";
$piwikSiteId = "";

/* PayPal */

$paypalUrl = "";                                                    // PayPal donation URL, leave "" for empty

/* Discord */

$discordUrl = "";                                                    // Discord URL, leave "" for empty

/* MOTD */

$motdTitle = "";
$motdContent = "";

//-----------------------------------------------------
// FRONTEND SETTINGS
//-----------------------------------------------------

/* Marker Settings */

$noPokemon = false;                                                 // true/false
$noPokemonNumbers = false;                                          // true/false
$enablePokemon = 'true';                                            // true/false
$noHighLevelData = false;                                           // true/false
$noNeverHidePokemon = false;                                             // true/false
$hidePokemon = '[13,16,19,21,39,41,46,48,52,161,163,165,167,177,183,190,194,198,261,263,300,312]';         // [] for empty
$noHidePokemon = false;                                             // true/false
$neverHidePokemon = '[76,112,130,131,143,149,201,242,248]';         // [] for empty

$noHidePokemonByIV = false;                                             // true/false
$hideIv = 0;                                                   // "" for empty or a number
$noHidePokemonByLevel = false;                                             // true/false
$hideLevel = 0;                                                   // "" for empty or a number

$noGyms = false;                                                    // true/false
$enableGyms = 'false';                                              // true/false
$noGymSidebar = false;                                              // true/false
$gymSidebar = 'true';                                               // true/false

$noRaids = false;                                                   // true/false
$enableRaids = 'false';                                             // true/false
$activeRaids = 'false';                                             // true/false
$exRaids = 'false';                                             // true/false
$minRaidLevel = 1;
$maxRaidLevel = 5;

$noPokestops = false;                                               // true/false
$enablePokestops = 'false';                                         // true/false
$enableLured = 1;                                                   // O: all, 1: lured only

$noScannedLocations = false;                                        // true/false
$enableScannedLocations = 'false';                                  // true/false

$noSpawnPoints = false;                                             // true/false
$enableSpawnPoints = 'false';                                       // true/false

$noRanges = false;                                                  // true/false
$enableRanges = 'false';                                            // true/false

/* Location & Search Settings */

$noSearchLocation = false;                                          // true/false

$noStartMe = false;                                                 // true/false
$enableStartMe = 'false';                                           // true/false

$noStartLast = false;                                               // true/false
$enableStartLast = 'false';                                         // true/false

$noFollowMe = false;                                                // true/false
$enableFollowMe = 'false';                                          // true/false

$noSpawnArea = false;                                               // true/false
$enableSpawnArea = 'false';                                         // true/false

/* Notification Settings */

$noNotifyPokemon = false;                                           // true/false
$notifyPokemon = '[]';                                           // [] for empty

$noAlwaysNotifyPokemon = false;                                           // true/false
$alwaysNotifyPokemon = '[76,112,130,131,143,149,201,242,248]';                                           // [] for empty

$noNotifyRarity = false;                                            // true/false
$notifyRarity = '[]';                                               // "Common", "Uncommon", "Rare", "Very Rare", "Ultra Rare"

$noNotifyIv = false;                                                // true/false
$notifyIv = '""';                                                   // "" for empty or a number

$noNotifyLevel = false;                                                // true/false
$notifyLevel = '""';                                                   // "" for empty or a number

$noCombineNotifications = false;                                                // true/false
$combineNotifications = 'false';                                                   // "" for empty or a number

$noNotifyRaid = false;                                              // true/false
$notifyRaid = 5;                                                    // O to disable

$noNotifySound = false;                                             // true/false
$notifySound = 'false';                                             // true/false

$noCriesSound = false;                                             // true/false
$criesSound = 'false';                                             // true/false

/* Style Settings */

$copyrightSafe = true;

$noMapStyle = false;                                                // true/false
$mapStyle = 'style_pgo_dynamic';                                    // roadmap, satellite, hybrid, nolabels_style, dark_style, style_light2, style_pgo, dark_style_nl, style_pgo_day, style_pgo_night, style_pgo_dynamic

$noIconSize = false;                                                // true/false
$iconSize = 0;                                                      // -8, 0, 10, 20

$noGymStyle = false;                                                // true/false
$gymStyle = 'ingame';                                               // ingame, shield

$noLocationStyle = false;                                           // true/false
$locationStyle = 'none';                                            // none, google, red, red_animated, blue, blue_animated, yellow, yellow_animated, pokesition, pokeball

//-----------------------------------------------------
// DEBUGGING
//-----------------------------------------------------

// Do not enable unless requested

$enableDebug = false;

//-----------------------------------------------------
// DATABASE CONFIG
//-----------------------------------------------------

$fork = "default";                                                  // default/asner/sloppy

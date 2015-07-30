<?
$database_host = "127.0.0.1";
$database_name = "genealogy";
$database_username = "root";
$database_password = "XXXXXXX";

$cemeteries_table = "gen_cemeteries";
$headstones_table = "gen_headstones";
$people_table = "gen_people";
$photos_table = "gen_photos";
$histories_table = "gen_histories";
$families_table = "gen_families";
$children_table = "gen_children";
$photolinks_table = "gen_photo_links";
$doclinks_table = "gen_history_links";
$languages_table = "gen_language";
$states_table = "gen_states";
$countries_table = "gen_countries";
$sources_table = "gen_sources";
$citations_table = "gen_src_citations";
$events_table = "gen_events";
$eventtypes_table = "gen_event_types";
$reports_table = "gen_reports";
$trees_table = "gen_trees";
$notelinks_table = "gen_notes";
$xnotes_table = "gen_xref_notes";
$saveimport_table = "gen_save_import";
$users_table = "gen_users";

$rootpath = "/srv/www/htdocs/genealogy/";
$homepage = "index.htm";
$target = "_self";
$language = "English";
$logname = "log_file";
$logfile = $rootpath . $logname;
$maxloglines = "1000";
$maxsearchresults = "300";
$lineendingdisplay = "\\r\\n";
$lineending = "\r\n";
$gendexfile = "gendex";
$photopath = "photos";
$customheader = "topmenu.html";
$customfooter = "footer.html";
$custommeta = "meta.html";
$headstonepath = "headstone";
$historypath = "history";
$backuppath = "backup";
$emailaddr = "Steve@Gledhills.com";
$dbowner = "Steve Gledhill";
$saveimport = "";
$requirelogin = "";
$ldsdefault = "0";
$chooselang = "0";
$nonames = "0";
$photosext = "jpg";
$maxdesc = "6";
$change_cutoff = "30";
$change_limit = "10";
$defaulttree = "";

$cms[support] = "";
$cms[url] = "";
$cms[tngpath] = "";
$cms[module] = "";
$cms[cloaklogin] = "";
$cms[credits] = "";

if(file_exists($cms[tngpath] . "customconfig.php")) { include($cms[tngpath] . "customconfig.php"); }
?>

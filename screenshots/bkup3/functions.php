<?php
/*****************************************************************************
 * secondsToTime (seconds from mid-night)                                    *
 * given a number of seconds, return hh:mm:ss                                *
 *****************************************************************************/
function secondsToTime($sec) {
//echo "initial = $sec<br>";
    if( empty($sec))
        return "";
    return date("H:i:s",$sec);
/*
    $tmp = $sec % 3600;
    $h = ($sec - $tmp) / 3600;
    $sec -= ($h * 3600);
//echo "h = $h, new seconds=$sec<br>";
    $tmp = $sec % 60;
    $m = ($sec - $tmp) / 60;
    $sec -= ($m * 60);
//echo "m = $m, new seconds=$sec<br>";
    $str = $h . ":";
    if($m < 10)
        $str .= "0";
    $str .= $m;

//    $str .= ":";
//    if($sec < 10)
//        $str .= "0";
//    $str .= $sec;
    return (string) $str;
*/
}

/*****************************************************************************
 * timeToSeconds (string hh:mm:ss)                                           *
 * given hh:mm:ss, return the number of seconds                              *
 *****************************************************************************/
function timeToSeconds($strTime) {
    if( empty($strTime))
        return 0;
//echo " original Time=$strTime";
    return strtotime($strtotime);
/*
  $startPos = strpos($strTime, ":");
  if($startPos > 0) {
    $hour = substr($strTime,0,$startPos);
    $min = substr($strTime,$startPos+1);
    if($hour > 0 && hour < 25 && $min >= 0 && $min < 60)
        $seconds= $hour * 3600 + $min * 60;
    }
//echo "hour = ($hour), min = ($min)<br>\n";
    return $seconds;
*/
}

$getAreaShort = array(-1 => "Unassigned", 0 => "Crest", 1 => "Snake", 2 => "Western", 3 => "Millicent",  4 => "Training", 5 => "Staff", 6 => "Any Area");
$getArea      = array(-1 => "Unassigned", 0 => "Crest", 1 => "Snake Creek", 2 => "Great Western",   3 => "Millicent",  4 => "Training", 5 => "Staff");
$getTopShack  = array(-1 => "Unassigned",0 => "Crest Top Shack",   1 => "Snake Creek", 2 => "Western Top", 3 => "Millicent Top", 4 => "Aid Room 1", 5 => "Aid Room 2");
$getTopShackDBName  = array(0 => "Crest Top Shack",   1 => "Snake Creek Top", 2 => "Majestic Top", 3 => "Western Top", 4 => "Millicent Top", 5 => "Aid Room 1", 6 => "Aid Room 2");
$areaCount = 6;
$getShifts=array(0 => "Day"  , 1 => "Swing",         2 => "Full Night", 3=> "3/4 Night", 4 => "Night");
$shiftCount = 5;
$shiftValue=array(0 => 1  , 1 => 1, 2 => 1, 3=> 0.75, 4 => 0.5);

$getTeamLead=array(0 => "No", 1 => "Team Lead", 2 =>"Asst Team Lead", 3=> "Extra");

$shiftsOvr=array(
0 => "Use actual time"  ,
1 => "Saturday 7:45",
2 => "Saturday 13:45",
3 => "Sunday 7:45",
4 => "Monday 7:45",
5 => "Monday 2:45 pm",
6 => "Monday 3:15 pm",
7 => "Monday 6:45pm",
8 => "Monday 11:45pm");

?>

<?php

    // ods_php_parser_usage.php
    
    // Shows an Example how to use ODS PHP Parser.

include("ods_php_parser.php");

/////////////////////////////////////////////////
/*
// Disable Buffering
ini_set('result_buffering', 'off');
ini_set('zlib.result_compression', false);
while (@ob_end_flush());
ini_set('implicit_flush', true);
ob_implicit_flush(true);
header("Content-type: text/plain");
header('Cache-Control: no-cache'); 
*/
/////////////////////////////////////////////////

$nl = "<br>\r\n";
$fn = './uploads/'.$_POST['ods'];  // FileName
$my_ods;
$result;
$k;
$v;
$i; $i_max;
$j; $j_max;
$val;
$range;
$bg;


//---------------------------------------

// Open
$my_ods = new ODS();
$result = $my_ods->Open($fn);
if ($result == FALSE)
{
    echo "Error opening." . $nl; //
    return;
}

//---------------------------------------

// Parse
$my_ods->Parse();

//---------------------------------------

// Close
$result = $my_ods->Close();
if ($result == FALSE)
{
    echo "Error closing." . $nl; //
    return;
}

//---------------------------------------
$input = array();

for ($i = 1; $i <= $my_ods->SheetCount; $i++)
{
    $input[$i-1] = [];
    for ($j = $my_ods->Sheets[$i][ODS::FIRST_USED_ROW]; $j <= $my_ods->Sheets[$i][ODS::LAST_USED_ROW]; $j++)
    {
        $input[$i-1][$j-1] = [];
        for ($k = $my_ods->Sheets[$i][ODS::FIRST_USED_COL]; $k <= $my_ods->Sheets[$i][ODS::LAST_USED_COL]; $k++)
        {
            $val = ''.PHP_EOL;
            if ( isset( $my_ods->Sheets[$i][ODS::CELLS][$j][$k][ODS::VALUE] ) )
            {
                $val = $my_ods->Sheets[$i][ODS::CELLS][$j][$k][ODS::VALUE];
            }
            if ($val == '')
            {
                $val = '&nbsp;'.PHP_EOL;
            }
            else
            {
                $val = htmlentities($val, ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
            }
            $input[$i-1][$j-1][] = $val;
        }
    }
}

//---------------------------------------

$result = '<Teachers_List>'.PHP_EOL;
foreach ($input[0] as $key => $row) {
    if ($key == 0) continue;
    if (trim($row[0]) == '&NewLine;') break;
    
    $result .= '    <Teacher>'.PHP_EOL;
    $result .= '        <Name>'.str_replace('&num;', '#', $row[0]).'</Name>'.PHP_EOL;
    $result .= '        <Target_Number_of_Hours>0</Target_Number_of_Hours>'.PHP_EOL;
	$result .= '        <Qualified_Subjects>'.PHP_EOL;
	$result .= '        </Qualified_Subjects>'.PHP_EOL;
	$result .= '        <Comments></Comments>'.PHP_EOL;
    $result .= '    </Teacher>'.PHP_EOL;
}
$result .= '</Teachers_List>'.PHP_EOL;

$groups = $input[0][0];
$groups = array_slice($groups, 2);
array_pop($groups);

$result .= '<Students_List>'.PHP_EOL;
$result .= '    <Year>'.PHP_EOL;
$result .= '        <Name>'.date('Y.').'</Name>'.PHP_EOL;
$result .= '        <Number_of_Students>0</Number_of_Students>'.PHP_EOL;
$result .= '        <Comments></Comments>'.PHP_EOL;
$result .= '        <Number_of_Categories>0</Number_of_Categories>'.PHP_EOL;
$result .= '        <Separator></Separator>'.PHP_EOL;

foreach ($groups as $group) {
        $result .= '        <Group>'.PHP_EOL;
        $result .= '            <Name>'.$group.'</Name>'.PHP_EOL;
        $result .= '            <Number_of_Students>20</Number_of_Students>'.PHP_EOL;
        $result .= '            <Comments></Comments>'.PHP_EOL;
        $result .= '        </Group>'.PHP_EOL;
}

$result .= '    </Year>'.PHP_EOL;
$result .= '</Students_List>'.PHP_EOL;

$result .= '<Activities_List>'.PHP_EOL;
$activity_id = 0;

foreach ($input[0] as $key => $row) {
    if ($key == 0) continue;

    foreach ($groups as $col => $group) {
        $duration = $row[$col+2];
        if ($duration == 0) continue;

        if ($duration <= 2) {
            $result .= '    <Activity>'.PHP_EOL;
            $result .= '        <Teacher>'.str_replace('&num;', '#', $row[0]).'</Teacher>'.PHP_EOL;
            $result .= '        <Subject>'.$row[1].'</Subject>'.PHP_EOL;
            $result .= '        <Students>'.$group.'</Students>'.PHP_EOL;
            $result .= '        <Duration>'.$duration.'</Duration>'.PHP_EOL;
            $result .= '        <Total_Duration>'.$row[$col+2].'</Total_Duration>'.PHP_EOL;
            $result .= '        <Id>'.(++$activity_id).'</Id>'.PHP_EOL;
            $result .= '        <Activity_Group_Id>0</Activity_Group_Id>'.PHP_EOL;
            $result .= '        <Active>true</Active>'.PHP_EOL;
            $result .= '        <Comments></Comments>'.PHP_EOL;
            $result .= '    </Activity>'.PHP_EOL;
            continue;
        }

        $activity_group_id = $activity_id + 1;
        while ($duration > 0) {
            $result .= '    <Activity>'.PHP_EOL;
            $result .= '        <Teacher>'.str_replace('&num;', '#', $row[0]).'</Teacher>'.PHP_EOL;
            $result .= '        <Subject>'.$row[1].'</Subject>'.PHP_EOL;
            $result .= '        <Students>'.$group.'</Students>'.PHP_EOL;
            $result .= '        <Duration>'.($duration==1?1:2).'</Duration>'.PHP_EOL;
            $result .= '        <Total_Duration>'.$row[$col+2].'</Total_Duration>'.PHP_EOL;
            $result .= '        <Id>'.(++$activity_id).'</Id>'.PHP_EOL;
            $result .= '        <Activity_Group_Id>'.$activity_group_id.'</Activity_Group_Id>'.PHP_EOL;
            $result .= '        <Active>true</Active>'.PHP_EOL;
            $result .= '        <Comments></Comments>'.PHP_EOL;
            $result .= '    </Activity>'.PHP_EOL;
            $duration -= 2;
        }
    }
}
$result .= '</Activities_List>'.PHP_EOL;

$myfile = fopen("./converted/".$_POST['ods'].".fet", "w") or die("Unable to open file!");
$header = file_get_contents("./header.txt");
fwrite($myfile, $header);
fwrite($myfile, $result);
$footer = file_get_contents("./footer.txt");
fwrite($myfile, $footer);
fclose($myfile);
echo "success";
?>

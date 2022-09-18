<?php
session_start();

$HEADER = $_SESSION["EXCEL_HEADER"];
$DATA = $_SESSION["EXCEL_DATA"];
$FILE = $_SESSION["EXCEL_FILE"];

# ... 01: Deduce Header Data
$HH = "";
if (sizeof($HEADER)>0) {
  $HH = "<tr valign='top'>";
  for ($i=0; $i < sizeof($HEADER); $i++) { 
    $HH .= "<th>".$HEADER[$i]."</th>";
  }
  $HH .= "</tr>";
}


# ... 02: Data Rows
$ROW_LIST = "";
if (sizeof($DATA)>0) {
  for ($i=0; $i < sizeof($DATA); $i++) {
    $ROW = array();
    $ROW = $DATA[$i];

    $DD = "<tr valign='top>'";
    for ($j=0; $j < sizeof($ROW); $j++) { 
      $DD .= "<td style='mso-number-format: \\@;'>".$ROW[$j]."</td>";
    }
    $DD .= "</tr>";

    $ROW_LIST .= $DD;
  }
}


// ... 03: PACKAGE EXCEL DATA
$EXCEL_DATA = "<table border='1' align='left'>".$HH."".$ROW_LIST."</table>";


// ... 04: TRIGGER EXCEL DOWNLOAD                 
header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header("Content-Disposition: attachment; filename=$FILE");
echo $EXCEL_DATA;
?>

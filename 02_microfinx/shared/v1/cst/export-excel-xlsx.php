<?php
session_start();
require_once 'PHPExcel-1.8/Classes/PHPExcel.php';

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

    $DD = "<tr valign='top'>";
    for ($j=0; $j < sizeof($ROW); $j++) { 
      $DD .= "<td style='mso-number-format: \\@;'>".$ROW[$j]."</td>";
    }
    $DD .= "</tr>";

    $ROW_LIST .= $DD;
  }
}

// ... 03: PACKAGE EXCEL DATA
$EXCEL_DATA = "<table border='1' align='left'>".$HH."".$ROW_LIST."</table>";


// ... 04 SAVE TABLE TO TEMP FILE
$tmpfile = tempnam(sys_get_temp_dir(), 'html');
file_put_contents($tmpfile, $EXCEL_DATA);

// ... 05: START THE XLSX PROCESSING
$objPHPExcel = new PHPExcel();
$excelHTMLReader = PHPExcel_IOFactory::createReader('HTML');
$excelHTMLReader->loadIntoExisting($tmpfile, $objPHPExcel);
$objPHPExcel->getActiveSheet()->setTitle('TestFile'); // Change sheet's title if you want

unlink($tmpfile); // delete temporary file because it isn't needed anymore

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('DATA');
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$FILE.'"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>

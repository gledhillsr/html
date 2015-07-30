<?php 
// Has a spreadsheet been created? 
if ( !file_exists('binary.xls') ) { 

 // Include PEAR::Spreadsheet_Excel_Writer 
 require_once "Spreadsheet/Excel/Writer.php"; 
  
 // Create an instance, passing the filename to create 
 $xls =& new Spreadsheet_Excel_Writer('binary.xls'); 
  
 // Add a worksheet to the file, returning an object to add data to 
 $sheet =& $xls->addWorksheet('Binary Count'); 
  
 // Write some numbers 
 for ( $i=0;$i<11;$i++ ) { 
   // Use PHP's decbin() function to convert integer to binary 
   $sheet->write($i,0,decbin($i)); 
 } 
  
 // Finish the spreadsheet, dumping it to the browser 
 $xls->close(); 
} 
?>
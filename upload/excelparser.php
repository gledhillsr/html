<?php
include_once("debug.php");
include_once("exceldate.php");
include_once("excelfont.php");

//------------------------------------------------------------------------
// ABC Excel Parser Pro
//
// Version 2.5
// Copyright (c) 2002 Zakkis Technology, Inc.
// All rights reserved.
//
// This script parses a binary Excel file and store all data in an array.
// For more information see README.TXT file included in this distribution.
//
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
// "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
// LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
// FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
// REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
// INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
// SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
// HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
// STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
// ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
// OF THE POSSIBILITY OF SUCH DAMAGE.
//
//------------------------------------------------------------------------


class ExcelFileParser {

	var $f_header;
	var $fc;
	var $sfc;
	var $max_blocks;
	var $max_sblocks;
	var $fat;
	var $sbd;
	var $sfat;

	var $syear;
	var $formats;
	var $xf;
	var $fonts;

    var $dbglog;


    function ExcelFileParser($logfile="",$level=ABC_NO_LOG) {
	$this->dbglog = &DebugOut::getWriterSingleton($logfile,"",$level);
        $this->dbglog->info("Logger started");
    }

	function populateFormat() {
	$this->dbglog->trace(" populateFormat() function call");

	$ret = array (
        	0=> "General",
        	1=> "0",
        	2=> "0.00",
        	3=> "#,##0",
        	4=> "#,##0.00",
        	5=> "($#,##0_);($#,##0)",
        	6=> "($#,##0_);[Red]($#,##0)",
        	7=> "($#,##0.00);($#,##0.00)",
        	8=> "($#,##0.00_);[Red]($#,##0.00)",
        	9=> "0%",
        	0xa=> "0.00%",
        	0xb=> "0.00E+00",
        	0xc=> "# ?/?",
        	0xd=> "# ??/??",
        	0xe=> "m/d/yy",
        	0xf=> "d-mmm-yy",
        	0x10=> "d-mmm",
        	0x11=> "mmm-yy",
        	0x12=> "h:mm AM/PM",
        	0x13=> "h:mm:ss AM/PM",
        	0x14=> "h:mm",
        	0x15=> "h:mm:ss",
        	0x16=> "m/d/yy h:mm",

        	// 0x17 - 0x24 reserved for international and undocumented
        	0x17=> "0x17",
        	0x18=> "0x18",
        	0x19=> "0x19",
        	0x1a=> "0x1a",
        	0x1b=> "0x1b",
	        0x1c=> "0x1c",
        	0x1d=> "0x1d",
	        0x1e=> "0x1e",
	        0x1f=> "0x1f",
	        0x20=> "0x20",
	        0x21=> "0x21",
	        0x22=> "0x22",
	        0x23=> "0x23",
	        0x24=> "0x24",

        	// 0x17 - 0x24 reserved for international and undocumented
        	0x25=> "(#,##0_);(#,##0)",
        	0x26=> "(#,##0_);[Red](#,##0)",
        	0x27=> "(#,##0.00_);(#,##0.00)",
        	0x28=> "(#,##0.00_);[Red](#,##0.00)",
        	0x29=> "_(*#,##0_);_(*(#,##0);_(* \"-\"_);_(@_)",
        	0x2a=> "_($*#,##0_);_($*(#,##0);_($* \"-\"_);_(@_)",
        	0x2b=> "_(*#,##0.00_);_(*(#,##0.00);_(*\"-\"??_);_(@_)",
        	0x2c=> "_($*#,##0.00_);_($*(#,##0.00);_($*\"-\"??_);_(@_)",
        	0x2d=> "mm:ss",
        	0x2e=> "[h]:mm:ss",
        	0x2f=> "mm:ss.0",
        	0x30=> "##0.0E+0",
	        0x31=> "@");

            $this->dbglog->dump($ret,"\$ret");
            $this->dbglog->trace("populateFormat() function return");

        	return $ret;

	}

	function str2long($str) {
		return ord($str[0]) + 256*(ord($str[1]) +
			256*(ord($str[2]) + 256*(ord($str[3])) ));
	}

	function xls2tstamp($date) {
	$date=$date>25568?$date:25569;
	/*There was a bug if Converting date before 1-1-1970 (tstamp 0)*/
   		$ofs=(70 * 365 + 17+2) * 86400;
  		 return ($date * 86400) - $ofs;
	}


	function getDateArray($date) {
	   return ExcelDateUtil::getDateArray($date);
	}

	function isDateFormat($val){
		$f_i=$this->xf['format'][$val];
		if(preg_match("/[m|d|y]/i",$this->format[$f_i])!=0){
		    if(strrpos($this->format[$f_i],'[')!=FALSE) {
		        $tmp = preg_replace("/(\[\/?)(\w+)([^\]]*\])/","'\\1'.''.'\\3'",$this->format[$f_i]);
		    	if(preg_match("/[m|d|y]/i",$tmp)!=0)
		    	   return TRUE;
		    	 else
		    	   return FALSE;
		    } else {
		        return TRUE;
		    }
		} else
		  return FALSE;
	}


	function getUnicodeString($str,$ofs){
	   $size=0;
	   $i_ofs=0;
/*	   if (ord($str[$ofs])==255) {
	     $size=ord($str[$ofs])+ 256*(ord($str[$ofs+1]));
	     $i_ofs=2;
	   } else {*/
	     $size=ord($str[$ofs]);
	     $i_ofs=1;
/*	   }*/
	   return substr($str,$ofs+$i_ofs+1,$size);

	}


	function getByteString($str,$ofs){
	   $size=0;
	   $i_ofs=0;
//	   if (ord($str[$ofs])==255) {
//	     $size=ord($str[$ofs])+ 256*(ord($str[$ofs+1]));
//	     $i_ofs=2;
//	   } else {
	     $size=ord($str[$ofs]);
	     $i_ofs=1;
//	   }
	   return substr($str,$ofs+$i_ofs+1,$size);
	}



	/*
	 * Get blocks chain
	 */
	function get_blocks_chain($start,$small_fat=false) {

	$this->dbglog->trace("get_blocks_chain(".var_export($start,true).",".var_export($small_fat,true).") function call ");
		$chain = array();

		$next_block = $start;
		if( !$small_fat ) {
			while(  ($next_block!=0xfffffffe) &&
				($next_block <= $this->max_blocks) &&
				(($next_block+1) <= count($this->fat)) ) {
				$chain[] = $next_block;
				$next_block = $this->fat[$next_block];
			}
		} else {
			while(  ($next_block!=0xfffffffe) &&
				($next_block <= $this->max_sblocks) &&
				(($next_block+1) <= count($this->sfat)) ) {
				$chain[] = $next_block;
				$next_block = $this->sfat[$next_block];
			}
		}
		if( $next_block != 0xfffffffe ) return false;

		$this->dbglog->dump($chain,"\$chain");
		$this->dbglog->trace("get_blocks_chain() function return");
		return $chain;
	}

	/* Find stream by name
	 *
	 */

	function find_stream($item_name,$item_num=0) {

        $this->dbglog->trace("find_stream(".var_export($item_name,true).",".var_export($item_num,true).") function call ");

		$dt = ord($this->directory[$item_num*0x80+0x42]);
		$prev = $this->str2long(substr($this->directory,$item_num*0x80+0x44,4));
		$next = $this->str2long(substr($this->directory,$item_num*0x80+0x48,4));
		$dir = $this->str2long(substr($this->directory,$item_num*0x80+0x4c,4));

		$curr_name = '';
		if( ($dt==2) || ($dt==5) )
			for( $i=0;
			 $i < (ord($this->directory[$item_num*0x80+0x40]) +
			  256*ord($this->directory[$item_num*0x80+0x41]))/2-1;
			 $i++ )
				$curr_name .= $this->directory[$item_num*0x80+$i*2];

		if( (($dt==2) || ($dt==5)) && (strcmp($curr_name,$item_name)==0) ){
		    $this->dbglog->trace("find_stream() function return with ".var_export($item_num,true));
			return $item_num;
		}

		if( $prev != 0xffffffff ) {
			$i = $this->find_stream($item_name,$prev);
			if( $i>=0 ){
			    $this->dbglog->trace("find_stream() function return with ".var_export($i,true));
    			return $i;
   			 }
		}
		if( $next != 0xffffffff ) {
			$i = $this->find_stream($item_name,$next);
			if( $i>=0 ){
			    $this->dbglog->trace("find_stream() function return with ".var_export($i,true));
			    return $i;
			}
		}
		if( $dir != 0xffffffff ) {
			$i = $this->find_stream($item_name,$dir);
			if( $i>=0 ) {
			    $this->dbglog->trace("find_stream() function return with ".var_export($i,true));
			    return $i;
			}
		}
        $this->dbglog->trace("find_stream() function return with -1");
		return -1;
	}

	function rk_decode($rk) {

	    $this->dbglog->trace("rk_decode(".var_export($rk,true).") function call");
		$res = array();
		if( $rk & 2 ) {
			// integer
			$val = ($rk & 0xfffffffc) >> 2;
			if( $rk & 1 ) $val = $val / 100;
			if (((float)$val) == floor((float)$val)){
			   $res['val'] = (int)$val;
			   $res['type'] = 1;
			} else {
			   $res['val'] = (float)$val;
			   $res['type'] = 2;
			}

		} else {
			// float
			$res['type'] = 2;
			$frk = $rk;

			$fexp =  (($frk & 0x7ff00000) >> 20) - 1023;
			$val = 1+(($frk & 0x000fffff) >> 2)/262144;

			if( $fexp > 0 ) {
				for( $i=0; $i<$fexp; $i++ )
					$val *= 2;
			} else {
				if( $fexp==-1023 ) {
					$val=0;
				} else {
				 for( $i=0; $i<abs($fexp); $i++ )
					$val /= 2;
				}
			}

			if( $rk & 1 ) $val = $val / 100;
			if( $rk & 0x80000000 ) $val = -$val;

			$res['val'] = (float)$val;
		}
		$this->dbglog->trace("rk_decode() function returns");
		return $res;
	}

	// Parse worksheet
	//-----------------

	function parse_worksheet($ws) {

        $this->dbglog->debug("parse_worksheet(DATA) function");
		if( strlen($ws) <= 0 ){
		    $this->dbglog->trace("parse_worksheet() function returns 7 (Data not Found)");
    	    return 7;
    	}
		if( strlen($ws) <  4 ){
		    $this->dbglog->trace("parse_worksheet() function returns 6 (File Corrupted)");
		    return 6;
		}

		// parse workbook header
		if( strlen($ws) < 256*ord($ws[3])+ord($ws[2]) ) return 6;

		if( ord($ws[0]) != 0x09 ) return 6;
		$vers = ord($ws[1]);
		if( ($vers!=0) && ($vers!=2) && ($vers!=4) && ($vers!=8) )
			return 8;

		if( $vers!=8 ) {
		 $biff_ver = ($ver+4)/2;
		} else {
		 if( strlen($ws) < 12 ) return 6;
		 switch( ord($ws[4])+256*ord($ws[5]) ) {
			case 0x0500:
				if( ord($ws[0x0a])+256*ord($ws[0x0b]) < 1994 ) {
					$biff_ver = 5;
				} else {
					switch(ord( $ws[8])+256*ord($ws[9]) ) {
					 case 2412:
					 case 3218:
					 case 3321:
/*dbg*/ 	            $this->dbglog->debug("Parsed BIFF version is 5");
						$biff_ver = 5;
						 break;
					 default:
					    $this->dbglog->debug("Parsed BIFF version is 7");
						$biff_ver = 7;
						break;
					}
				}
				break;
			case 0x0600:
/*DBG*/		    $this->dbglog->debug("Parsed BIFF version is 8");
				$biff_ver = 8;
				break;
			default:
				return 8;
		 }
		}

		if( $biff_ver < 5 ) {
/*DBG*/  $this->dbglog->debug("parse_worksheet() function found ($biff_ver < 5) return 8");
		  return 8;
		}
		$ptr = 0;
		$data = array('biff_version' => $biff_ver );

		while( (ord($ws[$ptr])!=0x0a) && ($ptr<strlen($ws)) ) {
		 switch (ord($ws[$ptr])+256*ord($ws[$ptr+1])) {

		  // Number
		  case 0x0203:
/*DBG*/     $this->dbglog->trace("found NUMBER");

			if( ($biff_ver < 3) ){
/*DBG*/         $this->dbglog->trace("$biff_ver < 3 break;");
			    break;
			}
			if( (ord($ws[$ptr+2])+256*ord($ws[$ptr+3])) < 14 ){
/*DBG*/         $this->dbglog->debug("parse_worksheet() return 6");
				return 6;
			}

			$row = ord($ws[$ptr+4])+256*ord($ws[$ptr+5]);
			$col = ord($ws[$ptr+6])+256*ord($ws[$ptr+7]);
			$num_lo = $this->str2long(substr($ws,$ptr+10,4));
			$num_hi = $this->str2long(substr($ws,$ptr+14,4));
			$xf_i = ord($ws[$ptr+8])+256*ord($ws[$ptr+9]);

			if($this->isDateFormat($xf_i)){
				$data['cell'][$row][$col]['type'] = 3;
			} else {
				$data['cell'][$row][$col]['type'] = 2;
			}

			$fonti = $this->xf['font'][$xf_i];
		    $data['cell'][$row][$fc+$i]['font'] = $fonti;



			$fexp = (($num_hi & 0x7ff00000) >> 20) - 1023;
			$val = 1+(($num_hi & 0x000fffff)+$num_lo/4294967296)/1048576;

			if( $fexp > 0 ) {
				for( $i=0; $i<$fexp; $i++ )
					$val *= 2;
			} else {
				for( $i=0; $i<abs($fexp); $i++ )
					$val /= 2;
			}
			if( $num_hi & 0x80000000 ) $val = -$val;

			$data['cell'][$row][$col]['data'] = (float)$val;

			if( !isset($data['max_row']) ||
			    ($data['max_row'] < $row) )
				$data['max_row'] = $row;

			if( !isset($data['max_col']) ||
			    ($data['max_col'] < $col) )
				$data['max_col'] = $col;

			break;

		  // RK
		  case 0x027e:
/*DBG*/  $this->dbglog->trace("found RK");
			if( ($biff_ver < 3) ) break;
			if( (ord($ws[$ptr+2])+256*ord($ws[$ptr+3])) < 0x0a )
				return 6;
			$row  = ord($ws[$ptr+4])+256*ord($ws[$ptr+5]);
			$col  = ord($ws[$ptr+6])+256*ord($ws[$ptr+7]);
			$xf_i = ord($ws[$ptr+8])+256*ord($ws[$ptr+9]);
			$val  = $this->rk_decode(
					$this->str2long(substr($ws,$ptr+10,4))
				);

			if($this->isDateFormat($xf_i)==TRUE){
				$data['cell'][$row][$col]['type'] = 3;
			} else {
				$data['cell'][$row][$col]['type'] = $val['type'];
			}
			$fonti = $this->xf['font'][$xf_i];

		    $data['cell'][$row][$col]['font'] = $fonti;
			$data['cell'][$row][$col]['data'] = $val['val'];


			if( !isset($data['max_row']) ||
			    ($data['max_row'] < $row) )
				$data['max_row'] = $row;

			if( !isset($data['max_col']) ||
			    ($data['max_col'] < $col) )
				$data['max_col'] = $col;

			break;

		  // MULRK
		  case 0x00bd:
/*DBG*/  $this->dbglog->trace("found  MULL RK");
			if( ($biff_ver < 5) ) break;
			$sz = ord($ws[$ptr+2])+256*ord($ws[$ptr+3]);
			if( $sz < 6 ) return 6;

			$row = ord($ws[$ptr+4])+256*ord($ws[$ptr+5]);
			$fc = ord($ws[$ptr+6])+256*ord($ws[$ptr+7]);
			$lc = ord($ws[$ptr+$sz+2])+256*ord($ws[$ptr+$sz+3]);

			for( $i=0; $i<=$lc-$fc; $i++) {
			 $val = $this->rk_decode(
				$this->str2long(substr($ws,$ptr+10+$i*6,4))
				);

			   $xf_i=ord($ws[$ptr+8+$i*6])+256*ord($ws[($ptr+9+$i*6)]);
			   if($this->isDateFormat($xf_i)==TRUE) {
			   	$data['cell'][$row][$fc+$i]['type'] = 3;
			   } else {
			   	$data['cell'][$row][$fc+$i]['type'] = $val['type'];
			   }
			   $fonti = $this->xf['font'][$xf_i];
		       $data['cell'][$row][$fc+$i]['font'] = $fonti;
			   $data['cell'][$row][$fc+$i]['data'] = $val['val'];
			}

			if( !isset($data['max_row']) ||
			    ($data['max_row'] < $row) )
				$data['max_row'] = $row;

			if( !isset($data['max_col']) ||
			    ($data['max_col'] < $lc) )
				$data['max_col'] = $lc;

			break;

		  // LABEL
		  case 0x0204:
/*DBG*/  $this->dbglog->trace("found LABEL");
			if( ($biff_ver < 3) ){
			    break;
			}
			if( (ord($ws[$ptr+2])+256*ord($ws[$ptr+3])) < 8 ){
				return 6;
			}
			$row = ord($ws[$ptr+4])+256*ord($ws[$ptr+5]);
			$col = ord($ws[$ptr+6])+256*ord($ws[$ptr+7]);
			$xf = ord($ws[$ptr+8])+256*ord($ws[$ptr+9]);
			$fonti = $this->xf['font'][$xf];
			$font =  $this->fonts[$fonti];


			$str_len = ord($ws[$ptr+10])+256*ord($ws[$ptr+11]);

			if( $ptr+12+$str_len > strlen($ws) )
				return 6;
			$this->sst['unicode'][] = false;
			$this->sst['data'][] = substr($ws,$ptr+12,$str_len);
			$data['cell'][$row][$col]['type'] = 0;
			$sst_ind = count($this->sst['data'])-1;
			$data['cell'][$row][$col]['data'] = $sst_ind;
			$data['cell'][$row][$col]['font'] = $fonti;

/*			echo str_replace("\n","<br>\n", ExcelFont::toString($font,$fonti));
		    echo "FontRecord for sting ".$this->sst['data'][$sst_ind]."<br>";*/

			if( !isset($data['max_row']) ||
			    ($data['max_row'] < $row) )
				$data['max_row'] = $row;

			if( !isset($data['max_col']) ||
			    ($data['max_col'] < $col) )
				$data['max_col'] = $col;



			break;

		  // LABELSST
		  case 0x00fd:
			if( $biff_ver < 8 ) break;
			if( (ord($ws[$ptr+2])+256*ord($ws[$ptr+3])) < 0x0a )
				return 6;
			$row = ord($ws[$ptr+4])+256*ord($ws[$ptr+5]);
			$col = ord($ws[$ptr+6])+256*ord($ws[$ptr+7]);
			$xf = ord($ws[$ptr+8])+256*ord($ws[$ptr+9]);
			$fonti = $this->xf['font'][$xf];
			$font = &$this->fonts[$fonti];


			$data['cell'][$row][$col]['type'] = 0;
			$sst_ind = $this->str2long(substr($ws,$ptr+10,4));
			$data['cell'][$row][$col]['data'] = $sst_ind;
			$data['cell'][$row][$col]['font'] = $fonti;

/*            echo "FontRecord for sting at $row,$col<br>";
			echo str_replace("\n","<br>\n", ExcelFont::toString($font,$fonti));*/

			if( !isset($data['max_row']) ||
			    ($data['max_row'] < $row) )
				$data['max_row'] = $row;

			if( !isset($data['max_col']) ||
			    ($data['max_col'] < $col) )
				$data['max_col'] = $col;

			break;

		  // unknown, unsupported or unused opcode
		  default:
			break;
		 }

		 $ptr += 4+256*ord($ws[$ptr+3])+ord($ws[$ptr+2]);
		}
/*DEBUG*/ $this->dbglog->debug("parse_worksheet() function returns ".var_export($data,true)); /*DEBUG*/

		return $data;

	}

	// Parse workbook
	//----------------

	function parse_workbook() {

/*DBG*/ $this->dbglog->debug("parse_workbook() function");

		$header_sig = array(0xd0,0xcf,0x11,0xe0,0xa1,0xb1,0x1a,0xe1);

		// check file header
		for( $i=0; $i<count($header_sig); $i++ )
			if( $header_sig[$i] != ord($this->f_header[$i]) ){
/*DBG*/    	    $this->dbglog->error("parse_workbook() function founds invalid header");
/*DBG*/    	    $this->dbglog->error("parse_workbook() function returns 5");
				return 5;
            }
		$root_entry_block = $this->str2long(substr($this->f_header,0x30,4));
		$num_fat_blocks = $this->str2long(substr($this->f_header,0x2c,4));

/*TRC*/ $this->dbglog->trace("Header parsed");

		$this->fat = array();
		for( $i=0; $i<$num_fat_blocks; $i++ ){
/*TRC*/		$this->dbglog->trace("FOR LOOP iteration i =".$i);
			$fat_block = $this->str2long(substr($this->f_header,0x4c+4*$i,4));
			$fat = substr($this->fc,$fat_block*0x200,0x200);

			if( strlen($fat) < 0x200 ){
/*DBG*/    	   $this->dbglog->debug("parse_workbook() function found (strlen($fat) < 0x200) returns 6");
			    return 6;
			}

			for( $j=0; $j<0x80; $j++ ){
			 $this->fat[] = $this->str2long(substr($fat,$j*4,4));
			}
		}
		if( count($this->fat) < $num_fat_blocks ){
/*DBG*/    	$this->dbglog->debug("parse_workbook() function found (count($this->fat) < $num_fat_blocks) returns 6");
			return 6;
		}

		$chain = $this->get_blocks_chain($root_entry_block);
		$this->directory = '';
		for( $i=0; $i<count($chain); $i++ )
			$this->directory .= substr($this->fc,$chain[$i]*0x200,0x200);

		$this->sfat = array();
		$small_block = $this->str2long(substr($this->f_header,0x3c,4));
		if( $small_block != 0xfeffffff )  {
		 $root_entry_index = $this->find_stream('Root Entry');
		 if( $root_entry_index < 0 ){
/*DBG*/    	$this->dbglog->debug("parse_workbook() function dont found Root Entry returns 6");
		    return 6;
		 }
		 $sdc_start_block = $this->str2long(substr($this->directory,$root_entry_index*0x80+0x74,4));
		 $small_data_chain = $this->get_blocks_chain($sdc_start_block);
		 $this->max_sblocks = count($small_data_chain) * 8;

		 $schain = $this->get_blocks_chain($small_block);
		 for( $i=0; $i<count($schain); $i++ ) {
		  $sfat = substr($this->fc,$schain[$i]*0x200,0x200);
		  if( strlen($sfat) < 0x200 ){
/*DBG*/    	 $this->dbglog->debug("parse_workbook() function found (strlen($sfat) < 0x200)  returns 6");
		     return 6;
 	      }
		  for( $j=0; $j<0x80; $j++ )
		   $this->sfat[] = $this->str2long(substr($sfat,$j*4,4));
		 }

		 $this->sfc = '';
		 for( $i=0; $i<count($small_data_chain); $i++ )
		  $this->sfc .= substr($this->fc,$small_data_chain[$i]*0x200,0x200);
		}

		$workbook_index = $this->find_stream('Workbook');
		if( $workbook_index<0 ) {
			$workbook_index = $this->find_stream('Book');
			if( $workbook_index<0 ){
/*DBG*/    	    $this->dbglog->debug("parse_workbook() function workbook index not found returns 7");
				return 7;
			}
		}

		$workbook_start_block = $this->str2long(substr($this->directory,$workbook_index*0x80+0x74,4));
		$workbook_length = $this->str2long(substr($this->directory,$workbook_index*0x80+0x78,4));
		$wb = '';

		if( $workbook_length > 0 ) {
		 if( $workbook_length >= 0x1000 ) {
			$chain = $this->get_blocks_chain($workbook_start_block);
			for( $i=0; $i<count($chain); $i++ )
				$wb .= substr($this->fc,$chain[$i]*0x200,0x200);
		 } else {
			$chain = $this->get_blocks_chain($workbook_start_block,true);
			for( $i=0; $i<count($chain); $i++ )
				$wb .= substr($this->sfc,$chain[$i]*0x40,0x40);
		 }
			$wb = substr($wb,0,$workbook_length);
			if( strlen($wb) != $workbook_length )
				return 6;
		}

		if( strlen($wb) <= 0 ){
/*DBG*/    $this->dbglog->debug("parse_workbook() function workbook found (strlen($wb) <= 0) returns 7");
		   return 7;
		}
		if( strlen($wb) <  4 ) {
/*DBG*/    $this->dbglog->debug("parse_workbook() function workbook found (strlen($wb) < 4) returns 6");
		    return 6;
		}

		// parse workbook header
		if( strlen($wb) < 256*ord($wb[3])+ord($wb[2]) ){
/*DBG*/    $this->dbglog->debug("parse_workbook() function workbook found (strlen($wb) < 256*ord($wb[3])+ord($wb[2])) < 4) returns 6");
		    return 6;
		}

		if( ord($wb[0]) != 0x09 ){
/*DBG*/    $this->dbglog->debug("parse_workbook() function workbook found (ord($wb[0]) != 0x09) returns 6");
		    return 6;
		}
		$vers = ord($wb[1]);
		if( ($vers!=0) && ($vers!=2) && ($vers!=4) && ($vers!=8) ){
			return 8;
        }
		if( $vers!=8 ) {
		 $biff_ver = ($ver+4)/2;
		} else {
		 if( strlen($wb) < 12 ) return 6;
		 switch( ord($wb[4])+256*ord($wb[5]) ) {
			case 0x0500:
				if( ord($wb[0x0a])+256*ord($wb[0x0b]) < 1994 ) {
					$biff_ver = 5;
				} else {
					switch(ord( $wb[8])+256*ord($wb[9]) ) {
					 case 2412:
					 case 3218:
					 case 3321:
						$biff_ver = 5;
						break;
					 default:
						$biff_ver = 7;
						break;
					}
				}
				break;
			case 0x0600:
				$biff_ver = 8;
				break;
			default:
				return 8;
		 }
		}

		if( $biff_ver < 5 ) return 8;

		$ptr = 0;
		$this->worksheet['offset'] = array();
		$this->worksheet['options'] = array();
		$this->worksheet['unicode'] = array();
		$this->worksheet['name'] = array();
		$this->worksheet['data'] = array();
		$this->format = $this->populateFormat();
		$this->fonts = array();
		$this->fonts[0] = ExcelFont::basicFontRecord();


		$this->xf = array();
		$this->xf['format'] = array();
		$this->xf['font'] = array();
		$this->xf['type_prot'] = array();
		$this->xf['alignment'] = array();
		$this->xf['decoration'] = array();



		$xf_cnt=0;


		$this->sst['unicode'] = array();
		$this->sst['data'] = array();

		$opcode = 0;
		$sst_defined = false;

		while( (ord($wb[$ptr])!=0x0a) && ($ptr<strlen($wb)) ) {
		 $oc = ord($wb[$ptr])+256*ord($wb[$ptr+1]);
		 if( $oc != 0x3c ) $opcode = $oc;
		 switch ($opcode) {
		  case 0x0085:
			$ofs = $this->str2long(substr($wb,$ptr+4,4));
			$this->worksheet['offset'][] = $ofs;
			$this->worksheet['options'][] =
			 ord($wb[$ptr+8])+256*ord($wb[$ptr+9]);
			if( $biff_ver==8 ) {
			 $len = ord($wb[$ptr+10]);
			 if( (ord($wb[$ptr+11]) & 1) > 0 ) {
			 	$this->worksheet['unicode'][] = true;
				$len = $len*2;
			 } else {
			 	$this->worksheet['unicode'][] = false;
			 }
			 $this->worksheet['name'][] = substr($wb,$ptr+12,$len);
			} else {
			 $this->worksheet['unicode'][] = false;
			 $len = ord($wb[$ptr+10]);
			 $this->worksheet['name'][] = substr($wb,$ptr+11,$len);
			}

			$pws = $this->parse_worksheet(substr($wb,$ofs));
			if( is_array($pws) )
				$this->worksheet['data'][] = $pws;
			else
				return $pws;
			break;

		 // Format
		  case 0x041e:
 		  	$fidx = ord($wb[$ptr+4])+256*ord($wb[$ptr+5]);
		  	if($fidx<0x31 ||$fidx==0x31 )
		  	    break;
		  	else {
		  	  if($biff_ver>7) {
		  	  	$this->format[$fidx] = $this->getUnicodeString($wb,$ptr+6);
		  	  } else{
		  	  }
		  	}
		        break;

		 // FONT 0x31
		   case EXCEL_FONT_RID:
                $rec = ExcelFont::getFontRecord($wb,$ptr+4);
                $this->fonts[count($this->fonts)] = $rec;
/*echo str_replace("\n","<br>\n",ExcelFont::toString($rec,count($this->fonts)-1));
echo "FontRecord<br>" */;
		        break;

		 // XF
		  case 0x00e0:

		  	$this->xf['font'][$xf_cnt] = ord($wb[$ptr+4])+256*ord($wb[$ptr+5]);
		  	$this->xf['format'][$xf_cnt] = ord($wb[$ptr+6])+256*ord($wb[$ptr+7]);
		  	$this->xf['type'][$xf_cnt]  = "1";
		  	$this->xf['bitmask'][$xf_cnt] = "1";
		  	$xf_cnt++;
		        break;

		  // SST
		  case 0x00fc:
			if( $biff_ver < 8 ) break;

			$sbuflen = ord($wb[$ptr+2]) + 256*ord($wb[$ptr+3]);

			if( $oc != 0x3c ) {
			 if( $sst_defined ) return 6;
			 $snum = $this->str2long(substr($wb,$ptr+8,4));
			 $sptr = $ptr+12;
			 $sst_defined = true;
			} else {
			 if( $rslen > $slen ) {
				$sptr = $ptr+4;
				$rslen -= $slen;
				$slen = $rslen;

				if( (ord($wb[$sptr]) & 1) > 0 ) {
				 if( $char_bytes == 1 ) {
				  $sstr = '';
				  for( $i=0; $i<strlen($str); $i++ )
				  	$sstr .= $str[$i].chr(0);
				  $str = $sstr;
				  $char_bytes=2;
				 }
				 $schar_bytes = 2;
				} else {
				 $schar_bytes = 1;
				}

				if( $sptr+$slen*$schar_bytes > $ptr+4+$sbuflen )
					$slen = ($ptr+$sbuflen-$sptr+3)/$schar_bytes;

				$sstr = substr($wb,$sptr+1,$slen*$schar_bytes);

				if( ($char_bytes == 2) && ($schar_bytes == 1) )
				{
					$sstr2 = '';
					for( $i=0; $i<strlen($sstr); $i++ )
						$sstr2 .= $sstr[$i].chr(0);
					$sstr = $sstr2;
				}
				$str .= $sstr;

				$sptr += $slen*$schar_bytes+1+4*$rt+$fesz;
			 	if( $slen < $rslen ) {
					if( ($sptr >= strlen($wb)) ||
					    ($sptr < $ptr+4+$sbuflen) ||
					    (ord($wb[$sptr]) != 0x3c) ) return 6;
					break;
			 	} else {
					if( $char_bytes == 2 ) {
						$this->sst['unicode'][] = true;
					} else {
						$this->sst['unicode'][] = false;
					}
					$this->sst['data'][] = $str;
					$snum--;
			 	}
			 } else {
				$sptr = $ptr+4;
				if( $sptr > $ptr ) $sptr += 4*$rt+$fesz;
			 }
			}

			while(  ($sptr < $ptr+4+$sbuflen) &&
				($sptr < strlen($wb)) &&
				($snum > 0) )
			{
			 $rslen = ord($wb[$sptr])+256*ord($wb[$sptr+1]);
			 $slen = $rslen;

			 if( (ord($wb[$sptr+2]) & 1) > 0 ) {
				$char_bytes = 2;
			 } else {
				$char_bytes = 1;
			 }

			 $rt = 0;
			 $fesz = 0;
			 switch (ord($wb[$sptr+2]) & 0x0c) {
			  // Rich-Text with Far-East
			  case 0x0c:
				$rt = ord($wb[$sptr+3])+256*(ord($wb[$sptr+4]));
				$fesz = $this->str2long(substr($wb,$sptr+5,4));
				if( $sptr+9+$slen*$char_bytes > $ptr+4+$sbuflen )
					$slen = ($ptr+$sbuflen-$sptr-5)/$char_bytes;
				$str = substr($wb,$sptr+9,$slen*$char_bytes);
				$sptr += $slen*$char_bytes+9;
				break;

			  // Rich-Text
			  case 8:
				$rt = ord($wb[$sptr+3])+256*(ord($wb[$sptr+4]));
				if( $sptr+5+$slen*$char_bytes > $ptr+4+$sbuflen )
					$slen = ($ptr+$sbuflen-$sptr-1)/$char_bytes;
				$str = substr($wb,$sptr+5,$slen*$char_bytes);
				$sptr += $slen*$char_bytes+5;
				break;

			  // Far-East
			  case 4:
				$fesz = $this->str2long(substr($wb,$sptr+3,4));
				if( $sptr+7+$slen*$char_bytes > $ptr+4+$sbuflen )
					$slen = ($ptr+$sbuflen-$sptr-3)/$char_bytes;
				$str = substr($wb,$sptr+7,$slen*$char_bytes);
				$sptr += $slen*$char_bytes+7;
				break;

			  // Compressed or uncompressed unicode
			  case 0:
				if( $sptr+3+$slen*$char_bytes > $ptr+4+$sbuflen )
					$slen = ($ptr+$sbuflen-$sptr+1)/$char_bytes;
			 	$str = substr($wb,$sptr+3,$slen*$char_bytes);
			 	$sptr += $slen*$char_bytes+3;
				break;
			 }

			 if( $slen < $rslen ) {
				if( ($sptr >= strlen($wb)) ||
				    ($sptr < $ptr+4+$sbuflen) ||
				    (ord($wb[$sptr]) != 0x3c) ) return 6;
			 } else {
				if( $char_bytes == 2 ) {
					$this->sst['unicode'][] = true;
				} else {
					$this->sst['unicode'][] = false;
				}
				$sptr += 4*$rt+$fesz;
				$this->sst['data'][] = $str;
			 	$snum--;
			 }
			} // switch
			break;
		 } // switch
			$this->wsb[] = substr($wb,$ptr,4+256*ord($wb[$ptr+3])+ord($wb[$ptr+2]));
			$ptr += 4+256*ord($wb[$ptr+3])+ord($wb[$ptr+2]);
		} // while

		  $this->workbook = $wb;
		  $this->biff_version = $biff_ver;
/*DBG*/   $this->dbglog->debug("parse_workbook() function returns 0");
		   return 0;

	}

	// ParseFromString & ParseFromFile
	//---------------------------------
	//
	// IN:
	//	string contents - File contents
	//	string filename - File name of an existing Excel file.
	//
	// OUT:
	//	0 - success
	//	1 - can't open file
	//	2 - file too small to be an Excel file
	//	3 - error reading header
	//	4 - error reading file
	//	5 - This is not an Excel file or file stored in < Excel 5.0
	//	6 - file corrupted
	//	7 - data not found
	//	8 - Unsupported file version

	function ParseFromString($fc) {




		$file_size = strlen($fc);
		if( !$file_size || ($file_size <= 0x200) )
			return 2;

		$this->max_blocks = (int)(($file_size-1) / 0x200) - 1;

		// read file header
		$this->f_header = substr($fc,0,0x200);
		if( strlen($this->f_header) < 0x200 )
			return 3;

		// read rest of file
		$this->fc = substr($fc,0x200);
		if( strlen($this->fc) < $file_size-0x200 )
			return 4;

		return $this->parse_workbook();

		$debug->close();
	}

	function ParseFromFile($filename) {

		// trying to open file
		$fh = @fopen ($filename,'rb');
		if( !$fh ) return 1;

		$file_size = filesize($filename);
		if( !$file_size || ($file_size <= 0x200) ) {
			@fclose($fh);
			return 2;
		}

		$this->max_blocks = (int)(($file_size-1) / 0x200) - 1;

		// read file header
		$this->f_header = fread( $fh, 0x200 );
		if( strlen($this->f_header) < 0x200 ) {
			@fclose($fh);
			return 3;
		}

		// read rest of file
		$this->fc = fread( $fh, $file_size-0x200 );
		if( strlen($this->fc) < $file_size-0x200 ) {
			@fclose($fh);
			return 4;
		}

		// close file
		@fclose($fh);

		return $this->parse_workbook();
	}

}

?>
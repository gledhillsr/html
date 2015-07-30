/*
 * BTN.java
 *
 * Created on December 12, 2002, 1:43 PM
 */

import java.sql.*;
import java.net.*;
import java.util.*;
import java.io.*;
import java.text.*;
import javax.swing.*;


/**
 *
 * @author  Gledhill
 */
public class MonthlyService {

    final static String MONSERV_FILE = "Monserv.det";
//18 fields    
    final static int BTN = 0;
    final static int DATE = 1;
    final static int WTN = 4;
    final static int QTY = 8;
    final static int USOC = 9;
    final static int DESCRIPTION = 10;
    final static int TOTAL = 15;
    final static int FIELD_COUNT = 18;
    
    Vector col = new Vector(FIELD_COUNT);    //exact number of columns
    Vector row;
    String btn;	//col[0]
    String lastBTN;
    String invDate;   //col[1]
    private boolean exceptionError;
    static private javax.swing.JFrame frame = null;
    private Customer customer;

    DecimalFormat myFormatter;
    double Totals = 0;
    double SubTotal = 0.0;
    GregorianCalendar date = new GregorianCalendar();
    int rowCount = 0;
    int wtnCount = 0;
/**** database fields *********************************/    
/*************************************/    
    static boolean DEBUG = false;
	static String DEBUG_BTN_STR = "8019674402733xx";
//	static String DEBUG_BTN_STR = "8019429260298";
//	
    boolean DEBUG(String str)  {
		return str.indexOf(DEBUG_BTN_STR) != -1 ? true : false;
	}
    boolean DEBUG(BTN btn)  {
		return btn.getBTN().indexOf(DEBUG_BTN_STR) != -1 ? true : false;
	}

//class BubbleSort2Algorithm extends SortAlgorithm {
    void sort(Object a[]) throws Exception {
	for (int i = a.length; --i>=0; ) {
            boolean flipped = false;
	    for (int j = 0; j<i; j++) {
//		if (stopRequested) {
//		    return;
//		}
		
//		if (a[j] > a[j+1]) {
		String[] aa = (String[])a[j];
		String[] bb = (String[])a[j+1];
                int cmp = aa[0].compareTo(bb[0]);   //compare BTN's'
                int cmp2 = aa[4].compareTo(bb[4]);   //compare BTN's'
		if ( cmp > 0 || (cmp == 0 && cmp2 > 0)) {
		    String[] T = (String[]) a[j];
		    a[j] = a[j+1];
		    a[j+1] = T;
		    flipped = true;
		}
//		pause(i,j);

	    }
	    if (!flipped) {
	        return;
	    }
        }
    }
//}


    
/** Creates a new instance of BTN */
public MonthlyService(String szDir,javax.swing.JFrame frm,Customer cust,Vector MSBtns) {
    File[] pStr;
    int max = -1;
    int i;
    if(DEBUG)
        System.out.println("Processing MonthlyService directory "+szDir);
    frame = frm;
    customer = cust;
    RandomAccessFile MSFile = null;;
    myFormatter = new DecimalFormat("###,###.00");
    File rootDir = new File(szDir);
    File subDir;
    File file;
    pStr = rootDir.listFiles();  //list all files or directories
    max = pStr.length;
System.out.println("max="+max);
    for(i=0; i < max; ++i) {
        subDir = pStr[i];
System.out.println("subDir="+subDir.getAbsolutePath()+",: is directory="+subDir.isDirectory());
        file = null;
        String fileName = null;
        if(subDir.isDirectory())
            fileName = subDir.getAbsolutePath() + subDir.separator + MONSERV_FILE;
        else if(subDir.getName().equalsIgnoreCase(MONSERV_FILE))     //the ignore case junk was just for windows
            fileName =  szDir + subDir.separator + subDir.getName();
        if(fileName != null)
            file = new File(fileName);
        if(file != null && file.exists()) {
            try {
System.out.println("scanning file: "+file);
                MSFile = new RandomAccessFile(file, "r");
                if(MSFile != null) {
                    if(DEBUG)
                        System.out.println("opening: "+file);
                    processMSRecords(MSFile, cust, MSBtns, file);
                    MSFile.close();
                }
            } catch (Exception e) {
                System.out.println("Error opening:"+e);
                new MessageDialog(frame,"Error opening file "+file+".","OK");            
//              return;
            }
        }
    }
    
    btn = "";
    exceptionError = false;
}
private boolean processMSRecords(RandomAccessFile MSFile, Customer cust, Vector MSBtns, File file){
    try {
        //read file here
        int rowCount = 0;
        String line = MSFile.readLine();    
        String lastWTN = "";
        int lastWTNPos = 0;
        while(line != null && line.length() > 0) {
//boolean myDebug = false;                
            rowCount++;
            if(rowCount != 1 && line.indexOf("#1",1) != 1) {
                if(DEBUG(line))
                    System.out.println("processing: "+line);
//                    else
//                        System.out.print(".");                    
                String[] args = line.split(",");
                if(args.length != FIELD_COUNT) {
                //==split did not work, so do it myself!!!!
                //  check for commas within strings==
                    if(DEBUG(line))
                        System.out.println(rowCount+") = "+line);
                    int startIndex=0, lastIndex;
                    int pos = 0; 
//                        if(line.charAt(0) == '"') 
//                            startIndex++;	//skip past bogus starting " character
                    args = new String[FIELD_COUNT];
                    lastIndex = getNextComma(line,startIndex);
                    while(lastIndex != -1){
                        if(pos >= FIELD_COUNT){
                            //ERROR
                            return false;
                        }
                        if(DEBUG(line))
                            System.out.println(pos+") = "+line.substring(startIndex,lastIndex));
                            args[pos++] = line.substring(startIndex,lastIndex);
                            startIndex = lastIndex+1;
                            lastIndex = getNextComma(line,startIndex);
                        }
                        if(pos != FIELD_COUNT) {
//if correct count, then convert all ` characters back to commas in any quoted strings
//                            if(DEBUG)
                                System.out.println("Error in MONSERV.DET, processing line "+rowCount+", there were "+pos+" arguments (should be "+FIELD_COUNT+").");
		                System.out.println("Line: "+line);
                            MessageDialog md = new MessageDialog(frame,"Error in MONSERV.DET, processing line "+rowCount+", there were "+args.length+" arguments (should be "+FIELD_COUNT+").","Skip", "Abort");
                            if(md.btnSel == 2)  //2nd button is abort
                                return false;
                        }
                    } 
                    
                    //remove leading/trailing " (quote) if it exists, for all args
                    int i;
                    for(i=0; i < FIELD_COUNT; ++i){
                        if(args[i].charAt(0) == '"') {
                            args[i] = args[i].substring(1,args[i].length()-1);
                        }
                    }
//                    args[BTN] = args[BTN].trim();

                    if(row == null)
                        row = new Vector();
                    //I can add code here to make sure vector is sorted. using row.insertElementAt()
//xxxx                    row.add(args);
//zzzz                    
                    //I can add code here to make sure vector is sorted. using row.insertElementAt()
                    //remove trailing " O" or " 6" from strings
if(DEBUG(args[BTN])) 
  System.out.println("args[BTN]=("+args[BTN]+")");
                    if(args[BTN].indexOf(" ") == (args[BTN].length()-2)) {
                        args[BTN] = args[BTN].substring(0,args[BTN].length()-2);
if(DEBUG(args[BTN])) 
  System.out.println("trimmed args[BTN]=("+args[BTN]+")");
				    }
                    String currWTN = args[WTN];
                    String currBTN = args[BTN];
                    if(row.size() == 0) {
                        row.add(args);
                        lastWTNPos = 0;
                        lastWTN = currWTN;
if(DEBUG(args[BTN])) 
  System.out.println("add to end of row array"+args[0]+" "+args[4]);

                    }else {
//                            row.insertElementAt(args,++idx);
                        
                        if(currWTN.equals(lastWTN)){
                            row.insertElementAt(args,++lastWTNPos);
                        } else {
                            int idx = 0;
                            int cmp=-1;
                            int last = row.size();
                            do {
                                String[] argList = (String[])row.elementAt(idx);
                                String tmpWTN = argList[WTN];
                                String tmpBTN = argList[BTN];
//if(currBTN.startsWith("8015665200653") || currBTN.startsWith("8013528708379")) 
//    System.out.println("..");
                                cmp = currBTN.compareTo(tmpBTN);
//System.out.println(idx+") curr=["+currBTN+"] tmpBTN=["+tmpBTN+"]  compare="+currBTN.compareTo(tmpBTN));
                                if(cmp < 0) {   //looked to far (tmpBTN is AFTER currBTN)
//                                    idx--;      //insert at position of temBTN (slides tmpBTN higher)
                                    break;  
                                }
                                if(cmp==0)  {   //currBTN == tmpBTN
                                    cmp = currWTN.compareTo(tmpWTN);
                                    if(cmp > 0) {
//                                        idx--;      //backup up 1 and save
                                        break;  //insert line at idx                                
                                    }
                                }
                            } while(++idx < last );
                            row.insertElementAt(args,idx);
                            lastWTNPos = idx;
                            lastWTN = currWTN;
                        } //end test if same WTN
//if(myDebug)                    
//  System.out.println(idx+") "+args[0]+" "+args[4]);
                    
                    } //end test is row size > 0
//zzzzz                    


                    if(DEBUG(args[BTN]))
                        System.out.println("added BTN["+args[BTN]+"], new size= "+row.size());
                    int last = MSBtns.size();
                    //add BTN's (not WTN's) to dialog box

                    if (last == 0) {
                        MSBtns.add(args[0]);
                    } else {
                        String strLast = (String)MSBtns.elementAt(last-1);
                        if(!strLast.equals(args[0]))
                            MSBtns.add(args[0]);
                    }

                } //end if valid line
                line = MSFile.readLine();
            } //END LOOP FOR EACH Line in the input file
        } catch (Exception e) {
            System.out.println("Error processing: "+e);
            new MessageDialog(frame,"Error processing file "+file+".","OK");            
            return false;
    }
    return true;
}


private int getNextComma(String line,int startIndex) {
    int quoteIndex,commaIndex;
    if(startIndex >= line.length())
        return -1;  //end of line
    quoteIndex = line.indexOf('"',startIndex);
    commaIndex = line.indexOf(',',startIndex);
    if (commaIndex == -1) {
        return line.length(); //end of line
    } else if (quoteIndex == -1 || commaIndex < quoteIndex) { //no quote, or comma is first
        return commaIndex;
    } else if(commaIndex < quoteIndex) { //comma is first
        return commaIndex;
    }  else
      quoteIndex = line.indexOf('"',quoteIndex+1);
      if (quoteIndex == -1) {
        //error, mis-matched quotes
        return -1;
      }
      commaIndex = line.indexOf(',',quoteIndex+1);
      if(commaIndex == -1)  //quoted character was at EOL
        commaIndex = line.length(); //end of line
      return commaIndex;
    }

public boolean writeHTMLs(DBAccess dbAccess) {
     String subDir = BTNfile.getHtmlDir(customer.getID());
     String szDir = BTNfile.homeDir+subDir;
     int idx = 0;
     int cnt = row.size();
     RandomAccessFile out = null;
     //loop for each row
     lastBTN="";
     Totals = 0.0;
     SubTotal = 0.0;
     //initialize any totals here
   try {
     while(idx < cnt){
         String[] args = (String[])row.elementAt(idx);
         //if new row, close old file, and open new file
         btn=args[BTN]; //this should never change within the class
         invDate=args[DATE];
//System.out.println("idx ="+btn.indexOf(" ")+"   pos="+(btn.length()-2));
         if(btn.indexOf(" ") == (btn.length()-2))
             btn = btn.substring(0,btn.length()-2);
         
         if(DEBUG)
            System.out.println("writeHTMLs - "+szDir+"\\"+btn+"_MS.html");
//boolean myDebug = false;
//if(btn.startsWith("8015665200653")) {
//myDebug = true;
//System.out.println(idx+"] btn="+args[0]+" wtn="+args[4]);
//}
         
         //open (or keep open) correct BTN file
         if(!lastBTN.equals(btn)){
             //if old file exists, output footer and close
if(DEBUG)
  System.out.println("new file");
             if(out != null){
                 //close file
                 writeMSFooter(dbAccess,out);
                 out.close();                  
             }
             //create and initialize new file
             String fileName = szDir+"\\"+btn+"_MS.html";
             out = new RandomAccessFile(fileName, "rw");
             out.setLength(0);
             writeMSHeader(out);
//if(myDebug)             
// System.out.println("generating file for "+btn);                 
             lastBTN = btn;
             //initialize totals here also
         }
         writeMSBody(out,idx);
         idx++;
     }
    } catch (Exception e) {
        //debug message
         System.out.println("Error: "+e+" on file :"+szDir+"\\"+btn+".");         
    }
     if (out != null) {
        try {
         //close file
            writeMSFooter(dbAccess,out);
            out.close();    //can throw IO exception
        } catch (Exception e) {
            //debug message
        }
     }
return true;    
}

public boolean updateDB() {
return true;    
}

public boolean read(ResultSet results) {
	exceptionError = false;
/*	
        btn = DBAccess.readString(results,"BTN",exceptionError);
        String str = DBAccess.readString(results,"LastUpdated",exceptionError);
        LastUpdated = 0;
        try {
            LastUpdated = Long.parseLong(str);
        } catch (Exception e) { }
        Location = DBAccess.readString(results,"Location",exceptionError);
        Division = DBAccess.readString(results,"Division",exceptionError);
        Department = DBAccess.readString(results,"Department",exceptionError);
        AMC = DBAccess.readString(results,"AMC",exceptionError);
        Description = DBAccess.readString(results,"Description",exceptionError);
        ContractExpDate = 0;
        str = DBAccess.readString(results,"ContractExpDate",exceptionError);
        try {
            ContractExpDate = Long.parseLong(str);
        } catch (Exception e) { }
        Cost = DBAccess.readDouble(results,"Cost",exceptionError);
        Review = DBAccess.readString(results,"Review",exceptionError);
        CustomerID = DBAccess.readString(results,"CustomerID",exceptionError);
        ReviewNotes = DBAccess.readString(results,"ReviewNotes",exceptionError);

//new
        prevCost 		= DBAccess.readDouble(results,"prevCost",exceptionError);
        prevUpdated 	= DBAccess.readLong(results,"prevUpdated",exceptionError);

		MonthlySavings 	= DBAccess.readDouble(results,"MonthlySavings",exceptionError);
		Product 		= DBAccess.readInt(results,"Product",exceptionError);
		MRC 			= DBAccess.readDouble(results,"MRC",exceptionError);
*/
        return exceptionError;
    }
    public String getBTN()          {        return btn;    }
/*	
    public long getLastUpdated()    {      return LastUpdated;    }
    public String getLocation()     {      return Location;    }
    public String getDivision()     {      return Division;    }
    public String getDepartment()   {      return Department;    }
    public String getAMC()          {      return AMC;    }
    public String getDescription()  {      return Description;    }
    public long getContractExpDate(){   return ContractExpDate;    }
    public double getCost()         {      return Cost;    }
    public String getReview()       {      return Review;    }
    public String getCustomerID()   {      return CustomerID;    }
    public double getPrevCost()     {      return prevCost;    }
    public long getPrevUpdated()    {      return prevUpdated;    }

    public void setLastUpdated(long millis) { LastUpdated = millis; }
    public void setCost(double amt)         { Cost = amt;    }
    public void setAMC(String str)          { AMC  = str;    }
    public void setPrevCost(double amt)     { prevCost = amt; }
    public void setPrevUpdated(long millis) { prevUpdated = millis; }
*/	
//-----------------------------------------------------
// getSQLUpdateString
//-----------------------------------------------------
    public String getSQLUpdateString() {
    String qryString = "UPDATE btn SET " +
/*	
        " LastUpdated=\"" + LastUpdated +
        "\", Location=\"" + Location +
        "\", Division=\"" + Division +
        "\", Department=\"" + Department +
        "\", AMC=\"" + AMC +
        "\", Description=\"" + Description +
        "\", ContractExpDate=\"" + ContractExpDate +
        "\", Cost=\"" + Cost +
        "\", Review=\"" + Review +
        "\", CustomerID=\"" + CustomerID +
        "\", ReviewNotes=\"" + ReviewNotes +        
        "\", prevCost=\"" + prevCost +
        "\", prevUpdated=\"" + prevUpdated +
        "\", MonthlySavings=\"" + MonthlySavings +
        "\", Product=\"" + Product +
        "\", MRC=\"" + MRC +
*/
        "\" WHERE BTN=\"" + btn + "\"";
    if(DEBUG)
        System.out.println(qryString);
    return qryString;
    }


//-----------------------------------------------------
// getSQLResetByCustomerIDString
//-----------------------------------------------------
//    public static String getSQLResetByCustomerIDString(String CustomerID) {
//        String qryString= "SELECT * FROM btn WHERE CustomerID=\""+CustomerID+"\""; //select 1 btn
//        if(DEBUG)
//            System.out.println(qryString);
//        return qryString;
//    }
    
//-----------------------------------------------------
// getSQLSelectBTNString
//-----------------------------------------------------
    public static String getSQLSelectBTNString(String btn) {
        String qryString= "SELECT * FROM btn WHERE BTN=\""+btn+"\""; //select 1 btn
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLResetString
//-----------------------------------------------------
    public static String getSQLResetString() {
        String qryString= "SELECT * FROM btn ORDER BY BTN"; //sort by default key
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }
    
//-----------------------------------------------------
// getSQLInsertString
//-----------------------------------------------------
    public String getSQLInsertString() {
        String qryString = "INSERT INTO btn "+
        " Values(\""+ btn + 
/*		
        "\",\"" + LastUpdated +
        "\",\"" + Location + 
        "\",\"" + Division +
        "\",\"" + Department +
        "\",\"" + AMC +
        "\",\"" + Description +
        "\",\"" + ContractExpDate +
        "\",\"" + Cost +
        "\",\"" + Review +
        "\",\"" + CustomerID + 
        "\",\"" + ReviewNotes + 
        "\",\"" + prevCost + 
        "\",\"" + prevUpdated + 
        "\",\"" + MonthlySavings + 
        "\",\"" + Product + 
        "\",\"" + MRC + 
*/
        "\")" ;
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

//-----------------------------------------------------
// getSQLDeleteString
//-----------------------------------------------------
    public String getSQLDeleteString() {
        int i;
        String qryString = "DELETE FROM btn WHERE BTN=\""+btn+"\"";
        if(DEBUG)
            System.out.println(qryString);
        return qryString;
    }

    public String toString() {
        return btn;
    }

/*********************************
 *  writeSummaryHeader         
 *********************************/
    public void writeMSHeader(RandomAccessFile out) throws IOException {
        Totals = 0.0;
        SubTotal = 0.0;
        rowCount = 0;
        wtnCount = 0;
        String str = 
"<html>\n"+
"<head>\n"+
"<meta http-equiv=\"Content-Language\" content=\"en-us\">\n"+
"<meta http-equiv=\"Content-Type\" content=\"text/html; charset=windows-1252\">\n"+
"<title>Monthly Service</title>\n"+
"<style type=\"text/css\">\n"+
"<!--\n"+
"body  {font-size:12; color:#000000; background-color:#ffffff  background=\"../../../ncmnthbk.jpg\"}\n"+
"table {border-width:1px; border-color:#ffffff; border-style:solid; border-collapse:collapse; border-spacing:0}\n"+
"th    {font-size:14; font-weight: bold; color:#000000; background-color:#ffffff; border-width:1px; border-color:#ffffff; border-style:solid; padding:2px}\n"+
"tr  {font-size: 9pt; font-family: Arial, serif; line-height: 11pt; color:#000000; } \n" +
".r1 {font-size: 8pt; background-color:#F0F0E8; font-family: Arial, Helvetica, sans-serif; } \n" +
".r2 {font-size: 8pt; background-color:#F7F7EF; font-family: Arial, Helvetica, sans-serif; } \n" +
".ry {font-size: 8pt; background-color:#FFFFCC; font-family: Arial, Helvetica, sans-serif; } \n" +
".rw {font-size:12; color:#000000; background-color:#ffffff; border-width:1px; border-color:#ffffff; border-style:solid; padding:1px}\n"+
"-->\n"+
"</style>\n"+
"</head>\n"+
"<body background=\"../../../ncmnthbk.jpg\">\n"+
"<p align=\"center\"><font size=5>Monthly Service - "+customer.getName()+"</font></p>\n"+
"<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">\n"+
"  <tr class=rw>\n"+
"    <td width=\"50%\"><font size=2>Billing Telephone Number: <b>"+btn+"</b></font></td>\n"+
"    <td width=\"50%\"><font size=2>\n"+
//invDate                
"      <p align=\"right\">Dated: "+invDate.substring(0,2)+"/"+invDate.substring(2,4)+"/"+invDate.substring(4)+"</font></td>\n"+
"  </tr>\n"+
"</table>\n"+
"<p align=\"left\">&nbsp;</p>\n"+
"<table border=0 cellpadding=0 cellspacing=0 width=\"100%\">\n"+
"  <tr class=rw>\n"+
"    <td width=\"10%\"><font size=2>Tel&nbsp;Number</font></td>\n"+
"    <td width=\"10%\" align=\"center\"><font size=2>&nbsp;Qty&nbsp;</font></td>\n"+
"    <td width=\"10%\"><font size=2>&nbsp;USOC&nbsp;</font></td>\n"+
"    <td width=\"60%\"><font size=2>&nbsp;USOC&nbsp;Desc&nbsp;</font></td>\n"+
"    <td width=\"10%\" align=\"right\"><font size=2>&nbsp;Total&nbsp;</font></td>\n"+
"  </tr>\n";
    out.writeBytes(str);
    }
    
/*********************************
 *  writeMSBody         
 *********************************/
    public void writeMSBody(RandomAccessFile out, int idx) throws IOException {
        String[] args = (String[])row.elementAt(idx);
        boolean showSubtotal = false;
        if(idx < row.size()-1 ) {
            String[] nextArgs = (String[])row.elementAt(idx+1);
            if(!args[WTN].equals(nextArgs[WTN]))
                showSubtotal = true;
        } else {
            showSubtotal = true;
        }
            
        double tot=-1;
        try {
        String str=args[TOTAL]; //this should never change within the class
            tot = Double.valueOf(str.trim()).doubleValue();
            Totals += tot;
            SubTotal += tot;

        } catch (Exception e) {
System.out.println("Exception error:"+e+", evaluating "+args[TOTAL]);
        }
        rowCount++;
        String tr;
        if((rowCount % 2) == 1) {
            tr = " class=r1";
        } else {
            tr = " class=r2";
        }
        String str= 
"  <tr"+tr+">\n"+
"    <td>"+args[WTN]+"</td>\n"+
"    <td align=\"center\">"+args[QTY]+"</td>\n"+
"    <td>"+args[USOC]+"</td>\n"+
"    <td>"+args[DESCRIPTION]+"</td>\n"+
"    <td align=\"right\">$"+myFormatter.format(tot)+"</td>\n"+
"  </tr>\n";
                        
         out.writeBytes(str);
         if(showSubtotal) {
             showSubs(out);
         }
    }

    public void showSubs(RandomAccessFile out) throws IOException {
        String str= 
"  <tr class=ry>\n"+
"    <td></td> <td></td> <td></td> <td></td> <td align=\"right\"><b>$"+myFormatter.format(SubTotal)+"</b></td>\n"+
"  </tr>\n"+
"  <tr class=ry>\n"+
"    <td>&nbsp;</td> <td></td> <td></td> <td></td> <td></td>\n"+
"  </tr>\n";
        SubTotal = 0.0;
//int zzzz = row.size();
//if(zzzz == rowCount+1)
//System.out.println(zzzz+"  "+rowCount+"    ");
         out.writeBytes(str);
}
    
/*********************************
 *  writeSummaryFooter         
 *********************************/
    public void writeMSFooter(DBAccess dbAccess, RandomAccessFile out) throws IOException {

//    showSubs(out);
    
        String str = 
"<tr class=rw>\n"+
" <td></td> <td></td> <td></td> <td></td>\n"+
" <td align=\"right\"><b>$"+myFormatter.format(Totals)+"</b></td>\n"+
"</tr>\n"+
"</table>\n"+
"</body>\n"+
"</html>\n";
        out.writeBytes(str);
        if(DEBUG(lastBTN)) 
            System.out.println("writeMSFooter ("+lastBTN+")");

        String LocationAddress = "";
        BTN btnDB = dbAccess.readBTN(lastBTN);
        if(DEBUG(lastBTN)){
            System.out.println("readBTN returned: "+btnDB);
            btnDB.DEBUG = true;  //turn on internal debugging for the BTN object (SQL queries)
        }

        if(btnDB == null) {
            btnDB = new BTN(lastBTN,customer.getID(),LocationAddress,date.getTimeInMillis(),0.0);
            if(DEBUG(lastBTN)){
        	System.out.println("new BTN returned "+btnDB);
                btnDB.DEBUG = true;
            }
                long millis;
                try {
                    int year = Integer.parseInt(invDate.substring(4));
                    int month = Integer.parseInt(invDate.substring(0,2)) - 1;	//month should be 0 based
                    int dom = Integer.parseInt(invDate.substring(2,4));
                    GregorianCalendar gc = new GregorianCalendar(year, month, dom);
                    if(DEBUG(lastBTN))
                        System.out.println("date was OK");
                    millis = gc.getTimeInMillis();
                } catch (Exception e) {
System.out.println("invalid date, using today, invDate=("+invDate+") m=("+invDate.substring(0,2)+"), d=("+invDate.substring(2,4)+"), y=("+invDate.substring(4)+")");
                    millis = date.getTimeInMillis();
                }
                btnDB.setLastUpdated(millis);
            btnDB.setMonthlyService(Totals);
//remember to for for ServiceOrders            
            if(btnDB != null) {
               dbAccess.addBTN(btnDB);
if(DEBUG(lastBTN))System.out.println("btnDB added");
            }
        } else {
            if(customer.getID().equals(btnDB.getCustomerID())) {
                //CONVERT invDate TO Millis
                //zzzzz
                long millis;
                try {
                    int year = Integer.parseInt(invDate.substring(4));
                    int month = Integer.parseInt(invDate.substring(0,2)) - 1;	//month should be 0 based
                    int dom = Integer.parseInt(invDate.substring(2,4));
                    GregorianCalendar gc = new GregorianCalendar(year, month, dom);
                    millis = gc.getTimeInMillis();
                } catch (Exception e) {
System.out.println("invalid date, using today, invDate=("+invDate+") m=("+invDate.substring(0,2)+"), d=("+invDate.substring(2,4)+"), y=("+invDate.substring(4)+")");
                    millis = date.getTimeInMillis();
                }
                btnDB.setLastUpdated(millis);
                btnDB.setMonthlyService(Totals);
if(DEBUG(lastBTN))
    System.out.println("updateBTN("+btnDB.getBTN()+")");
                dbAccess.updateBTN(btnDB);
            } else {
                new MessageDialog(frame,"Error, BTN "+lastBTN+" is now under a different customer.  The old customer is "
                    +btnDB.getCustomerID()+", and the new customer is "+customer.getID(),"OK");            
                
            }
        }

    }
}  /* end MonthlyService class */

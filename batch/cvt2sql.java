/*
 * DoRobot.java
 *
 * Created on February 28, 2003, 6:32 AM
 */

/**
 *
 * @author  Steve
 */
import java.io.*;
import java.util.*;
import java.awt.*;
import java.awt.event.*;

public class cvt2sql {
boolean trace = false;
boolean debug = false;
float rc1,rc12,rc24,rc36,rc48,rc60,rc84;
float nrc1,nrc12,nrc24,nrc36,nrc48,nrc60,nrc84;
String carrier;
String npanxx;
String RBOC,t2;
String date;
String wireCenter,CLLI,POP,CLLI_2;
float miles;
boolean parsingMonths;
boolean parsingWireCenter,parsingMiles,parsingPOP,done;
int nrcState,popState;

    /** Creates a new instance of DoRobot */
    public cvt2sql(String[] args) {
        if(args.length != 1 && args.length != 22) {
            System.out.println("Error!  Usage: java cvt2sql <directory> [<-debug> <-traceRobot>]");
            System.exit(1);
        }
        String szDir = args[0];
        if(args.length == 2) {
//System.out.println("("+args[1].substring(0,2)+")");            
            if(args[1].substring(0,2).equals("-d")) {
                debug = true;
            } else if(args[1].substring(0,2).equals("-t")) {
                trace = true;
			}
        }
        System.out.println("Using directory file: " + szDir);
        File dr = new File(szDir);
        if(dr == null || !dr.exists()  || !dr.isDirectory()) {
            System.out.println("Error!  Directory: (" + szDir + ") must exist.");
            System.exit(1);
        }
		//loop through all files looking for correct names
		File[] files = dr.listFiles();
		for(int i=0; i < files.length; ++i) {
			String szFileName = files[i].getName();
			if(szFileName.endsWith(".txt") && szFileName.startsWith("npanxx.")) {
				int pos2 =szFileName.indexOf('.',8);
				carrier = szFileName.substring(7,pos2);
				npanxx = szFileName.substring(pos2+1,pos2+7);
				if(parseFile(files[i]))
                {
                    if(!POP.equals("XX"))
                        writeData();
                    else
                        System.out.println("Could not finish processing");
                }
			}
		}
		
	}
// ---------
	private boolean parseFile(File fh) {
		System.out.println("processing: "+fh.getName());
		if(!fh.canRead()) {
			System.out.println("Error, can't read file");
			return false;
		}
        RandomAccessFile inFile = null;
		try {
	        inFile = new RandomAccessFile(fh, "r");
		} catch (Exception e) {}		
		if(inFile == null) {
			System.out.println("Error, can't open file");
			return false;
		}
        String line;
		int cnt = 0;
        nrc1 = rc1 = 0;
        nrc12 = rc12 = 0;
        nrc24 = rc24 = 0;
        nrc36 = rc36 = 0;
        nrc48 = rc48 = 0;
        nrc60 = rc60 = 0;
        nrc84 = rc84 = 0;
        wireCenter = CLLI = POP = CLLI_2 = "XX";
        miles = -1; //not initialized
        nrcState = popState = -1;
		boolean found = false;
        parsingWireCenter =parsingMiles =parsingPOP = parsingMonths = done = false;
        
		try {
			while ((line = inFile.readLine())!= null) {
				cnt++;
				if(line.equals("DS1-1.544 MBPS")) {
					if(cnt != 3) {
						System.out.println("Error 1: DS1-1.544 MBPS not correct");
						return false; //can't parse file yet
					} else {
						line = inFile.readLine(); //"INTER-STATE"
						line = inFile.readLine(); //"SPECIAL ACCESS"
						line = inFile.readLine(); //"POINT TO POINT"
						date = inFile.readLine(); //"DATE"
						line = inFile.readLine(); //"MO  TOTALS  %   NRCs"
						parsingMonths = true;
						continue;
					}
					
				} else if(cnt == 1) {
					RBOC = line;
					continue;
				} else if(cnt == 2) {
					t2 = line;
					continue;
				} else if(parsingMonths) {
					if(!parseMonths(line,cnt)) {
						System.out.println("ERROR parsing months");
						return false;
					}
				} 
				if(parsingWireCenter) {
                    if(!parseWireCenter(line,cnt)) {
						System.out.println("ERROR parsing Wire Center");
						return false;
                    }
                } else if(parsingMiles) {
                    if(!parseMiles(line,cnt)) {
						System.out.println("ERROR parsing Wire Center");
						return false;
                    }
                } else if(parsingPOP) {
                    if(!parsePOP(line,cnt)) {
						System.out.println("ERROR parsing POP");
						return false;
                    }
                }
                if(done)
                    return true;
			}
		} catch (Exception e) {
			System.out.println("Oops, Exception:" + e);
		}		
		try {
			inFile.close();
		} catch (Exception e) {}		
		return true;
	}
//**************************
// 	  parseMiles
//**************************	
boolean parseMiles(String line, int cnt) {
    if(line.endsWith(" MILES")) {
        StringTokenizer st = new StringTokenizer(line);
        String szMiles = st.nextToken();
		try {
			miles = Float.parseFloat(szMiles);
        } catch (Exception e) {
            System.out.println("ERROR parsing for miles string:"+line);
            return false;
        }
        parsingMiles = false;
        parsingPOP = true;
    }
    return true;
}
//**************************
// 	  parsePOP
//**************************	
boolean parsePOP(String line, int cnt) {
    if(popState == -1) {
        if(line.length() == 0) {
            popState = 0;
        }
    } else if(popState == 0) {
        POP = line;
        popState++;
    } else if(popState == 1) {
        CLLI_2 = line;
        parsingPOP = false;
        done = true;
    }
    return true;
}
//**************************
// 	  parseWireCenter
//**************************	
boolean parseWireCenter(String line, int cnt) {
    if(nrcState == -1) {
        String str = line.substring(0,5);
        if(str.equals("NRC (")) {
            nrcState = 0;
        }
    } else if(nrcState == 0) {
        wireCenter = line;
        nrcState++;
    } else if(nrcState == 1) {
        CLLI = line;
        parsingWireCenter = false;
        parsingMiles = true;
    }
    return true;
}
//**************************
// 	  parseMonths
//**************************	
boolean parseMonths(String line, int cnt) {
    StringTokenizer st = new StringTokenizer(line);
    if (st.hasMoreTokens()) {
        String months = st.nextToken();
			//process
        String totals = st.nextToken();
		if(months == null && totals == null) {
            System.out.println("Parse error on line:" + line);
            return false;
        }
        String pct = st.nextToken();
        String nrc = st.nextToken();
			//validate tokens
		try {
			int k = Integer.parseInt(months);
			//validate 1,36,60
			if(totals.charAt(0) == '$' && nrc.charAt(0) == '$') {
//    			System.out.print(months+"\t "+totals);
			   	//ok
				float val = Float.parseFloat(totals.substring(1));
				float val2 = Float.parseFloat(nrc.substring(1));
//				System.out.print(k+" "+val+" "+val2+", ");
                switch (k) {
                        case 1:  rc1  = val;  nrc1   = val2;    break;
                        case 12: rc12 = val;  nrc12  = val2;    break;
                        case 24: rc24 = val;  nrc24  = val2;    break;
                        case 36: rc36 = val;  nrc36  = val2;    break;
                        case 48: rc48 = val;  nrc48  = val2;    break;
                        case 60: rc60 = val;  nrc60  = val2;    break;
                        case 84: rc84 = val;  nrc84  = val2;    break;
                        default:
                            System.out.println("error parsing month values");
                            return false;
                }
			} else {
                System.out.println();
                parsingMonths = false;
                parsingWireCenter = true;
                if(rc1 > 0.0 || rc12 > 0.0 || rc24 > 0.0)
                    return true;
                else {
    				System.out.println("parse error on line:" + line);
                    return false;
                }
			}
		} catch (Exception e) {
			System.out.println("parse error on line:" + line);
			return false;
		}
	}	
	return true;
}
// ---------
	private void writeData() {
//		System.out.println("  writeData: "+amt1+" "+amt36+" "+amt60);
		String szQuery = "INSERT INTO foo VALUES ('"
        +carrier+"', '"
        +RBOC+"', '"
        +date+"', '"
        +wireCenter+"', '"
        +CLLI+"', '"
        +POP+"', '"
        +CLLI_2+"', '"
        +miles+"', '"
        +rc1  +"', '"
        +nrc1 +"', '"
        +rc12 +"', '"
        +nrc12+"', '"
        +nrc24+"', '"
        +rc24 +"', '"
        +rc36 +"', '"
        +nrc36+"', '"
        +rc48 +"', '"
        +nrc48+"', '"
        +rc60 +"', '"
        +nrc60+"', '"
        +rc84 +"', '"
        +nrc84
        +"') WHERE npanxx="+npanxx;
		System.out.println(szQuery);
//String amt1,amt36,amt60;
	}
// ---------
    public static void main(String[] args) {
        new cvt2sql(args);
    }


}	//end of class cvt2sql
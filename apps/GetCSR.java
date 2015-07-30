/*
 * GetCSR.java
 *
 * Created on February 28, 2003, 6:32 AM
 */

/**
 *
 * @author  Jim
 */
import java.io.*;
import java.util.*;
import java.awt.*;
import java.awt.event.*;

public class GetCSR {
Robot robot;
boolean trace = false;
boolean debug = false;
    /** Creates a new instance of GetCSR */
    public GetCSR(String[] args) {
        if(args.length == 0) {
            System.out.println("Error!  Usage: java GetCSR <file name>");
            System.exit(1);
        }
        String inFile = args[0];
        if(args.length == 2) {
System.out.println("("+args[1].substring(0,2)+")");            
            if(args[1].substring(0,2).equals("-d"))
                debug = true;
            else if(args[1].substring(0,2).equals("-t"))
                trace = true;
        }
        System.out.println("Using data file: " + inFile);
        File fh = new File(inFile);
        if(fh == null || !fh.exists() || fh.canRead() != true || !fh.isFile()) {
            System.out.println("Error!  File: (" + inFile + ") must exist.");
            System.exit(1);
        }
        //open file
 try {
    boolean done = false;
    FileReader fr = new FileReader(fh);
    BufferedReader in = new BufferedReader(fr);
    String line; 
    char[] newDiget = {'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K'}; //NOTE: SKIPS THE 'I'
    String BTN, code, custimerID, BTNdir;
    String companyID = in.readLine();
    companyID = "\\web\\csr_home\\"+companyID.trim();
    BTNdir = companyID + "\\BTN";
    File companyFH = new File(companyID);
    if(companyID == null || !companyFH.exists() || companyFH.isFile()) {
        System.out.println("Error!  Directory: (" + companyID + ") must exist.");
        System.exit(1);
    }
   File BTNfh = new File(BTNdir);
   if(!BTNfh.exists()) {
       if(!BTNfh.mkdir()) {
        System.out.println("Error!  Cannot create directory: (" + BTNdir + ").");
        System.exit(1);
       }
   }
   robot = new Robot();
   if(robot == null) {
        System.out.println("Error!  Cannot create robot.");
        System.exit(1);
   }
   System.out.println("processing BTN's into "+companyID);     
   System.out.println("You have 10 seconds until I start");
   boolean circut = false;
   pause(10);
    BTN = null;
    code = null;
    while(!done) {
        line = in.readLine();
        if(line != null) {
//            System.out.println(line);
//----            
        StringTokenizer st = new StringTokenizer(line);
        if (st.hasMoreTokens()) {
            BTN = st.nextToken();
            circut = false;
            System.out.print(BTN + " ");
            if (st.hasMoreTokens()) {
                code = st.nextToken();
                BTN = BTN.toUpperCase();
                if(BTN.length() == 11) {
                     char first = BTN.charAt(0);
                     if(first == '0' || first == 'O') { //zero or Oh
                        int i = 0;
                        try {
                            long l = Long.parseLong(BTN.substring(1)); //only here to test if BTN is a #
//       System.out.print("l="+l+" ");
                            String str = BTN.substring(4,5);
                            i = Integer.parseInt(str);
                            if(i < 1 || i >9) {
                             System.out.println("Error, invalid BTN (" + BTN + ")");
                             continue;
                            }
                            BTN = BTN.substring(1,4) + newDiget[i] + BTN.substring(5);
                            circut = true;
                            System.out.print("("+BTN+") ");
                        } catch (Exception e) {
                            System.out.println("Error.");
                            continue;
                        }
                    } else {
                        System.out.println("Error, invalid BTN (" + BTN + ")");
                        continue;
                    }
                } else if (BTN.length() != 10) {
                    System.out.println("Error, invalid BTN, length= "+BTN.length());
                    continue;
                }
                else {
                    try {
                    long l = Long.parseLong(BTN); //only here to test if BTN is a #
//         System.out.print("l="+l+" ");
                    } catch (Exception e) {
                        System.out.println("Error, invalid BTN");
                        continue;                
                    }
                }
                //by now, the BTN looks good
                System.out.print(code + " ");
                if(code.length() != 3) {
                    System.out.println("Error, invalid code");
                    continue;
                 }
                try { 
                    int i = Integer.parseInt(code); //only here to test code
                } catch (Exception e) {
                    System.out.println("Error, invalid code");
                    continue;
                }
                //by now, the code looks good
            } else {
                System.out.println("Error, Missing code");
                continue;
            }
        } else {
                
    
            //blank line
            System.out.println("Error, Blank line");
            continue;
        }
//---
        } else {
            System.exit(0); //eof
        }
     System.out.print(" Processing");
     /*****************************************/
     pause(1);
//Shift Tab tab
     Send(KeyEvent.VK_SHIFT,1);
     Send(KeyEvent.VK_TAB);
     Send(KeyEvent.VK_TAB);
     Send(KeyEvent.VK_SHIFT,0);
//BTN
     SendString(BTN);
//tab
     Send(KeyEvent.VK_TAB);
	 if(circut)
		Send(KeyEvent.VK_ENTER);    //enter

//Customer code
     SendString(code);
//tab, Enter
     Send(KeyEvent.VK_TAB);
     Send(KeyEvent.VK_ENTER);
//wait 20 seconds     
     pause(20);
//Alt F, A
     Send(KeyEvent.VK_ALT,1);
     Send(KeyEvent.VK_F);   //file
     Send(KeyEvent.VK_ALT,0);
     Send(KeyEvent.VK_A);   //Save As
//wait 5 seconds     
     pause(5);
//path name (\web\csr_home\<clintID>/BTN/<BTN>.txt
//     String fileName = BTNdir + "\\" + BTN + ".txt";
//     String fileName = BTN + ".txt";
     String fileName = BTN;
//hack = true; 
     boolean fileExists = false;
     File fileHandle = new File(fileName + ".htm");
     if(fileHandle != null && fileHandle.exists())
         fileExists = true;
     SendString(fileName);

     Send(KeyEvent.VK_ENTER);    //enter
     if(fileExists) {
		pause(8); 				//wait 8 seconds
        Send(KeyEvent.VK_Y);    //Y (replace file)
	}
     pause(10); //wait 10 seconds
// tab, enter     
     Send(KeyEvent.VK_TAB);
     Send(KeyEvent.VK_ENTER);
/*******************/     
     //if circut, answer warning box (may be right after BTN entry)
     //wait for records to download (scary)
     //save to disk
     //wait for write to finish
//     robot.delay(2000);     //wait 2 seconds to finish
     /*****************************************/
     System.out.println(" OK");
    }
     Send(KeyEvent.VK_SHIFT,1);
     Send(KeyEvent.VK_TAB);
     Send(KeyEvent.VK_TAB);
     Send(KeyEvent.VK_SHIFT,0);
//BTN
     SendString("Finished");
    
     } catch (Exception e) {
         System.out.println("I/O exception " + e + ". While processing file" + inFile);
         System.exit(1);
     }
    }
    
/**
 * @param args the command line arguments
 */
    public static void main(String[] args) {
        new GetCSR(args);
    }

/**
 * pause
 */
    private void pause(int seconds) {
    if(debug || trace)
        System.out.print(" [delay " + seconds + " seconds] ");
    if(!debug)
         robot.delay(seconds * 1000);
    }
/**
 * Send
 */
    private void  Send(int keycode, int down){
        if(debug || trace) {
            String str;
            if(keycode == KeyEvent.VK_SHIFT)
                str = "shift";
            else if(keycode == KeyEvent.VK_ALT)
                str = "alt";
            else 
                str = "{" + keycode + "}";
            str += (down == 1) ? " down" : " up";
                
            System.out.print("[" + str + "] ");
        } 
        if (!debug) {
            if(down == 1)
                robot.keyPress(keycode);
            else
                robot.keyRelease(keycode);
            robot.delay(250);
      }
    }
/**
 * Send
 */
    private void  Send(int keycode){
        if(debug || trace) {
            String str = "[" + keycode + "] ";
            if(keycode == KeyEvent.VK_TAB)
                str = "[tab] ";
            else if(keycode == KeyEvent.VK_ENTER)
                str = "[enter] ";
            else if(keycode >= KeyEvent.VK_A && keycode <= KeyEvent.VK_Z) {
                char ch = (char)('A' + (char)(keycode - KeyEvent.VK_A));
                str = "[" + ch + "] ";
            }
            System.out.print(str);
        } 
        if (!debug) {
            robot.keyPress(keycode);
            robot.keyRelease(keycode);
            robot.delay(250);
        }
    }
/**
 * SendString
 */
    private void SendString(String str) {
        if(debug || trace) {
            System.out.print("("+str+") ");        
        } 
        if(!debug) {
            int len = str.length();
            for(int i = 0; i < len; ++i) {
                char key = str.charAt(i);
                int keycode = 0;
                if(key >= '0' && key <= '9')
                    keycode = KeyEvent.VK_0 + key - '0';
                else if(key >= 'A' && key <= 'Z')
                    keycode = KeyEvent.VK_A + key - 'A';
                else if(key >= 'a' && key <= 'z')
                    keycode = KeyEvent.VK_A + key - 'a';
                else if(key == '\\')
                    keycode = '/'; //KeyEvent.VK_SLASH OR VK_BACK_SLASH;
                else if(key == '.')
                    keycode = '.'; //KeyEvent.VK_PERIOD;
                else if(key == ':')
                    keycode = KeyEvent.VK_COLON;
				else {
					System.out.println("Error: Unknown character(" + key + ")");
				}	
                if(keycode != 0) {
					if(trace) {
						System.out.print(keycode + " ");    
					}
    	            robot.keyPress(keycode);
	                robot.keyRelease(keycode);
                }
            }
        robot.delay(1000);            
        }
    }
}

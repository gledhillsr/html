/*
 * DoRobot.java
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
//import javax.swing.*;

public class DoRobot {
Robot robot;
boolean debug = false;
final static boolean NEW_DIALOG = true;
final static boolean EXISTING_DIALOG = false;
static int CENTER_X = 325;
static int CENTER_Y = 270;
Vector dialogPos = new Vector(10);

    /** Creates a new instance of DoRobot */
    public DoRobot(String[] args) {

        if(args.length != 1 && args.length != 2) {
            System.out.println("Error!  Usage: java DoRobot <carrier> <NPANXX file name> [-debug]");
            System.exit(1);
        }
		

		DBAccess dbaccess = new DBAccess(null,"1800save");
//		System.out.println("dbaccess="+dbaccess);	
		dbaccess.resetNpanxx();

        String carrier = args[0];
        if(args.length == 2) {
//System.out.println("("+args[1].substring(0,2)+")");            
            if(args[1].substring(0,2).equals("-d")) {
                debug = true;
			}
        }
//System.out.println("debug="+debug);		
	  String npanxx = null; // = in.readLine();
 try {
    boolean done = false;
   robot = new Robot();
//robot.keyPress(0x20a1); //0x3a
//robot.keyRelease(0x20a1);

   if(robot == null) {
        System.out.println("Error!  Cannot create robot.");
        System.exit(1);
   }
   System.out.println("processing NPANXX's");     
   System.out.println("You have 5 seconds until I start");
   boolean circut = false;
   pause(5);
   int idx = prescanForErrors("201007",3);
		 Send(KeyEvent.VK_ENTER,250);   //enter
		 Send(KeyEvent.VK_ENTER,250);   //enter
		 Send(KeyEvent.VK_TAB,250);     //TAB
		 Send(KeyEvent.VK_ENTER,1000);   //enter
		 
   idx = prescanForErrors("212849",3);
		 Send(KeyEvent.VK_ENTER,1000);   //enter

   idx = prescanForErrors("270728",3);
		 Send(KeyEvent.VK_ENTER,1000);   //enter

   idx = prescanForErrors("618787",3);
		 Send(KeyEvent.VK_ENTER,500);   //enter
		 Send(KeyEvent.VK_ENTER,500);   //enter

   idx = prescanForErrors("318354",3);		//NO POP DATA FOUND
		 Send(KeyEvent.VK_ENTER,500);   //enter
		 Send(KeyEvent.VK_ENTER,500);   //enter

   idx = prescanForErrors("320543",3);	//NO MEET-POINT FOR CLLIs
		 Send(KeyEvent.VK_ENTER,500);   //enter
		 Send(KeyEvent.VK_ENTER,500);   //enter

//   idx = prescanForErrors("217631",3);
//		 Send(KeyEvent.VK_ENTER,500);   //enter

System.out.println(" ");		 
System.out.println(" ");		 
	int count = 0;
	int increment = 50; //for testing
	if(increment != 1)
	System.out.println("testing.  Only processing every "+increment+" records.");
    while(!done) {

		Npanxx np = dbaccess.nextNpanxx();
		if(np == null)
			break;
		npanxx = np.getNPANXX();
		if(count++ % 50 != 0) //hack
			continue;	  //hack
//		if(count++ > 10) //hack
//			break;		 //hack
        if(npanxx != null){
            System.out.print("processing: "+npanxx);
			if(debug) System.out.println(" ");
		} else
			break;
     /*****************************************/
	 robot.delay(1000);  		//wait for previous dialog to go away
	 MousePress(94,374,EXISTING_DIALOG,"Press Calculate DS1");
	 robot.delay(100);  		//wait for previous dialog to go away
     SendString(npanxx);		//type in npanxx
	 Send(KeyEvent.VK_ENTER,1000);   //enter
/*	
	if(checkForError(260,130,100,"????0000")){ //point on title bar of "PHNZAZNN has PNAnxxs in more than one LATA.  Please select one!"
System.out.println(" Error: PHNZAZNN has PNAnxxs in more than one LATA. -SKIPPED-");
		MousePress(402,377,EXISTING_DIALOG,"Press Cancel");
		 continue;
	}
*/
	if(doesDialogExist()) {
//System.out.println("********************dialog found at cursor******** wait 10 seconds");
//robot.delay(10000);  		//wait for previous dialog to go away
		int top = scanForColor(CENTER_X,CENTER_X,40,CENTER_Y,0,3);
		int left = scanForColor(40,CENTER_X,CENTER_Y,CENTER_Y,3,0);
		Dimension dim = new Dimension(left,top);
		idx = dialogPos.indexOf(dim);
System.out.print(" pos=("+dim.width+", "+dim.height+")");
		switch(idx) {
			case 0:
System.out.println(" Error: NXX "+npanxx+" not found, re-enter");
				 Send(KeyEvent.VK_ENTER,250);   //enter
				 Send(KeyEvent.VK_ENTER,250);   //enter
				 Send(KeyEvent.VK_TAB,250);     //TAB
				 Send(KeyEvent.VK_ENTER,500);   //enter
			break;
			case 1:
System.out.println(" Error: More that 1 LATA");
				 Send(KeyEvent.VK_ENTER,600);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
			break;
			case 2:
System.out.println(" Error, Use NECA rates for independent");
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
			break;
			case 3:
System.out.println(" Error, No Meet-Point For CLLIs");
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
			break;
			case 4:
System.out.println(" Error, No POP DATA FOUND");
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
			break;
			case 5:
System.out.println(" Error, NO Meet Point for CLLIs");
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
			break;
			default:
//				System.out.println(" Fatel ERROR: dialog not handled");
//				System.exit(1);
System.out.println(" Error, ****** UNKNOWN ERROR processing: "+npanxx);
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,400);   //enter
				 Send(KeyEvent.VK_ENTER,600);   //enter
			
		}
		continue;
/****
	if(checkForError(205,222,100,"'NXX "+npanxx+" not found, re-enter'")){
		 Send(KeyEvent.VK_ENTER,250);   //enter
		 Send(KeyEvent.VK_ENTER,250);   //enter
		 Send(KeyEvent.VK_TAB,250);     //TAB
		 Send(KeyEvent.VK_ENTER,250);   //enter
		 System.out.println(" Error: NXX not found");
		 continue;
	} else if(checkForError(200,190,250,"Enter POP NPAnxx...")){ //point on title bar of 
		MousePress(335,315,EXISTING_DIALOG,"Press Cancel");	//move to cancel button (can't be done via keyboard)	
		continue;
	} else if(checkForError(260,222,100,"????1111")){ //point on title bar of 
//this next part is weird, I cant tell which one is which, but one has a cancel key
//so I test for that first by pressing (with a click) the cancel key.  This works only
//because the 2nd dialog box has nothing where the cancel key was	
//press cancel if it's there (if it's not, no big deal)
		MousePress(344,315,NEW_DIALOG,"Press Cancel");	//move to cancel button (can't be done via keyboard)
		if(!checkForError(260,222,100,"????2222")){ //is dialog still up?
//not there, so the cancel key must have worked		
System.out.println(" Error: Use NECA rates for Independent. -SKIPPED-");
		} else {
System.out.println(" Error: Input unknown, re-enter data. -SKIPPED-");
			 Send(KeyEvent.VK_ENTER,250);   //enter
//press OK button (extra click doesn't hurt)
			 MousePress(344,315,NEW_DIALOG,"Press Cancel");	//move to cancel button (can't be done via keyboard)
		}
		continue;
	}
****/	
	} //end does dialog exist
	 MousePress(549,374,EXISTING_DIALOG,"Press Print");	//move to cancel button (can't be done via keyboard)
	 MousePress(333,283,NEW_DIALOG,"Press ASCII");	//move to ASCII button
	 Send(KeyEvent.VK_ENTER,500);    //default is NO comments
	 
	 String newFileName = "npanxx."+carrier+"."+npanxx + ".txt";
     SendString(newFileName);
	 Send(KeyEvent.VK_ENTER,250);    //enter
     Send(KeyEvent.VK_TAB,250);		//tab
	 Send(KeyEvent.VK_SPACE,500);   //space
	 
     /*****************************************/
	 //confirm that file exists
     newFileName = "j:\\" + newFileName;
     File newFH = new File(newFileName);
	 if(newFH.exists())
	     System.out.println(" OK");
	else {
		System.out.println(" ERROR: file "+newFileName+" does not exist, aborting...");
		break;
	} 
    }
//     SendString("Finished");
    
     } catch (Exception e) {
         System.out.println("Exception " + e + ". While processing: " + npanxx);
         System.exit(1);
     }
    }
    
/**
 * @param args the command line arguments
 */
    public static void main(String[] args) {
        new DoRobot(args);
    }


/***************************
 * prescanForErrors
 ***************************/
private int prescanForErrors(String npanxx, int step) {
	 robot.delay(500);  		//wait for previous dialog to go away
	 MousePress(94,374,EXISTING_DIALOG,"Press Calculate DS1");
	 robot.delay(100);  		//wait for previous dialog to go away
     SendString(npanxx);		//type in npanxx
	 Send(KeyEvent.VK_ENTER,500);   //enter
	 int top = scanForColor(CENTER_X,CENTER_X,40,CENTER_Y,0,step);
	 if(top == 0) {
	 	System.out.println("Prescan error, "+npanxx+" did NOT generate an error");
		System.exit(0);
	 }
	 int left = scanForColor(40,CENTER_X,CENTER_Y,CENTER_Y,step,0);
	 if(left == 0) {
	 	System.out.println("Prescan error, "+npanxx+" did NOT generate an error");
		System.exit(0);
	 }
	 dialogPos.add(new Dimension(left,top));
System.out.println("pre-processing "+npanxx+", adding element at "+left+", "+top+" at position: "+(dialogPos.size()-1));		
	 return dialogPos.size()-1;
}

/***************************
 * MousePress
 ***************************/
	private void MousePress(int x,int y,boolean isNewDialog,String str) {
		int postDelay = isNewDialog ? 500 : 100;
		if(debug)
			System.out.println("pressing mosue button at ("+x+", "+y+", "+str+") post delay = "+postDelay);
		
	    robot.mouseMove(x,y);
		if(debug)		 
			robot.delay(1000); //wait for dialog to disappear
			
	    robot.mousePress(InputEvent.BUTTON1_MASK);
	    robot.mouseRelease(InputEvent.BUTTON1_MASK);
	    robot.delay(postDelay); //wait for dialog to appear
	}
/************************
 * pause
 ************************/
private void pause(int seconds) {
    if(debug)
        System.out.print(" [delay " + seconds + " seconds] ");
	for(int i=0; i < seconds; ++i) {
	 	System.out.print(i+" ");
     	robot.delay(1000);
	 	Toolkit.getDefaultToolkit().beep();
	}
	System.out.println();
}

/***************************
 * doesDialogExist
 ***************************/
private boolean doesDialogExist() {
	return (scanForColor(CENTER_X,CENTER_X,40,CENTER_Y,0,10) > 0);
}

/***************************
 * scanYForColor
 ***************************/
private int scanForColor(int x1, int x2, int y1, int y2, int stepx, int stepy) {
	Color clr = robot.getPixelColor(CENTER_X,15);
	int r1 = clr.getRed();
	int g1 = clr.getGreen();
	int b1 = clr.getBlue();
	int i, r2, g2, b2, diff = -1;
	Color clr2 = null;
	if(stepy > 0) {
		for(i=y1; i < y2; i+= stepy){
			clr2 = robot.getPixelColor(x1,i);
robot.mouseMove(x1,i);
robot.delay(1);
			r2 = clr2.getRed();
			g2 = clr2.getGreen();
			b2 = clr2.getBlue();
			diff = (Math.abs(r1-r2)/4 + Math.abs(g1-g2)/2 + Math.abs(b1-b2));
			if(diff < 90) {
//System.out.println("dr="+Math.abs(r1-r2)+" dg="+Math.abs(g1-g2)+" db="+Math.abs(b1-b2));			
//	System.out.print(" color at y= "+i+" diff="+diff);
//round to the nearest 6
i = ((i+3) / 6) * 6;
				return i;
			}
		}
	} else {
		for(i=x1; i < x2; i+= stepx){
			clr2 = robot.getPixelColor(i,y1);
robot.mouseMove(i,y1);
robot.delay(1);
			r2 = clr2.getRed();
			g2 = clr2.getGreen();
			b2 = clr2.getBlue();
			diff = (Math.abs(r1-r2)/4 + Math.abs(g1-g2)/2 + Math.abs(b1-b2));
			if(diff < 90) {
//System.out.println("dr="+Math.abs(r1-r2)+" dg="+Math.abs(g1-g2)+" db="+Math.abs(b1-b2));			
//	System.out.print(" color at x= "+i+" diff="+diff);
//round to the nearest 6
i = ((i+3) / 6) * 6;
				return i;
			}
		}
	}
	return 0;
}
	
/***************************
 * checkForError
 ***************************/
	private boolean checkForError(int x, int y, int len, String str) {
//		Color clr = robot.getPixelColor(x,y);
robot.mouseMove(200,15);
//robot.delay(500);
		Color clr = robot.getPixelColor(200,15);
		
if(debug) {		
	System.out.print("Looking for "+str);
}
	int r1 = clr.getRed();
	int g1 = clr.getGreen();
	int b1 = clr.getBlue();
	    robot.mouseMove(x,y);
		int r2;
		int g2;
		int b2;
		int diff = -1;
		Color clr2 = null;
		int i;
		for(i=1; i < len; ++i){
			clr2 = robot.getPixelColor(x+i,y);
		    robot.mouseMove(x+i,y);
 	        robot.delay(1);
			r2 = clr2.getRed();
			g2 = clr2.getGreen();
			b2 = clr2.getBlue();
			diff = (Math.abs(r1-r2) + Math.abs(g1-g2) + Math.abs(b1-b2));
			if(diff > 250) {
if(debug)			
	System.out.println(" NOT found.  DIFF = "+diff);
				return false;
			}
		}
		if(debug) {		
			System.out.println(" (Found)");
	System.out.println(" NOT found.  DIFF = "+diff);
			//be verbose by duplicating the testing area
			for(int j=1; j < 10; ++j) {
				for(i=1; i < len; ++i){
				    robot.mouseMove(x+i,y);
				        robot.delay(1);
				}
			}
			Toolkit.getDefaultToolkit().beep();
			robot.delay(2000);
		}
		return true;
	}
/**
 * Send
 */
    private void  Send(int keycode, int down, int delay){
        if(debug) {
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
		
        if(down == 1)
            robot.keyPress(keycode);
        else
            robot.keyRelease(keycode);
        robot.delay(delay);
    } //end Send(int, int)
/**
 * Send
 */
    private void  Send(int keycode, int delay){
        if(debug) {
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
		
        robot.keyPress(keycode);
        robot.keyRelease(keycode);
        robot.delay(delay);
    } //end Send
/**
 * SendString
 */
    private void SendString(String str) {
        if(debug) {
            System.out.println("type string("+str+") ");        
        } 
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
                keycode =  '/'; //KeyEvent.VK_SLASH OR VK_BACK_SLASH;
            else if(key == '.')
                keycode = '.'; //KeyEvent.VK_PERIOD;
            else if(key == '_')
                keycode = KeyEvent.VK_UNDERSCORE;
            else if(key == ':')
                keycode = KeyEvent.VK_COLON;
			else {
				System.out.println("Error: Unknown character(" + key + ")");
			}	
            if(keycode != 0) {
				if(debug) {
					System.out.print(keycode + " ");    
				}
	            robot.keyPress(keycode);
                robot.keyRelease(keycode);
            }
//        robot.delay(1000);            
        }
    }  //end SendString
} //end class DoRobot

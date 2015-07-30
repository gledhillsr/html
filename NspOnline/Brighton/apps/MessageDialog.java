import java.awt.*;
import java.awt.event.*;
import java.util.*;
import java.lang.reflect.*;
import javax.swing.*;
import javax.swing.event.*;

public class MessageDialog extends JDialog implements ActionListener , KeyListener {

    javax.swing.JFrame frame;

    MessageDialog(javax.swing.JFrame f, String msg, String btn1) {
        super(f, "Error", true);
	    showMessageDialog(f, msg, btn1, null, null);
    }
    MessageDialog(javax.swing.JFrame f, String msg, String btn1, String btn2) {
        super(f, "Error", true);
	    showMessageDialog(f, msg, btn1, btn2, null);
    }
    MessageDialog(javax.swing.JFrame f, String msg, String btn1, String btn2, String btn3)
    {
        super(f, "Error", true);
	    showMessageDialog(f, msg, btn1, btn2, btn3);
    }

    private void showMessageDialog(JFrame f, String msg, String btn1, String btn2, String btn3)
    {
        frame = f;
//zzz        frame.lastBtnSelected = null;

        // Create the components.
//        Box box = Box.createHorizontalBox(); //buttons can be left justified
        JPanel box = new JPanel();
        JButton b1 = new JButton(btn1);
        // Listen for events.
        b1.addKeyListener(this);

        box.add(b1);
        if (btn2 != null) {
            JButton b2 = new JButton(btn2);
            box.add(b2);
            b2.addActionListener(this);
	        b2.addKeyListener(this);
        }
        if (btn3 != null) {
            JButton b3 = new JButton(btn3);
            box.add(b3);
            b3.addActionListener(this);
	        b3.addKeyListener(this);
        }
        JLabel l = new JLabel(msg, JLabel.CENTER) {
            // This adds some space around the text.
            public Dimension getPreferredSize() {
                Dimension d = super.getPreferredSize();
                return new Dimension(d.width+40, d.height+40);
            }
        };

        // Listen for events.
        b1.addActionListener(this);
        addWindowListener(new WindowEventHandler());

        // Layout components.
        getContentPane().add(l, BorderLayout.NORTH);
//        add(b, BorderLayout.SOUTH);
        getContentPane().add(box, BorderLayout.SOUTH);

        pack();
        Dimension myDim = getSize();
        Dimension frameDim = f.getSize();
        Dimension screenSize = getToolkit().getScreenSize();
        Point loc = f.getLocation();

        // Center the dialog w.r.t. the frame.
        loc.translate((frameDim.width-myDim.width)/2,
            (frameDim.height-myDim.height)/2);

        // Ensure that slave is withing screen bounds.
        loc.x = Math.max(0, Math.min(loc.x, screenSize.width-getSize().width));
        loc.y = Math.max(0,
            Math.min(loc.y, screenSize.width-getSize().height));

        setLocation(loc.x, loc.y);
//        getContentPane().setLocation(loc.x, loc.y);
        show();
    }

	public void keyTyped(KeyEvent e) {
        String str = ((JButton)e.getSource()).getLabel();
        char key = e.getKeyChar();
        if(str.charAt(0) == Character.toUpperCase(key) || //check for 1st letter of button
        	key == '\r') { //check for enter key  
//zzz	        frame.lastBtnSelected = str;
    	    dispose();
        }
    }
	public void keyPressed(KeyEvent e) {}
	public void keyReleased(KeyEvent e) {}

   public void actionPerformed(ActionEvent evt) {
//zzz        frame.lastBtnSelected = evt.getActionCommand();
        dispose();
    }

    class WindowEventHandler extends WindowAdapter
    {
        public void windowClosing(WindowEvent evt)
        {
            dispose();
        }
    }
}

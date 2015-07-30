//package com.jarlink.mailman;

import javax.mail.*;
import javax.mail.internet.*;
import javax.activation.*;
import java.util.*;

/**
 * Title:        MailMan
 * Description:  Mail Manager
 * Copyright:    Copyright (c) 2001
 * Company:      JarLink
 * @author Jared Allen
 * @version 1.0
 */

public class MailMan {
    private InternetAddress hostAddress;   	//ie mail.phoenixdsl.com
    private InternetAddress fromAddress;	//ie Steve@gledhills.com


	private static String _smtpHost = "mail.carriersales.com";
	private static String _pop3Host = "mail.carriersales.com";
	private static String _user 	= "brian.carriersales.com";
	private static String _password = "xxxx";
	private static String _fromName = "autoUpdate@ordertracker.info";

//	private static String _smtpHost = "mail.gledhills.com";
//	private static String _pop3Host = "mail.gledhills.com";
//	private static String _user 	= "steve%gledhills.com";
//	private static String _password = "XXXXXXX";
//	private static String _fromName = "steve@Gledhills.com";

	private static String POP_MAIL 	= "pop3";
	private static String INBOX 	= "INBOX";
	private static String SMTP_MAIL = "smtp";

	private Store store;
	private Folder folder;
	private Session session;

    /**
     * MailMan constructor.
     *
     * @param host  The smtp host address.
     * @param from  The return address.
     */
    public MailMan(String host, String from) {
        try {
            hostAddress = new InternetAddress(host);
            fromAddress = new InternetAddress(from);
			_smtpHost = host;
			_fromName = from;
			try {
			Properties sysProperties = System.getProperties();
//System.out.println(sysProperties);
			session = Session.getDefaultInstance(sysProperties, null);
//System.out.println(session);
			session.setDebug(false);
			store = session.getStore(POP_MAIL);
//System.out.println(store);
			store.connect(_pop3Host, -1, _user, _password);
			Folder folder =  store.getDefaultFolder();
//System.out.println(folder);
			folder = folder.getFolder(INBOX);
//System.out.println(folder);
			folder.open(Folder.READ_WRITE);
			int totalMessages = folder.getMessageCount();
System.out.println("totalMessages="+totalMessages);
			} catch (Exception e) {
				System.out.println("create mail exception e="+e);
			}

        } catch(AddressException ex) {
            throw new IllegalArgumentException();
        }
    }

	public void close() {
		try {
			if(folder != null)
				folder.close(true);
			if(store != null)
				store.close();
		} catch (Exception e) {
			System.out.println("create mail exception e="+e);
		}

	}
    /**
     * Sets the smtp host address.
     *
     * @param host  The smtp host address.
     */
    public void setHostAddress(String host) {
        try {
            hostAddress = new InternetAddress(host);
        } catch(AddressException ex) {
            throw new IllegalArgumentException();
        }
    }

    /**
     * Gets the smtp host address.
     *
     * @return  The smtp host address.
     */
    public String getHostAddress() {
        return hostAddress.getAddress();
    }

    /**
     * Sets the return address.
     *
     * @param from  The return address.
     */
    public void setFromAddress(String from) {
        try {
            fromAddress = new InternetAddress(from);
        } catch(AddressException ex) {
            throw new IllegalArgumentException();
        }
    }

    /**
     * Gets the return address.
     *
     * @return  The return address.
     */
    public String getFromAddress() {
        return fromAddress.getAddress();
    }

    /**
     * Sends a message with the given subject and message body to the given
     * of recipient.  The message is sent via the pre-set smtp host with the
     * pre-set return address.
     *
     * @param subject       Subject of the message.
     * @param message       The message body.
     * @param recipients    The recipient.
     */
    public void sendMessage(String subject, String message, String recipient) throws MailManException {
        String[] recipients = new String[1];
        recipients[0] = recipient;
        sendMessage(subject, message, recipients);
    }

    /**
     * Sends a message with the given subject and message body to the given list
     * of recipients.  The message is sent via the pre-set smtp host with the
     * pre-set return address.
     *
     * @param subject       Subject of the message.
     * @param message       The message body.
     * @param recipients    An array of strings representing all of the
     *                      recipients.
     */
    public void sendMessage(String subject, String message, String[] recipients) throws MailManException {
        Properties props = new Properties();
        Session session;
        MimeMessage msg;
        InternetAddress[] rcptAddresses = new InternetAddress[recipients.length];
        MimeBodyPart body;
        Multipart mp;

        props.put("mail.smtp.host", hostAddress.getAddress());
        session = Session.getDefaultInstance(props, null);
        msg = new MimeMessage(session);
        try {
            msg.setFrom(fromAddress);
            for(int i = 0; i < rcptAddresses.length; i++) {
                try {
                    rcptAddresses[i] = new InternetAddress(recipients[i]);
                } catch(AddressException ex) {
                    throw new MailManException("Invalid recipient address: " + recipients[i]);
                }
            }
            msg.setRecipients(Message.RecipientType.TO, rcptAddresses);
            msg.setSubject(subject);
            msg.setSentDate(new Date());
            // create and fill the first message part
            body = new MimeBodyPart();
            body.setText(message);
            // create the Multipart and add its parts to it
            mp = new MimeMultipart();
            mp.addBodyPart(body);
            // add the Multipart to the message
            msg.setContent(mp);
            // send the message
//            Transport.send(msg);
			Transport transport = session.getTransport(SMTP_MAIL);
//System.out.println("transport="+transport);
			transport.connect(_smtpHost, _user, _password);
//System.out.println("sending");
			transport.sendMessage(msg,rcptAddresses);
//System.out.println("sent");

        } catch(MessagingException ex) {
            throw new MailManException(ex.toString());
        }
    }

    public static void main(String[] args) throws MailManException {
        if(args.length < 5) {
            System.out.println("Invalid arguments");
            System.out.println("java com.jarlink.mailmain.MailMan <host> <from address> <subject> <message> <recipient address>");
            return;
        }
        MailMan mailMan = new MailMan(args[0], args[1]);
        mailMan.sendMessage(args[2], args[3], args[4]);
    }
}
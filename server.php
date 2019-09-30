<?php
/*PHP WEB SERVICE for PDF FORMS
Written by J A Suthers 21/09/19
Version="1.0" 
https://github.com/jasonsuthers/PDF-Submit-to-web-service
 */
/* Set date for future use !! */
date_default_timezone_set("Europe/London");
/* PUT data comes in on the stdin stream from iphone ect. */
$putdata = fopen("php://input", "r");
/* Open a temp file for writing */
$uFile = tempnam("./tmp/", "Time Sheet - ");
/* Used to clear headers later in the script */
ob_start();
echo $uFile;
$output = ob_get_contents();
$handle = fopen($uFile, "w");
/* Read the data 1 KB at a time
   and write to the file */
while ($data = fread($putdata, 1024))
  fwrite($handle, $data);
/* Needed to add the .pdf extension to the file */
 rename($uFile, $uFile .= '.pdf');
 /* Little break */
 sleep(2);
/* Not needed but for future additions */ 
chmod($uFile, 0644);
/* setup some options for email attachments and information */
$path = ($uFile);
$file1 = basename($path, ".pdf"); 
$file2 = basename($path); 
$file3 = date("h:i:sa");
$email_to = "youremailadd.com"; // The email you are sending to
$email_from = "someemail@.com"; // The email you are sending from
$email_subject = "Timesheet Successfully Submitted : ".$file2."".$file3; // The Subject of the email
$email_txt = "Your Time Sheet has been successfully submitted. Your referance code is : ".$file1; // Message that the email has in it
$fileatt = ($uFile); // Path to the file (example)
$fileatt_type = "application/pdf"; // File Type
$fileatt_name = "".$file2; // Filename that will be used for the file as the attachment
$file = fopen($fileatt,'rb');
$data = fread($file,filesize($fileatt));
fclose($file);
$semi_rand = md5(time());
$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
$headers="From: $email_from"; // Who the email is from (example)
$headers .= "\nMIME-Version: 1.0\n" .
"Content-Type: multipart/mixed;\n" .
" boundary=\"{$mime_boundary}\"";
$email_message .= "This is a multi-part message in MIME format.\n\n" .
"--{$mime_boundary}\n" .
"Content-Type:text/html; charset=\"iso-8859-1\"\n" .
"Content-Transfer-Encoding: 7bit\n\n" . $email_txt;
$email_message .= "\n\n";
$data = chunk_split(base64_encode($data));
$email_message .= "--{$mime_boundary}\n" .
"Content-Type: {$fileatt_type};\n" .
" name=\"{$fileatt_name}\"\n" .
"Content-Transfer-Encoding: base64\n\n" .
$data . "\n\n" .
"--{$mime_boundary}--\n";
mail($email_to,$email_subject,$email_message,$headers);

//Send Success Response to PDF Reader as FDF Type if sending other than ios or android theres a fail message, need to investigate this
ob_end_clean();
header('Content-type: application/vnd.fdf');
echo ('success.fdf');
/* Close the streams */
fclose($handle);
fclose($putdata);
?>




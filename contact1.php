<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
require_once("AntiSpam.php");

$q = AntiSpam::getRandomQuestion();
header('Content-type: text/html; charset=utf-8');


#########################################################################
#	Pluscom Services LIMITED        					                                #
#	http://www.pluscom.co.ke        						                    #
#	All rights by pluscom.co.ke                                   			#
#-----------------------------------------------------------------------#
#	I-Net: http://www.pluscom.co.ke                           					#
#########################################################################
// It´s not allowed to remove the copyright notice!


  $script_root = substr(__FILE__, 0,
                        strrpos(__FILE__,
                                DIRECTORY_SEPARATOR)
                       ).DIRECTORY_SEPARATOR;

require_once $script_root.'upload.php';

$remote = getenv("REMOTE_ADDR");



@require('config.php');

if ($_POST['delete'])
{
unset($_POST);
}

// take over the data from the formular
if ($_POST["mt-mk"]) {

// variables of the data fields
   $name      = $_POST["name"];
   $email      = $_POST["email"];
   $phonenumber = $_POST["phonenumber"];
   $place   = $_POST["place"];
   $subject   = $_POST["subject"];
   $message   = $_POST["message"];
   
   $date = date("d.m.Y | H:i");
   $ip = $_SERVER['REMOTE_ADDR']; 
   $UserAgent = $_SERVER["HTTP_USER_AGENT"];
   $host = getHostByAddr($remote);


// examination of the data fields
$name = stripslashes($name);
$email = stripslashes($email);
$subject = stripslashes($subject);
$message = stripslashes($message);
 
//check antiSpam
if(isset($_POST["q_id"])){
	$answer = AntiSpam::getAnswerById(intval($_POST["q_id"]));
	if($_POST["q"] != $answer){
		$fehler['q_id12'] = "<span class='errormsg'>The answer for the <strong>security question</strong> is not correct.</span>";
	}
}
if(!$name) {
 
 $fehler['name'] = "<span class='errormsg'>Please enter your <strong>name</strong>.</span>";
 
}


if (!preg_match("/^[0-9a-zA-ZÄÜÖ_.-]+@[0-9a-z.-]+\.[a-z]{2,6}$/", $email)) {
   $fehler['email'] = "<span class='errormsg'>Please enter a <strong>e-mail-address</strong>.</span>";
}

 
if(!$subject) {
 
 $fehler['subject'] = "<span class='errormsg'>Please enter a <strong>subject</strong>.</span>";
 
 
}
 
if(!$message) {
 
 $fehler['message'] = "<span class='errormsg'>Please enter a <strong>message</strong>.</span>";
 
 
}



    if (!isset($fehler) || count($fehler) == 0) {
      $error             = false;
      $errorMessage      = '';
      $uploadErrors      = array();
      $uploadedFiles     = array();
      $totalUploadSize   = 0;

      if ($cfg['UPLOAD_ACTIVE'] && in_array($_SERVER['REMOTE_ADDR'], $cfg['BLACKLIST_IP']) === true) {
          $error = true;
          $fehler['upload'] = "<span class='errormsg'>You have no authorization to upload files.</span>";
      }

      if (!$error) {
          for ($i=0; $i < $cfg['NUM_ATTACHMENT_FIELDS']; $i++) {
              if ($_FILES['f']['error'][$i] == UPLOAD_ERR_NO_FILE) {
                  continue;
              }

              $extension = explode('.', $_FILES['f']['name'][$i]);
              $extension = strtolower($extension[count($extension)-1]);
              $totalUploadSize += $_FILES['f']['size'][$i];

              if ($_FILES['f']['error'][$i] != UPLOAD_ERR_OK) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  switch ($_FILES['f']['error'][$i]) {
                      case UPLOAD_ERR_INI_SIZE :
                          $uploadErrors[$j]['error'] = 'the file is too big (PHP-Ini directive).';
                      break;
                      case UPLOAD_ERR_FORM_SIZE :
                          $uploadErrors[$j]['error'] = 'the file is too big (MAX_FILE_SIZE in HTML-Formular).';
                      break;
                      case UPLOAD_ERR_PARTIAL :
						  if ($cfg['UPLOAD_ACTIVE']) {
                          	  $uploadErrors[$j]['error'] = 'the file has been uploaded partially.';
						  } else {
							  $uploadErrors[$j]['error'] = 'the file has been sent partially.';
					  	  }
                      break;
                      case UPLOAD_ERR_NO_TMP_DIR :
                          $uploadErrors[$j]['error'] = 'No temporarily folder has been found.';
                      break;
                      case UPLOAD_ERR_CANT_WRITE :
                          $uploadErrors[$j]['error'] = 'error during saving the file.';
                      break;
                      case UPLOAD_ERR_EXTENSION  :
                          $uploadErrors[$j]['error'] = 'unknown error due to an extension.';
                      break;
                      default :
						  if ($cfg['UPLOAD_ACTIVE']) {
                          	  $uploadErrors[$j]['error'] = 'unknown error on uploading.';
						  } else {
							  $uploadErrors[$j]['error'] = 'unknown error on sending the email attachments.';
						  }
                  }

                  $j++;
                  $error = true;
              }
              else if ($totalUploadSize > $cfg['MAX_ATTACHMENT_SIZE']*1024) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'maximum upload reached ('.$cfg['MAX_ATTACHMENT_SIZE'].' KB).';
                  $j++;
                  $error = true;
              }
              else if ($_FILES['f']['size'][$i] > $cfg['MAX_FILE_SIZE']*1024) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'The file is too big (max. '.$cfg['MAX_FILE_SIZE'].' KB).';
                  $j++;
                  $error = true;
              }
              else if (!empty($cfg['BLACKLIST_EXT']) && strpos($cfg['BLACKLIST_EXT'], $extension) !== false) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'the file extension is not permitted.';
                  $j++;
                  $error = true;
              }
              else if (preg_match("=^[\\:*?<>|/]+$=", $_FILES['f']['name'][$i])) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'invalid symbols in the file name (\/:*?<>|).';
                  $j++;
                  $error = true;
              }
              else if ($cfg['UPLOAD_ACTIVE'] && file_exists($cfg['UPLOAD_FOLDER'].'/'.$_FILES['f']['name'][$i])) {
                  $uploadErrors[$j]['name'] = $_FILES['f']['name'][$i];
                  $uploadErrors[$j]['error'] = 'the file already exist.';
                  $j++;
                  $error = true;
              }
              else {
				  if ($cfg['UPLOAD_ACTIVE']) {
                     move_uploaded_file($_FILES['f']['tmp_name'][$i], $cfg['UPLOAD_FOLDER'].'/'.$_FILES['f']['name'][$i]);	
				  }
                  $uploadedFiles[$_FILES['f']['tmp_name'][$i]] = $_FILES['f']['name'][$i];
              }
          }
      }

      if ($error) {
          $errorMessage = 'following errors occured when sending the contact formular:'."\n";
          if (count($uploadErrors) > 0) {
              foreach ($uploadErrors as $err) {
                  $tmp .= '<strong>'.$err['name']."</strong><br/>\n- ".$err['error']."<br/><br/>\n";
              }
              $tmp = "<br/><br/>\n".$tmp;
          }
          $errorMessage .= $tmp.'';
          $fehler['upload'] = $errorMessage;
      }
  }


// if no error, an email will be sent
   if (!isset($fehler))
   {
		// ------------------------------------------------------------
		// -------------------- send mail to admin --------------------
		// ------------------------------------------------------------
	   
		// ---- create mail-message for admin
	  
	  $mailcontent  = "The following has been sent by the contact form:\n" . "-------------------------------------------------------------------------\n\n";
   $mailcontent .= "Name: " . $name . "\n";
   $mailcontent .= "E-Mail: " . $email . "\n\n";
   $mailcontent .= "Phone Number: " . $phonenumber . "\n";
   $mailcontent .= "Place: " . $place . "\n";
   $mailcontent .= "\nSubject: " . $subject . "\n";
   $mailcontent .= "Message:\n" . $_POST['message'] = preg_replace("/\r\r|\r\n|\n\r|\n\n/","\n",$_POST['message']) . "\n\n";

		if(count($uploadedFiles) > 0){
			if($cfg['UPLOAD_ACTIVE']){
				$mailcontent .= 'The following files have been uploaded:'."\n";
				foreach ($uploadedFiles as $filename) {
					$mailcontent .= ' - '.$cfg['DOWNLOAD_URL'].'/'.$cfg['UPLOAD_FOLDER'].'/'.$filename."\n";
				}
			} else {
				$mailcontent .= 'The following files have been attached:'."\n";
				foreach ($uploadedFiles as $filename) {
					$mailcontent .= ' - '.$filename."\n";
				}
			}
		}
		$mailcontent .= "\n\nIP address: " . $ip . "\n";
		$mailcontent = strip_tags ($mailcontent);
	   
		// ---- get attachments for admin
		$attachments = array();
		if(!$cfg['UPLOAD_ACTIVE'] && count($uploadedFiles) > 0){
			foreach($uploadedFiles as $tempFilename => $filename) {
				$attachments[$filename] = file_get_contents($tempFilename);	
			}
		}
		
		// ---- send mail to admin
$success = sendMyMail($email, $name, $empfaenger, $subject, $mailcontent, $attachments);
		
		// ------------------------------------------------------------
		// ------------------- send mail to customer ------------------
		// ------------------------------------------------------------
		if($success){
			
			// ---- create mail-message for customer
		
		
			$mailcontent  = "Thank you very much for your e-mail. We will reply as fast as we can.\n\n";
  $mailcontent .= "Summary: \n" .
  "-------------------------------------------------------------------------\n\n";
  $mailcontent .= "Name: " . $name . "\n";
  $mailcontent .= "E-Mail: " . $email . "\n\n";
  $mailcontent .= "Phone Number: " . $phonenumber . "\n";
  $mailcontent .= "Place: " . $place . "\n";
  $mailcontent .= "\nSubject: " . $subject . "\n";
  $mailcontent .= "Message:\n" . str_replace("\r", "", $message) . "\n\n";
			if(count($uploadedFiles) > 0){
				$mailcontent .= 'You have assigned the following files:'."\n";
				foreach($uploadedFiles as $file){
					$mailcontent .= ' - '.$file."\n";
				}
			}
			$mailcontent = strip_tags ($mailcontent);	
			

			
			// ---- send mail to customer
$success = sendMyMail($empfaenger, $ihrname, $email, "Your request", $mailcontent);
			echo "<META HTTP-EQUIV=\"refresh\" content=\"0;URL=".$danke."\">";
			exit;
		}
	}
}
// clean post
foreach($_POST as $key => $value){
    $_POST[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
}
?>
<?php




function sendMyMail($fromMail, $fromName, $toMail, $subject, $content, $attachments=array()){
	
	$boundary = md5(uniqid(time()));
	$eol = PHP_EOL;
	
	// header
	$header = "From: =?UTF-8?B?".base64_encode(stripslashes($fromName))."?= <".$fromMail.">".$eol;
	$header .= "Reply-To: <".$fromMail.">".$eol;
	$header .= "MIME-Version: 1.0".$eol;
	if(is_array($attachments) && 0<count($attachments)){
		$header .= "Content-Type: multipart/mixed; boundary=\"".$boundary."\"";
	}
	else{
		$header .= "Content-type: text/plain; charset=utf-8";
	}
	
	
	// content with attachments
	if(is_array($attachments) && 0<count($attachments)){
		
		// content
		$message = "--".$boundary.$eol;
		$message .= "Content-type: text/plain; charset=utf-8".$eol;
		$message .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$message .= $content.$eol;

		// attachments
		foreach($attachments as $filename=>$filecontent){
			$filecontent = chunk_split(base64_encode($filecontent));
			$message .= "--".$boundary.$eol;
			$message .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol;
			$message .= "Content-Transfer-Encoding: base64".$eol;
			$message .= "Content-Disposition: attachment; filename=\"".$filename."\"".$eol.$eol;			
			$message .= $filecontent.$eol;
		}
		$message .= "--".$boundary."--";
	}
	// content without attachments
	else{
		$message = $content;
	}
	
	// subject
	$subject = "=?UTF-8?B?".base64_encode($subject)."?=";
	
	// send mail
	return mail($toMail, $subject, $message, $header);
}

?>
<!DOCTYPE html>
<html lang="de-DE">
	<head>
		<meta charset="utf-8">
		<meta name="language" content="de"/>
		<meta name="description" content="kontaktformular.com"/>
		<meta name="revisit" content="After 7 days"/>
		<meta name="robots" content="INDEX,FOLLOW"/>
		<title>kontaktformular.com</title>
		<link href="style-contact-form.css" rel="stylesheet" type="text/css" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	</head>
	<body id="Kontaktformularseite">
		<form class="kontaktformular" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" enctype="multipart/form-data">
			<input type="hidden" name="action" value="smail" />
			<input type="hidden" name="content" value="formular"/>
			<div class="row">	
				<label>Name: <span class="pflichtfeld">*</span></label>
				<div class="field">
					<?php if ($fehler["name"] != "") { echo $fehler["name"]; } ?><input type="text" name="name" maxlength="50" id="textfield" value="<?php echo $_POST[name]; ?>"  <?php if ($fehler["name"] != "") { echo 'class="errordesignfields"'; } ?>/>
					
				</div>
			</div>
			<div class="row">	
				<label>E-Mail: <span class="pflichtfeld">*</span></label>
				<div class="field">
						<?php if ($fehler["email"] != "") { echo $fehler["email"]; } ?><input type="text" name="email" maxlength="50" value="<?php echo $_POST[email]; ?>"  <?php if ($fehler["email"] != "") { echo 'class="errordesignfields"'; } ?>/>
				
				</div>
			</div>
			<div class="row">	
				<label>Place: </label>
				<div class="field">
					<input type="text" name="place" maxlength="50" value="<?php echo $_POST[place]; ?>"  />
				</div>
			</div>
			
			<div class="row">	
				<label>Phone Number: </label>
				<div class="field">
					<input type="text" name="phonenumber" maxlength="50" value="<?php echo $_POST[phonenumber]; ?>"  />
				</div>
			</div>
			<div class="row">
				<label>Subject: <span class="pflichtfeld">*</span></label>
				<div class="field">
					<?php if ($fehler["subject"] != "") { echo $fehler["subject"]; } ?><input type="text" name="subject" maxlength="50" value="<?php echo $_POST[subject]; ?>"  <?php if ($fehler["subject"] != "") { echo 'class="errordesignfields"'; } ?>/>
					
				</div>
			</div>
			<div class="row">	
				<label>Message: <span class="pflichtfeld">*</span></label>
				<div class="field">
						<?php if ($fehler["message"] != "") { echo $fehler["message"]; } ?><textarea name="message"  cols="30" rows="10" <?php if ($fehler["message"] != "") { echo 'class="errordesignfields"'; } ?>><?php echo $_POST[message]; ?></textarea>
				
				</div>
			</div>

			<?php
				for ($i=0; $i < $cfg['NUM_ATTACHMENT_FIELDS']; $i++) {
					echo '<div class="row"><label>Attachment</label><div class="field"><input type="file" size="12" name="f[]" /></div></div>';
				}
			?>
			<br/>
			<div class="row">
				<label>Security Question:</label>
				<div class="field">
					<?php echo $q[1]; ?>  <input type="hidden" name="q_id" value="<?php echo $q[0]; ?>"/>
				</div>
			</div>
			<div class="row">
				<label>Please enter: <span class="pflichtfeld">*</span></label>
				<div class="field">
					<input type="text" <?php if ($fehler["q_id12"] != "") { echo 'class="errordesignfields"'; } ?> name="q"/>
					
				</div>
				</div>
				
<div class="row">
<label></label>
<div class="field">
				<?php if ($fehler["q_id12"] != "") { echo $fehler["q_id12"]; } ?>
				<?php 
if ($fehler) {
}
   else {
      print "For protection against spam answer the question.";
         }
?>

</div>
</div>


			
			<br/>
			<div class="row">
				<label></label>
				<div class="field buttons">
					<div class="pflichtfeldhinweis">Advice: Fields with <span class="pflichtfeld">*</span> have to be filled.</div>
					<input type="submit" name="mt-mk" value="Send" onclick="tescht();"/>
					<input type="submit" name="delete" value="Delete" />
				</div>
			</div>
		</form> 
	</body>
</html>

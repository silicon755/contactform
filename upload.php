<?php
$cfg = array();

#########################################################################
# Pluscom Services LIMITED                                                  #
# http://www.pluscom.co.ke                                        #
# All rights by pluscom.co.ke                                         #
#-----------------------------------------------------------------------#
# I-Net: http://www.pluscom.co.ke                                     #
#########################################################################
// It´s not allowed to remove the copyright notice!

  

  // true = attachments will be uploaded to a directory
  // false = attachments will be sent as an email-attachment (standard)
  $cfg['UPLOAD_ACTIVE'] = false;

  //--------------------------------------------------------------------------------------//
  //--------------------------------------------------------------------------------------//
  //--------------------------------------------------------------------------------------//


  // maximum file size in KB
  // this option is regulated by the php and server settings
  $cfg['MAX_FILE_SIZE'] = 1024;

  // maximum size of several files in KB. (at more than 1 upload field)
  $cfg['MAX_ATTACHMENT_SIZE'] = 2048;

  // number of attachment fields
  $cfg['NUM_ATTACHMENT_FIELDS'] = 0;

  // forbidden file extensions
  // example: exe|com|pif
  $cfg['BLACKLIST_EXT'] = 'exe|pif|gif|php|htm|html|com|bat';

  // blocked IP´s
  // example: array('192.168.1.2', '192.168.2.4');
  $cfg['BLACKLIST_IP'] = array();
  
  //--------------------------------------------------------------------------------------//
  //--------------------------------------------------------------------------------------//
  //--------------------------------------------------------------------------------------//
  
  // in case of uploading the attachments to a directory please complete these details !!!

  // the folder "upload" needs to be created. This folder needs writing authorization (chmod 777)
  $cfg['UPLOAD_FOLDER'] = 'upload';

  // path to the formular (without / at the end!!)
  $cfg['DOWNLOAD_URL'] = 'http://www.domain.com/contactform';

?>
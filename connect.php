<?php

ini_set('dispalay_errors',0);

$dbhost="localhost";
$dbuser="root";
$dbpass="";
$dbname="lovifansdb";
$dbase = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname);


 use PHPMailer\PHPMailer\PHPMailer;
 use PHPMailer\PHPMailer\SMTP;
 use PHPMailer\PHPMailer\Exception;




function file_upload($dbase,$file,$index=0) {

    $supfiles = [["jpg","jpeg","png","svg","gif"],["mp4","mov","avi","wmv","AVCHD"],["mp3","aac","aiff","alac","m4a","dsd"],["pdf","txt","pptx","xlsx","docx"]]; 
    $ftypes = ["image","video","audio","docs"];
    $support = false;

    
    if(!empty($file["name"][$index])) {
        $fname = $file["name"][$index];
        $ftype = $file["type"][$index];

        $fext = explode(".", $fname)[1];
        $stype = "";
    
        for($i = 0; $i < 4; $i++) {
            foreach($supfiles[$i] as $s) {
                if($s == $fext) {
                    $support = true;
                    $stype = $ftypes[$i];
                    break;
                }
            }
        }
    
        if($support && $file["size"][$index] <= 209715200) {
            $path = "$_SESSION[priv]/uploads/" . $stype ."/". date("Y-m-d")."/";

            if(!is_dir($path)) mkdir($path); // Create the directory
        

            $new_fname = date("His") . $_SESSION["ustrid"] . "$index" .".". $fext;
            $uploadedFile = $path . $new_fname ;

                
            if(move_uploaded_file($file['tmp_name'][$index], $uploadedFile)) {
                mysqli_query($dbase,"INSERT INTO  files  ( fiid, uid,  finame,  finname,  fitype, fistat ,fidate, fiip ) VALUES (NULL, $_SESSION[uid],'$fname', '$new_fname', '$stype','A' ,current_timestamp(), '$_SERVER[REMOTE_ADDR]')");
                $fid = mysqli_insert_id($dbase);
                mysqli_query($dbase,"INSERT INTO  fconnect  ( fcid ,  fiid ) VALUES (NULL, $fid)");
                $fcid = mysqli_insert_id($dbase);
                return "$fcid";
    
            } else return '';
        } else return ''; 
    } else return '';

}

 function mailing($mail){

 
         require "$_SESSION[priv]/PHPMailer/src/Exception.php";
         require "$_SESSION[priv]/PHPMailer/src/PHPMailer.php";
         require "$_SESSION[priv]/PHPMailer/src/SMTP.php";
 
 
         $mail = new PHPMailer(true);
 
         //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
 
         $mail->isSMTP();
         $mail->SMTPAuth = true;
         $mail->Host = "host domain";
         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
         $mail->Port = null; //port
         $mail->Username = "username";
         $mail->Password = "password";
 
         $mail->isHTML(true);
 
         return $mail;

 }

 function randoms($len = 20){
         $c = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
         $rnd = "";
         for($i=1;$i<=$len;$i++) $rnd.= $c[rand(0,61)];
         return $rnd;
 }
?>
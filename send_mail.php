<?php
/*
This first bit sets the email address that you want the form to be submitted to.
You will need to change this value to a valid email address that you can access.
*/
$webmaster_email = "TODO@yahoo.com";

/*
This bit sets the URLs of the supporting pages.
If you change the names of any of the pages, you will need to change the values here.
*/
$feedback_page = "index.html";
//$error_page = "error_message.html";
//$thankyou_page = "thank_you.html";

/*
This next bit loads the form field data into variables.
If you add a form field, you will need to add it here.
*/
$name = $_REQUEST['name'] ;
$email_address = $_REQUEST['email'] ;
$phone = $_REQUEST['tel'] ;
$comments = $_REQUEST['message'] ;

/*
The following function checks for email injection.
Specifically, it checks for carriage returns - typically used by spammers to inject a CC list.
*/
function isInjected($str) {
	$injections = array('(\n+)',
	'(\r+)',
	'(\t+)',
	'(%0A+)',
	'(%0D+)',
	'(%08+)',
	'(%09+)'
	);
	$inject = join('|', $injections);
	$inject = "/$inject/i";
	if(preg_match($inject,$str)) {
		return true;
	}
	else {
		return false;
	}
}

// If the user tries to access this script directly
if (!isset($_REQUEST['email']) || !isset($_POST['submit'])) {
	header( "Location: $feedback_page" ); //redirect them to the form
}

// If the form fields are empty, redirect to the error page.
elseif (empty($name) || empty($email_address) || empty($comments)) {
	header( "Location: $feedback_page" ); //redirect them to the form
}

/* 
If email injection is detected, redirect to the error page.
If you add a form field, you should add it here.
*/
elseif ( isInjected($email_address) || isInjected($name)  || isInjected($phone)|| isInjected($comments) ) {
	header( "Location: $feedback_page" ); //redirect them to the form
}

// If user properly clicks submit button
elseif (isset($_POST['submit'])) {
	$name2 = htmlspecialchars(stripslashes(trim($name)));
	$email_address2 = htmlspecialchars(stripslashes(trim($email_address)));
	$phone2 = htmlspecialchars(stripslashes(trim($phone)));
	$comments2 = htmlspecialchars(stripslashes(trim($comments)));

	if(!preg_match("/^[A-Za-z .'-]+$/", $name2)){
    	$error = 'Invalid name';
    }
    if(!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/", $email_address2)){
      $error = 'Invalid email';
    }
    if(strlen($phone2)>=11 && !preg_match('/^[0-9]*$/', $phone2)){
      $error = 'Invalid phone';
    }
    if(strlen($comments2) === 0){
      $error = 'Your message should not be empty';
    }

    //Start building the email
    $msg = 
	"You have received a message via party-thyme-catering.com!" . "\r\n" . 
	"Name: " . $name2 . "\r\n" . 
	"Email: " . $email_address2 . "\r\n" . 
	"Phone Number: " . $phone2 . "\r\n" . 
	"Message: " . $comments2 ;

	// make sure each line doesn't exceed 70 characters
	$msg = wordwrap($msg, 70);

	// To send HTML mail, the Content-type header must be set
	$headers = "From: ".$webmaster_email."\r\n"; //double check if this works properly
	$headers .= "Reply-To: ".$webmaster_email."\r\n"; //double check if this works properly
	$headers .= "X-Mailer: PHP/".phpversion();
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text; charset=iso-8859-1\r\n";

    //If all tests passed, send the email
    if(!isset($error)) mail( "$webmaster_email", "Online Catering Order Request", $msg, $headers);
    //TODO: should it redirect to a new page if successfully sent? yes.

	header( "Location: $feedback_page" );
}
?>
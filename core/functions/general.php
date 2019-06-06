<?php
use PHPMailer\PHPMailer\PHPMailer;

function add_event($hook, $function) {
    global $actions;

    // create an array of function handlers if it doesn't already exist
    if(!isset($actions[$hook]))
        $actions[ $hook ] = array();

    // append the current function to the list of function handlers
    $actions[$hook][] = $function;
}

function perform_event($hook) {
    global $actions;

    if(isset($actions[$hook])) {
        // call each function handler associated with this hook
        foreach($actions[$hook] as $function)
            call_user_func($function);
    }
}

function get_settings() {
	global $database;
	global $config;

    $settings = $database->query("SELECT * FROM `settings` WHERE `id` = 1")->fetch_object();
    $settings->url = $config['url'];

    /* Parse the email templates quickly */
    $activation_email_template = json_decode($settings->activation_email_template);
    $settings->activation_email_template_subject = $activation_email_template->subject;
    $settings->activation_email_template_body 	= $activation_email_template->body;

    $lost_password_email_template = json_decode($settings->lost_password_email_template);
    $settings->lost_password_email_template_subject = $lost_password_email_template->subject;
    $settings->lost_password_email_template_body 	= $lost_password_email_template->body;

    $credentials_email_template = json_decode($settings->credentials_email_template);
    $settings->credentials_email_template_subject = $credentials_email_template->subject;
    $settings->credentials_email_template_body 	= $credentials_email_template->body;

    return $settings;
}

function generate_email_template($email_template_subject_array = [], $email_template_subject, $email_template_body_array = [], $email_template_body) {

    $email_template_subject = str_replace(
        array_keys($email_template_subject_array),
        array_values($email_template_subject_array),
        $email_template_subject
    );

    $email_template_body = str_replace(
        array_keys($email_template_body_array),
        array_values($email_template_body_array),
        $email_template_body
    );

    return (object) [
    	'subject' => $email_template_subject,
		'body' => $email_template_body
	];
}

function send_server_mail($to, $from, $title, $message) {

	$headers = "From: " . strip_tags($from) . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	mail($to, $title, $message, $headers);
}

function sendmail($to, $title, $message) {
	global $settings;

	if(!empty($settings->smtp_host)) {

		try {
            $mail = new PHPMailer;
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->SMTPDebug = 0;

            if ($settings->smtp_encryption != '0') {
                $mail->SMTPSecure = $settings->smtp_encryption;
            }

            $mail->SMTPAuth = $settings->smtp_auth;
            $mail->isHTML(true);

            $mail->Host = $settings->smtp_host;
            $mail->Port = $settings->smtp_port;
            $mail->Username = $settings->smtp_user;
            $mail->Password = $settings->smtp_pass;

            $mail->setFrom($settings->smtp_from, $settings->title);
            $mail->addReplyTo($settings->smtp_from, $settings->title);

            /* Check if receipient is array or not */
            if(is_array($to)) {
            	foreach($to as $address) {
                    $mail->addAddress($address);
                }
			} else {
                $mail->addAddress($to);
            }

            $mail->Subject = $title;
            $mail->Body = $message;

            $sent = $mail->send();
        } catch (Exception $e) {
//            echo 'Message could not be sent.';
//            echo 'Mailer Error: ' . $mail->ErrorInfo;
        }


	} else {
        send_server_mail($to, $settings->smtp_from, $title, $message);
	}

}


function parse_url_parameters() {
	return (isset($_GET['page'])) ? explode('/', filter_var(rtrim($_GET['page'], '/'), FILTER_SANITIZE_URL)) : [];
}

function redirect($new_page = '') {
	global $settings;

	header('Location: ' . $settings->url . $new_page);
	die();
}

function trim_value(&$value) {
	$value = trim($value);
}

function colorful_number($number) {
    if($number > 0) {
        return '<span style="color: #28a745 !important;">+' . $number . '</span>';
    }
	elseif($number < 0) {
        return '<span style="color: #dc3545 !important;">' . $number . '</span>';

    } else {
        return '-';
    }
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) === $date;
}

function filter_banned_words($value) {
	global $settings;

	$words = explode(',', $settings->banned_words);
	array_walk($words, 'trim_value');

	foreach($words as $word) {
		$value = str_replace($word, str_repeat('*', strlen($word)), $value);
	}

	return $value;
}


function generate_slug($string, $delimiter = '_') {

    /* Convert accents characters */
    $string = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

    /* Replace all non words characters with the specified $delimiter */
    $string = preg_replace('/\W/', $delimiter, $string);

    /* Check for double $delimiters and remove them so it only will be 1 delimiter */
    $string = preg_replace('/_+/', '_', $string);

    /* Remove the $delimiter character from the start and the end of the string */
    $string = trim($string, $delimiter);

    return $string;
}

function generate_string($length) {
	$characters = str_split('abcdefghijklmnopqrstuvwxyz0123456789');
	$content = '';

	for($i = 1; $i <= $length; $i++) {
		$content .= $characters[array_rand($characters, 1)];
	}

	return $content;
}


function resize($file_name, $path, $width, $height, $center = false) {
	/* Get original image x y*/
	list($w, $h) = getimagesize($file_name);

	/* calculate new image size with ratio */
	$ratio = max($width/$w, $height/$h);
	$h = ceil($height / $ratio);
	$x = ($w - $width / $ratio) / 2;
	$w = ceil($width / $ratio);
	$y = 0;
	if($center) $y = 250 + $h/1.5;

	/* read binary data from image file */
	$imgString = file_get_contents($file_name);

	/* create image from string */
	$image = imagecreatefromstring($imgString);
	$tmp = imagecreatetruecolor($width, $height);
	imagecopyresampled($tmp, $image,
	0, 0,
	$x, $y,
	$width, $height,
	$w, $h);

	/* Save image */
	imagejpeg($tmp, $path, 100);

	return $path;
	/* cleanup memory */
	imagedestroy($image);
	imagedestroy($tmp);
}

function formatBytes($bytes, $precision = 2) {
    $kilobyte = 1024;
    $megabyte = $kilobyte * 1024;
    $gigabyte = $megabyte * 1024;
    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {
        return $bytes . ' B';

    } elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
        return round($bytes / $kilobyte, $precision) . ' KB';

    } elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
        return round($bytes / $megabyte, $precision) . ' MB';

    } elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
        return round($bytes / $gigabyte, $precision) . ' GB';

    } elseif ($bytes >= $terabyte) {
        return round($bytes / $terabyte, $precision) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

function string_resize($string, $maxchar) {
	$length = strlen($string);
	if($length > $maxchar) {
		$cutsize = -($length-$maxchar);
		$string  = substr($string, 0, $cutsize);
		$string  = $string . '..';
	}
	return $string;
}



function display_notifications() {
	global $language;

	$types = ['error', 'success', 'info'];
	foreach($types as $type) {
		if(isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
			if(!is_array($_SESSION[$type])) $_SESSION[$type] = [$_SESSION[$type]];

			foreach($_SESSION[$type] as $message) {
				$csstype = ($type == 'error') ? 'danger' : $type;

				echo '
					<div class="alert alert-' . $csstype . ' animated fadeInDown">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<strong>' . $language->global->message_type->$type . '</strong> ' . $message . '
					</div>
				';
			}
			unset($_SESSION[$type]);
		}
	}

}

?>

<?php

$user_agent     =   $_SERVER['HTTP_USER_AGENT'];
function os_detect() { 
    global $user_agent;
    $os_platform    =   "Unknown OS";
  
    //Taken from cttyi
    $os_array       =   array(
                            '/windows nt 10/i'     =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
							'/kalilinux/i'          =>  'Kali Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile',
							'/Windows Phone/i'      =>  'Windows Phone'
                        );

    foreach ($os_array as $regex => $value) { 
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }   
    return $os_platform;
}

function browser_detect() {

    global $user_agent;

    $browser        =   "Unknown Browser";

    $browsers  =   array(
                            '/msie/i'                       =>  'Internet Explorer',
                            '/firefox/i'                    =>  'Firefox',
							'/Mozilla/i'	                =>	'Mozila',
						    '/Mozilla/5.0/i'                =>	'Mozila',
                            '/safari/i'                     =>  'Safari',
                            '/chrome/i'                     =>  'Chrome',
                            '/edge/i'                       =>  'Edge',
                            '/opera/i'                      =>  'Opera',
							'/OPR/i'                        =>  'Opera',
                            '/netscape/i'                   =>  'Netscape',
                            '/maxthon/i'                    =>  'Maxthon',
                            '/konqueror/i'                  =>  'Konqueror',
						    '/Bot/i'		                =>	'BOT Browser',
						    '/Valve Steam GameOverlay/i'    =>  'Steam',
                            '/mobile/i'                     =>  'Handheld Browser'
                        );

    foreach ($browsers as $regex => $value) { 

        if (preg_match($regex, $user_agent)) {
            $browser    =   $value;
        }

    }

    return $browser;

}
function GetIP(){
    //Trustworthiness hierarchy. It's worth noting that the HTTP_* can be set to any arbitrary value by the user.
    //It should not be used for anything other than reference and logging.
    $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
      ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    return $ip;
}

function GetRawIP(){
    return $_SERVER['REMOTE_ADDR']
}

function log_ip($filename){

    $os        =   os_detect();
	$browser   =   browser_detect();
    $raw_ip    =   GetRawIP();
    $ip        =   GetIP();

    if(isset($_SERVER['HTTP_REFERER'])){
        $site = "Direct";
    }else{
        $site = $_SERVER['HTTP_REFERER'];
    }

    $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
    $location = $details->city .", ".$details->region .", ".$details->country;
		
    if ($ip == $raw_ip){
        $myfile = file_put_contents(__DIR__ . $filename,date("Y-M-D - H:I:S - "). "(".$location. ") "
        . $raw_ip." | ". $os. " | ". $browser. " | From: "
        . $site. " | Agent:" .$user_agent .PHP_EOL , FILE_APPEND | LOCK_EX);
    }else{
        $myfile = file_put_contents(__DIR__ . $filename,date("Y-M-D - H:I:S - "). "(".$location. ") "
        . $raw_ip."{Set: ".$ip."}"." | ". $os. " | ". $browser. " | From: "
        . $site. " | Agent:" .$user_agent .PHP_EOL , FILE_APPEND | LOCK_EX);
    }

	fclose($myfile);

}
	

?>

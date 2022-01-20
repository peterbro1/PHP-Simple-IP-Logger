<?php

function GetIP(){
    //Trustworthiness hierarchy. It's worth noting that the HTTP_* can be set to any arbitrary value by the user.
    //It should not be used for anything other than reference and logging.
    $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : isset($_SERVER['HTTP_X_FORWARDED_FOR']) 
      ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
    return $ip;
}

function GetRawIP(){
    return $_SERVER['REMOTE_ADDR'];
}

function log_ip($filename){
    $raw_ip     =   GetRawIP();
    $ip         =   GetIP();
    $site       =   isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "No Refer";


    try{
        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}"));
        $location = $details->city .", ".$details->region .", ".$details->country;
    }catch (Exception $e){
        $location = "Unknown Location";
    }

    $fp = fopen($filename, 'a');
    fwrite($fp,"\n\n==========".date(DATE_RFC2822)."==========");
    fwrite($fp,"\nDirect IP: ".$raw_ip);
    fwrite($fp,"\nDeduced IP: ".$ip);
    fwrite($fp,"\nLocation: ".$location);
    //Browser and OS detection "Cleanup" is convoluted and unnecessarily difficult these days,
    //so will print the whole string. No data is lost.
    fwrite($fp,"\nUser:". $_SERVER['HTTP_USER_AGENT']);
    fwrite($fp,"\n==============================");
    fclose($fp);

}
	

?>

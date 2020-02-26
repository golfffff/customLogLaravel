<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

class Logger extends Controller
{
    //
    public function show()
    {
        Log::info('show log gen from show()');
        return "log gen = ".$this->gen_uuid();
    }

    public function gen_uuid($len=8)
    {
        $hex = md5("your_random_salt_here_31415" . uniqid("", true));
    
        $pack = pack('H*', $hex);
    
        $uid = base64_encode($pack);        // max 22 chars
    
        $uid = preg_replace("[^A-Za-z0-9]", "", $uid);    // mixed case
        //$uid = ereg_replace("[^A-Z0-9]", "", strtoupper($uid));    // uppercase only
    
        if ($len<4)
            $len=4;
        if ($len>128)
            $len=128;                       // prevent silliness, can remove
    
        while (strlen($uid)<$len)
            $uid = $uid . $this->gen_uuid(22);     // append until length achieved
    
        return substr($uid, 0, $len);
    }
}

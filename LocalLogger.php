<?php

namespace App\Logging; // creatre Logging

use Monolog\Formatter\LineFormatter;

class LocalLogger
{
    private $request;

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter($this->getLogFormatter());
        }
    }

    protected function getLogFormatter()
    {
        $uniqueClientId = $this->getUniqueClientId();
        $uniqueUuid = $this->gen_uuid();

        $format = str_replace(
            '[%datetime%] ',
            sprintf('[%%datetime%%] %s ', $uniqueUuid),
            LineFormatter::SIMPLE_FORMAT
        );

        return new LineFormatter($format, null, true, true);
    }

    protected function getUniqueClientId()
    {
        $clientId = md5($this->request->server('HTTP_USER_AGENT').'/'.$this->request->ip());
        $sessionId = \Session::getId();

        return "[{$clientId}:{$sessionId}]";
        
    }
    protected function gen_uuid($len=8)
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

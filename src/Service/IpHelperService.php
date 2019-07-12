<?php

namespace App\Service;

class IpHelperService
{
    /**
     * IP Address
     * @var string
     */
    private $ip = "";

    /**
     * Network Mask
     * @var string
     */
    private $mask = "";

    /**
     * CIDR
     * @var int
     */
    private $cidr = 0;

    /**
     * IpHelperService constructor.
     * @param $ip
     * @param $mask
     */
    public function __construct($ip, $mask)
    {
        $this->ip = $ip;
        $this->mask = $mask;
        $this->cidr = $this->netmaskToCidr($mask);
    }

    /**
     * convert cidr to netmask
     * e.g. 21 = 255.255.248.0
     *
     * @param $cidr
     * @return bool|string
     */
    private function cidrToNetmask($cidr)
    {
        $bin = "";
        for ($i = 1; $i <= 32; $i++) {
            $bin .= $cidr >= $i ? '1' : '0';
        }
        $netmask = long2ip(bindec($bin));
        if ($netmask == "0.0.0.0") {
            return false;
        }

        return $netmask;
    }

    /**
     * get network address from cidr subnet
     * e.g. 10.0.2.56/21 = 10.0.0.0
     *
     * @param $ip
     * @param $cidr
     * @return string
     */
    private function cidrToNetwork($ip, $cidr)
    {
        $network = long2ip((ip2long($ip)) & ((-1 << (32 - (int)$cidr))));
        return $network;
    }

    /**
     * convert netmask to cidr
     * e.g. 255.255.255.128 = 25
     *
     * @param $netmask
     * @return int
     */
    private function netmaskToCidr($netmask)
    {
        $bits = 0;
        $netmask = explode(".", $netmask);

        foreach ($netmask as $octect)
            $bits += strlen(str_replace("0", "", decbin($octect)));

        return $bits;
    }

    /**
     * is ip in subnet
     * e.g. is 10.5.21.30 in 10.5.16.0/20 == true
     *      is 192.168.50.2 in 192.168.30.0/23 == false
     *
     * @param $ip
     * @param $network
     * @param $cidr
     * @return bool
     */
    private function cidrMatch($ip, $network, $cidr)
    {
        if ((ip2long($ip) & ~((1 << (32 - $cidr)) - 1)) == ip2long($network)) {
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllIps() {
        $bin = "";
        $ret = [];

        for($i=1;$i<=32;$i++) {
            $bin .= $this->cidr >= $i ? '1' : '0';
        }

        $this->cidr = bindec($bin);

        $ip = ip2long($this->ip);
        $nm = $this->cidr;
        $nw = ($ip & $nm);
        $bc = $nw | ~$nm;
        $bc_long = ip2long(long2ip($bc));

        for($zm=1;($nw + $zm)<=($bc_long - 1);$zm++)
        {
            $ret[]=long2ip($nw + $zm);
        }
        return $ret;
    }

    public function ping($ip) {
        exec(sprintf('ping -n 1 %s', escapeshellarg($ip)), $res, $rval);
        return $rval === 0;
    }
}
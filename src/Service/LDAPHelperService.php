<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class LDAPHelperService
{
    /**
     * @var ContainerParametersHelper
     */
    private $containerHelper;

    /**
     * LDAP Resource
     * @var resource
     */
    private $ldapConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LDAPHelperService constructor.
     * @param ContainerParametersHelper $containerParametersHelper
     * @throws \Exception
     */
    public function __construct(ContainerParametersHelper $containerParametersHelper, LoggerInterface $logger)
    {
        $this->containerHelper = $containerParametersHelper;
        $this->logger = $logger;

        if(!function_exists("ldap_bind")) {
            throw new \Exception("LDAP not available on this Server!");
        }

        if(is_null($this->containerHelper->getParameter('ldap.server'))) {
            throw new \Exception("LDAP is disabled in this environment!");
        }

        $server = $this->containerHelper->getParameter('ldap.server');
        $port = $this->containerHelper->getParameter('ldap.port');

        $this->ldapConnection = ldap_connect($server, $port);
        ldap_set_option($this->ldapConnection,LDAP_OPT_PROTOCOL_VERSION,3);
        ldap_set_option($this->ldapConnection,LDAP_OPT_REFERRALS,0);
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function checkPassword($username, $password) : bool
    {
        $check = @ldap_bind($this->ldapConnection, $username, $password);
        if(!empty($php_errormsg)) $this->logger->error($php_errormsg);

        return $check;
    }
}
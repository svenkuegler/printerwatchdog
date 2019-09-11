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
     * @var bool
     */
    private $isEnabled = false;

    /**
     * LDAPHelperService constructor.
     * @param ContainerParametersHelper $containerParametersHelper
     * @param LoggerInterface $logger
     */
    public function __construct(ContainerParametersHelper $containerParametersHelper, LoggerInterface $logger)
    {
        $this->containerHelper = $containerParametersHelper;
        $this->logger = $logger;

        if(is_null($this->containerHelper->getParameter('ldap.server'))) {
            $this->logger->debug("LDAP is disabled in this environment!");
            $this->isEnabled = false;
            return;
        }

        if(!function_exists("ldap_bind")) {
            $this->logger->debug("LDAP PHP-Module not available on this Server!");
            $this->isEnabled = false;
            return;
        }

        $server = $this->containerHelper->getParameter('ldap.server');
        $port = $this->containerHelper->getParameter('ldap.port');

        $this->ldapConnection = ldap_connect($server, $port);
        if(!empty($php_errormsg)) $this->logger->error($php_errormsg);
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
        if($this->isEnabled) {
            $check = @ldap_bind($this->ldapConnection, $username, $password);
            if(!empty($php_errormsg)) $this->logger->error($php_errormsg);

            return $check;
        } else {
            $this->logger->error('Tried to check LDAP Password but LDAP seems to be disabled!');
            return false;
        }

    }
}
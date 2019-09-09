<?php

namespace App\Entity;

class LdapUser
{
    /**
     * @var string
     */
    private $displayName = "";

    /**
     * @var string
     */
    private $username = "";

    /**
     * @var string
     */
    private $userPrincipalName = "";

    /**
     * @var string
     */
    private $mail = "";

    /**
     * @var bool
     */
    private $alreadyExists = false;

    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     * @return LdapUser
     */
    public function setDisplayName(string $displayName): LdapUser
    {
        $this->displayName = $displayName;
        return $this;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return LdapUser
     */
    public function setUsername(string $username): LdapUser
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserPrincipalName(): string
    {
        return $this->userPrincipalName;
    }

    /**
     * @param string $userPrincipalName
     * @return LdapUser
     */
    public function setUserPrincipalName(string $userPrincipalName): LdapUser
    {
        $this->userPrincipalName = $userPrincipalName;
        return $this;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param string $mail
     * @return LdapUser
     */
    public function setMail(string $mail): LdapUser
    {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAlreadyExists(): bool
    {
        return $this->alreadyExists;
    }

    /**
     * @param bool $alreadyExists
     * @return LdapUser
     */
    public function setAlreadyExists(bool $alreadyExists): LdapUser
    {
        $this->alreadyExists = $alreadyExists;
        return $this;
    }

}
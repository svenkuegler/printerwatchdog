<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrinterRepository")
 */
class Printer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $Ip;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $SerialNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Location;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastCheck;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isColorPrinter;

    /**
     * @ORM\Column(type="integer")
     */
    private $TonerBlack;

    /**
     * @ORM\Column(type="integer")
     */
    private $TonerYellow;

    /**
     * @ORM\Column(type="integer")
     */
    private $TonerMagenta;

    /**
     * @ORM\Column(type="integer")
     */
    private $TonerCyan;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Type;

    /**
     * @ORM\Column(type="integer")
     */
    private $TotalPages;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->Ip;
    }

    public function setIp(string $Ip): self
    {
        $this->Ip = $Ip;

        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->SerialNumber;
    }

    public function setSerialNumber(?string $SerialNumber): self
    {
        $this->SerialNumber = $SerialNumber;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLocation()
    {
        return $this->Location;
    }

    /**
     * @param mixed $Location
     */
    public function setLocation($Location): void
    {
        $this->Location = $Location;
    }

    /**
     * @return mixed
     */
    public function getLastCheck()
    {
        return $this->lastCheck;
    }

    /**
     * @param mixed $lastCheck
     */
    public function setLastCheck($lastCheck): void
    {
        $this->lastCheck = $lastCheck;
    }

    /**
     * @return mixed
     */
    public function getisColorPrinter()
    {
        return $this->isColorPrinter;
    }

    /**
     * @param mixed $isColorPrinter
     */
    public function setIsColorPrinter($isColorPrinter): void
    {
        $this->isColorPrinter = $isColorPrinter;
    }

    /**
     * @return mixed
     */
    public function getTonerBlack()
    {
        return $this->TonerBlack;
    }

    /**
     * @param mixed $TonerBlack
     */
    public function setTonerBlack($TonerBlack): void
    {
        $this->TonerBlack = $TonerBlack;
    }

    /**
     * @return mixed
     */
    public function getTonerYellow()
    {
        return $this->TonerYellow;
    }

    /**
     * @param mixed $TonerYellow
     */
    public function setTonerYellow($TonerYellow): void
    {
        $this->TonerYellow = $TonerYellow;
    }

    /**
     * @return mixed
     */
    public function getTonerMagenta()
    {
        return $this->TonerMagenta;
    }

    /**
     * @param mixed $TonerMagenta
     */
    public function setTonerMagenta($TonerMagenta): void
    {
        $this->TonerMagenta = $TonerMagenta;
    }

    /**
     * @return mixed
     */
    public function getTonerCyan()
    {
        return $this->TonerCyan;
    }

    /**
     * @param mixed $TonerCyan
     */
    public function setTonerCyan($TonerCyan): void
    {
        $this->TonerCyan = $TonerCyan;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;

        return $this;
    }

    public function getTotalPages(): ?int
    {
        return $this->TotalPages;
    }

    public function setTotalPages(int $TotalPages): self
    {
        $this->TotalPages = $TotalPages;

        return $this;
    }

}

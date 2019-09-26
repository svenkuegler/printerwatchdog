<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PrinterHistory", mappedBy="Printer", orphanRemoval=true)
     */
    private $printerHistories;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $unreachableCount;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $TonerBlackDescription;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $TonerCyanDescription;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $TonerMagentaDescription;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $TonerYellowDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ConsoleDisplay;

    public function __construct()
    {
        $this->printerHistories = new ArrayCollection();
    }

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
    public function setLocation($Location): self
    {
        $this->Location = $Location;
        return $this;
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
    public function setLastCheck($lastCheck): self
    {
        $this->lastCheck = $lastCheck;
        return $this;
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
    public function setIsColorPrinter($isColorPrinter): self
    {
        $this->isColorPrinter = $isColorPrinter;
        return $this;
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
    public function setTonerBlack($TonerBlack): self
    {
        $this->TonerBlack = $TonerBlack;
        return $this;
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
    public function setTonerYellow($TonerYellow): self
    {
        $this->TonerYellow = $TonerYellow;

        return $this;
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
    public function setTonerMagenta($TonerMagenta): self
    {
        $this->TonerMagenta = $TonerMagenta;
        return $this;
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
    public function setTonerCyan($TonerCyan): self
    {
        $this->TonerCyan = $TonerCyan;
        return $this;
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

    /**
     * @return Collection|PrinterHistory[]
     */
    public function getPrinterHistories(): Collection
    {
        return $this->printerHistories;
    }

    public function addPrinterHistory(PrinterHistory $printerHistory): self
    {
        if (!$this->printerHistories->contains($printerHistory)) {
            $this->printerHistories[] = $printerHistory;
            $printerHistory->setPrinter($this);
        }

        return $this;
    }

    public function removePrinterHistory(PrinterHistory $printerHistory): self
    {
        if ($this->printerHistories->contains($printerHistory)) {
            $this->printerHistories->removeElement($printerHistory);
            // set the owning side to null (unless already changed)
            if ($printerHistory->getPrinter() === $this) {
                $printerHistory->setPrinter(null);
            }
        }

        return $this;
    }

    public function getUnreachableCount(): int
    {
        return intval($this->unreachableCount);
    }

    public function setUnreachableCount(?int $unreachableCount): self
    {
        $this->unreachableCount = $unreachableCount;

        return $this;
    }

    public function getTonerBlackDescription(): ?string
    {
        return $this->TonerBlackDescription;
    }

    public function setTonerBlackDescription(?string $TonerBlackDescription): self
    {
        $this->TonerBlackDescription = $TonerBlackDescription;

        return $this;
    }

    public function getTonerCyanDescription(): ?string
    {
        return $this->TonerCyanDescription;
    }

    public function setTonerCyanDescription(?string $TonerCyanDescription): self
    {
        $this->TonerCyanDescription = $TonerCyanDescription;

        return $this;
    }

    public function getTonerMagentaDescription(): ?string
    {
        return $this->TonerMagentaDescription;
    }

    public function setTonerMagentaDescription(?string $TonerMagentaDescription): self
    {
        $this->TonerMagentaDescription = $TonerMagentaDescription;

        return $this;
    }

    public function getTonerYellowDescription(): ?string
    {
        return $this->TonerYellowDescription;
    }

    public function setTonerYellowDescription(?string $TonerYellowDescription): self
    {
        $this->TonerYellowDescription = $TonerYellowDescription;

        return $this;
    }

    public function getConsoleDisplay(): ?string
    {
        return $this->ConsoleDisplay;
    }

    public function setConsoleDisplay(?string $ConsoleDisplay): self
    {
        $this->ConsoleDisplay = $ConsoleDisplay;

        return $this;
    }

}

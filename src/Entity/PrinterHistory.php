<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrinterHistoryRepository")
 */
class PrinterHistory
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Printer", inversedBy="printerHistories")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Printer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $Timestamp;

    /**
     * @ORM\Column(type="integer")
     */
    private $TotalPages;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $TonerBlack;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $TonerYellow;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $TonerMagenta;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $TonerCyan;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrinter(): ?Printer
    {
        return $this->Printer;
    }

    public function setPrinter(?Printer $Printer): self
    {
        $this->Printer = $Printer;

        return $this;
    }

    public function getTimestamp(): ?\DateTimeInterface
    {
        return $this->Timestamp;
    }

    public function setTimestamp(\DateTimeInterface $Timestamp): self
    {
        $this->Timestamp = $Timestamp;

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

    public function getTonerBlack(): ?int
    {
        return $this->TonerBlack;
    }

    public function setTonerBlack(?int $TonerBlack): self
    {
        $this->TonerBlack = $TonerBlack;

        return $this;
    }

    public function getTonerYellow(): ?int
    {
        return $this->TonerYellow;
    }

    public function setTonerYellow(?int $TonerYellow): self
    {
        $this->TonerYellow = $TonerYellow;

        return $this;
    }

    public function getTonerMagenta(): ?int
    {
        return $this->TonerMagenta;
    }

    public function setTonerMagenta(?int $TonerMagenta): self
    {
        $this->TonerMagenta = $TonerMagenta;

        return $this;
    }

    public function getTonerCyan(): ?int
    {
        return $this->TonerCyan;
    }

    public function setTonerCyan(?int $TonerCyan): self
    {
        $this->TonerCyan = $TonerCyan;

        return $this;
    }
}

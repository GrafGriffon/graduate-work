<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NewsRepository::class)
 */
class Newsletter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    public string $mail;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    public string $hash;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isAccepted;

    public function __construct(string $mail)
    {
        $this->hash = sha1($mail);
        $this->mail = $mail;
        $this->isAccepted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setIsAccepted(bool $status): self
    {
        $this->isAccepted = $status;
        return $this;
    }
}

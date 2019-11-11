<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRequestRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserRequest
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="userRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Files", inversedBy="userRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Requirements", inversedBy="userRequests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $requirement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?Users
    {
        return $this->user;
    }

    public function setUser(?Users $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFile(): ?Files
    {
        return $this->file;
    }

    public function setFile(?Files $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getRequirement(): ?Requirements
    {
        return $this->requirement;
    }

    public function setRequirement(?Requirements $requirement): self
    {
        $this->requirement = $requirement;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @throws Exception
     */
    public function setCreatedAt(): self
    {
        $this->created_at =  new DateTime();

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }
}

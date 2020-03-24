<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RequirementsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Requirements
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="requirements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_created;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;


    /**
     * @ORM\Column(type="array")
     */
    private $fields = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserRequest", mappedBy="requirement", orphanRemoval=true)
     */
    private $userRequests;

    public function __construct()
    {
        $this->userRequests = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCreated(): ?Users
    {
        return $this->user_created;
    }

    public function setUserCreated(?Users $user_created): self
    {
        $this->user_created = $user_created;

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

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @ORM\PreFlush
     * @throws Exception
     */
    public function setUpdatedAt(): self
    {
        $this->updated_at = new DateTime();

        return $this;
    }
    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?DateTimeInterface $deleted_at): self
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }
    public function getFields(): ?array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return Collection|UserRequest[]
     */
    public function getUserRequests(): Collection
    {
        return $this->userRequests;
    }

    public function addUserRequest(UserRequest $userRequest): self
    {
        if (!$this->userRequests->contains($userRequest)) {
            $this->userRequests[] = $userRequest;
            $userRequest->setRequirement($this);
        }

        return $this;
    }

    public function removeUserRequest(UserRequest $userRequest): self
    {
        if ($this->userRequests->contains($userRequest)) {
            $this->userRequests->removeElement($userRequest);
            // set the owning side to null (unless already changed)
            if ($userRequest->getRequirement() === $this) {
                $userRequest->setRequirement(null);
            }
        }

        return $this;
    }

}

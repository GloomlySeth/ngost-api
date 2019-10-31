<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UsersRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"username"}, message="Пользователь с таким именем уже существует", errorPath="message")
 * @UniqueEntity(fields={"email"}, message="Пользователь с таким email уже существует", errorPath="message")
 */
class Users implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotNull(message="Имя пользователя не может быть пустым")
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotNull(message="Email не может быть пустым")
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;
    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;
    /**
     * @ORM\Column(type="array")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pages", mappedBy="user_updated")
     */
    private $pages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\News", mappedBy="created_user")
     */
    private $news;

    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->news = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->created_at;
    }
    /**
     * @return array
     */
    public function getRoles()
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array $roles
     * @return Users
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

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
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return '';
    }
    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Users) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    /**
     * @return Collection|Pages[]
     */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Pages $page): self
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setUserUpdated($this);
        }

        return $this;
    }

    public function removePage(Pages $page): self
    {
        if ($this->pages->contains($page)) {
            $this->pages->removeElement($page);
            // set the owning side to null (unless already changed)
            if ($page->getUserUpdated() === $this) {
                $page->setUserUpdated(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|News[]
     */
    public function getNews(): Collection
    {
        return $this->news;
    }

    public function addNews(News $news): self
    {
        if (!$this->news->contains($news)) {
            $this->news[] = $news;
            $news->setCreatedUser($this);
        }

        return $this;
    }

    public function removeNews(News $news): self
    {
        if ($this->news->contains($news)) {
            $this->news->removeElement($news);
            // set the owning side to null (unless already changed)
            if ($news->getCreatedUser() === $this) {
                $news->setCreatedUser(null);
            }
        }

        return $this;
    }
}

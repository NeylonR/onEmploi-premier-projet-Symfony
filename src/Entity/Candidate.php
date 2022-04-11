<?php

namespace App\Entity;

use Serializable;
use App\Entity\Offer;
use App\Entity\ContractType;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CandidateRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: CandidateRepository::class)]
/**
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Candidate implements UserInterface, \Serializable, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private $lastName;

    #[ORM\Column(type: 'integer')]
    private $age;

    #[ORM\Column(type: 'string', length: 255)]
    private $city;

    #[ORM\ManyToMany(targetEntity: ContractType::class, inversedBy: 'candidates')]
    private $contractType;

    #[ORM\ManyToMany(targetEntity: Offer::class, inversedBy: 'applicants')]
    private $applications;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $photo;

    /** 
     * @Vich\UploadableField(mapping= "candidate_photo", fileNameProperty="photo")
     * @Assert\File(
     *     maxSize = "10000k",
     *     mimeTypes = {"image/jpg", "image/jpeg", "image/png"},
     *     mimeTypesMessage = "Please upload a valid image"
     * )
     */
    private $photoFile;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }

    public function __construct()
    {
        $this->contractType = new ArrayCollection();
        $this->applications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_CANDIDATE';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return Collection<int, ContractType>
     */
    public function getContractType(): Collection
    {
        return $this->contractType;
    }

    public function addContractType(ContractType $contractType): self
    {
        if (!$this->contractType->contains($contractType)) {
            $this->contractType[] = $contractType;
        }

        return $this;
    }

    public function removeContractType(ContractType $contractType): self
    {
        $this->contractType->removeElement($contractType);

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Offer $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications[] = $application;
        }

        return $this;
    }

    public function removeApplication(Offer $application): self
    {
        $this->applications->removeElement($application);

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPhotoFile(): ?File
    {
        return $this->photoFile;
    }

    public function setPhotoFile(?File $photoFile = null): void
    {
        $this->photoFile = $photoFile;
 
        if($photoFile !== null){
            $this->updatedAt = new \DateTime();
        }

        return;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}

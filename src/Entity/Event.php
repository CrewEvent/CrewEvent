<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'events')]
#[HasLifecycleCallbacks]
#[Vich\Uploadable]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[Assert\Length(min: 6, minMessage: "le nom doit étre au moins de {limit} charatéres")]
    #[ORM\Column(length: 255, nullable: false)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $updatedAt = null;


    #[ORM\Column(length: 255, type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @Vich\UploadableField(mapping="event_image", fileNameProperty="image")
     * @Assert\Valid
     * @Assert\File(
     *     maxSize="1000K",
     *     mimeTypes={
     *         "image/jpg", "image/gif", "image/jpeg", "image/png"
     *     }
     * )
     * @var File
     */
    private $imageFile;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Participant::class, orphanRemoval: true)]
    private Collection $participants;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $infoGenerale = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $Annonces = [];



    public function __construct()
    {
        $this->username = new ArrayCollection();
        $this->participants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($image) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = (new \DateTimeImmutable());
        }
    }

    public function getImageFile()
    {
        return $this->imageFile;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamp()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->name,
            //$this->active,
        ]);
    }

    public function unserialize($serialized): void
    {
        [
            $this->name,
            //$this->active,
        ] = \unserialize($serialized, [self::class]);
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
            $participant->setEvent($this);
        }

        return $this;
    }

    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getEvent() === $this) {
                $participant->setEvent(null);
            }
        }

        return $this;
    }

    public function getInfoGenerale(): array
    {
        return $this->infoGenerale;
    }

    public function setInfoGenerale(?array $infoGenerale): self
    {
        array_push($this->infoGenerale , $infoGenerale);

        return $this;
    }

    public function deleteInfoGenerale(?int $key): self
    {

        unset($this->infoGenerale[$key]);
        $this->infoGenerale = array_values($this->infoGenerale);
        return $this;

    }
    public function updateInfoGenerale(array $values, int $index){
        $this->infoGenerale[$index] = $values;
        return $this;
    }

    public function getAnnonces(): array
    {
        return $this->Annonces;
    }

    public function setAnnonces(?array $Annonces): self
    {
        array_push($this->Annonces , $Annonces);
        return $this;
    }

    public function deleteAnnonce(?int $key): self
    {

        unset($this->Annonces[$key]);
        $this->Annonces = array_values($this->Annonces);
        return $this;

    }

    public function updateAnnonce(array $values, int $index){
        $this->Annonces[$index] = $values;
        return $this;
    }

}
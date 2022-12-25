<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: PublicationRepository::class)]
#[ORM\Table(name: 'publications')]
#[HasLifecycleCallbacks]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $publisher = null;


    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ["default" => "CURRENT_TIMESTAMP"])]
    private ?\DateTimeImmutable $createdAt = null;


    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $comments = [];

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $likes = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $link = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
    #[ORM\PrePersist]
    public function updateTimestamp()
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getComments(): array
    {
        return $this->comments;
    }

    public function setComments(?array $comments): self
    {
        array_push($this->comments, $comments);

        return $this;
    }

    public function getLikes(): array
    {
        return $this->likes;
    }

    public function setLikes(?array $likes): self
    {
        array_push($this->likes, $likes);

        return $this;
    }
    public function unlike(?String $user): self
    {
        //On supprime juste une l'identifiant 
        $i = 0;
        foreach ($this->likes as $like) {
            if ($like['liker'] == $user) {
                unset($this->likes[$i]);
            }
            $i++;
        }
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }
}

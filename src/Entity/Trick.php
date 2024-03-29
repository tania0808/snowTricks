<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 55, unique: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(fetch: 'EAGER', inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'create')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[Gedmo\Timestampable(on: 'update')]
    private \DateTimeImmutable $updatedAt;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, cascade: ['remove'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Media::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private Collection $media;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Media::class, cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private Collection $videos;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    public function getVideos(): Collection
    {
        // Use a Criteria object to filter the collection based on the 'type' property
        $criteria = Criteria::create()->where(Criteria::expr()->eq('type', 'video'));

        // Apply the criteria and return the filtered collection
        return $this->videos->matching($criteria);
    }
    public function getImages(): Collection
    {
        // Use a Criteria object to filter the collection based on the 'type' property
        $criteria = Criteria::create()->where(Criteria::expr()->eq('type', 'image'));

        // Apply the criteria and return the filtered collection
        return $this->videos->matching($criteria);
    }

    public function setVideos(array $videos): void
    {
        $this->videos = new ArrayCollection($videos);

        foreach ($videos as $video) {
            $video->setType('video');
            $video->setTrick($this);
        }
    }

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->media = new ArrayCollection();
        $this->videos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->createdAt = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updatedAt = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media[] = $medium;
            $medium->setTrick($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getTrick() === $this) {
                $medium->setTrick(null);
            }
        }

        return $this;
    }

    public function getMainMedia()
    {
        return $this->media->filter(function (Media $media) {
            return 'image' === $media->getType();
        })->first();
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }
}

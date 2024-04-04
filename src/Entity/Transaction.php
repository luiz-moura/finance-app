<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('transaction')]
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('transaction')]
    private $title;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Groups('transaction')]
    private $value;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 10)]
    #[ORM\Column(type: 'string', length: 45)]
    #[Groups('transaction')]
    private $type;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private $image;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'transactions')]
    #[Groups('transaction')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function created_at(): void
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle($title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getValue(): ?string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage($image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImageDir(): ?string
    {
        $package = new Package(new EmptyVersionStrategy());

        return $package->getUrl("uploads/{$this->getImage()}");
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt->format('Y-m-d H:i:s');
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        $this->categories->add($category);

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->getId(),
            'title'         => $this->getTitle(),
            'value'         => $this->getValue(),
            'type'          => $this->getType(),
            'image'         => $this->getImage(),
            'image_url'     => $this->getImageDir(),
            'created_at'    => $this->getCreatedAt(),
            'categories'    => $this->getCategories()->map(fn ($cat) => $cat->toArray())->toArray(),
        ];
    }
}

<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Transaction
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private $value;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 10)]
    #[ORM\Column(type: 'string', length: 45)]
    private $type;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'transactions')]
    private $categories;

    #[ORM\PrePersist]
    public function createdAt(): void
    {
        $this->createdAt = new DateTime();
    }

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getValue(): ?string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return date_format($this->createdAt, 'Y-m-d H:i:s');
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

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
            'created_at'    => $this->getCreatedAt(),
            'categories'    => $this->getCategories()->map(fn ($cat) => $cat->toArray())->toArray(),
        ];
    }
}

<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BudgetRepository;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private $value;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    private $Category;

    #[ORM\PrePersist]
    public function createdAt(): void
    {
        $this->createdAt = new DateTime();
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

    public function getValue(): ?string
    {
        return 'R$ ' . number_format($this->value, 2, ',', '.');
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return date_format($this->createdAt, 'Y-m-d H:i:s');
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'value'         => $this->getValue(),
            'category'      => $this->getCategory()->toArray(),
            'category_id'   => $this->getCategory()->getId(),
            'created_at'    => $this->getCreatedAt(),
        ];
    }
}

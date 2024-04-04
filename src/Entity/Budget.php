<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BudgetRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BudgetRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['name'], message: 'This name is already in use.')]
class Budget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups('budget', 'category')]
    private $id;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups('budget', 'category')]
    private $name;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Groups('budget', 'category')]
    private $value;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('budget', 'category')]
    private $category;

    #[ORM\PrePersist]
    public function created_at(): void
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

    public function setName($name): self
    {
        $this->name = $name;

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

    public function getCreatedAt(): ?string
    {
        return date_format($this->createdAt, 'Y-m-d H:i:s');
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $Category): self
    {
        $this->category = $Category;

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

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
    private ?int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups('budget', 'category')]
    private ?string $name;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Groups('budget', 'category')]
    private ?float $value;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[Assert\NotBlank]
    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'budgets')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups('budget', 'category')]
    private ?Category $category;

    public function __construct()
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value
            ? sprintf('R$ %s', number_format($this->value, 2, ',', '.'))
            : null;
    }

    public function setValue(?float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt
            ? date_format($this->createdAt, 'Y-m-d H:i:s')
            : null;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}

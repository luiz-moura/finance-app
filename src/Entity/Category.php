<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['name'], message: 'This name is already in use.')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['category', 'transaction', 'budget'])]
    private ?int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['category', 'transaction', 'budget'])]
    private ?string $name;

    #[Assert\NotBlank]
    #[Assert\CssColor]
    #[ORM\Column(type: 'string', length: 10)]
    #[Groups('category')]
    private ?string $background;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\ManyToMany(targetEntity: Transaction::class, mappedBy: 'categories')]
    private Collection $transactions;

    #[ORM\OneToMany(targetEntity: Budget::class, mappedBy: 'category', orphanRemoval: true)]
    private Collection $budgets;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->budgets = new ArrayCollection();
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

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground(?string $background): self
    {
        $this->background = $background;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt
            ? date_format($this->createdAt, 'Y-m-d H:i:s')
            : null;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function getBudgets(): Collection
    {
        return $this->budgets;
    }
}

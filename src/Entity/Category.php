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
    private $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(['category', 'transaction', 'budget'])]
    private $name;

    #[Assert\NotBlank]
    #[Assert\CssColor]
    #[ORM\Column(type: 'string', length: 10)]
    #[Groups('category')]
    private $background;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToMany(targetEntity: Transaction::class, mappedBy: 'categories')]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Budget::class, orphanRemoval: true)]
    private Collection $budgets;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
        $this->budgets = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBackground(): ?string
    {
        return $this->background;
    }

    public function setBackground($background): self
    {
        $this->background = $background;

        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return date_format($this->createdAt, 'Y-m-d H:i:s');
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        $this->transactions->add($transaction);
        $transaction->addCategory($this);

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        $this->transactions->removeElement($transaction);
        $transaction->removeCategory($this);

        return $this;
    }

    public function getBudgets(): Collection
    {
        return $this->budgets;
    }

    public function addBudget(Budget $budget): self
    {
        $this->budgets->add($budget);
        $budget->setCategory($this);

        return $this;
    }

    public function removeBudget(Budget $budget): self
    {
        $this->budgets->removeElement($budget);
        $budget->setCategory(null);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->getId(),
            'name'          => $this->getName(),
            'background'    => $this->getBackground(),
            'created_at'    => $this->getCreatedAt(),
        ];
    }
}

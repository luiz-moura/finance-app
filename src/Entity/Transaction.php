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
    private ?int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    #[ORM\Column(type: 'string', length: 255)]
    #[Groups('transaction')]
    private ?string $title;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    #[Groups('transaction')]
    private ?float $value;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 10)]
    #[ORM\Column(type: 'string', length: 45)]
    #[Groups('transaction')]
    private ?string $type;

    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['png', 'jpg'],
        mimeTypesMessage: 'Please upload a valid image',
        disallowEmptyMessage: true
    )]
    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $image;

    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'transactions')]
    #[Groups('transaction')]
    private Collection $categories;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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

    public function setTitle(?string $title): self
    {
        $this->title = $title;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getImageDir(): ?string
    {
        $package = new Package(new EmptyVersionStrategy());

        return $package->getUrl("uploads/{$this->getImage()}");
    }

    public function getCreated_at(): ?string
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
}

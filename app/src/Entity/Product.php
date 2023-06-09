<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenAPi\Attributes as OA;


#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["default"])]
    #[OA\Property(default: 1)]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["default", "create"])]
    #[OA\Property(default: "Item 3000")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["default", "create"])]
    #[OA\Property(default: "Best item in this shop")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(["default", "create"])]
    #[OA\Property(default: "https://path/to/image.png")]
    private ?string $photo = null;

    #[ORM\Column]
    #[Groups(["default", "create"])]
    #[OA\Property(default: 13.37)]
    private ?float $price = null;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'products')]
    private Collection $orders;

    #[ORM\ManyToMany(targetEntity: Cart::class, mappedBy: 'products')]
    private Collection $carts;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->carts = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->addProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeProduct($this);
        }

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'photo' => $this->photo,
            'price' => $this->price,
        );
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts->add($cart);
            $cart->addProduct($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            $cart->removeProduct($this);
        }

        return $this;
    }
}

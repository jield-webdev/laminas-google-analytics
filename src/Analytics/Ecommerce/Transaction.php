<?php

namespace LaminasGoogleAnalytics\Analytics\Ecommerce;

class Transaction
{
    protected ?string $affiliation = null;

    protected ?float $tax = null;

    protected ?float $shipping = null;

    protected ?string $city = null;

    protected ?string $state = null;

    protected ?string $country = null;

    protected array $items = [];

    public function __construct(protected int $id, protected float $total)
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Transaction
    {
        $this->id = $id;
        return $this;
    }

    public function getAffiliation(): ?string
    {
        return $this->affiliation;
    }

    public function setAffiliation(?string $affiliation): Transaction
    {
        $this->affiliation = $affiliation;
        return $this;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): Transaction
    {
        $this->total = $total;
        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(?float $tax): Transaction
    {
        $this->tax = $tax;
        return $this;
    }

    public function getShipping(): ?float
    {
        return $this->shipping;
    }

    public function setShipping(?float $shipping): Transaction
    {
        $this->shipping = $shipping;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): Transaction
    {
        $this->city = $city;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): Transaction
    {
        $this->state = $state;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): Transaction
    {
        $this->country = $country;
        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): Transaction
    {
        $this->items = $items;
        return $this;
    }

    public function addItem(Item $item): Transaction
    {
        $sku = $item->getSku();
        if (array_key_exists($sku, $this->items)) {
            $quantity = $this->items[$sku]->getQuantity() + $item->getQuantity();

            $this->items[$sku]->setQuantity($quantity);
        } else {
            $this->items[$sku] = $item;
        }

        return $this;
    }
}

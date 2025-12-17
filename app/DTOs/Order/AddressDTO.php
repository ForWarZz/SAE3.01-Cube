<?php

namespace App\DTOs\Order;

use App\Models\Address;

class AddressDTO
{
    public function __construct(
        public string $name,
        public string $street,
        public string $city,
    ) {}

    public static function fromModel(Address $address): self
    {
        return new self(
            name: $address->prenom_adresse.' '.$address->nom_adresse,
            street: $address->num_voie_adresse.' '.$address->rue_adresse,
            city: ($address->city->cp_ville ?? '').' '.($address->city->nom_ville ?? ''),
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'street' => $this->street,
            'city' => $this->city,
        ];
    }
}

<?php


namespace App\ResourcePlanning\Application\GetResourcesForProduct;

use PASVL\Validation\ValidatorBuilder;

final class GetResourcesForProductInput
{
    private $productCode;
    private $productGroup;

    private function validate($order)
    {
        $pattern = [
            "product" => [
                "product_code" => ":string",
                "product_group" => ":string?"
            ]
        ];

        $validator = ValidatorBuilder::forArray($pattern)->build();
        $validator->validate($order);
    }

    public function __construct($input)
    {
        $this->validate($input);
        $this->productCode = $input["product"]["product_code"];
        $this->productGroup = $input["product"]["product_group"];
    }

    public function productCode(): string
    {
        return $this->productCode;
    }

    public function productGroup(): ?string
    {
        return $this->productGroup;
    }

}

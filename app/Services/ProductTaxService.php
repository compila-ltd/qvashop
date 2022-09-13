<?php

namespace App\Services;

use App\Models\ProductTax;

class ProductTaxService
{
    public function store(array $data)
    {
        $collection = collect($data);

        if ($collection['tax_id']) {
            foreach ($collection['tax_id'] as $key => $val) {
                $product_tax = new ProductTax();
                $product_tax->tax_id = $val;
                $product_tax->product_id = $collection['product_id'];
                $product_tax->tax = $collection['tax'][$key];
                $product_tax->tax_type = $collection['tax_type'][$key];
                $product_tax->save();
            }
        }

    }

    public function product_duplicate_store($product_taxes , $product_new)
    {
         foreach ($product_taxes as $key => $tax) {
            $product_tax = new ProductTax;
            $product_tax->product_id = $product_new->id;
            $product_tax->tax_id = $tax->tax_id;
            $product_tax->tax = $tax->tax;
            $product_tax->tax_type = $tax->tax_type;
            $product_tax->save();
        }
    }

}

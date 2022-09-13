<?php

namespace App\Services;

use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;

class ProductFlashDealService
{
    public function store(array $data, Product $product)
    {
        $collection = collect($data);

        if ($collection['flash_deal_id']) {
            $flash_deal_product = FlashDealProduct::firstOrNew([
                'flash_deal_id' => $collection['flash_deal_id'], 
                'product_id' => $product->id]
                );
            $flash_deal_product->flash_deal_id = $collection['flash_deal_id'];
            $flash_deal_product->product_id = $product->id;
            $flash_deal_product->save();

            $flash_deal = FlashDeal::findOrFail($collection['flash_deal_id']);
            $product->discount = $collection['flash_discount'];
            $product->discount_type = $collection['flash_discount_type'];
            $product->discount_start_date = $flash_deal->start_date;
            $product->discount_end_date   = $flash_deal->end_date;
            $product->save();
        }

    }

}

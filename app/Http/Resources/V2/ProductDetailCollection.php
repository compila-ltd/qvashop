<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Models\Review;
use App\Models\Attribute;


class ProductDetailCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $precision = 2;
                $calculable_price = home_discounted_base_price($data, false);
                $calculable_price = number_format($calculable_price, $precision, '.', '');
                $calculable_price = floatval($calculable_price);
                // $calculable_price = round($calculable_price, 2);
                $photo_paths = get_images_path($data->photos);

                $photos = [];


                if (!empty($photo_paths)) {
                    for ($i = 0; $i < count($photo_paths); $i++) {
                        if ($photo_paths[$i] != "" ) {
                            $item = array();
                            $item['variant'] = "";
                            $item['path'] = $photo_paths[$i];
                            $photos[]= $item;
                        }

                    }

                }

                foreach ($data->stocks as $stockItem){
                    if($stockItem->image != null && $stockItem->image != ""){
                        $item = array();
                        $item['variant'] = $stockItem->variant;
                        $item['path'] = uploaded_asset($stockItem->image) ;
                        $photos[]= $item;
                    }
                }

                $brand = [
                    'id'=> 0,
                    'name'=> "",
                    'logo'=> "",
                ];

                if($data->brand != null) {
                    $brand = [
                        'id'=> $data->brand->id,
                        'name'=> $data->brand->getTranslation('name'),
                        'logo'=> uploaded_asset($data->brand->logo),
                    ];
                }


                return [
                    'id' => (integer)$data->id,
                    'name' => $data->getTranslation('name'),
                    'added_by' => $data->added_by,
                    'seller_id' => $data->user->id,
                    'shop_id' => $data->added_by == 'admin' ? 0 : $data->user->shop->id,
                    'shop_name' => $data->added_by == 'admin' ? translate('In House Product') : $data->user->shop->name,
                    'shop_logo' => $data->added_by == 'admin' ? uploaded_asset(get_setting('header_logo')) : uploaded_asset($data->user->shop->logo)??"",
                    'photos' => $photos,
                    'thumbnail_image' => uploaded_asset($data->thumbnail_img),
                    'tags' => explode(',', $data->tags),
                    'price_high_low' => (double)explode('-', home_discounted_base_price($data, false))[0] == (double)explode('-', home_discounted_price($data, false))[1] ? format_price((double)explode('-', home_discounted_price($data, false))[0]) : "From " . format_price((double)explode('-', home_discounted_price($data, false))[0]) . " to " . format_price((double)explode('-', home_discounted_price($data, false))[1]),
                    'choice_options' => $this->convertToChoiceOptions(json_decode($data->choice_options)),
                    'colors' => json_decode($data->colors),
                    'has_discount' => home_base_price($data, false) != home_discounted_base_price($data, false),
                    'discount'=> "-".discount_in_percentage($data)."%",
                    'stroked_price' => home_base_price($data),
                    'main_price' => home_discounted_base_price($data),
                    'calculable_price' => $calculable_price,
                    'currency_symbol' => currency_symbol(),
                    'current_stock' => (integer)$data->stocks->first()->qty,
                    'unit' => $data->unit,
                    'rating' => (double)$data->rating,
                    'rating_count' => (integer)Review::where(['product_id' => $data->id])->count(),
                    'earn_point' => (double)$data->earn_point,
                    'description' => $data->getTranslation('description'),
                    'video_link' => $data->video_link != null ?  $data->video_link : "",
                    'brand' => $brand,
                    'link' => route('product', $data->slug)
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }

    protected function convertToChoiceOptions($data)
    {
        $result = array();
//        if($data) {
        foreach ($data as $key => $choice) {
            $item['name'] = $choice->attribute_id;
            $item['title'] = Attribute::find($choice->attribute_id)->getTranslation('name');
            $item['options'] = $choice->values;
            array_push($result, $item);
        }
//        }
        return $result;
    }

    protected function convertPhotos($data)
    {
        $result = array();
        foreach ($data as $key => $item) {
            array_push($result, uploaded_asset($item));
        }
        return $result;
    }
}

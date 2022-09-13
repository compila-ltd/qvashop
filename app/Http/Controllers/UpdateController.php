<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Artisan;
use App\Models\Upload;
use App\Models\Product;
use App\Models\Tax;
use App\Models\ProductTax;
use App\Models\Seller;
use App\Models\SellerPackage;
use App\Models\SellerWithdrawRequest;
use App\Models\Shop;
use App\Models\User;
use Schema;
use Str;
use ZipArchive;

class UpdateController extends Controller
{
    public function step0(Request $request)
    {
        if (env('DEMO_MODE') == 'On') {
            flash(translate('This action is disabled in demo mode'))->error();
            return back();
        }
        if ($request->has('update_zip')) {
            if (class_exists('ZipArchive')) {
                // Create update directory.
                $dir = 'updates';
                if (!is_dir($dir))
                    mkdir($dir, 0777, true);

                $path = Upload::findOrFail($request->update_zip)->file_name;

                //Unzip uploaded update file and remove zip file.
                $zip = new ZipArchive;
                $res = $zip->open(base_path('public/' . $path));

                if ($res === true) {
                    $res = $zip->extractTo(base_path());
                    $zip->close();
                } else {
                    flash(translate('Could not open the updates zip file.'))->error();
                    return back();
                }

                return redirect()->route('update.step1');
            } else {
                flash(translate('Please enable ZipArchive extension.'))->error();
            }
        } else {
            return view('update.step0');
        }
    }

    public function step1()
    {
        if (get_setting('current_version') == '6.2.0') {
            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.1.5') {
            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.1.4') {
            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.1.3') {
            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.1.2') {
            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.1.1') {
            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.1') {
            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '6.0') {
            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '5.5.7') {
            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        }
        if (get_setting('current_version') == '5.5.6') {
            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } elseif (get_setting('current_version') == '5.5.5') {
            $sql_path = base_path('sqlupdates/v556.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } elseif (get_setting('current_version') == '5.5.4') {
            $sql_path = base_path('sqlupdates/v555.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v556.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } elseif (get_setting('current_version') == '5.5.3') {
            $sql_path = base_path('sqlupdates/v554.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v555.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v556.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } elseif (get_setting('current_version') == '5.5.2') {
            $sql_path = base_path('sqlupdates/v553.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v554.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v555.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v556.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } elseif (get_setting('current_version') == '5.5.1') {
            $sql_path = base_path('sqlupdates/v552.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v553.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v554.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v555.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v556.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } elseif (get_setting('current_version') == '5.5') {
            $sql_path = base_path('sqlupdates/v551.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v552.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v553.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v554.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v555.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v556.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v557.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v60.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v61.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v611.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v612.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v613.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v614.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v615.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v620.sql');
            DB::unprepared(file_get_contents($sql_path));

            $sql_path = base_path('sqlupdates/v630.sql');
            DB::unprepared(file_get_contents($sql_path));

            return redirect()->route('update.step2');
        } else {
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
            $newRouteServiceProvier      = base_path('app/Providers/RouteServiceProvider.txt');
            copy($newRouteServiceProvier, $previousRouteServiceProvier);

            return view('update.done');
        }
    }

    public function step2()
    {
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        $this->setAdmnRole();
        $this->convertSellerIntoShop();
        $this->convertSellerIntoUser();
        $this->convertSellerPackageIntoShop();
        $this->convertTrasnalations();
        // $this->convertColorsName();

        $previousRouteServiceProvier = base_path('app/Providers/RouteServiceProvider.php');
        $newRouteServiceProvier      = base_path('app/Providers/RouteServiceProvider.txt');
        copy($newRouteServiceProvier, $previousRouteServiceProvier);

        return view('update.done');
    }

    public function setAdmnRole()
    {
        $admin_user = User::where('user_type', 'admin')->first();
        $admin_user->assignRole(['Super Admin']);
    }

    public function convertSellerIntoShop()
    {
        $sellers = Seller::all();

        foreach ($sellers as $seller) {
            $shop = Shop::where('user_id', $seller->user_id)->first();
            if ($shop) {
                if (!$shop->rating) {
                    $shop->rating = $seller->rating;
                    $shop->num_of_reviews = $seller->num_of_reviews;
                }
                if (!$shop->num_of_sale) {
                    $shop->num_of_sale = $seller->num_of_sale;
                }
                if (!$shop->seller_package_id) {
                    $shop->seller_package_id = $seller->seller_package_id;
                    $shop->product_upload_limit = $seller->product_upload_limit;
                    $shop->package_invalid_at = $seller->invalid_at;
                }
                if ($shop->admin_to_pay == 0) {
                    $shop->admin_to_pay = $seller->admin_to_pay;
                }
                $shop->verification_status = $seller->verification_status;
                $shop->verification_info = $seller->verification_info;
                $shop->cash_on_delivery_status = $seller->cash_on_delivery_status;
                if (!$shop->bank_name) {
                    $shop->bank_name = $seller->bank_name;
                    $shop->bank_acc_name = $seller->bank_acc_name;
                    $shop->bank_acc_no = $seller->bank_acc_no;
                    $shop->bank_routing_no = $seller->bank_routing_no;
                    $shop->bank_payment_status = $seller->bank_payment_status;
                }

                $shop->save();
            }
        }
    }

    public function convertSellerIntoUser()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::all();

        foreach ($seller_withdraw_requests as $seller_withdraw_request) {
            $seller = Seller::where('id', $seller_withdraw_request->user_id)->first();
            if ($seller) {
                $seller_withdraw_request->user_id = $seller->user_id;

                $seller_withdraw_request->save();
            }
        }
    }

    public function convertSellerPackageIntoShop()
    {
        if (Schema::hasTable('seller_packages')) {
            $shops = Shop::all();
            foreach ($shops as $shop) {
                $seller_package = SellerPackage::where('id', $shop->seller_package_id)->first();
                if ($seller_package) {
                    $shop->product_upload_limit = $seller_package->product_upload_limit;

                    $shop->save();
                }
            }
        }
    }

    public function convertTaxes()
    {
        $tax = Tax::first();

        foreach (Product::all() as $product) {
            $product_tax = new ProductTax;
            $product_tax->product_id = $product->id;
            $product_tax->tax_id = $tax->id;
            $product_tax->tax = $product->tax;
            $product_tax->tax_type = $product->tax_type;
            $product_tax->save();
        }
    }

    public function convertTrasnalations()
    {
        foreach (\App\Models\Translation::all() as $translation) {
            $lang_key = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(' ', '_', strtolower($translation->lang_key)));
            $translation->lang_key = $lang_key;
            $translation->save();
        }
    }

    public function convertColorsName()
    {
        foreach (\App\Models\Color::all() as $color) {
            $color->name = Str::replace(' ', '', $color->name);
            $color->save();
        }
    }

    public function convertRatingAndSales()
    {
        foreach (\App\Models\Seller::all() as $seller) {
            $total = 0;
            $rating = 0;
            $num_of_sale = 0;
            try {
                foreach ($seller->user->products as $seller_product) {
                    $total += $seller_product->reviews->count();
                    $rating += $seller_product->reviews->sum('rating');
                    $num_of_sale += $seller_product->num_of_sale;
                }
                if ($total > 0) {
                    $seller->rating = $rating / $total;
                    $seller->num_of_reviews = $total;
                }
                $seller->num_of_sale = $num_of_sale;
                $seller->save();
            } catch (\Exception $e) {
            }
        }
    }
}

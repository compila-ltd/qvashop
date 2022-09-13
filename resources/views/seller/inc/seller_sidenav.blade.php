<div class="aiz-sidebar-wrap">
    <div class="aiz-sidebar left c-scrollbar">
        <div class="aiz-side-nav-logo-wrap">
            <div class="d-block text-center my-3">
                @if (optional(Auth::user()->shop)->logo != null)
                    <img class="mw-100 mb-3" src="{{ uploaded_asset(optional(Auth::user()->shop)->logo) }}" class="brand-icon"
                        alt="{{ get_setting('site_name') }}">
                @else
                    <img class="mw-100 mb-3" src="{{ uploaded_asset(get_setting('header_logo')) }}" class="brand-icon"
                        alt="{{ get_setting('site_name') }}">
                @endif
                <h3 class="fs-16  m-0 text-primary">{{ optional(Auth::user()->shop)->name }}</h3>
                <p class="text-primary">{{ Auth::user()->email }}</p>
            </div>
        </div>
        <div class="aiz-side-nav-wrap">
            <div class="px-20px mb-3">
                <input class="form-control bg-soft-secondary border-0 form-control-sm text-white" type="text"
                    name="" placeholder="{{ translate('Search in menu') }}" id="menu-search"
                    onkeyup="menuSearch()">
            </div>
            <ul class="aiz-side-nav-list" id="search-menu">
            </ul>
            <ul class="aiz-side-nav-list" id="main-menu" data-toggle="aiz-side-menu">
                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.dashboard') }}" class="aiz-side-nav-link">
                        <i class="las la-home aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Dashboard') }}</span>
                    </a>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="#" class="aiz-side-nav-link">
                        <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Products') }}</span>
                        <span class="aiz-side-nav-arrow"></span>
                    </a>
                    <!--Submenu-->
                    <ul class="aiz-side-nav-list level-2">
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.products') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['seller.products', 'seller.products.create', 'seller.products.edit']) }}">
                                <span class="aiz-side-nav-text">{{ translate('Products') }}</span>
                            </a>
                        </li>

                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.product_bulk_upload.index') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['product_bulk_upload.index']) }}">
                                <span class="aiz-side-nav-text">{{ translate('Product Bulk Upload') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.digitalproducts') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['seller.digitalproducts', 'seller.digitalproducts.create', 'seller.digitalproducts.edit']) }}">
                                <span class="aiz-side-nav-text">{{ translate('Digital Products') }}</span>
                            </a>
                        </li>
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('seller.reviews') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['seller.reviews']) }}">
                                <span class="aiz-side-nav-text">{{ translate('Product Reviews') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.uploaded-files.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['seller.uploaded-files.index', 'seller.uploads.create']) }}">
                        <i class="las la-folder-open aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Uploaded Files') }}</span>
                    </a>
                </li>
                @if (addon_is_activated('seller_subscription'))
                    <li class="aiz-side-nav-item">
                        <a href="#" class="aiz-side-nav-link">
                            <i class="las la-shopping-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Package') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('seller.seller_packages_list') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Packages') }}</span>
                                </a>
                            </li>

                            <li class="aiz-side-nav-item">
                                <a href="{{ route('seller.packages_payment_list') }}" class="aiz-side-nav-link">
                                    <span class="aiz-side-nav-text">{{ translate('Purchase Packages') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (get_setting('coupon_system') == 1)
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('seller.coupon.index') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['seller.coupon.index', 'seller.coupon.create', 'seller.coupon.edit']) }}">
                            <i class="las la-bullhorn aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Coupon') }}</span>
                        </a>
                    </li>
                @endif
                @if (addon_is_activated('wholesale') && get_setting('seller_wholesale_product') == 1)
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('seller.wholesale_products_list') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['wholesale_product_create.seller', 'wholesale_product_edit.seller']) }}">
                            <i class="las la-luggage-cart aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Wholesale Products') }}</span>
                        </a>
                    </li>
                @endif
                @if (addon_is_activated('auction') && get_setting('seller_auction_product') == 1)
                    <li class="aiz-side-nav-item">
                        <a href="javascript:void(0);" class="aiz-side-nav-link">
                            <i class="las la-gavel aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Auction') }}</span>
                            <span class="aiz-side-nav-arrow"></span>
                        </a>
                        <ul class="aiz-side-nav-list level-2">
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('auction_products.seller.index') }}"
                                    class="aiz-side-nav-link {{ areActiveRoutes(['auction_products.seller.index', 'auction_product_create.seller', 'auction_product_edit.seller', 'product_bids.seller']) }}">
                                    <span
                                        class="aiz-side-nav-text">{{ translate('All Auction Products') }}</span>
                                </a>
                            </li>
                            <li class="aiz-side-nav-item">
                                <a href="{{ route('auction_products_orders.seller') }}"
                                    class="aiz-side-nav-link {{ areActiveRoutes(['auction_products_orders.seller']) }}">
                                    <span
                                        class="aiz-side-nav-text">{{ translate('Auction Product Orders') }}</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if (addon_is_activated('pos_system'))
                    @if (get_setting('pos_activation_for_seller') != null && get_setting('pos_activation_for_seller') != 0)
                        <li class="aiz-side-nav-item">
                            <a href="{{ route('poin-of-sales.seller_index') }}"
                                class="aiz-side-nav-link {{ areActiveRoutes(['poin-of-sales.seller_index']) }}">
                                <i class="las la-fax aiz-side-nav-icon"></i>
                                <span class="aiz-side-nav-text">{{ translate('POS Manager') }}</span>
                            </a>
                        </li>
                    @endif
                @endif
                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.orders.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['seller.orders.index', 'seller.orders.show']) }}">
                        <i class="las la-money-bill aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Orders') }}</span>
                    </a>
                </li>
                @if (addon_is_activated('refund_request'))
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('vendor_refund_request') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['vendor_refund_request', 'reason_show']) }}">
                            <i class="las la-backward aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Received Refund Request') }}</span>
                        </a>
                    </li>
                @endif


                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.shop.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['seller.shop.index']) }}">
                        <i class="las la-cog aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Shop Setting') }}</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.payments.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['seller.payments.index']) }}">
                        <i class="las la-history aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Payment History') }}</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.money_withdraw_requests.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['seller.money_withdraw_requests.index']) }}">
                        <i class="las la-money-bill-wave-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Money Withdraw') }}</span>
                    </a>
                </li>

                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.commission-history.index') }}" class="aiz-side-nav-link">
                        <i class="las la-file-alt aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Commission History') }}</span>
                    </a>
                </li>

                @if (get_setting('conversation_system') == 1)
                    @php
                        $conversation = \App\Models\Conversation::where('sender_id', Auth::user()->id)
                            ->where('sender_viewed', 0)
                            ->get();
                    @endphp
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('seller.conversations.index') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['seller.conversations.index', 'seller.conversations.show']) }}">
                            <i class="las la-comment aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Conversations') }}</span>
                            @if (count($conversation) > 0)
                                <span class="badge badge-success">({{ count($conversation) }})</span>
                            @endif
                        </a>
                    </li>
                @endif

                @if (get_setting('product_query_activation') == 1)
                    <li class="aiz-side-nav-item">
                        <a href="{{ route('seller.product_query.index') }}"
                            class="aiz-side-nav-link {{ areActiveRoutes(['seller.product_query.index']) }}">
                            <i class="las la-question-circle aiz-side-nav-icon"></i>
                            <span class="aiz-side-nav-text">{{ translate('Product Queries') }}</span>

                        </a>
                    </li>
                @endif

                @php
                    $support_ticket = DB::table('tickets')
                        ->where('client_viewed', 0)
                        ->where('user_id', Auth::user()->id)
                        ->count();
                @endphp
                <li class="aiz-side-nav-item">
                    <a href="{{ route('seller.support_ticket.index') }}"
                        class="aiz-side-nav-link {{ areActiveRoutes(['seller.support_ticket.index']) }}">
                        <i class="las la-atom aiz-side-nav-icon"></i>
                        <span class="aiz-side-nav-text">{{ translate('Support Ticket') }}</span>
                        @if ($support_ticket > 0)
                            <span class="badge badge-inline badge-success">{{ $support_ticket }}</span>
                        @endif
                    </a>
                </li>

            </ul><!-- .aiz-side-nav -->
        </div><!-- .aiz-side-nav-wrap -->
    </div><!-- .aiz-sidebar -->
    <div class="aiz-sidebar-overlay"></div>
</div><!-- .aiz-sidebar -->

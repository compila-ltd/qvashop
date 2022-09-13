@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="align-items-center">
        <h1 class="h3">{{translate('All Notifications')}}</h1>
    </div>
</div>


<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <form class="" id="sort_customers" action="" method="GET">
                <div class="card-header row gutters-5">
                    <div class="col">
                        <h5 class="mb-0 h6">{{translate('Notifications')}}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($notifications as $notification)
                            @if($notification->type == 'App\Notifications\OrderNotification')
                                <li class="list-group-item d-flex justify-content-between align-items- py-3">
                                    <div class="media text-inherit">
                                        <div class="media-body">
                                            <p class="mb-1 text-truncate-2">
                                                {{ translate('Order code: ') }}
                                                <a href="{{route('all_orders.show', encrypt($notification->data['order_id']))}}">
                                                    {{$notification->data['order_code']}}
                                                </a>
                                                {{translate(' has been '. ucfirst(str_replace('_', ' ', $notification->data['status'])))}}
                                            </p>
                                            <small class="text-muted">
                                                {{ date("F j Y, g:i a", strtotime($notification->created_at)) }}
                                            </small>
                                        </div>
                                    </div>
                                </li>
                            @endif

                        @empty
                            <li class="list-group-item">
                                <div class="py-4 text-center fs-16">{{ translate('No notification found') }}</div>
                            </li>
                        @endforelse
                    </ul>
                    
                    {{ $notifications->links() }}
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Conversations')}}</h5>
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @forelse ($conversations as $key => $conversation)
                    @if ($conversation->receiver != null && $conversation->sender != null)
                        <li class="list-group-item px-0">
                            <div class="row gutters-10">
                                <div class="col-auto">
                                    <div class="media">
                                        <span class="avatar avatar-sm flex-shrink-0">
                                            @if (Auth::user()->id == $conversation->sender_id)
                                                <img @if ($conversation->receiver->avatar_original == null) src="{{ asset('assets/img/avatar-place.png') }}" @else src="{{ uploaded_asset($conversation->receiver->avatar_original) }}" @endif onerror="this.onerror=null;this.src='{{ asset('assets/img/avatar-place.png') }}';">
                                            @else
                                                <img @if ($conversation->sender->avatar_original == null) src="{{ asset('assets/img/avatar-place.png') }}" @else src="{{ uploaded_asset($conversation->sender->avatar_original) }}" @endif class="rounded-circle" onerror="this.onerror=null;this.src='{{ asset('assets/img/avatar-place.png') }}';">
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="col-auto col-lg-3">
                                    <p>
                                        @if (Auth::user()->id == $conversation->sender_id)
                                            <span class="fw-600">{{ $conversation->receiver->name }}</span>
                                        @else
                                            <span class="fw-600">{{ $conversation->sender->name }}</span>
                                        @endif
                                        <br>
                                        <span class="opacity-50">
                                            {{ date('h:i:m d-m-Y', strtotime($conversation->messages->last()->created_at)) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-12 col-lg">
                                    <div class="block-body">
                                        <div class="block-body-inner pb-3">
                                            <div class="row no-gutters">
                                                <div class="col">
                                                    <h6 class="mt-0">
                                                        <a href="{{ route('conversations.show', encrypt($conversation->id)) }}" class="text-dark fw-600">
                                                            {{ $conversation->title }}
                                                        </a>
                                                        @if ((Auth::user()->id == $conversation->sender_id && $conversation->sender_viewed == 0) || (Auth::user()->id == $conversation->receiver_id && $conversation->receiver_viewed == 0))
                                                            <span class="badge badge-inline badge-danger">{{ translate('New') }}</span>
                                                        @endif
                                                    </h6>
                                                </div>
                                            </div>
                                            <p class="mb-0 opacity-50">
                                                {{ $conversation->messages->last()->message }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif
                @empty
                    <div class="col">
                        <div class="text-center bg-white p-4 rounded shadow">
                            <img class="mw-100 h-200px" src="{{ asset('assets/img/nothing.svg') }}" alt="Image">
                            <h5 class="mb-0 h5 mt-3">{{ translate("There isn't anything added yet")}}</h5>
                        </div>
                    </div>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="aiz-pagination">
      	{{ $conversations->links() }}
    </div>

@endsection

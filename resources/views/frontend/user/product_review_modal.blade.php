<div class="modal-header">
    <h5 class="modal-title h6">{{translate('Review')}}</h5>
    <button type="button" class="close" data-dismiss="modal">
    </button>
</div>

@if($review == null)
    <form action="{{ route('reviews.store') }}" method="POST" >
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <div class="modal-body">
            <div class="form-group">
                <label class="opacity-60">{{ translate('Product')}}</label>
                <p>{{ $product->getTranslation('name') }}</p>
            </div>
            <div class="form-group">
                <label class="opacity-60">{{ translate('Rating')}}</label>
                <div class="rating rating-input">
                    <label>
                        <input type="radio" name="rating" value="1" required>
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="2">
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="3">
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="4">
                        <i class="las la-star"></i>
                    </label>
                    <label>
                        <input type="radio" name="rating" value="5">
                        <i class="las la-star"></i>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="opacity-60">{{ translate('Comment')}}</label>
                <textarea class="form-control" rows="4" name="comment" placeholder="{{ translate('Your review')}}" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-sm btn-primary">{{translate('Submit review')}}</button>
            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
        </div>
    </form>
@else
<li class="media list-group-item d-flex">
    <div class="media-body text-left">
        <div class="form-group">
            <label class="opacity-60">{{ translate('Rating')}}</label>
            <p class="rating rating-sm">
                @for ($i=0; $i < $review->rating; $i++)
                    <i class="las la-star active"></i>
                @endfor
                @for ($i=0; $i < 5-$review->rating; $i++)
                    <i class="las la-star"></i>
                @endfor
            </p>
        </div>
        <div class="form-group">
            <label class="opacity-60">{{ translate('Comment')}}</label>
            <p class="comment-text">
                {{ $review->comment }}
            </p>
        </div>
    </div>
</li>
@endif


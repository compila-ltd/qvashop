<a href="{{ route('compare') }}" class="d-flex align-items-center text-reset">
    <i class="la la-refresh la-2x"></i>
    <span class="flex-grow-1 ml-1">
        @if(Session::has('compare'))
        <span class="badge badge-primary badge-inline badge-pill">{{ count(Session::get('compare'))}}</span>
        @else
        <span class="badge badge-primary badge-inline badge-pill">0</span>
        @endif
        <span class="nav-box-text d-none d-xl-block">{{ translate('Compare')}}</span>
    </span>
</a>
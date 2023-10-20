<img src="{{ asset('assets/img/logo-qvashop.png') }}" height="40" style="display:inline-block;">
<p>{{ $content }}</p>
<p><b>{{ translate('Message') }}: </b>{{ $details }}</p>
<p>Para mas detalles, por favor continuar mediante el siguiente enlace: </p>
<a class="btn btn-primary btn-md" href="{{ $link }}">{{ translate('See Details') }}</a>

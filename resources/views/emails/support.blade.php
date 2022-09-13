<h1>{{ translate('Ticket') }}</h1>
<p>{{ $content }}</p>
<p><b>{{ translate('Sender') }}: </b>{{ $sender }}</p>
<p>
	<b>{{ translate('Details') }}:</b>
	<br>
	@php echo $details; @endphp
</p>
<a class="btn btn-primary btn-md" href="{{ $link }}">{{ translate('See ticket') }}</a>

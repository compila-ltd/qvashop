<!doctype html>
@if(\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@else
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@endif

<head>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="app-url" content="{{ getBaseURL() }}">
	<meta name="file-base-url" content="{{ getFileBaseURL() }}">

	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<title>{{ get_setting('website_name').' | '.get_setting('site_motto') }}</title>

	<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

	<!-- google font -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700">

	<!-- aiz core css -->
	<link rel="stylesheet" href="{{ asset('assets/css/vendors.css') }}">
	@if(\App\Models\Language::where('code', Session::get('locale', Config::get('app.locale')))->first()->rtl == 1)
	<link rel="stylesheet" href="{{ asset('assets/css/bootstrap-rtl.min.css') }}">
	@endif
	<link rel="stylesheet" href="{{ asset('assets/css/aiz-core.css') }}">

	<style>
		body {
			font-size: 12px;
		}
	</style>
	<script>
		var AIZ = AIZ || {};
		AIZ.local = {
			nothing_selected: "{!! translate('Nothing selected', null, true) !!}",
			nothing_found: "{!! translate('Nothing found', null, true) !!}",
			choose_file: "{{ translate('Choose file') }}",
			file_selected: "{{ translate('File selected') }}",
			files_selected: "{{ translate('Files selected') }}",
			add_more_files: "{{ translate('Add more files') }}",
			adding_more_files: "{{ translate('Adding more files') }}",
			drop_files_here_paste_or: "{{ translate('Drop files here, paste or') }}",
			browse: "{{ translate('Browse') }}",
			upload_complete: "{{ translate('Upload complete') }}",
			upload_paused: "{{ translate('Upload paused') }}",
			resume_upload: "{{ translate('Resume upload') }}",
			pause_upload: "{{ translate('Pause upload') }}",
			retry_upload: "{{ translate('Retry upload') }}",
			cancel_upload: "{{ translate('Cancel upload') }}",
			uploading: "{{ translate('Uploading') }}",
			processing: "{{ translate('Processing') }}",
			complete: "{{ translate('Complete') }}",
			file: "{{ translate('File') }}",
			files: "{{ translate('Files') }}",
		}
	</script>

</head>

<body class="">

	<div class="aiz-main-wrapper">
		@include('backend.inc.admin_sidenav')
		<div class="aiz-content-wrapper">
			@include('backend.inc.admin_nav')
			<div class="aiz-main-content">
				<div class="px-15px px-lg-25px">
					@yield('content')
				</div>
				<div class="bg-white text-center py-3 px-15px px-lg-25px mt-auto">
					<p class="mb-0">&copy; {{ get_setting('site_name') }} v{{ get_setting('current_version') }}</p>
				</div>
			</div>
		</div>
	</div>

	@yield('modal')

	<script src="{{ asset('assets/js/vendors.js') }}"></script>
	<script src="{{ asset('assets/js/aiz-core.js?v=202404041500') }}"></script>

	@yield('script')

	@include('frontend.flash')

	<script type="text/javascript">

		if ($('#lang-change').length > 0) {
			$('#lang-change .dropdown-menu a').each(function() {
				$(this).on('click', function(e) {
					e.preventDefault();
					var $this = $(this);
					var locale = $this.data('flag');
					$.post("{{ route('language.change') }}", {
							_token: '{{ csrf_token() }}',
							locale: locale
						},
						function(data) {
							location.reload();
						});

				});
			});
		}

		function menuSearch() {
			var filter, item;
			filter = $("#menu-search").val().toUpperCase();
			items = $("#main-menu").find("a");
			items = items.filter(function(i, item) {
				if ($(item).find(".aiz-side-nav-text")[0].innerText.toUpperCase().indexOf(filter) > -1 && $(item).attr('href') !== '#') {
					return item;
				}
			});

			if (filter !== '') {
				$("#main-menu").addClass('d-none');
				$("#search-menu").html('')
				if (items.length > 0) {
					for (i = 0; i < items.length; i++) {
						const text = $(items[i]).find(".aiz-side-nav-text")[0].innerText;
						const link = $(items[i]).attr('href');
						$("#search-menu").append(`<li class="aiz-side-nav-item"><a href="${link}" class="aiz-side-nav-link"><i class="las la-ellipsis-h aiz-side-nav-icon"></i><span>${text}</span></a></li`);
					}
				} else {
					$("#search-menu").html(`<li class="aiz-side-nav-item"><span	class="text-center text-muted d-block">{{ translate('Nothing Found') }}</span></li>`);
				}
			} else {
				$("#main-menu").removeClass('d-none');
				$("#search-menu").html('')
			}
		}
	</script>

</body>

</html>
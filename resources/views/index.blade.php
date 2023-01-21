<!DOCTYPE HTML>
<html lang="{{str_replace('_', '-', app()->getLocale())}}">
<head>
	<title>DD:A Builder</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="icon" type="image/png" href="/assets/images/tower/crystalCore.png">
	<link href="{{mix('assets/css/style.css')}}" rel="stylesheet">
</head>
<body>
	<div id="app"></div>
	<script>
		window.APP = {
			user: {!! auth()->user() ? json_encode(new \App\Http\Resources\UserResource(auth()->user())) : 'null' !!},
			supportedLocales: {!! json_encode(config('app.locales')) !!}
		};
	</script>
	<script src="{{mix('assets/js/main.js')}}"></script>
	@if (app()->isProduction())
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-39334248-36"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag() {dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-39334248-36');
	</script>
	@endif
</body>
</html>
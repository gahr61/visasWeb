<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="favicon.ico" />
	<title>Visas - Servicios premier</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
	<div id="root"></div>
	
	@viteReactRefresh
    @vite('resources/js/app.js')
	
</body>
</html>
<!DOCTYPE html>
<html>
<head>
	<title>Contacto desde página web</title>

	<style type="text/css">
		.container{
			width: 55%;
			text-align: left;
			margin-left: 22%;
			background: #fff;
			padding: 5px
		}

		@media (min-width: 1024px){
			.container{
				width: 35%;
				margin-left: 33%;
			}
		}


		@media (min-width: 320px) and (max-width: 767px){
			.container{
				width: 90%;
				margin-left: 5%; 
			}
		}

	</style>

</head>
<body>
	<div style="background: #f4f4f4; padding: 15px">
		<div class="container">
			<h2 style="text-align: center;">Contacto desde página web</h2>
			<p>
				--------------------------------------
			</p>
			<p>
				<span>Nombre: </span>
				<label>{{$data['cuerpo']['name']}}</label>
			</p>
			<p>
				<span>Email: </span>
				<label>{{$data['cuerpo']['email']}}</label>
			</p>
			<p>
				<span>Teléfono:</span>
				<label>{{$data['cuerpo']['phone']}}</label>
			</p>
			<p>
				<span>Mensaje:</span>
				<p>{{$data['cuerpo']['message']}}</p>
			</p>
			<br/>
			<p>
				--------------------------------------
			</p>
		</div>
	</div>
</body>
</html>
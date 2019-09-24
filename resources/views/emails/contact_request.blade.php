<!DOCTYPE html>

<html>

<body>


	<h3>el usuario <strong>{{$data["username"]}}(user_social_id {{$data["user_social_id"]}})</strong> tiene la siguiente consulta para hacerle:</h3>

	<p><strong>Mensaje:</strong> "{{$data["message"]}}"</p>

	<p><strong>Correo del Usuario:</strong> {{$data["email"]}} 
	<p><strong>Origen:</strong> {{$data["origin"]}}.</p>

</body>

</html>
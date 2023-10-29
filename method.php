<?php
// Definimos la codificación de los caracteres
define("DB_ENCODE", "utf8");
// Definimos una constante como nombre del proyecto
define("PRO_NOMBRE", "cc");
// Datos de producción, descomentar para pasar
define("DB_HOST", "localhost");
// Nombre de la base de datos
define("DB_NAME", "soa");
// Usuario de la base de datos
define("DB_USERNAME", "root");
// Contraseña del usuario de la base de datos
define("DB_PASSWORD", "");
///////////////////////////////////////////////////////////////////////////
// Conexión a la database utilizando mysqli
$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Comprobar si la conexión a la base de datos tuvo errores
if ($conexion->connect_error) {
	die("Falló conexión a la base de datos: " . $conexion->connect_error);
}
///////////////////////////////////////////////////////////////////////////
// Obtener el método de solicitud HTTP
$request_method = $_SERVER['REQUEST_METHOD'];

switch ($request_method) {
    case 'GET':

			if(isset($_GET['id'])) {
				$id = $_GET['id'];
				$sql = "SELECT blogs.id, blogs.title, blogs.content, blogs.author, blogs.publishedDate, blogs.img,
							 comments.id, comments.name, comments.email, comments.message, comments.idBlog
								FROM blogs
								INNER JOIN comments ON blogs.id = comments.idBlog
								WHERE blogs.id = ${id};
								";
		    // Ejecutar la consulta
		    $result = $conexion->query($sql);

		    // Comprobar si la consulta tuvo resultados
		    if ($result) {
		    	if ($result->num_rows > 0) {
			      // Iterar a través de los resultados y almacenarlos en un array
			      $results = [];
			      while ($row = $result->fetch_assoc()) {
			          $results[] = $row;
			      }
						// Assuming $results is an array containing your data
						$json_data = json_encode($results);

						// Set the appropriate Content-Type header to indicate that you are sending JSON
						header('Content-Type: application/json');

						// Output the JSON data
						echo $json_data;

		      } else {
		      	echo "No se encontraron registros.";
		    	}
		    } else {
		    	echo "Error en la consulta: " . $conexion->error;
		    }
		    break;
				
			} else {
				#echo "No parameter received.";
				// Consulta SQL para seleccionar todos los registros de la tabla "blogs"
		    $sql = "SELECT id, title, content, author, publishedDate, img FROM blogs";
		    // Ejecutar la consulta
		    $result = $conexion->query($sql);

		    // Comprobar si la consulta tuvo resultados
		    if ($result) {
		    	if ($result->num_rows > 0) {
			      // Iterar a través de los resultados y almacenarlos en un array
			      $results = [];
			      while ($row = $result->fetch_assoc()) {
			          $results[] = $row;
			      }
						// Assuming $results is an array containing your data
						$json_data = json_encode($results);

						// Set the appropriate Content-Type header to indicate that you are sending JSON
						header('Content-Type: application/json');

						// Output the JSON data
						echo $json_data;

		      } else {
		      	echo "No se encontraron registros.";
		    	}
		    } else {
		    	echo "Error en la consulta: " . $conexion->error;
		    }
		    break;
			}


      
		
		case 'POST':
			// Obtener los valores del formulario
			$name = $_POST['name'];
			$email = $_POST['email'];
			$message = $_POST['message'];
			$idBlog = $_POST['idBlog'];

			// Crear la consulta SQL para insertar datos en la base de datos
			$sql = "INSERT INTO `comments` (`name`, `email`, `message`, `idBlog`) VALUES ('$name', '$email', '$message', '$idBlog')";

			// Ejecutar la consulta
			$result = $conexion->query($sql);

			// Verificar si la consulta se ejecutó con éxito
			if ($result) {
			    // La inserción en la base de datos fue exitosa
			    $insertedId = $conexion->insert_id; // Obtener el ID del comentario insertado
			    echo "La inserción en la base de datos fue exitosa. ID del comentario: $insertedId";
			} else {
			    // Hubo un error en la inserción
			    echo "Error al insertar en la base de datos: " . $conexion->error;
			}
			break;


      
    default:
    	echo 'Método de solicitud no definido';
    	break;
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>


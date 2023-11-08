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
define("DB_PASSWORD", "root");
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

		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$sql = "SELECT blogs.id, blogs.title, blogs.content, blogs.author, blogs.publishedDate,
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
					//echo "No se encontraron registros.";
					// Consulta SQL para seleccionar todos los registros de la tabla "blogs"
					$sql = "SELECT id, title, content, author, publishedDate, img FROM blogs Where id = ${id}; ";
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
					$conexion->close();
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
			$conexion->close();

		}
		break;

	case 'POST':
		$name = $_POST['name'];
		$email = $_POST['email'];
		$message = $_POST['message'];
		$idBlog = $_POST['idBlog'];

		if (isset($name, $email, $message, $idBlog)) {
			//echo $name, $email, $message, $idBlog;
			$sql = "INSERT INTO comments (name, email, message, idBlog) VALUES ('$name', '$email', '$message', '$idBlog')";

			// Ejecutar la consulta
			$result = $conexion->query($sql);

			// Verificar si la consulta se ejecutó con éxito
			if ($result) {
				// La inserción en la base de datos fue exitosa
				$insertedId = $conexion->insert_id; // Obtener el ID del comentario insertado
				echo "Comentario añadido correctamente";
			} else {
				// Hubo un error en la inserción
				echo "Error al insertar en la base de datos: " . $conexion->error;
			}
			$conexion->close();
		} else {
			echo "no recibi parametros";
		}
		break;

	case 'PUT':
		// Procesar solicitud DELETE
		$data = json_decode(file_get_contents("php://input"));

		// Check if the commentId is provided
		if (isset($data->comment_id)) {
			$comment_id = $data->comment_id;
			$name = $data->name;
			$email = $data->email;
			$message = $data->message;

			$sql = "UPDATE comments SET name = '$name' , email = '$email' , message = '$message' WHERE comments.id = '$comment_id'";

			if ($conexion->query($sql) === TRUE) {
				echo "Comentario actualizado con éxito.";
			} else {
				echo "Error al actualizar comentario: " . $conexion->error;
			}
			$conexion->close();
		} else {
			// Handle the case where commentId is not provided
			echo ("comment information not provided");
		}

		break;
		case 'PATCH':
			$data = json_decode(file_get_contents("php://input"));

    // Check if the comment_id is provided
    if (isset($data->comment_id)) {
        $comment_id = $data->comment_id;

        // Check if editedFields is provided
        if (isset($data->editedFields)) {
            $editedFields = $data->editedFields;
            $updateFields = [];

            if (isset($editedFields->name)) {
                $name = $editedFields->name;
                $updateFields[] = "name = '$name'";
            }
            if (isset($editedFields->email)) {
                $email = $editedFields->email;
                $updateFields[] = "email = '$email'";
            }
            if (isset($editedFields->message)) {
                $message = $editedFields->message;
                $updateFields[] = "message = '$message'";
            }

            if (!empty($updateFields)) {
                $updateString = implode(', ', $updateFields);

                $sql = "UPDATE comments SET $updateString WHERE id = $comment_id";

                if ($conexion->query($sql) === TRUE) {
                    echo "Comentario actualizado con éxito.";
                } else {
                    echo "Error al actualizar comentario: " . $conexion->error;
                }
            } else {
                // Handle the case where no fields are provided for update
                echo "No fields to update.";
            }
        } else {
            // Handle the case where editedFields is not provided
            echo "Edited fields not provided.";
        }
    } else {
        // Handle the case where comment_id is not provided
        echo "Comment ID not provided.";
    }
    $conexion->close();
			// // Procesar solicitud PATCH
			// $data = json_decode(file_get_contents("php://input"));
	
			// // Check if the commentId is provided
			// if (isset($data->comment_id)) {
			// 	$comment_id = $data->comment_id;
			// 	$editedFields = $data['editedFields'];
			// 	$updateFields = [];
	
			// 	if (isset($editedFields->name)) {
			// 		$name = $editedFields->name;
			// 		$updateFields[] = "name = '$name'";
			// 	}
			// 	if (isset($editedFields->email)) {
			// 		$email = $editedFields->email;
			// 		$updateFields[] = "email = '$email'";
			// 	}
			// 	if (isset($editedFields->message)) {
			// 		$message = $editedFields->message;
			// 		$updateFields[] = "message = '$message'";
			// 	}
	
			// 	if (!empty($updateFields)) {
			// 		$updateString = implode(', ', $updateFields);
	
			// 		$sql = "UPDATE comments SET $updateString WHERE id = $comment_id";
	
			// 		if ($conexion->query($sql) === TRUE) {
			// 			echo "Comentario actualizado con éxito.";
			// 		} else {
			// 			echo "Error al actualizar comentario: " . $conexion->error;
			// 		}
			// 	} else {
			// 		// Handle the case where no fields are provided for update
			// 		echo "No fields to update.";
			// 	}
	
			// 	$conexion->close();
			// } else {
			// 	// Handle the case where commentId is not provided
			// 	echo "Comment ID not provided.";
			// }
	
			break;

	case 'DELETE':
		// Procesar solicitud DELETE
		$data = json_decode(file_get_contents("php://input"));

		// Check if the commentId is provided
		if (isset($data->comment_id)) {
			$comment_id = $data->comment_id;

			$sql = "DELETE FROM comments WHERE id = $comment_id";

			if ($conexion->query($sql) === TRUE) {
				echo "Registro eliminado con éxito.";
			} else {
				echo "Error al eliminar registro: " . $conexion->error;
			}
			$conexion->close();
		} else {
			// Handle the case where commentId is not provided
			echo ("comment information not provided");
		}

		break;




	default:
		echo 'Método de solicitud no definido';
		$conexion->close();
		break;
}

?>
<?php
session_start();

require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
if (!current_user_can('manage_options')) {
    wp_die('Acceso restringido solo a administradores.');
}

$conexion = new mysqli("mysql", "cliente", "cliente", "inscripciones_db");
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $conexion->prepare("DELETE FROM sugerencias WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    echo "<p style='color:red;'>Sugerencia eliminada.</p>";
}

$resultado = $conexion->query("SELECT * FROM sugerencias ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ver sugerencias</title>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background-color: #f0f0f0;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        form {
            margin: 0;
        }
        .delete-button {
            background-color: red;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .delete-button:hover {
            background-color: darkred;
        }
</style>
</head>
<body>

  <h1>Listado de sugerencias</h1>

<?php if ($resultado->num_rows > 0) { ?>
  <table>
    <thead>
       <tr>
         <th>ID</th>
         <th>Nombre</th>
         <th>Mensaje</th>
         <th>Acciones</th>
       </tr>
     </thead>
     <tbody>
<?php while($fila = $resultado->fetch_assoc()) { ?>
       <tr>
         <td><?= $fila['id'] ?></td>
         <td><?= htmlspecialchars($fila['nombre']) ?></td>
         <td><?= nl2br(htmlspecialchars($fila['mensaje'])) ?></td>
         <td>
<form method="post" onsubmit="return confirm('Estas seguro de eliminar esta sugerencia?');">
<input type="hidden" name="delete_id" value="<?= $fila['id'] ?>">
<button type="submit" class="delete-button">Eliminar</button>
</form>
         </td>
        </tr>
<?php } ?>
      </tbody>
  </table>
<?php } else { ?>
  <p>No hay sugerencias registradas.</p>
<?php } ?>

</body>
</html>

<?php
$conexion->close();
?>



<?php
$host = "mysql";
$usuario = "cliente";
$contrasena = "cliente";
$basededatos = "inscripciones_db";

$conexion = new mysqli($host, $usuario, $contrasena, $basededatos);
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$busqueda = isset($_GET['busqueda']) ? $conexion->real_escape_string($_GET['busqueda']) : '';

$sql = "SELECT id, alumno_nombre, alumno_apellido1, alumno_apellido2, alumno_dni, curso_solicitado 
        FROM inscripciones";

if (!empty($busqueda)) {
    $sql .= " WHERE alumno_nombre LIKE '%$busqueda%' 
              OR alumno_dni LIKE '%$busqueda%' 
              OR curso_solicitado LIKE '%$busqueda%'";
}

$resultado = $conexion->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descarga de Inscripciones</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        input[type=submit] { margin-top: 15px; padding: 10px 20px; }
        form.busqueda { margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>Listado de Inscripciones</h2>

<form class="busqueda" method="get">
    <label for="busqueda">Buscar por nombre, curso o DNI:</label>
    <input type="text" name="busqueda" id="busqueda" value="<?= htmlspecialchars($busqueda) ?>" />
    <input type="submit" value="Buscar" />
</form>

<form id="formulario" action="generar_pdf.php" method="get" onsubmit="return prepararEnvio();">
    <table>
        <thead>
            <tr>
                <th><input type="checkbox" onclick="toggleTodos(this)"> Seleccionar todos</th>
                <th>DNI</th>
                <th>Nombre</th>
                <th>Curso solicitado</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($resultado->num_rows > 0) { ?>
                <?php while ($fila = $resultado->fetch_assoc()) { ?>
                    <tr>
                        <td><input type="checkbox" name="ids[]" value="<?= $fila['id'] ?>"></td>
                        <td><?= htmlspecialchars($fila['alumno_dni']) ?></td>
                        <td><?= htmlspecialchars($fila['alumno_nombre'] . " " . $fila['alumno_apellido1'] . " " . $fila['alumno_apellido2']) ?></td>
                        <td><?= htmlspecialchars($fila['curso_solicitado']) ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="4">No hay inscripciones registradas.</td></tr>
            <?php } ?>
        </tbody>
    </table>

    <input type="hidden" name="id" id="id_hidden">
    <input type="submit" value="Descargar seleccion">
</form>

<script>
function prepararEnvio() {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]:checked');
    if (checkboxes.length === 0) {
        alert("Selecciona al menos una inscripcion.");
        return false;
    }

    const ids = Array.from(checkboxes).map(cb => cb.value).join(",");
    document.getElementById("id_hidden").value = ids;
    return true;
}

function toggleTodos(source) {
    const checkboxes = document.querySelectorAll('input[name="ids[]"]');
    for (const cb of checkboxes) {
        cb.checked = source.checked;
    }
}
</script>

</body>
</html>


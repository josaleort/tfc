<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $mensaje = $_POST["sugerencia"];

    $conexion = new mysqli("mysql", "cliente", "cliente", "inscripciones_db");

    if ($conexion->connect_error) {
        die("Error de conexion: " . $conexion->connect_error);
    }

    $stmt = $conexion->prepare("INSERT INTO sugerencias (nombre, mensaje) VALUES (?, ?)");
    $stmt->bind_param("ss", $nombre, $mensaje);
    $stmt->execute();

    $stmt->close();
    $conexion->close();

    echo "<p style='color:green;'>Gracias por tu sugerencia!</p>";
}
?>

<section id="sugerencias" style="padding: 2rem; background-color: #eef2f5;">
  <p>Hay algo que crees que podriamos mejorar en esta web o en el proceso de inscripcion? Dejanos tu sugerencia!</p>
  <form action="" method="post">
    <label for="nombre">Nombre:</label><br />
    <input type="text" id="nombre" name="nombre" required>

    <label for="sugerencia">Tu sugerencia:</label><br />
    <textarea id="sugerencia" name="sugerencia" rows="5" required></textarea>

    <input type="submit" value="Enviar sugerencia">
  </form>
</section>



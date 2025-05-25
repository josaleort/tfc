<?php

function shortcode_descarga_inscripciones() {
    ob_start();

    $conexion = new mysqli("mysql", "cliente", "cliente", "inscripciones_db");
    if ($conexion->connect_error) {
        return "Error de conexion a la base de datos.";
    }

    $sql = "SELECT id, alumno_nombre, alumno_apellido1, alumno_apellido2, curso_solicitado FROM inscripciones";
    $resultado = $conexion->query($sql);
?>

    <form id="formulario" action="<?php echo get_template_directory_uri(); ?>/generar_pdf.php" method="get" onsubmit="return prepararEnvio();">
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleTodos(this)"> Todos</th>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Curso</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado->num_rows > 0) { ?>
                    <?php while ($fila = $resultado->fetch_assoc()) { ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?= $fila['id'] ?>"></td>
                            <td><?= $fila['id'] ?></td>
                            <td><?= $fila['alumno_nombre'] . " " . $fila['alumno_apellido1'] . " " . $fila['alumno_apellido2'] ?></td>
                            <td><?= $fila['curso_solicitado'] ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr><td colspan="4">No hay inscripciones.</td></tr>
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

    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f2f2f2; }
        input[type=submit] { margin-top: 15px; padding: 10px 20px; }
    </style>

    <?php
    return ob_get_clean();
}
add_shortcode('descarga_inscripciones', 'shortcode_descarga_inscripciones');

function shortcode_formulario_sugerencias() {
    ob_start();

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sugerencia_formulario_enviar"])) {
        $nombre = sanitize_text_field($_POST["nombre"]);
        $mensaje = sanitize_textarea_field($_POST["sugerencia"]);

        $conexion = new mysqli("mysql", "cliente", "cliente", "inscripciones_db");
        if ($conexion->connect_error) {
            echo "<p style='color:red;'>Error de conexion con la base de datos.</p>";
        } else {
            $stmt = $conexion->prepare("INSERT INTO sugerencias (nombre, mensaje) VALUES (?, ?)");
            $stmt->bind_param("ss", $nombre, $mensaje);
            $stmt->execute();
            $stmt->close();
            $conexion->close();

            echo "<p style='color:green;'>Gracias por tu sugerencia!</p>";
        }
    }

    ?>
    <section id="sugerencias" style="padding: 2rem; background-color: #bfcde6;">
        <p>Hay algo que crees que podriamos mejorar? Dejanos tu sugerencia!</p>
        <form method="post">
            <label for="nombre">Nombre:</label><br />
            <input type="text" id="nombre" name="nombre" required><br /><br />

            <label for="sugerencia">Tu sugerencia:</label><br />
            <textarea id="sugerencia" name="sugerencia" rows="5" required></textarea><br /><br />

            <input type="submit" name="sugerencia_formulario_enviar" value="Enviar sugerencia">
        </form>
    </section>
    <?php

    return ob_get_clean();
}
add_shortcode('formulario_sugerencias', 'shortcode_formulario_sugerencias');

function shortcode_administrador($atts, $content = null) {
    if (current_user_can('manage_options')) {
        return do_shortcode($content);
    } else {
        return '<p><strong>Acceso restringido solo para administradores.</strong></p>';
    }
}
add_shortcode('solo_admin', 'shortcode_administrador');

function shortcode_chatbot() {
    ob_start();
    include get_template_directory() . '/chatbot/chatbot.php';
    return ob_get_clean();
}
add_shortcode('chatbot', 'shortcode_chatbot');

function cargar_recursos_chatbot() {
        wp_enqueue_style('chatbot-php', get_template_directory_uri() . '/chatbot/chatbot.php');
        wp_enqueue_style('chatbot-css', get_template_directory_uri() . '/chatbot/chatbot.css');
        wp_enqueue_script('chatbot-js', get_template_directory_uri() . '/chatbot/chatbot.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'cargar_recursos_chatbot');


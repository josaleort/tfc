<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

$conexion = new mysqli("mysql", "cliente", "cliente", "inscripciones_db");
if ($conexion->connect_error) {
    die("Error de conexion: " . $conexion->connect_error);
}

$foto_nombre = '';
$attach_id = 0;

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $foto_nombre = basename($_FILES['foto']['name']);
    $ruta_tema = get_template_directory() . '/fotos/';
    
    if (!is_dir($ruta_tema)) {
        mkdir($ruta_tema, 0755, true);
    }
    
    $ruta_archivo = $ruta_tema . $foto_nombre;
    move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_archivo);

    $attach_id = subir_imagen($ruta_archivo, $foto_nombre);
}

function subir_imagen($ruta_archivo, $nombre_archivo) {
    $upload_dir = wp_upload_dir();
    $destino = $upload_dir['path'] . '/' . $nombre_archivo;

    if (!copy($ruta_archivo, $destino)) {
        return 0;
    }

    $filetype = wp_check_filetype(basename($destino), null);

    $attachment = array(
        'guid'           => $upload_dir['url'] . '/' . basename($destino),
        'post_mime_type' => $filetype['type'],
        'post_title'     => sanitize_file_name($nombre_archivo),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $destino);

    require_once(ABSPATH . 'wp-admin/includes/image.php');

    $attach_data = wp_generate_attachment_metadata($attach_id, $destino);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}

$estudios_anio = $_POST['estudios_anio'] ?? '';
$estudios_mes = $_POST['estudios_mes'] ?? '';
$estudios_dia = $_POST['estudios_dia'] ?? '';

$alumno_fecha_nac = $_POST['alumno_nac_anio'] . '-' . $_POST['alumno_nac_mes'] . '-' . $_POST['alumno_nac_dia'];
$tutor_fecha_nac = $_POST['tutor_nac_anio'] . '-' . $_POST['tutor_nac_mes'] . '-' . $_POST['tutor_nac_dia'];
$estudios_fecha = $estudios_anio . '-' . $estudios_mes . '-' . $estudios_dia;

$alumno_nombre = $_POST['alumno_nombre'];
$alumno_apellido1 = $_POST['alumno_apellido1'];
$alumno_apellido2 = $_POST['alumno_apellido2'];
$alumno_dni = $_POST['alumno_dni'];
$alumno_lugar = $_POST['alumno_lugar'];
$alumno_nacionalidad = $_POST['alumno_nacionalidad'];
$alumno_domicilio = $_POST['alumno_domicilio'];
$alumno_localidad = $_POST['alumno_localidad'];
$alumno_cp = $_POST['alumno_cp'];
$alumno_sexo = $_POST['alumno_sexo'];
$observaciones = $_POST['observaciones'] ?? '';

$tutor_nombre = $_POST['tutor_nombre'];
$tutor_apellido1 = $_POST['tutor_apellido1'];
$tutor_apellido2 = $_POST['tutor_apellido2'];
$tutor_dni = $_POST['tutor_dni'];
$tutor_nacionalidad = $_POST['tutor_nacionalidad'];
$tutor_domicilio = $_POST['tutor_domicilio'];
$tutor_localidad = $_POST['tutor_localidad'];
$tutor_cp = $_POST['tutor_cp'];
$tutor_sexo = $_POST['tutor_sexo'];
$tutor_telefono = $_POST['tutor_telefono'];
$tutor_email = $_POST['tutor_email'];

$estudios_anteriores = $_POST['estudios_anteriores'] ?? '';
$centro_estudios = $_POST['centro_estudios'] ?? '';
$curso_solicitado = $_POST['curso_solicitado'];

$consentimiento_datos = isset($_POST['consentimiento_datos']) ? 1 : 0;
$consentimiento_imagenes = isset($_POST['consentimiento_imagenes']) ? 1 : 0;
$declaracion_veracidad = isset($_POST['declaracion_veracidad']) ? 1 : 0;

$stmt = $conexion->prepare("
  INSERT INTO inscripciones (
    alumno_nombre, alumno_apellido1, alumno_apellido2, alumno_dni, alumno_nacimiento, alumno_lugar,
    alumno_nacionalidad, alumno_domicilio, alumno_localidad, alumno_cp, alumno_sexo, observaciones,
    tutor_nombre, tutor_apellido1, tutor_apellido2, tutor_dni, tutor_nacimiento, tutor_nacionalidad,
    tutor_domicilio, tutor_localidad, tutor_cp, tutor_sexo, tutor_telefono, tutor_email,
    estudios_anteriores, estudios_fecha, centro_estudios, curso_solicitado,
    foto_nombre, consentimiento_datos, consentimiento_imagenes, declaracion_veracidad
  ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
");

$types = str_repeat('s', 29) . str_repeat('i', 3);

$values = [
    $alumno_nombre,
    $alumno_apellido1,
    $alumno_apellido2,
    $alumno_dni,
    $alumno_fecha_nac,
    $alumno_lugar,
    $alumno_nacionalidad,
    $alumno_domicilio,
    $alumno_localidad,
    $alumno_cp,
    $alumno_sexo,
    $observaciones,
    $tutor_nombre,
    $tutor_apellido1,
    $tutor_apellido2,
    $tutor_dni,
    $tutor_fecha_nac,
    $tutor_nacionalidad,
    $tutor_domicilio,
    $tutor_localidad,
    $tutor_cp,
    $tutor_sexo,
    $tutor_telefono,
    $tutor_email,
    $estudios_anteriores,
    $estudios_fecha,
    $centro_estudios,
    $curso_solicitado,
    $foto_nombre,
    $consentimiento_datos,
    $consentimiento_imagenes,
    $declaracion_veracidad
];

$tmp = [];
$tmp[] = &$types;
foreach ($values as $key => $value) {
    $tmp[] = &$values[$key];
}

call_user_func_array([$stmt, 'bind_param'], $tmp);

if ($stmt->execute()) {
    header("Location: http://localhost/exito/");
    exit();
} else {
    header("Location: http://localhost/error/");
    exit();
}

$stmt->close();
$conexion->close();
?>

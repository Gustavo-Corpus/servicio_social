<?php
// Include config file
require_once "config.php";
?>

<!DOCTYPE html>
<html lang="es">
<link rel="stylesheet" href="style_create.css">

<head>
    <meta charset="UTF-8">
    <title>Administración de Tablas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <script>
        $(document).ready(function () {
            $(".section-header").on("click", function () {
                $(this).next(".section-content").slideToggle();
            });
        });
    </script>
</head>

<body>
    <div class="wrapper">
        <a href="index.php" class="btn btn-info top-right-btn">Regresar</a>

        <h2>Administración de Tablas</h2>
        <p>Utilice las secciones para agregar registros en las tablas.</p>

        <!-- Sección Usuarios -->
        <div>
            <div class="section-header">
                <h3>Usuarios</h3>
            </div>
            <div class="section-content">
                <form action="process_users.php" method="post">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Apellido</label>
                        <input type="text" name="apellido" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Edad</label>
                        <input type="text" name="edad" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Sexo</label>
                        <select name="sexo" class="form-control">
                            <option value="Masculino">Masculino</option>
                            <option value="Femenino">Femenino</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Estatus</label>
                        <select name="estatus" class="form-control">
                            <option value="Activo">Activo</option>
                            <option value="Inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Dirección</label>
                        <textarea name="direccion" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Ocupación</label>
                        <input type="text" name="ocupacion" class="form-control">
                    </div>
                    <input type="submit" class="btn btn-primary" value="Guardar">
                </form>
            </div>
        </div>

        <!-- Nueva Sección: Tabla Users -->
        <div>
            <div class="section-header">
                <h3>Tabla Users</h3>
            </div>
            <div class="section-content">
                <form action="process_users_table.php" method="post">
                    <div class="form-group">
                        <label>ID</label>
                        <input type="text" name="id" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Fecha de Creación</label>
                        <input type="date" name="created_at" class="form-control" required>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Guardar">
                </form>
            </div>
        </div>

        <!-- Sección Evaluaciones -->
        <div>
            <div class="section-header">
                <h3>Evaluaciones</h3>
            </div>
            <div class="section-content">
                <form action="process_evaluaciones.php" method="post">
                    <div class="form-group">
                        <label>ID Usuario</label>
                        <input type="text" name="id_usuario" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Mes</label>
                        <input type="text" name="mes" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Año</label>
                        <input type="text" name="anio" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Calificación</label>
                        <input type="text" name="calificacion" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Comentarios</label>
                        <textarea name="comentarios" class="form-control"></textarea>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Guardar">
                </form>
            </div>
        </div>

        <!-- Sección Estados -->
        <div>
            <div class="section-header">
                <h3>Estados</h3>
            </div>
            <div class="section-content">
                <form action="process_estados.php" method="post">
                    <div class="form-group">
                        <label>Nombre del Estado</label>
                        <input type="text" name="nombre_estado" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <textarea name="descripcion" class="form-control"></textarea>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Guardar">
                </form>
            </div>
        </div>



    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style_crud.css">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
    <style type="text/css">
        .wrapper {
            width: 850px;
            margin: 0 auto;
        }

        .page-header h2 {
            margin-top: 0;
        }

        table tr td:last-child a {
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <!-- Formulario de búsqueda -->
                    <form method="get" action="index.php" class="form-inline">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control"
                                placeholder="Buscar en todas las tablas"
                                value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                        <a href="index.php" class="btn btn-default">Reiniciar</a>
                    </form>

                    <br>

                    <!-- Tabla de Usuarios -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Usuarios</h2>
                        <button id="toggleUserTable" class="btn btn-info pull-right">Ocultar Tabla de Usuarios</button>
                        <a href="create.php" class="btn btn-success pull-right" style="margin-right: 10px;">Agregar
                            nuevo usuario</a>
                    </div>
                    <div id="userTable">
                        <?php
                        require_once "config.php";

                        $search = isset($_GET['search']) ? $_GET['search'] : '';

                        $sql = "SELECT * FROM usuarios";
                        if (!empty($search)) {
                            $sql .= " WHERE id_usuarios LIKE '%$search%' OR nombre LIKE '%$search%'";
                        }

                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>#</th>";
                                echo "<th>Nombre</th>";
                                echo "<th>Apellido</th>";
                                echo "<th>Edad</th>";
                                echo "<th>Sexo</th>";
                                echo "<th>Estatus</th>";
                                echo "<th>Correo</th>";
                                echo "<th>Dirección</th>";
                                echo "<th>Ocupación</th>";
                                echo "<th>Acción</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id_usuarios'] . "</td>";
                                    echo "<td>" . $row['nombre'] . "</td>";
                                    echo "<td>" . $row['apellido'] . "</td>";
                                    echo "<td>" . $row['edad'] . "</td>";
                                    echo "<td>" . $row['sexo'] . "</td>";
                                    echo "<td>" . $row['estatus'] . "</td>";
                                    echo "<td>" . $row['correo'] . "</td>";
                                    echo "<td>" . $row['direccion'] . "</td>";
                                    echo "<td>" . $row['ocupacion'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='read.php?id_usuarios=" . $row['id_usuarios'] . "' title='Ver' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                    echo "<a href='update.php?id_usuarios=" . $row['id_usuarios'] . "' title='Actualizar' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                    echo "<a href='delete.php?id_usuarios=" . $row['id_usuarios'] . "' title='Borrar' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result);
                            } else {
                                echo "<p class='lead'><em>No se encontraron registros en la tabla Usuarios.</em></p>";
                            }
                        } else {
                            echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($link);
                        }
                        ?>
                    </div>

                    <!-- Tabla de Evaluaciones -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Evaluaciones</h2>
                        <button id="toggleEvaluationsTable" class="btn btn-info pull-right">Ocultar Tabla de
                            Evaluaciones</button>
                    </div>
                    <div id="evaluationsTable">
                        <?php
                        $sql = "SELECT * FROM evaluaciones";
                        if (!empty($search)) {
                            $sql .= " WHERE id_evaluacion LIKE '%$search%' OR id_usuario LIKE '%$search%' OR comentarios LIKE '%$search%'";
                        }

                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>#</th>";
                                echo "<th>ID Usuario</th>";
                                echo "<th>Mes</th>";
                                echo "<th>Año</th>";
                                echo "<th>Calificación</th>";
                                echo "<th>Comentarios</th>";
                                echo "<th>Acción</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id_evaluacion'] . "</td>";
                                    echo "<td>" . $row['id_usuario'] . "</td>";
                                    echo "<td>" . $row['mes'] . "</td>";
                                    echo "<td>" . $row['anio'] . "</td>";
                                    echo "<td>" . $row['calificacion'] . "</td>";
                                    echo "<td>" . $row['comentarios'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='read_evaluaciones.php?id_evaluacion=" . $row['id_evaluacion'] . "' title='Ver' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                    echo "<a href='update_evaluaciones.php?id_evaluacion=" . $row['id_evaluacion'] . "' title='Actualizar' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                    echo "<a href='delete_evaluaciones.php?id_evaluacion=" . $row['id_evaluacion'] . "' title='Borrar' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result);
                            } else {
                                echo "<p class='lead'><em>No se encontraron registros en la tabla Evaluaciones.</em></p>";
                            }
                        } else {
                            echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($link);
                        }
                        ?>
                    </div>

                    <!-- Tabla de Estados -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Estados</h2>
                        <button id="toggleStatesTable" class="btn btn-info pull-right">Ocultar Tabla de Estados</button>
                    </div>
                    <div id="statesTable">
                        <?php
                        $sql = "SELECT * FROM estados";
                        if (!empty($search)) {
                            $sql .= " WHERE id_estado LIKE '%$search%' OR estado LIKE '%$search%'";
                        }

                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>#</th>";
                                echo "<th>Estado</th>";
                                echo "<th>Acción</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id_estado'] . "</td>";
                                    echo "<td>" . $row['estado'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='read_estado.php?id_estado=" . $row['id_estado'] . "' title='Ver' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                    echo "<a href='update_estado.php?id_estado=" . $row['id_estado'] . "' title='Actualizar' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                    echo "<a href='delete_estado.php?id_estado=" . $row['id_estado'] . "' title='Borrar' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result);
                            } else {
                                echo "<p class='lead'><em>No se encontraron registros en la tabla Estados.</em></p>";
                            }
                        } else {
                            echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($link);
                        }
                        ?>
                    </div>

                    <!-- Tabla de Users -->
                    <div class="page-header clearfix">
                        <h2 class="pull-left">Users</h2>
                        <button id="toggleUsersTable" class="btn btn-info pull-right">Ocultar Tabla de Users</button>
                    </div>
                    <div id="usersTable">
                        <?php
                        $sql = "SELECT * FROM users";
                        if (!empty($search)) {
                            $sql .= " WHERE id LIKE '%$search%' OR username LIKE '%$search%'";
                        }

                        if ($result = mysqli_query($link, $sql)) {
                            if (mysqli_num_rows($result) > 0) {
                                echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>#</th>";
                                echo "<th>Username</th>";
                                echo "<th>Password</th>";
                                echo "<th>Created At</th>";
                                echo "<th>Acción</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while ($row = mysqli_fetch_array($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['username'] . "</td>";
                                    echo "<td>" . $row['password'] . "</td>";
                                    echo "<td>" . $row['created_at'] . "</td>";
                                    echo "<td>";
                                    echo "<a href='read_user.php?id=" . $row['id'] . "' title='Ver' data-toggle='tooltip'><span class='glyphicon glyphicon-eye-open'></span></a>";
                                    echo "<a href='update_user.php?id=" . $row['id'] . "' title='Actualizar' data-toggle='tooltip'><span class='glyphicon glyphicon-pencil'></span></a>";
                                    echo "<a href='delete_user.php?id=" . $row['id'] . "' title='Borrar' data-toggle='tooltip'><span class='glyphicon glyphicon-trash'></span></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                                mysqli_free_result($result);
                            } else {
                                echo "<p class='lead'><em>No se encontraron registros en la tabla Users.</em></p>";
                            }
                        } else {
                            echo "ERROR: No se pudo ejecutar $sql. " . mysqli_error($link);
                        }
                        ?>
                    </div>

                    <!-- Script para controlar el despliegue -->
                    <script>
                        $(document).ready(function () {
                            $('#toggleUserTable').click(function () {
                                $('#userTable').toggle();
                                $(this).text(function (i, text) {
                                    return text === 'Ocultar Tabla de Usuarios' ? 'Mostrar Tabla de Usuarios' : 'Ocultar Tabla de Usuarios';
                                });
                            });

                            $('#toggleEvaluationsTable').click(function () {
                                $('#evaluationsTable').toggle();
                                $(this).text(function (i, text) {
                                    return text === 'Ocultar Tabla de Evaluaciones' ? 'Mostrar Tabla de Evaluaciones' : 'Ocultar Tabla de Evaluaciones';
                                });
                            });

                            $('#toggleStatesTable').click(function () {
                                $('#statesTable').toggle();
                                $(this).text(function (i, text) {
                                    return text === 'Ocultar Tabla de Estados' ? 'Mostrar Tabla de Estados' : 'Ocultar Tabla de Estados';
                                });
                            });

                            $('#toggleUsersTable').click(function () {
                                $('#usersTable').toggle();
                                $(this).text(function (i, text) {
                                    return text === 'Ocultar Tabla de Users' ? 'Mostrar Tabla de Users' : 'Ocultar Tabla de Users';
                                });
                            });
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>
</body>

</html>
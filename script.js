// Variables globales para las gráficas
let distribucionChart = null;
let calificacionesChart = null;

// Variables para el CRUD
let editandoId = null;
const modal = document.getElementById("empleadoModal");
const form = document.getElementById("empleadoForm");

// Función para cargar y mostrar las estadísticas
function cargarEstadisticas() {
  fetch("obtener_datos.php?action=getStats")
    .then((response) => response.json())
    .then((data) => {
      actualizarEstadisticasGenerales(data.generales);
      actualizarGraficas(data.distribucion);
    })
    .catch((error) => console.error("Error al cargar estadísticas:", error));
}

// Función para actualizar las estadísticas generales
function actualizarEstadisticasGenerales(datos) {
  document.getElementById("totalEmpleados").textContent = datos.total_empleados;
  document.getElementById("promedioGeneral").textContent =
    datos.promedio_general;
  document.getElementById("totalEstados").textContent = datos.total_estados;
}

// Función para actualizar las gráficas
function actualizarGraficas(datos) {
  const estados = datos.map((item) => item.estado);
  const cantidades = datos.map((item) => item.cantidad_empleados);
  const promedios = datos.map((item) => item.promedio_calificacion);

  // Destruir gráficas existentes si las hay
  if (distribucionChart) distribucionChart.destroy();
  if (calificacionesChart) calificacionesChart.destroy();

  // Crear gráfica de distribución
  const ctxDistribucion = document
    .getElementById("distribucionChart")
    .getContext("2d");
  distribucionChart = new Chart(ctxDistribucion, {
    type: "pie",
    data: {
      labels: estados,
      datasets: [
        {
          data: cantidades,
          backgroundColor: estados.map(
            (_, i) => `hsl(${(i * 360) / estados.length}, 70%, 60%)`
          ),
        },
      ],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: "right",
        },
        title: {
          display: true,
          text: "Empleados por Estado",
        },
      },
    },
  });

  // Crear gráfica de calificaciones
  const ctxCalificaciones = document
    .getElementById("calificacionesChart")
    .getContext("2d");
  calificacionesChart = new Chart(ctxCalificaciones, {
    type: "bar",
    data: {
      labels: estados,
      datasets: [
        {
          label: "Promedio de Calificaciones",
          data: promedios,
          backgroundColor: "rgba(54, 162, 235, 0.8)",
        },
      ],
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 0.5,
            precision: 2,
          },
        },
      },
      plugins: {
        title: {
          display: true,
          text: "Promedio de Calificaciones por Estado",
        },
        tooltip: {
          callbacks: {
            label: function (context) {
              return `Promedio: ${context.raw.toFixed(2)}`;
            },
          },
        },
      },
    },
  });
}

// Funciones CRUD
async function crearEmpleado(datosEmpleado) {
  try {
    const response = await fetch("crud_operations.php?action=create", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datosEmpleado),
    });

    const data = await response.json();
    if (data.success) {
      return data;
    } else {
      throw new Error(data.error);
    }
  } catch (error) {
    console.error("Error al crear empleado:", error);
    throw error;
  }
}

async function actualizarEmpleado(id, datosEmpleado) {
  try {
    const response = await fetch(`crud_operations.php?action=update&id=${id}`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(datosEmpleado),
    });

    const data = await response.json();
    if (data.success) {
      return data;
    } else {
      throw new Error(data.error);
    }
  } catch (error) {
    console.error("Error al actualizar empleado:", error);
    throw error;
  }
}

async function eliminarEmpleado(id) {
  if (!confirm("¿Está seguro de que desea eliminar este empleado?")) {
    return;
  }

  try {
    const response = await fetch(`crud_operations.php?action=delete&id=${id}`, {
      method: "POST",
    });

    const data = await response.json();
    if (data.success) {
      cargarEmpleados(document.getElementById("estado").value);
      cargarEstadisticas();
      mostrarMensaje("Empleado eliminado exitosamente", "success");
      return data;
    } else {
      throw new Error(data.error);
    }
  } catch (error) {
    console.error("Error al eliminar empleado:", error);
    mostrarMensaje("Error al eliminar empleado", "error");
    throw error;
  }
}

async function cargarCatalogos() {
  try {
    console.log("Iniciando carga de catálogos...");

    // Cargar departamentos
    const respDepartamentos = await fetch(
      "crud_operations.php?action=getDepartamentos"
    );
    const textoDepartamentos = await respDepartamentos.text(); // Primero obtener el texto
    console.log("Respuesta departamentos:", textoDepartamentos); // Debug

    const departamentos = JSON.parse(textoDepartamentos);
    if (!departamentos.success) {
      throw new Error(departamentos.error || "Error al obtener departamentos");
    }

    const selectDepartamento = document.getElementById("id_departamento");
    if (selectDepartamento) {
      selectDepartamento.innerHTML =
        '<option value="">Seleccione un departamento...</option>' +
        departamentos.data
          .map(
            (d) =>
              `<option value="${d.id_departamento}">${d.nombre_departamento}</option>`
          )
          .join("");
    }

    // Cargar estados
    const respEstados = await fetch("cargar_estados.php");
    const textoEstados = await respEstados.text(); // Primero obtener el texto
    console.log("Respuesta estados:", textoEstados); // Debug

    const estados = JSON.parse(textoEstados);
    const selectEstado = document.getElementById("id_estado");
    if (selectEstado) {
      selectEstado.innerHTML =
        '<option value="">Seleccione un estado...</option>' +
        estados
          .map((e) => `<option value="${e.id_estado}">${e.estado}</option>`)
          .join("");
    }

    console.log("Catálogos cargados exitosamente");
  } catch (error) {
    console.error("Error detallado al cargar catálogos:", error);
    console.log("Stack trace:", error.stack);
    alert("Error al cargar los catálogos: " + error.message);
  }
}

// Funciones del Modal
function abrirModalCrear() {
  editandoId = null;
  document.getElementById("modalTitle").textContent = "Nuevo Empleado";
  form.reset();
  cargarCatalogos();
  modal.style.display = "block";
}

function abrirModalEditar(empleado) {
  editandoId = empleado.id_usuarios;
  document.getElementById("modalTitle").textContent = "Editar Empleado";

  cargarCatalogos().then(() => {
    document.getElementById("nombre").value = empleado.nombre;
    document.getElementById("apellido").value = empleado.apellido;
    document.getElementById("sexo").value = empleado.sexo;
    document.getElementById("correo").value = empleado.correo;
    document.getElementById("edad").value = empleado.edad;
    document.getElementById("direccion").value = empleado.direccion;
    document.getElementById("ocupacion").value = empleado.ocupacion;
    document.getElementById("id_departamento").value = empleado.id_departamento;
    document.getElementById("id_estado").value = empleado.id_estado;
    document.getElementById("estatus").value = empleado.estatus;
  });

  modal.style.display = "block";
}

function cerrarModal() {
  modal.style.display = "none";
  form.reset();
  editandoId = null;
}

// Función para mostrar mensajes
function mostrarMensaje(mensaje, tipo) {
  alert(mensaje); // Puedes reemplazar esto con una implementación más elegante
}

// Funciones existentes
function verificarAutenticacion() {
  fetch("verificar_sesion.php")
    .then((response) => response.json())
    .then((data) => {
      if (!data.autenticado) {
        window.location.href = "login.html";
      }
    })
    .catch(() => {
      window.location.href = "login.html";
    });
}

function cargarDatosUsuario() {
  fetch("obtener_datos.php?action=getUserData")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("userName").textContent = data.username;
        if (data.profile_pic) {
          document.getElementById("userProfilePic").src = data.profile_pic;
        }
      }
    })
    .catch((error) =>
      console.error("Error al cargar datos del usuario:", error)
    );
}

function mostrarBotonDescarga() {
  const zona = document.getElementById("estado").value;
  const contenedorDescarga = document.getElementById("descargar-contenedor");

  if (zona && zona !== "Seleccione un estado...") {
    contenedorDescarga.style.display = "block";
  } else {
    contenedorDescarga.style.display = "none";
  }
}

function descargarTodos() {
  window.location.href = "generar_excel_todos.php";
}

function descargarExcel() {
  const zona = document.getElementById("estado").value;
  if (zona && zona !== "Seleccione un estado...") {
    window.location.href = `generar_excel.php?zona=${encodeURIComponent(zona)}`;
  }
}

// Event Listener principal
document.addEventListener("DOMContentLoaded", function () {
  // Verificar autenticación primero
  verificarAutenticacion();

  // Cargar estadísticas iniciales
  cargarEstadisticas();

  // Cargar datos del usuario
  cargarDatosUsuario();

  // CRUD Event Listeners
  const btnNuevoEmpleado = document.getElementById("btnNuevoEmpleado");
  if (btnNuevoEmpleado) {
    btnNuevoEmpleado.addEventListener("click", abrirModalCrear);
  }

  const empleadoForm = document.getElementById("empleadoForm");
  if (empleadoForm) {
    empleadoForm.addEventListener("submit", async function (e) {
      e.preventDefault();

      // Recoger todos los datos del formulario
      const datosEmpleado = {
        nombre: document.getElementById("nombre").value,
        apellido: document.getElementById("apellido").value,
        sexo: document.getElementById("sexo").value,
        correo: document.getElementById("correo").value,
        edad: document.getElementById("edad").value,
        direccion: document.getElementById("direccion").value,
        ocupacion: document.getElementById("ocupacion").value,
        id_departamento: document.getElementById("id_departamento").value,
        id_estado: document.getElementById("id_estado").value,
        estatus: document.getElementById("estatus").value,
      };

      try {
        console.log("Datos a enviar:", datosEmpleado); // Debug

        const response = await fetch("crud_operations.php?action=create", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(datosEmpleado),
        });

        const result = await response.json();
        console.log("Respuesta del servidor:", result); // Debug

        if (result.success) {
          alert("Empleado creado exitosamente");
          cerrarModal();
          // Recargar la tabla
          const estadoActual = document.getElementById("estado").value;
          await cargarEmpleados(estadoActual);
          await cargarEstadisticas();
        } else {
          throw new Error(result.error || "Error al crear empleado");
        }
      } catch (error) {
        console.error("Error completo:", error);
        alert("Error al crear empleado: " + error.message);
      }
    });
  }

  // Resto de tu código existente para la carga de estados y manejo de eventos
  const estadoSelect = document.getElementById("estado");
  const empleadosBody = document.getElementById("empleados-body");

  // Cargar estados
  fetch("cargar_estados.php")
    .then((response) => response.json())
    .then((data) => {
      data.forEach((estado) => {
        const option = document.createElement("option");
        option.value = estado.id_estado;
        option.textContent = estado.estado;
        estadoSelect.appendChild(option);
      });
    })
    .catch((error) => console.error("Error al cargar estados:", error));

  // Evento change del select de estados
  estadoSelect.addEventListener("change", function () {
    const estadoId = this.value;
    mostrarBotonDescarga();

    if (!estadoId) {
      empleadosBody.innerHTML = "";
      return;
    }

    if ($.fn.DataTable.isDataTable("#empleados-table")) {
      $("#empleados-table").DataTable().clear().destroy();
    }

    fetch(`obtener_datos.php?action=getEmployees&estado=${estadoId}`)
      .then((response) => response.json())
      .then((data) => {
        empleadosBody.innerHTML = data
          .map(
            (empleado) => `
                    <tr>
                        <td>${empleado.id_usuarios}</td>
                        <td>${empleado.nombre}</td>
                        <td>${empleado.apellido}</td>
                        <td>${empleado.ocupacion || "No especificado"}</td>
                        <td>${
                          empleado.promedio_calificacion || "Sin evaluaciones"
                        }</td>
                        <td class="action-buttons">
                            <button class="btn-edit" onclick='abrirModalEditar(${JSON.stringify(
                              empleado
                            )})'>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-delete" onclick="eliminarEmpleado(${
                              empleado.id_usuarios
                            })">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `
          )
          .join("");

        $("#empleados-table").DataTable({
          language: {
            url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/Spanish.json",
          },
          pageLength: 25,
          order: [[1, "asc"]],
          responsive: true,
        });
      })
      .catch((error) => {
        console.error("Error:", error);
        empleadosBody.innerHTML =
          '<tr><td colspan="6">Error al cargar los datos</td></tr>';
      });
    cargarEstadisticas();
  });

  // Manejo del menú de usuario
  const userMenuTrigger = document.getElementById("userMenuTrigger");
  const userMenuDropdown = document.getElementById("userMenuDropdown");
  const logoutButton = document.getElementById("logoutButton");

  userMenuTrigger.addEventListener("click", function () {
    userMenuDropdown.classList.toggle("active");
  });

  document.addEventListener("click", function (event) {
    if (
      !userMenuTrigger.contains(event.target) &&
      !userMenuDropdown.contains(event.target)
    ) {
      userMenuDropdown.classList.remove("active");
    }
  });

  logoutButton.addEventListener("click", function () {
    fetch("cerrar_sesion.php")
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          window.location.href = "login.html";
        }
      })
      .catch((error) => console.error("Error al cerrar sesión:", error));
  });
});

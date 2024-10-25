// script.js

// Variables globales para las gráficas
let distribucionChart = null;
let calificacionesChart = null;

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
          max: 5,
        },
      },
      plugins: {
        title: {
          display: true,
          text: "Promedio de Calificaciones por Estado",
        },
      },
    },
  });
}

// Función para verificar autenticación
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

// Función para obtener y mostrar los datos del usuario
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

// Función para mostrar el botón de descarga
function mostrarBotonDescarga() {
  console.log("mostrarBotonDescarga llamada");
  const zona = document.getElementById("estado").value;
  console.log("Zona seleccionada:", zona);
  const contenedorDescarga = document.getElementById("descargar-contenedor");

  if (zona && zona !== "Seleccione un estado...") {
    console.log("Mostrando botón de descarga");
    contenedorDescarga.style.display = "block";
  } else {
    console.log("Ocultando botón de descarga");
    contenedorDescarga.style.display = "none";
  }
}

// Función para descargar el archivo Excel con todos los empleados
function descargarTodos() {
  window.location.href = "generar_excel_todos.php";
}

// Función para descargar el archivo Excel
function descargarExcel() {
  const zona = document.getElementById("estado").value;
  if (zona && zona !== "Seleccione un estado...") {
    window.location.href = `generar_excel.php?zona=${encodeURIComponent(zona)}`;
  }
}

// Manejo del menú desplegable y carga de datos
document.addEventListener("DOMContentLoaded", function () {
  // Verificar autenticación primero
  verificarAutenticacion();

  // Cargar estadísticas iniciales
  cargarEstadisticas();

  // Cargar datos del usuario
  cargarDatosUsuario();

  const estadoSelect = document.getElementById("estado");
  const empleadosBody = document.getElementById("empleados-body");

  console.log("DOM fully loaded. Fetching estados...");

  // Cargar estados
  fetch("cargar_estados.php")
    .then((response) => {
      console.log("Response received:", response);
      return response.json();
    })
    .then((data) => {
      console.log("Estados data:", data);
      data.forEach((estado) => {
        const option = document.createElement("option");
        option.value = estado.id_estado;
        option.textContent = estado.estado;
        estadoSelect.appendChild(option);
      });
      console.log("Estados loaded into select");
    })
    .catch((error) => console.error("Error al cargar estados:", error));

  // Función para inicializar DataTables
  function inicializarDataTable() {
    $("#empleados-table").DataTable({
      destroy: true, // Destruye cualquier DataTable anterior para evitar conflictos al recargar datos
      language: {
        url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/Spanish.json", // Traducción al español
      },
    });
  }

  // Modificar el evento change del select
  estadoSelect.addEventListener("change", function () {
    const estadoId = this.value;
    console.log("Estado seleccionado:", estadoId);
    mostrarBotonDescarga();

    if (!estadoId) {
      empleadosBody.innerHTML = "";
      return;
    }

    // Destruir la instancia de DataTables existente (si la hay)
    if ($.fn.DataTable.isDataTable("#empleados-table")) {
      $("#empleados-table").DataTable().clear().destroy();
    }

    fetch(`obtener_datos.php?action=getEmployees&estado=${estadoId}`)
      .then((response) => response.json())
      .then((data) => {
        console.log("Empleados data:", data);
        empleadosBody.innerHTML = "";

        // Insertar los nuevos datos en la tabla
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
                  </tr>
              `
          )
          .join("");

        // Inicializar DataTables con opciones adicionales para manejar más datos
        $("#empleados-table").DataTable({
          language: {
            url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/Spanish.json",
          },
          pageLength: 25, // Mostrar más registros por página
          order: [[1, "asc"]], // Ordenar por nombre por defecto
          responsive: true, // Hacer la tabla responsive
        });
      })
      .catch((error) => {
        console.error("Error:", error);
        empleadosBody.innerHTML =
          '<tr><td colspan="5">Error al cargar los datos</td></tr>';
      });
    cargarEstadisticas(); // Actualizar estadísticas después de cambiar la selección
  });

  // Manejo del menú de usuario
  const userMenuTrigger = document.getElementById("userMenuTrigger");
  const userMenuDropdown = document.getElementById("userMenuDropdown");
  const logoutButton = document.getElementById("logoutButton");

  userMenuTrigger.addEventListener("click", function () {
    userMenuDropdown.classList.toggle("active");
  });

  // Cerrar el menú al hacer clic fuera
  document.addEventListener("click", function (event) {
    if (
      !userMenuTrigger.contains(event.target) &&
      !userMenuDropdown.contains(event.target)
    ) {
      userMenuDropdown.classList.remove("active");
    }
  });

  // Manejar el cierre de sesión
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

  // Variables globales para las gráficas
  let distribucionChart = null;
  let calificacionesChart = null;

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
    document.getElementById("totalEmpleados").textContent =
      datos.total_empleados;
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
            max: 5,
          },
        },
        plugins: {
          title: {
            display: true,
            text: "Promedio de Calificaciones por Estado",
          },
        },
      },
    });
  }
});

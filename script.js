window.onerror = function (msg, url, line, col, error) {
  console.error("%cError no manejado:", "color: red; font-weight: bold", {
    mensaje: msg,
    url: url,
    linea: line,
    columna: col,
    error: error,
  });
  return false;
};

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
      // En lugar de llamar a cargarEmpleados, disparamos el evento change en el select
      const estadoSelect = document.getElementById("estado");
      if (estadoSelect) {
        const event = new Event("change");
        estadoSelect.dispatchEvent(event);
      }

      // Actualizar estadísticas
      await cargarEstadisticas();
      alert("Empleado eliminado exitosamente");
    } else {
      throw new Error(data.error || "Error al eliminar empleado");
    }
  } catch (error) {
    console.error("Error al eliminar empleado:", error);
    alert("Error al eliminar empleado");
  }
}

// Función auxiliar para obtener el nombre del mes
function getNombreMes(mes) {
  const meses = [
    "Enero",
    "Febrero",
    "Marzo",
    "Abril",
    "Mayo",
    "Junio",
    "Julio",
    "Agosto",
    "Septiembre",
    "Octubre",
    "Noviembre",
    "Diciembre",
  ];
  return meses[mes - 1] || "";
}

function cerrarModalEvaluacion() {
  try {
    const evaluacionModal = document.getElementById("evaluacionModal");
    const evaluacionForm = document.getElementById("evaluacionForm");

    if (!evaluacionModal || !evaluacionForm) {
      console.error("No se encontraron elementos del modal");
      return;
    }

    // Ocultar el modal
    evaluacionModal.style.display = "none";

    // Limpiar el formulario y campos ocultos
    evaluacionForm.reset();
    document.getElementById("evaluacionId").value = "";
    document.getElementById("empleadoIdEval").value = "";

    // Restaurar texto del botón
    const submitButton = evaluacionForm.querySelector('button[type="submit"]');
    if (submitButton) {
      submitButton.textContent = "Guardar Evaluación";
    }

    // Recargar datos si es necesario
    const estadoSeleccionado = document.getElementById("estado").value;
    if (estadoSeleccionado) {
      // Disparar el evento change para recargar la tabla
      const event = new Event("change");
      document.getElementById("estado").dispatchEvent(event);
    }
  } catch (error) {
    console.error("Error al cerrar modal:", error);
  }
}

// Función para llenar el select de años
function llenarSelectAnios() {
  const selectAnio = document.getElementById("anio");
  const anioActual = new Date().getFullYear();
  const anioInicial = 2020; // Puedes ajustar este año según necesites

  selectAnio.innerHTML = "";
  for (let anio = anioActual; anio >= anioInicial; anio--) {
    const option = document.createElement("option");
    option.value = anio;
    option.textContent = anio;
    selectAnio.appendChild(option);
  }
}

// Función para abrir el modal de evaluación
async function abrirModalEvaluacion(empleadoId, nombreCompleto) {
  try {
    document.getElementById("empleadoIdEval").value = empleadoId;
    document.getElementById(
      "evaluacionTitle"
    ).textContent = `Evaluar a ${nombreCompleto}`;

    // Llenar select de años
    llenarSelectAnios();

    // Establecer el año y mes actual por defecto
    const fecha = new Date();
    document.getElementById("anio").value = fecha.getFullYear();
    document.getElementById("mes").value = fecha.getMonth() + 1;

    // Cargar evaluaciones existentes
    await cargarEvaluacionesEmpleado(empleadoId);

    evaluacionModal.style.display = "block";
  } catch (error) {
    console.error("Error al abrir modal de evaluación:", error);
    alert("Error al cargar las evaluaciones del empleado");
  }
}

// Función para cargar evaluaciones existentes
async function cargarEvaluacionesEmpleado(empleadoId) {
  try {
    const response = await fetch(
      `obtener_evaluaciones.php?id_usuario=${empleadoId}`
    );
    const data = await response.json();

    console.log("Datos recibidos:", data);

    const tbody = document.getElementById("evaluacionesBody");
    if (!tbody) {
      throw new Error("No se encontró el elemento tbody");
    }

    tbody.innerHTML = data
      .map((eval) => {
        // Asegurarnos de que tenemos un id_evaluacion
        if (!eval.id_evaluacion) {
          console.error("Evaluación sin ID:", eval);
          return "";
        }

        // Crear el objeto de datos para el botón de manera segura
        const evalData = {
          id_evaluacion: eval.id_evaluacion, // Asegurarnos de incluir el ID
          mes: eval.mes,
          anio: eval.anio,
          calificacion: eval.calificacion,
          comentarios: (eval.comentarios || "").replace(/"/g, '\\"'), // Escapar comillas
        };

        return `
              <tr>
                  <td>${getNombreMes(Number(eval.mes))}</td>
                  <td>${eval.anio}</td>
                  <td class="evaluacion-valor">${Number(
                    eval.calificacion
                  ).toFixed(1)}</td>
                  <td>${eval.comentarios || "-"}</td>
                  <td class="action-buttons">
                      <button type="button"
                              class="btn-edit"
                              data-eval-id="${eval.id_evaluacion}"
                              onclick='editarEvaluacion({
                                  "id_evaluacion": "${eval.id_evaluacion}",
                                  "mes": ${eval.mes},
                                  "anio": ${eval.anio},
                                  "calificacion": ${eval.calificacion},
                                  "comentarios": "${evalData.comentarios}"
                              })'>
                          <i class="fas fa-edit"></i>
                      </button>
                  </td>
              </tr>`;
      })
      .join("");
  } catch (error) {
    console.error("Error al cargar evaluaciones:", error);
    alert("Error al cargar las evaluaciones del empleado");
  }
}

// Función para editar una evaluación existente
function editarEvaluacion(evaluacion) {
  try {
    // Log detallado de los datos recibidos
    console.log(
      "%cDatos de evaluación recibidos",
      "color: blue; font-weight: bold"
    );
    console.log("Tipo de evaluacion:", typeof evaluacion);
    console.log("Evaluación completa:", evaluacion);
    console.log("ID evaluación:", evaluacion.id_evaluacion);

    // Validar que el ID de evaluación exista
    if (!evaluacion || !evaluacion.id_evaluacion) {
      console.error("ID de evaluación faltante:", evaluacion);
      throw new Error("ID de evaluación no proporcionado");
    }

    // Asignar valores y verificar cada asignación
    const campos = {
      evaluacionId: document.getElementById("evaluacionId"),
      mes: document.getElementById("mes"),
      anio: document.getElementById("anio"),
      calificacion: document.getElementById("calificacion"),
      comentarios: document.getElementById("comentarios"),
    };

    // Verificar que todos los elementos existen
    for (const [nombre, elemento] of Object.entries(campos)) {
      if (!elemento) {
        throw new Error(`Elemento ${nombre} no encontrado en el DOM`);
      }
    }

    // Asignar valores
    campos.evaluacionId.value = evaluacion.id_evaluacion;
    campos.mes.value = evaluacion.mes;
    campos.anio.value = evaluacion.anio;
    campos.calificacion.value = evaluacion.calificacion;
    campos.comentarios.value = evaluacion.comentarios || "";

    // Log de verificación
    console.log(
      "%cValores asignados al formulario",
      "color: green; font-weight: bold"
    );
    Object.entries(campos).forEach(([nombre, elemento]) => {
      console.log(`${nombre}:`, elemento.value);
    });

    // Cambiar el texto del botón
    const submitButton = document.querySelector(
      '#evaluacionForm button[type="submit"]'
    );
    if (submitButton) {
      submitButton.textContent = "Actualizar Evaluación";
    }
  } catch (error) {
    console.error(
      "%cError en editarEvaluacion:",
      "color: red; font-weight: bold",
      error
    );
    alert("Error al cargar los datos de la evaluación: " + error.message);
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

async function abrirModalEditar(empleado) {
  try {
    editandoId = empleado.id_usuarios;
    document.getElementById("modalTitle").textContent = "Editar Empleado";

    // Esperar a que se carguen los catálogos
    await cargarCatalogos();

    // Llenar todos los campos del formulario
    document.getElementById("nombre").value = empleado.nombre;
    document.getElementById("apellido").value = empleado.apellido;
    document.getElementById("sexo").value =
      empleado.sexo === "masculino" ? "M" : "F";
    document.getElementById("correo").value = empleado.correo;
    document.getElementById("edad").value = empleado.edad;
    document.getElementById("direccion").value = empleado.direccion;
    document.getElementById("ocupacion").value = empleado.ocupacion;
    document.getElementById("id_departamento").value = empleado.id_departamento;
    document.getElementById("id_estado").value = empleado.id_estado;
    document.getElementById("estatus").value = empleado.estatus;

    // Mostrar el modal
    modal.style.display = "block";
  } catch (error) {
    console.error("Error al abrir modal de edición:", error);
    alert("Error al cargar los datos del empleado");
  }
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

  // Inicializar el modal de evaluación
  const evaluacionModal = document.getElementById("evaluacionModal");
  const evaluacionForm = document.getElementById("evaluacionForm");

  // Añade esta función para debug
  function logFormData(formData) {
    console.group("Datos del formulario");
    console.log("ID Evaluación:", formData.evaluacionId);
    console.log("ID Usuario:", formData.id_usuario);
    console.log("Mes:", formData.mes);
    console.log("Año:", formData.anio);
    console.log("Calificación:", formData.calificacion);
    console.log("Comentarios:", formData.comentarios);
    console.groupEnd();
  }

  // En tu evento submit del formulario
  evaluacionForm.addEventListener("submit", async function (e) {
    e.preventDefault();

    const evaluacionId = document.getElementById("evaluacionId").value;
    const empleadoId = document.getElementById("empleadoIdEval").value;

    console.log("Iniciando actualización de evaluación");
    console.log("ID Evaluación:", evaluacionId);
    console.log("ID Empleado:", empleadoId);

    if (!evaluacionId) {
      console.error("No hay ID de evaluación");
      alert("Error: No se encontró el ID de la evaluación");
      return;
    }

    const datosEvaluacion = {
      id_usuario: empleadoId,
      mes: parseInt(document.getElementById("mes").value),
      anio: parseInt(document.getElementById("anio").value),
      calificacion: parseFloat(document.getElementById("calificacion").value),
      comentarios: document.getElementById("comentarios").value,
    };

    logFormData({ evaluacionId, ...datosEvaluacion });

    try {
      const url = `actualizar_evaluacion.php?id=${evaluacionId}`;
      console.log("URL de la petición:", url);
      console.log("Datos a enviar:", datosEvaluacion);

      const response = await fetch(url, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(datosEvaluacion),
      });

      const result = await response.json();
      console.log("Respuesta del servidor:", result);

      if (result.success) {
        console.log("Actualización exitosa");
        await cargarEvaluacionesEmpleado(empleadoId);
        alert("Evaluación actualizada exitosamente");
      } else {
        throw new Error(
          result.error || "Error desconocido en la actualización"
        );
      }
    } catch (error) {
      console.error("Error en la actualización:", error);
      alert("Error al actualizar la evaluación: " + error.message);
    }
  });

  const empleadoForm = document.getElementById("empleadoForm");
  if (empleadoForm) {
    empleadoForm.addEventListener("submit", async function (e) {
      e.preventDefault();

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
        let url = "crud_operations.php?action=";
        url += editandoId ? `update&id=${editandoId}` : "create";

        const response = await fetch(url, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(datosEmpleado),
        });

        const result = await response.json();

        if (result.success) {
          alert(
            editandoId
              ? "Empleado actualizado exitosamente"
              : "Empleado creado exitosamente"
          );
          cerrarModal();

          // Obtener el estado seleccionado actualmente
          const estadoSeleccionado = document.getElementById("estado").value;

          // Recargar datos usando el evento change del select
          const estadoSelect = document.getElementById("estado");
          if (estadoSelect) {
            const event = new Event("change");
            estadoSelect.dispatchEvent(event);
          }

          // Actualizar estadísticas
          await cargarEstadisticas();
        } else {
          throw new Error(result.error || "Error al procesar empleado");
        }
      } catch (error) {
        console.error("Error completo:", error);
        alert("Error al procesar empleado: " + error.message);
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
              <td>${empleado.promedio_calificacion || "Sin evaluaciones"}</td>
              <td class="action-buttons">
                  <button class="btn-evaluate" onclick='abrirModalEvaluacion(${
                    empleado.id_usuarios
                  }, "${empleado.nombre} ${empleado.apellido}")'>
                      <i class="fas fa-star"></i>
                  </button>
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

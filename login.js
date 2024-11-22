document.addEventListener("DOMContentLoaded", function () {
  const container = document.getElementById("container");
  const registerBtn = document.getElementById("register");
  const loginBtn = document.getElementById("login");

  // Manejar el cambio entre Login y Registro
  if (registerBtn && loginBtn) {
    registerBtn.addEventListener("click", () => {
      container.classList.add("active");
    });

    loginBtn.addEventListener("click", () => {
      container.classList.remove("active");
    });
  }

  // Validación del Formulario de Iniciar Sesión
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      e.preventDefault();

      const usernameElement = loginForm.querySelector('input[name="username"]');
      const passwordElement = loginForm.querySelector('input[name="password"]');

      if (!usernameElement || !passwordElement) {
        alert("Error: No se pudo encontrar el campo de usuario o contraseña.");
        return;
      }

      const username = usernameElement.value.trim();
      const password = passwordElement.value.trim();

      if (username === "" || password === "") {
        alert("Por favor, complete todos los campos.");
        return;
      }

      const formData = new FormData(loginForm);

      fetch("validar_login.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            window.location.href = "index.html";
          } else {
            alert(data.mensaje || "Error al iniciar sesión");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("Error al procesar la solicitud");
        });
    });
  }

  // Validación del Formulario de Registro
  const registroForm = document.getElementById("registroForm");
  if (registroForm) {
    registroForm.addEventListener("submit", function (e) {
      const passwordElement = registroForm.querySelector(
        'input[name="password"]'
      );
      const confirmPasswordElement = registroForm.querySelector(
        'input[name="confirm-password"]'
      );

      if (!passwordElement || !confirmPasswordElement) {
        alert("Error: No se pudo encontrar el campo de contraseña.");
        return;
      }

      const password = passwordElement.value.trim();
      const confirmPassword = confirmPasswordElement.value.trim();

      if (password === "" || confirmPassword === "") {
        e.preventDefault();
        alert("Por favor, complete todos los campos.");
        return;
      }

      if (password !== confirmPassword) {
        e.preventDefault();
        alert("Las contraseñas no coinciden. Inténtelo de nuevo.");
        return;
      }
    });
  }

  // Código para alternar entre formularios en diseño móvil
  const loginFormContainer = container.querySelector(".sign-in");
  const registerFormContainer = container.querySelector(".sign-up");

  // Crear botones para alternar en vista móvil
  const mobileButtons = document.createElement("div");
  mobileButtons.classList.add("mobile-buttons");

  const loginToggleButton = document.createElement("button");
  loginToggleButton.textContent = "Iniciar Sesión";
  loginToggleButton.classList.add("active");

  const registerToggleButton = document.createElement("button");
  registerToggleButton.textContent = "Crear Cuenta";

  mobileButtons.appendChild(loginToggleButton);
  mobileButtons.appendChild(registerToggleButton);
  container.insertBefore(mobileButtons, loginFormContainer);

  console.log("Mobile buttons added to the DOM."); // Mensaje para verificar en consola

  // Funciones para mostrar el formulario correspondiente
  function showLogin() {
    loginFormContainer.classList.add("active");
    registerFormContainer.classList.remove("active");
    loginToggleButton.classList.add("active");
    registerToggleButton.classList.remove("active");
    mobileButtons.style.display = "flex"; // Asegura que los botones estén visibles
  }

  function showRegister() {
    registerFormContainer.classList.add("active");
    loginFormContainer.classList.remove("active");
    registerToggleButton.classList.add("active");
    loginToggleButton.classList.remove("active");
    mobileButtons.style.display = "flex"; // Asegura que los botones estén visibles
  }

  // Eventos de clic para alternar entre formularios
  loginToggleButton.addEventListener("click", showLogin);
  registerToggleButton.addEventListener("click", showRegister);

  // Inicializar mostrando el formulario de inicio de sesión
  showLogin();
});

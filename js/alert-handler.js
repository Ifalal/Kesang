document.addEventListener("DOMContentLoaded", function () {
  const loginMessage = document.body.getAttribute("data-login-message");
  const registerMessage = document.body.getAttribute("data-register-message");

  if (loginMessage) {
    const loginModal = new bootstrap.Modal(document.getElementById("loginModal"));
    loginModal.show();
    alert(loginMessage);
  }

  if (registerMessage) {
    const registerModal = new bootstrap.Modal(document.getElementById("registerModal"));
    registerModal.show();
    alert(registerMessage);
  }
});

document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("loginForm");
  if (!form) return;

  setupPasswordToggle("loginPassword", "togglePassword");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const fields = [
      { id: "loginEmail", rules: ["required", "email"] },
      { id: "loginPassword", rules: ["required"] }
    ];

    const isValid = validateFields(fields);
    if (!isValid) return;

    const email = document.getElementById("loginEmail").value;
    const password = document.getElementById("loginPassword").value;

    try {
      const res = await fetch("/data/login.json");
      const users = await res.json();

      const user = users.find(
        u => u.username === email && u.password === password
      );

      if (!user) {
        showAuthError("Invalid email or password");
        return;
      }

      // Success
      localStorage.setItem("isLoggedIn", "true");
      localStorage.setItem("userRole", user.role);
       localStorage.setItem("userAccountType", user.accountType);
      localStorage.setItem("username", user.username);

      if (user.accountType === "admin") {
        window.location.href = "/dashboard/admin";
      } else {
        window.location.href = "/dashboard/customer";
      }

    } catch (err) {
      console.error("Login failed:", err);
    }
  });

});

/* Show authentication error under password only */
function showAuthError(message) {
  const passwordInput = document.getElementById("loginPassword");
  if (!passwordInput) return;

  passwordInput.classList.add("border-rose-300", "focus:border-rose-400", "focus:ring-rose-200");
  passwordInput.setAttribute("aria-invalid", "true");

  const group = (typeof getFieldGroup === "function" ? getFieldGroup(passwordInput) : passwordInput.closest("[data-field-group]")) || passwordInput.parentElement;
  const errorEl = group?.querySelector("[data-field-error]") || group?.querySelector(".error");

  if (errorEl) {
    errorEl.textContent = message;
    errorEl.classList.remove("hidden");
  }
}

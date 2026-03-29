document.addEventListener("DOMContentLoaded", () => {

  const form = document.getElementById("forgotForm");
  if (!form) return;
  const submitBtn = form.querySelector('button[type="submit"]');

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const fields = [
      { id: "forgotEmail", rules: ["required", "email"] }
    ];

    const isValid = validateFields(fields);
    if (!isValid) return;

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.classList.add("is-loading");
      submitBtn.setAttribute("aria-disabled", "true");
    }

    document.getElementById("resetStatus").textContent =
      "Reset link sent to your email";

    if (submitBtn) {
      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.classList.remove("is-loading");
        submitBtn.setAttribute("aria-disabled", "false");
      }, 500);
    }
  });

});

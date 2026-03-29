document.addEventListener("DOMContentLoaded", () => {
  const nextBtn = document.getElementById("nextBtn");
  const backBtn = document.getElementById("backBtn");
  const submitBtn = document.getElementById("signupSubmitBtn");
  const form = document.getElementById("signupForm");
  const stepNodes = Array.from(document.querySelectorAll("[data-signup-step]"));
  const formSteps = Array.from(document.querySelectorAll("[data-signup-panel]"));
  const connectors = Array.from(document.querySelectorAll("[data-signup-connector]"));
  const stateSelect = document.getElementById("state");
  const progressBar = document.getElementById("signupProgressBar");
  const currentStepLabel = document.getElementById("signupCurrentStep");
  const currentStepName = document.getElementById("signupCurrentLabel");
  const stepLabels = ["Personal Details", "Address"];
  const emailInput = document.getElementById("signupEmail");
  const emailOtpInput = document.getElementById("signupEmailOtp");
  const sendEmailOtpBtn = document.getElementById("sendEmailOtpBtn");
  const verifyEmailOtpBtn = document.getElementById("verifyEmailOtpBtn");
  const emailVerifiedIcon = document.getElementById("signupEmailVerifiedIcon");
  const emailOtpBlock = document.getElementById("signupEmailOtpBlock");
  const emailOtpStatus = document.getElementById("signupEmailOtpStatus");
  const emailOtpError = document.getElementById("signupEmailOtpError");
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";
  const emailOtpSendUrl = form?.dataset.emailOtpSendUrl || "";
  const emailOtpVerifyUrl = form?.dataset.emailOtpVerifyUrl || "";
  const initialVerifiedEmail = form?.dataset.emailOtpInitialEmail || "";
  const normalizedInitialVerifiedEmail = normalizeEmail(initialVerifiedEmail);
  const normalizedCurrentEmail = normalizeEmail(emailInput?.value || "");
  const emailOtpFlowEnabled = Boolean(
      emailInput &&
      emailOtpInput &&
      sendEmailOtpBtn &&
      verifyEmailOtpBtn &&
      emailVerifiedIcon &&
      emailOtpBlock &&
      emailOtpStatus &&
      emailOtpError &&
      emailOtpSendUrl !== "" &&
      emailOtpVerifyUrl !== ""
  );

  let emailOtpVerified =
    form?.dataset.emailOtpInitialVerified === "true" &&
    normalizedInitialVerifiedEmail !== "" &&
    normalizedCurrentEmail !== "" &&
    normalizedInitialVerifiedEmail === normalizedCurrentEmail;
  let verifiedEmail = emailOtpVerified ? normalizedInitialVerifiedEmail : "";

  if (!form || !nextBtn || !backBtn || !stepNodes.length || !formSteps.length) {
    return;
  }

  if (typeof setupPasswordToggle === "function") {
    setupPasswordToggle("signupPassword", "toggleSignupPassword");
    setupPasswordToggle("confirmPassword", "toggleConfirmPassword");
  }

  nextBtn.addEventListener("click", (event) => {
    event.preventDefault();

    if (!validateStep1()) {
      return;
    }

    toggleStep(2);
  });

  backBtn.addEventListener("click", () => {
    toggleStep(1);
  });

  form.addEventListener("submit", (event) => {
    event.preventDefault();

    if (!validateStep2()) {
      return;
    }

    // Business rule: the final submit stores one full customer name for the backend signup flow.
    setHiddenField(
      form,
      "name",
      `${document.getElementById("firstName").value} ${document.getElementById("lastName").value}`.trim()
    );

    if (submitBtn) {
      submitBtn.disabled = true;
      submitBtn.classList.add("cursor-not-allowed", "opacity-70");
      submitBtn.setAttribute("aria-disabled", "true");
    }

    form.submit();
  });

  if (emailOtpFlowEnabled) {
    // Business rule: any email change after verification should restart the verification cycle.
    emailInput.addEventListener("input", () => {
      resetEmailVerificationState();
      clearError(emailInput);
      updateNextButtonState();
    });

    // Business rule: OTP should stay numeric and easy for the customer to type.
    emailOtpInput.addEventListener("input", () => {
      emailOtpInput.value = emailOtpInput.value.replace(/\D/g, "").slice(0, 6);
      resetEmailOtpError();
    });

    sendEmailOtpBtn.addEventListener("click", async () => {
      if (!validateEmailBeforeOtpSend()) {
        return;
      }

      await sendEmailOtpRequest();
    });

    verifyEmailOtpBtn.addEventListener("click", async () => {
      if (!validateEmailBeforeOtpVerify()) {
        return;
      }

      await verifyEmailOtpRequest();
    });

    emailOtpInput.addEventListener("keydown", async (event) => {
      if (event.key !== "Enter") {
        return;
      }

      event.preventDefault();

      if (!validateEmailBeforeOtpVerify()) {
        return;
      }

      await verifyEmailOtpRequest();
    });
  }

  populateStates();
  toggleStep(Number(form.querySelector("[data-signup-panel]:not(.hidden)")?.dataset.step || 1));
  restoreVerifiedEmailState();
  syncEmailOtpUi();
  updateNextButtonState();

  window.addEventListener("pageshow", () => {
    restoreVerifiedEmailState();
    syncEmailOtpUi();
    updateNextButtonState();
  });

  function toggleStep(step) {
    formSteps.forEach((node) => {
      node.classList.toggle("hidden", Number(node.dataset.step) !== step);
    });

    stepNodes.forEach((node, index) => {
      const stepNumber = index + 1;
      const isCurrent = stepNumber === step;
      const isActive = stepNumber <= step;
      const circle = node.querySelector("[data-signup-step-circle]");
      const caption = node.querySelector("[data-signup-step-caption]");
      const label = node.querySelector("[data-signup-step-label]");

      node.classList.toggle("border-primary-200", isCurrent);
      node.classList.toggle("bg-primary-50/80", isCurrent);
      node.classList.toggle("shadow-sm", isCurrent);
      node.classList.toggle("border-slate-200", !isCurrent);
      node.classList.toggle("bg-white", !isCurrent);

      if (circle) {
        circle.classList.toggle("border-primary-600", isActive);
        circle.classList.toggle("bg-primary-600", isActive);
        circle.classList.toggle("text-white", isActive);
        circle.classList.toggle("border-slate-300", !isActive);
        circle.classList.toggle("bg-white", !isActive);
        circle.classList.toggle("text-slate-400", !isActive);
      }

      if (caption) {
        caption.classList.toggle("text-primary-600", isActive);
        caption.classList.toggle("text-slate-400", !isActive);
      }

      if (label) {
        label.classList.toggle("text-slate-900", isActive);
        label.classList.toggle("text-slate-500", !isActive);
      }
    });

    connectors.forEach((node, index) => {
      const isActive = index < step - 1;
      node.classList.toggle("bg-primary-600", isActive);
      node.classList.toggle("bg-slate-200", !isActive);
    });

    if (progressBar) {
      progressBar.setAttribute("aria-valuenow", String(step));
    }

    if (currentStepLabel) {
      currentStepLabel.textContent = String(step);
    }

    if (currentStepName) {
      currentStepName.textContent = stepLabels[step - 1] || "";
    }
  }

  function validateStep1() {
    const fields = [
      { id: "firstName", rules: ["required"] },
      { id: "lastName", rules: ["required"] },
      { id: "signupEmail", rules: ["required", "email"] },
      { id: "phone", rules: ["required", "phone"] },
      { id: "signupPassword", rules: ["required"] },
      { id: "confirmPassword", rules: ["required"] },
    ];

    let valid = validateFields(fields);
    const passwordInput = document.getElementById("signupPassword");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    if (password !== "" && confirmPassword !== "" && !validatePasswordMatch("signupPassword", "confirmPassword")) {
      valid = false;
    }

    // Business rule: the customer should not move to the next signup step unless the password already meets the minimum policy.
    if (password !== "" && password.length < 8) {
      setFieldError(passwordInput, "The password field must be at least 8 characters.");
      valid = false;
    }

    // Business rule: verified email is mandatory before the customer can continue to the next signup step.
    if (emailOtpFlowEnabled && !isCurrentEmailVerified()) {
      showEmailVerificationRequired();
      valid = false;
    }

    return valid;
  }

  function validateStep2() {
    return validateFields([
      { id: "addressLine1", rules: ["required"] },
      { id: "pincode", rules: ["required", "pincode"] },
      { id: "city", rules: ["required"] },
      { id: "state", rules: ["required"] },
    ]);
  }

  function validateEmailBeforeOtpSend() {
    resetEmailOtpError();
    clearEmailOtpStatus();

    return validateFields([{ id: "signupEmail", rules: ["required", "email"] }]);
  }

  function validateEmailBeforeOtpVerify() {
    resetEmailOtpError();

    if (!validateFields([{ id: "signupEmail", rules: ["required", "email"] }])) {
      return false;
    }

    const otpValue = emailOtpInput.value.trim();

    if (!/^\d{6}$/.test(otpValue)) {
      setEmailOtpError("Please enter the 6-digit OTP sent to your email.");
      return false;
    }

    return true;
  }

  async function sendEmailOtpRequest() {
    const normalizedEmail = normalizeEmail(emailInput.value);

    // Business rule: a freshly requested OTP always starts a new verification cycle for the current email.
    emailOtpVerified = false;
    verifiedEmail = "";
    syncEmailOtpUi();
    updateNextButtonState();

    setEmailOtpButtonState(sendEmailOtpBtn, true, "Sending...");

    try {
      const response = await fetch(emailOtpSendUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
          email: normalizedEmail,
        }),
      });

      const payload = await parseJsonResponse(response);

      if (!response.ok) {
        handleEmailOtpFailure(payload, "Unable to send OTP right now. Please try again.");
        return;
      }

      // Business message: after OTP send, show the verification panel and guide the customer to the next action.
      clearError(emailInput);
      showEmailOtpBlock();
      emailOtpInput.value = "";
      setEmailOtpStatus(payload.message || "OTP sent to your email successfully.", "success");
      emailOtpInput.focus();
      resetEmailOtpError();
    } catch (error) {
      setEmailOtpError("Unable to send OTP right now. Please try again.");
    } finally {
      setEmailOtpButtonState(sendEmailOtpBtn, false, "Get OTP");
      updateNextButtonState();
    }
  }

  async function verifyEmailOtpRequest() {
    const normalizedEmail = normalizeEmail(emailInput.value);
    const normalizedOtp = emailOtpInput.value.trim();

    setEmailOtpButtonState(verifyEmailOtpBtn, true, "Verifying...");

    try {
      const response = await fetch(emailOtpVerifyUrl, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "X-CSRF-TOKEN": csrfToken,
        },
        body: JSON.stringify({
          email: normalizedEmail,
          otp: normalizedOtp,
        }),
      });

      const payload = await parseJsonResponse(response);

      if (!response.ok) {
        handleEmailOtpFailure(payload, "Unable to verify OTP right now. Please try again.");
        return;
      }

      // Business rule: once OTP matches, mark this exact email as verified for the next signup step.
      emailOtpVerified = true;
      verifiedEmail = normalizedEmail;
      clearError(emailInput);
      resetEmailOtpError();
      setEmailOtpStatus(payload.message || "Email verified successfully. You can continue with signup now.", "success");
      syncEmailOtpUi();
    } catch (error) {
      setEmailOtpError("Unable to verify OTP right now. Please try again.");
    } finally {
      setEmailOtpButtonState(verifyEmailOtpBtn, false, "Verify OTP");
      updateNextButtonState();
    }
  }

  function updateNextButtonState() {
    const canMoveNext = !emailOtpFlowEnabled || isCurrentEmailVerified();

    nextBtn.disabled = !canMoveNext;
    nextBtn.classList.toggle("cursor-not-allowed", !canMoveNext);
    nextBtn.classList.toggle("opacity-70", !canMoveNext);
    nextBtn.setAttribute("aria-disabled", canMoveNext ? "false" : "true");
  }

  function isCurrentEmailVerified() {
    return emailOtpVerified && verifiedEmail !== "" && verifiedEmail === normalizeEmail(emailInput.value);
  }

  function resetEmailVerificationState() {
    const normalizedEmail = normalizeEmail(emailInput.value);

    if (normalizedEmail === verifiedEmail) {
      return;
    }

    emailOtpVerified = false;
    verifiedEmail = "";
    emailOtpInput.value = "";
    clearEmailOtpStatus();
    resetEmailOtpError();
    syncEmailOtpUi();
  }

  function showEmailVerificationRequired() {
    showEmailOtpBlock();
    sendEmailOtpBtn.classList.remove("hidden");
    emailVerifiedIcon.classList.add("hidden");
    setFieldError(emailInput, "Please verify your email with OTP before continuing.");
    setEmailOtpStatus("Please verify your email to continue to the address step.", "warning");
  }

  function syncEmailOtpUi() {
    if (!emailOtpFlowEnabled) {
      return;
    }

    const currentEmail = normalizeEmail(emailInput.value);
    const hasVerifiedEmail = isCurrentEmailVerified();

    sendEmailOtpBtn.classList.toggle("hidden", hasVerifiedEmail);
    emailVerifiedIcon.classList.toggle("hidden", !hasVerifiedEmail);

    if (hasVerifiedEmail) {
      hideEmailOtpBlock();
      clearEmailOtpStatus();

      return;
    }

    if (currentEmail === "") {
      clearEmailOtpStatus();
      resetEmailOtpError();
    } else if (emailOtpStatus.textContent.trim() === "Email verified successfully.") {
      clearEmailOtpStatus();
    }

    hideEmailOtpBlock();
  }

  function restoreVerifiedEmailState() {
    if (!emailOtpFlowEnabled) {
      return;
    }

    const currentEmail = normalizeEmail(emailInput.value);

    if (currentEmail === "" || verifiedEmail === "" || currentEmail !== verifiedEmail) {
      if (currentEmail === normalizedInitialVerifiedEmail && normalizedInitialVerifiedEmail !== "") {
        emailOtpVerified = true;
        verifiedEmail = normalizedInitialVerifiedEmail;
        return;
      }

      emailOtpVerified = false;
      verifiedEmail = "";
    }
  }

  function showEmailOtpBlock() {
    emailOtpBlock.classList.remove("hidden");
  }

  function hideEmailOtpBlock() {
    emailOtpBlock.classList.add("hidden");
  }

  function setEmailOtpStatus(message, type) {
    emailOtpStatus.textContent = message;
    emailOtpStatus.classList.remove("hidden", "text-emerald-600", "text-amber-600", "text-slate-600");

    if (type === "success") {
      emailOtpStatus.classList.add("text-emerald-600");
      return;
    }

    if (type === "warning") {
      emailOtpStatus.classList.add("text-amber-600");
      return;
    }

    emailOtpStatus.classList.add("text-slate-600");
  }

  function clearEmailOtpStatus() {
    emailOtpStatus.textContent = "";
    emailOtpStatus.classList.add("hidden");
    emailOtpStatus.classList.remove("text-emerald-600", "text-amber-600", "text-slate-600");
  }

  function setEmailOtpError(message) {
    emailOtpError.textContent = message;
    emailOtpError.classList.remove("hidden");
    emailOtpInput.classList.add(...errorInputClasses);
    emailOtpInput.setAttribute("aria-invalid", "true");
    emailOtpBlock.classList.remove("hidden");
  }

  function resetEmailOtpError() {
    emailOtpError.textContent = "";
    emailOtpError.classList.add("hidden");
    emailOtpInput.classList.remove(...errorInputClasses);
    emailOtpInput.removeAttribute("aria-invalid");
  }

  function handleEmailOtpFailure(payload, defaultMessage) {
    const errorMessage = payload?.message || defaultMessage;
    const errors = payload?.errors || {};

    if (Array.isArray(errors.email) && errors.email.length > 0) {
      clearEmailOtpStatus();
      setFieldError(emailInput, errors.email[0]);
      return;
    }

    if (Array.isArray(errors.otp) && errors.otp.length > 0) {
      setEmailOtpError(errors.otp[0]);
      return;
    }

    setEmailOtpError(errorMessage);
  }

  function setEmailOtpButtonState(button, isBusy, busyLabel) {
    const idleLabel = button.id === "sendEmailOtpBtn" ? "Get OTP" : "Verify OTP";

    button.disabled = isBusy;
    button.textContent = isBusy ? busyLabel : idleLabel;
    button.classList.toggle("cursor-not-allowed", isBusy);
    button.classList.toggle("opacity-70", isBusy);
  }

  function setFieldError(input, message) {
    const group = getFieldGroup(input);
    const errorElement = getErrorElement(group);

    input.classList.add(...errorInputClasses);
    input.setAttribute("aria-invalid", "true");

    if (errorElement) {
      errorElement.textContent = message;
      errorElement.classList.remove("hidden");
    }
  }

  function normalizeEmail(value) {
    return String(value || "").trim().toLowerCase();
  }

  async function parseJsonResponse(response) {
    try {
      return await response.json();
    } catch (error) {
      return null;
    }
  }

  function populateStates() {
    if (!stateSelect) {
      return;
    }

    const states = [
      "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chhattisgarh", "Goa",
      "Gujarat", "Haryana", "Himachal Pradesh", "Jharkhand", "Karnataka", "Kerala",
      "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland",
      "Odisha", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura",
      "Uttar Pradesh", "Uttarakhand", "West Bengal",
      "Andaman and Nicobar Islands", "Chandigarh", "Dadra and Nagar Haveli and Daman and Diu",
      "Delhi", "Jammu and Kashmir", "Ladakh", "Lakshadweep", "Puducherry",
    ];
    const selectedState = stateSelect.dataset.oldValue || "";

    states.forEach((state) => {
      const option = document.createElement("option");
      option.value = state;
      option.textContent = state;

      if (selectedState === state) {
        option.selected = true;
      }

      stateSelect.appendChild(option);
    });
  }

  function setHiddenField(formEl, name, value) {
    let hiddenField = formEl.querySelector(`input[name="${name}"]`);

    if (!hiddenField) {
      hiddenField = document.createElement("input");
      hiddenField.type = "hidden";
      hiddenField.name = name;
      formEl.appendChild(hiddenField);
    }

    hiddenField.value = value;
  }
});

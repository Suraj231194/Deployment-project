document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("contactForm");
    if (!form) return;
    const submitBtn = document.getElementById("contactSubmitBtn") || form.querySelector('button[type="submit"]');

    form.addEventListener("submit", function (e) {
        e.preventDefault();

        const fields = [
            { id: "name", rules: ["required"] },
            { id: "email", rules: ["required", "email"] },
            { id: "phone", rules: ["required", "phone"] },
            { id: "inquiryType", rules: ["required"] },
            { id: "message", rules: ["required"] }
        ];

        const isValid = validateFields(fields);

        if (!isValid) return false;

        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.classList.add("is-loading");
            submitBtn.setAttribute("aria-disabled", "true");
        }

        const formStatus = document.getElementById("formStatus");
        formStatus.textContent = "Your support request has been received. A ticket number has been sent to your email.";
        formStatus.classList.remove("error");
        formStatus.classList.add("success");

        const fullNameInput = document.getElementById("name");
        const emailInput = document.getElementById("email");
        const phoneInput = document.getElementById("phone");
        const inquiryTypeInput = document.getElementById("inquiryType");
        const messageInput = document.getElementById("message");

        console.log("Form submitted:", {
            name: fullNameInput?.value ?? "",
            email: emailInput?.value ?? "",
            phone: phoneInput?.value ?? "",
            inquiryType: inquiryTypeInput?.value ?? "",
            message: messageInput?.value ?? ""
        });

        form.reset();
        setTimeout(function () {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove("is-loading");
                submitBtn.setAttribute("aria-disabled", "false");
            }
        }, 400);
        return false;
    });

});

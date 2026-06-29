(function () {
    "use strict";
    var form = document.querySelector("[data-reset-form]");
    var message = document.querySelector("[data-auth-message]");
    var token = new URLSearchParams(window.location.search).get("token") || "";
    function show(text, success) {
        message.hidden = false;
        message.textContent = text;
        message.classList.toggle("is-success", !!success);
    }
    form.addEventListener("submit", function (event) {
        event.preventDefault();
        var password = form.elements.password.value;
        if (password !== form.elements.confirmPassword.value) {
            show("Şifrələr eyni deyil.");
            return;
        }
        var button = form.querySelector("button[type='submit']");
        button.disabled = true;
        fetch("api/student-auth.php?action=reset-password", {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ token: token, password: password })
        })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                if (!payload.ok) throw new Error(payload.message || "Şifrə yenilənmədi.");
                show(payload.message, true);
                window.setTimeout(function () { window.location.href = "login.html?mode=login"; }, 900);
            })
            .catch(function (error) { show(error.message); })
            .finally(function () { button.disabled = false; });
    });
}());

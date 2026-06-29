(function () {
    "use strict";
    var form = document.querySelector("[data-datalab-checkout]");
    if (!form) return;

    fetch("api/student-auth.php?action=session", { credentials: "same-origin" })
        .then(function (response) {
            if (!response.ok) {
                window.location.href = "login.html?mode=login&redirect=" + encodeURIComponent("checkout.html");
                return null;
            }
            return response.json();
        })
        .then(function (payload) {
            if (!payload || !payload.user) return;
            var names = String(payload.user.name || "").trim().split(/\s+/);
            if (form.elements.firstName) form.elements.firstName.value = names.shift() || "";
            if (form.elements.lastName) form.elements.lastName.value = names.join(" ");
            if (form.elements.email) {
                form.elements.email.value = payload.user.email || "";
                form.elements.email.readOnly = true;
            }
        })
        .catch(function () {
            window.location.href = "login.html?mode=login&redirect=" + encodeURIComponent("checkout.html");
        });
}());

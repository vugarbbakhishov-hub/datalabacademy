<!doctype html>
<html lang="az">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DatalabAcademy | E-poçt təsdiqi</title>
    <meta name="robots" content="noindex,nofollow">
    <link rel="stylesheet" href="assets/css/plugins/euclid-circulara.css">
    <link rel="stylesheet" href="assets/css/datalab-auth.css?v=20260618-verify">
</head>
<body class="datalab-auth-page">
    <main class="datalab-auth-stage">
        <section class="datalab-auth-visual">
            <a class="datalab-auth-brand" href="index.php"><img src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"></a>
            <div class="datalab-auth-copy"><span class="eyebrow">Hesab təsdiqi</span><h1>Öyrənmə hesabınız qorunur.</h1><p>E-poçt təsdiqi hesabın həqiqətən sizə aid olduğunu yoxlayır.</p></div>
        </section>
        <section class="datalab-auth-card">
            <div class="datalab-auth-card-head"><img src="assets/images/logo/datalab-logo-transparent.png" alt="DatalabAcademy"><h2 data-verify-title>Yoxlanılır...</h2><p data-verify-message>Təsdiq linki yoxlanılır.</p></div>
            <a class="rbt-btn btn-gradient datalab-auth-submit" href="login.html?mode=login" data-verify-login hidden>Daxil ol</a>
        </section>
    </main>
    <script>
    (function () {
        var token = new URLSearchParams(location.search).get("token") || "";
        fetch("api/student-auth.php?action=verify-email&token=" + encodeURIComponent(token), { credentials: "same-origin" })
            .then(function (response) { return response.json(); })
            .then(function (payload) {
                document.querySelector("[data-verify-title]").textContent = payload.ok ? "E-poçt təsdiqləndi" : "Link etibarsızdır";
                document.querySelector("[data-verify-message]").textContent = payload.message;
                document.querySelector("[data-verify-login]").hidden = !payload.ok;
            });
    }());
    </script>
</body>
</html>

/* DatalabAcademy admin əlavəsi: ödəniş sorğuları (kart-kart təsdiq),
   ödəniş kartları idarəsi (dizaynlı) və dərs izləmə (enrollments).
   datalab-admin.js-ə toxunmadan müstəqil işləyir. */
(function () {
    "use strict";
    if (window.__dlShopAdmin) return;
    window.__dlShopAdmin = true;

    var API = "api/admin.php";
    var state = { csrf: "", shopOrders: [], paymentCards: [], students: [] };
    var orderFilter = "pending";
    var enrollSearch = "";

    function esc(s) { var d = document.createElement("div"); d.textContent = s == null ? "" : String(s); return d.innerHTML; }
    function $(sel) { return document.querySelector(sel); }

    /* ---------------- gözəl təsdiq modalı (native confirm əvəzinə) ---------------- */
    function injectModalCss() {
        if (document.getElementById("dl-modal-css")) return;
        var st = document.createElement("style");
        st.id = "dl-modal-css";
        st.textContent =
            ".dl-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);-webkit-backdrop-filter:blur(3px);backdrop-filter:blur(3px);display:flex;align-items:center;justify-content:center;z-index:99999;animation:dlmf .15s ease}" +
            "@keyframes dlmf{from{opacity:0}to{opacity:1}}" +
            ".dl-modal{background:#fff;border-radius:18px;padding:30px 28px;max-width:430px;width:90%;text-align:center;box-shadow:0 30px 80px rgba(2,6,23,.35);animation:dlmp .18s ease}" +
            "@keyframes dlmp{from{transform:translateY(14px) scale(.96);opacity:0}to{transform:none;opacity:1}}" +
            ".dl-modal-ic{width:66px;height:66px;border-radius:50%;display:grid;place-items:center;margin:0 auto 16px;font-size:30px}" +
            ".dl-modal-ic.ok{background:rgba(49,93,245,.12);color:#315df5}.dl-modal-ic.danger{background:rgba(239,68,68,.12);color:#ef4444}" +
            ".dl-modal h4{margin:0 0 8px;font-size:20px;color:#0f172a;font-weight:800}" +
            ".dl-modal p{margin:0 0 24px;color:#64748b;font-size:14px;line-height:1.55}" +
            ".dl-modal-actions{display:flex;gap:10px;justify-content:center}" +
            ".dl-modal-actions button{padding:11px 24px;border-radius:11px;font-weight:700;font-size:14px;cursor:pointer;border:0;transition:.15s}" +
            ".dl-modal-cancel{background:#eef1f7;color:#475569}.dl-modal-cancel:hover{background:#e2e8f0}" +
            ".dl-modal-ok{background:linear-gradient(135deg,#315df5,#9b51e0);color:#fff}.dl-modal-ok.danger{background:#ef4444}.dl-modal-ok:hover{filter:brightness(1.06)}";
        document.head.appendChild(st);
    }
    function dlConfirm(opts, onYes) {
        injectModalCss();
        var o = opts || {};
        var ov = document.createElement("div");
        ov.className = "dl-modal-overlay";
        ov.innerHTML = '<div class="dl-modal" role="dialog" aria-modal="true">'
            + '<div class="dl-modal-ic ' + (o.danger ? "danger" : "ok") + '"><i class="feather-' + (o.danger ? "alert-triangle" : "check-circle") + '"></i></div>'
            + '<h4>' + esc(o.title || "Təsdiq") + '</h4>'
            + '<p>' + esc(o.message || "") + '</p>'
            + '<div class="dl-modal-actions">'
            + '<button class="dl-modal-cancel" type="button">' + esc(o.cancelText || "Ləğv et") + '</button>'
            + '<button class="dl-modal-ok ' + (o.danger ? "danger" : "") + '" type="button">' + esc(o.confirmText || "Təsdiqlə") + '</button>'
            + '</div></div>';
        document.body.appendChild(ov);
        if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
        function close() { if (ov.parentNode) ov.parentNode.removeChild(ov); document.removeEventListener("keydown", onKey); }
        function onKey(e) { if (e.key === "Escape") close(); }
        ov.querySelector(".dl-modal-cancel").addEventListener("click", close);
        ov.addEventListener("click", function (e) { if (e.target === ov) close(); });
        ov.querySelector(".dl-modal-ok").addEventListener("click", function () { close(); onYes(); });
        document.addEventListener("keydown", onKey);
    }
    // datalab-admin.js və digər modullar üçün qlobal əlçatan et
    window.dlConfirm = dlConfirm;

    function fetchState() {
        return fetch(API + "?action=state", { headers: { "Accept": "application/json" } })
            .then(function (r) { return r.json(); })
            .then(function (p) {
                if (p && p.ok && p.data) {
                    state.csrf = p.data.shopCsrf || state.csrf;
                    state.shopOrders = p.data.shopOrders || [];
                    state.paymentCards = p.data.paymentCards || [];
                    state.students = p.data.students || [];
                    renderAll();
                }
            }).catch(function () {});
    }

    function applyData(p) {
        if (p && p.ok && p.data) {
            state.csrf = p.data.shopCsrf || state.csrf;
            state.shopOrders = p.data.shopOrders || [];
            state.paymentCards = p.data.paymentCards || [];
            state.students = p.data.students || [];
            renderAll();
        }
        return p;
    }
    function doPost(action, body) {
        return fetch(API + "?action=" + action, {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-Token": state.csrf },
            body: JSON.stringify(body || {})
        }).then(function (r) { return r.json(); });
    }
    function post(action, body) {
        // csrf hazır deyilsə əvvəlcə state yüklə (giriş tamamlandıqdan sonra token gəlir)
        var prep = state.csrf ? Promise.resolve() : fetchState();
        return prep.then(function () { return doPost(action, body); }).then(function (p) {
            if (p && !p.ok && p.message && /CSRF/i.test(p.message)) {
                // token köhnəlibsə təzələ və bir dəfə yenidən cəhd et
                return fetchState().then(function () { return doPost(action, body); }).then(applyData);
            }
            return applyData(p);
        });
    }

    /* ---------------- 1) Ödəniş sorğuları (shop orders) ---------------- */
    function statusBadge(s) {
        var map = { pending: ["#b45309", "#fef3c7", "Gözləyir"], paid: ["#15803d", "#dcfce7", "Təsdiqlənib"], rejected: ["#b91c1c", "#fee2e2", "Rədd"] };
        var m = map[s] || ["#475569", "#e2e8f0", s];
        return '<span style="color:' + m[0] + ';background:' + m[1] + ';padding:3px 9px;border-radius:20px;font-size:12px;font-weight:700">' + esc(m[2]) + '</span>';
    }

    function renderOrders() {
        var tbody = $("[data-shoporders-table]");
        if (!tbody) return;
        var pending = state.shopOrders.filter(function (o) { return o.status === "pending"; });
        var badge = $("[data-nav-payments]");
        if (badge) { badge.textContent = pending.length; badge.style.display = pending.length ? "" : "none"; }

        var rows = state.shopOrders.filter(function (o) { return orderFilter === "all" ? true : o.status === orderFilter; });
        if (!rows.length) { tbody.innerHTML = '<tr><td colspan="8" class="dl-admin-empty-cell">Sorğu yoxdur.</td></tr>'; return; }
        tbody.innerHTML = rows.map(function (o) {
            var items = (o.items || []).map(function (it) { return esc(it.title) + " ($" + Number(it.amount).toFixed(0) + ")"; }).join("<br>");
            var act = o.status === "pending"
                ? '<button class="dl-admin-primary" type="button" data-approve="' + esc(o.id) + '"><i class="feather-check"></i>Təsdiqlə</button> <button type="button" class="dl-bulk-danger" data-reject="' + esc(o.id) + '"><i class="feather-x"></i>Rədd</button>'
                : "—";
            return '<tr>'
                + '<td><b>' + esc(o.id) + '</b>' + (o.note ? '<br><small style="color:#94a3b8">' + esc(o.note) + '</small>' : '') + '</td>'
                + '<td>' + esc(o.customer) + '<br><small style="color:#94a3b8">' + esc(o.email) + '</small></td>'
                + '<td style="font-size:13px">' + items + '</td>'
                + '<td>' + esc(o.cardBank || o.cardId || "") + '</td>'
                + '<td><b>$' + Number(o.amount).toFixed(2) + '</b></td>'
                + '<td>' + esc(o.date) + '</td>'
                + '<td>' + statusBadge(o.status) + '</td>'
                + '<td style="white-space:nowrap">' + act + '</td>'
                + '</tr>';
        }).join("");

        tbody.querySelectorAll("[data-approve]").forEach(function (b) {
            b.addEventListener("click", function () {
                dlConfirm({ title: "Ödənişi təsdiqlə", message: "Ödənişi təsdiqləyirsiniz? Tələbəyə kurslara giriş dərhal açılacaq.", confirmText: "Bəli, təsdiqlə" }, function () {
                    b.disabled = true;
                    post("order-approve", { id: b.getAttribute("data-approve") });
                });
            });
        });
        tbody.querySelectorAll("[data-reject]").forEach(function (b) {
            b.addEventListener("click", function () {
                dlConfirm({ title: "Ödənişi rədd et", message: "Bu ödəniş sorğusunu rədd edirsiniz?", confirmText: "Bəli, rədd et", danger: true }, function () {
                    b.disabled = true;
                    post("order-reject", { id: b.getAttribute("data-reject") });
                });
            });
        });
    }

    /* ---------------- 2) Ödəniş kartları ---------------- */
    var THEMES = {
        violet: "linear-gradient(135deg,#7b2ff7,#f107a3)", ocean: "linear-gradient(135deg,#2193b0,#6dd5ed)",
        emerald: "linear-gradient(135deg,#0f9b8e,#38ef7d)", sunset: "linear-gradient(135deg,#ff512f,#f09819)",
        dark: "linear-gradient(135deg,#232526,#414345)", gold: "linear-gradient(135deg,#b8860b,#ffd700)"
    };
    function fmtNum(n) { n = String(n || "").replace(/\s+/g, ""); return n.replace(/(.{4})/g, "$1 ").trim(); }

    function renderCards() {
        var grid = $("[data-cards-grid]");
        if (!grid) return;
        if (!state.paymentCards.length) { grid.innerHTML = '<div class="dl-admin-empty-cell">Hələ kart əlavə edilməyib. “Yeni kart” düyməsi ilə əlavə edin.</div>'; return; }
        grid.innerHTML = state.paymentCards.map(function (c) {
            var grad = THEMES[c.theme] || THEMES.violet;
            var hidden = c.status === "hidden" ? '<span style="position:absolute;top:10px;left:14px;z-index:2;font-size:11px;background:rgba(0,0,0,.35);padding:2px 8px;border-radius:10px">Gizli</span>' : '';
            return '<div class="dl-admin-bankcard" style="background:' + grad + '">'
                + hidden
                + '<div class="bc-bank">' + esc(c.bank || "Bank") + '</div>'
                + '<div class="bc-num">' + esc(fmtNum(c.number)) + '</div>'
                + '<div class="bc-bottom"><span>' + esc(c.holder || "") + '</span></div>'
                + (c.note ? '<div class="bc-note">' + esc(c.note) + '</div>' : '')
                + '<div class="bc-actions"><button type="button" data-card-edit="' + esc(c.id) + '"><i class="feather-edit-2"></i></button>'
                + '<button type="button" data-card-del="' + esc(c.id) + '"><i class="feather-trash-2"></i></button></div>'
                + '</div>';
        }).join("");

        grid.querySelectorAll("[data-card-edit]").forEach(function (b) {
            b.addEventListener("click", function () { openCardForm(findCard(b.getAttribute("data-card-edit"))); });
        });
        grid.querySelectorAll("[data-card-del]").forEach(function (b) {
            b.addEventListener("click", function () {
                dlConfirm({ title: "Kartı sil", message: "Bu ödəniş kartını silmək istəyirsiniz?", confirmText: "Sil", danger: true }, function () {
                    post("card-delete", { id: b.getAttribute("data-card-del") });
                });
            });
        });
    }
    function findCard(id) { for (var i = 0; i < state.paymentCards.length; i++) if (state.paymentCards[i].id === id) return state.paymentCards[i]; return null; }

    function openCardForm(card) {
        var form = $("[data-card-form]"); if (!form) return;
        form.hidden = false;
        form.elements.id.value = card ? card.id : "";
        form.elements.bank.value = card ? (card.bank || "") : "";
        form.elements.number.value = card ? (card.number || "") : "";
        form.elements.holder.value = card ? (card.holder || "") : "";
        form.elements.note.value = card ? (card.note || "") : "";
        form.elements.theme.value = card ? (card.theme || "violet") : "violet";
        form.elements.status.value = card ? (card.status || "active") : "active";
        form.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function wireCardForm() {
        var form = $("[data-card-form]"); if (!form || form.__wired) return; form.__wired = true;
        var newBtn = $("[data-card-new]"), cancel = $("[data-card-cancel]");
        if (newBtn) newBtn.addEventListener("click", function () { openCardForm(null); });
        if (cancel) cancel.addEventListener("click", function () { form.hidden = true; });
        // kart nömrəsi: hər 4 rəqəmdən bir boşluq
        var numInput = form.elements.number;
        if (numInput) numInput.addEventListener("input", function () {
            var pos = numInput.selectionStart;
            var before = numInput.value;
            var digits = numInput.value.replace(/\D/g, "").slice(0, 19);
            numInput.value = digits.replace(/(.{4})/g, "$1 ").trim();
            if (pos === before.length) { numInput.selectionStart = numInput.selectionEnd = numInput.value.length; }
        });
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            var btn = form.querySelector("button[type=submit]"); if (btn) btn.disabled = true;
            post("card-save", {
                id: form.elements.id.value, bank: form.elements.bank.value, number: form.elements.number.value,
                holder: form.elements.holder.value, note: form.elements.note.value,
                theme: form.elements.theme.value, status: form.elements.status.value
            }).then(function (p) {
                if (btn) btn.disabled = false;
                if (p && p.ok) { form.hidden = true; }
                else { alert((p && p.message) || "Xəta."); }
            });
        });
    }

    /* ---------------- 3) Dərs izləmə (enrollments) ---------------- */
    function renderEnrollments() {
        var root = $("[data-enrollments-root]");
        if (!root) return;
        var list = state.students.filter(function (s) {
            return (s.enrollments && s.enrollments.length);
        });
        if (enrollSearch) {
            var q = enrollSearch.toLowerCase();
            list = list.filter(function (s) { return (s.name + " " + s.email).toLowerCase().indexOf(q) >= 0; });
        }
        if (!list.length) { root.innerHTML = '<div class="dl-admin-empty-cell">Qeydiyyatlı tələbə yoxdur.</div>'; return; }
        root.innerHTML = list.map(function (s) {
            var rows = s.enrollments.map(function (e, ix) {
                var rk = "enr-" + s.id + "-" + ix;
                var total = e.totalLessons || 0;
                var pct = total ? Math.min(100, Math.round(e.completedLessons / total * 100)) : 0;
                var bar = '<div style="background:#eef1f7;border-radius:6px;height:8px;width:140px;overflow:hidden;display:inline-block;vertical-align:middle"><span style="display:block;height:100%;background:linear-gradient(90deg,#315df5,#9b51e0);width:' + pct + '%"></span></div> <b>' + pct + '%</b>';
                var st = e.status === "active"
                    ? '<button type="button" class="dl-bulk-danger" data-enr-revoke="' + e.id + '">Girişi ləğv et</button>'
                    : '<span style="color:#b91c1c;font-weight:700">Ləğv edilib</span> <button type="button" data-enr-restore="' + e.id + '">Bərpa et</button>';
                var statusBadgeHtml = e.status === "active"
                    ? '<span style="color:#15803d;font-weight:700">Aktiv</span>'
                    : '<span style="color:#b91c1c;font-weight:700">Ləğv</span>';
                var main = '<tr><td>' + esc(e.title)
                    + ' <button type="button" data-lessons-toggle="' + rk + '" data-student="' + s.id + '" data-course="' + esc(e.courseId) + '" style="margin-left:6px;background:#eef1f7;border:0;border-radius:8px;padding:3px 9px;cursor:pointer;font-size:12px;font-weight:600">Dərslər ▾</button>'
                    + '</td><td>' + statusBadgeHtml + '</td><td>'
                    + e.completedLessons + ' / ' + total + ' tamamlanıb (' + e.watchedLessons + ' baxılıb) ' + bar
                    + '</td><td>' + esc((e.enrolledAt || "").slice(0, 10)) + '</td><td>' + st + '</td></tr>';
                var detail = '<tr data-lessons-row="' + rk + '" hidden><td colspan="5" style="background:#f8fafc"><div data-lessons-body="' + rk + '" style="padding:8px 4px;color:#64748b">Yüklənir…</div></td></tr>';
                return main + detail;
            }).join("");
            return '<div class="dl-enroll-card" style="border:1px solid #e8ecf4;border-radius:12px;padding:14px 16px;margin-bottom:14px">'
                + '<div style="font-weight:800;margin-bottom:8px">' + esc(s.name) + ' <small style="color:#94a3b8;font-weight:500">' + esc(s.email) + '</small></div>'
                + '<div class="dl-admin-table-wrap"><table class="dl-admin-table"><thead><tr><th>Kurs</th><th>Status</th><th>İrəliləyiş</th><th>Qoşulub</th><th></th></tr></thead><tbody>' + rows + '</tbody></table></div>'
                + '</div>';
        }).join("");

        root.querySelectorAll("[data-enr-revoke]").forEach(function (b) {
            b.addEventListener("click", function () {
                dlConfirm({ title: "Girişi ləğv et", message: "Bu tələbənin kursa girişini ləğv edirsiniz?", confirmText: "Ləğv et", danger: true }, function () {
                    post("enrollment-set-status", { id: parseInt(b.getAttribute("data-enr-revoke"), 10), status: "revoked" });
                });
            });
        });
        root.querySelectorAll("[data-enr-restore]").forEach(function (b) {
            b.addEventListener("click", function () { post("enrollment-set-status", { id: parseInt(b.getAttribute("data-enr-restore"), 10), status: "active" }); });
        });
        // Dərslər detalını aç/bağla (lazy fetch)
        root.querySelectorAll("[data-lessons-toggle]").forEach(function (b) {
            b.addEventListener("click", function () {
                var rk = b.getAttribute("data-lessons-toggle");
                var row = root.querySelector('[data-lessons-row="' + rk + '"]');
                if (!row) return;
                row.hidden = !row.hidden;
                b.innerHTML = row.hidden ? "Dərslər ▾" : "Dərslər ▴";
                if (!row.hidden && !b.__loaded) {
                    b.__loaded = true;
                    loadLessons(b.getAttribute("data-student"), b.getAttribute("data-course"), rk);
                }
            });
        });
    }

    function loadLessons(studentId, courseId, rk) {
        var body = document.querySelector('[data-lessons-body="' + rk + '"]');
        if (!body) return;
        fetch(API + "?action=student-lessons&student=" + encodeURIComponent(studentId) + "&course=" + encodeURIComponent(courseId), { headers: { "Accept": "application/json" } })
            .then(function (r) { return r.json(); })
            .then(function (p) {
                if (!p.ok || !p.lessons || !p.lessons.length) { body.innerHTML = '<span style="color:#94a3b8">Bu kurs üçün dərs siyahısı tapılmadı.</span>'; return; }
                var done = p.lessons.filter(function (l) { return l.completed; }).length;
                body.innerHTML = '<div style="font-weight:700;margin-bottom:6px;color:#334155">' + done + ' / ' + p.lessons.length + ' dərs tamamlanıb</div>'
                    + '<ul style="list-style:none;margin:0;padding:0;display:grid;gap:5px">' + p.lessons.map(function (l) {
                        var ic = l.completed ? '<i class="feather-check-circle" style="color:#16a34a"></i>'
                            : (l.watched ? '<i class="feather-play-circle" style="color:#3b82f6"></i>' : '<i class="feather-circle" style="color:#cbd5e1"></i>');
                        var lbl = l.completed ? '<span style="color:#16a34a;font-weight:600">Tamamlanıb</span>'
                            : (l.watched ? '<span style="color:#3b82f6">Baxılıb (' + l.percent + '%)</span>' : '<span style="color:#94a3b8">Başlanmayıb</span>');
                        return '<li style="display:flex;align-items:center;gap:8px;font-size:13px;padding:4px 8px;background:#fff;border-radius:8px">'
                            + '<span>' + ic + '</span><span style="flex:1">' + esc(l.title) + ' <small style="color:#94a3b8">— ' + esc(l.section) + '</small></span>' + lbl + '</li>';
                    }).join("") + '</ul>';
                if (window.feather && typeof window.feather.replace === "function") window.feather.replace();
            })
            .catch(function () { body.innerHTML = '<span style="color:#ef4444">Yüklənmədi.</span>'; });
    }

    function renderAll() { renderOrders(); renderCards(); renderEnrollments(); }

    function init() {
        wireCardForm();
        var f = $("[data-shoporder-filter]");
        if (f) f.addEventListener("change", function () { orderFilter = f.value; renderOrders(); });
        var es = $("[data-enroll-search]");
        if (es) es.addEventListener("input", function () { enrollSearch = es.value; renderEnrollments(); });
        // əlaqəli tablar açılanda state-i (və csrf-i) təzələ — giriş tamamlandıqdan sonra işləsin
        document.querySelectorAll('[data-admin-tab="payments"],[data-admin-tab="payment-cards"],[data-admin-tab="enrollments"]').forEach(function (b) {
            b.addEventListener("click", function () { fetchState(); });
        });
        fetchState();
    }
    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
    else init();
})();

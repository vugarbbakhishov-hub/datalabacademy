/* DatalabAcademy — AI mentor chat widget (self-contained, animated robot). */
(function () {
    "use strict";
    if (window.__dlAiChat) return;
    window.__dlAiChat = true;

    var API = "api/ai-chat.php";
    var history = [];

    function esc(v) {
        return String(v == null ? "" : v)
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#39;");
    }
    // Minimal markdown: code blocks, inline code, **bold**, line breaks
    function fmt(text) {
        var out = esc(text);
        out = out.replace(/```([\s\S]*?)```/g, function (_, c) { return '<pre>' + c.replace(/^\n/, "") + '</pre>'; });
        out = out.replace(/`([^`]+)`/g, '<code>$1</code>');
        out = out.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        out = out.replace(/\n/g, "<br>");
        return out;
    }

    var css = ''
        + '.dl-ai-fab{position:fixed;right:22px;bottom:22px;z-index:99998;width:64px;height:64px;border:0;border-radius:50%;cursor:pointer;color:#fff;background:linear-gradient(135deg,#315df5,#9b51e0);box-shadow:0 14px 34px rgba(49,93,245,.45);display:grid;place-items:center;transition:transform .2s ease;overflow:visible}'
        + '.dl-ai-fab:hover{transform:translateY(-3px) scale(1.06)}'
        + '.dl-ai-fab::before{content:"";position:absolute;inset:-4px;border-radius:50%;border:2px solid rgba(155,81,224,.55);animation:dlAiRing 2.4s ease-out infinite;pointer-events:none}'
        + '.dl-ai-fab .dl-ai-robot{width:38px;height:38px;animation:dlRobotFloat 3.2s ease-in-out infinite;transform-origin:50% 60%}'
        + '.dl-ai-robot .eyes{animation:dlRobotBlink 4.2s infinite;transform-origin:24px 23px;transform-box:fill-box}'
        + '.dl-ai-robot .ant{animation:dlRobotAntenna 1.6s ease-in-out infinite;transform-origin:24px 6px;transform-box:fill-box}'
        + '@keyframes dlRobotFloat{0%,100%{transform:translateY(0) rotate(0)}25%{transform:translateY(-2px) rotate(-5deg)}50%{transform:translateY(-3px) rotate(0)}75%{transform:translateY(-2px) rotate(5deg)}}'
        + '@keyframes dlRobotBlink{0%,40%,46%,100%{transform:scaleY(1)}43%{transform:scaleY(.12)}}'
        + '@keyframes dlRobotAntenna{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.65)}}'
        + '@keyframes dlAiRing{0%{transform:scale(.8);opacity:.7}70%{transform:scale(1.45);opacity:0}100%{opacity:0}}'
        + '.dl-ai-fab.is-active::before{display:none}'
        + '.dl-ai-panel{position:fixed;right:22px;bottom:94px;z-index:99999;width:min(380px,calc(100vw - 32px));height:min(560px,calc(100vh - 130px));display:none;flex-direction:column;overflow:hidden;border-radius:18px;background:#fff;box-shadow:0 30px 70px rgba(15,23,42,.32);font-family:inherit}'
        + '.dl-ai-panel.is-open{display:flex;animation:dlAiIn .22s ease}'
        + '@keyframes dlAiIn{from{opacity:0;transform:translateY(14px)}to{opacity:1;transform:none}}'
        + '.dl-ai-head{display:flex;align-items:center;gap:10px;padding:14px 16px;color:#fff;background:linear-gradient(135deg,#315df5,#9b51e0)}'
        + '.dl-ai-head b{font-size:15px;font-weight:800}.dl-ai-head small{display:block;font-size:11px;opacity:.85}'
        + '.dl-ai-head .dl-ai-ava{width:38px;height:38px;border-radius:12px;background:rgba(255,255,255,.18);display:grid;place-items:center}'
        + '.dl-ai-x{margin-left:auto;background:transparent;border:0;color:#fff;cursor:pointer;font-size:20px;line-height:1;opacity:.85}'
        + '.dl-ai-body{flex:1;overflow-y:auto;padding:16px;background:#f6f8fd;display:flex;flex-direction:column;gap:10px}'
        + '.dl-ai-msg{max-width:85%;padding:10px 13px;border-radius:14px;font-size:14px;line-height:1.5;white-space:normal;word-wrap:break-word}'
        + '.dl-ai-msg.bot{align-self:flex-start;background:#fff;color:#14213a;border:1px solid #e4e9f3;border-bottom-left-radius:5px}'
        + '.dl-ai-msg.me{align-self:flex-end;color:#fff;background:linear-gradient(135deg,#315df5,#6b6bff);border-bottom-right-radius:5px}'
        + '.dl-ai-msg pre{white-space:pre-wrap;background:#0f1828;color:#e7ecf6;padding:9px 11px;border-radius:9px;font-size:12.5px;margin:6px 0;overflow-x:auto}'
        + '.dl-ai-msg code{background:rgba(49,93,245,.1);padding:1px 5px;border-radius:5px;font-size:12.5px}'
        + '.dl-ai-typing{align-self:flex-start;color:#667085;font-size:13px;padding:4px 6px}'
        + '.dl-ai-foot{display:flex;gap:8px;padding:12px;background:#fff;border-top:1px solid #e4e9f3}'
        + '.dl-ai-foot textarea{flex:1;resize:none;max-height:90px;min-height:42px;padding:11px 13px;border:1px solid #e4e9f3;border-radius:11px;font:inherit;font-size:14px;outline:none}'
        + '.dl-ai-foot textarea:focus{border-color:#315df5;box-shadow:0 0 0 3px rgba(49,93,245,.12)}'
        + '.dl-ai-send{width:44px;height:44px;flex:0 0 auto;border:0;border-radius:11px;color:#fff;background:linear-gradient(135deg,#315df5,#9b51e0);cursor:pointer;display:grid;place-items:center}'
        + '.dl-ai-send:disabled{opacity:.5;cursor:default}'
        + '.active-dark-mode .dl-ai-panel{background:#131d31}'
        + '.active-dark-mode .dl-ai-body{background:#0f1828}'
        + '.active-dark-mode .dl-ai-msg.bot{background:#16213a;color:#e7ecf6;border-color:#283449}'
        + '.active-dark-mode .dl-ai-foot{background:#131d31;border-color:#283449}'
        + '.active-dark-mode .dl-ai-foot textarea{background:#0f1828;color:#f3f6fc;border-color:#283449}';

    function el(html) { var d = document.createElement("div"); d.innerHTML = html.trim(); return d.firstChild; }

    function init() {
        var style = document.createElement("style");
        style.textContent = css;
        document.head.appendChild(style);

        var fab = el('<button class="dl-ai-fab" aria-label="AI köməkçi">'
            + '<svg class="dl-ai-robot" viewBox="0 0 48 48" fill="none">'
            + '<line class="ant" x1="24" y1="6" x2="24" y2="12" stroke="#fff" stroke-width="2.4" stroke-linecap="round"></line>'
            + '<circle class="ant" cx="24" cy="5" r="2.6" fill="#fff"></circle>'
            + '<rect x="6.5" y="18.5" width="3.6" height="9" rx="1.8" fill="#fff"></rect>'
            + '<rect x="37.9" y="18.5" width="3.6" height="9" rx="1.8" fill="#fff"></rect>'
            + '<rect x="11" y="12" width="26" height="23" rx="8" fill="#fff"></rect>'
            + '<g class="eyes" fill="#315df5"><circle cx="19" cy="23" r="2.9"></circle><circle cx="29" cy="23" r="2.9"></circle></g>'
            + '<rect x="19" y="29" width="10" height="2.6" rx="1.3" fill="#9b51e0"></rect>'
            + '</svg></button>');
        var panel = el(''
            + '<div class="dl-ai-panel" role="dialog" aria-label="AI mentor">'
            + '<div class="dl-ai-head"><span class="dl-ai-ava"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 1 3 3v1h1a3 3 0 0 1 3 3v2a3 3 0 0 1-3 3h-1v1a3 3 0 0 1-6 0v-1H8a3 3 0 0 1-3-3V9a3 3 0 0 1 3-3h1V5a3 3 0 0 1 3-3z"></path></svg></span>'
            + '<div><b>AI Mentor</b><small>DatalabAcademy köməkçisi</small></div>'
            + '<button class="dl-ai-x" aria-label="Bağla">&times;</button></div>'
            + '<div class="dl-ai-body" data-ai-body></div>'
            + '<div class="dl-ai-foot"><textarea data-ai-input placeholder="Sualını yaz..." rows="1"></textarea>'
            + '<button class="dl-ai-send" data-ai-send aria-label="Göndər"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg></button></div>'
            + '</div>');

        document.body.appendChild(fab);
        document.body.appendChild(panel);

        var body = panel.querySelector("[data-ai-body]");
        var input = panel.querySelector("[data-ai-input]");
        var sendBtn = panel.querySelector("[data-ai-send]");
        var greeted = false;

        function addMsg(text, who) {
            var m = document.createElement("div");
            m.className = "dl-ai-msg " + (who === "me" ? "me" : "bot");
            m.innerHTML = who === "me" ? esc(text) : fmt(text);
            body.appendChild(m);
            body.scrollTop = body.scrollHeight;
            return m;
        }

        function open() {
            panel.classList.add("is-open");
            fab.classList.add("is-active");
            if (!greeted) { greeted = true; addMsg("Salam! 👋 Mən DatalabAcademy AI mentoruyam. Data Analitika, SQL, Excel və AI üzrə suallarına kömək edə bilərəm. Nə öyrənmək istəyirsən?", "bot"); }
            input.focus();
        }
        function close() { panel.classList.remove("is-open"); fab.classList.remove("is-active"); }

        fab.addEventListener("click", function () { panel.classList.contains("is-open") ? close() : open(); });
        panel.querySelector(".dl-ai-x").addEventListener("click", close);

        function send() {
            var text = input.value.trim();
            if (!text) return;
            addMsg(text, "me");
            history.push({ role: "user", content: text });
            input.value = "";
            input.style.height = "auto";
            sendBtn.disabled = true;
            var typing = document.createElement("div");
            typing.className = "dl-ai-typing";
            typing.textContent = "AI yazır...";
            body.appendChild(typing);
            body.scrollTop = body.scrollHeight;

            fetch(API, { method: "POST", headers: { "Content-Type": "application/json" }, credentials: "same-origin", body: JSON.stringify({ message: text, history: history.slice(-8) }) })
                .then(function (r) { return r.json(); })
                .then(function (p) {
                    typing.remove();
                    if (p.ok) { addMsg(p.reply, "bot"); history.push({ role: "assistant", content: p.reply }); }
                    else { addMsg(p.message || "Xəta baş verdi.", "bot"); }
                })
                .catch(function () { typing.remove(); addMsg("Şəbəkə xətası. Yenidən cəhd et.", "bot"); })
                .finally(function () { sendBtn.disabled = false; input.focus(); });
        }

        sendBtn.addEventListener("click", send);
        input.addEventListener("keydown", function (e) {
            if (e.key === "Enter" && !e.shiftKey) { e.preventDefault(); send(); }
        });
        input.addEventListener("input", function () { input.style.height = "auto"; input.style.height = Math.min(90, input.scrollHeight) + "px"; });
    }

    if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", init);
    else init();
}());

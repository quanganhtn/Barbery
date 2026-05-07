// lấy element theo id
window.$id = function (id) {
    return document.getElementById(id);
};

// chuẩn hóa số điện thoại
window.normalizePhone = function (s) {
    let x = (s || "").replace(/\s+/g, "");
    x = x.replace(/^\+?84/, "0");
    return x;
};

//định dạng số tiền
window.formatPrice = function (price) {
    return new Intl.NumberFormat("vi-VN").format(Number(price || 0)) + "đ";
};

//đổi phút thành chữ
window.minutesToText = function (m) {
    const mm = Number(m || 0);
    if (mm < 60) return `${mm} phút`;
    const h = Math.floor(mm / 60);
    const r = mm % 60;

    return r ? `${h} giờ ${r} phút` : `${h} giờ`;
};

//tính giờ kết thúc
window.computeEndTime = function (dateStr, timeStr, totalMin) {
    if (!dateStr || !timeStr || !totalMin) return "-"; //nhận ngày, giờ, tgian làm dịch vụ

    const d = new Date(`${dateStr}T${timeStr}:00`);//tạo đối tượng và lấy ngày giờ bắt đầu + tgian dịch vụ
    d.setMinutes(d.getMinutes() + Number(totalMin));

    const hh = String(d.getHours()).padStart(2, "0");
    const mm = String(d.getMinutes()).padStart(2, "0");

    return `${hh}:${mm}`;
};

//gọi API
window.fetchJson = async function (url, opts = {}) {
    const res = await fetch(url, {   //gửi request đến server

        headers: {  //thông tin gửi kèm request
            Accept: "application/json",
            ...(opts.headers || {}),
        },
        ...opts,
    });

    const json = await res.json().catch(() => ({})); //đọc server trả về

    return { res, json };
};

//hiện thông báo nổi
window.showToast = function (message, type = "success") {
    const container = window.$id("toast-container");
    if (!container) return;

    const toast = document.createElement("div");
    const base = "px-6 py-3 rounded-xl shadow-lg slide-in";

    const cls =
        type === "success" ? "bg-green-500 text-white" :
            type === "error" ? "bg-red-500 text-white" :
                "bg-gray-200 text-black";

    toast.className = `${base} ${cls}`;
    toast.innerHTML = `<p class="font-medium">${message}</p>`;

    container.appendChild(toast);

    setTimeout(() => toast.remove(), 3000);
};

// ===== LOADING =====
window.showLoading = function (show) {
    const el = window.$id("loading-overlay");
    if (!el) return;

    el.classList.toggle("hidden", !show);
    el.classList.toggle("flex", show);
};

// ===== MOBILE MENU =====
window.toggleMobileMenu = function () {
    const menu = window.$id("mobile-menu");
    const overlay = window.$id("mobile-overlay");

    menu?.classList.toggle("-translate-x-full");
    overlay?.classList.toggle("hidden");
};

// ===== NAV ACTIVE ON SCROLL =====
document.addEventListener("DOMContentLoaded", () => {
    const navLinks = document.querySelectorAll(".nav-link[data-section]");
    const scrollBox = document.getElementById("app") || window;

    function setActiveNav() {
        if (!navLinks.length) return;

        let current = "home";

        navLinks.forEach(link => {
            const section = document.getElementById(link.dataset.section);
            if (!section) return;

            const top = section.getBoundingClientRect().top;

            if (top <= 160) {
                current = link.dataset.section;
            }
        });

        navLinks.forEach(link => {
            const isActive = link.dataset.section === current;

            link.classList.toggle("text-gold", isActive);
            link.classList.toggle("font-bold", isActive);
            link.classList.toggle("text-gray-300", !isActive);
        });
    }

    scrollBox.addEventListener("scroll", setActiveNav);
    window.addEventListener("load", setActiveNav);
    window.addEventListener("hashchange", () => {
        setTimeout(setActiveNav, 200);
    });

    setTimeout(setActiveNav, 300);
});

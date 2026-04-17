/* =========================================================
   BARBERY.JS — Booking + Lookup (API) + Available Slots
   - Step 3: lấy giờ trống theo duration từ API /available-slots
   - Step 4 summary: tổng thời gian + giờ kết thúc
========================================================= */


// ===== DOM HELPERS (BẮT BUỘC) =====
function $(id) {
    return document.getElementById(id);
}

// ===== CATALOG (LOAD FROM API) =====
let services = [];
let stylists = [];

async function loadCatalog() {
    // Guard: routes phải tồn tại
    if (!window.Barbery?.routes?.services || !window.Barbery?.routes?.stylists) {
        console.error("window.Barbery.routes missing", window.Barbery);
        throw new Error("Missing Barbery routes");
    }

    const [sRes, stRes] = await Promise.all([
        fetch(window.Barbery.routes.services, { headers: { Accept: "application/json" } }),
        fetch(window.Barbery.routes.stylists, { headers: { Accept: "application/json" } }),
    ]);

    async function fetchJson(url, opts = {}) {
        try {
            const res = await fetch(url, {
                headers: { Accept: "application/json", ...(opts.headers || {}) },
                ...opts,
            });
            const json = await res.json();
            if (!res.ok || !json?.ok) {
                throw new Error("Không lấy được dữ liệu");
            }
            return { res, json };
        } catch (error) {
            console.error("Error fetching data: ", error);
            throw error;
        }
    }

    if (!sRes.ok || sJson?.ok === false) {
        console.warn("services api error", sRes.status, sJson);
        throw new Error(sJson?.message || "Không tải được danh sách dịch vụ");
    }
    if (!stRes.ok || stJson?.ok === false) {
        console.warn("stylists api error", stRes.status, stJson);
        throw new Error(stJson?.message || "Không tải được danh sách thợ cắt");
    }

    const sList = Array.isArray(sJson?.data) ? sJson.data : [];
    const stList = Array.isArray(stJson?.data) ? stJson.data : [];

    // NOTE: duration_min là nguồn chính để tính tổng thời gian.
    services = sList.map((x) => ({
        id: x.id,
        name: x.name,
        price: Number(x.price || 0),
        duration_min: Number(x.duration_min || 30),
        icon: x.icon || "⭐",
    }));

    function getInitials(name) {
        name = (name || "").trim();
        if (!name) return "B";
        const parts = name.split(/\s+/);
        const first = parts[0]?.[0] || "";
        const last = parts.length > 1 ? parts[parts.length - 1][0] : "";
        return (first + last).toUpperCase();
    }

    stylists = stList.map((x) => ({
        id: x.id,
        name: x.name,
        role: x.role || "Stylist",
        exp: Number(x.exp || 0),
        rating: Number(x.rating || 0),
        specialty: x.specialty
            ? String(x.specialty).split(",").map((t) => t.trim()).filter(Boolean)
            : [],
        status: x.status || "available",

        // NEW:
        avatar_url: x.avatar_url ? String(x.avatar_url) : null,
        initials: getInitials(x.name),
    }));

}

// ===== LEGACY TIME SLOTS (không còn dùng để render UI, chỉ giữ để reference) =====
const timeSlots = [
    "08:00", "08:30", "09:00", "09:30", "10:00", "10:30", "11:00", "11:30",
    "12:00", "12:30", "13:00", "13:30",
    "14:00", "14:30", "15:00", "15:30", "16:00", "16:30",
    "17:00", "17:30", "18:00", "18:30", "19:00", "19:30",
];

// ===== APP STATE =====
let currentStep = 1;
let bookingData = { services: [], stylist: null, date: null, time: null };
let availableTimes = new Set();

// ===== UTILS =====
// LƯU Ý: file này chỉ được có 1 normalizePhone. Nếu build báo trùng => bạn đang có 2 cái, xoá bớt 1.
function normalizePhone(s) {
    let x = (s || "").replace(/\s+/g, "");
    x = x.replace(/^\+?84/, "0");
    return x;
}

function formatPrice(price) {
    return new Intl.NumberFormat("vi-VN").format(Number(price || 0)) + "đ";
}

function minutesToText(m) {
    const mm = Number(m || 0);
    if (mm < 60) return `${mm} phút`;
    const h = Math.floor(mm / 60);
    const r = mm % 60;
    return r ? `${h} giờ ${r} phút` : `${h} giờ`;
}

function computeTotalDuration(selectedServices) {
    return (selectedServices || []).reduce((sum, s) => sum + Number(s.duration_min || 0), 0);
}

function computeEndTime(dateStr, timeStr, totalMin) {
    if (!dateStr || !timeStr || !totalMin) return "-";
    const d = new Date(`${dateStr}T${timeStr}:00`);
    d.setMinutes(d.getMinutes() + Number(totalMin));
    const hh = String(d.getHours()).padStart(2, "0");
    const mm = String(d.getMinutes()).padStart(2, "0");
    return `${hh}:${mm}`;
}

function getNext14Days() {
    const days = [];
    for (let i = 0; i < 14; i++) {
        const d = new Date();
        d.setDate(d.getDate() + i);
        days.push(d.toISOString().split("T")[0]); // YYYY-MM-DD
    }
    return days;
}

function showToast(message, type = "success") {
    const container = $("toast-container");
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
}

function showLoading(show) {
    const el = $("loading-overlay");
    if (!el) return;
    el.classList.toggle("hidden", !show);
}

async function fetchJson(url, opts = {}) {
    const res = await fetch(url, {
        headers: { Accept: "application/json", ...(opts.headers || {}) },
        ...opts,
    });
    const json = await res.json().catch(() => ({}));
    return { res, json };
}

function canLoadSlots() {
    return !!(bookingData.stylist && bookingData.date && bookingData.services?.length);
}

// ===== MOBILE MENU =====
window.toggleMobileMenu = function () {
    $("mobile-menu")?.classList.toggle("open");
    $("mobile-overlay")?.classList.toggle("open");
};

// =========================================================
// AVAILABLE SLOTS (API)
// =========================================================
async function fetchAvailableSlots() {
    if (!canLoadSlots()) {
        availableTimes = new Set();
        return;
    }

    const totalMin = computeTotalDuration(bookingData.services);
    const url =
        `${window.Barbery.routes.availableSlots}` +
        `?stylist_id=${bookingData.stylist.id}` +
        `&date=${bookingData.date}` +
        `&duration=${totalMin}`;

    try {
        const { res, json } = await fetchJson(url);

        if (!res.ok || !json?.ok) {
            availableTimes = new Set();
            console.warn("availableSlots not ok", res.status, json);
            return;
        }

        const slots = Array.isArray(json.data) ? json.data : (json.data?.slots || []);
        availableTimes = new Set(slots);
    } catch (e) {
        availableTimes = new Set();
        console.warn("availableSlots fetch error", e);
    }
}

async function refreshSlotsAndRender() {
    bookingData.time = null;

    if (!canLoadSlots()) {
        availableTimes = new Set();
        renderTimeSlots();
        validateStep3();
        updateSummary();
        return;
    }

    showLoading(true);
    await fetchAvailableSlots();
    showLoading(false);

    renderTimeSlots();
    validateStep3();
    updateSummary();
}

// =========================================================
// BOOKING FLOW
// =========================================================
window.nextStep = async function () {
    if (currentStep >= 4) return;

    $(`booking-step-${currentStep}`)?.classList.add("hidden");
    currentStep++;
    $(`booking-step-${currentStep}`)?.classList.remove("hidden");

    updateStepIndicators();
    updateSummary();

    if (currentStep === 3) {
        renderDates();
        renderTimeSlots();

        if (canLoadSlots()) {
            showLoading(true);
            await fetchAvailableSlots();
            showLoading(false);
            renderTimeSlots();
        }

        validateStep3();
    }
};

window.prevStep = async function () {
    if (currentStep <= 1) return;

    $(`booking-step-${currentStep}`)?.classList.add("hidden");
    currentStep--;
    $(`booking-step-${currentStep}`)?.classList.remove("hidden");

    updateStepIndicators();
    updateSummary();

    if (currentStep === 3) {
        renderDates();
        if (canLoadSlots()) {
            showLoading(true);
            await fetchAvailableSlots();
            showLoading(false);
        }
        renderTimeSlots();
        validateStep3();
    }
};

function updateStepIndicators() {
    for (let i = 1; i <= 4; i++) {
        const el = $(`step-${i}-indicator`);
        if (!el) continue;

        el.classList.remove("step-active", "step-completed");
        el.classList.add("bg-gray-700", "text-gray-400");

        if (i < currentStep) {
            el.classList.add("step-completed");
            el.innerHTML = "✓";
        } else if (i === currentStep) {
            el.classList.add("step-active");
            el.innerHTML = String(i);
        } else {
            el.innerHTML = String(i);
        }
    }
}

window.resetBooking = function () {
    currentStep = 1;
    bookingData = { services: [], stylist: null, date: null, time: null };
    availableTimes = new Set();

    document.querySelectorAll(".booking-step").forEach((s) => s.classList.add("hidden"));
    $("booking-step-1")?.classList.remove("hidden");
    $("booking-success")?.classList.add("hidden");

    ["customer-name", "customer-phone", "customer-email", "customer-notes"].forEach((id) => {
        if ($(id)) $(id).value = "";
    });

    updateStepIndicators();
    renderServices();
    renderStylists();
    renderTimeSlots();
    updateSummary();
};

// =========================================================
// RENDER SERVICES
// =========================================================
function renderServices() {
    const c = $("service-list");
    if (!c) return;

    const selectedSet = new Set((bookingData.services || []).map((x) => String(x.id)));

    c.innerHTML = services.map((s) => {
        const isSelected = selectedSet.has(String(s.id));
        return `
      <div class="service-card bg-dark rounded-xl p-4 border cursor-pointer
        ${isSelected ? "border-gold ring-2 ring-gold/30" : "border-gray-800"}"
        onclick="toggleService('${s.id}')">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gray-700/40 rounded-xl flex items-center justify-center text-2xl">${s.icon}</div>
          <div class="flex-1">
            <h3 class="font-semibold text-white">${s.name}</h3>
            <p class="text-sm text-gray-500">~${s.duration_min} phút</p>
          </div>
          <p class="font-bold text-gold">${formatPrice(s.price)}</p>
        </div>
      </div>
    `;
    }).join("");
}

window.toggleService = async function (id) {
    if (!Array.isArray(bookingData.services)) bookingData.services = [];

    const sv = services.find((s) => String(s.id) === String(id));
    if (!sv) return;

    const idx = bookingData.services.findIndex((x) => String(x.id) === String(id));
    if (idx >= 0) bookingData.services.splice(idx, 1);
    else bookingData.services.push(sv);

    renderServices();
    $("btn-step1-next").disabled = bookingData.services.length === 0;

    if (currentStep === 3) {
        await refreshSlotsAndRender();
        return;
    }

    updateSummary();
};

// =========================================================
// RENDER STYLISTS
// =========================================================
function renderStylists() {
    const c = $("stylist-list");
    if (!c) return;

    c.innerHTML = stylists.map((s) => {
        const isSelected = bookingData.stylist && String(bookingData.stylist.id) === String(s.id);

        return `
      <div class="stylist-card bg-dark rounded-xl p-4 border cursor-pointer
        ${isSelected ? "border-gold ring-2 ring-gold/30" : "border-gray-800"}"
        onclick="selectStylist('${s.id}')">
        <div class="flex gap-4 items-center">

          <!-- AVATAR -->
          <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
            ${s.avatar_url
                ? `<img src="${s.avatar_url}" alt="${s.name || ""}"
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">`
                : ``
            }
            <span class="${s.avatar_url ? "hidden" : ""} text-black font-bold">
              ${s.initials || "B"}
            </span>
          </div>

          <div>
            <h3 class="text-white font-semibold">${s.name}</h3>
            <p class="text-sm text-gray-500">${s.exp} năm • ⭐ ${s.rating}</p>
            <p class="text-xs text-gray-400">${(s.specialty || []).join(", ")}</p>
          </div>
        </div>
      </div>
    `;
    }).join("");
}


window.selectStylist = async function (id) {
    const clicked = stylists.find((s) => String(s.id) === String(id));
    if (!clicked) return;

    if (bookingData.stylist && String(bookingData.stylist.id) === String(id)) {
        bookingData.stylist = null;
        availableTimes = new Set();
        bookingData.time = null;

        renderStylists();
        $("btn-step2-next").disabled = true;

        if (currentStep === 3) {
            renderTimeSlots();
            validateStep3();
        }

        updateSummary();
        return;
    }

    bookingData.stylist = clicked;
    bookingData.time = null;

    renderStylists();
    $("btn-step2-next").disabled = false;

    if (currentStep === 3) {
        await refreshSlotsAndRender();
        return;
    }

    updateSummary();
};

// =========================================================
// DATE & TIME (Step 3)
// =========================================================
function renderDates() {
    const c = $("date-list");
    if (!c) return;

    const days = getNext14Days();
    c.innerHTML = days.map((d) => {
        const dt = new Date(d);
        return `
      <button class="date-btn ${bookingData.date === d ? "selected" : ""}" onclick="selectDate('${d}')">
        <p>${dt.getDate()}/${dt.getMonth() + 1}</p>
      </button>`;
    }).join("");
}

window.selectDate = async function (date) {
    bookingData.date = date;
    renderDates();

    if (currentStep === 3) {
        await refreshSlotsAndRender();
        return;
    }

    updateSummary();
};

function renderTimeSlots() {
    const c = $("time-list");
    if (!c) return;

    if (!canLoadSlots()) {
        c.innerHTML = `
            <div class="col-span-4 sm:col-span-6 text-gray-500 text-sm">
                Vui lòng chọn dịch vụ, thợ cắt và ngày để xem giờ trống.
            </div>`;
        return;
    }

    // Dùng toàn bộ danh sách giờ chuẩn để render
    const allSlots = [...timeSlots];

    const hasAnyAvailable = allSlots.some(t => availableTimes.has(t));

    if (!hasAnyAvailable) {
        c.innerHTML = `
            <div class="col-span-4 sm:col-span-6 text-gray-500 text-sm">
                Không còn khung giờ phù hợp cho tổng thời gian đã chọn.
            </div>`;
        return;
    }

    c.innerHTML = allSlots.map((t) => {
        const isAvailable = availableTimes.has(t);
        const isSelected = bookingData.time === t;

        return `
            <button
                type="button"
                class="time-slot ${isSelected ? "selected" : ""} ${!isAvailable ? "disabled-slot" : ""}"
                ${isAvailable ? `onclick="selectTime('${t}')"` : "disabled"}
            >
                ${t}
            </button>
        `;
    }).join("");
}

window.selectTime = function (t) {
    if (!availableTimes.has(t)) return;

    bookingData.time = t;
    renderTimeSlots();
    validateStep3();
    updateSummary();
};

function validateStep3() {
    $("btn-step3-next").disabled = !(bookingData.date && bookingData.time);
}

// =========================================================
// SUMMARY (Step 4)
// =========================================================
function updateSummary() {
    const svs = bookingData.services || [];
    const names = svs.length ? svs.map((x) => x.name).join(", ") : "-";
    const total = svs.reduce((sum, x) => sum + Number(x.price || 0), 0);

    const totalMin = computeTotalDuration(svs);
    const durationText = svs.length ? minutesToText(totalMin) : "-";
    const endTimeText =
        bookingData.date && bookingData.time && svs.length
            ? computeEndTime(bookingData.date, bookingData.time, totalMin)
            : "-";

    if ($("summary-service")) $("summary-service").textContent = names;
    if ($("summary-total")) $("summary-total").textContent = svs.length ? formatPrice(total) : "-";

    if ($("summary-stylist")) $("summary-stylist").textContent = bookingData.stylist?.name || "-";
    if ($("summary-date")) $("summary-date").textContent = bookingData.date || "-";
    if ($("summary-time")) $("summary-time").textContent = bookingData.time || "-";

    if ($("summary-duration")) $("summary-duration").textContent = durationText;
    if ($("summary-endtime")) $("summary-endtime").textContent = endTimeText;
}
window.updateSummary = updateSummary;

// =========================================================
// SUBMIT BOOKING
// =========================================================
window.submitBooking = async function () {
    // Lấy dữ liệu từ form
    const name = $("customer-name").value.trim();
    const phone = normalizePhone($("customer-phone").value.trim());
    const email = $("customer-email").value.trim();
    const notes = $("customer-notes").value.trim();

    // Validate phía client
    if (!name || !phone || !email) {
        showToast("Vui lòng nhập họ tên, số điện thoại và email", "error");
        return;
    }

    // Kiểm tra dữ liệu booking đã chọn đủ chưa
    if (!bookingData.services?.length || !bookingData.stylist || !bookingData.date || !bookingData.time) {
        showToast("Thiếu thông tin đặt lịch", "error");
        return;
    }

    showLoading(true);

    try {
        const res = await fetch(window.Barbery.routes.createBooking, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                // Thông tin khách
                customer_name: name,
                customer_phone: phone,
                customer_email: email,

                // Thông tin booking
                service_ids: bookingData.services.map((s) => s.id),
                stylist_id: bookingData.stylist.id,
                booking_date: bookingData.date,
                booking_time: bookingData.time,
                notes: notes || null,
            }),
        });

        const json = await res.json().catch(() => ({}));
        showLoading(false);

        if (!res.ok) {
            showToast(json.message || "Lỗi đặt lịch", "error");
            return;
        }

        // Ẩn các step và hiện màn hình thành công
        document.querySelectorAll(".booking-step").forEach((s) => s.classList.add("hidden"));
        $("booking-success").classList.remove("hidden");

        // Hiển thị mã đặt lịch
        $("booking-code-display").textContent = json.data.booking_code;

        showToast("Đặt lịch thành công!");
    } catch (error) {
        showLoading(false);
        showToast("Không kết nối được server", "error");
    }
};

// =========================================================
// LOOKUP
// =========================================================
async function handleLookupSubmit(e) {
    e.preventDefault();

    const input = $("lookup-input");
    const q = normalizePhone(input?.value?.trim());
    if (!q) return;

    showLoading(true);
    try {
        const { res, json } = await fetchJson(`${window.Barbery.routes.lookup}?q=${encodeURIComponent(q)}`);
        showLoading(false);

        if (!res.ok || !json?.ok) {
            showToast(json?.message || "Tra cứu lỗi", "error");
            return;
        }

        const resultsWrap = $("lookup-results");
        const emptyWrap = $("lookup-empty");
        const list = $("lookup-list");

        if (list) list.innerHTML = "";

        const items = json.data || [];
        if (items.length === 0) {
            resultsWrap?.classList.add("hidden");
            emptyWrap?.classList.remove("hidden");
            return;
        }

        emptyWrap?.classList.add("hidden");
        resultsWrap?.classList.remove("hidden");

        if (list) {
            list.innerHTML = items.map((b) => {
                const durationText = b.total_duration_min ? minutesToText(b.total_duration_min) : "-";
                const endTime = b.total_duration_min
                    ? computeEndTime(b.booking_date, b.booking_time, b.total_duration_min)
                    : "-";

                return `
          <div class="bg-dark p-4 rounded-xl border border-gray-800">
            <p class="font-semibold text-gold">Mã: ${b.booking_code}</p>
            <p>Dịch vụ: ${b.service_name}</p>
            <p>Thợ: ${b.stylist_name}</p>

            <p>Ngày: ${b.booking_date}</p>
            <p>Giờ bắt đầu: ${b.booking_time}</p>
            <p>Tổng thời gian: ${durationText}</p>
            <p>Giờ kết thúc: ${endTime}</p>

            <p class="text-sm text-gray-400">Trạng thái: ${b.status}</p>
          </div>
        `;
            }).join("");
        }
    } catch {
        showLoading(false);
        showToast("Không kết nối server", "error");
    }
}

// =========================================================
// INIT
// =========================================================
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Booking page
        if ($("service-list")) {
            showLoading(true);
            await loadCatalog();
            renderServices();
            renderStylists();
            updateStepIndicators();
            renderTimeSlots();
            updateSummary();
            showLoading(false);
        }

        // Lookup page
        const form = $("lookup-form");
        if (form) form.addEventListener("submit", handleLookupSubmit);
    } catch (e) {
        console.error(e);
        showLoading(false);
        showToast(e?.message || "Lỗi tải dữ liệu", "error");
    }
});

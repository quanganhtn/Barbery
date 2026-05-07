// =========================================================
// LOOKUP.JS — Lookup page only
// =========================================================

const $ = window.$id;
const normalizePhone = window.normalizePhone;
const minutesToText = window.minutesToText;
const computeEndTime = window.computeEndTime;
const showToast = window.showToast;
const showLoading = window.showLoading;
const fetchJson = window.fetchJson;
// =========================================================
// LOOKUP
// =========================================================
async function handleLookupSubmit(e) {
    e.preventDefault();

    const input = $("lookup-input");
    const raw = input?.value?.trim();

    if (!raw) return;

    const q = raw.toUpperCase().startsWith("BK")
        ? raw.trim()
        : normalizePhone(raw);

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
document.addEventListener("DOMContentLoaded", () => {
    const form = $("lookup-form");

    if (form) {
        form.addEventListener("submit", handleLookupSubmit);
    }
});

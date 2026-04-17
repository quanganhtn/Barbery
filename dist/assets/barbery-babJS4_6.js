function s(t){return document.getElementById(t)}let L=[],T=[];async function I(){var c,m,d,h;if(!((m=(c=window.Barbery)==null?void 0:c.routes)!=null&&m.services)||!((h=(d=window.Barbery)==null?void 0:d.routes)!=null&&h.stylists))throw console.error("window.Barbery.routes missing",window.Barbery),new Error("Missing Barbery routes");const[t,e]=await Promise.all([fetch(window.Barbery.routes.services,{headers:{Accept:"application/json"}}),fetch(window.Barbery.routes.stylists,{headers:{Accept:"application/json"}})]);if(!t.ok||(sJson==null?void 0:sJson.ok)===!1)throw console.warn("services api error",t.status,sJson),new Error((sJson==null?void 0:sJson.message)||"Không tải được danh sách dịch vụ");if(!e.ok||(stJson==null?void 0:stJson.ok)===!1)throw console.warn("stylists api error",e.status,stJson),new Error((stJson==null?void 0:stJson.message)||"Không tải được danh sách thợ cắt");const n=Array.isArray(sJson==null?void 0:sJson.data)?sJson.data:[],r=Array.isArray(stJson==null?void 0:stJson.data)?stJson.data:[];L=n.map(o=>({id:o.id,name:o.name,price:Number(o.price||0),duration_min:Number(o.duration_min||30),icon:o.icon||"⭐"}));function a(o){var N;if(o=(o||"").trim(),!o)return"B";const l=o.split(/\s+/),k=((N=l[0])==null?void 0:N[0])||"",x=l.length>1?l[l.length-1][0]:"";return(k+x).toUpperCase()}T=r.map(o=>({id:o.id,name:o.name,role:o.role||"Stylist",exp:Number(o.exp||0),rating:Number(o.rating||0),specialty:o.specialty?String(o.specialty).split(",").map(l=>l.trim()).filter(Boolean):[],status:o.status||"available",avatar_url:o.avatar_url?String(o.avatar_url):null,initials:a(o.name)}))}const K=["08:00","08:30","09:00","09:30","10:00","10:30","11:00","11:30","12:00","12:30","13:00","13:30","14:00","14:30","15:00","15:30","16:00","16:30","17:00","17:30","18:00","18:30","19:00","19:30"];let u=1,i={services:[],stylist:null,date:null,time:null},p=new Set;function j(t){let e=(t||"").replace(/\s+/g,"");return e=e.replace(/^\+?84/,"0"),e}function C(t){return new Intl.NumberFormat("vi-VN").format(Number(t||0))+"đ"}function D(t){const e=Number(t||0);if(e<60)return`${e} phút`;const n=Math.floor(e/60),r=e%60;return r?`${n} giờ ${r} phút`:`${n} giờ`}function E(t){return(t||[]).reduce((e,n)=>e+Number(n.duration_min||0),0)}function H(t,e,n){if(!t||!e||!n)return"-";const r=new Date(`${t}T${e}:00`);r.setMinutes(r.getMinutes()+Number(n));const a=String(r.getHours()).padStart(2,"0"),c=String(r.getMinutes()).padStart(2,"0");return`${a}:${c}`}function R(){const t=[];for(let e=0;e<14;e++){const n=new Date;n.setDate(n.getDate()+e),t.push(n.toISOString().split("T")[0])}return t}function v(t,e="success"){const n=s("toast-container");if(!n)return;const r=document.createElement("div"),a="px-6 py-3 rounded-xl shadow-lg slide-in",c=e==="success"?"bg-green-500 text-white":e==="error"?"bg-red-500 text-white":"bg-gray-200 text-black";r.className=`${a} ${c}`,r.innerHTML=`<p class="font-medium">${t}</p>`,n.appendChild(r),setTimeout(()=>r.remove(),3e3)}function g(t){const e=s("loading-overlay");e&&e.classList.toggle("hidden",!t)}async function J(t,e={}){const n=await fetch(t,{headers:{Accept:"application/json",...e.headers||{}},...e}),r=await n.json().catch(()=>({}));return{res:n,json:r}}function w(){var t;return!!(i.stylist&&i.date&&((t=i.services)!=null&&t.length))}window.toggleMobileMenu=function(){var t,e;(t=s("mobile-menu"))==null||t.classList.toggle("open"),(e=s("mobile-overlay"))==null||e.classList.toggle("open")};async function _(){var n;if(!w()){p=new Set;return}const t=E(i.services),e=`${window.Barbery.routes.availableSlots}?stylist_id=${i.stylist.id}&date=${i.date}&duration=${t}`;try{const{res:r,json:a}=await J(e);if(!r.ok||!(a!=null&&a.ok)){p=new Set,console.warn("availableSlots not ok",r.status,a);return}const c=Array.isArray(a.data)?a.data:((n=a.data)==null?void 0:n.slots)||[];p=new Set(c)}catch(r){p=new Set,console.warn("availableSlots fetch error",r)}}async function A(){if(i.time=null,!w()){p=new Set,y(),b(),f();return}g(!0),await _(),g(!1),y(),b(),f()}window.nextStep=async function(){var t,e;u>=4||((t=s(`booking-step-${u}`))==null||t.classList.add("hidden"),u++,(e=s(`booking-step-${u}`))==null||e.classList.remove("hidden"),$(),f(),u===3&&(B(),y(),w()&&(g(!0),await _(),g(!1),y()),b()))};window.prevStep=async function(){var t,e;u<=1||((t=s(`booking-step-${u}`))==null||t.classList.add("hidden"),u--,(e=s(`booking-step-${u}`))==null||e.classList.remove("hidden"),$(),f(),u===3&&(B(),w()&&(g(!0),await _(),g(!1)),y(),b()))};function $(){for(let t=1;t<=4;t++){const e=s(`step-${t}-indicator`);e&&(e.classList.remove("step-active","step-completed"),e.classList.add("bg-gray-700","text-gray-400"),t<u?(e.classList.add("step-completed"),e.innerHTML="✓"):(t===u&&e.classList.add("step-active"),e.innerHTML=String(t)))}}window.resetBooking=function(){var t,e;u=1,i={services:[],stylist:null,date:null,time:null},p=new Set,document.querySelectorAll(".booking-step").forEach(n=>n.classList.add("hidden")),(t=s("booking-step-1"))==null||t.classList.remove("hidden"),(e=s("booking-success"))==null||e.classList.add("hidden"),["customer-name","customer-phone","customer-email","customer-notes"].forEach(n=>{s(n)&&(s(n).value="")}),$(),M(),S(),y(),f()};function M(){const t=s("service-list");if(!t)return;const e=new Set((i.services||[]).map(n=>String(n.id)));t.innerHTML=L.map(n=>`
      <div class="service-card bg-dark rounded-xl p-4 border cursor-pointer
        ${e.has(String(n.id))?"border-gold ring-2 ring-gold/30":"border-gray-800"}"
        onclick="toggleService('${n.id}')">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-gray-700/40 rounded-xl flex items-center justify-center text-2xl">${n.icon}</div>
          <div class="flex-1">
            <h3 class="font-semibold text-white">${n.name}</h3>
            <p class="text-sm text-gray-500">~${n.duration_min} phút</p>
          </div>
          <p class="font-bold text-gold">${C(n.price)}</p>
        </div>
      </div>
    `).join("")}window.toggleService=async function(t){Array.isArray(i.services)||(i.services=[]);const e=L.find(r=>String(r.id)===String(t));if(!e)return;const n=i.services.findIndex(r=>String(r.id)===String(t));if(n>=0?i.services.splice(n,1):i.services.push(e),M(),s("btn-step1-next").disabled=i.services.length===0,u===3){await A();return}f()};function S(){const t=s("stylist-list");t&&(t.innerHTML=T.map(e=>`
      <div class="stylist-card bg-dark rounded-xl p-4 border cursor-pointer
        ${i.stylist&&String(i.stylist.id)===String(e.id)?"border-gold ring-2 ring-gold/30":"border-gray-800"}"
        onclick="selectStylist('${e.id}')">
        <div class="flex gap-4 items-center">

          <!-- AVATAR -->
          <div class="w-14 h-14 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
            ${e.avatar_url?`<img src="${e.avatar_url}" alt="${e.name||""}"
                     class="w-full h-full object-cover"
                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">`:""}
            <span class="${e.avatar_url?"hidden":""} text-black font-bold">
              ${e.initials||"B"}
            </span>
          </div>

          <div>
            <h3 class="text-white font-semibold">${e.name}</h3>
            <p class="text-sm text-gray-500">${e.exp} năm • ⭐ ${e.rating}</p>
            <p class="text-xs text-gray-400">${(e.specialty||[]).join(", ")}</p>
          </div>
        </div>
      </div>
    `).join(""))}window.selectStylist=async function(t){const e=T.find(n=>String(n.id)===String(t));if(e){if(i.stylist&&String(i.stylist.id)===String(t)){i.stylist=null,p=new Set,i.time=null,S(),s("btn-step2-next").disabled=!0,u===3&&(y(),b()),f();return}if(i.stylist=e,i.time=null,S(),s("btn-step2-next").disabled=!1,u===3){await A();return}f()}};function B(){const t=s("date-list");if(!t)return;const e=R();t.innerHTML=e.map(n=>{const r=new Date(n);return`
      <button class="date-btn ${i.date===n?"selected":""}" onclick="selectDate('${n}')">
        <p>${r.getDate()}/${r.getMonth()+1}</p>
      </button>`}).join("")}window.selectDate=async function(t){if(i.date=t,B(),u===3){await A();return}f()};function y(){const t=s("time-list");if(!t)return;if(!w()){t.innerHTML=`
            <div class="col-span-4 sm:col-span-6 text-gray-500 text-sm">
                Vui lòng chọn dịch vụ, thợ cắt và ngày để xem giờ trống.
            </div>`;return}const e=[...K];if(!e.some(r=>p.has(r))){t.innerHTML=`
            <div class="col-span-4 sm:col-span-6 text-gray-500 text-sm">
                Không còn khung giờ phù hợp cho tổng thời gian đã chọn.
            </div>`;return}t.innerHTML=e.map(r=>{const a=p.has(r);return`
            <button
                type="button"
                class="time-slot ${i.time===r?"selected":""} ${a?"":"disabled-slot"}"
                ${a?`onclick="selectTime('${r}')"`:"disabled"}
            >
                ${r}
            </button>
        `}).join("")}window.selectTime=function(t){p.has(t)&&(i.time=t,y(),b(),f())};function b(){s("btn-step3-next").disabled=!(i.date&&i.time)}function f(){var m;const t=i.services||[],e=t.length?t.map(d=>d.name).join(", "):"-",n=t.reduce((d,h)=>d+Number(h.price||0),0),r=E(t),a=t.length?D(r):"-",c=i.date&&i.time&&t.length?H(i.date,i.time,r):"-";s("summary-service")&&(s("summary-service").textContent=e),s("summary-total")&&(s("summary-total").textContent=t.length?C(n):"-"),s("summary-stylist")&&(s("summary-stylist").textContent=((m=i.stylist)==null?void 0:m.name)||"-"),s("summary-date")&&(s("summary-date").textContent=i.date||"-"),s("summary-time")&&(s("summary-time").textContent=i.time||"-"),s("summary-duration")&&(s("summary-duration").textContent=a),s("summary-endtime")&&(s("summary-endtime").textContent=c)}window.updateSummary=f;window.submitBooking=async function(){var a;const t=s("customer-name").value.trim(),e=j(s("customer-phone").value.trim()),n=s("customer-email").value.trim(),r=s("customer-notes").value.trim();if(!t||!e||!n){v("Vui lòng nhập họ tên, số điện thoại và email","error");return}if(!((a=i.services)!=null&&a.length)||!i.stylist||!i.date||!i.time){v("Thiếu thông tin đặt lịch","error");return}g(!0);try{const c=await fetch(window.Barbery.routes.createBooking,{method:"POST",headers:{"Content-Type":"application/json",Accept:"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({customer_name:t,customer_phone:e,customer_email:n,service_ids:i.services.map(d=>d.id),stylist_id:i.stylist.id,booking_date:i.date,booking_time:i.time,notes:r||null})}),m=await c.json().catch(()=>({}));if(g(!1),!c.ok){v(m.message||"Lỗi đặt lịch","error");return}document.querySelectorAll(".booking-step").forEach(d=>d.classList.add("hidden")),s("booking-success").classList.remove("hidden"),s("booking-code-display").textContent=m.data.booking_code,v("Đặt lịch thành công!")}catch{g(!1),v("Không kết nối được server","error")}};async function q(t){var r;t.preventDefault();const e=s("lookup-input"),n=j((r=e==null?void 0:e.value)==null?void 0:r.trim());if(n){g(!0);try{const{res:a,json:c}=await J(`${window.Barbery.routes.lookup}?q=${encodeURIComponent(n)}`);if(g(!1),!a.ok||!(c!=null&&c.ok)){v((c==null?void 0:c.message)||"Tra cứu lỗi","error");return}const m=s("lookup-results"),d=s("lookup-empty"),h=s("lookup-list");h&&(h.innerHTML="");const o=c.data||[];if(o.length===0){m==null||m.classList.add("hidden"),d==null||d.classList.remove("hidden");return}d==null||d.classList.add("hidden"),m==null||m.classList.remove("hidden"),h&&(h.innerHTML=o.map(l=>{const k=l.total_duration_min?D(l.total_duration_min):"-",x=l.total_duration_min?H(l.booking_date,l.booking_time,l.total_duration_min):"-";return`
          <div class="bg-dark p-4 rounded-xl border border-gray-800">
            <p class="font-semibold text-gold">Mã: ${l.booking_code}</p>
            <p>Dịch vụ: ${l.service_name}</p>
            <p>Thợ: ${l.stylist_name}</p>

            <p>Ngày: ${l.booking_date}</p>
            <p>Giờ bắt đầu: ${l.booking_time}</p>
            <p>Tổng thời gian: ${k}</p>
            <p>Giờ kết thúc: ${x}</p>

            <p class="text-sm text-gray-400">Trạng thái: ${l.status}</p>
          </div>
        `}).join(""))}catch{g(!1),v("Không kết nối server","error")}}}document.addEventListener("DOMContentLoaded",async()=>{try{s("service-list")&&(g(!0),await I(),M(),S(),$(),y(),f(),g(!1));const t=s("lookup-form");t&&t.addEventListener("submit",q)}catch(t){console.error(t),g(!1),v((t==null?void 0:t.message)||"Lỗi tải dữ liệu","error")}});

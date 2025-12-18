/* ---------- Constants ---------- */
const DAYS = ['Monday','Tuesday','Wednesday','Thursday','Friday'];
const TIMES = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'];

/* ---------- Global State (filled from DB) ---------- */
const Data = {
  halls: [],        // [{id,name,capacity,equipment[]}]
  courses: [],      // [{code,title}]
  lecturers: [],    // ["Name", ...]
  bookings: []      // [{id, course, lecturer, day, time, size, hallId, notes}]
};

/* ---------- DOM refs ---------- */
const content = typeof document !== "undefined" ? document.getElementById('content') : null;

/* ---------- Utilities ---------- */
function el(html){
  const t = document.createElement('template');
  t.innerHTML = html.trim();
  return t.content.firstElementChild;
}
function uid(){ return Math.random().toString(36).slice(2,9); } // may be unused once DB ids are used
function toast(msg){
  const t = el(`<div style="position:fixed;bottom:18px;right:18px;background:#0f151d;border:1px solid #274058;padding:10px 12px;border-radius:10px;z-index:9999">${msg}</div>`);
  document.body.appendChild(t);
  setTimeout(()=>t.remove(),2200);
}
function hmToMin(hm){ const [h,m]=hm.split(':').map(Number); return h*60 + (m||0); }
function timeOverlap(t1, t2){
  const [aStart,aEnd] = t1.split('-').map(hmToMin);
  const [bStart,bEnd] = t2.split('-').map(hmToMin);
  return Math.max(aStart,bStart) < Math.min(aEnd,bEnd);
}
function conflictExists(newItem){
  return Data.bookings.some(b => (
    b.day===newItem.day && b.hallId===newItem.hallId && timeOverlap(b.time, newItem.time) && b.id!==newItem.id
  ));
}
function formatEquipment(eq){ return Array.isArray(eq) ? eq.join(', ') : (eq||''); }

/* ---------- Forecast (simple frequency-based) ---------- */
function forecastHotSpots(){
  const tally = {};
  Data.bookings.forEach(b=>{ const k = `${b.day}-${b.time}`; tally[k]=(tally[k]||0)+1; });
  return Object.entries(tally)
    .sort((a,b)=>b[1]-a[1])
    .slice(0,3)
    .map(([k])=>k); // ["Tuesday-10:00-12:00", ...]
}

/* ---------- API Helpers ---------- */
async function apiGet(url){
  const r = await fetch(url, { credentials: 'include' });
  if(!r.ok) throw new Error(`GET ${url} -> ${r.status}`);
  return r.json();
}
async function apiJSON(url, method, body){
  const r = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json' },
    credentials: 'include',
    body: JSON.stringify(body||{})
  });
  if(!r.ok) throw new Error(`${method} ${url} -> ${r.status}`);
  return r.json();
}

/* ---------- Load data from DB ---------- */
async function loadDataFromDB(){
  try{
    const dbData = await apiGet('../config/get_data.php');
    // expected: {halls:[{equipment:"Projector,Wi-Fi"}...], courses:[{code,title}], lecturers:["..."], bookings:[...]}

    // Normalize halls.equipment to array
    dbData.halls = (dbData.halls||[]).map(h=>{
      if(typeof h.equipment === 'string') h.equipment = h.equipment.split(',').map(s=>s.trim()).filter(Boolean);
      return h;
    });

    // Normalize courses to {code,name} since UI expects name
    dbData.courses = (dbData.courses||[]).map(c=>({
      code: c.code,
      name: c.title ?? c.name ?? c.code
    }));

    Object.assign(Data, dbData);

  const hallFilter = document.getElementById('hallFilter');
if (hallFilter) {
  hallFilter.innerHTML =
    `<option value="All">All Halls</option>` +
    Data.halls
      .map(h => `<option value="${h.id}">${h.name}</option>`)
      .join('');
}



    // Initial render
    setTab('dashboard');
    toast('Loaded data from server.');
  }catch(e){
    console.error(e);
    toast('Failed to load data from server.');
  }
}

/* ---------- Rendering Engine ---------- */
let charts = {};
function setTab(name){
  // toggle buttons state
  document.querySelectorAll('.tab').forEach(t=>{
    const active = t.dataset.tab===name;
    t.classList.toggle('active', active);
    t.setAttribute('aria-selected', active ? 'true' : 'false');
  });

  switch(name){
    case 'dashboard': return renderDashboard();
    case 'timetable': return renderTimetable();
    case 'halls':     return renderHalls();
    case 'requests':  return renderRequests();
    case 'analytics': return renderAnalytics();
    case 'settings':  return renderSettings();
  }
}

/* ----- Dashboard ----- */
function countConflicts(){
  let c=0;
  for(let i=0;i<Data.bookings.length;i++){
    for(let j=i+1;j<Data.bookings.length;j++){
      const a=Data.bookings[i], b=Data.bookings[j];
      if(a.day===b.day && a.hallId===b.hallId && timeOverlap(a.time,b.time)) c++;
    }
  }
  return c;
}
function calcUtilization(){
  // naive: sessions / (halls * time-slots)
  const slots = DAYS.length * (TIMES.length-1);
  const util = (Data.bookings.length / (Data.halls.length * slots)) * 100;
  return Math.round(util || 0);
}
(async () => {
  try {
    const demand = await loadDemand();

    // ===== Heatmap (Day × Time) =====
    // compute max for coloring
    const maxCnt = demand.bySlot.reduce((m, r) => Math.max(m, r.cnt), 0);
    const slotMap = new Map(
      demand.bySlot.map(r => [`${r.day}|${r.time}`, r.cnt])
    );

    // Build grid (table)
    const heat = el(`<div class="card">
        <div class="section-title">Demand Heatmap (by Slot)</div>
        <div class="heat-wrap"><table class="heat"><thead><tr>
          <th>Time</th>${DAYS.map(d=>`<th>${d}</th>`).join('')}
        </tr></thead><tbody></tbody></table></div>
      </div>`);
    const tbody = heat.querySelector('tbody');

    for (let i = 0; i < TIMES.length - 1; i++) {
      const start = TIMES[i];
      const end   = TIMES[i+1] || '18:00';
      const slot  = `${start}-${end}`;
      const tr = el(`<tr><th>${slot}</th>${DAYS.map(d => `<td data-day="${d}" data-time="${slot}"></td>`).join('')}</tr>`);
      DAYS.forEach(d => {
        const td = tr.querySelector(`td[data-day="${d}"][data-time="${slot}"]`);
        const count = slotMap.get(`${d}|${slot}`) || 0;
        td.textContent = count || '';
        td.style.background = heatColor(count, maxCnt);
        td.title = `${d} ${slot} • ${count} booking(s)`;
      });
      tbody.append(tr);
    }

    // ===== Top Halls Bar Chart =====
    const top = demand.byHall.slice(0, 10);
    const chartId = uid();
    const topCard = el(`<div class="card">
        <div class="section-title">Top Halls by Demand</div>
        <canvas id="${chartId}" height="160"></canvas>
      </div>`);

    // Attach to content (below earlier cards)
    content.append(el(`<div class="grid"></div>`));
    content.lastElementChild.append(heat, topCard);

    // Chart.js bar
    new Chart(document.getElementById(chartId), {
      type: 'bar',
      data: {
        labels: top.map(x => x.hallName),
        datasets: [{ label: 'Bookings', data: top.map(x => x.cnt), backgroundColor: '#1DB954' }]
      },
      options: {
        plugins: { legend: { display: false }},
        scales: { x: { ticks: { color: '#bcd0e2' }}, y: { ticks: { color: '#bcd0e2' }, beginAtZero: true } }
      }
    });

    // ===== Hotspots list (optional small panel) =====
    const hotList = el(`<div class="card">
        <div class="section-title">Hot Slots</div>
        <div class="list"></div>
      </div>`);
    const list = hotList.querySelector('.list');
    demand.hotspots.forEach(h => {
      list.append(el(`<div class="list-item">
        <div><strong>${h.day}</strong> • ${h.time}</div>
        <span class="badge">${h.cnt} bookings</span>
      </div>`));
    });
    content.lastElementChild.append(hotList);

  } catch (e) {
    console.error(e);
    // Fail silently on the dashboard; it's an enhancement
  }
})();


/* ----- Timetable ----- */
function normalizeTimeRange(t){
  // "10:0 - 12:00" -> "10:00-12:00"
  const [a,b] = String(t).replace(/\s+/g,'').split('-');
  const pad = (s)=> {
    const [h,m='00'] = s.split(':');
    return `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
  };
  return `${pad(a)}-${pad(b)}`;
}
function slotList(){
  // Build row slots once: ["08:00-09:00", ...]
  const slots = [];
  for(let i=0;i<TIMES.length;i++){
    const start = TIMES[i];
    const end   = TIMES[i+1] || '18:00';
    if(start===end) continue;
    slots.push(`${start}-${end}`);
  }
  return slots;
}
function timeToMin(hm){ const [h,m]=hm.split(':').map(Number); return h*60+(m||0); }
function rangesOverlap(r1, r2){
  const [a1,a2] = r1.split('-').map(timeToMin);
  const [b1,b2] = r2.split('-').map(timeToMin);
  return Math.max(a1,b1) < Math.min(a2,b2);
}

function renderTimetable(){
  content.innerHTML = '';
  const table = el(`<div class="card timetable">
    <table role="grid">
      <thead>
        <tr><th>Time</th>${DAYS.map(d=>`<th>${d}</th>`).join('')}</tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>`);
  const body = table.querySelector('tbody');

  // build hour rows
  TIMES.forEach((t,i)=>{
    const next = TIMES[i+1] || '18:00';
    const row = el(`<tr>
      <td><strong>${t}</strong>–${next}</td>
      ${DAYS.map(d=>`<td data-day="${d}" data-slot="${t}-${next}"></td>`).join('')}
    </tr>`);
    body.append(row);
  });

  content.append(table);

  // helper: format start hour string "HH:MM" -> row selector "^HH:MM-"
  function startSelector(day, timeRange) {
    const [startRaw] = String(timeRange||'').trim().split('-');
    const start = (startRaw || '').trim();
    return `td[data-day="${day.trim()}"][data-slot^="${start}-"]`;
  }

  // place bookings in their start-hour row
  Data.bookings.forEach(b=>{
    if(!b || !b.day || !b.time) return;

    const cell = body.querySelector(startSelector(b.day, b.time));
    if (cell) {
      const hall = Data.halls.find(h => String(h.id) === String(b.hallId));
      const hallName = hall ? hall.name : (b.hallName || b.hallId);

      const hasConflict = Data.bookings.some(x =>
        x !== b &&
        x.day && x.time &&
        x.day.trim() === b.day.trim() &&
        String(x.hallId) === String(b.hallId) &&
        timeOverlap(String(x.time).trim(), String(b.time).trim())
      );

      cell.append(el(
        `<span class="event ${hasConflict ? 'conflict':''}" 
               title="${b.course} • ${b.lecturer} • ${hallName} • ${b.time}">
           <strong>${b.course}</strong><br>
           <span class="hint">${hallName} • ${b.time}</span>
         </span>`
      ));
    }
  });
}




/* ----- Halls ----- */
function renderHalls(){
  content.innerHTML = '';
  const list = el(`<div class="stack"></div>`);

  Data.halls.forEach(h=>{
    const usage = Data.bookings.filter(b=>String(b.hallId)===String(h.id)).length;

    const item = el(`
      <div class="list-item"
           data-hall-id="${h.id}"
           data-hall-name="${(h.name||'').toLowerCase()}"
           data-capacity="${h.capacity}"
           data-equipment="${(h.equipment||[]).join('|').toLowerCase()}">
        <div>
          <div><strong>${h.name}</strong> <span class="hint">(${h.capacity} cap)</span></div>
          <div class="hint">${formatEquipment(h.equipment||[])}</div>
        </div>
        <div class="row">
          <span class="badge">${usage} sessions</span>
          <button class="btn" onclick="openBookingForm('${h.id}')">Book</button>
        </div>
      </div>
    `);

    list.append(item);
  });

  const wrap = el(`<div class="card"><div class="section-title">Halls</div></div>`);
  wrap.append(list);
  content.append(wrap);
}


/* ----- Requests (Booking CRUD) ----- */
function renderRequests(){
  content.innerHTML = '';
  const wrap = el(`<div class="grid"></div>`);
  const form = el(`<div class="card" style="position:relative">
    <div class="section-title">New Booking</div>
    <div class="stack" id="bookingForm"></div>
  </div>`);
  wrap.append(form);
  content.append(wrap);
  buildBookingForm();

  const list = el(`<div class="card"><div class="section-title">All Bookings</div><div class="list" id="reqList"></div></div>`);
  wrap.append(list);
  refreshRequestsList();
}

function buildBookingForm(prefHall = null, editing = null){
  const root = document.getElementById('bookingForm');
  root.innerHTML = '';

  // Pre-fill values if editing
  const isEditing = !!editing;
  const courseVal   = isEditing ? `${editing.course}` : (Data.courses[0]?.code || '');
  const lecturerVal = isEditing ? editing.lecturer : (Data.lecturers[0] || '');
  const dayVal      = isEditing ? editing.day : 'Monday';

  // derive start & duration from time slot like "10:00-12:00"
  let startVal = '08:00', durVal = 1;
  if (isEditing && editing.time) {
    const [s,e] = editing.time.split('-');
    startVal = s;
    const sH = parseInt(s.split(':')[0],10), eH = parseInt(e.split(':')[0],10);
    durVal = Math.max(1, Math.min(4, eH - sH));
  }

  const sizeVal     = isEditing ? editing.size : 60;
  const hallIdVal   = isEditing ? String(editing.hallId) : (prefHall? String(prefHall) : (Data.halls[0]?.id??''));
  const notesVal    = isEditing ? (editing.notes || '') : '';

  const startOptions = TIMES.slice(0, TIMES.length - 1);

  root.append(el(`<div class="grid">
    <div class="stack">
      <label>Course</label>
      <select id="bfCourse" class="select">
        ${Data.courses.map(c => `<option value="${c.code}" ${c.code===courseVal?'selected':''}>${c.code} — ${c.name}</option>`).join('')}
      </select>
    </div>
    <div class="stack">
      <label>Lecturer</label>
      <select id="bfLecturer" class="select">
        ${Data.lecturers.map(l => `<option ${l===lecturerVal?'selected':''}>${l}</option>`).join('')}
      </select>
    </div>
    <div class="stack">
      <label>Day</label>
      <select id="bfDay" class="select">
        ${DAYS.map(d => `<option ${d===dayVal?'selected':''}>${d}</option>`).join('')}
      </select>
    </div>
    <div class="stack">
      <label>Start</label>
      <select id="bfStart" class="select">
        ${startOptions.map(t => `<option ${t===startVal?'selected':''}>${t}</option>`).join('')}
      </select>
    </div>
    <div class="stack">
      <label>Duration (hours)</label>
      <select id="bfDur" class="select">
        ${[1,2,3,4].map(h => `<option value="${h}" ${Number(h)===Number(durVal)?'selected':''}>${h}</option>`).join('')}
      </select>
    </div>
    <div class="stack">
      <label>Expected Size</label>
      <input id="bfSize" class="input" type="number" min="1" step="1" value="${sizeVal}" />
    </div>
    <div class="stack">
      <!-- Show hall name but keep id in value -->
      <label>Hall</label>
      <select id="bfHall" class="select">
        ${Data.halls.map(h => `
          <option value="${h.id}" ${String(h.id)===String(hallIdVal)?'selected':''}>${h.name}</option>
        `).join('')}
      </select>
    </div>
    <div class="stack">
      <label>Notes</label>
      <input id="bfNotes" class="input" placeholder="e.g., needs Smart Board" value="${notesVal}"/>
    </div>
  </div>`));

  const actions = el(`<div class="row" style="margin-top:10px">
    <button class="btn" id="checkBtn">Check Availability</button>
    ${isEditing
      ? `<button class="btn primary" id="updateBtn" data-id="${editing.id}">Update Booking</button>
         <button class="btn ghost" id="cancelEditBtn">Cancel</button>`
      : `<button class="btn primary" id="createBtn">Create Booking</button>`
    }
  </div>`);
  root.append(actions);

  document.getElementById('checkBtn').onclick = checkAvailability;

  if (isEditing) {
    document.getElementById('updateBtn').onclick = () => updateBooking(editing.id);
    document.getElementById('cancelEditBtn').onclick = () => { buildBookingForm(); };
  } else {
    document.getElementById('createBtn').onclick = createBooking;
  }
}


function openBookingForm(hallId){
  setTab('requests');
  buildBookingForm(hallId);
}

function timeAdd(start, hours){
  const [h,m] = start.split(':').map(Number);
  const endH = h + Number(hours);
  return `${String(endH).padStart(2,'0')}:${String(m).padStart(2,'0')}`;
}

function checkAvailability(){
  const b = readForm();
  const hall = hallById(b.hallId);
  const okCap = hall && (hall.capacity >= b.size);
  const hasConflict = conflictExists(b);
  alert(`Capacity: ${okCap? 'OK' : 'Too small'}\nConflict: ${hasConflict? 'YES' : 'No'}`);
}

async function createBooking(){
  const b = readForm();
  const err = validateBooking(b);
  if(err) return alert(err);

  try{
    const res = await apiJSON('../config/add_booking.php','POST', b);
    if(res.success){
      b.id = res.id;
      Data.bookings.push(b);
      toast('Booking created.');
      refreshRequestsList();
    }else{
      alert('Error saving booking: ' + (res.error || 'Unknown error'));
    }
  }catch(e){
    console.error(e);
    alert('Server error while creating booking.');
  }
}

function readForm(){
  const course = document.getElementById('bfCourse').value.split(' — ')[0];
  const lecturer = document.getElementById('bfLecturer').value;
  const day = document.getElementById('bfDay').value;
  const start = document.getElementById('bfStart').value;
  const dur = Number(document.getElementById('bfDur').value);
  const time = `${start}-${timeAdd(start, dur)}`;
  const size = Number(document.getElementById('bfSize').value);
  const hallId = document.getElementById('bfHall').value;
  const notes = document.getElementById('bfNotes').value;
  return { course, lecturer, day, time, size, hallId, notes };
}

function validateBooking(b){
  const hall = hallById(b.hallId);
  if(!hall) return 'Unknown hall';
  if(b.size <= 0) return 'Expected size must be greater than 0.';
  if(b.size > hall.capacity) return `Hall capacity (${hall.capacity}) is less than expected size (${b.size}).`;
  if(conflictExists(b)) return 'Time slot already booked for this hall.';
  return '';
}

function hallById(id){ return Data.halls.find(h=>h.id===id); }

function refreshRequestsList(){
  const root = document.getElementById('reqList'); if(!root) return;
  root.innerHTML = '';
  if(Data.bookings.length===0){
    root.append(el(`<div class="hint">No bookings yet.</div>`));
    return;
  }

  Data.bookings.forEach((b, idx)=>{
    const row = el(`<div class="list-item">
      <div>
        <div><span class="badge">${b.day}</span> <strong>${b.course}</strong> • ${b.time}</div>
        <div class="hint">${b.hallId} • ${b.lecturer}${b.notes? ' • '+b.notes:''}</div>
      </div>
      <div class="row">
        <span class="badge">${b.size}</span>
        <button class="btn" aria-label="Edit booking">Edit</button>
        <button class="btn" aria-label="Delete booking">Delete</button>
      </div>
    </div>`);
    const [editBtn, delBtn] = row.querySelectorAll('.btn');

    editBtn.onclick = async ()=>{
      const newSize = parseInt(prompt('Expected size', b.size)||b.size,10);
      const newNotes = prompt('Notes', b.notes||'')||'';
      const copy = { ...b, size: newSize, notes: newNotes };

      const err = validateBooking(copy); if(err) return alert(err);

      try{
        const res = await apiJSON('../config/update_booking.php','POST', copy);
        if(res.success){
          Data.bookings[idx] = copy;
          refreshRequestsList();
          toast('Booking updated.');
        }else{
          alert('Update failed: ' + (res.error || 'Unknown error'));
        }
      }catch(e){
        console.error(e);
        alert('Server error while updating.');
      }
    };

    delBtn.onclick = async ()=>{
      if(!confirm('Delete booking?')) return;
      try{
        const res = await apiJSON('../config/delete_booking.php','POST', { id: b.id });
        if(res.success){
          Data.bookings.splice(idx,1);
          refreshRequestsList();
          toast('Booking deleted.');
        }else{
          alert('Delete failed: ' + (res.error || 'Unknown error'));
        }
      }catch(e){
        console.error(e);
        alert('Server error while deleting.');
      }
    };

    root.append(row);
  });
}

/* ----- Analytics ----- */
/* ----- Analytics (util/daily + Forecast Heatmap) ----- */
async function renderAnalytics(){
  content.innerHTML = '';

  // 1) Existing two charts (utilization + daily)
  const c1 = uid(); const c2 = uid();
  content.append(el(`<div class="grid">
    <div class="card"><div class="section-title">Utilization by Hall</div><canvas id="${c1}" height="160"></canvas></div>
    <div class="card"><div class="section-title">Daily Load (sessions)</div><canvas id="${c2}" height="160"></canvas></div>
  </div>`));

  const util = Data.halls.map(h=> Data.bookings.filter(b=>b.hallId===h.id).length);
  charts.util && charts.util.destroy();
  charts.util = new Chart(document.getElementById(c1), {
    type:'bar',
    data:{ labels: Data.halls.map(h=>h.name), datasets:[{ label:'Sessions', data: util, backgroundColor: '#1DB954' }]},
    options:{ plugins:{legend:{display:false}}, scales:{x:{ticks:{color:'#bcd0e2'}}, y:{ticks:{color:'#bcd0e2'}}} }
  });

  const daily = DAYS.map(d=> Data.bookings.filter(b=>b.day===d).length);
  charts.daily && charts.daily.destroy();
  charts.daily = new Chart(document.getElementById(c2), {
    type:'line',
    data:{ labels: DAYS, datasets:[{ label:'Sessions', data: daily, borderColor:'#3b82f6', backgroundColor:'rgba(59,130,246,.2)', tension:.3, fill:true }]},
    options:{ plugins:{legend:{display:false}}, scales:{x:{ticks:{color:'#bcd0e2'}}, y:{ticks:{color:'#bcd0e2'}}} }
  });

  // 2) New: Forecast Heatmap card
  const heatCard = el(`
    <div class="card">
      <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
        <div class="section-title">Hall Demand Forecast (Heatmap)</div>
        <div class="hm-legend"><span>Low</span><span class="hm-swatch"></span><span>High</span></div>
      </div>
      <div id="forecastWrap" style="overflow:auto; margin-top:10px;"></div>
      <div class="divider"></div>
      <div>
        <div class="section-title">Top Hot Slots</div>
        <div id="forecastTop" class="list"></div>
      </div>
      <div class="hint" style="margin-top:8px;">Based on historical bookings frequency per day & time-slot.</div>
    </div>
  `);
  content.append(heatCard);

  // Fetch forecast data via PHP proxy
  try {
    const f = await apiGet('../config/get_forecast.php'); // { ok, days, slots, score:[{day,time,demand}], top:[...] }
    if (!f.ok) throw new Error(f.error || 'Forecast not ok');

    // Build a map for quick lookups
    const demandMap = {};
    let maxDemand = 0;
    (f.score || []).forEach(s => {
      const key = `${s.day}__${s.time}`;
      demandMap[key] = s.demand;
      if (s.demand > maxDemand) maxDemand = s.demand;
    });

    // Color helper: darker green for higher demand
    function cellStyle(d){
      if (!maxDemand) return 'background:#0b1f15; border:1px solid #203345;';
      // scale 0..1
      const x = d / maxDemand;
      // interpolate light -> dark green
      const bg = `hsl(140, 60%, ${Math.round(22 + (1 - x) * 20)}%)`; // ~22%..42% lightness
      const bd = `hsl(140, 50%, ${Math.round(20 + (1 - x) * 18)}%)`;
      return `background:${bg}; border:1px solid ${bd};`;
    }

    // Build Heatmap Table
    const wrap = document.getElementById('forecastWrap');
    const table = el(`<table class="heatmap" role="grid"><thead></thead><tbody></tbody></table>`);
    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');

    // Header row: first empty + time slots
    const hRow = el('<tr></tr>');
    hRow.append(el('<th style="position:sticky;left:0;background:#0f141b;z-index:1;">Day</th>'));
    (f.slots || []).forEach(t => {
      hRow.append(el(`<th>${t}</th>`));
    });
    thead.append(hRow);

    // Rows per day
    (f.days || DAYS).forEach(day => {
      const tr = el('<tr></tr>');
      tr.append(el(`<th style="position:sticky;left:0;background:#0f141b;z-index:1;">${day}</th>`));
      (f.slots || []).forEach(t => {
        const d = demandMap[`${day}__${t}`] || 0;
        const td = el(`<td><div class="hm-cell" title="${day} ${t} • demand:${d}" style="padding:6px 8px; ${cellStyle(d)}">${d}</div></td>`);
        tr.append(td);
      });
      tbody.append(tr);
    });

    wrap.innerHTML = '';
    wrap.append(table);

    // Top hot slots list
    const topWrap = document.getElementById('forecastTop');
    topWrap.innerHTML = '';
    if ((f.top || []).length === 0) {
      topWrap.append(el(`<div class="hint">Not enough data yet.</div>`));
    } else {
      (f.top || []).forEach(item => {
        topWrap.append(el(`
          <div class="list-item">
            <div><strong>${item.day}</strong> • ${item.time}</div>
            <span class="badge">${item.demand} bookings</span>
          </div>
        `));
      });
    }
  } catch (e) {
    console.error(e);
    document.getElementById('forecastWrap').innerHTML =
      `<div class="hint">Couldn’t load forecast. Start ml_service and ensure get_forecast.php can reach it.</div>`;
  }
}


/* ----- Settings (client-only helpers kept minimal) ----- */
function renderSettings(){
  content.innerHTML = '';
  const wrap = el(`<div class="grid"></div>`);
  const hallsCard = el(`<div class="card">
    <div class="section-title">Halls (read-only)</div>
    <div class="stack" id="hallsAdmin"></div>
    <div class="hint">Halls are managed in the database (config). This view is read-only.</div>
  </div>`);
  wrap.append(hallsCard);

  const exportCard = el(`<div class="card">
    <div class="section-title">Export (CSV)</div>
    <div class="stack">
      <button class="btn" id="exportCsvBtn">Export Bookings CSV</button>
      <div class="hint">Exports bookings currently loaded from the server.</div>
    </div>
  </div>`);
  wrap.append(exportCard);

  content.append(wrap);

  const list = document.getElementById('hallsAdmin');
  list.innerHTML = '';
  Data.halls.forEach(h=>{
    list.append(el(`<div class="list-item">
      <div><strong>${h.name}</strong> <span class="hint">${h.capacity} cap</span><div class="hint">${formatEquipment(h.equipment)}</div></div>
    </div>`));
  });

  document.getElementById('exportCsvBtn').onclick = exportCSV;
}

/* ----- Filters & Actions ----- */
function applyFilters(){
  const hallVal = document.getElementById('hallFilter').value;       // "All" or hall ID
  const minCap  = Number(document.getElementById('capacityFilter').value || 0);
  const equip   = document.getElementById('equipmentFilter').value;  // "Any" or equipment name

  // Show the Halls tab (re-renders the list)
  setTab('halls');

  // Wait for the list to exist, then filter rows
  requestAnimationFrame(() => {
    const items = content.querySelectorAll('.list-item[data-hall-id]');
    const eqWanted = (equip || '').toLowerCase();

    items.forEach(li => {
      const id   = li.dataset.hallId;                 // string
      const cap  = parseInt(li.dataset.capacity, 10); // number
      const eqs  = li.dataset.equipment || '';        // "projector|wi-fi"
      const okCap   = !minCap || cap >= minCap;
      const okEquip = equip === 'Any' || eqs.includes(eqWanted);
      const okHall  = hallVal === 'All' || String(id) === String(hallVal);

      li.style.display = (okCap && okEquip && okHall) ? '' : 'none';
    });
  });
}


function exportCSV(){
  const rows = [['Course','Lecturer','Day','Time','Hall','Size','Notes']].concat(
    Data.bookings.map(b=>[b.course,b.lecturer,b.day,b.time,b.hallId,b.size,b.notes||''])
  );
  const csv = rows.map(r=>r.map(x=>`"${String(x).replaceAll('"','""')}"`).join(',')).join('\n');
  const blob = new Blob([csv],{type:'text/csv'});
  const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='nsbm_bookings.csv'; a.click();
}

/* ---------- Init / Wiring ---------- */
function init(){
  // Robust tab handling (event delegation)
  const nav = document.querySelector('.nav');
  if (nav) {
    nav.addEventListener('click', (e) => {
      const btn = e.target.closest('.tab');
      if (!btn) return;
      const name = btn.dataset.tab;
      if (name) setTab(name);
    });
  }

  // Side actions
  const applyBtn = document.getElementById('applyFilters');
  if(applyBtn) applyBtn.addEventListener('click', applyFilters);

  const newBookingBtn = document.getElementById('newBookingBtn');
  if(newBookingBtn) newBookingBtn.addEventListener('click', ()=> setTab('requests'));

  const exportBtn = document.getElementById('exportBtn');
  if(exportBtn) exportBtn.addEventListener('click', exportCSV);

  const printBtn = document.getElementById('printBtn');
  if(printBtn) printBtn.addEventListener('click', ()=> window.print());

  const seedBtn = document.getElementById('seedBtn');
  if(seedBtn){
    seedBtn.disabled = true;
    seedBtn.title = 'Demo disabled (using real DB)';
    seedBtn.addEventListener('click', ()=> toast('Demo data is disabled. Now using database.'));
  }

  // Load from DB and render
  loadDataFromDB();
}

// Kick off after DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

async function updateBooking(id){
  const b = readForm();
  b.id = Number(id);

  const err = validateBooking(b);
  if (err) return alert(err);

  try{
    const res = await apiJSON('../config/update_booking.php','POST', b);
    if(res.success){
      // Update local state
      const idx = Data.bookings.findIndex(x => Number(x.id) === Number(id));
      if (idx >= 0) Data.bookings[idx] = { id:Number(id), ...b };
      toast('Booking updated.');
      // Refresh both requests list and dashboard
      refreshRequestsList();
      setTab('dashboard');
    }else{
      alert('Update failed: ' + (res.error || 'Unknown error'));
    }
  }catch(e){
    console.error(e);
    alert('Server error while updating.');
  }
}
const API_BASE = '/config'; // adjust if your path differs

async function addHallAPI(payload){ return apiJSON(`${API_BASE}/add_hall.php`, 'POST', payload); }
async function updateHallAPI(payload){ return apiJSON(`${API_BASE}/update_hall.php`, 'POST', payload); }
async function deleteHallAPI(id){ return apiJSON(`${API_BASE}/delete_hall.php`, 'POST', { id }); }

// Rebuild the sidebar filter + rebuild booking form if we're on Requests
function refreshHallDependents() {
  const hallFilter = document.getElementById('hallFilter');
  if (hallFilter) {
    hallFilter.innerHTML = `<option value="All">All</option>` + Data.halls
      .map(h => `<option value="${h.id}">${h.name}</option>`).join('');
  }
  // do NOT edit buildBookingForm; just re-render Requests if currently there
  const active = document.querySelector('.tab.active')?.dataset.tab;
  if (active === 'requests') renderRequests();
}

function renderSettings(){
  content.innerHTML = '';
  const wrap = el(`<div class="grid"></div>`);

  // Add form
  const addCard = el(`
    <div class="card">
      <div class="section-title">Add Hall</div>
      <div class="stack">
        <div class="row"><label style="min-width:110px">Code</label><input id="h_code" class="input" placeholder="e.g., C2-009"></div>
        <div class="row"><label style="min-width:110px">Name</label><input id="h_name" class="input" placeholder="C2-009 (Lecture Hall)"></div>
        <div class="row"><label style="min-width:110px">Capacity</label><input id="h_capacity" type="number" min="1" class="input" placeholder="350"></div>
        <div class="row"><label style="min-width:110px">Equipment</label><input id="h_equipment" class="input" placeholder="Projector,Smart Board,Wi-Fi"></div>
        <div class="row"><button class="btn primary" id="h_add_btn">Add Hall</button></div>
      </div>
    </div>
  `);

  // List
  const listCard = el(`
    <div class="card">
      <div class="section-title">Manage Halls</div>
      <div class="list" id="hallList"></div>
    </div>
  `);

  wrap.append(addCard, listCard);
  content.append(wrap);

  // render table
  function drawHallList(){
    const root = document.getElementById('hallList');
    root.innerHTML = '';
    if (!Data.halls.length) {
      root.append(el(`<div class="hint">No halls yet.</div>`));
      return;
    }
    Data.halls
      .slice()
      .sort((a,b)=>a.name.localeCompare(b.name))
      .forEach(h => {
        const row = el(`
          <div class="list-item">
            <div>
              <div><strong>${h.name}</strong> <span class="hint">(${h.code}) • ${h.capacity} cap</span></div>
              <div class="hint">${(h.equipment||[]).join(', ') || '—'}</div>
            </div>
            <div class="row">
              <button class="btn" data-edit="${h.id}">Edit</button>
              <button class="btn" data-del="${h.id}">Delete</button>
            </div>
          </div>
        `);
        // Edit handler (inline prompt version)
        row.querySelector('[data-edit]').onclick = async () => {
          const code = prompt('Code', h.code) ?? h.code;
          const name = prompt('Name', h.name) ?? h.name;
          const cap  = parseInt(prompt('Capacity', String(h.capacity)) ?? h.capacity, 10);
          const eq   = prompt('Equipment (comma separated)', (h.equipment||[]).join(',')) ?? (h.equipment||[]).join(',');
          if (!name || !code || !cap || cap<=0) return;
          const payload = { id: h.id, code, name, capacity: cap, equipment: eq };
          try{
            const res = await updateHallAPI(payload);
            if (!res.success) return alert(res.error || 'Update failed');
            // Update local state
            Object.assign(h, { code, name, capacity: cap, equipment: eq.split(',').map(s=>s.trim()).filter(Boolean) });
            drawHallList();
            refreshHallDependents();
            toast('Hall updated.');
          }catch(e){ console.error(e); alert('Server error while updating hall.'); }
        };

        // Delete
        row.querySelector('[data-del]').onclick = async () => {
          if (!confirm(`Delete hall "${h.name}"?`)) return;
          try{
            const res = await deleteHallAPI(h.id);
            if (!res.success) return alert(res.error || 'Delete failed');
            // remove locally
            Data.halls = Data.halls.filter(x=>x.id !== h.id);
            drawHallList();
            refreshHallDependents();
            toast('Hall deleted.');
          }catch(e){ console.error(e); alert('Server error while deleting hall.'); }
        };

        document.getElementById('hallList').append(row);
      });
  }
  drawHallList();

  // Add
  document.getElementById('h_add_btn').onclick = async ()=>{
    const code = document.getElementById('h_code').value.trim();
    const name = document.getElementById('h_name').value.trim();
    const capacity = parseInt(document.getElementById('h_capacity').value || '0', 10);
    const equipment = document.getElementById('h_equipment').value.trim();

    if (!code || !name || capacity<=0) return alert('Please fill Code, Name and a valid Capacity.');
    try{
      const res = await addHallAPI({ code, name, capacity, equipment });
      if (!res.success) return alert(res.error || 'Add failed');

      Data.halls.push(res.hall); // res.hall contains id + normalized equipment array
      // clear form
      document.getElementById('h_code').value='';
      document.getElementById('h_name').value='';
      document.getElementById('h_capacity').value='';
      document.getElementById('h_equipment').value='';

      drawHallList();
      refreshHallDependents();
      toast('Hall added.');
    }catch(e){ console.error(e); alert('Server error while adding hall.'); }
  };
}

async function addHallAPI(h) {
  const payload = {
    code: (h.code || '').trim(),
    name: (h.name || '').trim(),
    capacity: Number(h.capacity || 0),
    equipment: Array.isArray(h.equipment) ? h.equipment.join(',') : String(h.equipment || '')
  };
  const r = await fetch('../config/add_hall.php', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(payload)
  });
  return r.json();  // will include {success:false,error:"..."} on failure
}

async function loadDemand() {
  const r = await fetch('../config/get_demand.php', { credentials: 'include' });
  if (!r.ok) throw new Error('demand fetch failed');
  return r.json(); // { bySlot:[{day,time,cnt}], byHall:[{hallName,cnt}], hotspots:[] }
}

function heatColor(n, max) {
  // dark → bright green scale
  if (!max) return '#102218';
  const t = Math.min(1, n / max);
  const g = Math.round(80 + t * 120); // 80..200
  return `rgb(20, ${g}, 80)`;
}




// Lecturer/lecturer.js
(function () {
  // 1) Override data loader to call lecturer-specific endpoint
  const origLoad = window.loadDataFromDB;
  window.loadDataFromDB = async function () {
    try {
      const r = await fetch('../config/get_data_lecturer.php', {credentials:'include'});
      const dbData = await r.json();
      if (!r.ok) throw new Error(dbData.error || ('HTTP '+r.status));

      // Normalize equipment + courses like app.js does
      dbData.halls = (dbData.halls||[]).map(h=>{
        if (typeof h.equipment === 'string') {
          h.equipment = h.equipment.split(',').map(s=>s.trim()).filter(Boolean);
        }
        return h;
      });
      dbData.courses = (dbData.courses||[]).map(c=>({ code:c.code, name:c.title ?? c.name ?? c.code }));

      // Stuff into global Data
      Object.assign(window.Data, dbData);

      // Populate hall filter
      const hallFilter = document.getElementById('hallFilter');
      if (hallFilter) {
        hallFilter.innerHTML = `<option value="All">All</option>` +
          window.Data.halls.map(h=>`<option>${h.name}</option>`).join('');
        hallFilter.dataset.mode = 'byName'; // for your filtering fix
      }

      // First render
      window.setTab('dashboard');
    } catch (e) {
      console.error(e);
      alert('Failed to load lecturer data.');
    }
  };

  // 2) After the booking form is built, force lecturer to current user
  const origBuild = window.buildBookingForm;
  window.buildBookingForm = function (prefHall) {
    origBuild(prefHall);
    const sel = document.getElementById('bfLecturer');
    if (sel && window.CURRENT_LECTURER) {
      sel.innerHTML = `<option>${window.CURRENT_LECTURER}</option>`;
      sel.disabled = true;
      sel.title = 'Locked to your account';
    }
  };

  // 3) Hook createBooking to send to lecturer-only endpoint
  const origCreate = window.createBooking;
  window.createBooking = async function () {
    const b = window.readForm();
    // Force lecturer name from session
    b.lecturer = window.CURRENT_LECTURER;

    const err = window.validateBooking(b);
    if (err) return alert(err);

    try {
      const r = await fetch('../config/add_booking_lecturer.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        credentials:'include',
        body: JSON.stringify(b)
      });
      const data = await r.json();
      if (data.success) {
        b.id = data.id;
        window.Data.bookings.unshift(b);
        window.refreshRequestsList();
        alert('Booking created.');
      } else {
        alert('Error: ' + (data.error || 'Unknown error'));
      }
    } catch (e) {
      console.error(e); alert('Server error while creating booking.');
    }
  };

  // 4) Kick off (the shared app.js calls loadDataFromDB() in init)
})();

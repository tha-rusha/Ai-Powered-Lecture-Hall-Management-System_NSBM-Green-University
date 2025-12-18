// Try authenticated endpoint first; if 401, fall back to public data
async function load(){
  // 1) Try student-specific API (works when logged in as student)
  try {
    const r = await fetch('../config/get_student_data.php', { credentials:'include' });
    if (r.ok) {
      const data = await r.json();
      // expected by your render code:
      window.Student = {
        me: data.me || null,
        courses: data.courses || [],
        halls: data.halls || [],
        timetable: data.timetable || data.bookings || []
      };
      initStudentUI();   // call your existing render/bootstrap
      return;
    }
  } catch (_) {
    /* ignore and fall through to public mode */
  }

  // 2) Public/guest mode (no session)
  const r2 = await fetch('../config/get_data.php'); // public dataset: halls/courses/bookings
  if (!r2.ok) throw new Error('public data failed');
  const pub = await r2.json();

  window.Student = {
    me: null,                    // guest
    courses: pub.courses || [],  // shown in filters
    halls: pub.halls || [],
    timetable: pub.bookings || [] // whole timetable; your UI can still filter
  };

  initStudentUI();
}

// Call on DOM ready (you already had this in your file)
document.addEventListener('DOMContentLoaded', ()=>{
  document.querySelector('.nav')
    .addEventListener('click', (e)=>{
      const b = e.target.closest('.tab'); if(b) setTab(b.dataset.tab);
    });
  load().catch(()=>alert('Failed to load student data'));
});

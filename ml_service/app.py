# ml_service/app.py
from flask import Flask, jsonify, request
from flask_cors import CORS
import pymysql

# --- Flask app ---
app = Flask(__name__)
CORS(app)  # optional, useful during dev

# --- DB helper ---
def get_conn():
    return pymysql.connect(
        host="localhost",
        user="root",
        password="",
        database="nsbm",
        cursorclass=pymysql.cursors.DictCursor,
        autocommit=True,
    )

# --- Utils ---
DAYS = ["Monday","Tuesday","Wednesday","Thursday","Friday"]

def slot_key(slot: str) -> int:
    """Sort time ranges like '10:00-12:00' by start time."""
    try:
        start = slot.split("-")[0]
        h, m = start.split(":")
        return int(h) * 60 + int(m)
    except Exception:
        return 0

# ---------- Routes ----------
@app.get("/health")
def health():
    return jsonify(ok=True)

@app.post("/train")
def train():
    # placeholder "training" (you can wire real model later)
    # example: count unique (day,time_slot) pairs
    with get_conn() as conn, conn.cursor() as cur:
        cur.execute("SELECT COUNT(*) AS c FROM (SELECT day, time_slot FROM bookings GROUP BY day, time_slot) t;")
        row = cur.fetchone()
    return jsonify(ok=True, keys=row["c"])

@app.get("/forecast")
def forecast():
    """
    Baseline forecast = historical frequency of bookings per (day, time_slot).
    Returns:
      {
        "ok": true,
        "days": ["Monday",...],
        "slots": ["08:00-09:00", ...]  # all distinct slots sorted by start time
        "score": [{"day":"Tuesday","time":"10:00-12:00","demand":12}, ...],
        "top":   [{"day":"Tuesday","time":"10:00-12:00","demand":12}, ...]   # top 3
      }
    """
    with get_conn() as conn, conn.cursor() as cur:
        cur.execute("""
            SELECT day, time_slot, COUNT(*) AS cnt
            FROM bookings
            GROUP BY day, time_slot
        """)
        rows = cur.fetchall()

    # unique slots present in data, sorted by start time
    all_slots = sorted({r["time_slot"] for r in rows}, key=slot_key)

    # map (day, slot) -> demand count
    hist = {(r["day"], r["time_slot"]): int(r["cnt"]) for r in rows}

    score = []
    for d in DAYS:
        for t in all_slots:
            score.append({
                "day": d,
                "time": t,
                "demand": hist.get((d, t), 0)
            })

    top = sorted(score, key=lambda x: x["demand"], reverse=True)[:3]
    return jsonify({"ok": True, "days": DAYS, "slots": all_slots, "score": score, "top": top})

# --- Dev server ---
if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5001, debug=True)

import socket
import psycopg2
import os

# === PostgreSQL DB Config (your real settings) ===
DB_CONFIG = {
    "host": "46.105.63.64",
    "port": 5437,
    "database": "taxiora_gps",
    "user": "txiuser_iotgps",
    "password": "HZ6oYUzLNhmV9C"
}

# === Function to check if VPN (WireGuard) is active ===
def is_vpn_active():
    response = os.system("ping -n 1 46.105.63.64 >nul")
    return response == 0

# === Start the TCP GPS Server ===
HOST = '0.0.0.0'
PORT = 8080

with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.bind((HOST, PORT))
    s.listen()
    print(f"🚀 GPS TCP Server running on {HOST}:{PORT}")
    
    while True:
        conn, addr = s.accept()
        with conn:
            print(f"📡 Connected by {addr}")
            while True:
                data = conn.recv(1024)
                if not data:
                    break

                gps_data = data.decode(errors='ignore').strip()
                print(f"📦 Received Data: {gps_data}")

                if is_vpn_active():
                    try:
                        conn_pg = psycopg2.connect(**DB_CONFIG)
                        cur = conn_pg.cursor()

                        # Example insert with raw data only (for now)
                        cur.execute("""
                            INSERT INTO gps_data (imei, latitude, longitude, speed) 
                            VALUES (%s, %s, %s, %s)
                        """, ('simulated_imei', 36.8, 10.1, 0))  # You can parse real data later

                        conn_pg.commit()
                        cur.close()
                        conn_pg.close()
                        print("✅ Inserted into PostgreSQL")
                    except Exception as e:
                        print(f"❌ Database Error: {e}")
                else:
                    print("⚠️ WireGuard VPN not active. Skipping DB insert.")

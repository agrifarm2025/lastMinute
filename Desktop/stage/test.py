import socket

HOST = '0.0.0.0'
PORT = 8080  # or 5001, 9000...

with socket.socket(socket.AF_INET, socket.SOCK_STREAM) as s:
    s.bind((HOST, PORT))
    s.listen()
    print(f"🚀 Server running on {HOST}:{PORT}")
    while True:
        conn, addr = s.accept()
        with conn:
            print(f"✅ Connected by {addr}")
            while True:
                data = conn.recv(1024)
                if not data:
                    break
                print(f"📦 Received: {data.decode(errors='ignore')}")

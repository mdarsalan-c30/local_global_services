import sqlite3
import os

db_path = r"d:\websiteproject\SAPIT WEBSITE FINAL\api\database.db"

if not os.path.exists(db_path):
    print("Database not found!")
    exit(1)

conn = sqlite3.connect(db_path)
cursor = conn.cursor()

cursor.execute("SELECT id, name, service_key FROM submenus")
rows = cursor.fetchall()

print("Database Submenus (id, name, service_key):")
for r in rows:
    print(f" - ID {r[0]}: '{r[1]}' (key: '{r[2]}')")

conn.close()

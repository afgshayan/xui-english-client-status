
# 🇮🇷 Persian VPN User Status Page for x-ui-english

A responsive, easy-to-install PHP page that displays live information about each user's VPN account from the [x-ui-english](https://github.com/NidukaAkalanka/x-ui-english) panel — specifically built for Persian users.

> ✅ Fully RTL and Persian-friendly  
> 🧠 Smart usage and expiry calculations  
> 🎨 Clean and modern design with Bootstrap

---

## 📌 What does it do?

This lightweight PHP tool reads the `x-ui-english` SQLite database directly and shows the following:

- UUID & username (email field)
- Total quota and remaining quota (in GB)
- Used data (upload + download)
- Expiry time with human-readable text like:  
  ➤ `7 روز مانده` — *7 days remaining*  
  ➤ `2 روز گذشته` — *2 days expired*
- IP limit (number of allowed devices)

It’s designed as a simple **shareable status page** for each user, without needing login — just a UUID in the URL.

---

## 🛠 Installation (Ubuntu 22.04+)

### 1. Install Apache and PHP:

```bash
sudo apt update
sudo apt install apache2 php php-sqlite3 -y
```

### 2. Create the PHP file:

```bash
sudo nano /var/www/html/status.php
```

Paste the contents of `status.php` (from this repo) into the file.

### 3. Set correct permissions:

```bash
sudo chmod 755 /var/www/html/status.php
```

### 4. Restart Apache:

```bash
sudo systemctl restart apache2
```

---

## 🔍 Usage

Just open the following link in your browser:

```
http://YOUR_SERVER_IP/status.php?uuid=USER_UUID
```

Example:

```
http://123.123.123.123/status.php?uuid=000000000-00000-000000-00-00000000
```

You can generate and share this link with each VPN user so they can check their status anytime — **no login required**.

---

## 📁 File Structure

```
/var/www/html/
└── status.php   ← Main status display page
```

---

## ⚙️ Technical Details

- 📦 **Database file**: `/etc/x-ui-english/x-ui-english.db`
- 🧩 Reads from 2 tables:
  - `users` → for username/password
  - `inbounds` → for UUIDs, traffic, limits, expiry
- 🧮 Traffic usage and limits are shown in GB
- 📅 Expiry time (timestamp in milliseconds) is converted to Persian date text
- 💡 Fully RTL and uses Bootstrap + Font Awesome (CDN-loaded)

---

## 🖼️ UI Preview
<p align="center">
  <img src="https://raw.githubusercontent.com/afgshayan/xui-english-client-status/refs/heads/main/Screenshot.png" alt="پیش‌نمایش وضعیت اکانت VPN" width="600">
</p>

```

---

## 💡 Tips

- Make sure x-ui-english is already installed and running on your server.
- Confirm that the SQLite database path is:  
  `/etc/x-ui-english/x-ui-english.db`
- You can modify the file and add your own branding/logo easily at the top.

---

## 🚀 To-do & Suggestions

Pull requests are welcome!

Ideas for improvement:
- Export to PDF / printable format
- Add Persian/English language switch
- Display usage history (if tracked)
- Secure with optional password or token

---

## 📜 License

MIT License — free to use, share, and modify.

---

## 🙌 Credits

- Developed for Persian-speaking VPN providers  
- Built on top of the amazing [x-ui-english](https://github.com/NidukaAkalanka/x-ui-english) panel  
- PHP + Bootstrap + SQLite ❤️

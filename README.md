# Error Manager Lite (v1.0)

**A lightweight, database-free error tracking tool for PHP developers.**

I built this tool for myself because I was tired of fighting with heavy logging systems or digging through raw text files on a server. I wanted something I could **drop into a folder and have it work instantly** – right on the client's domain, without leaving the browser.

## Why use this?
- **Zero Configuration:** No MySQL, no migrations, no complex libraries.
- **Stay in the Flow:** Access your logs and to-do list at `yourdomain.com/error-manager`.
- **Client-Friendly:** A simple form for clients to report issues (e.g., "Wrong translation", "Missing image").
- **Flat-File Storage:** All data is stored in readable files. Easy to backup, easy to move.

## Features (Lite Version)
- Single language interface.
- Basic error reporting form for clients.
- Password-protected Admin Dashboard.
- Internal To-Do list for the developer.
- Secure Bcrypt authentication.

## Installation
1. Upload the files to your server.
2. Set write permissions (755) for `/data`, `/logs`, and `/uploads`.
3. Open `password-generator.php` to create your admin hash.
4. Update `config.php` and you're live.

---

## Need more? Check out Error Manager PRO (v1.0.1)

If you are working with international clients or need more robust features, I've built a **PRO version**. It’s the same reliable engine, but "client-ready" for professional handovers.

**What's in PRO:**
- ** 7 Regional Languages:** Fully localized in EN, PL, DE, FR, NL, CS, HU.
- ** CSV Export:** Generate reports to show your client exactly what you've fixed.
- ** File Attachments:** Let clients upload screenshots of the bugs.
- ** Hardened Security:** CSRF protection, XSS sanitization, and path traversal blocks.
- **📧Email Notifications:** Get alerted the second a new bug is reported.

**[Get Error Manager PRO for $5 on Gumroad](https://machinestudioeu.gumroad.com/)**

---

## Technical Details
- **Requirements:** PHP 7.4 or higher.
- **License:** MIT (Lite Version).
- **Author:** [krismachine](https://github.com/krismachine) // [machinestudio.eu](https://machinestudio.eu)

*"Built by a developer to stop the email tennis with clients."*
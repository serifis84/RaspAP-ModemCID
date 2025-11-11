# Modem CID Plugin for RaspAP

A custom RaspAP plugin that displays live logs from `modem-cid.service` (a Caller ID modem server).

## Features
- Shows live systemd logs via `journalctl`
- Auto-refreshes every 3 seconds
- No caching issues (buffer-safe)
- Simple RaspAP integration with Font Awesome icon

## Installation
1. Copy this folder to `/var/www/html/plugins/ModemCID/`
2. Restart your webserver:
   ```bash
   sudo systemctl restart lighttpd

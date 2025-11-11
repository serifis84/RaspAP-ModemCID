# Modem CID Plugin for RaspAP

A custom RaspAP plugin that displays live logs from `modem-cid.service` (a Caller ID modem server).

## Features
- Shows live systemd logs via `journalctl`
- Auto-refreshes every 3 seconds
- No caching issues (buffer-safe)
- Simple RaspAP integration with Font Awesome icon

## Installation
cd /var/www/html/plugins
sudo git clone https://github.com/serifis84/RaspAP-ModemCID.git
sudo mv RaspAP-ModemCID ModemCID
sudo systemctl restart lighttpd

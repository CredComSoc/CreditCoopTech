#backend deploy

sudo systemctl stop backend.service
sudo systemctl disable backend.service
sudo systemctl daemon-reload
sudo rm /etc/systemd/system/backend.service

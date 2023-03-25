#backend deploy

sudo cp backend.service /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable backend.service
sudo systemctl start backend.service

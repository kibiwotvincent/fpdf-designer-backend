sudo chown -R $USER:www-data .;
sudo chmod 700 setup.sh;
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
sudo chmod -R 775 storage;
sudo chmod -R 775 bootstrap/cache;
sudo chmod -R 740 .env;

# OLD python 2.7
#sudo apt-get install python-smbus i2c-tools git 
#sudo apt install python-pip
#sudo pip install flask

#enable the i2c-bus
sudo raspi-config
# 5 enable i2c
#sudo vi /etc/modules
# enable
grep -qxF 'i2c-dev' /etc/modules || echo "i2c-dev" >> /etc/modules
grep -qxF 'i2c-bcm2708' /etc/modules || echo "i2c-bcm2708" >> /etc/modules
sudo i2cdetect -y 1

# not needed anymore -but good examples
#git clone https://github.com/CaptainStouf/raspberry_lcd4x20_I2C

sudo apt-get install -y python3-dev python3-rpi.gpio
sudo apt-get install -y python3-rpi.gpio python3-pigpio python3-smbus
sudo apt-get install -y python3-pip 
sudo apt-get install -y i2c-tools
pip3 install --upgrade pip
sudo pip3 install Flask



# OLD python 2.7
#sudo apt-get install python-smbus i2c-tools git 
#sudo apt install python-pip
#sudo pip install flask

#enable the i2c-bus
sudo raspi-config
# 5 enable i2c
#sudo vi /etc/modules
# enable 
echo "i2c-dev" >> /etc/modules
echo "i2c-bcm2708" >> /etc/modules
sudo i2cdetect -y 1

# not needed anymore -but good examples
#git clone https://github.com/CaptainStouf/raspberry_lcd4x20_I2C

sudo apt-get install python-dev python-rpi.gpio
sudo apt-get install python-rpi.gpio python3-rpi.gpio python3-pigpio python3-smbus
sudo apt-get install python3-pip 
sudo apt-get install i2c-tools
sudo apt-get install python-smbus
pip install --upgrade pip
sudo pip3 install Flask
sudo pip3 install request


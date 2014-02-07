#!/bin/sh

# This sctipt can be used to automatically prepare
# environment to run tests for this bundle

#----------------------------------------------------#
#------- Chmod permissions to cacheds and logs ------#
#----------------------------------------------------#
sudo chmod 0777 Tests/app/cache Tests/app/logs Resources/public/js/fp_js_validator.js
sudo chown -R `whoami`:`whoami` Tests/app/cache Tests/app/logs Resources/public/js/fp_js_validator.js

#----------------------------------------------------#
#--------- Add new temporary host to Apache ---------#
#-------- named "fp_js_form_validator.test"  --------#
#----------------------------------------------------#
HOST_NAME="fp_js_form_validator.test"
HOST_PATH="$(pwd)/Tests/app"
CONF_PATH="/etc/apache2/sites-available/$HOST_NAME"

if [ ! -f /etc/apache2/sites-enabled/${HOST_NAME} ]; then
    sudo cp Tests/app/Resources/apache.tpl.conf ${CONF_PATH}
    sudo sed -i "s,%%HOST%%,$HOST_NAME,g" ${CONF_PATH}
    sudo sed -i "s,%%PATH%%,$HOST_PATH,g" ${CONF_PATH}
    sudo ln -sfn ${CONF_PATH} "/etc/apache2/sites-enabled/$HOST_NAME"
    sudo service apache2 restart
fi

#----------------------------------------------------#
#----------- Add a hostname to /etc/hosts -----------#
#----------------------------------------------------#
INHOSTS=$(cat /etc/hosts | grep fp_js_form_validator.test)
if [ -z "$INHOSTS" ]; then
    sudo bash -c "echo '127.0.0.1 $HOST_NAME' >> /etc/hosts"
fi

#----------------------------------------------------#
#-------------- Enable the local config -------------#
#----------------------------------------------------#
if [ ! -f Tests/app/Resources/local_config.php ]; then
    cp Tests/app/Resources/local_config.php.tpl Tests/app/Resources/local_config.php
fi

#----------------------------------------------------#
#------------------- Run Selenium -------------------#
#----------------------------------------------------#
if [ ! -f selenium.jar ]; then
    curl http://selenium.googlecode.com/files/selenium-server-standalone-2.33.0.jar > selenium.jar
fi
java -jar selenium.jar > selenium.log &
sleep 4

#----------------------------------------------------#
#------------------- Run Tests ----------------------#
#----------------------------------------------------#
phpunit -v
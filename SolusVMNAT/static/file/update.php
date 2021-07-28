<?php
require_once __DIR__.'/../../version.php';
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}else{
?>
#!/bin/sh
clear
Font_Black="\033[30m";
Font_Red="\033[31m";
Font_Green="\033[32m";
Font_Yellow="\033[33m";
Font_Blue="\033[34m";
Font_Purple="\033[35m";
Font_SkyBlue="\033[36m";
Font_White="\033[37m";
Font_Suffix="\033[0m";
version="<?php echo $version; ?>"

echo -e "${Font_SkyBlue}NAT Plugin update script${Font_Suffix}"
echo -e "${Font_Yellow} ** Checking system info...${Font_Suffix}"
os=`tr [:upper:] [:lower:] <<< $(uname -s)`
arch=`uname -m`

if [ "${arch}" = "x86" ];then
    arch="386"
else
    if [ "${arch}" = "x86_64" ];then
        arch="amd64"
    fi
fi
url="https://git.zeroteam.top/https://github.com/CoiaPrant/NATPlugin/releases/download/v"${version}"/NATPlugin_"${version}"_"${os}"_"${arch}".tar.gz"

echo -e "${Font_Yellow} ** Checking wget...${Font_Suffix}"
wget -V > /dev/null 2>&1
if [ $? -ne 0 ];then
    echo -e "${Font_Red}    [Error] Please install wget${Font_Suffix}"
    exit 1
fi
echo -e "${Font_Green}    [Success] Wget found${Font_Suffix}"

echo -e "${Font_Yellow} ** Prepare for update...${Font_Suffix}"
service NATPlugin stop > /dev/null 2>&1

echo -e "${Font_Yellow} ** Creating Program Dictionary...${Font_Suffix}"
if [ ! -d "/etc/NATPlugin/" ];then
    mkdir /etc/NATPlugin/ > /dev/null 2>&1
fi

echo -e "${Font_Yellow} ** Removing old files...${Font_Suffix}"
rm -rf /etc/NATPlugin/NATPlugin > /dev/null 2>&1

echo -e "${Font_Yellow} ** Showing the node infomation${Font_Suffix}"
echo -e "    New Version: " ${version}

echo -e "${Font_Yellow} ** Downloading files and configuring...${Font_Suffix}"
wget  -qO /tmp/NATPlugin.tar.gz ${url}
tar -xvzf /tmp/NATPlugin.tar.gz -C /tmp/ > /dev/null 2>&1
mv /tmp/NATPlugin /etc/NATPlugin/NATPlugin > /dev/null 2>&1
rm -rf /tmp/* > /dev/null 2>&1
chmod 777 /etc/NATPlugin/NATPlugin

if [ -a "/usr/bin/systemctl" ];then
    wget -qO /etc/systemd/system/NATPlugin.service https://git.zeroteam.top/https://github.com/CoiaPrant/NATPlugin/blob/master/NATPlugin.service
    ln -sf /etc/systemd/system/NATPlugin.service /etc/systemd/system/multi-user.target.wants/NATPlugin.service
    systemctl daemon-reload > /dev/null 2>&1
    systemctl enable NATPlugin > /dev/null 2>&1
else
    wget -qO /etc/init.d/NATPlugin https://git.zeroteam.top/https://github.com/CoiaPrant/NATPlugin/blob/master/init.d
    chmod 777 /etc/init.d/NATPlugin
    chkconfig --add /etc/init.d/NATPlugin > /dev/null 2>&1
    chkconfig NATPlugin on > /dev/null 2>&1
fi

echo -e "${Font_Yellow} ** Starting Program...${Font_Suffix}"
service NATPlugin start > /dev/null 2>&1

echo -e "${Font_Green} [Success] Completed update${Font_Suffix}"
<?php } ?>
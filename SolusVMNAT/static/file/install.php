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

echo -e "${Font_SkyBlue}NAT Plugin installation script${Font_Suffix}"
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

echo -e "${Font_Yellow} ** Prepare for installation...${Font_Suffix}"
service NATPlugin stop > /dev/null 2>&1
if [ ! -a "/usr/bin/systemctl" ];then
    sleep 10
fi

echo -e "${Font_Yellow} ** Creating Program Dictionary...${Font_Suffix}"
if [ ! -d "/etc/NATPlugin/" ];then
    mkdir /etc/NATPlugin/ > /dev/null 2>&1
fi

echo -e "${Font_Yellow} ** Showing the node infomation${Font_Suffix}"
echo -e "    Version: " ${version}

echo -e "${Font_Yellow} ** Please enter the node infomation${Font_Suffix}"
read -ep "    NodeID: " nodeid

echo -e "${Font_Yellow} ** Downloading files and configuring...${Font_Suffix}"
if [ -a "/usr/bin/systemctl" ];then
    wget -qO /etc/systemd/system/NATPlugin.service https://git.zeroteam.top/https://github.com/CoiaPrant/NATPlugin/blob/master/NATPlugin.service
    ln -sf /etc/systemd/system/NATPlugin.service /etc/systemd/system/multi-user.target.wants/NATPlugin.service
    systemctl daemon-reload
    systemctl enable NATPlugin > /dev/null 2>&1
else
    wget -qO /etc/init.d/NATPlugin https://git.zeroteam.top/https://github.com/CoiaPrant/NATPlugin/blob/master/init.d
    chmod 777 /etc/init.d/NATPlugin
    chkconfig --add /etc/init.d/NATPlugin
fi
wget -qO /tmp/NATPlugin.tar.gz ${url}
tar -xvzf /tmp/NATPlugin.tar.gz -C /tmp/ > /dev/null 2>&1
rm -rf /etc/NATPlugin/NATPlugin > /dev/null 2>&1
mv /tmp/NATPlugin /etc/NATPlugin/NATPlugin > /dev/null 2>&1
rm -rf /tmp/* > /dev/null 2>&1
chmod 777 /etc/NATPlugin/NATPlugin
wget -qO /etc/NATPlugin/config.json "<?php echo SolusVMNAT_GetSystemURL(); ?>modules/addons/SolusVMNAT/install.php?do=json&key=<?php echo md5(SolusVMNAT_GetConfigModule()['key']); ?>&id=${nodeid}"

echo -e "${Font_Yellow} ** Configuring system...${Font_Suffix}"
sed -i '/fs.file-max/d' /etc/sysctl.conf
sed -i '/fs.inotify.max_user_instances/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_syncookies/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_fin_timeout/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_tw_reuse/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_max_syn_backlog/d' /etc/sysctl.conf
sed -i '/net.ipv4.ip_local_port_range/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_max_tw_buckets/d' /etc/sysctl.conf
sed -i '/net.ipv4.route.gc_timeout/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_synack_retries/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_syn_retries/d' /etc/sysctl.conf
sed -i '/net.core.somaxconn/d' /etc/sysctl.conf
sed -i '/net.core.netdev_max_backlog/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_timestamps/d' /etc/sysctl.conf
sed -i '/net.ipv4.tcp_max_orphans/d' /etc/sysctl.conf
sed -i '/net.ipv4.ip_forward/d' /etc/sysctl.conf
echo "fs.file-max = 1048576
fs.inotify.max_user_instances = 8192
net.ipv4.tcp_syncookies = 1
net.ipv4.tcp_fin_timeout = 30
net.ipv4.tcp_tw_reuse = 1
net.ipv4.ip_local_port_range = 1024 65000
net.ipv4.tcp_max_syn_backlog = 16384
net.ipv4.tcp_max_tw_buckets = 6000
net.ipv4.route.gc_timeout = 100
net.ipv4.tcp_syn_retries = 1
net.ipv4.tcp_synack_retries = 1
net.core.somaxconn = 32768
net.core.netdev_max_backlog = 32768
net.ipv4.tcp_timestamps = 0
net.ipv4.tcp_max_orphans = 32768
# forward ipv4
net.ipv4.ip_forward = 1">>/etc/sysctl.conf
echo "*               soft    nofile           1048576
*               hard    nofile          1048576">/etc/security/limits.conf
echo "ulimit -SHn 1048576" >> /etc/profile
sysctl -p > /dev/null 2>&1
if [ -e "/usr/local/svmstack/nginx/conf/services/legacy-master.conf" ];then
    wget -qO /usr/local/svmstack/nginx/conf/services/legacy-master.conf https://git.zeroteam.top/https://github.com/CoiaPrant/NATPlugin/blob/master/solusvm-legacy-master.conf
    service svmstack-nginx restart
fi

echo -e "${Font_Yellow} ** Starting Program...${Font_Suffix}"
service NATPlugin start > /dev/null 2>&1

echo -e "${Font_Green} [Success] Completed installation${Font_Suffix}"
echo -e "${Font_SkyBlue} [Tip] Please Reboot${Font_Suffix}"
<?php } ?>
<?php
register_menu("System Info", true, "system_info", 'SETTINGS', '');

function system_info()
{
    global $ui;
    _admin();
    $ui->assign('_title', 'System Information');
    $ui->assign('_system_menu', 'settings');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    function checkRadiusServer($host, $port, $timeout = 1)
    {
        $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        if ($fp) {
            fclose($fp);
            return true;
        } else {
            return false;
        }
    }

    function get_server_memory_usage()
    {
        $free = shell_exec('free -m');
        $free = (string) trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);

        $total_memory = $mem[1];
        $used_memory = $mem[2];
        $free_memory = $total_memory - $used_memory;
        $memory_usage_percentage = round($used_memory / $total_memory * 100);

        $memory_usage = [
            'total' => $total_memory,
            'free' => $free_memory,
            'used' => $used_memory,
            'used_percentage' => round($memory_usage_percentage),
        ];

        return $memory_usage;
    }

    function getSystemInfo()
{
    $memory_usage = get_server_memory_usage();

    $db = ORM::getDb();
    $serverInfo = $db->getAttribute(PDO::ATTR_SERVER_VERSION);
    $databaseName = $db->query('SELECT DATABASE()')->fetchColumn();
    $serverName = gethostname();

    // Fallback: Let's use $_SERVER['SERVER_NAME'] if gethostname() is not available
    if (!$serverName) {
        $serverName = $_SERVER['SERVER_NAME'];
    }

    $systemInfo = [
        'Server Name' => $serverName,
        'Operating System' => php_uname('s'),
        'PHP Version' => phpversion(),
        'Server Software' => $_SERVER['SERVER_SOFTWARE'],
        'Server IP Address' => $_SERVER['SERVER_ADDR'],
        'Server Port' => $_SERVER['SERVER_PORT'],
        'Remote IP Address' => $_SERVER['REMOTE_ADDR'],
        'Remote Port' => $_SERVER['REMOTE_PORT'],
        'Database Server' => $serverInfo,
        'Database Name' => $databaseName,
        // Add more system information here
    ];

    return $systemInfo;
}

function generateServiceTable() {
    function check_service($service_name) {
        if (empty($service_name)) {
            return false;
        }

        $command = sprintf("pgrep %s", escapeshellarg($service_name));
        exec($command, $output, $result_code);
        return $result_code === 0;
    }

    $services_to_check = array("FreeRADIUS", "MySQL", "Cron", "SSHd");

    $table = array('title' => 'Service Status', 'rows' => array());

    foreach ($services_to_check as $service_name) {
        $running = check_service(strtolower($service_name));
        $class = ($running) ? "label pull-right bg-green" : "label pull-right bg-red";
        $label = ($running) ? "running" : "not running";

        $value = sprintf('<small class="%s">%s</small>', $class, $label);
		

        $table['rows'][] = array($service_name, $value);
    }

    return $table;
}

    $systemInfo = getSystemInfo();

    $ui->assign('systemInfo', $systemInfo);
    $ui->assign('memory_usage', get_server_memory_usage());
	$ui->assign('serviceTable', generateServiceTable());

    // Display the template
    $ui->display('system_info.tpl');
}
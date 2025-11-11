<?php

/**
 * ModemCID plugin
 *
 * Displays live logs from modem-cid.service in RaspAP
 */

namespace RaspAP\Plugins\ModemCID;

use RaspAP\Plugins\PluginInterface;
use RaspAP\UI\Sidebar;

class ModemCID implements PluginInterface
{
    private string $pluginPath;
    private string $pluginName;
    private string $templateMain;
    private string $apiKey;
    private string $serviceStatus;

    public function __construct(string $pluginPath, string $pluginName)
    {
        $this->pluginPath    = $pluginPath;
        $this->pluginName    = $pluginName;
        $this->templateMain  = 'main';
        $this->serviceStatus = 'up';
        $this->apiKey        = '';

        if ($loaded = self::loadData()) {
            $this->apiKey        = $loaded->getApiKey();
            $this->serviceStatus = $loaded->getServiceStatus();
        }
    }

    /**
     * Initialize the plugin and add sidebar entry
     */
    public function initialize(Sidebar $sidebar): void
    {
        $label   = _('Modem CID');
        $icon    = 'fas fa-phone';
        $action  = 'plugin__' . $this->getName();
        $priority = 65;

        $sidebar->addItem($label, $icon, $action, $priority);
    }

    /**
     * Handle plugin page + AJAX log refresh
     */
    public function handlePageAction(string $page): bool
    {
        // AJAX endpoint: /?ajax=modemcid
        if (isset($_GET['ajax']) && $_GET['ajax'] === strtolower($this->getName())) {
	    // prevent any buffered RaspAP output from leaking into the response
	    while (ob_get_level()) {
	        ob_end_clean();
	    }

	    header('Content-Type: text/plain; charset=utf-8');
	    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	    header('Pragma: no-cache');
	    echo $this->getModemCidLog();
	    flush();
	    exit();
	}

        // Main plugin route: /?page=plugin__ModemCID
        if (strpos($page, "/plugin__" . $this->getName()) === 0) {

            $status = new \RaspAP\Messages\StatusMessage;

            if (!RASPI_MONITOR_ENABLED) {
                if (isset($_POST['saveSettings'])) {
                    if (isset($_POST['txtapikey'])) {
                        $apiKey = trim($_POST['txtapikey']);
                        if (strlen($apiKey) === 0) {
                            $status->addMessage('Please enter a valid API key', 'warning');
                        } else {
                            $this->saveSampleSettings($status, $apiKey);
                            $status->addMessage('Restarting modem-cid.service', 'info');
                        }
                    }
                } elseif (isset($_POST['startSampleService'])) {
                    $status->addMessage('Attempting to start modem-cid.service', 'info');
                    $this->setServiceStatus('up');
                } elseif (isset($_POST['stopSampleService'])) {
                    $status->addMessage('Attempting to stop modem-cid.service', 'info');
                    $this->setServiceStatus('down');
                }
            }

            $__template_data = [
                'title'         => _('Modem CID'),
                'description'   => _('Displays live logs from modem-cid.service'),
                'author'        => _('Grigorios Vassilopoulos'),
                'uri'           => 'https://github.com/serifis84/RaspAP-ModemCID',
                'icon'          => 'fas fa-plug',
                'serviceStatus' => $this->getServiceStatus(),
                'serviceName'   => 'modem-cid.service',
                'action'        => 'plugin__' . $this->getName(),
                'pluginName'    => $this->getName(),
                'serviceLog'    => $this->getModemCidLog(),
                'apiKey'        => $this->getApiKey()
            ];

            echo $this->renderTemplate($this->templateMain, compact(
                'status',
                '__template_data'
            ));
            return true;
        }

        return false;
    }

    /**
     * Render a template from this plugin's directory
     */
    public function renderTemplate(string $templateName, array $__data = []): string
    {
        $templateFile = "{$this->pluginPath}/{$this->getName()}/templates/{$templateName}.php";

        if (!file_exists($templateFile)) {
            return "Template file {$templateFile} not found.";
        }
        if (!empty($__data)) {
            extract($__data);
        }

        ob_start();
        include $templateFile;
        return ob_get_clean();
    }

    /**
     * Save plugin settings
     */
    public function saveSampleSettings($status, $apiKey)
    {
        $status->addMessage('Saving Sample API key', 'info');
        $this->setApiKey($apiKey);
        return $status;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
        $this->persistData();
    }

    public function getServiceStatus()
    {
        return $this->serviceStatus;
    }

    public function setServiceStatus($status): void
    {
        $this->serviceStatus = $status;
        $this->persistData();
    }

    /**
     * Read last 200 lines from modem-cid.service via journalctl
     */
    private function getModemCidLog(): string
    {
        $cmd = 'sudo /usr/bin/journalctl -u modem-cid.service -n 200 --no-pager 2>&1';
        $output = [];
        $ret    = 0;

        exec($cmd, $output, $ret);

        if ($ret !== 0) {
            $msg = "Failed to read modem-cid.service logs (exit code $ret).";
            if (!empty($output)) {
                $msg .= "\n" . implode("\n", $output);
            }
            return htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $log = implode("\n", $output);
        if ($log === '') {
            $log = 'No log output received from modem-cid.service.';
        }

        return htmlspecialchars($log, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Persist plugin state (ephemeral)
     */
    public function persistData(): void
    {
        $serialized = serialize($this);
        file_put_contents("/tmp/plugin__{$this->getName()}.data", $serialized);
    }

    public static function loadData(): ?self
    {
        $filePath = "/tmp/plugin__" . self::getName() . ".data";
        if (file_exists($filePath)) {
            $data = file_get_contents($filePath);
            return unserialize($data);
        }
        return null;
    }

    public static function getName(): string
    {
        return basename(str_replace('\\', '/', static::class));
    }
}

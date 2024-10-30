<?php

class htprH2p
{
    const PLUGIN_NOT_CONFIGURED = 'You need to configure HTML2PDF plugin first';

    private $api;
    private $buttontext;
    private $buttonclass;
    private $outputformat;
    private $filename;
    private $pagesize;
    private $javascriptdelay;
    private $uselandscape;

    private $atts;

    private $default = array(
        'buttontext' => 'Download page as pdf',
        'buttonclass' => 'btn',
        'outputformat' => 'pdf',
        'filename' => 'page',
        'pagesize' => 'A4',
        'javascriptdelay' => '0',
        'uselandscape' => 'false',
    );

    protected static $instance = NULL;
    public $plugin_url;

    public function __construct()
    {
        $this->api = isset(get_option('h2p')['api']) ? get_option('h2p')['api'] : '';
        $this->plugin_url = trailingslashit(plugins_url('h2p'));
    }

    public static function get_instance()
    {
        NULL === self::$instance and self::$instance = new self;
        return self::$instance;
    }

    public function init($atts = array())
    {
        $this->registerAtts($atts);
        $this->enqueue();

        ob_start();
        $this->h2p();
        $output = ob_get_clean();
        return $output;
    }

    public function registerAtts($atts)
    {
        $this->atts = array_change_key_case((array)$atts, CASE_LOWER);
        foreach ($this->default as $key => $value) {
            $this->setAttr($key);
        }
    }

    public function setAttr($attr)
    {
        $this->setAdminAttr($attr);
        $this->setShortCodeAttr($attr);
        $this->setDefaultAttr($attr);
    }

    public function setAdminAttr($attr)
    {
        $this->{$attr} = (get_option('h2p')[$attr]) ? get_option('h2p')[$attr] : null;
    }

    public function setShortCodeAttr($attr)
    {
        if (!empty($this->atts[$attr])) {
            $this->{$attr} = $this->atts[$attr];
        }
    }

    public function setDefaultAttr($attr)
    {
        if (empty($this->{$attr})) {
            $this->{$attr} = $this->default[$attr];
        }
    }

    public function h2p()
    {
        if ($this->api) {
            ?>
            <div id="h2p">
                <button class="<?= $this->buttonclass ?>"><?= $this->buttontext ?></button>
            </div>
            <?php
        } else {
            ?>
            <p style="color: red;"><?= self::PLUGIN_NOT_CONFIGURED ?></p>
            <?php
        }
    }

    public function enqueue()
    {
        wp_enqueue_script(
            'ajax-script',
            "{$this->plugin_url}js/app.js",
            array('jquery')
        );
        wp_localize_script(
            'ajax-script',
            'wp_ajax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'ajaxnonce' => wp_create_nonce('sec_get_pdf'),
                'filename' => $this->filename . '.' . $this->outputformat,
                'params' => $this->setUrlParams(),
                'load' => "{$this->plugin_url}img/ajax-loader.gif"
            )
        );
    }

    public function setUrlParams()
    {
        $params = array(
            'outputformat'=>urlencode($this->outputformat),
            'filename'=>urlencode($this->filename),
            'pagesize'=>urlencode($this->pagesize),
            'javascriptdelay'=>urlencode($this->javascriptdelay),
            'uselandscape'=>urlencode($this->uselandscape),
            );
        return json_encode($params);
    }

    public function getUrlParams($json_params)
    {
        try {
            $params = json_decode(stripcslashes($json_params));
            $query = '';
            foreach ($params as $key=>$param){
                $query = $query.'&'.htmlspecialchars($key).'='.htmlspecialchars($param);
            }
            return $query;
        }
        catch (Exception $e){
            return '';
        }
    }

    public function get_pdf()
    {
        check_ajax_referer('sec_get_pdf', 'security');
        global $wp;
        $site = home_url(add_query_arg(array(), $wp->request));
        $file = htmlspecialchars(stripcslashes($_POST['filename']));
        $result = file_get_contents("http://api.html2pdfrocket.com/pdf?apikey=" . urlencode($this->api) . "&value=" . urlencode($site) . $this->getUrlParams($_POST['params']));
        $url_save = dirname(dirname(__FILE__)) . "/files/" . $file;
        $url_file = "{$this->plugin_url}files/" . $file;
        file_put_contents($url_save, $result);
        echo $url_file;
        wp_die();
    }
}
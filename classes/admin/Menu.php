<?php

class htprMenu
{
    protected static $instance = NULL;
    private $page;

    public function __construct(htprPage $page)
    {
        $this->page = $page;
    }

    public function init()
    {
        add_menu_page('HTML 2 PDF Rocket Settings', 'HTML 2 PDF Rocket', 'manage_options', 'h2p-plugin', array($this->page, 'init'));
    }

}
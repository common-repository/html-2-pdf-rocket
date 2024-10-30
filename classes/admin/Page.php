<?php

class htprPage
{
    const BUTTON_CLASS = 'The class will be added to the button tag';
    const BUTTON_TEXT = 'The text will be displayed on the button';
    const API_DESC = 'Get free API key from <a href="http://www.html2pdfrocket.com" target="_blank">www.html2pdfrocket.com</a>';
    const SECTION_INFO = 'Enter your settings below:';
    const DESCRIPTION = 'First of all you need to get free API key from <a href="http://www.html2pdfrocket.com" target="_blank">www.html2pdfrocket.com</a><br>Use a shortcode on your page to allow the page to be downloaded as a PDF. For example add <code>[h2p]</code> to your page. The settings will be taken by default from this admin panel but you can override them for individual pages like so: <code>[h2p buttonclass="btnclass" buttontext="Download PDF" outputformat="pdf" filename="page" pagesize="A4" javascriptdelay="0" uselandscape="true"]</code><hr>';
    const FILENAME_DESC = 'Optionally the name you want the file to be called when downloading';
    const OUTPUTFORMAT_DESC = 'Must be either "pdf", "jpg", "png", "bmp" or "svg" if not supplied the default is PDF';
    const PAGESIZE_DESC = 'Default size is A4 but you can use Letter, A0, A2, A3, A5, Legal, etc.';
    const JAVASCRIPTDELAY_DESC = 'Milliseconds to wait for JS to finish executing before converting the page.  Useful for ajax calls';
    const USELANDSCAPE_DESC = 'True to rotate page to landscape, false or leave blank for portrait';
    const GET_API = 'Get a free API key';

    private $options;

    public function __construct()
    {
        add_action('admin_init', array($this, 'page_init'));
    }


    public function init()
    {
        $this->options = get_option('h2p');
        ?>
        <div class="wrap">
            <h1>HTML 2 PDF Rocket</h1>
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('h2p_group');
                do_settings_sections('setting-admin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init()
    {
        register_setting(
            'h2p_group', // Option group
            'h2p', // Option name
            array($this, 'sanitize') // Sanitize
        );

        add_settings_section(
            'desc_section', // ID
            'How to use', // Title
            array($this, 'print_description'), // Callback
            'setting-admin' // Page
        );

        add_settings_section(
            'main_setting_section', // ID
            'Main Settings', // Title
            array($this, 'print_section_info'), // Callback
            'setting-admin' // Page
        );

        add_settings_section(
            'optional_setting_section', // ID
            'Optional Settings', // Title
            array($this, 'print_section_info'), // Callback
            'setting-admin' // Page
        );

        add_settings_field(
            'api', // ID
            'API key ' . (!(get_option('h2p')['api']) ? '(<a href="https://www.html2pdfrocket.com/Account/Register" target="_blank">' . self::GET_API . '</a>)' : ''), // Title
            array($this, 'api_callback'), // Callback
            'setting-admin', // Page
            'main_setting_section' // Section
        );

        add_settings_field(
            'buttontext',
            'Button text',
            array($this, 'button_callback'),
            'setting-admin',
            'main_setting_section'
        );

        add_settings_field(
            'buttonclass',
            'Button class',
            array($this, 'buttonclass_callback'),
            'setting-admin',
            'main_setting_section'
        );

        add_settings_field(
            'filename',
            'Default file name',
            array($this, 'filename_callback'),
            'setting-admin',
            'optional_setting_section'
        );

        add_settings_field(
            'outputformat',
            'Default output format',
            array($this, 'outputformat_callback'),
            'setting-admin',
            'optional_setting_section'
        );

        add_settings_field(
            'pagesize',
            'Default pagesize',
            array($this, 'pagesize_callback'),
            'setting-admin',
            'optional_setting_section'
        );

        add_settings_field(
            'javascriptdelay',
            'Default javascript delay',
            array($this, 'javascriptdelay_callback'),
            'setting-admin',
            'optional_setting_section'
        );

        add_settings_field(
            'uselandscape',
            'Use landscape',
            array($this, 'uselandscape_callback'),
            'setting-admin',
            'optional_setting_section'
        );
    }

    public function sanitize($input)
    {
        $new_input = array();
        if (isset($input['api']))
            $new_input['api'] = trim(sanitize_text_field($input['api']));

        if (isset($input['buttontext']))
            $new_input['buttontext'] = trim(sanitize_text_field($input['buttontext']));

        if (isset($input['buttonclass']))
            $new_input['buttonclass'] = trim(sanitize_text_field($input['buttonclass']));

        if (isset($input['filename']))
            $new_input['filename'] = trim(sanitize_text_field($input['filename']));

        if (isset($input['outputformat']))
            $new_input['outputformat'] = trim(sanitize_text_field($input['outputformat']));

        if (isset($input['pagesize']))
            $new_input['pagesize'] = trim(sanitize_text_field($input['pagesize']));

        if (isset($input['javascriptdelay']))
            $new_input['javascriptdelay'] = trim(sanitize_text_field($input['javascriptdelay']));

        if (isset($input['uselandscape']))
            $new_input['uselandscape'] = trim(sanitize_text_field($input['uselandscape']));

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        print self::SECTION_INFO;
    }

    /**
     * Print the Description
     */
    public function print_description()
    {
        print self::DESCRIPTION;
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function api_callback()
    {
        printf(
            '<input type="text" id="api" name="h2p[api]" value="%s" style="width: 280px;"/><p class="description">' . self::API_DESC . '</p>',
            isset($this->options['api']) ? esc_attr($this->options['api']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function button_callback()
    {
        printf(
            '<input type="text" id="buttontext" name="h2p[buttontext]" value="%s" /><p class="description">' . self::BUTTON_TEXT . '</p>',
            isset($this->options['buttontext']) ? esc_attr($this->options['buttontext']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function buttonclass_callback()
    {
        printf(
            '<input type="text" id="buttonclass" name="h2p[buttonclass]" value="%s" /><p class="description">' . self::BUTTON_CLASS . '</p>',
            isset($this->options['buttonclass']) ? esc_attr($this->options['buttonclass']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function filename_callback()
    {
        printf(
            '<input type="text" id="filename" name="h2p[filename]" value="%s" /><p class="description">' . self::FILENAME_DESC . '</p>',
            isset($this->options['filename']) ? esc_attr($this->options['filename']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function outputformat_callback()
    {
        printf(
            '<input type="text" id="outputformat" name="h2p[outputformat]" value="%s" /><p class="description">' . self::OUTPUTFORMAT_DESC . '</p>',
            isset($this->options['outputformat']) ? esc_attr($this->options['outputformat']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function pagesize_callback()
    {
        printf(
            '<input type="text" id="pagesize" name="h2p[pagesize]" value="%s" /><p class="description">' . self::PAGESIZE_DESC . '</p>',
            isset($this->options['pagesize']) ? esc_attr($this->options['pagesize']) : ''
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function javascriptdelay_callback()
    {
        printf(
            '<input type="text" id="javascriptdelay" name="h2p[javascriptdelay]" value="%s" /><p class="description">' . self::JAVASCRIPTDELAY_DESC . '</p>',
            isset($this->options['javascriptdelay']) ? esc_attr($this->options['javascriptdelay']) : ''
        );
    }


    /**
     * Get the settings option array and print one of its values
     */
    public function uselandscape_callback()
    {
        printf(
            '<input type="text" id="uselandscape" name="h2p[uselandscape]" value="%s" /><p class="description">' . self::USELANDSCAPE_DESC . '</p>',
            isset($this->options['uselandscape']) ? esc_attr($this->options['uselandscape']) : ''
        );
    }
}
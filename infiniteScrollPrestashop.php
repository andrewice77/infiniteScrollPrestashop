<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class infiniteScrollPrestashop extends Module
{

    protected $controllersScrollable;

    public function __construct()
    {

        $this->name = 'infiniteScrollPrestashop';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'AndrewIce77';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Infinite Scroll for Prestashop', [], 'Modules.infiniteScrollPs.Admin');
        $this->description = $this->trans('Add automatically infinite scroll to your Prestashop', [], 'Modules.infiniteScrollPs.Admin');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        $this->ps_versions_compliancy = array('min' => '1.7.1.0', 'max' => _PS_VERSION_);

        $this->controllersScrollable = [
            'index',
            'category',
            'supplier',
            'manufacturer',
            'pricesdrop',
        ];
    }


    public function install()
    {
        return  parent::install() &&
                $this->registerHook('displayHeader') &&
                $this->registerHook('displayFooter') &&
                $this->initParams();

    }

    public function uninstall()
    {
        Configuration::deleteByName('INFINITE_SCROLL_PS');
        return parent::uninstall();
    }


    public function postProcess()
    {
        if (Tools::isSubmit('submitInfiniteScrollPs')) {

            $controllers = Tools::getValue('controllers_enabled') ?: [];
            if(is_string($controllers))
                $controllers = [$controllers];

            $array = array_intersect($controllers, $this->controllersScrollable);

            $data = [
                'btn_text' => Tools::getValue('btn_text'),
                'btn_color' => Tools::getValue('btn_color'),
                'scroll_type' => Tools::getValue('scroll_type'),
                'controllers_enabled' => $array
            ];

            Configuration::updateValue('INFINITE_SCROLL_PS', json_encode($data));


            return $this->displayConfirmation($this->trans('The settings have been updated.', [], 'Admin.Notifications.Success'));
        }
        return '';
    }


    public function getContent()
    {
        return $this->postProcess() . $this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = [
            'form' =>  [
                'legend' => [
                    'title'         => $this->trans('Settings', [], 'Modules.infiniteScrollPs.Admin'),
                    'icon'          => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type'      => 'text',
                        'label'     => $this->trans('Text Button', [], 'Modules.infiniteScrollPs.Admin'),
                        'name'      => 'btn_text',
                        'class'     => 'fixed-width-xl',
                    ],
                    [
                        'type'      => 'color',
                        'label'     => $this->trans('Color button', [], 'Modules.infiniteScrollPs.Admin'),
                        'name'      => 'btn_color',
                        'class'     => 'fixed-width-md',
                    ],
                    [
                        'type'      => 'select',
                        'label'     => $this->trans('Type of scroll', [], 'Modules.infiniteScrollPs.Admin'),
                        'name'      => 'scroll_type',
                        'class'     => 'fixed-width-sm',
                        'required'  => true,
                        'options'   => [
                            'query' => [
                                [
                                    'id_option' => '1',
                                    'name' => $this->trans('Automatically', [], 'Modules.infiniteScrollPs.Admin'),
                                ],
                                [
                                    'id_option' => '2',
                                    'name' => $this->trans('Manually', [], 'Modules.infiniteScrollPs.Admin'),
                                ]
                            ],
                            'id'    => 'id_option',
                            'name'  => 'name'
                        ]
                    ],
                    [
                        'type'      => 'controllers_select',
                        'label'     => $this->trans('Controllers Enabled', [], 'Modules.infiniteScrollPs.Admin'),
                        'name'      => 'controllers_enabled',
                        'descr'      => $this->trans('Select the controllers where you want to enable the infinite scroll', [], 'Modules.infiniteScrollPs.Admin'),
                    ],
                ],
                'submit' => [
                    'title'     => $this->trans('Save', [], 'Admin.Actions'),
                ],
            ]
        ];

        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitInfiniteScrollPs';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
                                .'&configure=' . $this->name
                                .'&tab_module=' . $this->tab
                                .'&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = [
            'controllers'   => $this->getAvailableControllers(),
            'fields_value'  => $this->getParams(),
        ];

        return $helper->generateForm([$fields_form]);
    }


    public function hookDisplayHeader(  )
    {
        return $this->displayInfiniteScroll();
    }

    public function hookDisplayFooter(  )
    {
        return $this->displayInfiniteScroll();
    }


    public function displayInfiniteScroll()
    {
        // Check controllers enabled

        // Include infinite scroll js and functionality
        return '';

    }


    /**================ PRIVATE METHODS  ================*/
    private function initParams()
    {
        $data = [
            'btn_text' => 'Load More...',
            'btn_color' => '#000000',
            'scroll_type' => '1',
            'controllers_enabled' => $this->controllersScrollable
        ];

        return Configuration::updateValue('INFINITE_SCROLL_PS', json_encode($data));
    }

    private function getParams()
    {
        $params = json_decode(Configuration::get('INFINITE_SCROLL_PS'), true);
        return $params ?: [];
    }

    private function getAvailableControllers()
    {
        $controllers = [];
        // Default controllers available
        $array = [ 'available' => array_flip($this->controllersScrollable) ];

        // Get all controllers
        $available = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);

        // Get controllers enabled on db
        $selected = json_decode(Configuration::get('INFINITE_SCROLL_PS'), true);

        // Merge and intersect the arrays
        $controllers['available'] = array_intersect_key($available, $array['available']);

        if( !empty($selected['controllers_enabled']) ){

            foreach( $controllers['available'] as $key => $value ){

                if( in_array($key, $selected['controllers_enabled']) ){
                    unset($controllers['available'][$key]);
                    $controllers['selected'][$key] = $value;
                }

            }

        }


        return $controllers;
    }



}
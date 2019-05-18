<?php
/**
* DISCLAIMER
*
* THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
* INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
* IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
* DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
* ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
* OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*
*  @author    LogsHub.com <sales@logshub.com>
*  @copyright 2019 LogsHub.com
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Logshubsearch extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'logshubsearch';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'LogsHub.com';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('LogsHub.com Search');
        $this->description = $this->l('Turn your search box into next level.');

        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('LOGSHUBSEARCH_LIVE_MODE', false);

        return parent::install() &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName('LOGSHUBSEARCH_LIVE_MODE');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLogshubsearchModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLogshubsearchModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'LOGSHUBSEARCH_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => [
                            ['id' => 'active_on', 'value' => true, 'label' => $this->l('Enabled')],
                            ['id' => 'active_off', 'value' => false, 'label' => $this->l('Disabled')]
                        ],
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_API_DOMAIN',
                        'label' => $this->l('API domain'),
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_API_PUBLIC_KEY',
                        'label' => $this->l('API public key'),
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_LIMIT',
                        'label' => $this->l('API products limit'),
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_CAT_LIMIT',
                        'label' => $this->l('API categories limit'),
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_FEATURES',
                        'label' => $this->l('API features'),
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_CONTAINER',
                        'label' => $this->l('Container selector'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Fullscreen'),
                        'name' => 'LOGSHUBSEARCH_FULLSCREEN',
                        'is_bool' => true,
                        'values' => [
                            ['id' => 'active_on', 'value' => true, 'label' => $this->l('Enabled')],
                            ['id' => 'active_off', 'value' => false, 'label' => $this->l('Disabled')]
                        ],
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_STARTUP_QUERY',
                        'label' => $this->l('Startup query'),
                    ],
                    [
                        'type' => 'text',
                        'name' => 'LOGSHUBSEARCH_DEFAULT_CURRENCY',
                        'label' => $this->l('Default currency'),
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        $default = [
            'LOGSHUBSEARCH_LIVE_MODE' => Configuration::get('LOGSHUBSEARCH_LIVE_MODE'),
            'LOGSHUBSEARCH_API_DOMAIN' => Configuration::get('LOGSHUBSEARCH_API_DOMAIN'),
            'LOGSHUBSEARCH_API_PUBLIC_KEY' => Configuration::get('LOGSHUBSEARCH_API_PUBLIC_KEY'),
            'LOGSHUBSEARCH_LIMIT' => Configuration::get('LOGSHUBSEARCH_LIMIT'),
            'LOGSHUBSEARCH_CAT_LIMIT' => Configuration::get('LOGSHUBSEARCH_CAT_LIMIT'),
            'LOGSHUBSEARCH_FEATURES' => Configuration::get('LOGSHUBSEARCH_FEATURES'),
            'LOGSHUBSEARCH_CONTAINER' => Configuration::get('LOGSHUBSEARCH_CONTAINER'),
            'LOGSHUBSEARCH_FULLSCREEN' => Configuration::get('LOGSHUBSEARCH_FULLSCREEN'),
            'LOGSHUBSEARCH_STARTUP_QUERY' => Configuration::get('LOGSHUBSEARCH_STARTUP_QUERY'),
            'LOGSHUBSEARCH_DEFAULT_CURRENCY' => Configuration::get('LOGSHUBSEARCH_DEFAULT_CURRENCY'),
        ];

        if (empty($default['LOGSHUBSEARCH_API_DOMAIN'])){
            $default['LOGSHUBSEARCH_API_DOMAIN'] = 'uk01.apisearch.logshub.com';
        }
        if (empty($default['LOGSHUBSEARCH_LIMIT'])){
            $default['LOGSHUBSEARCH_LIMIT'] = 18;
        }
        if (empty($default['LOGSHUBSEARCH_CAT_LIMIT'])){
            $default['LOGSHUBSEARCH_CAT_LIMIT'] = 10;
        }
        if (empty($default['LOGSHUBSEARCH_FEATURES'])){
            $default['LOGSHUBSEARCH_FEATURES'] = 'products,categories,fullresponse';
        }
        if (empty($default['LOGSHUBSEARCH_CONTAINER'])){
            $default['LOGSHUBSEARCH_CONTAINER'] = '#search_widget';
        }
        if (empty($default['LOGSHUBSEARCH_FULLSCREEN'])){
            $default['LOGSHUBSEARCH_FULLSCREEN'] = true;
        }

        return $default;
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookDisplayHeader()
    {
        $variables = [
            'domain' => Configuration::get('LOGSHUBSEARCH_API_DOMAIN'),
            'pubKey' => Configuration::get('LOGSHUBSEARCH_API_PUBLIC_KEY'),
            'container' => Configuration::get('LOGSHUBSEARCH_CONTAINER'),
            'fullscreen' => Configuration::get('LOGSHUBSEARCH_FULLSCREEN'),
            'limit' => Configuration::get('LOGSHUBSEARCH_LIMIT', 18),
            'categoryLimit' => Configuration::get('LOGSHUBSEARCH_CAT_LIMIT', 10),
            'features' => Configuration::get('LOGSHUBSEARCH_FEATURES'),
            'startupQuery' => Configuration::get('LOGSHUBSEARCH_STARTUP_QUERY'),
            'currency' => Configuration::get('LOGSHUBSEARCH_DEFAULT_CURRENCY'),
        ];

        if (!Configuration::get('LOGSHUBSEARCH_LIVE_MODE') || !$variables['domain'] || !$variables['pubKey']){
            return;
        }

        $this->context->controller->addJquery();
        $this->context->controller->addJS($this->_path.'/views/js/handlebars.min.js');
        $this->context->controller->addJS($this->_path.'/views/js/typeahead.jquery.min.js');
        $this->context->controller->addJS($this->_path.'/views/js/logshub.js');
        $this->context->controller->addCSS($this->_path.'/views/css/logshub.min.css');

        foreach ($variables as $k => $v){
            $this->context->smarty->assign($k, $v);
        }

        return $this->context->smarty->fetch($this->local_path.'views/templates/search-box.tpl');
    }
}

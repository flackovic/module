<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class MyModule extends Module
{
    public function __construct()
    {
        $this->name = 'mymodule';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Filip Pilifi';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('My module');
        $this->description = $this->l('Description of my module.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('MYMODULE_NAME')) {
            $this->warning = $this->l('No name provided');
        }
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() ||
            !$this->registerHook('actionOrderStatusPostUpdate') ||
            !Configuration::updateValue('MYMODULE_NAME', 'my friend')
        ) {
            return false;
        }

        // Check if fiscal table exists
        $checkForTableSql = 'SHOW TABLES LIKE "'._DB_PREFIX_.'fiscal"';

        // Create table
        $createTableSql = 'CREATE TABLE '._DB_PREFIX_.'fiscal (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            invoice_id int(30) NOT NULL,
            pk_id int(30) NOT NULL,
            jir int(30) NOT NULL,
            created_at TIMESTAMP
            )';

        $dbInstance = Db::getInstance();

        $tables = $dbInstance->executeS($checkForTableSql);
        if(0 === count($tables)) {
            // Table doesn't exist
            $dbInstance->execute($createTableSql);
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !Configuration::deleteByName('MYMODULE_NAME')
        ) {
            return false;
        }

        // TODO: Remove table created on install
        return true;
    }

    public function hookActionOrderStatusPostUpdate($params)
    {

        /**
         * Check if order is considered paid.
         * This should, later on, check order status explicitly by name/id since some are
         * considered paid but are not candidates for fiscal
         *
         * TODO: Implement explicit check by id/name
         */
        if(!$params['newOrderStatus']->paid) {

            var_dump($params);die;
            return;
        }

        var_dump($params);die;

    }
}
<?php
/**
 * Dynamic SEO Tags Module for PrestaShop 8/9
 *
 * @author    Daniel Ionașcu
 * @copyright 2025 Daniel Ionașcu
 * @license   MIT
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/src/Generator/MetaDescriptionGenerator.php';

class HknSeoTags extends Module
{
    public function __construct()
    {
        $this->name = 'hknseotags';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Daniel Ionașcu';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => '9.9.9',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Dynamic SEO Tags');
        $this->description = $this->l('Automatically generate meta descriptions for products and categories.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
    }

    public function install()
    {
        if (!parent::install() || !$this->registerHook('displayHeader')) {
            return false;
        }

        foreach (Language::getLanguages(false) as $language) {
            $idLang = (int)$language['id_lang'];
            Configuration::updateValue('SEO_PRODUCT_TEMPLATE_' . $idLang, 'Buy [product_name] at [shop_name]. [price]. [stock_status].');
            Configuration::updateValue('SEO_CATEGORY_TEMPLATE_' . $idLang, 'Browse our [category_name] collection at [shop_name].');
        }

        Configuration::updateValue('SEO_TAGLINE', 'Your trusted online store');

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        foreach (Language::getLanguages(false) as $language) {
            $idLang = (int)$language['id_lang'];
            Configuration::deleteByName('SEO_PRODUCT_TEMPLATE_' . $idLang);
            Configuration::deleteByName('SEO_CATEGORY_TEMPLATE_' . $idLang);
        }

        Configuration::deleteByName('SEO_TAGLINE');

        return true;
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit' . $this->name)) {
            foreach (Language::getLanguages(false) as $language) {
                $idLang = (int)$language['id_lang'];
                $productTemplate = Tools::getValue('SEO_PRODUCT_TEMPLATE_' . $idLang);
                $categoryTemplate = Tools::getValue('SEO_CATEGORY_TEMPLATE_' . $idLang);

                Configuration::updateValue('SEO_PRODUCT_TEMPLATE_' . $idLang, $productTemplate);
                Configuration::updateValue('SEO_CATEGORY_TEMPLATE_' . $idLang, $categoryTemplate);
            }

            $tagline = Tools::getValue('SEO_TAGLINE');
            Configuration::updateValue('SEO_TAGLINE', $tagline);

            $output .= $this->displayConfirmation($this->l('Settings updated successfully.'));
        }

        return $output . $this->renderForm();
    }

    protected function renderForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submit' . $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = [
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        ];

        return $helper->generateForm([$this->getConfigForm()]);
    }

    protected function getConfigForm()
    {
        $languages = Language::getLanguages(false);
        $inputs = [];

        foreach ($languages as $language) {
            $idLang = (int)$language['id_lang'];

            $inputs[] = [
                'type' => 'textarea',
                'label' => sprintf($this->l('Product Template (%s)'), $language['name']),
                'name' => 'SEO_PRODUCT_TEMPLATE_' . $idLang,
                'rows' => 3,
                'cols' => 60,
                'desc' => $this->l('Available placeholders: [product_name], [manufacturer_name], [shop_name], [price], [stock_status]'),
            ];

            $inputs[] = [
                'type' => 'textarea',
                'label' => sprintf($this->l('Category Template (%s)'), $language['name']),
                'name' => 'SEO_CATEGORY_TEMPLATE_' . $idLang,
                'rows' => 3,
                'cols' => 60,
                'desc' => $this->l('Available placeholders: [category_name], [shop_name], [tagline]'),
            ];
        }

        $inputs[] = [
            'type' => 'text',
            'label' => $this->l('Shop Tagline'),
            'name' => 'SEO_TAGLINE',
            'size' => 60,
            'desc' => $this->l('Used in [tagline] placeholder'),
        ];

        return [
            'form' => [
                'legend' => [
                    'title' => $this->l('SEO Settings'),
                    'icon' => 'icon-cogs',
                ],
                'input' => $inputs,
                'submit' => [
                    'title' => $this->l('Save'),
                ],
            ],
        ];
    }

    protected function getConfigFormValues()
    {
        $values = [];

        foreach (Language::getLanguages(false) as $language) {
            $idLang = (int)$language['id_lang'];
            $values['SEO_PRODUCT_TEMPLATE_' . $idLang] = Configuration::get('SEO_PRODUCT_TEMPLATE_' . $idLang);
            $values['SEO_CATEGORY_TEMPLATE_' . $idLang] = Configuration::get('SEO_CATEGORY_TEMPLATE_' . $idLang);
        }

        $values['SEO_TAGLINE'] = Configuration::get('SEO_TAGLINE');

        return $values;
    }

    public function hookDisplayHeader($params)
    {
        if (!$this->active) {
            return;
        }

        $controller = $this->context->controller;
        $pageType = $controller->php_self;
        $idLang = (int)$this->context->language->id;

        if ($pageType === 'product') {
            $idProduct = (int)Tools::getValue('id_product');
            if (!$idProduct) {
                return;
            }

            $product = new Product($idProduct, true, $idLang);
            if (!Validate::isLoadedObject($product)) {
                return;
            }

            if (!empty($product->meta_description)) {
                return;
            }

            $template = Configuration::get('SEO_PRODUCT_TEMPLATE_' . $idLang);
            if (empty($template)) {
                return;
            }

            $generator = new MetaDescriptionGenerator();
            $metaDescription = $generator->generateProductMeta($product, $template, $this->context);

            if (isset($controller->page) && is_array($controller->page)) {
                $controller->page['meta']['description'] = $metaDescription;
            }

        } elseif ($pageType === 'category') {
            $idCategory = (int)Tools::getValue('id_category');
            if (!$idCategory) {
                return;
            }

            $category = new Category($idCategory, $idLang);
            if (!Validate::isLoadedObject($category)) {
                return;
            }

            if (!empty($category->meta_description)) {
                return;
            }

            $template = Configuration::get('SEO_CATEGORY_TEMPLATE_' . $idLang);
            if (empty($template)) {
                return;
            }

            $generator = new MetaDescriptionGenerator();
            $metaDescription = $generator->generateCategoryMeta($category, $template, $this->context);

            if (isset($controller->page) && is_array($controller->page)) {
                $controller->page['meta']['description'] = $metaDescription;
            }
        }
    }
}

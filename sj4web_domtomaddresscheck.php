<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Sj4web_DomtomAddressCheck extends Module
{
    public function __construct()
    {
        $this->name = 'sj4web_domtomaddresscheck';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'SJ4WEB.FR';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Validation des adresses DOM-TOM', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck');
        $this->description = $this->trans('Empêche les clients de sélectionner "France" si le code postal est DOM-TOM.', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck');
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('actionValidateCustomerAddressForm')
            && $this->registerHook('displayHeader')
            && Configuration::updateValue('SJ4WEB_DOMTOM_CHECK', 1);
    }

    public function uninstall()
    {
        return parent::uninstall() && Configuration::deleteByName('SJ4WEB_DOMTOM_CHECK');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('SJ4WEB_DOMTOM_CHECK', (int)Tools::getValue('SJ4WEB_DOMTOM_CHECK'));
        }

        return $this->renderForm();
    }

    private function renderForm()
    {
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->trans('Paramètres', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->trans('Activer la validation', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck'),
                        'name' => 'SJ4WEB_DOMTOM_CHECK',
                        'values' => [
                            ['id' => 'active_on', 'value' => 1, 'label' => $this->trans('Oui', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck')],
                            ['id' => 'active_off', 'value' => 0, 'label' => $this->trans('Non', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck')]
                        ]
                    ]
                ],
                'submit' => ['title' => $this->trans('Enregistrer', [], 'Modules.Sj4web_domtomaddresscheck.Sj4web_domtomaddresscheck')]
            ]
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->fields_value['SJ4WEB_DOMTOM_CHECK'] = Configuration::get('SJ4WEB_DOMTOM_CHECK');

        return $helper->generateForm([$fields_form]);
    }

    public function hookActionValidateCustomerAddressForm($params)
    {
        if (!Configuration::get('SJ4WEB_DOMTOM_CHECK')) {
            return;
        }

        $form = $params['form'];

        // Récupérer les champs du formulaire
        $countryField = $form->getField('id_country');
        $postcodeField = $form->getField('postcode');

        // Vérifier que les champs existent bien
        if (!$countryField || !$postcodeField) {
            return; // Évite une erreur si un champ est absent
        }

        $id_country = (int)$countryField->getValue();
        $postcode = (int)$postcodeField->getValue();

        // Récupérer la configuration DOM TOM pour le code postal saisie (ça retourne queluqe chose si et seulement c'est un code postal DOM-TOM)
        $domtom_data = $this->isDomTomPostalCode($postcode);

        if ($domtom_data) {
            if (isset($domtom_data['id_country']) && !empty($domtom_data['id_country'])) {
                /* Si l'id country correspond pas l'id country du code postal saisi qui existe dans la table domtom alors on lève l'erreur*/
                if ($id_country !== (int)$domtom_data['id_country']) {
                    $countryField->addError(sprintf(
                            $this->trans('Le code postal %s correspond à %s. Veuillez sélectionner "%s" comme pays.'),
                            $postcode,
                            $domtom_data['nom'],
                            Country::getNameById(Context::getContext()->language->id, $domtom_data['id_country'])
                        )
                    );
                }
            }
        }
    }

    function isDomTomPostalCode($postcode)
    {
        $domtom_codes_postaux = $this->getDomtomPostalConfig();

        foreach ($domtom_codes_postaux as $data) {
            if ($postcode >= $data['start'] && $postcode <= $data['end']) {
                return $data; // Retourne les infos du territoire si trouvé
            }
        }

        return false; // Pas trouvé = code postal non DOM-TOM
    }

    /**
     * Donne la liste des DOM TOM
     * @return array[]
     */
    private function getDomtomPostalConfig()
    {
        return [
            'GP' => ['nom' => 'Guadeloupe', 'start' => 97100, 'end' => 97190, 'id_country' => Country::getByIso('GP')],
            'MQ' => ['nom' => 'Martinique', 'start' => 97200, 'end' => 97290, 'id_country' => Country::getByIso('MQ')],
            'GF' => ['nom' => 'Guyane française', 'start' => 97300, 'end' => 97390, 'id_country' => Country::getByIso('GF')],
            'RE' => ['nom' => 'La Réunion', 'start' => 97400, 'end' => 97490, 'id_country' => Country::getByIso('RE')],
            'YT' => ['nom' => 'Mayotte', 'start' => 97600, 'end' => 97690, 'id_country' => Country::getByIso('YT')],
            'PM' => ['nom' => 'Saint-Pierre-et-Miquelon', 'start' => 97500, 'end' => 97590, 'id_country' => Country::getByIso('PM')],
            'NC' => ['nom' => 'Nouvelle-Calédonie', 'start' => 98800, 'end' => 98890, 'id_country' => Country::getByIso('NC')],
            'PF' => ['nom' => 'Polynésie française', 'start' => 98700, 'end' => 98790, 'id_country' => Country::getByIso('PF')],
            'WF' => ['nom' => 'Wallis-et-Futuna', 'start' => 98600, 'end' => 98690, 'id_country' => Country::getByIso('WF')],
            'TA' => ['nom' => 'Terres australes et antarctiques françaises', 'start' => 98400, 'end' => 98490, 'id_country' => Country::getByIso('TA')],
            'BL' => ['nom' => 'Saint-Barthélemy', 'start' => 97700, 'end' => 97790, 'id_country' => Country::getByIso('BL')],
            'MF' => ['nom' => 'Saint-Martin', 'start' => 97800, 'end' => 97890, 'id_country' => Country::getByIso('MF')]
        ];
    }

    public function hookDisplayHeader()
    {
        // On envoi ce qu'il faut coté Javascript uniquement si on est dans un partie adresse ou sur le controlleur order
        if ($this->isAddressPage() && Configuration::get('SJ4WEB_DOMTOM_CHECK')) {
            Media::addJsDef([
                'domtom_country_ids' => [
                    'GP' => Country::getByIso('GP'),
                    'MQ' => Country::getByIso('MQ'),
                    'GF' => Country::getByIso('GF'),
                    'RE' => Country::getByIso('RE'),
                    'YT' => Country::getByIso('YT'),
                    'PM' => Country::getByIso('PM'),
                    'NC' => Country::getByIso('NC'),
                    'PF' => Country::getByIso('PF'),
                    'WF' => Country::getByIso('WF'),
                    'TA' => Country::getByIso('TA'),
                    'BL' => Country::getByIso('BL'),
                    'MF' => Country::getByIso('MF')
                ]
            ]);

            $this->context->controller->addJS($this->_path . 'views/js/domtomvalidation.js');
        }
    }

    private function isAddressPage()
    {
        $controller = Tools::getValue('controller');
        return in_array($controller, ['address', 'order', 'order-opc']);
    }

}

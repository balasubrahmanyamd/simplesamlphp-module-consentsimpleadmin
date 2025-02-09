<?php

declare(strict_types=1);

namespace SimpleSAML\Module\consentsimpleadmin\Controller;

//use Exception;
//use SimpleSAML\Auth;
use SimpleSAML\Configuration;
//use SimpleSAML\Logger;
//use SimpleSAML\Module\consent\Auth\Process\Consent;
use SimpleSAML\Module\consent\Store;
use SimpleSAML\Session;
//use SimpleSAML\Metadata\MetaDataStorageHandler;
//use SimpleSAML\Module\consent\Store;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller class for the consentsimpleadmin module.
 *
 * This class serves the different views available in the module.
 *
 * @package simplesamlphp/simplesamlphp-module-consentsimpleadmin
 */
class Statistics
{
    /** @var \SimpleSAML\Configuration */
    protected Configuration $config;

    /** @var \SimpleSAML\Session */
    protected Session $session;


    /**
     * Controller constructor.
     *
     * It initializes the global configuration and session for the controllers implemented here.
     *
     * @param \SimpleSAML\Configuration $config The configuration to use by the controllers.
     * @param \SimpleSAML\Session $session The session to use by the controllers.
     *
     * @throws \Exception
     */
    public function __construct(
        Configuration $config,
        Session $session
    ) {
        $this->config = $config;
        $this->session = $session;
    }



    /**
     * @param \Symfony\Component\HttpFoundation\Request $request The current request.
     *
     * @return \SimpleSAML\XHTML\Template
     */
    public function stats(Request $request): Template
    {
        $authUtils = new Utils\Auth();
        $authUtils->requireAdmin();

        // Get config object
        $consentconfig = $this->config::getConfig('module_consentSimpleAdmin.php');

        // Parse consent config
        $consent_storage = Store::parseStoreConfig($consentconfig->getValue('store'));

        // Get all consents for user
        $stats = $consent_storage->getStatistics();

        // Init template
        $t = new Template($config, 'consentSimpleAdmin:consentstats.twig');
        $translator = $t->getTranslator();

        $t->data['stats'] = $stats;
        $t->data['total'] = $translator->t(
            '{consentSimpleAdmin:consentsimpleadmin:stattotal}',
            ['%NO%' => $t->data['stats']['total']]
        );
        $t->data['statusers'] = $translator->t(
            '{consentSimpleAdmin:consentsimpleadmin:statusers}',
            ['%NO%' => $t->data['stats']['users']]
        );
        $t->data['statservices'] = $translator->t(
            '{consentSimpleAdmin:consentsimpleadmin:statservices}',
            ['%NO%' => $t->data['stats']['services']]
        );
        return $t;
    }
}

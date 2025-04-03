<?php
/**
 * @package     THM
 * @extension   mod_contact
 * @author      THM - Referat Neue Medien, <webredaktion@thm.de>
 * @copyright   2025 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

use Joomla\CMS\Extension\Service\Provider\{HelperFactory, Module, ModuleDispatcherFactory};
use Joomla\DI\{Container, ServiceProviderInterface};

return new class () implements ServiceProviderInterface {

    public function register(Container $container): void
    {
        $container->registerServiceProvider(new ModuleDispatcherFactory('\\THM\\Module\\Contact'));
        $container->registerServiceProvider(new HelperFactory('\\THM\\Module\\Contact\\Site\\Helper'));
        $container->registerServiceProvider(new Module());
    }
};
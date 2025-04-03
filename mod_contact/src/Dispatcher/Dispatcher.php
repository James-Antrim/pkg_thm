<?php
/**
 * @package     THM
 * @extension   mod_contact
 * @author      THM - Referat Neue Medien, <webredaktion@thm.de>
 * @copyright   2025 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace THM\Module\Contact\Site\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\{Application\SiteApplication, Dispatcher\AbstractModuleDispatcher};
use Joomla\CMS\Helper\{HelperFactoryAwareInterface, HelperFactoryAwareTrait};
use Joomla\Registry\Registry;
use THM\Module\Contact\Site\Helper\ContactHelper as Helper;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    /**
     * Returns the layout data.
     *
     * @return  array
     *
     * @since   4.4.0
     */
    protected function getLayoutData(): array
    {
        $layoutData = parent::getLayoutData();

        if (empty($layoutData['app']) or empty($layoutData['input'])) {
            return $layoutData;
        }

        /** @var Registry $appData */
        $appData = $layoutData['app'];
        /** @var SiteApplication $appData */
        $inputData = $layoutData['input'];
        $context   = strtolower($inputData->get('option', '') . '.' . $inputData->get('view', ''));

        if (in_array($context, ['com_content.article', 'com_content.category'])) {
            /** @var Helper $helper */
            $helper = $this->getHelperFactory()->getHelper('ContactHelper');
            if ($contacts = $helper->contacts($appData)) {
                $layoutData['contacts'] = $contacts;
            }
        }

        return $layoutData;
    }
}
<?php
/**
 * @package     THM
 * @extension   mod_contact
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2025 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace THM\Modules\Contact\Site\Dispatcher;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\{Dispatcher\ModuleDispatcher, Helper\ModuleHelper};
use Joomla\Registry\Registry;
use THM\Modules\Contact\Site\Helper\ContactHelper as Helper;

class Dispatcher extends ModuleDispatcher
{
    /** @inheritDoc
     * @throws Exception on uninstantiated calls for core classes
     */
    public function dispatch(): void
    {
        $resourceID = $this->input->getInt('id');
        $view       = $this->input->getCmd('view');

        if ($this->input->getCmd('option') !== 'com_content' or !in_array($view, ['article', 'category']) or !$resourceID) {
            return;
        }

        $contacts = [];
        $helper   = new Helper($this->app);
        $params   = new Registry($this->module->params);
        $suffix   = (string) $params->get('suffix');
        $pattern  = '({contact' . $suffix . '\s(.*?)})';

        if ($view === 'article') {
            $text = html_entity_decode($helper->articleText($resourceID));
            preg_match($pattern, $text, $matches);

            if (!$matches or !$contacts = $helper->contacts($matches)) {
                $text = html_entity_decode($helper->categoryText($resourceID, true));
                preg_match($pattern, $text, $matches);

                if (!$matches or !$contacts = $helper->contacts($matches)) {
                    return;
                }
            }
        }
        else {
            $text = html_entity_decode($helper->categoryText($resourceID));
            preg_match($pattern, $text, $matches);

            if (!$matches or !$contacts = $helper->contacts($matches)) {
                return;
            }
        }

        $moduleclass_sfx = htmlspecialchars($suffix);
        require ModuleHelper::getLayoutPath('mod_contact', $params->get('layout', 'default'));
    }
}
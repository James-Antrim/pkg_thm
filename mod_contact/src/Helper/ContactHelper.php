<?php
/**
 * @package     THM
 * @extension   mod_contact
 * @author      THM - Referat Neue Medien, <webredaktion@thm.de>
 * @copyright   2025 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace THM\Module\Contact\Site\Helper;

use Joomla\CMS\Table\{Category, Content};
use Joomla\Database\{DatabaseAwareInterface, DatabaseAwareTrait, DatabaseDriver, ParameterType};
use Joomla\CMS\Application\SiteApplication;

class ContactHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    private SiteApplication $app;

    /**
     * Returns the text of the article.
     *
     * @param   int  $articleID
     *
     * @return string
     */
    public function articleText(int $articleID): string
    {
        /** @var DatabaseDriver $db */
        $db      = $this->getDatabase();
        $article = new Content($db);

        return $article->load($articleID) ? $article->introtext . $article->fulltext : '';
    }

    /**
     * Returns the text of the category description.
     *
     * @param   int   $categoryID  the id of the resource
     * @param   bool  $byArticle   whether the id is that of an article
     *
     * @return string
     */
    public function categoryText(int $categoryID, bool $byArticle = false): string
    {
        /** @var DatabaseDriver $db */
        $db = $this->getDatabase();

        if ($byArticle) {
            $article = new Content($db);

            if (!$article->load($categoryID)) {
                return '';
            }

            $categoryID = $article->catid;
        }

        $category = new Category($db);

        return $category->load($categoryID) ? $category->description : '';
    }

    /**
     * Delivers contact properties for the contacts listed in the displayed content.
     *
     * @param   SiteApplication  $app
     *
     * @return array
     */
    public function contacts(SiteApplication $app): array
    {
        $this->app = $app;

        $resourceID = $app->input->getInt('id');
        $view       = $app->input->getCmd('view');

        $params  = $app->getParams();
        $suffix  = (string) $params->get('suffix');
        $pattern = '({contact' . $suffix . '\s(.*?)})';

        if ($view === 'article') {
            $text = html_entity_decode($this->articleText($resourceID));
            preg_match($pattern, $text, $matches);

            if ($matches and $contacts = $this->resolve($matches)) {
                return $contacts;
            }

            $text = html_entity_decode($this->categoryText($resourceID, true));
            preg_match($pattern, $text, $matches);

            return !$matches ? [] : $this->resolve($matches);
        }

        $text = html_entity_decode($this->categoryText($resourceID));
        preg_match($pattern, $text, $matches);

        return !$matches ? [] : $this->resolve($matches);
    }

    /**
     * Resolves pattern matches to contacts.
     *
     * @param   string[]  $contacts  the text matches to resolve to contacts
     *
     * @return array
     */
    public function resolve(array $contacts): array
    {
        $contacts = array_map('trim', $contacts);
        $db       = $this->getDatabase();
        $language = $db->qn('language');
        $order    = "FIELD('" . implode("','", $contacts) . "')";
        $tag      = $db->q($this->app->getLanguage()->getTag());

        $query = $db->getQuery(true);
        $query->select('*')->from($db->qn('#__contact_details'))
            ->where($db->qn('published') . ' = 1')
            ->whereIn($db->qn('name'), $contacts, ParameterType::STRING)
            ->where("($language = '*' OR $language = $tag)")
            ->order($order);

        $db->setQuery($query);

        return $db->loadObjectList() ?: [];
    }
}
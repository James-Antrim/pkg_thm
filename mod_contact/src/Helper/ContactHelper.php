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
     * @param   array  $contacts  the text matches to resolve to contacts
     *
     * @return array
     */
    public function resolve(array $contacts): array
    {
        $names  = array_map('trim', array_filter(explode(',', $contacts[1]), function($a){return !is_numeric($a);}));
        $ids    = array_map('trim', array_filter(explode(',', $contacts[1]), function($a){return is_numeric($a);}));

        if (!empty($names) || !empty($ids))
        {
            $db         = $this->getDatabase();
            $language   = $db->qn('language');
            $order      = array_map('trim', explode(',', $contacts[1]));
            $tag        = $db->q($this->app->getLanguage()->getTag());

            $query = $db->getQuery(true);
            $query->select('*')->from($db->qn('#__contact_details'))
                ->where($db->qn('published') . ' = 1')
                ->where("($language = '*' OR $language = $tag)");

            if (!empty($ids))
            {
                $query->where($db->quoteName('id')
                    . ' IN ' . '(' . implode(",", $ids) . ')', (!empty($names)) ? 'OR' : 'AND');
            }

            if (!empty($names))
            {
                $contition = $db->quoteName('name')
                    . ' IN ' . '(' . implode(",", array_map(function($nms){return '"' . $nms . '"';}, $names)) . ')';

                (!empty($ids)) ? $query->orWhere($contition) : $query->where($contition);
            }

            $db->setQuery($query);

            $results = $db->loadObjectList();

            if (!empty($results))
            {
                $result = array();
                
                // Reorder the results
                foreach ($order as $contact)
                {
                    $key = (is_numeric($contact))
                        ? array_search((int) $contact, array_column($results, 'id'))
                        : array_search($contact, array_column($results, 'name'));

                    if ($key !== false)
                    {
                        array_push($result, $results[$key]);
                    }
                }

                return $result;
            }
        }

        return array();
    }
}
<?php
/**
 * @package     THM
 * @extension   mod_contact
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2025 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace THM\Modules\Contact\Site\Helper;

use Joomla\CMS\Table\{Category, Content};
use Joomla\Database\{DatabaseAwareInterface, DatabaseAwareTrait, DatabaseDriver, ParameterType};
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\CMSApplicationInterface;

class ContactHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    private CMSApplication $app;

    /**
     * Moves the app into the instance for simple access to its functions.
     *
     * @param   CMSApplication  $app
     */
    public function __construct(CMSApplicationInterface $app)
    {
        $this->app = $app;
    }

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
     * Creates a list of contacts.
     *
     * @param   string[]  $contacts  the text matches to resolve to contacts
     *
     * @return array
     */
    public function contacts(array $contacts): array
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

        return $db->loadObjectList();
    }
}
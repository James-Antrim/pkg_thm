<?php
/**
 * @package     THM
 * @extension   mod_contact
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2025 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

use Joomla\CMS\HTML\HTMLHelper as HTML;
use Joomla\CMS\Language\Text;

foreach ($contacts as $contact) {
    if (!$contact->name) {
        continue;
    }

    $contact->address   = str_replace("|", "<br />", $contact->address);
    $contact->email_to  = HTML::_('content.prepare', strtolower($contact->email_to));
    $contact->email_to  = str_replace("|", "<br />", $contact->email_to);
    $contact->fax       = str_replace("|", "<br />", $contact->fax);
    $contact->telephone = str_replace("|", "<br />", $contact->telephone);
    ?>
    <p class="contact-name">
        <strong><?php echo $contact->name; ?></strong><br/>
    </p>
    <ul class="contact-details">
        <?php if ($contact->address): ?>
            <li>
                <span class="fa fa-map-marker-alt"></span>
                <span class="contact-data"><?php echo $contact->address; ?></span>
            </li>
        <?php endif; ?>
        <?php if ($contact->telephone): ?>
            <li>
                <span class="fa fa-phone"></span>
                <span class="contact-data"><?php echo $contact->telephone; ?></span>
            </li>
        <?php endif; ?>
        <?php if ($contact->fax): ?>
            <li>
                <span class="fa fa-print"></span>
                <span class="contact-data"><?php echo $contact->fax; ?></span>
            </li>
        <?php endif; ?>
        <?php if ($contact->email_to): ?>
            <li>
                <span class="fa fa-envelope"></span>
                <span class="contact-data"><?php echo $contact->email_to; ?></span>
            </li>
        <?php endif; ?>
        <?php if ($contact->webpage): ?>
            <li>
                <span class="fa fa-external-link-alt"></span>
                <span class="contact-data">
                    <a href="<?php echo $contact->webpage; ?>" target="_blank">
                        <?php echo Text::_('MOD_CONTACT_LINK'); ?>
                    </a>
                </span>
            </li>
        <?php endif; ?>
    </ul>
    <br/>
    <?php
}
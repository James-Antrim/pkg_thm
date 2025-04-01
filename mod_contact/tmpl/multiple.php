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

    $title = '';

    // Check if [] optional substring exists
    if (strpos($contact->address, '**')) {
        $address = explode('**', $contact->address);
        [$title, $address2] = explode('*', $address[1]);
        $address[1] = $address2;
    }
    else {
        $address = [$contact->address];
    }

    $address           = str_replace("|", "<br/>", $address);
    $contact->email_to = HTML::_('content.prepare', strtolower($contact->email_to));
    $contact->email_to = str_replace("|", "<br />", $contact->email_to);
    $fax               = strpos($contact->fax, '**') ? explode('**', $contact->fax) : [$contact->fax];
    $fax               = str_replace("|", "<br/>", $fax);
    $contact->misc     = str_replace("|", "<br />", $contact->misc);
    $telephone         = strpos($contact->telephone, '**') ? explode('**', $contact->telephone) : [$contact->telephone];
    $telephone         = str_replace("|", "<br/>", $telephone);

    ?>

    <p class="contact-name">
        <strong><?php echo $contact->name; ?></strong><br/>
    </p>
    <ul class="thmcontact">';
        <?php if ($contact->address): ?>
            <li>
                <span class="fa fa-map-marker-alt"></span>
                <span class="contact-data"><?php echo $address[0]; ?>'</span>
            </li>
        <?php endif; ?>
        <?php if ($contact->telephone): ?>
            <li>
                <span class="fa fa-phone"></span>
                <span class="contact-data"><?php echo $telephone[0]; ?></span>
            </li>
        <?php endif; ?>
        <?php if ($contact->fax): ?>
            <li>
                <span class="fa fa-print"></span>
                <span class="contact-data"><?php echo $fax[0]; ?></span>
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
        <?php if ($contact->misc): ?>
            <li>
                <span class="fa fa-info"></span>
                <span class="contact-data"><?php echo $contact->misc; ?></span>
            </li>
        <?php endif; ?>
    </ul>
    <br/>
    <?php if (!empty($title)): ?>
        <p class="contact-title">
            <strong><?php echo $title; ?></strong>
        </p>
        <ul class="contact-details">
            <?php if (!empty($address[1])): ?>
                <li>
                    <span class="fa fa-map-marker-alt"></span>
                    <span class="contact-data"><?php echo $address[1]; ?>'</span>
                </li>
            <?php endif; ?>
            <?php if (!empty($telephone[1])): ?>
                <li>
                    <span class="fa fa-phone"></span>
                    <span class="contact-data"><?php echo $telephone[1]; ?></span>
                </li>
            <?php endif; ?>
            <?php if (!empty($fax[1])): ?>
                <li>
                    <span class="fa fa-print"></span>
                    <span class="contact-data"><?php echo $fax[1]; ?></span>
                </li>
            <?php endif; ?>
        </ul>
        <br/>
    <?php endif; ?>
    <?php
}
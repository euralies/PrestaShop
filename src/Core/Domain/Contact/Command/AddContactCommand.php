<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Contact\Command;

use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Class AddContactCommand is responsible for adding the contact data.
 */
class AddContactCommand extends AbstractContactCommand
{
    /**
     * @var string[]
     */
    private $localisedTitles;

    /**
     * @var bool
     */
    private $isMessageSavingEnabled;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var string[]
     */
    private $localisedDescription;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @param string[] $localisedTitles
     * @param bool $isMessageSavingEnabled
     *
     * @throws ContactConstraintException
     */
    public function __construct(array $localisedTitles, $isMessageSavingEnabled)
    {
        $this->assertIsLocalisedTitleValid($localisedTitles);

        $this->localisedTitles = $localisedTitles;
        $this->isMessageSavingEnabled = $isMessageSavingEnabled;
    }

    /**
     * @return string[]
     */
    public function getLocalisedTitles()
    {
        return $this->localisedTitles;
    }

    /**
     * @return bool
     */
    public function isMessageSavingEnabled()
    {
        return $this->isMessageSavingEnabled;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     *
     * @throws DomainConstraintException
     */
    public function setEmail($email)
    {
        if ($email) {
            $this->email = new Email($email);
        }

        return $this;
    }

    /**
     * @return string[]
     */
    public function getLocalisedDescription()
    {
        return $this->localisedDescription;
    }

    /**
     * @param string[] $localisedDescription
     *
     * @return self
     */
    public function setLocalisedDescription($localisedDescription)
    {
        $this->localisedDescription = $localisedDescription;

        return $this;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     *
     * @throws ContactConstraintException
     */
    public function setShopAssociation(array $shopAssociation)
    {
        if (!$this->assertArrayContainsAllIntegerValues($shopAssociation)) {
            throw new ContactConstraintException(
                sprintf(
                    'Given shop association %s must contain all integer values',
                    var_export($shopAssociation, true)
                ),
                ContactConstraintException::INVALID_SHOP_ASSOCIATION
            );
        }

        $this->shopAssociation = $shopAssociation;

        return $this;
    }

    /**
     * @param array $localisedTitles
     *
     * @throws ContactConstraintException
     */
    private function assertIsLocalisedTitleValid(array $localisedTitles)
    {
        if (!$this->assertIsNotEmptyAndContainsAllNonEmptyStringValues($localisedTitles)) {
            throw new ContactConstraintException(
                sprintf(
                    'Expected to have not empty titles array but received %s',
                    var_export($localisedTitles, true)
                ),
                ContactConstraintException::INVALID_TITLE
            );
        }

        foreach ($localisedTitles as $title) {
            if (!$this->assertIsGenericName($title)) {
                throw new ContactConstraintException(
                    sprintf(
                        'Expected value %s to match given regex /^[^<>={}]*$/u but failed',
                        var_export($title, true)
                    ),
                    ContactConstraintException::INVALID_TITLE
                );
            }
        }
    }
}

<?php declare(strict_types=1);

namespace Blauband\EmailBase\Core\Content\LoggedMail;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class LoggedMailEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $fromMail;

    /**
     * @var string
     */
    protected $toMail;

    /**
     * @var string
     */
    protected $bccMail;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $bodyHtml;

    /**
     * @var string
     */
    protected $bodyPlain;

    /**
     * @var CustomerEntity
     */
    protected $customer;

    /**
     * @return string
     */
    public function getFromMail(): string
    {
        return $this->fromMail;
    }

    /**
     * @param string $fromMail
     *
     * @return LoggedMailEntity
     */
    public function setFromMail(string $fromMail): LoggedMailEntity
    {
        $this->fromMail = $fromMail;

        return $this;
    }

    /**
     * @return string
     */
    public function getToMail(): string
    {
        return $this->toMail;
    }

    /**
     * @param string $toMail
     *
     * @return LoggedMailEntity
     */
    public function setToMail(string $toMail): LoggedMailEntity
    {
        $this->toMail = $toMail;

        return $this;
    }

    /**
     * @return string
     */
    public function getBccMail(): string
    {
        return $this->bccMail;
    }

    /**
     * @param string $bccMail
     *
     * @return LoggedMailEntity
     */
    public function setBccMail(string $bccMail): LoggedMailEntity
    {
        $this->bccMail = $bccMail;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     *
     * @return LoggedMailEntity
     */
    public function setSubject(string $subject): LoggedMailEntity
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyHtml(): string
    {
        return $this->bodyHtml;
    }

    /**
     * @param string $bodyHtml
     *
     * @return LoggedMailEntity
     */
    public function setBodyHtml(string $bodyHtml): LoggedMailEntity
    {
        $this->bodyHtml = $bodyHtml;

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyPlain(): string
    {
        return $this->bodyPlain;
    }

    /**
     * @param string $bodyPlain
     *
     * @return LoggedMailEntity
     */
    public function setBodyPlain(string $bodyPlain): LoggedMailEntity
    {
        $this->bodyPlain = $bodyPlain;

        return $this;
    }

    /**
     * @return CustomerEntity
     */
    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    /**
     * @param CustomerEntity $customer
     *
     * @return LoggedMailEntity
     */
    public function setCustomer(CustomerEntity $customer): LoggedMailEntity
    {
        $this->customer = $customer;

        return $this;
    }
}

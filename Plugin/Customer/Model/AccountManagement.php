<?php
/**
 * Trust Payments Magento 2
 *
 * This Magento 2 extension enables to process payments with Trust Payments (https://www.trustpayments.com//).
 *
 * @package TrustPayments_Payment
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
namespace TrustPayments\Payment\Plugin\Customer\Model;

use Magento\Checkout\Model\Session as CheckoutSession;

class AccountManagement
{

    /**
     *
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     *
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(CheckoutSession $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function beforeIsEmailAvailable(\Magento\Customer\Model\AccountManagement $subject, $customerEmail,
        $websiteId = null)
    {
        $this->checkoutSession->setTrustPaymentsCheckoutEmailAddress($customerEmail);
    }
}
<?php
/**
 * Trust Payments Magento 2
 *
 * This Magento 2 extension enables to process payments with Trust Payments (https://www.trustpayments.com//).
 *
 * @package TrustPayments_Payment
 * @author customweb GmbH (http://www.customweb.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
namespace TrustPayments\Payment\Plugin\Sales\Model\AdminOrder;

use TrustPayments\Payment\Model\Payment\Method\Adapter;

class Create
{

    public function beforeCreateOrder(\Magento\Sales\Model\AdminOrder\Create $subject)
    {
        if ($subject->getQuote()
            ->getPayment()
            ->getMethodInstance() instanceof Adapter) {
            $subject->setSendConfirmation(false);
        }
    }
}
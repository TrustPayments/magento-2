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
namespace TrustPayments\Payment\Model;

use Magento\Framework\Exception\LocalizedException;

class CustomerIdManipulationException extends LocalizedException
{
    public function __construct()
    {
        parent::__construct(\__('The payment timed out. Please reload the page and submit the order again.'));
    }
}
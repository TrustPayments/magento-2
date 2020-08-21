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
namespace TrustPayments\Payment\Model\Webhook\Listener;

/**
 * Webhook listener command pool interface.
 */
interface CommandPoolInterface
{

    /**
     * Retrieves listener.
     *
     * @param string $commandCode
     * @return CommandInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($commandCode);
}
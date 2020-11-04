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
namespace TrustPayments\Payment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Trust Payments payment method configuration search results.
 *
 * @api
 */
interface PaymentMethodConfigurationSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get payment method configurations list.
     *
     * @return \TrustPayments\Payment\Api\Data\PaymentMethodConfigurationInterface[]
     */
    public function getItems();

    /**
     * Set payment method configurations list.
     *
     * @param \TrustPayments\Payment\Api\Data\PaymentMethodConfigurationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
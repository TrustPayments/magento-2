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
namespace TrustPayments\Payment\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Trust Payments token info search results.
 *
 * @api
 */
interface TokenInfoSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get token infos list.
     *
     * @return \TrustPayments\Payment\Api\Data\TokenInfoInterface[]
     */
    public function getItems();

    /**
     * Set token infos list.
     *
     * @param \TrustPayments\Payment\Api\Data\TokenInfoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
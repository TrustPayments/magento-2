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
namespace TrustPayments\Payment\Api;

use TrustPayments\Payment\Model\TokenInfo;

/**
 * Token info management interface.
 *
 * @api
 */
interface TokenInfoManagementInterface
{

    /**
     * Fetches the token version's latest state from Trust Payments and updates the stored information.
     *
     * @param int $spaceId
     * @param int $tokenVersionId
     */
    public function updateTokenVersion($spaceId, $tokenVersionId);

    /**
     * Fetches the token's latest state from Trust Payments and updates the stored information.
     *
     * @param int $spaceId
     * @param int $tokenId
     */
    public function updateToken($spaceId, $tokenId);

    /**
     * Deletes the token on Trust Payments.
     *
     * @param Data\TokenInfoInterface $token
     */
    public function deleteToken(TokenInfo $token);
}
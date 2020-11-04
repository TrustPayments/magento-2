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
namespace TrustPayments\Payment\Model\Webhook\Listener;

use TrustPayments\Payment\Api\TokenInfoManagementInterface;
use TrustPayments\Payment\Model\Webhook\ListenerInterface;
use TrustPayments\Payment\Model\Webhook\Request;

/**
 * Webhook listener to handle token versions.
 */
class TokenVersionListener implements ListenerInterface
{

    /**
     *
     * @var TokenInfoManagementInterface
     */
    private $tokenInfoManagement;

    /**
     *
     * @param TokenInfoManagementInterface $tokenInfoManagement
     */
    public function __construct(TokenInfoManagementInterface $tokenInfoManagement)
    {
        $this->tokenInfoManagement = $tokenInfoManagement;
    }

    public function execute(Request $request)
    {
        $this->tokenInfoManagement->updateTokenVersion($request->getSpaceId(), $request->getEntityId());
    }
}
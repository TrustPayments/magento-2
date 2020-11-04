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

use TrustPayments\Payment\Model\Service\ManualTaskService;
use TrustPayments\Payment\Model\Webhook\ListenerInterface;
use TrustPayments\Payment\Model\Webhook\Request;

/**
 * Webhook listener to handle manual tasks.
 */
class ManualTaskListener implements ListenerInterface
{

    /**
     *
     * @var ManualTaskService
     */
    private $manualTaskService;

    /**
     *
     * @param ManualTaskService $manualTaskService
     */
    public function __construct(ManualTaskService $manualTaskService)
    {
        $this->manualTaskService = $manualTaskService;
    }

    public function execute(Request $request)
    {
        $this->manualTaskService->update();
    }
}
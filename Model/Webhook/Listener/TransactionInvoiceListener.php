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

use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Psr\Log\LoggerInterface;
use TrustPayments\Payment\Api\TransactionInfoManagementInterface;
use TrustPayments\Payment\Api\TransactionInfoRepositoryInterface;
use TrustPayments\Payment\Model\ApiClient;
use TrustPayments\Payment\Model\Webhook\Request;
use TrustPayments\Sdk\Service\TransactionInvoiceService;

/**
 * Webhook listener to handle transaction invoices.
 */
class TransactionInvoiceListener extends AbstractOrderRelatedListener
{

    /**
     *
     * @var ApiClient
     */
    protected $apiClient;

    /**
     *
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param OrderFactory $orderFactory
     * @param OrderResourceModel $orderResourceModel
     * @param CommandPoolInterface $commandPool
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param TransactionInfoManagementInterface $transactionInfoManagement
     * @param ApiClient $apiClient
     */
    public function __construct(ResourceConnection $resource, LoggerInterface $logger, OrderFactory $orderFactory,
        OrderResourceModel $orderResourceModel, CommandPoolInterface $commandPool,
        TransactionInfoRepositoryInterface $transactionInfoRepository,
        TransactionInfoManagementInterface $transactionInfoManagement, ApiClient $apiClient)
    {
        parent::__construct($resource, $logger, $orderFactory, $orderResourceModel, $commandPool,
            $transactionInfoRepository, $transactionInfoManagement);
        $this->apiClient = $apiClient;
    }

    /**
     * Loads the transaction invoice for the webhook request.
     *
     * @param Request $request
     * @return \TrustPayments\Sdk\Model\TransactionInvoice
     */
    protected function loadEntity(Request $request)
    {
        return $this->apiClient->getService(TransactionInvoiceService::class)->read($request->getSpaceId(),
            $request->getEntityId());
    }

    /**
     * Gets the transaction's ID.
     *
     * @param \TrustPayments\Sdk\Model\TransactionInvoice $entity
     * @return int
     */
    protected function getTransactionId($entity)
    {
        return $entity->getLinkedTransaction();
    }
}
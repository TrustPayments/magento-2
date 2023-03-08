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
namespace TrustPayments\Payment\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use TrustPayments\Payment\Api\TransactionInfoManagementInterface;
use TrustPayments\Payment\Api\TransactionInfoRepositoryInterface;
use TrustPayments\Payment\Api\Data\TransactionInfoInterface;
use TrustPayments\Payment\Helper\Data as Helper;
use TrustPayments\Sdk\Model\ChargeAttemptState;
use TrustPayments\Sdk\Model\EntityQuery;
use TrustPayments\Sdk\Model\EntityQueryFilter;
use TrustPayments\Sdk\Model\EntityQueryFilterType;
use TrustPayments\Sdk\Model\FailureReason;
use TrustPayments\Sdk\Model\Transaction;
use TrustPayments\Sdk\Model\TransactionState;
use TrustPayments\Sdk\Service\ChargeAttemptService;

/**
 * Transaction info management service.
 */
class TransactionInfoManagement implements TransactionInfoManagementInterface
{
    /**
     *
     * @var Helper
     */
    private $helper;

    /**
     *
     * @var TransactionInfoRepositoryInterface
     */
    private $transactionInfoRepository;

    /**
     *
     * @var TransactionInfoFactory
     */
    private $transactionInfoFactory;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param Helper $helper
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param TransactionInfoFactory $transactionInfoFactory
     * @param ApiClient $apiClient
     */
    public function __construct(Helper $helper, TransactionInfoRepositoryInterface $transactionInfoRepository,
        TransactionInfoFactory $transactionInfoFactory, ApiClient $apiClient)
    {
        $this->helper = $helper;
        $this->transactionInfoRepository = $transactionInfoRepository;
        $this->transactionInfoFactory = $transactionInfoFactory;
        $this->apiClient = $apiClient;
    }

    public function update(Transaction $transaction, Order $order)
    {
        try {
            $info = $this->transactionInfoRepository->getByTransactionId($transaction->getLinkedSpaceId(),
                $transaction->getId());

            if ($info->getOrderId() != $order->getId()) {
                throw new \Exception('The Trust Payments transaction info is already linked to a different order.');
            }
        } catch (NoSuchEntityException $e) {
            $info = $this->transactionInfoFactory->create();
        }
        $info->setData(TransactionInfoInterface::TRANSACTION_ID, $transaction->getId());
        $info->setData(TransactionInfoInterface::AUTHORIZATION_AMOUNT, $transaction->getAuthorizationAmount());
        $info->setData(TransactionInfoInterface::ORDER_ID, $order->getId());
        $info->setData(TransactionInfoInterface::STATE, $transaction->getState());
        $info->setData(TransactionInfoInterface::SPACE_ID, $transaction->getLinkedSpaceId());
        $info->setData(TransactionInfoInterface::SPACE_VIEW_ID, $transaction->getSpaceViewId());
        $info->setData(TransactionInfoInterface::LANGUAGE, $transaction->getLanguage());
        $info->setData(TransactionInfoInterface::CURRENCY, $transaction->getCurrency());
        $info->setData(TransactionInfoInterface::CONNECTOR_ID,
            $transaction->getPaymentConnectorConfiguration() != null ? $transaction->getPaymentConnectorConfiguration()
                ->getConnector() : null);
        $info->setData(TransactionInfoInterface::PAYMENT_METHOD_ID,
            $transaction->getPaymentConnectorConfiguration() != null &&
            $transaction->getPaymentConnectorConfiguration()
                ->getPaymentMethodConfiguration() != null ? $transaction->getPaymentConnectorConfiguration()
                ->getPaymentMethodConfiguration()
                ->getPaymentMethod() : null);
        $info->setData(TransactionInfoInterface::IMAGE, $this->getPaymentMethodImage($transaction, $order));
        $info->setData(TransactionInfoInterface::LABELS, $this->getTransactionLabels($transaction));
        if ($transaction->getState() == TransactionState::FAILED || $transaction->getState() == TransactionState::DECLINE) {
            $info->setData(TransactionInfoInterface::FAILURE_REASON,
                $transaction->getFailureReason() instanceof FailureReason ? $transaction->getFailureReason()
                    ->getDescription() : null);
        }
        $this->transactionInfoRepository->save($info);
        return $info;
    }

    /**
     * Gets an array of the transaction's labels.
     *
     * @param Transaction $transaction
     * @return string[]
     */
    private function getTransactionLabels(Transaction $transaction)
    {
        $chargeAttempt = $this->getChargeAttempt($transaction);
        if ($chargeAttempt != null) {
            $labels = [];
            foreach ($chargeAttempt->getLabels() as $label) {
                $labels[$label->getDescriptor()->getId()] = $label->getContentAsString();
            }

            return $labels;
        } else {
            return [];
        }
    }

    /**
     * Gets the successful charge attempt of the transaction.
     *
     * @param Transaction $transaction
     * @return \TrustPayments\Sdk\Model\ChargeAttempt
     */
    private function getChargeAttempt(Transaction $transaction)
    {
        $query = new EntityQuery();
        $filter = new EntityQueryFilter();
        $filter->setType(EntityQueryFilterType::_AND);
        $filter->setChildren(
            [
                $this->helper->createEntityFilter('charge.transaction.id', $transaction->getId()),
                $this->helper->createEntityFilter('state', ChargeAttemptState::SUCCESSFUL)
            ]);
        $query->setFilter($filter);
        $query->setNumberOfEntities(1);
        $result = $this->apiClient->getService(ChargeAttemptService::class)->search($transaction->getLinkedSpaceId(),
            $query);
        if ($result != null && ! empty($result)) {
            return \current($result);
        } else {
            return null;
        }
    }

    /**
     * Gets the payment method's image.
     *
     * @param Transaction $transaction
     * @param Order $order
     * @return string
     */
    private function getPaymentMethodImage(Transaction $transaction, Order $order)
    {
        if ($transaction->getPaymentConnectorConfiguration() != null &&
            $transaction->getPaymentConnectorConfiguration()->getPaymentMethodConfiguration() != null) {
            return $this->extractImagePath(
                $transaction->getPaymentConnectorConfiguration()
                    ->getPaymentMethodConfiguration()
                    ->getResolvedImageUrl());
        } else {
            return $order->getPayment()
                ->getMethodInstance()
                ->getPaymentMethodConfiguration()
                ->getImage();
        }
    }

    /**
     * Extracts the image path from the URL.
     *
     * @param string $resolvedImageUrl
     * @return string
     */
    private function extractImagePath($resolvedImageUrl)
    {
        $index = \strpos($resolvedImageUrl, 'resource/');
        return \substr($resolvedImageUrl, $index + \strlen('resource/'));
    }
}
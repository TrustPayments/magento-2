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
namespace TrustPayments\Payment\Model\Service\Invoice;

use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Payment;
use TrustPayments\Payment\Api\PaymentMethodConfigurationManagementInterface;
use TrustPayments\Payment\Api\TransactionInfoRepositoryInterface;
use TrustPayments\Payment\Helper\Data as Helper;
use TrustPayments\Payment\Helper\Locale as LocaleHelper;
use TrustPayments\Payment\Model\ApiClient;
use TrustPayments\Payment\Model\Service\AbstractTransactionService;
use TrustPayments\Payment\Model\Service\Order\TransactionService as OrderTransactionService;
use TrustPayments\Sdk\Model\TransactionCompletion;
use TrustPayments\Sdk\Model\TransactionCompletionState;
use TrustPayments\Sdk\Model\TransactionInvoice;
use TrustPayments\Sdk\Model\TransactionInvoiceState;
use TrustPayments\Sdk\Model\TransactionLineItemUpdateRequest;
use TrustPayments\Sdk\Model\TransactionState;
use TrustPayments\Sdk\Service\TransactionService as TransactionApiService;

/**
 * Service to handle transactions in invoice context.
 */
class TransactionService extends AbstractTransactionService
{

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     *
     * @var LineItemService
     */
    private $lineItemService;

    /**
     *
     * @var TransactionInfoRepositoryInterface
     */
    private $transactionInfoRepository;

    /**
     *
     * @var OrderTransactionService
     */
    private $orderTransactionService;

    /**
     *
     * @param ResourceConnection $resource
     * @param Helper $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRegistry $customerRegistry
     * @param CartRepositoryInterface $quoteRepository
     * @param PaymentMethodConfigurationManagementInterface $paymentMethodConfigurationManagement
     * @param ApiClient $apiClient
     * @param CookieManagerInterface $cookieManager
     * @param LocaleHelper $localeHelper
     * @param LineItemService $lineItemService
     * @param TransactionInfoRepositoryInterface $transactionInfoRepository
     * @param OrderTransactionService $orderTransactionService
     */
    public function __construct(ResourceConnection $resource, Helper $helper, ScopeConfigInterface $scopeConfig,
        CustomerRegistry $customerRegistry, CartRepositoryInterface $quoteRepository, TimezoneInterface $timezone,
        PaymentMethodConfigurationManagementInterface $paymentMethodConfigurationManagement, ApiClient $apiClient,
        CookieManagerInterface $cookieManager, LocaleHelper $localeHelper, LineItemService $lineItemService,
        TransactionInfoRepositoryInterface $transactionInfoRepository, OrderTransactionService $orderTransactionService)
    {
        parent::__construct($resource, $helper, $scopeConfig, $customerRegistry, $quoteRepository, $timezone,
            $paymentMethodConfigurationManagement, $apiClient, $cookieManager);
        $this->apiClient = $apiClient;
        $this->localeHelper = $localeHelper;
        $this->lineItemService = $lineItemService;
        $this->transactionInfoRepository = $transactionInfoRepository;
        $this->orderTransactionService = $orderTransactionService;
    }

    /**
     * Updates the transaction's line items from the given invoice.
     *
     * @param Invoice $invoice
     * @param float $expectedAmount
     */
    public function updateLineItems(Invoice $invoice, $expectedAmount)
    {
        $transactionInfo = $this->transactionInfoRepository->getByOrderId($invoice->getOrderId());
        if ($transactionInfo->getState() == TransactionState::AUTHORIZED) {
            $lineItems = $this->lineItemService->convertInvoiceLineItems($invoice, $expectedAmount);

            $updateRequest = new TransactionLineItemUpdateRequest();
            $updateRequest->setTransactionId($transactionInfo->getTransactionId());
            $updateRequest->setNewLineItems($lineItems);
            $this->apiClient->getService(TransactionApiService::class)->updateTransactionLineItems(
                $transactionInfo->getSpaceId(), $updateRequest);
        }
    }

    /**
     * Completes the transaction linked to the given payment's and invoice's order.
     *
     * @param Payment $payment
     * @param Invoice $invoice
     * @param float $amount
     * @throws \Exception
     */
    public function complete(Payment $payment, Invoice $invoice, $amount)
    {
        $this->updateLineItems($invoice, $amount);

        $completion = $this->orderTransactionService->complete($invoice->getOrder());
        if (! ($completion instanceof TransactionCompletion) ||
            $completion->getState() == TransactionCompletionState::FAILED) {
            throw new \Magento\Framework\Exception\LocalizedException(
                \__('The capture of the invoice failed on the gateway: %1.',
                    $this->localeHelper->translate($completion->getFailureReason()
                        ->getDescription())));
        }

        try {
            $transactionInvoice = $this->orderTransactionService->getTransactionInvoice($invoice->getOrder());
            if ($transactionInvoice instanceof TransactionInvoice &&
                $transactionInvoice->getState() != TransactionInvoiceState::PAID &&
                $transactionInvoice->getState() != TransactionInvoiceState::NOT_APPLICABLE) {
                $invoice->setTrustpaymentsCapturePending(true);
            }
        } catch (NoSuchEntityException $e) {}

        $authorizationTransaction = $payment->getAuthorizationTransaction();
        $authorizationTransaction->close(false);
        $invoice->getOrder()
            ->addRelatedObject($invoice)
            ->addRelatedObject($authorizationTransaction);
    }
}
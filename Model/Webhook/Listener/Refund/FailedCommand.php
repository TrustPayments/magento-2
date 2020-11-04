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
namespace TrustPayments\Payment\Model\Webhook\Listener\Refund;

use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use TrustPayments\Payment\Api\RefundJobRepositoryInterface;
use TrustPayments\Payment\Helper\Locale as LocaleHelper;

/**
 * Webhook listener command to handle failed refunds.
 */
class FailedCommand extends AbstractCommand
{

    /**
     *
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     *
     * @var LocaleHelper
     */
    private $localeHelper;

    /**
     *
     * @param RefundJobRepositoryInterface $refundJobRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param LocaleHelper $localeHelper
     */
    public function __construct(RefundJobRepositoryInterface $refundJobRepository, OrderRepositoryInterface $orderRepository,
        LocaleHelper $localeHelper)
    {
        parent::__construct($refundJobRepository);
        $this->orderRepository = $orderRepository;
        $this->localeHelper = $localeHelper;
    }

    /**
     *
     * @param \TrustPayments\Sdk\Model\Refund $entity
     * @param Order $order
     */
    public function execute($entity, Order $order)
    {
        $order->addCommentToStatusHistory(
            \__('The refund of %1 failed on the gateway: %2',
                $order->getBaseCurrency()
                    ->formatTxt($entity->getAmount()),
                $this->localeHelper->translate($entity->getFailureReason()
                    ->getDescription())));
        $this->orderRepository->save($order);
        $this->deleteRefundJob($entity);
    }
}
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

/**
 * Payment method configuration management interface.
 *
 * @api
 */
interface PaymentMethodConfigurationManagementInterface
{

    /**
     * Synchronizes the payment method configurations from Trust Payments.
     */
    public function synchronize();

    /**
     * Updates the payment method configuration information.
     *
     * @param \TrustPayments\Sdk\Model\PaymentMethodConfiguration $configuration
     */
    public function update(\TrustPayments\Sdk\Model\PaymentMethodConfiguration $configuration);
}

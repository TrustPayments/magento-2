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
namespace TrustPayments\Payment\Model\Provider;

use Magento\Framework\Cache\FrontendInterface;
use TrustPayments\Payment\Model\ApiClient;
use TrustPayments\Sdk\Service\CurrencyService;

/**
 * Provider of currency information from the gateway.
 */
class CurrencyProvider extends AbstractProvider
{
    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param FrontendInterface $cache
     * @param ApiClient $apiClient
     */
    public function __construct(FrontendInterface $cache, ApiClient $apiClient)
    {
        parent::__construct($cache, 'trustpayments_payment_currencies',
            \TrustPayments\Sdk\Model\RestCurrency::class);
        $this->apiClient = $apiClient;
    }

    /**
     * Gets the currency by the given code.
     *
     * @param string $code
     * @return \TrustPayments\Sdk\Model\RestCurrency
     */
    public function find($code)
    {
        return parent::find($code);
    }

    /**
     * Gets a list of currencies.
     *
     * @return \TrustPayments\Sdk\Model\RestCurrency[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    protected function fetchData()
    {
        return $this->apiClient->getService(CurrencyService::class)->all();
    }

    protected function getId($entry)
    {
        /** @var \TrustPayments\Sdk\Model\RestCurrency $entry */
        return $entry->getCurrencyCode();
    }
}
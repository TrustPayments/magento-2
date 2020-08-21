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
namespace TrustPayments\Payment\Model\Provider;

use Magento\Framework\Cache\FrontendInterface;
use TrustPayments\Payment\Model\ApiClient;
use TrustPayments\Sdk\Service\LabelDescriptionService;

/**
 * Provider of label descriptor information from the gateway.
 */
class LabelDescriptorProvider extends AbstractProvider
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
        parent::__construct($cache, 'trustpayments_payment_label_descriptors',
            \TrustPayments\Sdk\Model\LabelDescriptor::class);
        $this->apiClient = $apiClient;
    }

    /**
     * Gets the label descriptor by the given id.
     *
     * @param int $id
     * @return \TrustPayments\Sdk\Model\LabelDescriptor
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * Gets a list of label descriptors.
     *
     * @return \TrustPayments\Sdk\Model\LabelDescriptor[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    protected function fetchData()
    {
        return $this->apiClient->getService(LabelDescriptionService::class)->all();
    }

    protected function getId($entry)
    {
        /** @var \TrustPayments\Sdk\Model\LabelDescriptor $entry */
        return $entry->getId();
    }
}
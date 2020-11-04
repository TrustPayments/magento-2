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
use TrustPayments\Sdk\Service\LabelDescriptionGroupService;

/**
 * Provider of label descriptor group information from the gateway.
 */
class LabelDescriptorGroupProvider extends AbstractProvider
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
        parent::__construct($cache, 'trustpayments_payment_label_descriptor_groups',
            \TrustPayments\Sdk\Model\LabelDescriptorGroup::class);
        $this->apiClient = $apiClient;
    }

    /**
     * Gets the label descriptor group by the given id.
     *
     * @param int $id
     * @return \TrustPayments\Sdk\Model\LabelDescriptorGroup
     */
    public function find($id)
    {
        return parent::find($id);
    }

    /**
     * Gets a list of label descriptor groups.
     *
     * @return \TrustPayments\Sdk\Model\LabelDescriptorGroup[]
     */
    public function getAll()
    {
        return parent::getAll();
    }

    protected function fetchData()
    {
        return $this->apiClient->getService(LabelDescriptionGroupService::class)->all();
    }

    protected function getId($entry)
    {
        /** @var \TrustPayments\Sdk\Model\LabelDescriptorGroup $entry */
        return $entry->getId();
    }
}
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
use TrustPayments\Payment\Api\PaymentMethodConfigurationRepositoryInterface;
use TrustPayments\Payment\Api\TokenInfoManagementInterface;
use TrustPayments\Payment\Api\TokenInfoRepositoryInterface;
use TrustPayments\Payment\Api\Data\TokenInfoInterface;
use TrustPayments\Payment\Helper\Data as Helper;
use TrustPayments\Sdk\Model\CreationEntityState;
use TrustPayments\Sdk\Model\EntityQuery;
use TrustPayments\Sdk\Model\EntityQueryFilter;
use TrustPayments\Sdk\Model\EntityQueryFilterType;
use TrustPayments\Sdk\Model\TokenVersion;
use TrustPayments\Sdk\Model\TokenVersionState;
use TrustPayments\Sdk\Service\TokenService;
use TrustPayments\Sdk\Service\TokenVersionService;

/**
 * Token info management service.
 */
class TokenInfoManagement implements TokenInfoManagementInterface
{

    /**
     *
     * @var Helper
     */
    private $helper;

    /**
     *
     * @var TokenInfoRepositoryInterface
     */
    private $tokenInfoRepository;

    /**
     *
     * @var TokenInfoFactory
     */
    private $tokenInfoFactory;

    /**
     *
     * @var PaymentMethodConfigurationRepositoryInterface
     */
    private $paymentMethodConfigurationRepository;

    /**
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     *
     * @param Helper $helper
     * @param TokenInfoRepositoryInterface $tokenInfoRepository
     * @param TokenInfoFactory $tokenInfoFactory
     * @param PaymentMethodConfigurationRepositoryInterface $paymentMethodConfigurationRepository
     * @param ApiClient $apiClient
     */
    public function __construct(Helper $helper, TokenInfoRepositoryInterface $tokenInfoRepository,
        TokenInfoFactory $tokenInfoFactory,
        PaymentMethodConfigurationRepositoryInterface $paymentMethodConfigurationRepository, ApiClient $apiClient)
    {
        $this->helper = $helper;
        $this->tokenInfoRepository = $tokenInfoRepository;
        $this->tokenInfoFactory = $tokenInfoFactory;
        $this->paymentMethodConfigurationRepository = $paymentMethodConfigurationRepository;
        $this->apiClient = $apiClient;
    }

    public function updateTokenVersion($spaceId, $tokenVersionId)
    {
        $tokenVersion = $this->apiClient->getService(TokenVersionService::class)->read($spaceId, $tokenVersionId);
        $this->updateTokenVersionInfo($tokenVersion);
    }

    public function updateToken($spaceId, $tokenId)
    {
        $query = new EntityQuery();
        $filter = new EntityQueryFilter();
        $filter->setType(EntityQueryFilterType::_AND);
        $filter->setChildren(
            [
                $this->helper->createEntityFilter('token.id', $tokenId),
                $this->helper->createEntityFilter('state', TokenVersionState::ACTIVE)
            ]);
        $query->setFilter($filter);
        $query->setNumberOfEntities(1);
        $tokenVersions = $this->apiClient->getService(TokenVersionService::class)->search($spaceId, $query);
        if (! empty($tokenVersions)) {
            $this->updateTokenVersionInfo($tokenVersions[0]);
        } else {
            try {
                $tokenInfo = $this->tokenInfoRepository->getByTokenId($spaceId, $tokenId);
                $this->tokenInfoRepository->delete($tokenInfo);
            } catch (NoSuchEntityException $e) {}
        }
    }

    protected function updateTokenVersionInfo(TokenVersion $tokenVersion)
    {
        try {
            $tokenInfo = $this->tokenInfoRepository->getByTokenId($tokenVersion->getLinkedSpaceId(),
                $tokenVersion->getToken()
                    ->getId());
        } catch (NoSuchEntityException $e) {
            $tokenInfo = $this->tokenInfoFactory->create();
        }

        if (! \in_array($tokenVersion->getToken()->getState(),
            [
                CreationEntityState::ACTIVE,
                CreationEntityState::INACTIVE
            ])) {
            if ($tokenInfo->getId()) {
                $this->tokenInfoRepository->delete($tokenInfo);
            }
        } else {
            $tokenInfo->setData(TokenInfoInterface::CUSTOMER_ID, $tokenVersion->getToken()
                ->getCustomerId());
            $tokenInfo->setData(TokenInfoInterface::NAME, $tokenVersion->getName());
            try {
                $tokenInfo->setData(TokenInfoInterface::PAYMENT_METHOD_ID,
                    $this->paymentMethodConfigurationRepository->getByConfigurationId($tokenVersion->getLinkedSpaceId(),
                        $tokenVersion->getPaymentConnectorConfiguration()
                            ->getPaymentMethodConfiguration()
                            ->getId())
                        ->getId());
                $tokenInfo->setData(TokenInfoInterface::CONNECTOR_ID,
                    $tokenVersion->getPaymentConnectorConfiguration()
                        ->getId());
            } catch (\Error $e) { //Catching, but not showing, ticket WAL-69414
                $error = $e;
            }

            $tokenInfo->setData(TokenInfoInterface::SPACE_ID, $tokenVersion->getLinkedSpaceId());
            $tokenInfo->setData(TokenInfoInterface::STATE, $tokenVersion->getToken()
                ->getState());
            $tokenInfo->setData(TokenInfoInterface::TOKEN_ID, $tokenVersion->getToken()
                ->getId());
            $this->tokenInfoRepository->save($tokenInfo);
        }
    }

    public function deleteToken(TokenInfo $token)
    {
        $this->apiClient->getService(TokenService::class)->delete($token->getSpaceId(), $token->getTokenId());
        $this->tokenInfoRepository->delete($token);
    }
}
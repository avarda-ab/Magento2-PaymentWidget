<?php

namespace Avarda\PaymentWidget\Model;

use Avarda\PaymentWidget\Helper\ConfigHelper;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\FlagManager;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Store\Model\StoreManagerInterface;

class GetPaymentWidget
{
    const FLAG_KEY = 'avarda_payment_widget';

    protected AvardaClient $avardaClient;
    protected ConfigHelper $configHelper;
    protected FlagManager $flagManager;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        AvardaClient $avardaClient,
        ConfigHelper $configHelper,
        FlagManager $flagManager,
        StoreManagerInterface $storeManager,
    ) {
        $this->avardaClient = $avardaClient;
        $this->configHelper = $configHelper;
        $this->flagManager = $flagManager;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $jwtValid = $this->flagManager->getFlagData($this->getFlag('valid'));
        if (!$jwtValid || $jwtValid < time()) {
            try {
                $this->getNewJwtData();
            } catch (Exception|GuzzleException $e) {
                return [];
            }
        }

        return $this->flagManager->getFlagData($this->getFlag()) ?? [];
    }

    /**
     * @return void
     * @throws GuzzleException
     * @throws ClientException
     */
    public function getNewJwtData(): void
    {
        $url = $this->configHelper->getApiUrl() . 'api/paymentwidget/partner/init';
        $headers = $this->avardaClient->buildHeader();
        $response = $this->avardaClient->get($url, $headers);
        $responseArray = json_decode($response, true);
        $this->flagManager->saveFlag($this->getFlag(), $responseArray);
        $this->flagManager->saveFlag($this->getFlag('valid'), strtotime($responseArray['expiredUtc'] ?? 0));
    }

    protected function getFlag(string $suffix = ''): string
    {
        $storeCode = $this->storeManager->getStore()->getCode();
        $base = self::FLAG_KEY . '_' . $storeCode;
        return $suffix ? $base . '_' . $suffix : $base;
    }
}

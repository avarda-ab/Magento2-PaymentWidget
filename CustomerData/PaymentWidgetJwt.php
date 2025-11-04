<?php

namespace Avarda\PaymentWidget\CustomerData;

use Avarda\PaymentWidget\Model\GetPaymentWidget;
use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

class PaymentWidgetJwt implements SectionSourceInterface
{
    protected GetPaymentWidget $getPaymentWidget;
    protected StoreManagerInterface $storeManager;

    public function __construct(
        GetPaymentWidget $getPaymentWidget,
        StoreManagerInterface $storeManager,
    ) {
        $this->getPaymentWidget = $getPaymentWidget;
        $this->storeManager = $storeManager;
    }

    public function getSectionData(): array
    {
        $data = $this->getPaymentWidget->execute();
        $data['expiredUtc'] = strtotime($data['expiredUtc'] ?? 0);
        $data['storeCode'] = $this->storeManager->getStore()->getCode();
        return $data;
    }
}

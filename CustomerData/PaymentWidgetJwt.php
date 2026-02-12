<?php

namespace Avarda\PaymentWidget\CustomerData;

use Avarda\PaymentWidget\Helper\ConfigHelper;
use Avarda\PaymentWidget\Model\GetPaymentWidget;
use Magento\Customer\CustomerData\SectionSourceInterface;

class PaymentWidgetJwt implements SectionSourceInterface
{
    protected GetPaymentWidget $getPaymentWidget;
    protected ConfigHelper $configHelper;

    public function __construct(
        GetPaymentWidget $getPaymentWidget,
        ConfigHelper $configHelper,
    ) {
        $this->getPaymentWidget = $getPaymentWidget;
        $this->configHelper = $configHelper;
    }

    public function getSectionData(): array
    {
        $data = $this->getPaymentWidget->execute();
        $data['expiredUtc'] = strtotime($data['expiredUtc'] ?? 0);
        $data['storeCode'] = $this->configHelper->getStoreCode();
        return $data;
    }
}

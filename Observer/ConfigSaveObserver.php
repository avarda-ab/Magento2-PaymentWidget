<?php
/**
 * @copyright Copyright © Avarda. All rights reserved.
 * @package   Avarda_CustomerInvoices
 */

namespace Avarda\PaymentWidget\Observer;

use Avarda\PaymentWidget\Helper\ConfigHelper;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\FlagManager;

class ConfigSaveObserver implements ObserverInterface
{
    protected FlagManager $flagManager;
    protected ConfigHelper $configHelper;

    public function __construct(
        FlagManager $flagManager,
        ConfigHelper $configHelper
    ) {
        $this->flagManager = $flagManager;
        $this->configHelper = $configHelper;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $paths = $observer->getEvent()->getData('changed_paths');
        $changed = [
            'avarda_payments/api/test_mode',
            'avarda_payments/api/client_secret',
            'avarda_payments/api/client_id',
            'payment/avarda_checkout3_checkout/test_mode',
            'payment/avarda_checkout3_checkout/client_secret',
            'payment/avarda_checkout3_checkout/client_id'
        ];
        $hasChanged = false;
        foreach ($changed as $item) {
            if (in_array($item, $paths)) {
                $hasChanged = true;
                break;
            }
        }
        // If api keys or api url is changed remove current api token data
        if ($hasChanged) {
            $store = $observer->getEvent()->getStore();

            // If we changed for a specific store only delete flags for that store. else delete all flags.
            if ($store) {
                $this->flagManager->deleteFlag($this->configHelper->getTokenFlagKey());
                $this->flagManager->deleteFlag($this->configHelper->getTokenValidFlagKey());
                return;
            }

            $this->configHelper->deleteAllTokenFlags();
        }
    }
}

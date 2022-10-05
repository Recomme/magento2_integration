<?php
namespace Recomme\PurchaseIntegration\Plugin;

use Magento\Sales\Model\ResourceModel\Order;

class OrderStatePlugin
{
    protected $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function afterSave(Order $subject, $result, $object) {
        $statuses = $this->scopeConfig->getValue('recomme_api/general/statuses', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $statusesArray = explode(',', $statuses);

        $rcrCode = $this->getRcrCodeFromComments($object->getStatusHistoryCollection());
        $newStatus = $object->getData('status');
        if(in_array($newStatus, $statusesArray)){
            $purchaseData = [
                'user_agent' => !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "",
                'timestamp' => time(),
                'ref_code' => $rcrCode,
                'order_timestamp' => $object->getData('created_at'),
                'last_name' => $object->getData('customer_lastname'),
                'invoice_amount' => $object->getData('base_subtotal_incl_tax'),
                'first_name' => $object->getData('customer_firstname'),
                'external_reference_id' => $object->getData('increment_id'),
                'email' => $object->getData('customer_email'),
                'currency_code' => $object->getData('base_currency_code'),
                'browser_ip' => !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : "",
                'discount_coupon' => $object->getCouponCode()
            ];
            
            $this->sendOrder($purchaseData);
        }
    }

    private function getRcrCodeFromComments($statusHistoryCollection) {
        foreach($statusHistoryCollection as $statusHistoryItem) {
            $comment = $statusHistoryItem->getComment();
            if(strpos($comment, 'recomme:') !== false) {
                return trim(str_replace('recomme:', '', $comment));
            }
        }
        return '';
    }

    private function sendOrder($purchaseData)
    {   
        $apiKey = $this->scopeConfig->getValue('recomme_api/general/api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $customerKey = $this->scopeConfig->getValue('recomme_api/general/customer_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $baseApiUrl = 'https://api.recomme.io';

        $bearerToken = "Authorization: Bearer " . $apiKey;
        $customer = "Customer: " . $customerKey;
        $endpoint = join('/', [$baseApiUrl, 'purchase']);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_HTTPHEADER => ['Content-Type: application/json', $bearerToken, $customer],
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $endpoint,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($purchaseData),
        ));

        if ($response = curl_exec($curl)) {
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        }

        curl_close($curl);
    }
}
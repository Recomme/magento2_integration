<?php
    namespace Recomme\PurchaseIntegration\Observer;

    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\Event\Observer;

    class OrderPlaceAfter implements ObserverInterface
    {
        protected $orderRepository;
        protected $cookieManager;

        /**
         * @param \Psr\Log\LoggerInterface $_logger
         */

        public function __construct(
            \Magento\Sales\Model\OrderRepository $orderRepository,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        ){
            $this->orderRepository = $orderRepository;
            $this->cookieManager = $cookieManager;
        }


        public function execute(Observer $observer)
        {
            $order = $observer->getEvent()->getOrder();
            $recommeCookie = $this->cookieManager->getCookie('recomme_r_code');
            if($recommeCookie) {
                $order->addCommentToStatusHistory('recomme: ' . $recommeCookie);
                $this->orderRepository->save($order);
            }
            return;

        }
    }
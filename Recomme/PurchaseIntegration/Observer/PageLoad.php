<?php
    namespace Recomme\PurchaseIntegration\Observer;

    use Magento\Framework\Event\ObserverInterface;
    use Magento\Framework\Event\Observer;

    class PageLoad implements ObserverInterface
    {
        protected $cookieManager;
        protected $cookieMetadataFactory;
        private $request;

        /**
         * @param \Psr\Log\LoggerInterface $_logger
         */

        public function __construct(
            \Magento\Framework\App\Request\Http $request,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
        ){
            $this->request = $request;
            $this->cookieManager = $cookieManager;
            $this->cookieMetadataFactory = $cookieMetadataFactory;
        }


        public function execute(Observer $observer)
        {
            $rcrCode = $this->request->getParam('rcr');
            if($rcrCode) {
                $this->setPublicCookie('recomme_r_code', $rcrCode);
            }
            return;

        }

        /**
        * set public cookie
        */
        public function setPublicCookie($cookieName, $value) {

            $metadata = $this->cookieMetadataFactory
                ->createPublicCookieMetadata()
                ->setDuration(86400) // Cookie will expire after one day (86400 seconds)
                ->setSecure(true) //the cookie is only available under HTTPS
                ->setPath('/')// The cookie will be available to all pages and subdirectories within the /subfolder path
                ->setHttpOnly(false); // cookies can be accessed by JS
        
            $this->cookieManager->setPublicCookie(
                $cookieName,
                $value,
                $metadata
            );
        }
    }
# Recomme plugin for Magento 2.*
Plugin for Magento 2 provides integration for basic recommendation process.

# How to install?
1. Dowload this repository
2. Place `Recomme` folder within your magento `app/code` directory
3. Run plugin installation comand ` php bin/magento module:enable Recomme_PurchaseIntegration`
4. Run `php bin/magento setup:upgrade` command
5. If you are in non-developer mode (eg. for production) - run `php bin/magento setup:static-content:deploy` command
6. Sometimes you may need to clear cache with `php bin/magento cache:clean`

# How to configure?
1. Grab `customer_key` and `api_key` from your Recomme admin panel (you need to add new integration under Integrations menu item)
2. Open your Magento admin panel
3. Go to `Stores` -> `Configuration`
4. You should fill Api Key, Customer Key
5. Then pick statuses that will cause order to be counted as successfull recommendation (use CTRL or Command key to select multiple)
(see screenshot below)
<img width="1555" alt="image" src="https://user-images.githubusercontent.com/6068311/194130877-04b64732-8cb8-4bac-9652-b61e903da55d.png">


That's it! Youre good to go with scaling up your sales!
 
![giphy](https://user-images.githubusercontent.com/6068311/194131663-fab21b8b-c29e-45d3-a28f-b1fe09c1531a.gif)




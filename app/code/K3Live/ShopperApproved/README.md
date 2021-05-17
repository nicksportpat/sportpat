# k3live_shopperapproved-m2
=============

# Magento2 Integration with ShopperApproved

## Installation

### Step 1

#### Using Composer
composer require k3live/shopperapproved-m2

#### Manually
Download the extension
Unzip the file contents to a local folder
Create a folder on the server {Magento 2 root}/app/code/K3Live/ShopperApproved
Copy the content from the local folder to the newly created folder on server

### Step 2 - Enable ShopperApproved ("cd" to {Magento root} folder)
  php -f bin/magento module:enable --clear-static-content K3Live_ShopperApproved
  
  php -f bin/magento setup:upgrade

### Step 3 - Configure Shopper Approved
Log into your Magento 2 Admin, then goto Stores -> Configuration -> K3Live -> ShopperApproved
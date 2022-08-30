# SaaS Data Management

This module adds an option to the Commerce Services Connector section of the Magento admin to clear catalog data used in Live Search and Product Recommencations.

The module is included in GXD managed demo environments in versions 2.4.3-p1+, but can be added manually to any installation.

### Usage
Reset is available under Stores->Configuration->Services->Saas Catalog Reset. You will need to have your API Keys and Data Space selected first. You will need to unassign the data space on other sites that are currently using the data space you wish to clear

### Installing on environments other than GXD managed Commerce Cloud

To add the Module:
`composer config repositories.saasmanagement git https://github.com/PMET-public/module-sass-data-management.git;composer require magentoese/module-saas-data-management:dev-master` then run `bin/magento setup:upgrade`

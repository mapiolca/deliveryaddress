# Delivery address - Dolibarr mod

This module allows you to add the delivery address of your contact in your customer/supplier order PDF.

## Requirements

Before installing this module, install abricot. Abricot is a collection of specific ATM functions and classes.
Install version dolibarr_module_abricot-3.1 or higher.
https://github.com/ATM-Consulting/dolibarr_module_abricot


## Display

To display a delivery address on the customer or supplier order pdf, add "Customer shipping contact" to the Contacts/Addresses tab.

## Substitution variables

The module provides substitution variables for the first external and internal contact with code `SHIPPING` linked to the current object.
Each variable is replaced with an empty value when the contact or field is missing.

- `__DELIVERYADDRESS_EXTERNAL_SHIPPING_FULLNAME__`
- `__DELIVERYADDRESS_EXTERNAL_SHIPPING_FULLADDRESS__`
- `__DELIVERYADDRESS_EXTERNAL_SHIPPING_PHONE__`
- `__DELIVERYADDRESS_EXTERNAL_SHIPPING_EMAIL__`
- `__DELIVERYADDRESS_INTERNAL_SHIPPING_FULLNAME__`
- `__DELIVERYADDRESS_INTERNAL_SHIPPING_FULLADDRESS__`
- `__DELIVERYADDRESS_INTERNAL_SHIPPING_PHONE__`
- `__DELIVERYADDRESS_INTERNAL_SHIPPING_EMAIL__`

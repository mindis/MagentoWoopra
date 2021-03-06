<?php
/**
 * Woopra Module for Magento
 *
 * @package     Woopra_Analytics
 * @author      K3Live for Woopra
 * @copyright   Copyright (c) 2013 Woopra (http://www.woopra.com/)
 * @license     Open Software License (OSL 3.0)
 */

class Woopra_Analytics_Model_Observer extends Varien_Event_Observer
{
    public function newsletterSubscriberChange(Varien_Event_Observer $observer)
    {
        if (Mage::helper('woopra')->getNewsletterSubscribed() != NULL) {
            $event = $observer->getEvent();
            $model = $event->getSubscriber();
            $subscriber_email = $model->getData('subscriber_email');
            if ($model->getIsStatusChanged() == 1 && $model->getData(subscriber_status) == 1) {
                Mage::getSingleton('core/session')->setData('woopra_subscriber_changed', 1);
                Mage::getSingleton('core/session')->setData('woopra_subscriber_status', Mage::helper('woopra')->getNewsletterSubscribed());
                Mage::getSingleton('core/session')->setData('woopra_subscriber_email', Mage::helper('core')->escapeHtml(addslashes($subscriber_email)));
            } else if ($model->getIsStatusChanged() == 1 && $model->getData(subscriber_status) == 3) {
                Mage::getSingleton('core/session')->setData('woopra_subscriber_changed', 1);
                Mage::getSingleton('core/session')->setData('woopra_subscriber_status', Mage::helper('woopra')->getNewsletterUnsubscribed());
                Mage::getSingleton('core/session')->setData('woopra_subscriber_email', Mage::helper('core')->escapeHtml(addslashes($subscriber_email)));
            } else {
            }
        }
    }

    public function controllerActionBefore(Varien_Event_Observer $observer)
    {
        //Mage::log($observer->getEvent()->getControllerAction()->getFullActionName(), null, 'woopra.log');

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_loginPost' && Mage::helper('woopra')->getCustomerLogin() != NULL)
        {
            Mage::getSingleton('core/session')->setData('woopra_login_logout_trigger', 1);
            Mage::getSingleton('core/session')->setData('woopra_login_logout_status', Mage::helper('woopra')->getCustomerLogin());
        }

    	if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_logoutSuccess' && Mage::helper('woopra')->getCustomerLogout() != NULL)
    	{
    		Mage::getSingleton('core/session')->setData('woopra_login_logout_trigger', 1);
            Mage::getSingleton('core/session')->setData('woopra_login_logout_status', Mage::helper('woopra')->getCustomerLogout());
    	}

    	if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'contacts_index_post' && Mage::helper('woopra')->getContactFormSent() != NULL)
    	{
    		$request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                Mage::getSingleton('core/session')->setData('woopra_contacts_index_post', 1);
                Mage::getSingleton('core/session')->setData('woopra_contacts_name', Mage::helper('core')->escapeHtml(addslashes($request['name'])));
                Mage::getSingleton('core/session')->setData('woopra_contacts_email', Mage::helper('core')->escapeHtml(addslashes($request['email'])));
                Mage::getSingleton('core/session')->setData('woopra_contacts_telephone', Mage::helper('core')->escapeHtml(addslashes($request['telephone'])));
                Mage::getSingleton('core/session')->setData('woopra_contacts_comment', Mage::helper('core')->escapeHtml(addslashes($request['comment'])));
                //Mage::log($request['name']." | ".$request['email']." | ".$request['telephone']." | ".$request['comment'], null, 'woopra.log');
            }
    	}
        
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_add' && Mage::helper('woopra')->getProductAddedToCart() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['product'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductAddedToCart());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_delete' && Mage::helper('woopra')->getProductRemovedFromCart() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product_id = Mage::getModel('checkout/cart')->getQuote()->getItemById($request['id'])->getProduct()->getId();
                $product = Mage::getModel('catalog/product')->load($product_id)->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductRemovedFromCart());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }
        
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'wishlist_index_add' && Mage::helper('woopra')->getProductAddedToWishlist() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['product'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductAddedToWishlist());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'wishlist_index_remove' && Mage::helper('woopra')->getProductRemovedFromWishlist() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product_id = Mage::getModel('wishlist/item')->load($request['item'])->getProduct()->getId();
                $product = Mage::getModel('catalog/product')->load($product_id)->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductRemovedFromWishlist());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', $product['name']);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', $product['sku']);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'catalog_product_compare_add' && Mage::helper('woopra')->getProductAddedToCompare() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['product'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductAddedToCompare());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'catalog_product_compare_remove' && Mage::helper('woopra')->getProductRemovedFromCompare() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['product'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductRemovedFromCompare());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', $product['name']);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', $product['sku']);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_index' && Mage::helper('woopra')->getCheckoutBillingAddress() != NULL)
        {
            Mage::getSingleton('core/session')->setData('woopra_checkout_trigger', 1);
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_savePayment' && Mage::helper('woopra')->getCheckoutPaymentMethod() != NULL)
        {
        	$request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            Mage::getSingleton('core/session')->setData('woopra_checkout_trigger', 1);
            Mage::getSingleton('core/session')->setData('woopra_checkout_payment_method', $request['payment']['method']);
            if ($request['payment']['method'] == 'ccsave')
            {
            	Mage::getSingleton('core/session')->setData('woopra_checkout_payment_cc_type', $request['payment']['cc_type']);
            }
            //Mage::log($request['payment']['cc_type'], null, 'woopra.log');
        }

        if (($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_success' || $observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_multishipping_success') && Mage::helper('woopra')->getCheckoutSuccess() != NULL)
        {
            $lastOrderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
            if ($lastOrderId) {
                $order = Mage::getModel('sales/order')->loadByIncrementId($lastOrderId);
                $cost = 0;
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_coupon_code', addslashes($order->getCouponCode()));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_discount_amount', round($order->getDiscountAmount(), 2));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_order_id', addslashes($lastOrderId));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_order_subtotal', round($order->getSubtotal(), 2));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_order_total', round($order->getGrandTotal(), 2));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_order_weight', round($order->getWeight(), 2));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_shipping_amount', round($order->getShippingAmount(), 2));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_shipping_description', addslashes($order->getShippingDescription()));
                Mage::getSingleton('core/session')->setData('woopra_checkout_success_total_items_ordered', round($order->getTotalQtyOrdered(), 0));
                $items = $order->getAllVisibleItems();
                foreach ($items as $itemId => $item)
                {
                    $cost = $cost + Mage::getModel('catalog/product')->load($item->getProductId())->getData('cost');
                }
                $profit = $order->getSubtotal() - $cost;
                if ($cost != 0)
                {
                    Mage::getSingleton('core/session')->setData('woopra_checkout_success_profit', round($profit, 2));
                }
                //Mage::log($order, null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'catalogsearch_result_index' && Mage::helper('woopra')->getCatalogSearch() != NULL)
        {
        	$request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                Mage::getSingleton('core/session')->setData('woopra_search_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_search_keywords', Mage::helper('core')->escapeHtml(addslashes($request['q'])));
                //Mage::log($request['q'], null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'catalogsearch_advanced_result' && Mage::helper('woopra')->getCatalogSearch() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                foreach ($request as $key => $answer) {
                    if (is_array($answer) == true) {
                        foreach ($answer as $subkey => $subanswer) {
                            $subtotal = $subtotal." ".$subkey.":".$subanswer;
                            $answer = $subtotal;
                        }
                        $subtotal = NULL;
                    }
                    $search_keywords = $search_keywords." | ".$key.": ".$answer;
                }
                Mage::getSingleton('core/session')->setData('woopra_search_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_search_keywords', Mage::helper('core')->escapeHtml(addslashes($search_keywords)));
                //Mage::log($search_keywords, null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'review_product_list' && Mage::helper('woopra')->getProductReviewRead() != NULL)
        {
        	$request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['id'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductReviewRead());
        		Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
    			Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
    			Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
    			//Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2), null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'review_product_post' && Mage::helper('woopra')->getProductReviewPosted() != NULL)
        {
        	$request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['id'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductReviewPosted());
        		Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
    			Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
    			Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                Mage::getSingleton('core/session')->setData('woopra_product_review_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_product_review_nickname', Mage::helper('core')->escapeHtml(addslashes($request['nickname'])));
                Mage::getSingleton('core/session')->setData('woopra_product_review_title', Mage::helper('core')->escapeHtml(addslashes($request['title'])));
                Mage::getSingleton('core/session')->setData('woopra_product_review_detail', Mage::helper('core')->escapeHtml(addslashes($request['detail'])));
    			//Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2)." | ".$request['nickname']." | ".$request['title']." | ".$request['detail'], null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_forgotpasswordpost' && Mage::helper('woopra')->getForgotPassword() != NULL)
        {
        	$request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            	Mage::getSingleton('core/session')->setData('woopra_forgot_password_trigger', 1);
            if ($request) {
                Mage::getSingleton('core/session')->setData('woopra_forgot_password_email', Mage::helper('core')->escapeHtml(addslashes($request['email'])));
                //Mage::log($request['email'], null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_editPost' && Mage::helper('woopra')->getChangedPassword() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request['change_password'] == 1 && $request['current_password'] != $request['password']) {
                Mage::getSingleton('core/session')->setData('woopra_password_changed_trigger', 1);
            }
            //Mage::log($request, null, 'woopra.log');
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'tag_index_save' && Mage::helper('woopra')->getProductTagAdded() != NULL) 
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['product'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_status', Mage::helper('woopra')->getProductTagAdded());
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_name', addslashes($product['name']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_sku', addslashes($product['sku']));
                Mage::getSingleton('core/session')->setData('woopra_cart_wishlist_price', round($product['price'], 2));
                Mage::getSingleton('core/session')->setData('woopra_product_tag_added_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_product_tag_name', Mage::helper('core')->escapeHtml(addslashes($request['productTagName'])));
                //Mage::log($product['name']." | ".$product['sku']." | ".round($product['price'], 2)." | ".$request['productTagName'], null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_couponPost' && Mage::helper('woopra')->getCouponCodeAdded() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $coupon_id = Mage::getModel('salesrule/coupon')->load($request['coupon_code'], 'code');
                Mage::getSingleton('core/session')->setData('woopra_coupon_code_trigger', 1);
                if ($request['remove'] == 1) {
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_status', Mage::helper('woopra')->getCouponCodeRemoved());
                } else {
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_status', Mage::helper('woopra')->getCouponCodeAdded());
                //Mage::log($coupon_id->getData(), null, 'woopra.log');
                }
                if ($coupon_id['coupon_id'] != null) {
                    $coupon_rules = Mage::getModel('salesrule/rule')->load($coupon_id->getRuleId());
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_validity', 'valid');
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_name', addslashes($coupon_rules->getData('name')));
                    //Mage::log($coupon_rules->getData(), null, 'woopra.log');
                    if ($coupon_rules->getData('is_active') == 1) {
                        Mage::getSingleton('core/session')->setData('woopra_coupon_code_active', 'active');
                        //Mage::log($coupon_rules->getData('is_active').' | = active', null, 'woopra.log');
                    } else {
                        Mage::getSingleton('core/session')->setData('woopra_coupon_code_active', 'inactive');
                        //Mage::log($coupon_rules->getData('is_active').' | = inactive', null, 'woopra.log');
                    }   
                } else {
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_validity', 'invalid');
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_name', '-');
                    Mage::getSingleton('core/session')->setData('woopra_coupon_code_active', '-');
                }
                Mage::getSingleton('core/session')->setData('woopra_coupon_code', Mage::helper('core')->escapeHtml(addslashes($request['coupon_code'])));
                //Mage::log($observer, null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_create' && Mage::helper('woopra')->getCustomerCreateAccount() != NULL)
        {
            Mage::getSingleton('core/session')->setData('woopra_create_account_trigger', 1);
            //Mage::log('Create account started.', null, 'woopra.log');
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'customer_account_createpost' && Mage::helper('woopra')->getCustomerCreateAccount() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            Mage::getSingleton('core/session')->setData('woopra_create_account_success_trigger', 1);
            if ($request['is_subscribed'] == 1) {
                Mage::getSingleton('core/session')->setData('woopra_subscriber_changed', 1);
                Mage::getSingleton('core/session')->setData('woopra_subscriber_status', Mage::helper('woopra')->getNewsletterSubscribed());
                Mage::getSingleton('core/session')->setData('woopra_subscriber_email', Mage::helper('core')->escapeHtml(addslashes($request[email])));
            }
            //Mage::log($request, null, 'woopra.log');
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_cart_estimatePost' && Mage::helper('woopra')->getEstimatePost() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $region = Mage::getModel('directory/region')->load($request['region_id']);
                Mage::getSingleton('core/session')->setData('woopra_estimate_post_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_estimate_post_country', $request['country_id']);
                Mage::getSingleton('core/session')->setData('woopra_estimate_post_state', $region->getName());
                Mage::getSingleton('core/session')->setData('woopra_estimate_post_zip', Mage::helper('core')->escapeHtml($request['estimate_postcode']));
                //Mage::log($request['country_id']." | ".$region->getName()." | ".$request['estimate_postcode'], null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'cms_index_noRoute' && Mage::helper('woopra')->getCmsNoRoute() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getOriginalPathInfo();
            Mage::getSingleton('core/session')->setData('woopra_cms_noroute_trigger', 1);
            if ($request) {
                Mage::getSingleton('core/session')->setData('woopra_cms_noroute_path', $request);
                //Mage::log($request, null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'poll_vote_add' && Mage::helper('woopra')->getPollVote() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $poll_title = Mage::getModel('poll/poll')->load($request['poll_id'])->getData();
                $poll_answers = Mage::getResourceModel('poll/poll_answer_collection')->addPollFilter($request['poll_id'])->getData();
                foreach ($poll_answers as $key => $answer) {
                    if ($answer['answer_id'] == $request['vote']) {
                        $answer_title = $answer['answer_title'];
                    }
                }
                Mage::getSingleton('core/session')->setData('woopra_poll_vote_trigger', 1);
                Mage::getSingleton('core/session')->setData('woopra_poll_vote_title', addslashes($poll_title['poll_title']));
                Mage::getSingleton('core/session')->setData('woopra_poll_vote_answer', addslashes($answer_title));
                //Mage::log($poll_title['poll_title']." | ".$answer_title, null, 'woopra.log');
            }
        }

        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'sendfriend_product_sendmail' && Mage::helper('woopra')->getProductEmailToFriend() != NULL)
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                $product = Mage::getModel('catalog/product')->load($request['id'])->getData();
                Mage::getSingleton('core/session')->setData('woopra_sendfriend_product_name', addslashes($product['name']));
                Mage::getSingleton('core/session')->setData('woopra_sendfriend_product_sku', addslashes($product['sku']));
                Mage::getSingleton('core/session')->setData('woopra_sendfriend_product_price', round($product['price'], 2));
                Mage::getSingleton('core/session')->setData('woopra_sendfriend_product_trigger', 1);
                //Mage::log($request, null, 'woopra.log');
            }
        }

        /* Payment Status Coming Soon
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_saveOrder')
        {
            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            if ($request) {
                Mage::log($request, null, 'woopra.log');
            }
        }*/

        /* Send tracking events during checkout using HTTP Tracking API - Coming One Day(TM)
        if ($observer->getEvent()->getControllerAction()->getFullActionName() == 'checkout_onepage_progress')
        {
            function sendTracking($event) {
                $customer = Mage::getSingleton('customer/session');
                $visitor = Mage::getSingleton('core/session')->getVisitorData();
                $url = 'host='.Mage::helper('woopra')->getHostname();
                $url .= '&cookie='.$visitor[session_id];
                $url .= '&ip='.$visitor[http_host];
                $url .= '&browser='.$visitor[http_user_agent];
                $url .= '&event='.$event;
                $url .= '&cv_name='.$customer->getName();
                $url .= '&cv_email='.$customer->getEmail();
                //Mage::log(urlencode($url), null, 'woopra.log');
                $curl = new Varien_Http_Adapter_Curl();
                $curl->setConfig(array('timeout' => 10));
                $curl->write(Zend_Http_Client::GET, 'http://www.woopra.com/track/ce/?'.urlencode($url));
                //Mage::log($curl->read(), null, 'woopra.log');
            }

            $request = $observer->getEvent()->getControllerAction()->getRequest()->getParams();
            switch ($request[toStep]) {
                case 'checkout_onepage_index':
                    Mage::log("BILLING", null, 'woopra.log');
                    sendTracking("checkout_billing_address");
                    break;
                case 'billing':
                    Mage::log("BILLING", null, 'woopra.log');
                    sendTracking("checkout_billing_address");
                    break;
                case 'shipping':
                    Mage::log("SHIPPING", null, 'woopra.log');
                    sendTracking("checkout_shipping_address");
                    break;
                case 'shipping_method':
                    Mage::log("SHIPPING METHOD", null, 'woopra.log');
                    sendTracking("checkout_shipping_method");
                    break;
                case 'payment':
                    Mage::log("PAYMENT", null, 'woopra.log');
                    sendTracking("checkout_payment_method");
                    break;
                case 'review':
                    Mage::log("REVIEW", null, 'woopra.log');
                    sendTracking("checkout_review");
                    break;
            }
        }*/
    }
}
?>
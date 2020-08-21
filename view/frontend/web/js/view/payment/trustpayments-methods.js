/**
 * Trust Payments Magento 2
 *
 * This Magento 2 extension enables to process payments with Trust Payments (https://www.trustpayments.com//).
 *
 * @package TrustPayments_Payment
 * @author customweb GmbH (http://www.customweb.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
define([
	'jquery',
	'uiComponent',
	'Magento_Checkout/js/model/payment/renderer-list'
], function(
	$,
	Component,
	rendererList
) {
	'use strict';
	
	// Loads the Trust Payments Javascript File
	if (window.checkoutConfig.trustpayments.javascriptUrl) {
		$.getScript(window.checkoutConfig.trustpayments.javascriptUrl);
	}
	
	// Loads the Trust Payments Lightbox File
	if (window.checkoutConfig.trustpayments.lightboxUrl) {
		$.getScript(window.checkoutConfig.trustpayments.lightboxUrl);
	}
	
	// Registers the Trust Payments payment methods
	$.each(window.checkoutConfig.payment, function(code){
		if (code.indexOf('trustpayments_payment_') === 0) {
			rendererList.push({
			    type: code,
			    component: 'TrustPayments_Payment/js/view/payment/method-renderer/trustpayments-method'
			});
		}
	});
	
	return Component.extend({});
});
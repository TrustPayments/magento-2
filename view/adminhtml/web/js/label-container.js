/**
 * Trust Payments Magento 2
 *
 * This Magento 2 extension enables to process payments with Trust Payments (https://www.trustpayments.com//).
 *
 * @package TrustPayments_Payment
 * @author wallee AG (http://www.wallee.com/)
 * @license http://www.apache.org/licenses/LICENSE-2.0  Apache Software License (ASL 2.0)
 */
require([
    'jquery',
], function ($) {
	$(function () {
		$('.trustpayments-label-container').each(function(){
			var container = $(this),
			
				toggleTable = function(){
					container.toggleClass('active');
				};
			
			container.find('> a').on('click', toggleTable);
		});
	});
});
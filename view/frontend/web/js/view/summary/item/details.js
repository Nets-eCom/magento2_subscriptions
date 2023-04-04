/**jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'uiComponent'
    ],
    function (Component) {
        "use strict";
        var quoteItemData = window.checkoutConfig.quoteItemData;
        var netsSubscription = window.checkoutConfig.subscription;
        return Component.extend({
            defaults: {
                template: 'Dibs_EasyCheckout/summary/item/details'
            },
            quoteItemData: quoteItemData,
            getValue: function(quoteItem) {
                return quoteItem.name;
            },
            getItem: function(item_id) {
                var itemElement = null;
                _.each(this.quoteItemData, function(element, index) {
                    if (element.item_id == item_id) {
                        itemElement = element;
                    }
                });
                return itemElement;
            },
            getNetsSubscription: function() {
                return netsSubscription;
            },
            getNetsSubscriptionFrequency : function() {
                return netsSubscription.interval;
            },
            getNetsSubscriptionStart : function() {
                return netsSubscription.startDate;
            },
            getNetsSubscriptionEnd : function() {
                return netsSubscription.endDate;
            }
        });
    }
);
/*define(['jquery'], function ($) {
    var productAttributeShowHide = {
        showHideAttr: function () {
            var action = $('[name="product[nets_interval]"]').val();
            var action1 = $('[name="product[nets_sub_interval]"]').val();
            var action2 = $('[name="product[nets_sub_interval_time]"]').val();
            var expiresAfter = action + " " + action1 + " "+ action2;
	    jQuery('[name="product[subscription_expires_after]"]').val(expiresAfter);
            //if (action==7) {
				//this.showFields('div[data-index="nets_sub_interval"]');
            //} else {
                //this.hideFields('div[data-index="nets_sub_interval"]');
           // }
        },

        hideFields: function (names) {
            $(names).toggle(false);
        },

        showFields: function (names) {
            $(names).toggle(true);
        }
    };
    return productAttributeShowHide;
});*/
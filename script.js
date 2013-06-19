jQuery(function(){

jQuery('#eshop_count').change(function(){
    var count = jQuery('#eshop_count option:selected').val();
    var usd = (jQuery('#eshop_price_usd').data('unitprice') * count).toFixed(2);
    jQuery('#eshop_price_usd').text(usd);
    var eur = (jQuery('#eshop_price_eur').data('unitprice') * count).toFixed(2);
    jQuery('#eshop_price_eur').text(eur);
    var btc = (jQuery('#eshop_price_btc').data('unitprice') * count).toFixed(3);
    jQuery('#eshop_price_btc').text(btc);
    jQuery('#eshop_total').val(btc);
});

});

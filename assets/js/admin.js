(function ($) {
	"use strict";

	/**
	 * OrderShield Admin JS
	 */
	$.orderShield = $.orderShield || {};

    $(document).ready(function () {
		$.orderShield.init();

        var qVars = $.orderShield.get_query_vars("page");
		if (qVars != undefined) {
			if (qVars.indexOf("order-shield") >= 0) {
				var cSettingsTab = qVars.split("#");
				$(
					'.order-shield-settings-menu li[data-tab="' +
						cSettingsTab[1] +
						'"]'
				).trigger("click");				
			}
		}

		if( $('#disable_limit_per_order').is( ':checked' ) ) {
			$('.limit_per_order_area').hide();
			$('.max_qty_per_order_area').hide();
    	}

		$(document).on('click', '#order-shield-manage-stock', function() {	
			if($(this).is(':checked')){
				$(".order-shield-enable-area").show();          
			} else {
				$(".order-shield-enable-area").hide();
			}
		});
		
		if( $('#order-shield-manage-stock').is(':checked') ) {
			$(".order-shield-enable-area").show();          
		} else {
			$(".order-shield-enable-area").hide();
		}

	});

    $.orderShield.init = function () {
		// $.orderShield.toggleFields();
		$.orderShield.bindEvents();
		// $.orderShield.initializeFields();
	};

    $.orderShield.bindEvents = function () {
      $(".order-shield-settings-menu li").on("click", function (e) {
        $.orderShield.settingsTab(this);
      });

      $('.order-shield-settings-button').removeClass('button');

      $(document).on( 'click', '#enable_otp', function(){
        if( $('#enable_otp').is( ':checked' ) ) {
          $('#order-shield-meta-sms_api_key').show();
        } else {
          $('#order-shield-meta-sms_api_key').hide();
        }   
      });	
      
      if( $('#enable_otp').is( ':checked' ) ) {
        $('#order-shield-meta-sms_api_key').show();
      } else {
        $('#order-shield-meta-sms_api_key').hide();
      }
    };

    $.orderShield.settingsTab = function (button) {
		var button = $(button),
			tabToGo = button.data("tab");

		button.addClass("active").siblings().removeClass("active");
		$("#order-shield-" + tabToGo)
			.addClass("active")
			.siblings()
			.removeClass("active");
		$('#order-shield_builder_id').val(tabToGo);	
	};

  $.orderShield.get_query_vars = function (name) {
		var vars = {};
		window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function (
			m,
			key,
			value
		) {
			vars[key] = value;
		});
		if (name != "") {
			return vars[name];
		}
		return vars;
	};

})(jQuery);
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

	});

    $.orderShield.init = function () {
		$.orderShield.bindEvents();
	};

    $.orderShield.bindEvents = function () {
      $(".order-shield-settings-menu li").on("click", function (e) {
        $.orderShield.settingsTab(this);
      });

      $('.order-shield-settings-button').removeClass('button');

      $(document).on( 'click', '#enable_otp', function(){
        if( $('#enable_otp').is( ':checked' ) ) {
          $('#order-shield-meta-sms_api_endpoint').show();
          $('#order-shield-meta-sms_api_key').show();
        } else {
          $('#order-shield-meta-sms_api_endpoint').hide();
          $('#order-shield-meta-sms_api_key').hide();
        }   
      });	
      
      if( $('#enable_otp').is( ':checked' ) ) {
		$('#order-shield-meta-sms_api_endpoint').show();
        $('#order-shield-meta-sms_api_key').show();
      } else {
        $('#order-shield-meta-sms_api_key').hide();
		$('#order-shield-meta-sms_api_endpoint').hide();
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
		$('#order_shield_builder_id').val(tabToGo);	
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

	$(document).on('click', '#license-submit', function(){
		let license_key = $("#ordershield_license_key").val();
		let $message = $('.order-shield-license-status');
		let that = $(this); 
		
		if(license_key !=''){
			that.html(order_shield.loader);
			that.prop("disabled",true);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: order_shield.ajax_url,
				data: {
					action: 'license_activate',
					security: order_shield.nonce,
					license_key: license_key
				},
				success: function(response, textStatus, jqXHR) {
					console.log('response==>', response)
					var statusCode = jqXHR.status;
					$message.removeClass('order-shield-license-status-success order-shield-license-status-error');
					if (statusCode === 200) {
						$message.addClass(response.class).text(response.message).show();
						that.html('');
						that.html(order_shield.activate);
						that.prop("disabled",false);
						setTimeout(function() {
							location.reload();
						}, 2000);
					} else {
						$message.addClass(response.class).text(response.message).show();
					}
				},
				error: function(jqXHR) {
					var response = jqXHR.responseJSON;
					$message.addClass(response.class).text(response.message).show();
					that.html('');
					that.html(order_shield.activate);
					that.prop("disabled",false);
				}
			});
		}

	});

	$(document).on('click', '#license-deactivate', function(){

		var confirmDeactivation = confirm('Are you sure you want to deactivate the license key?');
        if (!confirmDeactivation) {
            return;
        }


		let $message = $('.order-shield-license-status');
		let that = $(this); 
		
		that.html(order_shield.loader);
		that.prop("disabled",true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: order_shield.ajax_url,
			data: {
				action: 'license_deactivate',
				security: order_shield.nonce,
			},
			success: function(response, textStatus, jqXHR) {
				console.log('response==>', response)
				var statusCode = jqXHR.status;
				$message.removeClass('order-shield-license-status-success order-shield-license-status-error');
				if (statusCode === 200) {
					$message.addClass(response.class).text(response.message).show();
					that.html('');
					that.html(order_shield.activate);
					that.prop("disabled",false);
					setTimeout(function() {
					location.reload();
					}, 2000);
				} else {
					$message.addClass(response.class).text(response.message).show();
				}
				
			},
			error: function(jqXHR) {
				var response = jqXHR.responseJSON;
				$message.addClass(response.class).text(response.message).show();
				that.html('');
				that.html(order_shield.activate);
				that.prop("disabled",false);
			}
		});

	});

})(jQuery);
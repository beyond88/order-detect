(function ($) {
	"use strict";

	/**
	 * OrderBarrier Admin JS
	 */
	$.orderBarrier = $.orderBarrier || {};

    $(document).ready(function () {
		$.orderBarrier.init();

        var qVars = $.orderBarrier.get_query_vars("page");
		if (qVars != undefined) {
			if (qVars.indexOf("order-barrier") >= 0) {
				var cSettingsTab = qVars.split("#");
				$(
					'.order-barrier-settings-menu li[data-tab="' +
						cSettingsTab[1] +
						'"]'
				).trigger("click");				
			}
		}

	});

    $.orderBarrier.init = function () {
		$.orderBarrier.bindEvents();
	};

    $.orderBarrier.bindEvents = function () {
      $(".order-barrier-settings-menu li").on("click", function (e) {
        $.orderBarrier.settingsTab(this);
      });

      $('.order-barrier-settings-button').removeClass('button');

      $(document).on( 'click', '#enable_otp', function(){
        if( $('#enable_otp').is( ':checked' ) ) {
          $('#order-barrier-meta-sms_api_endpoint').show();
          $('#order-barrier-meta-sms_api_key').show();
		  $("#order-barrier-meta-sms_balance").show();
        } else {
          $('#order-barrier-meta-sms_api_endpoint').hide();
          $('#order-barrier-meta-sms_api_key').hide();
		  $("#order-barrier-meta-sms_balance").hide();
        }   
      });	
      
      if( $('#enable_otp').is( ':checked' ) ) {
		$('#order-barrier-meta-sms_api_endpoint').show();
        $('#order-barrier-meta-sms_api_key').show();
		$("#order-barrier-meta-sms_balance").show();
      } else {
        $('#order-barrier-meta-sms_api_key').hide();
		$('#order-barrier-meta-sms_api_endpoint').hide();
		$("#order-barrier-meta-sms_balance").hide();
      }
    };

    $.orderBarrier.settingsTab = function (button) {
		var button = $(button),
			tabToGo = button.data("tab");

		button.addClass("active").siblings().removeClass("active");
		$("#order-barrier-" + tabToGo)
			.addClass("active")
			.siblings()
			.removeClass("active");
		$('#order_barrier_builder_id').val(tabToGo);	
	};

  $.orderBarrier.get_query_vars = function (name) {
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
		let license_key = $("#orderbarrier_license_key").val();
		let $message = $('.order-barrier-license-status');
		let that = $(this); 
		
		if(license_key !=''){
			that.html(order_barrier.loader);
			that.prop("disabled",true);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: order_barrier.ajax_url,
				data: {
					action: 'license_activate',
					security: order_barrier.nonce,
					license_key: license_key
				},
				success: function(response, textStatus, jqXHR) {
					console.log('response==>', response)
					var statusCode = jqXHR.status;
					$message.removeClass('order-barrier-license-status-success order-barrier-license-status-error');
					if (statusCode === 200) {
						$message.addClass(response.class).text(response.message).show();
						that.html('');
						that.html(order_barrier.activate);
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
					that.html(order_barrier.activate);
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


		let $message = $('.order-barrier-license-status');
		let that = $(this); 
		
		that.html(order_barrier.loader);
		that.prop("disabled",true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: order_barrier.ajax_url,
			data: {
				action: 'license_deactivate',
				security: order_barrier.nonce,
			},
			success: function(response, textStatus, jqXHR) {
				console.log('response==>', response)
				var statusCode = jqXHR.status;
				$message.removeClass('order-barrier-license-status-success order-barrier-license-status-error');
				if (statusCode === 200) {
					$message.addClass(response.class).text(response.message).show();
					that.html('');
					that.html(order_barrier.activate);
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
				that.html(order_barrier.activate);
				that.prop("disabled",false);
			}
		});

	});

})(jQuery);
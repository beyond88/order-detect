(function ($) {
	"use strict";

	/**
	 * OrderDetect Admin JS
	 */
	$.orderDetect = $.orderDetect || {};

    $(document).ready(function () {
		$.orderDetect.init();

        var qVars = $.orderDetect.get_query_vars("page");
		if (qVars != undefined) {
			if (qVars.indexOf("order-detect") >= 0) {
				var cSettingsTab = qVars.split("#");
				$(
					'.order-detect-settings-menu li[data-tab="' +
						cSettingsTab[1] +
						'"]'
				).trigger("click");				
			}
		}

	});

    $.orderDetect.init = function () {
		$.orderDetect.bindEvents();
	};

    $.orderDetect.bindEvents = function () {
      $(".order-detect-settings-menu li").on("click", function (e) {
        $.orderDetect.settingsTab(this);
      });

      $('.order-detect-settings-button').removeClass('button');

      $(document).on( 'click', '#enable_otp', function(){
        if( $('#enable_otp').is( ':checked' ) ) {
          $('#order-detect-meta-sms_api_endpoint').show();
          $('#order-detect-meta-sms_api_key').show();
		  $("#order-detect-meta-sms_balance").show();
        } else {
          $('#order-detect-meta-sms_api_endpoint').hide();
          $('#order-detect-meta-sms_api_key').hide();
		  $("#order-detect-meta-sms_balance").hide();
        }   
      });	
      
      if( $('#enable_otp').is( ':checked' ) ) {
		$('#order-detect-meta-sms_api_endpoint').show();
        $('#order-detect-meta-sms_api_key').show();
		$("#order-detect-meta-sms_balance").show();
      } else {
        $('#order-detect-meta-sms_api_key').hide();
		$('#order-detect-meta-sms_api_endpoint').hide();
		$("#order-detect-meta-sms_balance").hide();
      }
    };

    $.orderDetect.settingsTab = function (button) {
		var button = $(button),
			tabToGo = button.data("tab");

		button.addClass("active").siblings().removeClass("active");
		$("#order-detect-" + tabToGo)
			.addClass("active")
			.siblings()
			.removeClass("active");
		$('#order_detect_builder_id').val(tabToGo);	
	};

  $.orderDetect.get_query_vars = function (name) {
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
		let license_key = $("#orderdetect_license_key").val();
		let $message = $('.order-detect-license-status');
		let that = $(this); 
		
		if(license_key !=''){
			that.html(order_detect.loader);
			that.prop("disabled",true);

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: order_detect.ajax_url,
				data: {
					action: 'license_activate',
					security: order_detect.nonce,
					license_key: license_key
				},
				success: function(response, textStatus, jqXHR) {
					var statusCode = jqXHR.status;
					$message.removeClass('order-detect-license-status-success order-detect-license-status-error');
					if (statusCode === 200) {
						$message.addClass(response.class).text(response.message).show();
						that.html('');
						that.html(order_detect.activate);
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
					that.html(order_detect.activate);
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


		let $message = $('.order-detect-license-status');
		let that = $(this); 
		
		that.html(order_detect.loader);
		that.prop("disabled",true);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: order_detect.ajax_url,
			data: {
				action: 'license_deactivate',
				security: order_detect.nonce,
			},
			success: function(response, textStatus, jqXHR) {
				console.log('response==>', response)
				var statusCode = jqXHR.status;
				$message.removeClass('order-detect-license-status-success order-detect-license-status-error');
				if (statusCode === 200) {
					$message.addClass(response.class).text(response.message).show();
					that.html('');
					that.html(order_detect.activate);
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
				that.html(order_detect.activate);
				that.prop("disabled",false);
			}
		});

	});

	// jQuery(document).ready(function($) {
	// 	$(document).on('click', '#the-list .order-preview', function() {
	// 		// let orderID = $(this).data('order-id');
	
	// 		// $.ajax({
	// 		// 	url: order_detect.ajax_url,
	// 		// 	type: 'POST',
	// 		// 	data: {
	// 		// 		action: 'get_customer_orders',
	// 		// 		order_id: orderID
	// 		// 	},
	// 		// 	success: function(response) {
	// 		// 		if (response.success) {
	// 		// 			let ordersTable = '<h3>Other Orders by this Customer</h3>';
	// 		// 			ordersTable += '<table class="wp-list-table widefat fixed striped">';
	// 		// 			ordersTable += '<thead><tr><th>Order ID</th><th>Date</th><th>Status</th><th>Total</th></tr></thead>';
	// 		// 			ordersTable += '<tbody>';
	
	// 		// 			response.data.orders.forEach(function(order) {
	// 		// 				ordersTable += '<tr>';
	// 		// 				ordersTable += '<td><a href="' + order.edit_link + '">#' + order.id + '</a></td>';
	// 		// 				ordersTable += '<td>' + order.date + '</td>';
	// 		// 				ordersTable += '<td>' + order.status + '</td>';
	// 		// 				ordersTable += '<td>' + order.total + '</td>';
	// 		// 				ordersTable += '</tr>';
	// 		// 			});
	
	// 		// 			ordersTable += '</tbody></table>';
	
	// 		// 			$('#wc-backbone-modal-dialog').append(ordersTable);
	// 		// 		}
	// 		// 	}
	// 		// });
	// 	});
	// });	

})(jQuery);
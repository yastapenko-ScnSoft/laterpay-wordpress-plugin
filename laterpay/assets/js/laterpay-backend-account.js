!function(e){e(function(){function t(){var t={pluginModeToggle:e("#lp_js_toggle-plugin-mode"),showMerchantContractsButton:e("#lp_js_show-merchant-contracts"),throttledFlashMessage:void 0,flashMessageTimeout:800,requestSent:!1},n=function(){e(".lp_js_validate-api-key").bind("input",function(){var e=this;setTimeout(function(){s(e)},50)}),e(".lp_js_validate-merchant-id").bind("input",function(){var e=this;setTimeout(function(){r(e)},50)}),t.pluginModeToggle.change(function(){l()}),window.onbeforeunload=function(){u()},t.showMerchantContractsButton.mousedown(function(){d()}).click(function(e){e.preventDefault()})},i=function(){for(var t=e(".lp_js_validate-api-key, .lp_js_validate-merchant-id"),n=0,i=t.length;i>n;n++)if(""===t.eq(n).val())return void t.eq(n).focus()},a=function(t){"live"===t?(e("#lp_js_plugin-mode-test-text").hide(),e("#lp_js_plugin-mode-live-text").show(),e("#lp_js_plugin-mode-indicator").fadeOut()):(e("#lp_js_plugin-mode-live-text").hide(),e("#lp_js_plugin-mode-test-text").show(),e("#lp_js_plugin-mode-indicator").fadeIn())},l=function(){var n=t.pluginModeToggle,i=e("#lp_js_plugin-mode-hidden-input"),a=0,l=1,s=n.prop("checked");c()?(i.val(a),n.prop("checked",!1),t.requestSent=!1):i.val(s?l:a),o("laterpay_plugin_mode")},o=function(n){t.requestSent||(t.requestSent=!0,e.post(ajaxurl,e("#"+n).serializeArray(),function(e){setMessage(e.message,e.success),a(e.mode)},"json").done(function(){t.requestSent=!1}))},s=function(n){var i=e(n),a=i.parents("form"),s=i.val().trim(),r=32;window.clearTimeout(t.throttledFlashMessage),s.length!==i.val().length&&i.val(s),0===s.length||s.length===r?o(a.attr("id")):t.throttledFlashMessage=window.setTimeout(function(){setMessage(lpVars.i18nApiKeyInvalid,!1)},t.flashMessageTimeout),c()&&l()},r=function(n){var i=e(n),a=i.parents("form"),s=i.val().trim(),r=22;window.clearTimeout(t.throttledFlashMessage),s.length!==i.val().length&&i.val(s),0===s.length||s.length===r?o(a.attr("id")):t.throttledFlashMessage=window.setTimeout(function(){setMessage(lpVars.i18nMerchantIdInvalid,!1)},t.flashMessageTimeout),c()&&l()},c=function(){return!t.pluginModeToggle.prop("checked")&&(32!==e("#lp_js_sandbox-api-key").val().length||22!==e("#lp_js_sandbox-merchant-id").val().length)||t.pluginModeToggle.prop("checked")&&(32!==e("#lp_js_live-api-key").val().length||22!==e("#lp_js_live-merchant-id").val().length)?!0:!1},d=function(){var n,i,a="https://laterpay.net/terms/index.html?group=merchant-contract",l=parseInt(e(window).height(),10),o=parseInt(e("#wpadminbar").height(),10)+26,s=l-o,r=e('<div id="lp_legal-docs-iframe" style="height:'+s+'px;"></div>'),c=e("#lp_legal-docs-iframe");t.showMerchantContractsButton.fadeOut(),0!==e("iframe",c).length&&e("iframe",c).remove(),0===c.length&&e("#lp_js_credentials-hint").after(r.slideDown(400,function(){n=e("#lp_legal-docs-iframe").offset(),i=n.top-o,e("BODY, HTML").animate({scrollTop:i},400)})),c=e("#lp_legal-docs-iframe"),c.html('<a href="#" id="lp_js_hide-merchant-contracts" class="lp_close-iframe">x</a><iframe src="'+a+'" frameborder="0" height="'+s+'" width="100%"></iframe>'),e("#lp_js_hide-merchant-contracts",c).bind("click",function(n){e(this).fadeOut().parent("#lp_legal-docs-iframe").slideUp(400,function(){e(this).remove()}),t.showMerchantContractsButton.fadeIn(),n.preventDefault()})},u=function(){return c()?lpVars.i18nPreventUnload:void 0},h=function(){n(),i()};h()}t()})}(jQuery);
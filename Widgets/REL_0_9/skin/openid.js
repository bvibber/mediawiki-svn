var openid = {
	current: 'openid',

	show: function(provider) {
		$('#provider_form_' + openid.current).attr('style', 'display:none');
		$('#provider_form_' + provider).attr('style', 'block');

		$('#openid_provider_' + openid.current +'_icon, #openid_provider_' + openid.current + '_link').removeClass('openid_selected');
		$('#openid_provider_' + provider +'_icon, #openid_provider_' + provider + '_link').addClass('openid_selected');

		openid.current = provider;
	},
	update: function() {
		$('#openid_url').val($('#openid_provider_url_' + openid.current).val().replace(/{.*}/, $('#openid_provider_param_' + openid.current).val()));
	}
};

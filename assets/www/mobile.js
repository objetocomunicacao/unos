(function($) {
	$.fn.mobile = function(options) {
         
		var defaults = {
			'token' : '',
			
		};

		var settigns = $.extend({}, defaults, options);
		
		
		
		return this.each(function() {

		});

	};

	$.fn.login = function(options) {
		
		var defaults = {
			'login' : '',
			'senha' : ''
		};

		var settigns = $.extend({}, defaults, options);

   		if (settigns.login != "" && settigns.senha != "") {
   			$.ajax({
   						url : 'http://localhost/apimobile/index.php/autenticacao/login',
   						dataType : 'json',
   						type : 'POST',
   						data : {
   							login : settigns.login,
   							senha : settigns.senha
   						},
   						success : function(usuarios) {
   							if(usuarios.success =='ok'){
   								$('#login').removeClass('current');
   								$('#home').addClass('current');
   								$("input[name='login']").val('');
   								$("input[name='senha']").val('');
   							}else{
   								$("input[name='login']").val('');
   								$("input[name='senha']").val('');
   							}
   						}
   					});

   		}

		return this.each(function() {

		});
		console.debug($.fn.login);
	};

})(jQuery);
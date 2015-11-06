var app = {
	development : true,
	init : function() {

	}
};

var mainPage = {
	init : function() {
		if (!$('#main-page').length) {
			return false;
		}

		var $modal = $('#create-modal');

		new Form({
			block : $modal.find('form'),
			url : '/?r=site/widget&widget=FightList&action=create&json=1',
			afterSuccess : function(data) {
				$modal.modal('hide');
				$modal.find('.success-block').hide();
				if(data.status == 1){
					window.location.href = data.redirect;
				}
				// $.pjax.reload({container:'#fightslist'});
			}
		});
		$('#create-game').click(function() {
			$modal.modal();
			return false;
		});

		$('#refresh-game').click(function() {
			$.pjax.reload({container:'#fightslist'});
			return false;
		});

		$('#games-grid').on('click', '.btn-play', function() {
			$.ajax({
				url : '/?r=site/widget&widget=FightList&action=start',
				data : $(this).data(),
				method : 'post',
				dataType : 'json',
				success : function(data) {
					if (data.status == 1) {
						window.location.href = data.redirect;
					}
				}
			});

			return false;
		});
	}
};

$(document).ready(function() {
	app.init();

	mainPage.init();
});
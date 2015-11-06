var fight = {
	owner : true,
	chat : null,
	connected : true,
	started : false,
	id : -1,
	userId : -1,
	opponentId : -1,
	player1 : null,
	player2 : null,
	locked : false,
	_holder : null,
	_interval : null,
	_server : null,
	init : function() {
		this._holder = $('#game-holder');
		this.chat = $('#chat');

		this.prepare();

		var self = this;
		this.player1 = new player({id : this.userId, index : 1});
		if (!this.owner) {
			this.start();
			this.initStart();
			this.player2 = new player({id : this.opponentId, index : 2});
		}

		var $input = this.chat.find('input');
		$('input').on("keypress", function(e) {
			if (e.keyCode == 13) {
				$input.next().find('a').click();
			}
		});
		this.chat.on('click', 'a', function() {
			var msg = $input.val();
			if (msg != '') {
				if (self.connected) {
					self.send({
						action : 'message',
						text : msg
					});
				}

				$input.val('');
			}

			return false;
		});
	},
	loading : function() {
		var $field = this._holder.find('.game-field');
		var cnt = -1;
		this._interval = setInterval(function() {
			cnt++;
			var l = cnt % 3 + 1;
			var str = '';
			for (var i=1; i<=l; i++) {
				str = str + '.';
			}

			$field.find('.point-blk').html(str);
		}, 1000);
	},
	prepare : function() {
		var self = this;
		this._server = new Server({
			onopen : function(e){
				self.send({
					action : 'connect',
					owner : self.owner
				});
			},
			onclose : function(e) {
				/* self.send({
					action : 'disconnect',
					owner : self.owner
				}); */
			},
			onmessage : function(e) {
				self.process(JSON.parse(e.data));
			},
			onerror  : function(e) {
				self.message('<span class="error-msg">Невозможно подключится к серверу. Попробуйте позже.</span>');
				clearInterval(self._interval);
				self.connected = false;
				self._holder.find('.game-field').html('');
			}
		});
		this._server.connect();

		this.loading();
	},
	send : function(data) {
		var params = {
			fight : this.id,
			user : this.getPlayer().id,
			index : this.owner ? 1 : 2
		};

		$.extend(params, data);
		logger.show('Sending server data:');
		logger.show(params);
		this._server.send(params);
	},
	getPlayer : function() {
		return this.owner ? this.player1 : this.player2;
	},
	getPlayerByIndex : function(index) {
		var p = 'player'+index;
		return this[p];
	},
	getOpponent : function() {
		return !this.owner ? this.player1 : this.player2;
	},
	process : function (data) {
		logger.show('Server response:');
		logger.show(data);

		switch (data.action) {
			case 'connect':
				if(this.owner && !this.started) {
					this.initPlayer2();
					this.opponentId = data.id;
					this.start();

					this.loadUser({});
				}
				break;
			case 'init':
				// Init Player2 for owner (cause of AJAX loading)
				this.getOpponent().init();

				this.processData(data);
				break;
			case 'reconnect':
				if (this.owner) {
					var self = this;
					var callback = function(){
						self.getOpponent().reInit();
						self.processData(data, true);
					};
					this.opponentId = data.ids[2];
					this.initPlayer2();

					this.loadUser('', {}, callback);
				} else {
					this.processData(data, true);
				}
				break;
			case 'message':
				this.message(data.text);
				break;
			case 'use':
				observer.trigger('player-'+data.player+'-use-card', [ data.card ]);
				break;
			 default:
				break;
				var method = data.action+'Action';
				this.getPlayer()[method](data);
				break;
		 }
	},
	initPlayer2 : function() {
		this.player2 = new player({id : this.opponentId, index : 2});
		this.player2.init();
	},
	processData : function(data, reconnect){
		if (typeof reconnect == 'undefined') {
			reconnect = false;
		}

		var $rows = this._holder.find('.card-row');
		this.player1.setCardRow($rows.eq(0));
		this.player2.setCardRow($rows.eq(1));

		var cardField = reconnect ? 'cards' : 'newCards';
		this.player1.addCards(data.data[1][cardField]);
		this.player2.addCards(data.data[2][cardField]);

		this.getPlayerByIndex(data.active).setActive();
	},
	message : function(m) {
		this.chat.find('.holder').append('<p>' + m + '</p>');
	},
	start : function() {
		clearInterval(this._interval);
		this._holder.find('.game-field').html('Противник присоединился. Загрузка...');
	},
	initStart : function() {
		var $block = $('<ul class="card-row" data-index="1"></ul><div class="line"></div><ul class="card-row" data-index="2"></ul>');
		this._holder.find('.game-field').html($block);
	},
	startFight : function() {
		this.started = true;
		if (this.owner) {
			this.send({action : 'init'});
		}
	},
	loadUser : function(action, data, callback) {
		var params = {
			fight : this.id,
			user : this.opponentId
		};
		$.extend(params, data);

		var self = this;
		$.ajax({
			url : '/?r=site/widget&widget=Fight&action=getUser',
			data : params,
			method : 'post',
			success : function(data) {
				$('#player-holder-2').html(data);

				if (!self.started) {
					self.initStart();
					self.startFight();
				}

				if ($.isFunction(callback)) {
					callback();
				}
			}
		})
	},
	getServer : function() {
		return this._server;
	},
	lock : function(locked) {
		this.locked = locked;
	},
	isLocked : function() {
		return this.locked;
	}
};

var player = function(options) {
	if (typeof options === 'undefined') {
		options = {};
	}
	var defaultOptions = {
		block : null,
		index : -1,
		id : null,
		hp : 20,
		mp : 1,
		maxMp : 1,
		active : false
	};
	options = $.extend(defaultOptions, options);
	$.extend(this, options);

	this.init();
};

player.prototype = {
	block : null,
	messageBlock : null,
	_cards : {},
	_row : null,
	_init : false,
	init : function() {
		this.block = $('#player-holder-' + this.index);
		this.messageBlock = this.block.find('.message');
		var cardsList = this.block.find('.card-list');

		this._cards = new cards({
			player : this,
			list : cardsList,
			items : cardsList.find('li')
		});

		this.initObserver();
	},
	reInit : function() {
		this._cards.list = this.block.find('.card-list');
		this._cards.initItems();
		this.initObserver();
	},
	initObserver : function() {
		if (this._init === true) {
			return ;
		}
		this._init = true;
		logger.show('Player ' + this.index + ' initialized observer');

		var self = this;
		$(document).on('player' + this.index + '-useCard', function(event, id) {
			logger.show('Player ' + self.index + ' used card');
			fight.send({
				action : 'use',
				card : id
			});
		});
		$(document).on('player-' + this.index + '-use-card', function(event, card){
			logger.show('Player ' + self.index + ' used card (server)');

			fight.lock(true);
			self._cards.use(card);
			fight.lock(false);
		});
	},
	getOpponent : function() {
		return fight.getPlayerByIndex(this.index == 1 ? 2 : 1);
	},
	addCards : function(data) {
		this._cards.add(data);
	},
	setActive : function() {
		if (this.isCurrent() && !this.active) {
			this.getOpponent().setNoActive();

			this.active = true;
			this._cards.onEvents();
		}
	},
	setNoActive : function() {
		if (this.active) {
			this.active = false;
			this._cards.offEvents();
		}
	},
	setCardRow : function ($row) {
		this._cards.setRow($row);
	},
	isCurrent : function() {
		return (this.index == 1 && fight.owner) || (this.index == 2 && !fight.owner);
	},
	message : function(m, type) {
		if (typeof type == 'undefined') {
			type = 'error';
		}
		var self = this;
		this.messageBlock.addClass(type).html(m).fadeIn(500, function () {
			/*setTimeout(function(){
				self.messageBlock.fadeOut(500).removeClass(type);
			}, 700);*/
		});
	},
	load : function(data, callback) {
		var params = {
			fight: fight.id,
			id: this.id
		};
		$.extend(params, data);

		fight.getServer().send();
	}
};

var cards = function(options) {
	if(typeof options == 'undefined'){
		options = {};
	}
	var defaultOptions = {
		player : null,
		list : null,
		items : null,
		count : 0,
		row : null,
		emptyClass : 'empty',
		locked : false
	};
	options = $.extend(defaultOptions, options);
	$.extend(this, options);

	// this.init();
};

cards.prototype = {
	setRow : function($row) {
		if (this.row === null) {
			this.row = $row;
		}
	},
	initItems : function() {
		this.items = this.list.find('li');
	},
	add : function(cards) {
		var self = this;
		$.each(cards, function(index, item) {
			var card = $('<div></div>');
			$.each({1 : 'cost', 2 : 'type', 3 : 'id'}, function(index, value) {
				card.attr('data-'+value, item[value])
					.data(value, item[value]);
			});
			card.removeClass(this.emptyClass)
				.html('<img src="' + item.img + '" /><span>' + item.cost + '</span>');

			card.hide();
			self.items.eq(self.count).removeClass(self.emptyClass).html('')
				.append(card);
			card.fadeIn(1000);
			self.count++;
		});
	},
	onEvents : function() {
		for(var i = 0; i<this.count; i++){
			this.items.eq(i).css('cursor', 'pointer');
		}
		var self = this;
		this.list.on('click', 'li', function() {
			self.useCard($(this));
		});
	},
	offEvents : function() {
		for (var i = 0; i<this.count; i++) {
			this.items.eq(i).css('cursor', 'default');
		}

		this.list.off('click', 'li', function(){});
	},
	useCard : function($card) {
		if (this.player.locked) {
			return false;
		}

		if (parseInt($card.find('> div').data('cost')) > this.player.mp) {
			this.player.message(messages.mpOut);
			return false;
		}

		var data = $card.find('> div').data();
		observer.trigger('player' + this.getIndex() + '-useCard', [ data.id ]);
	},
	getIndex : function() {
		return this.player.index;
	},
	use : function(card) {
		var $card = this.items.eq(card);
		// Use the card
		// Decrease MP. MP points will be changed after server response
		// But it still provides error message about mp points running out
		this.player.mp -= parseInt($card.find('> div').data('cost'));

		var $c = $card.clone();
		this.row.append($c);
		this.clearRow($card);
		this.count--;

		this.sort();
	},
	clearRow : function($row) {
		$row.html('').addClass(this.emptyClass);
	},
	sort : function() {
		var started = false;
		var card = 0;
		var cards = this.items.filter(':not(.' + this.emptyClass + ')');

		for (var i=0; i<5; i++) {
			if (!started && !this.items.eq(i).hasClass(this.emptyClass)) {
				card++;
				continue;
			}

			started = true;
			if (card < this.count) {
				this.items.eq(i).html(cards.eq(card).html()).removeClass(this.emptyClass);
				card++;
			} else {
				this.clearRow(this.items.eq(i));
			}
		}
	}
};

var messages = {
	mpOut : 'Недостаточно энергии'
};

var logger = {
	show : function(message) {
		if (app.development === true) {
			console.log(message);
		}
	}
};

var observer = {
	trigger : function(eventName, data) {
		if (typeof data == 'undefined') {
			data = [];
		}

		logger.show('Fired event:' + eventName);
		$(document).trigger(eventName, data);
	}
};
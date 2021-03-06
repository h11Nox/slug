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
	active : 1,
	phase : 1,
	_freeze : false,
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
		if (typeof data.phase != 'undefined') {
			this.active = data.active;
			this.phase = data.phase;
		}

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
				if (typeof data.player !== 'undefined') {
					this.getPlayer(data.player).message(data.text);
				}
				break;
			case 'use':
				observer.trigger('player-' + data.player + '-use-card', [ data ]);
				break;
			case 'endTurn':
				observer.trigger('player-' + data.player + '-end-turn', [data]);
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
		for (var i=1; i<=2; i++) {
			var player = this.getPlayerByIndex(i);
			player.setCardRow($rows.eq(i-1));
			player.addCards(data.data[i]['cards']);
			player.addHandCards(data.data[i]['hand']);
			player.updateData(data, true);

			if (i == data.active) {
				player.setActive();
			}
		}
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
	},
	freeze : function() {
		this.lock(true);
		this._freeze = true;
	},
	unfreeze : function() {
		this.lock(false);
		this._freeze = false;
	},
	isFreeze : function() {
		return this._freeze;
	},
	getPhase : function() {
		return this.phase;
	}
};

var player = function(options) {
	if (typeof options === 'undefined') {
		options = {};
	}
	var defaultOptions = {
		block : null,
		phaseBlock : null,
		index : -1,
		id : null,
		hp : 20,
		mp : 1,
		maxMp : 1,
		active : false,
		cssStyle : {
			activeClass : 'active',
			currentClass : 'current',
			damageClass : 'damaged',
			damageRestoreClass : 'damage-restore'
		}
	};
	options = $.extend(defaultOptions, options);
	$.extend(this, options);

	this.init();
};

player.prototype = {
	block : null,
	timer : null,
	photo : null,
	_cards : {},
	_row : null,
	_init : false,
	init : function() {
		this.block = $('#player-holder-' + this.index);
		this.timer = new timer({
			block : this.block.find('.timer'),
			player : this
		});
		this.img = this.block.find('img.player-photo:first');
		this.phaseBlock = this.block.find('.fight-phase');

		var cardsList = this.block.find('.card-list');

		this._cards = new cards({
			player : this,
			list : cardsList,
			items : cardsList.find('li')
		});

		var self = this;
		this.block.on('click', '.end-turn', function () {
			self.endTurn();
			return false;
		});

		this.block.on('click', '.player-photo', function(){
			self.getOpponent().getCards().clickHero();
			return false;
		});
		this.initAll();
	},
	reInit : function() {
		this._cards.list = this.block.find('.card-list');
		this._cards.initItems();
		this.timer.block = this.block.find('.timer');
		this.initAll();
	},
	initAll : function() {
		this.initObserver();
	},
	initObserver : function() {
		if (this._init === true) {
			return ;
		}
		this._init = true;
		logger.show('Player ' + this.index + ' initialized observer');

		var self = this;
		$(document).on('player' + this.index + '-useCard', function(event, id, data) {
			logger.show('Player ' + self.index + ' used card');
			fight.send({
				action : 'use',
				card : id,
				data : data
			});
		});
		$(document).on('player-' + this.index + '-use-card', function(event, data){
			logger.show('Player ' + self.index + ' used card (server)');

			fight.lock(true);
			self.updateData(data);
			self.getOpponent().updateData(data);
			self._cards.use(data.card);
			fight.lock(false);
		});
		$(document).on('player-' + this.index + '-end-turn', function(event, data){
			logger.show('Player ' + self.index + ' ended turn (server)');

			if (self.active) {
				self.timer.destroy();
				self.getOpponent().setActive();
			}
			fight.unfreeze();
		});
	},
	getCards : function() {
		return this._cards;
	},
	updateData : function(response, disableAnimation) {
		var animation = !(typeof disableAnimation !== 'undefined' && disableAnimation);
		var data = response.data[this.index];
		if (this.mp !== data.mp) {
			this.mp = data.mp;
			this.block.find('.m-point > span').css('height', String(data.mp * 100 / data.maxMp) + '%');
			this.block.find('.m-point-info > span:first').html(this.mp);
		}
		if (this.maxMp !== data.maxMp) {
			this.maxMp = data.maxMp;
			this.block.find('.m-point-info > span:eq(1)').html(this.maxMp);
		}
		if (this.hp !== data.health) {
			this.hp = data.health;

			if (animation) {
				var $healthBlock = this.block.find('.health span');
				$healthBlock.removeClass(this.cssStyle.damageRestoreClass)
					.removeClass(this.cssStyle.damageClass)
					.addClass(this.cssStyle.damageClass).html(this.hp);
				var self = this;
				setTimeout(function() {
					$healthBlock.addClass(self.cssStyle.damageRestoreClass);
				}, 1000);
			}
		}
	},
	getOpponent : function() {
		return fight.getPlayerByIndex(this.index == 1 ? 2 : 1);
	},
	getIndex : function() {
		return this.index;
	},
	addCards : function(data) {
		this._cards.add(data);
	},
	addHandCards : function(data) {
		this._cards.addToHand(data);
	},
	setActive : function() {
		if (!this.active) {
			this.phaseBlock.html(messages.game['phase' + fight.getPhase()]);
			this.getOpponent().setNoActive();

			this.block.addClass(this.cssStyle.activeClass);
			if (this.isCurrent()) {
				this.block.addClass(this.cssStyle.currentClass);
				if (fight.getPhase() === 1) {
					this._cards.onEvents();
				}
			}
			this.active = true;
			this.timer.start();
		}
	},
	setNoActive : function() {
		if (this.active) {
			this.phaseBlock.html(messages.game['phase' + fight.getPhase()]);
			this.active = false;

			this.block.removeClass(this.cssStyle.activeClass)
				.removeClass(this.cssStyle.currentClass);
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
		this.img.on('show.bs.tooltip', function () {
			setTimeout(function(){
				self.img.tooltip('destroy');
			}, 2000);
		});
		this.img.tooltip({title : m, placement : this.index == 1 ? 'bottom' : 'top'}).tooltip('show');
	},
	load : function(data, callback) {
		var params = {
			fight: fight.id,
			id: this.id
		};
		$.extend(params, data);

		fight.getServer().send();
	},
	endTurn : function() {
		if (this.active) {
			fight.freeze();
			fight.send({
				action : 'end-turn',
				player : this.index
			});
			// fight.unfreeze();
		}
	}
};

var cards = function(options) {
	if(typeof options == 'undefined'){
		options = {};
	}
	var defaultOptions = {
		player : null,
		list : null,
		hand : {},
		items : null,
		count : 0,
		row : null,
		emptyClass : 'empty',
		locked : false,
		maxCount : 5,
		isOn : false,
		backPath : '/img/default_back.jpg',
		selected  : null,
		animationDuration : 2000,
		cssStyle : {
			activeClass : 'selected',
			activeBodyClass : 'selected-card',
			unitClass : 'unit'
		}
	};
	options = $.extend(defaultOptions, options);
	$.extend(this, options);

	// this.init();
};

cards.prototype = {
	setRow : function($row) {
		if (this.row === null) {
			this.row = $row;

			var self = this;
			this.row.on('click', 'li', function() {
				$(document).trigger('player-' + self.getIndex() + '-click-unit', [$(this)]);
				return false;
			});
			$(document).on('player-' + this.player.getOpponent().getIndex() + '-click-unit', function(e, unit) {
				self.clickUnit(unit);
			});
		}
	},
	initItems : function() {
		this.items = this.list.find('li');
	},
	add : function(cards) {
		var self = this;
		$.each(cards, function(index, item) {
			var card = $('<div></div>');
			if (self.player.isCurrent()) {
				$.each({1 : 'cost', 2 : 'type', 3 : 'id', 4 : 'usage'}, function(index, value) {
					card.attr('data-'+value, item[value])
						.data(value, item[value]);
				});
				card.html('<img src="' + item.img + '" /><span>' + item.cost + '</span>');

				card.hide();
			} else {
				card.html('<img src="' + self.backPath + '" />');
			}

			self.items.eq(self.count).removeClass(self.emptyClass).html('')
				.append(card);
			card.fadeIn(1000);
			self.count++;
		});
	},
	addToHand : function(data) {
		for (var i in data) {
			var card = $('<div data-index="' + i + '"></div>');
			card.html(
				'<img src="' + data[i].card.img + '" />'
				+ '<span class="attack">' + data[i].data.damage + '</span>'
				+ '<span class="hp">' + data[i].data.hp + '</span>'
			);
			var $item = $('<li></li>');
			$item.addClass('unit-' + this.getIndex());
			this.row.append($item.append(card));
		}
	},
	onEvents : function() {
		if (this.isOn) {
			return ;
		}
		this.isOn = true;
		for(var i = 0; i<this.count; i++){
			this.items.eq(i).css('cursor', 'pointer');
		}
		var self = this;
		this.list.on('click', 'li', function() {
			self.useCard($(this));
		});
	},
	offEvents : function() {
		if (!this.isOn) {
			return ;
		}
		this.isOn = false;

		for (var i = 0; i<this.count; i++) {
			this.items.eq(i).css('cursor', 'default');
		}

		this.list.off('click', 'li', function(){});
	},
	useCard : function($card) {
		if (fight.isLocked()) {
			return false;
		}
		if ($card.hasClass(this.cssStyle.activeClass)) {
			this.unselect();
			return false;
		}
		if (!this.checkMp($card)) {
			return false;
		}

		if ($card.find('> div').data('usage') == 1) {
			this.doUseCard($card);
		} else {
			this.select($card);
		}
	},
	checkMp : function($card) {
		if (parseInt($card.find('> div').data('cost')) > this.player.mp) {
			this.player.message(messages.mpOut);
			return false;
		}
		return true;
	},
	doUseCard : function($card, data) {
		if (typeof data === 'undefined') {
			data = {};
		}
		this.unselect();
		observer.trigger('player' + this.getIndex() + '-useCard', [
			$card.find('> div').data('id'),
			data
		]);
	},
	unselect : function() {
		this.selected = null;
		$('body').removeClass(this.cssStyle.activeBodyClass)
			.removeClass(this.cssStyle.activeBodyClass + '-' + this.getIndex());
		this.items.removeClass(this.cssStyle.activeClass);
	},
	select : function($card) {
		this.unselect();
		this.selected = parseInt($card.find('> div').data('id'));
		$('body').addClass(this.cssStyle.activeBodyClass)
			.addClass(this.cssStyle.activeBodyClass + '-' + this.getIndex());
		$card.addClass(this.cssStyle.activeClass);
	},
	clickHero : function() {
		if (this.selected !== null) {
			var $card = this.items.eq(this.selected);
			if (this.checkMp($card)) {
				this.doUseCard($card, {
					type : 'player'
				});
			}
		}
	},
	clickUnit : function($unit) {
		if (this.selected !== null) {
			var $card = this.items.eq(this.selected);
			if (this.checkMp($card)) {
				this.doUseCard($card, {
					type : 'unit',
					index : $unit.find('> div').data('index')
				});
			}
		}
	},
	getIndex : function() {
		return this.player.index;
	},
	use : function(card) {
		var index = card.card.id;
		var $card = this.items.eq(index);
		// Use the card
		// Decrease MP. MP points will be changed after server response
		// But it still provides error message about mp points running out
		this.player.mp -= parseInt($card.find('> div').data('cost'));

		switch (cardData.types[card.card.type]) {
			case 'unit':
				this.addToHand([card]);
				break;
			case 'damage':
				this.player.getOpponent().block.find('.card')
					.html('<img src="' + card.card.img + '" />').show().fadeOut(this.animationDuration);
				break;
			default:
				//
				break;
		}
		this.clearRow($card, index + 1);
		this.count--;

		this.sort();
	},
	clearRow : function($row, index) {
		$row.html('<span>' + index + '</span>').addClass(this.emptyClass);
	},
	sort : function() {
		var started = false;
		var card = 0;
		var cards = this.items.filter(':not(.' + this.emptyClass + ')');

		for (var i=0; i<this.maxCount; i++) {
			if (!started && !this.items.eq(i).hasClass(this.emptyClass)) {
				card++;
				continue;
			}

			started = true;
			if (card < this.count) {
				this.items.eq(i).html(cards.eq(card).html()).removeClass(this.emptyClass)
					.find('> div').data('id', i).attr('data-id', i);
				card++;
			} else {
				this.clearRow(this.items.eq(i), i + 1);
			}
		}
	}
};

var timer = function(options) {
	if(typeof options == 'undefined'){
		options = {};
	}
	var defaultOptions = {
		block : null,
		time : 0,
		timePerMove : 60,
		player : null
	};
	options = $.extend(defaultOptions, options);
	$.extend(this, options);

	// this.init();
};

timer.prototype = {
	interval : null,
	started : false,
	start : function() {
		if (this.started) {
			return false;
		}

		this.started = true;
		this.time = this.timePerMove;
		var self = this;
		this.interval = setInterval(function(){
			self.tick();
		}, 1000);
	},
	tick : function() {
		if (fight.isFreeze()) {
			return ;
		}
		this.time--;
		if (this.time === 0) {
			this.end();
		} else {
			this.block.html(this.time);
		}
	},
	end : function() {
		this.stop();
		// this.player.endTurn();
		/** observer.trigger('player' + this.player.index + '-timeout', []); */
	},
	stop : function() {
		if (this.interval !== null) {
			clearInterval(this.interval);
		}
	},
	destroy : function() {
		this.time = this.timePerMove;
		this.started = false;
		this.stop();
	}
};

var cardData = {
	types : {
		1 : 'unit',
		2 : 'damage',
		3 : 'boost'
	}
};

var messages = {
	game : {
		phase1 : 'Фаза атаки',
		phase2 : 'Фаза защиты'
	},
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
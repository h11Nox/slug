var ArrayEditor = function(options){

	this.block = null;
	this.model = null;
	this.attribute = null;
	this.classes = null;
	this.multiple = false;
	this.keys = {}; // For Multiple Only
	this.data = {};
	this.min = null;
	this._index = 0;
	this._add = null;

	$.extend(this, options);

	this.init();
};

ArrayEditor.prototype = {

	init: function(){
		this._add = this.block.next();

		var self = this;
		this._add.click(function(){
			self.add();
		});
		this.block.on('click', '.remove-item', function(){
			$(this).closest('.input-row').remove();
		});

		if(this.data != {} && this.data.length){
			for(var i in this.data){
				this.add(this.data[i]);
			}
		}

		if(this.min !== null && this.min > this._index){
			var cnt = this.min - this._index;
			for(i = 1; i<= cnt; i++){
				this.add();
			}
		}
	},
	add : function(val){
		var $html = $(this.multiple ? '<div class="inputs-group input-row"><span class="remove-item danger">X</span></div>' : '<div class="input-group input-row"><span class="input-group-addon remove-item danger">X</span></div>');
		var inputs = [];
		if(!this.multiple){
			inputs[0] = this.getInput(val);
		}
		else{
			for(var i=0; i<this.keys.length; i++){
				inputs[i] = this.getInput(typeof val != 'undefined' && typeof val[this.keys[i]] != 'undefined' ? val[this.keys[i]] : '', this.keys[i]);
			}
			inputs.reverse();
		}

		for(var i=0; i<inputs.length; i++){
			$html.prepend(inputs[i]);
		}
		this.block.append($html);
		this._index++;
	},
	getInput : function(val, field){
		var $input = $('<input name="'+this.getNewName(field)+'" class="form-control" />');
		if(val != 'undefined'){
			$input.val(val);
		}
		if(this.classes != null){
			$input.attr('class', this.classes);
		}
		return $input;
	},
	getName : function(index, field){
		var name = this.model+'['+this.attribute+']['+index+']';
		if(typeof field != 'undefined'){
			name = name + '['+field+']';
		}
		return name;
	},
	getNewName : function(field){
		return this.getName(this._index, field);
	}
};
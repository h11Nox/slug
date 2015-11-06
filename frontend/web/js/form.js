var Form = function(options){
    var defaultOptions  = {
        block : null,
        successBlock : null,
        errorBlock : null,
        autoClear : true,
        afterClear : true,
        captcha : false,
        url : '',
        success : null,
        afterSuccess : null,
        _loader : null
    };
    options = $.extend(defaultOptions, options);

    $.extend(this, options);
    if(!this.captcha){
        if(this.block.find('.captcha-row')){
            this.captcha = true;
        }
    }

    this.init();
};

Form.prototype = {
    setForm : function(form){
        this.block = form;
        this.init();
        return this;
    },
    init : function(){
        this.initBlocks();
        this.successBlock.hide();
        this.errorBlock.html('');
        if(this.autoClear){
            this.clear();
        }

        var self = this;
        if(this.url != ''){
            this.block.find('.c-submit').click(function(){

                $.ajax({
                    url : self.url,
                    dataType: 'json',
                    method : 'post',
                    data : self.block.serialize(),
                    success : function(data){
                        if(data.result == 0){
                            if(self.captcha){
                                var $block = self.block.find('.captcha-row');
                                $block.find('a').click();
                                $block.find('input').val('');
                            }
                            self.errorBlock.html(data.errorSummary);
                        }
                        else{
                            self.errorBlock.html('');
                            $.isFunction(self.success) ? self.success(data) : self.showSuccess(data);
                            if($.isFunction(self.afterSuccess)){
                                self.afterSuccess(data);
                            }
                        }
                    }
                });

                return false;
            });
            this.block.submit(function(){

                return false;
            });
        }
    },
    initBlocks : function(){
        if(this.successBlock == null){
            this.successBlock = this.block.find('.success-holder');
        }
        if(this.errorBlock == null){
            this.errorBlock = this.block.find('.errors-holder');
        }
    },
    clear : function(){
        this.errorBlock.html('');
        this.block.find('input[type="text"]:visible, input[type="password"], textarea').val('').removeClass('.error');
    },
    showSuccess : function(){
        if(this.afterClear){
            this.clear();
        }
        if(this.successBlock){
            this.successBlock.show();
        }
    }
};
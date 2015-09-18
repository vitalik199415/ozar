;(function ( $, window, document, undefined ) {


	$.defaultOptions = {
		Selectors:{	
			propBlock: '.properties_block',
			groupName: '.group_name',
			slideButton: '.slide_button',
			prop: '.property',
			insertActiveFiltersBlock: '#insert_active_filters_block',
			insertActiveAdditionalBlock: '#insert_active_filters_additional_block',
			activeFiltersBlock: '.active_filters_block',
			outputFiltersBlock: '#output_filters_block',
			filtersForm: '#filters_form',
			filtersClearForm: '#filters_clear_form'
		},
		Elements:{
			slideButton: '<a href="#" class="slide_button"><span><i></i></span></a>',
			ArrowUiIconDown: 'ui-icon ui-icon-circle-triangle-s',
			ArrowUiIconRight: 'ui-icon ui-icon-circle-triangle-e',
			AwesomeIcon: '',
			AwesomeIconChange: '',
			topActiveBlock: '<div>'
		},
		Text: {
			collapse: 'Свернуть',
			expand: 'Развернуть',
			productFilters:'Фильтры товаров'
		},
		slidePropButton: true,
		chksVisible: 10,
		filtersTypes: '',
		filtersActiveHtml: '',
		filtersActiveAdditionalHtml: '',
		//types_active_additional_block: '',
		activePanelVisibility: true,
		activeAdditionalPanelVisibility: true,
		displayFiltersTypes: '',
		submitFiltersButtons: '#filter_block_activate_button, #filter_block_activate_top_button, #filter_activate_additional_block',
		clearFiltersButtons:'#filter_block_clear_button, #filter_block_clear_top_button, #filter_clear_additional_block',
		openClose: false,
        changePrQtyUrl: false,        
        priceSliderVisibility: true
	};

	$.widget("gbc.gbcFilters", {
	
	  options: $.defaultOptions,
		
		_create: function() {
			this.initChangePrQty();
            this.initFiltersTypes();
			this.priceSlider();
			this.activePanel();
			this.activeProperties();
			this.displayFiltersTypes(this.options.displayFiltersTypes);
			this.submitFilters(this.options.submitFiltersButtons);
			this.clearFilters(this.options.clearFiltersButtons);
			this.checkboxName();
		},

		displayFiltersTypes: function(type){
			switch (type){
				case 'leftSlide':
				case 'rightSlide':
					this.slideType(this.options.displayFiltersTypes);
				break
				case 'dialog':
					this.dialogType();
				break
			}
		},
		
		initFiltersTypes: function(){
			var $this = this;
			$.each(this.options.filtersTypes, function(id, type){
				if(type == 'checkbox'){
					$this.initFilterCheckbox(id);
				}
				else if (type == 'color' || type == 'color_name') {
					$this.initFilterColor(id);
				}
				else if (type == 'image') {
					$this.initImageColor(id);
				}
			});
		},
	  
		initFilterCheckbox: function(id){
			var filterElement = this.element.find('#'+id);	
			filterElement.buttonset();
			this.addButtonSlide(filterElement, id);
			this.scrollPanelStyle(filterElement);
		},
		
		initFilterColor: function(id){
			var filterElement = this.element.find('#'+id);
			filterElement.buttonset();
			this.addButtonSlide(filterElement, id);
			//this.scrollPanelStyle(filterElement, this.options);

		},
		
		initImageColor: function(id){
			var filterElement = this.element.find('#'+id);
			filterElement.buttonset();
			this.addButtonSlide(filterElement, id);
			this.scrollPanelStyle(filterElement);

		},
	  
	  checkbox_filter: {
				
	  },
	  
	  color_filter: {
	  
	  },
		
		scrollPanelStyle: function(el) {
			var o = this.options;
			chksCount = el.find(o.Selectors.propBlock).find(':checkbox').length;
			chkHeight = el.find(o.Selectors.prop).innerHeight();
			if(o.chksVisible < chksCount){
				el.find(o.Selectors.propBlock).css('max-height', o.chksVisible*chkHeight);
				el.find(o.Selectors.propBlock).jScrollPane();
			}
			else return;
		},
	  
		activePanel: function(){
			var $this = this.element;
			var o = this.options;
			if(this.options.activePanelVisibility){
				$this.find(o.Selectors.insertActiveFiltersBlock).append(o.filtersActiveHtml);
				$this.find(o.Selectors.activeFiltersBlock).buttonset();
			}
			if(this.options.activeAdditionalPanelVisibility){
				this.activeAdditionalPanel();
			}
		},
		
		activeAdditionalPanel: function(){
			var $this = this.element;
			var o = this.options;
			var addActiveBlock = $('#active_filters_additional_block');
			var addActiveChks = addActiveBlock.find(':checkbox');
			var insertActiveFiltersBlock = this.element.find(o.Selectors.insertActiveFiltersBlock);
			var outputFiltersBlock = this.element.find(o.Selectors.outputFiltersBlock);
			var outputFiltersChks =  outputFiltersBlock.find(':checkbox');
			addActiveBlock.find('.properties_block').buttonset();
			this._on( addActiveChks , {
				'change': function(event){
					var el = $(event.currentTarget);
					insertActiveFiltersBlock.find(':checkbox[name="'+el.attr('name')+'"]').prop('checked', el.prop('checked')).button('refresh');
					outputFiltersBlock.find(':checkbox[name="'+el.attr('value')+'"]').prop('checked', el.prop('checked')).button('refresh');
				}
			});
		},
		
		activeProperties: function(){
			var o = this.options;
			var insertActiveFiltersBlock = this.element.find(o.Selectors.insertActiveFiltersBlock);
			var activeChks = insertActiveFiltersBlock.find(':checkbox');
			var outputFiltersBlock = this.element.find(o.Selectors.outputFiltersBlock);
			var outputFiltersChks =  outputFiltersBlock.find(':checkbox');
			var activeFilterBlock = this.element.find('.active_filters_block');
			var addActiveBlock = $('#active_filters_additional_block');
			var addActiveChks = addActiveBlock.find(':checkbox');
			
			this._on(activeChks , {
				'change': function(event){
					var el = $(event.currentTarget);
					outputFiltersBlock.find(':checkbox[name="'+el.attr('value')+'"]').prop( 'checked', el.prop('checked')).button('refresh');
					addActiveBlock.find(':checkbox[value="'+el.attr('value')+'"]').prop( 'checked', el.prop('checked')).button('refresh');
				}
			})
			
			this._on(outputFiltersChks , {
				'change': function(event){
					var el = $(event.currentTarget);
					activeFilterBlock.find(':checkbox[value="'+el.attr('name')+'"]').prop( 'checked', el.prop('checked')).button('refresh');
					addActiveBlock.find(':checkbox[value="'+el.attr('name')+'"]').prop( 'checked', el.prop('checked')).button('refresh');
				}
			})
		},
		
		checkboxName: function(){
			var chksTitle = this.element.find('.property .checkbox_name');
			var addActiveChcks = $('#active_filters_additional_block').find('.property .checkbox_name');
			
			this._on(chksTitle, {
				'click': function(event){
					var el = $(event.currentTarget);
					el.parent().find('label').click();
				}
			});

			this._on(addActiveChcks, {
				'click': function(event){
					var el = $(event.currentTarget);
					el.parent().find('label').click();
				}
			});
		},

		submitFilters: function(buttons) {
			
			var eventFunc = {},
				$this = this.element,
				submitButtons = this.document.find(buttons);
				
			eventFunc['click'] = function(event) {
				el = $(event.currentTarget);
				
				if(this.element.find(':checkbox[value="products_filters_price[active]"]').prop('checked') == false){
					this.resetSlider();
				}

                loading_start();
                $this.find(this.options.Selectors.filtersForm).submit();
                return false;
			};
			this._on(submitButtons, eventFunc);	
		},
		
		resetSlider: function(){
			var inputMinCoast = this.element.find('#products_filters_price_price_from');
			var inputMaxCoast = this.element.find('#products_filters_price_price_to');
			
			inputMinCoast.val('');
			inputMaxCoast.val('');
		},
		
		clearFilters: function(buttons) {
			var eventFunc = {},
				$this = this.element,
				clearButtons = this.document.find(buttons);
				 
			eventFunc['click'] = function(event) {
                loading_start();
                $this.find(this.options.Selectors.filtersClearForm).submit();
                return false;
			};
			this._on(clearButtons, eventFunc);	
		},
		
	
		slideType: function(side) {
			this.element.addClass(side+"_block");	
			this.element.append("<div href='#' class='"+side+"_trigger_button'><span>"+this.options.Text.productFilters+"</span></div>");
			var $this = this,
				 hideButton = this.element.find("."+side+"_trigger_button"),
				 el = this.element.find('.block:first'),
				 screenHeight = $(window).height(),
				 o = this.options;
				 
			el.css('height',screenHeight - parseInt(this.element.css('top'), 10) - 10);
			this.scrollTopBlock();
			el.jScrollPane();
			el.hide();	
			hideButton.toggle(
				function(){
					el.show('fast');
					hideButton.toggleClass('active'); 
					o.openClose = true;
					return false;
				},
				function(){
					el.hide('fast');
					hideButton.toggleClass('active');
					o.openClose = false;
					return false;
			});
		},
		
		scrollTopBlock: function() {
			var intendHeight = parseInt(this.element.css('top'), 10),
				 el = this.element,
				 $this = this,
				 screenHeight = $(window).height(),
				 reinitScroll = true;
				 
				 $(window).scroll(function(){	
					var top = $(this).scrollTop();
					
					if (top < intendHeight) {
						el.css('top', (intendHeight-top));
					} 
					else 
					{
						el.css('top', 0);
					}
				});
				/*$(window).scroll(function(){	
					var top = $(this).scrollTop();

					if($this.options.openClose){	
						if (top < intendHeight) {
							el.css('top', (intendHeight-top));
							$this.element.find('.block:first').css('height', screenHeight - intendHeight+ top);
						} 
						else 
						{
							if(reinitScroll){
								reinitScroll = false;
								el.css('top', 0);
								$this.element.find('.block:first').css('height', screenHeight);


							}
						}
					}
				});*/
		},
		
		dialogType: function(){
			this.element.addClass('dialog_filters_block');
			this.element.before("<div href='#' class='open_dialog'><span>"+this.options.Text.productFilters+"</span></div>");
			var openDialog = this.element.parent().find('.open_dialog'),
				$this = this,
				dialogHeight = $(window).height()*0.9;
				dialogWidth = $(window).width()*0.8;
				
			this.element.dialog({
				resizable: false,
				autoOpen: false,
				show: {},
				hide: {},
				modal: true ,
				width: dialogWidth,
				height: dialogHeight
			});
			
			openDialog.click(function(){
				$this.element.dialog("open");
				$this.element.jScrollPane();
				$(document).find('.ui-widget-overlay').click(function(){
					$this.element.dialog("close");
				});
			});
		},
		
		addButtonSlide: function(el, id) {
	  		var o = this.options;
			el.find(o.Selectors.groupName).prepend(o.Elements.slideButton);
			var propertiesBlock = el.find(o.Selectors.propBlock);
			var slideButton = el.find(o.Selectors.slideButton);
			var tag_i = el.find(o.Selectors.slideButton).find('span i');
			var $this = this;
			if($this.getCookie(id+"_open") == "0"){
			
				propertiesBlock.slideUp(500);
				slideButton.addClass(o.Elements.ArrowUiIconRight);
				tag_i.addClass(o.Elements.AwesomeIconChange);
				slideButton.toggle(
					function(){
						propertiesBlock.slideDown(500);
						slideButton.removeClass(o.Elements.ArrowUiIconRight).addClass(o.Elements.ArrowUiIconDown);
						tag_i.removeClass(o.Elements.AwesomeIconChange).addClass(o.Elements.AwesomeIcon);
						$this.setCookie(id+"_open", "1");
					},
					function(){
						propertiesBlock.slideUp(500);
						slideButton.removeClass(o.Elements.ArrowUiIconDown).addClass(o.Elements.ArrowUiIconRight);
						tag_i.removeClass(o.Elements.AwesomeIcon).addClass(o.Elements.AwesomeIconChange);
						$this.setCookie(id+"_open", "0");
					}
				);
			}
			else
			{
				slideButton.addClass(o.Elements.ArrowUiIconDown);
				tag_i.addClass(o.Elements.AwesomeIcon);
				slideButton.toggle(
					function(){
						propertiesBlock.slideUp(500);
						slideButton.removeClass(o.Elements.ArrowUiIconDown).addClass(o.Elements.ArrowUiIconRight);
						tag_i.removeClass(o.Elements.AwesomeIcon).addClass(o.Elements.AwesomeIconChange);
						$this.setCookie(id+"_open", "0");
						
					},
					function(){
						propertiesBlock.slideDown(500);
						slideButton.removeClass(o.Elements.ArrowUiIconRight).addClass(o.Elements.ArrowUiIconDown);
						tag_i.removeClass(o.Elements.AwesomeIconChange).addClass(o.Elements.AwesomeIcon);
						$this.setCookie(id+"_open", "1");
					}
				);
			}	
		},

        initChangePrQty : function()
        {
            var filterElement = this.element.find("input[type=checkbox]");
            var eventFunc = {},
                $this = this.element;
            eventFunc['change'] = function(event) {
                this.submitChangePrQty();
            };
            this._on(filterElement, eventFunc);
        },

        submitChangePrQty : function()
        {
            var $this = this;
            var options = {
                url:       this.options.changePrQtyUrl,
                success:   function(responseText, statusText, xhr, $form) { 
				return $this.updateChangePrQty.apply($this, Array(responseText, statusText, xhr, $form)) },
                dataType:  'json'
            };
			
            $(this.element.find(this.options.Selectors.filtersForm)).ajaxSubmit(options);
        },

        updateChangePrQty : function($json)
        {
            var $form = this.element.find(this.options.Selectors.filtersForm);
			console.log($json);
            $.each($json, function(type, ms)
            {
                if(type == 'prop')
                {
                    $.each(ms, function(tkey, t)
                    {
                        $.each(t, function(prey, p)
                        {
		
                            var checkbox = $form.find("input[name='products_filters["+tkey+"]["+prey+"]']");

                            if(p == '+0' || p == '0') checkbox.button( "option", "disabled", true); else checkbox.button( "option", "disabled", false);
                            $(checkbox).parents('.property').find('.pr_qty').html('('+p+')');
                        });
                    });
                }
                if(type == 'additional_prop')
                {
                    $.each(ms, function(pkey, p)
                    {
                        var checkbox = $form.find("input[name='products_filters_additional["+pkey+"]']");

                        if(p == '+0' || p == '0') checkbox.button( "option", "disabled", true); else checkbox.button( "option", "disabled", false);
                        $(checkbox).parents('.property').find('.pr_qty').html('('+p+')');
                    });
                }
                if(type == 'price_prop')
                {
                    var price_qty_block = $form.find('.price_slider_block').find('.pr_qty');
                    price_qty_block.html('('+ms+')');
                }
            });
        },

		getCookie: function(name) {
		  var matches = document.cookie.match(new RegExp(
			"(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
		  ));
		  return matches ? decodeURIComponent(matches[1]) : undefined;
		},
		
		setCookie: function(name, value, options) {
			options = options || {};

			var expires = options.expires;

			if (typeof expires == "number" && expires) {
			var d = new Date();
			d.setTime(d.getTime() + expires*1000);
			expires = options.expires = d;
			}
			if (expires && expires.toUTCString) { 
			options.expires = expires.toUTCString();
			}

			value = encodeURIComponent(value);

			var updatedCookie = name + "=" + value;

			for(var propName in options) {
				updatedCookie += "; " + propName;
				var propValue = options[propName];    
				if (propValue !== true) { 
				  updatedCookie += "=" + propValue;
				}
			}

			document.cookie = updatedCookie;
		},
		
		deleteCookie: function(name) {
			this.setCookie(name, "", { expires: -1 })
		},

		priceSlider: function(){
			var $this = this;

            var minCost = parseInt(this.element.find('#products_filters_price_price_from').val());
			var maxCost = parseInt(this.element.find('#products_filters_price_price_to').val());
			var totalMin = parseInt(this.element.find('#products_filters_price_hidden_price_from').val());
			var totalMax = parseInt(this.element.find('#products_filters_price_hidden_price_to').val());

            var inputMinCoast = this.element.find('#products_filters_price_price_from');
			var inputMaxCoast = this.element.find('#products_filters_price_price_to');
			var sliderRangeBlock = this.element.find("#filters_price_slider");

            if(minCost == totalMin) inputMinCoast.val('');
            if(maxCost == totalMax) inputMaxCoast.val('');

			
			sliderRangeBlock.slider({
				min: totalMin,
				max: totalMax,
				values: [minCost, maxCost],
				range: true,
				stop: function(event, ui) {
					inputMinCoast.val(sliderRangeBlock.slider("values", 0));
					inputMaxCoast.val(sliderRangeBlock.slider("values", 1));
			    },
			    slide: function(event, ui) {
					inputMinCoast.val(sliderRangeBlock.slider("values", 0));
					inputMaxCoast.val(sliderRangeBlock.slider("values", 1));
			    },
				change: function(event, ui) {
                    if(inputMinCoast.val() == totalMin) inputMinCoast.val('');
                    if(inputMaxCoast.val() == totalMax) inputMaxCoast.val('');

                    $this.submitChangePrQty.apply($this, Array());
				}
			});

			inputMinCoast.change(function(){
                var value1 = inputMinCoast.val();
                var value2 = inputMaxCoast.val();

                if(parseInt(value1) > parseInt(value2)){
                  value1 = value2;
                  inputMinCoast.val(value1);
                }
                sliderRangeBlock.slider("values", 0, value1);
		    });

		    inputMaxCoast.change(function(){
		        var value1 = inputMinCoast.val();
		        var value2 = inputMaxCoast.val();
		        
		        if (value2 > maxCost) { 
					value2 = maxCost; 
					inputMaxCoast.val(maxCost)
				}

		        if(parseInt(value1) > parseInt(value2)){
		          value2 = value1;
		          inputMaxCoast.val(value2);
		        }

		        sliderRangeBlock.slider("values",1,value2);
		    });

		    this.element.find('.price_slider_block .slider_block input').find().keypress(function(event){
		          var key, keyChar;
		          if(!event) var event = window.event;
		          
		          if (event.keyCode) key = event.keyCode;
		          else if(event.which) key = event.which;
		        
		          if(key==null || key==0 || key==8 || key==13 || key==9 || key==46 || key==37 || key==39 ) return true;
		          keyChar=String.fromCharCode(key);
		          
		          if(!/\d/.test(keyChar)) return false;
		    });
			
		}
		
	});
	
})( jQuery, window, document );


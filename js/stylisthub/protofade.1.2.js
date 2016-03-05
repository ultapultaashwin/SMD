/**
 * SMD
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magemobiledesign.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * SMD does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * SMD does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    SMD
 * @package     SMD_Stylisthub
 * @version     1.2.3
 * @author      SMD Team <support@magemobiledesign.com>
 * @copyright   Copyright (c) 2014 SMD. (http://www.magemobiledesign.com)
 * @license     http://www.magemobiledesign.com/LICENSE.txt
 * 
 */

var Protofade = Class.create({

	initialize: function(element, options) {		
		this.options = {
      		duration: 1,
			delay: 4.0,
			randomize: false,
			autostart:true,
			controls:false,
			eSquare:false,
			eRows: 3, 
			eCols: 5,
			eColor: '#FFFFFF'
    	}
		Object.extend(this.options, options || {});

    	this.element        = $(element);
		this.slides			= this.element.childElements();
		this.num_slides		= this.slides.length;		
		this.current_slide 	= (this.options.randomize) ? (Math.floor(Math.random()*this.num_slides)) : 0;
		this.end_slide		= this.num_slides - 1;
		
		this.slides.invoke('hide');
		this.slides[this.current_slide].show();
				
		if (this.options.autostart) { 
			this.startSlideshow();
		}				
		if (this.options.controls) {
			this.addControls();
		}
		if (this.options.eSquare) {
			this.buildEsquare();
		}
	},
	
	addControls: function() {
		this.wrapper 		= this.element.up();
		this.controls		= new Element('div', { 'class': 'controls' });
		this.wrapper.insert(this.controls);
		
		this.btn_next 		= new Element('a', { 'class': 'next', 'title': 'Next', href: '#' }).update('');
		this.btn_previous	= new Element('a', { 'class': 'previous', 'title': 'Previous', href: '#' }).update('');
		this.btn_start		= new Element('a', { 'class': 'start', 'title': 'Start', href: '#' }).update('Start');
		this.btn_stop		= new Element('a', { 'class': 'stop', 'title': 'Stop', href: '#' }).update('Stop');
		
		this.btns = [this.btn_previous, this.btn_next, this.btn_start, this.btn_stop];
		this.btns.each(function(el){
			this.controls.insert(el);
		}.bind(this));
		
		this.btn_previous.observe('click', this.moveToPrevious.bindAsEventListener(this));
		this.btn_next.observe('click', this.moveToNext.bindAsEventListener(this));
		this.btn_start.observe('click', this.startSlideshow.bindAsEventListener(this));
		this.btn_stop.observe('click', this.stopSlideshow.bindAsEventListener(this));
	},
	
	buildEsquare: function() {		
		this.eSquares 	= [];
		var elDimension	 	= this.element.getDimensions();
		var elWidth  		= elDimension.width;
		var elHeight 		= elDimension.height;
				
		var sqWidth 		= elWidth / this.options.eCols;
		var sqHeight 		= elHeight / this.options.eRows;
	
		$R(0, this.options.eCols-1).each(function(col) {
			this.eSquares[col] = [];							 	
			$R(0, this.options.eRows-1).each(function(row) {
				var sqLeft = col * sqWidth;
			    var sqTop  = row * sqHeight;
				this.eSquares[col][row] = new Element('div').setStyle({
 														    opacity: 0, backgroundColor: this.options.eColor,
															position: 'absolute', 'z-index': 5,
															left: sqLeft + 'px', top: sqTop + 'px',
															width: sqWidth + 'px', height: sqHeight + 'px'		
														});
				this.element.insert(this.eSquares[col][row]);				 							 										 
			}.bind(this))
		}.bind(this));			
	},

	startSlideshow: function(event) {
		if (event) { Event.stop(event); }
		if (!this.running)	{
			this.executer = new PeriodicalExecuter(function(){
	  			this.updateSlide(this.current_slide+1);
	 		}.bind(this),this.options.delay);
			this.running=true;
		}
	},
	
	stopSlideshow: function(event) {	
		if (event) { Event.stop(event); } 
		if (this.executer) { 
			this.executer.stop();
			this.running=false;
		}	 
	},

	moveToPrevious: function (event) {
		if (event) { Event.stop(event); }
		this.stopSlideshow();
  		this.updateSlide(this.current_slide-1);
	},

	moveToNext: function (event) {
		if (event) { Event.stop(event); }
		this.stopSlideshow();
  		this.updateSlide(this.current_slide+1);
	},
	
	updateSlide: function(next_slide) {		
		if (next_slide > this.end_slide) { 
			next_slide = 0; 
		} 
		else if ( next_slide == -1 ) {
			next_slide = this.end_slide;
		}	
		this.fadeInOut(next_slide, this.current_slide);		
	},

 	fadeInOut: function (next, current) {		
		new Effect.Parallel([
			new Effect.Fade(this.slides[current], { sync: true }),
			new Effect.Appear(this.slides[next], { sync: true }) 
  		], { duration: this.options.duration });
		
		if (this.options.eSquare) {			
			$R(0, this.options.eCols-1).each(function(col) {	 						 	
				$R(0, this.options.eRows-1).each(function(row) {
					var eSquare = this.eSquares[col][row];
					var delay = Math.random() * 150;				
					setTimeout(this.delayedAppear.bind(this, eSquare), delay);
				}.bind(this))
			}.bind(this));	
		}
		
		this.current_slide = next;		
	},
	
	delayedAppear: function(eSquare)	{
		var opacity = Math.random();
		new Effect.Parallel([ 
			new Effect.Appear ( eSquare, { from: 0, to: opacity, duration: this.options.duration/4 } ),
			new Effect.Appear ( eSquare, { from: opacity, to: 0, duration: this.options.duration/1.25} )
		], { sync: false });			
	}

});
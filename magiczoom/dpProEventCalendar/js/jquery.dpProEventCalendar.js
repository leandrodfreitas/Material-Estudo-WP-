/*
 * jQuery DP Pro Event Calendar v1.2.1
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: http://www.dpereyra.com
 * @Email: info@dpereyra.com
 *
 * Depends:
 * jquery.js
 */
  
(function ($) {
	function DPProEventCalendar(element, options) {
		this.calendar = $(element);
		this.eventDates = $('.dp_pec_date', this.calendar);
		
		/* Setting vars*/
		this.settings = $.extend({}, $.fn.dpProEventCalendar.defaults, options); 
		this.no_draggable = false,
		this.hasTouch = false,
		this.downEvent = "mousedown.rs",
		this.moveEvent = "mousemove.rs",
		this.upEvent = "mouseup.rs",
		this.cancelEvent = 'mouseup.rs',
		this.isDragging = false,
		this.successfullyDragged = false,
		this.startTime = 0,
		this.startMouseX = 0,
		this.startMouseY = 0,
		this.currentDragPosition = 0,
		this.lastDragPosition = 0,
		this.accelerationX = 0,
		this.tx = 0;
		
		// Touch support
		if("ontouchstart" in window) {
					
			this.hasTouch = true;
			this.downEvent = "touchstart.rs";
			this.moveEvent = "touchmove.rs";
			this.upEvent = "touchend.rs";
			this.cancelEvent = 'touchcancel.rs';
		} 
		
		this.init();
	}
	
	DPProEventCalendar.prototype = {
		init : function(){
			var instance = this;
			
			$(instance.calendar).addClass(instance.settings.skin);
			instance._makeResponsive();
			
			$('.prev_month', instance.calendar).click(function(e) { instance._prevMonth(instance); });
			if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.prev_month', instance.calendar).hide();
			}
			$('.next_month', instance.calendar).click(function(e) { instance._nextMonth(instance); });
			if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.next_month', instance.calendar).hide();
			}
			
			/* touch support */
			if(instance.settings.draggable) {
				$('.dp_pec_content', instance.calendar).addClass('isDraggable');
				$('.dp_pec_content', instance.calendar).bind(instance.downEvent, function(e) { 	

					if(!instance.no_draggable) {
						instance.startDrag(e); 	
					} else if(!instance.hasTouch) {							
						e.preventDefault();
					}								
				});	
			}
			
			if(!instance.settings.isAdmin) {
				if($.proCalendar_isVersion('1.7', '<')) {
					$(instance.calendar).on({
						mouseenter:
						   function()
						   {
							   if(!$('.eventsPreviewDiv').length) {
									$('body').append($('<div />').addClass('eventsPreviewDiv'));
							   }
							  
							   $('.eventsPreviewDiv').removeClass('light dark').addClass(instance.settings.skin);
							   
								$('.eventsPreviewDiv').html($('.eventsPreview', $(this)).html());
								
								if($('.eventsPreviewDiv').html() != "") {
									$('.eventsPreviewDiv').show();
								}
						   },
						mouseleave:
						   function()
						   {
								$('.eventsPreviewDiv').html('').hide();
						   }
					   }, '.dp_pec_date:not(.disabled)'
					).bind('mousemove', function(e){
						if($('.eventsPreviewDiv').html() != "") {
							$('.eventsPreviewDiv').css({
							   left:  e.pageX,
							   top:   e.pageY
							});
						}
					});
				} else {
					$('.dp_pec_date:not(.disabled)', instance.calendar).live({
						mouseenter:
						   function()
						   {
							   if(!$('.eventsPreviewDiv').length) {
									$('body').append($('<div />').addClass('eventsPreviewDiv'));
							   }
							  
							   $('.eventsPreviewDiv').removeClass('light dark').addClass(instance.settings.skin);
							   
								$('.eventsPreviewDiv').html($('.eventsPreview', $(this)).html());
								
								if($('.eventsPreviewDiv').html() != "") {
									$('.eventsPreviewDiv').show();
								}
						   },
						mouseleave:
						   function()
						   {
								$('.eventsPreviewDiv').html('').hide();
						   }
					   }
					).bind('mousemove', function(e){
						if($('.eventsPreviewDiv').html() != "") {
							$('.eventsPreviewDiv').css({
							   left:  e.pageX,
							   top:   e.pageY
							});
						}
					});
				}

				if($.proCalendar_isVersion('1.7', '<')) {
					$(instance.calendar).on('mouseup', '.dp_pec_date:not(.disabled)', function(event) {
						
						if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && event.which === 1) {
							
							instance._removeElements();
							
							$.post(ProEventCalendarAjax.ajaxurl, { date: $(this).data('dppec-date'), calendar: instance.settings.calendar, action: 'getEvents', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
								function(data) {
	
									$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
									
									instance.eventDates = $('.dp_pec_date', instance.calendar);
									
									$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
								}
							);	
						}
		
					});
				} else {
					$('.dp_pec_date:not(.disabled)', instance.calendar).live('mouseup', function(event) {
					
						if(!$('.dp_pec_content', instance.calendar).hasClass('isDragging') && event.which === 1) {
							
							instance._removeElements();
							
							$.post(ProEventCalendarAjax.ajaxurl, { date: $(this).data('dppec-date'), calendar: instance.settings.calendar, action: 'getEvents', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
								function(data) {
	
									$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
									
									instance.eventDates = $('.dp_pec_date', instance.calendar);
									
									$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
								}
							);	
						}
		
					});
				}
			}
			
			if($.proCalendar_isVersion('1.7', '<')) {
				$(instance.calendar).on('click', '.dp_pec_date_event_back', function(event) {
					event.preventDefault();
					instance._removeElements();
					
					instance._changeMonth();
				});
			} else {
				$('.dp_pec_date_event_back', instance.calendar).live('click', function(event) {
					event.preventDefault();
					instance._removeElements();
					
					instance._changeMonth();
				});
			}
			
			$('.dp_pec_references', instance.calendar).click(function() {
				$('.dp_pec_references_div', instance.calendar).slideDown('fast');
			});
			
			$('.dp_pec_references_close', instance.calendar).click(function() {
				$('.dp_pec_references_div', instance.calendar).slideUp('fast');
			});
			
			$('.dp_pec_search', instance.calendar).one('click', function(event) {
				$(this).val("");
			});
			
			$('.dp_pec_search_form', instance.calendar).submit(function() {
				if($(this).find('.dp_pec_search').val() != "" && !$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
					instance._removeElements();
					
					$.post(ProEventCalendarAjax.ajaxurl, { key: $(this).find('.dp_pec_search').val(), calendar: instance.settings.calendar, action: 'getSearchResults', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
						function(data) {
							
							$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
							
							instance.eventDates = $('.dp_pec_date', instance.calendar);
							
							$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
						}
					);	
				}
				return false;
			});
			
			if($.proCalendar_isVersion('1.7', '<')) {
				$(instance.calendar).on('click', '.dp_pec_date_event_map', function(event) {
					event.preventDefault();
					$(this).closest('.dp_pec_date_event').find('.dp_pec_date_event_map_iframe').slideDown('fast');
				});
			} else {
				$('.dp_pec_date_event_map', instance.calendar).live('click', function(event) {
					event.preventDefault();
					$(this).closest('.dp_pec_date_event').find('.dp_pec_date_event_map_iframe').slideDown('fast');
				});
			}
		},
		
		_makeResponsive : function() {
			var instance = this;
			
			if(instance.calendar.width() < 500) {
				$(instance.calendar).addClass('dp_pec_400');

				$('.dp_pec_dayname span', instance.calendar).each(function(i) {
					$(this).html($(this).html().substr(0,3));
				});
				
				$('.prev_month strong', instance.calendar).hide();
				$('.next_month strong', instance.calendar).hide();
				
			} else {
				$(instance.calendar).removeClass('dp_pec_400');

				$('.prev_month strong', instance.calendar).show();
				$('.next_month strong', instance.calendar).show();
				
			}
		},
		_removeElements : function () {
			var instance = this;
			
			$('.dp_pec_date,.dp_pec_dayname,.dp_pec_isotope', instance.calendar).fadeOut(500);
			$('.dp_pec_content', instance.calendar).addClass( 'dp_pec_content_loading' );
		},
		
		_prevMonth : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualMonth--;
				instance.settings.actualMonth = instance.settings.actualMonth == 0 ? 12 : (instance.settings.actualMonth);
				instance.settings.actualYear = instance.settings.actualMonth == 12 ? instance.settings.actualYear - 1 : instance.settings.actualYear;
				
				instance._changeMonth();
			}
		},
		
		_nextMonth : function (instance) {
			if(!$('.dp_pec_content', instance.calendar).hasClass( 'dp_pec_content_loading' )) {
				instance.settings.actualMonth++;
				instance.settings.actualMonth = instance.settings.actualMonth == 13 ? 1 : (instance.settings.actualMonth);
				instance.settings.actualYear = instance.settings.actualMonth == 1 ? instance.settings.actualYear + 1 : instance.settings.actualYear;
	
				instance._changeMonth();
			}
		},
		
		_changeMonth : function () {
			var instance = this;
			
			//$('.dp_pec_content', instance.calendar).css({'overflow': 'hidden'});
			$('span.actual_month', instance.calendar).html( instance.settings.monthNames[(instance.settings.actualMonth - 1)] + ' ' + instance.settings.actualYear );

			instance._removeElements();
			
			if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.prev_month', instance.calendar).hide();
			} else {
				$('.prev_month', instance.calendar).show();
			}

			if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {
				$('.next_month', instance.calendar).hide();
			} else {
				$('.next_month', instance.calendar).show();
			}
			
			var date_timestamp = Date.UTC(instance.settings.actualYear, (instance.settings.actualMonth - 1), 1) / 1000;

			$.post(ProEventCalendarAjax.ajaxurl, { date: date_timestamp, calendar: instance.settings.calendar, is_admin: instance.settings.isAdmin, action: 'getDate', postEventsNonce : ProEventCalendarAjax.postEventsNonce },
				function(data) {
					
					$('.dp_pec_content', instance.calendar).removeClass( 'dp_pec_content_loading' ).empty().html(data);
					
					instance.eventDates = $('.dp_pec_date', instance.calendar);
					
					$('.dp_pec_date', instance.calendar).hide().fadeIn(500);
					instance._makeResponsive();
				}
			);	
			
			
		},
		
		_str_pad: function (input, pad_length, pad_string, pad_type) {
			
			var half = '',
				pad_to_go;
		 
			var str_pad_repeater = function (s, len) {
				var collect = '',
					i;
		 
				while (collect.length < len) {
					collect += s;
				}
				collect = collect.substr(0, len);
		 
				return collect;
			};
		 
			input += '';
			pad_string = pad_string !== undefined ? pad_string : ' ';
		 
			if (pad_type != 'STR_PAD_LEFT' && pad_type != 'STR_PAD_RIGHT' && pad_type != 'STR_PAD_BOTH') {
				pad_type = 'STR_PAD_RIGHT';
			}
			if ((pad_to_go = pad_length - input.length) > 0) {
				if (pad_type == 'STR_PAD_LEFT') {
					input = str_pad_repeater(pad_string, pad_to_go) + input;
				} else if (pad_type == 'STR_PAD_RIGHT') {
					input = input + str_pad_repeater(pad_string, pad_to_go);
				} else if (pad_type == 'STR_PAD_BOTH') {
					half = str_pad_repeater(pad_string, Math.ceil(pad_to_go / 2));
					input = half + input + half;
					input = input.substr(0, pad_length);
				}
			}
		 
			return input;
		},
		
		// Start dragging
		startDrag:function(e) {
			var instance = this;
			
			if(!instance.isDragging) {					
				var point;
				if(instance.hasTouch) {
					//parsing touch event
					var currTouches = e.originalEvent.touches;
					if(currTouches && currTouches.length > 0) {
						point = currTouches[0];
						instance.fingerCount = currTouches.length;
					}					
					else {	
						return false;						
					}
				} else {
					point = e;		
					
					if (e.target) el = e.target;
					else if (e.srcElement) el = e.srcElement;

					if(el.toString() !== "[object HTMLEmbedElement]" && el.toString() !== "[object HTMLObjectElement]") {	
						e.preventDefault();						
					}
				}

				instance.isDragging = true;
				
				instance.direction = null;
				instance.fingerData = instance.createFingerData();
				
				// check the number of fingers is what we are looking for, or we are capturing pinches
				if (!instance.hasTouch || (instance.fingerCount === instance.settings.fingers || instance.settings.fingers === "all") || instance.hasPinches()) {
					// get the coordinates of the touch
					instance.fingerData[0].start.x = instance.fingerData[0].end.x = point.pageX;
					instance.fingerData[0].start.y = instance.fingerData[0].end.y = point.pageY;
					startTime = instance.getTimeStamp();
					
					if(instance.fingerCount==2) {
						//Keep track of the initial pinch distance, so we can calculate the diff later
						//Store second finger data as start
						instance.fingerData[1].start.x = instance.fingerData[1].end.x = e.originalEvent.touches[1].pageX;
						instance.fingerData[1].start.y = instance.fingerData[1].end.y = e.originalEvent.touches[1].pageY;
						
						//startTouchesDistance = endTouchesDistance = calculateTouchesDistance(fingerData[0].start, fingerData[1].start);
					}
					
					if (instance.settings.swipeStatus || instance.settings.pinchStatus) {
						//ret = triggerHandler(event, phase);
					}
				}
				else {
					//A touch with more or less than the fingers we are looking for, so cancel
					instance.releaseDrag();
					ret = false; // actualy cancel so we dont register event...
				}
				
				if($.proCalendar_isVersion('1.7', '<')) {
					$(document).on(instance.moveEvent, function(e) { instance.moveDrag(e); })
						.on(instance.upEvent, function(e) { instance.releaseDrag(e); });
				} else {
					$(document).bind(instance.moveEvent, function(e) { instance.moveDrag(e); })
						.bind(instance.upEvent, function(e) { instance.releaseDrag(e); });
				}
				
				startPos = instance.tx = parseInt(instance.eventDates.css("left"), 10);	
				
				instance.successfullyDragged = false;
				instance.accelerationX = this.tx;
				instance.startTime = (e.timeStamp || new Date().getTime());
				instance.startMouseX = point.clientX;
				instance.startMouseY = point.clientY;
			}
			
			if(instance.hasTouch) {
				$('.dp_pec_content', instance.calendar).on(instance.cancelEvent, function(e) { instance.releaseDrag(e); });
			}
			
			return false;	
		},				
		moveDrag:function(e) {	
			var instance = this;
			
			var point;
			if(instance.hasTouch) {	
				if(instance.lockVerticalAxis) {
					return false;
				}	
				
				var touches = e.originalEvent.touches;
				// If touches more then one, so stop sliding and allow browser do default action
				
				if(touches.length > 1) {
					return false;
				}
				
				point = touches[0];	
				
				//e.preventDefault();				
			} else {
				point = e;
				//e.preventDefault();		
			}

			// Helps find last direction of drag move
			instance.lastDragPosition = instance.currentDragPosition;
			var distance = point.clientX - instance.startMouseX;
			if(instance.lastDragPosition != distance) {
				instance.currentDragPosition = distance;
			}

			if(distance != 0)
			{	

				if(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {			
					if(distance > 0) {
						distance = Math.sqrt(distance) * 5;
					}			
				} else if(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin) {		
					if(distance < 0) {
						distance = -Math.sqrt(-distance) * 5;
					}	
				}
				
				$('.dp_pec_content', instance.calendar).addClass('isDragging');
				instance.eventDates.css("left", distance);		
				
			}	
			
			var timeStamp = (e.timeStamp || new Date().getTime());
			if (timeStamp - instance.startTime > 350) {
				instance.startTime = timeStamp;
				instance.accelerationX = instance.tx + distance;						
			}
			
			if(!instance.checkedAxis) {
				
				var dir = true,
					diff = (Math.abs(point.pageX - instance.startMouseX) - Math.abs(point.pageY - instance.startMouseY) ) - (dir ? -7 : 7);

				if(diff > 7) {
					// hor movement
					if(dir) {
						e.preventDefault();
						instance.currMoveAxis = 'x';
					} else if(instance.hasTouch) {
						instance.completeGesture();
						return;
					} 
					instance.checkedAxis = true;
				} else if(diff < -7) {
					// ver movement
					if(!dir) {
						e.preventDefault();
						instance.currMoveAxis = 'y';
					} else if(instance.hasTouch) {
						instance.completeGesture();
						return;
					} 
					instance.checkedAxis = true;
				}
				return;
			}
			
			//Save the first finger data
			instance.fingerData[0].end.x = instance.hasTouch ? point.pageX : e.pageX;
			instance.fingerData[0].end.y = instance.hasTouch ? point.pageY : e.pageY;
			
			instance.direction = instance.calculateDirection(instance.fingerData[0].start, instance.fingerData[0].end);
			
			instance.validateDefaultEvent(instance.direction);
			
			return false;		
		},
		completeGesture: function() {
			var instance = this;
			instance.lockVerticalAxis = true;
			instance.releaseDrag();
		},
		releaseDrag:function(e) {
			var instance = this;
			
			if(instance.isDragging) {	
				var self = this;
				instance.isDragging = false;			
				instance.lockVerticalAxis = false;
				instance.checkedAxis = false;	
				$('.dp_pec_content', instance.calendar).removeClass('isDragging');
				
				var endPos = parseInt(instance.eventDates.css('left'), 10);

				$(document).unbind(instance.moveEvent).unbind(instance.upEvent);					

				if(endPos == instance._startPos) {						
					instance.successfullyDragged = false;
					return;
				} else {
					instance.successfullyDragged = true;
				}
				
				var dist = (instance.accelerationX - endPos);		
				var duration =  Math.max(40, (e.timeStamp || new Date().getTime()) - instance.startTime);
				// For nav speed calculation F=ma :)
				/*
				var v0 = Math.abs(dist) / duration;	
				
				
				var newDist = instance.eventDates.width() - Math.abs(startPos - endPos);
				var newDuration = Math.max((newDist * 1.08) / v0, 200);
				newDuration = Math.min(newDuration, 600);
				*/
				function returnToCurrent() {						
					/*
					newDist = Math.abs(startPos - endPos);
					newDuration = Math.max((newDist * 1.08) / v0, 200);
					newDuration = Math.min(newDuration, 500);
					*/

					$(instance.eventDates).animate(
						{'left': 0}, 
						'fast'
					);
				}
				
				// calculate move direction
				if((startPos - instance.settings.dragOffset) > endPos) {		

					if(instance.lastDragPosition < instance.currentDragPosition) {	
						returnToCurrent();
						return false;					
					}
					
					if(!(instance.settings.dateRangeEnd && instance.settings.dateRangeEnd.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin)) {
						instance._nextMonth(instance);
					} else {
						returnToCurrent();
					}
					
				} else if((startPos + instance.settings.dragOffset) < endPos) {	

					if(instance.lastDragPosition > instance.currentDragPosition) {
						returnToCurrent();
						return false;
					}
					
					if(!(instance.settings.dateRangeStart && instance.settings.dateRangeStart.substr(0, 7) == instance.settings.actualYear+"-"+instance._str_pad(instance.settings.actualMonth, 2, "0", 'STR_PAD_LEFT') && !instance.settings.isAdmin)) {
						instance._prevMonth(instance);
					} else {
						returnToCurrent();
					}

				} else {
					returnToCurrent();
				}
			}

			return false;
		},
		
		/**
		* Checks direction of the swipe and the value allowPageScroll to see if we should allow or prevent the default behaviour from occurring.
		* This will essentially allow page scrolling or not when the user is swiping on a touchSwipe object.
		*/
		validateDefaultEvent : function(direction) {
			if (this.settings.allowPageScroll === "none" || this.hasPinches()) {
				e.preventDefault();
			} else {
				var auto = this.settings.allowPageScroll === true;

				switch (direction) {
					case "left":
						if ((true && auto) || (!auto && this.settings.allowPageScroll != "horizontal")) {
							event.preventDefault();
						}
						break;

					case "right":
						if ((true && auto) || (!auto && this.settings.allowPageScroll != "horizontal")) {
							event.preventDefault();
						}
						break;

					case "up":
						if ((false && auto) || (!auto && this.settings.allowPageScroll != "vertical")) {
							e.preventDefault();
						}
						break;

					case "down":
						if ((false && auto) || (!auto && this.settings.allowPageScroll != "vertical")) {
							e.preventDefault();
						}
						break;
				}
			}

		},
		
		/**
		 * Returns true if any Pinch events have been registered
		 */
		hasPinches : function() {
			return this.settings.pinchStatus || this.settings.pinchIn || this.settings.pinchOut;
		},
		
		createFingerData : function() {
			var fingerData=[];
			for (var i=0; i<=5; i++) {
				fingerData.push({
					start:{ x: 0, y: 0 },
					end:{ x: 0, y: 0 },
					delta:{ x: 0, y: 0 }
				});
			}
			
			return fingerData;
		},
		
		/**
		* Calcualte the angle of the swipe
		* @param finger A finger object containing start and end points
		*/
		caluculateAngle : function(startPoint, endPoint) {
			var x = startPoint.x - endPoint.x;
			var y = endPoint.y - startPoint.y;
			var r = Math.atan2(y, x); //radians
			var angle = Math.round(r * 180 / Math.PI); //degrees

			//ensure value is positive
			if (angle < 0) {
				angle = 360 - Math.abs(angle);
			}

			return angle;
		},
		
		/**
		* Calcualte the direction of the swipe
		* This will also call caluculateAngle to get the latest angle of swipe
		* @param finger A finger object containing start and end points
		*/
		calculateDirection : function(startPoint, endPoint ) {
			var angle = this.caluculateAngle(startPoint, endPoint);

			if ((angle <= 45) && (angle >= 0)) {
				return "left";
			} else if ((angle <= 360) && (angle >= 315)) {
				return "left";
			} else if ((angle >= 135) && (angle <= 225)) {
				return "right";
			} else if ((angle > 45) && (angle < 135)) {
				return "down";
			} else {
				return "up";
			}
		},
		
		/**
		* Returns a MS time stamp of the current time
		*/
		getTimeStamp : function() {
			var now = new Date();
			return now.getTime();
		}
	}
	
	$.fn.dpProEventCalendar = function(options){  

		var dpProEventCalendar;
		this.each(function(){
			
			dpProEventCalendar = new DPProEventCalendar($(this), options);
			
			$(this).data("dpProEventCalendar", dpProEventCalendar);
			
		});
		
		return this;

	}
	
  	/* Default Parameters and Events */
	$.fn.dpProEventCalendar.defaults = {  
		monthNames : new Array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'),
		actualMonth : '',
		actualYear : '',
		skin : 'light',
		lockVertical: true,
		calendar: null,
		dateRangeStart: null,
		dateRangeEnd: null,
		draggable: true,
		isAdmin: false,
		dragOffset: 50,
		allowPageScroll: "vertical",
		fingers: 1
	};  
	
	$.fn.dpProEventCalendar.settings = {}
	
})(jQuery);

/* onShowProCalendar custom event */
 (function($){
  $.fn.extend({ 
    onShowProCalendar: function(callback, unbind){
      return this.each(function(){
        var obj = this;
        var bindopt = (unbind==undefined)?true:unbind; 
        if($.isFunction(callback)){
          if($(this).is(':hidden')){
            var checkVis = function(){
              if($(obj).is(':visible')){
                callback.call();
                if(bindopt){
                  $('body').unbind('click keyup keydown', checkVis);
                }
              }                         
            }
            $('body').bind('click keyup keydown', checkVis);
          }
          else{
            callback.call();
          }
        }
      });
    }
  });
})(jQuery);

(function($) {
/**
 * Used for version test cases.
 *
 * @param {string} left A string containing the version that will become
 *        the left hand operand.
 * @param {string} oper The comparison operator to test against. By
 *        default, the "==" operator will be used.
 * @param {string} right A string containing the version that will
 *        become the right hand operand. By default, the current jQuery
 *        version will be used.
 *
 * @return {boolean} Returns the evaluation of the expression, either
 *         true or false.
 */
$.proCalendar_isVersion = function(left, oper, right) {
    if (left) {
        var pre = /pre/i,
            replace = /[^\d]+/g,
            oper = oper || "==",
            right = right || $().jquery,
            l = left.replace(replace, ''),
            r = right.replace(replace, ''),
            l_len = l.length, r_len = r.length,
            l_pre = pre.test(left), r_pre = pre.test(right);

        l = (r_len > l_len ? parseInt(l) * Math.pow(10, (r_len - l_len)) : parseInt(l));
        r = (l_len > r_len ? parseInt(r) * Math.pow(10, (l_len - r_len)) : parseInt(r));

        switch(oper) {
            case "==": {
                return (true === (l == r && (l_pre == r_pre)));
            }
            case ">=": {
                return (true === (l >= r && (!l_pre || l_pre == r_pre)));
            }
            case "<=": {
                return (true === (l <= r && (!r_pre || r_pre == l_pre)));
            }
            case ">": {
                return (true === (l > r || (l == r && r_pre)));
            }
            case "<": {
                return (true === (l < r || (l == r && l_pre)));
            }
        }
    }

    return false;
}
})(jQuery);
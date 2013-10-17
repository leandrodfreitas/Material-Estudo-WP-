<?php
/*
 * DP Pro Event Calendar
 *
 * Copyright 2012, Diego Pereyra
 *
 * @Web: http://www.dpereyra.com
 * @Email: info@dpereyra.com
 *
 * Base Class
 */

require_once('dates.class.php');

class DpProEventCalendar {
	
	var $nonce;
	var $is_admin = false;
	var $type = 'calendar';
	var $limit = 0;
	var $id_calendar = null;
	var $calendar_obj;
	var $wpdb = null;
	var $datesObj;
	var $widget;
	var $translation = array( 
							'TXT_NO_EVENTS_FOUND' 	=> 'No Events were found.',
							'TXT_ALL_DAY' 			=> 'All Day',
							'TXT_REFERENCES' 		=> 'References',
							'TXT_SEARCH' 			=> 'Search...',
							'TXT_RESULTS_FOR' 		=> 'Results: ',
							'TXT_CURRENT_DATE'		=> 'Current Date',
							'PREV_MONTH' 			=> 'Prev Month',
							'NEXT_MONTH'			=> 'Next Month',
							'DAY_SUNDAY' 			=> 'Sunday',
							'DAY_MONDAY' 			=> 'Monday',
							'DAY_TUESDAY' 			=> 'Tuesday',
							'DAY_WEDNESDAY' 		=> 'Wednesday',
							'DAY_THURSDAY' 			=> 'Thursday',
							'DAY_FRIDAY' 			=> 'Friday',
							'DAY_SATURDAY' 			=> 'Saturday',
							'MONTHS' 				=> array(
														'January',
														'February',
														'March',
														'April',
														'May',
														'June',
														'July',
														'August',
														'September',
														'October',
														'November',
														'December'
													)
					   );
	var $table_calendar;
	
	function DpProEventCalendar( $is_admin = false, $id_calendar = null, $defaultDate = null, $translation = null, $widget = '' ) 
	{
		global $table_prefix;
		
		$this->table_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_CALENDARS;
		$this->table_events = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_EVENTS;
		$this->table_special_dates = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES;
		$this->table_special_dates_calendar = $table_prefix.DP_PRO_EVENT_CALENDAR_TABLE_SPECIAL_DATES_CALENDAR;
		
		$this->widget = $widget;
		if($is_admin) { $this->is_admin = true; }
		if(is_numeric($id_calendar)) { $this->setCalendar($id_calendar); }
		if(!isset($defaultDate)) { $defaultDate = $this->getDefaultDate(); }
		if(isset($translation)) { $this->translation = $translation; }
		
		$this->nonce = rand();
		
		$this->datesObj = new DPPEC_Dates($defaultDate);
    }
	
	function setCalendar($id) {
		$this->id_calendar = $id;	
		
		$this->getCalendarData();
		
		if($this->calendar_obj->lang_month_january != '') {
			$this->translation = array( 
				'TXT_NO_EVENTS_FOUND' 	=> $this->calendar_obj->lang_txt_no_events_found,
				'TXT_ALL_DAY' 			=> $this->calendar_obj->lang_txt_all_day,
				'TXT_REFERENCES' 		=> $this->calendar_obj->lang_txt_references,
				'TXT_SEARCH' 			=> $this->calendar_obj->lang_txt_search,
				'TXT_RESULTS_FOR' 		=> $this->calendar_obj->lang_txt_results_for,
				'TXT_CURRENT_DATE' 		=> $this->calendar_obj->lang_txt_current_date,
				'PREV_MONTH' 			=> $this->calendar_obj->lang_prev_month,
				'NEXT_MONTH'			=> $this->calendar_obj->lang_next_month,
				'DAY_SUNDAY' 			=> $this->calendar_obj->lang_day_sunday,
				'DAY_MONDAY' 			=> $this->calendar_obj->lang_day_monday,
				'DAY_TUESDAY' 			=> $this->calendar_obj->lang_day_tuesday,
				'DAY_WEDNESDAY' 		=> $this->calendar_obj->lang_day_wednesday,
				'DAY_THURSDAY' 			=> $this->calendar_obj->lang_day_thursday,
				'DAY_FRIDAY' 			=> $this->calendar_obj->lang_day_friday,
				'DAY_SATURDAY' 			=> $this->calendar_obj->lang_day_saturday,
				'MONTHS' 				=> array(
											$this->calendar_obj->lang_month_january,
											$this->calendar_obj->lang_month_february,
											$this->calendar_obj->lang_month_march,
											$this->calendar_obj->lang_month_april,
											$this->calendar_obj->lang_month_may,
											$this->calendar_obj->lang_month_june,
											$this->calendar_obj->lang_month_july,
											$this->calendar_obj->lang_month_august,
											$this->calendar_obj->lang_month_september,
											$this->calendar_obj->lang_month_october,
											$this->calendar_obj->lang_month_november,
											$this->calendar_obj->lang_month_december
										)
		   );
	   }
	}
	
	function getNonce() {
		if(!is_numeric($this->id_calendar)) { return false; }
		
		return $this->nonce;
	}
	
	function getDefaultDate() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return time(); }
		
		$default_date;
		$querystr = "
		SELECT default_date
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	
		foreach($calendar_obj as $key=>$value) { $$key = $value; }

		if($default_date == "" || $default_date == "0000-00-00") { $default_date = time(); } else { $default_date = strtotime($default_date); }
		return $default_date;
	}
	
	function getCalendarData() {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar)) { return time(); }
		
		$querystr = "
		SELECT *
		FROM ".$this->table_calendar ."
		WHERE id = ".$this->id_calendar;
		
		$calendar_obj = $wpdb->get_results($querystr, OBJECT);
		$calendar_obj = $calendar_obj[0];	

		$this->calendar_obj = $calendar_obj;
	}
	
	function getCountEventsByDate($date) {
		global $wpdb;

		if(!is_numeric($this->id_calendar)) { return false; }
		$querystr = " SET time_zone = '+00:00'";
		$wpdb->query($querystr);

		$querystr = "
		SELECT count(*) as counter
		FROM ". $this->table_events ."
		WHERE (date LIKE '".$date."%' || 
				('".$date."' BETWEEN date AND end_date AND recurring_frecuency = 1) ||
				((((UNIX_TIMESTAMP('".$date."') - UNIX_TIMESTAMP(DATE(date))) % 7) = 0) AND (UNIX_TIMESTAMP('".$date."') BETWEEN UNIX_TIMESTAMP(date) AND UNIX_TIMESTAMP(end_date)) AND recurring_frecuency = 2) ||
				((DAY('".$date."') = DAY(date)) AND (UNIX_TIMESTAMP('".$date."') BETWEEN UNIX_TIMESTAMP(date) AND UNIX_TIMESTAMP(end_date)) AND recurring_frecuency = 3) ||
				((DAY('".$date."') = DAY(date) && MONTH('".$date."') = MONTH(date)) AND (UNIX_TIMESTAMP('".$date."') BETWEEN UNIX_TIMESTAMP(date) AND UNIX_TIMESTAMP(end_date)) AND recurring_frecuency = 4)
		) AND id_calendar = ".$this->id_calendar;
		
		$result = $wpdb->get_results($querystr, OBJECT);
		$result = $result[0];	
		foreach($result as $key=>$value) { $$key = $value; }

		return $counter;
	}
	
	function monthlyCalendarLayout() 
	{
		$html = '';
		
		if($this->calendar_obj->first_day == 1) {
			if($this->datesObj->firstDayNum == 0) { $this->datesObj->firstDayNum == 7;  }
			$this->datesObj->firstDayNum--;
			
			$html .= '
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_MONDAY'].'</span>
				 </div>';
		} else {
			$html .= '
			     <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_SUNDAY'].'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_MONDAY'].'</span>
				 </div>';
		}
		$html .= '
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_TUESDAY'].'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_WEDNESDAY'].'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_THURSDAY'].'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_FRIDAY'].'</span>
				 </div>
				 <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_SATURDAY'].'</span>
				 </div>
				 ';
		if($this->calendar_obj->first_day == 1) {
			$html .= '
			     <div class="dp_pec_dayname">
						<span>'.$this->translation['DAY_SUNDAY'].'</span>
				 </div>';
		}
		
		$general_count == 0;
		
		if( $this->datesObj->firstDayNum != 6 ) {
			
			for($i = ($this->datesObj->daysInPrevMonth - $this->datesObj->firstDayNum); $i <= $this->datesObj->daysInPrevMonth; $i++) 
			{
				$html .= '
						<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
							<div class="dp_date_head"><span>'.$i.'</span></div>
						</div>';
				
				$general_count++;
			}
			
		}
		
		for($i = 1; $i <= $this->datesObj->daysInCurrentMonth; $i++) 
		{
			$curDate = $this->datesObj->currentYear.'-'.str_pad($this->datesObj->currentMonth, 2, "0", STR_PAD_LEFT).'-'.str_pad($i, 2, "0", STR_PAD_LEFT);
			$countEvents = $this->getCountEventsByDate($curDate);
			if(($this->calendar_obj->date_range_start != '0000-00-00' && $this->calendar_obj->date_range_start != NULL && (strtotime($curDate) < strtotime($this->calendar_obj->date_range_start)) || ( $this->calendar_obj->date_range_end != '0000-00-00' && $this->calendar_obj->date_range_end != NULL && strtotime($curDate) > strtotime($this->calendar_obj->date_range_end))) && !$this->is_admin) {
				$html .= '
					<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
						<div class="dp_date_head"><span>'.$i.'</span></div>
					</div>';
			} else {
				$special_date = "";
				$special_date_obj = $this->getSpecialDates($curDate);
				
				if($special_date_obj->color) {
					$special_date = "style='background-color: ".$special_date_obj->color.";' ";
				}
				
				if($curDate == date("Y-m-d", current_time('timestamp'))) {
					$special_date = "style='background-color: ".$this->calendar_obj->current_date_color.";' ";
				}
				
				$html .= '
					<div class="dp_pec_date '.($general_count % 7 == 0 ? 'first-child' : '').'" data-dppec-date="'.$curDate.'" '.$special_date.'>
						<div class="dp_date_head"><span>'.$i.'</span></div>
						'.($countEvents > 0 ? ($this->calendar_obj->show_x ? '<span class="dp_count_events">X</span>' : '<span class="dp_count_events">'.$countEvents.'</span>') : '').'
						';
				if($this->is_admin) {
					$html .= '
						<div class="dp_manage_special_dates" style="display: none;">
							<div class="dp_manage_sd_head">Special Date</div>
							<select>
								<option value="">None</option>';
								foreach($this->getSpecialDatesList() as $key) {
									$html .= '<option value="'.$key->id.','.$key->color.'" '.($key->id == $special_date_obj->id ? 'selected' : '').'>'.$key->title.'</option>';
								}
					$html .= '
							</select>	
						</div>';
				}
				if($countEvents > 0 && $this->calendar_obj->show_preview) {
					$html .= '
						<div class="eventsPreview">
							<ul>
						';
						$result = $this->getEventsByDate($curDate);
					foreach($result as $event) {
						if($this->calendar_obj->format_ampm) {
							$time = date('h:i A', strtotime($event->date));
						} else {
							$time = date('H:i', strtotime($event->date));				
						}
						$html .= '<li><span>'.$time.'</span>'.$event->title.'</li>';
					}
					$html .= '
							</ul>
						</div>';
				}
				
				$html .= '
					</div>';
			}
			
			$general_count++;
		}
		
		if( $this->datesObj->lastDayNum != ($this->calendar_obj->first_day == 1 ? 7 : 6) ) {
			
			for($i = 1; $i <= ( ($this->calendar_obj->first_day == 1 ? 7 : 6) - $this->datesObj->lastDayNum ); $i++) 
			{
				$html .= '
						<div class="dp_pec_date disabled '.($general_count % 7 == 0 ? 'first-child' : '').'">
							<div class="dp_date_head"><span>'.$i.'</span></div>
						</div>';
				
				$general_count++;
				
			}
			
		}
		$html .= '<div class="clear"></div>';
		return $html;
	}
	
	function getSpecialDates($date) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "
		SELECT sp.id, sp.color 
		FROM ". $this->table_special_dates ." sp
		INNER JOIN ". $this->table_special_dates_calendar ." spc ON spc.special_date = sp.`id`
		WHERE spc.calendar = ".$this->id_calendar." AND spc.`date` = '".$date."' ";
		$result = $wpdb->get_results($querystr, OBJECT);
		
		return $result[0];
	}
	
	function setSpecialDates( $sp, $date ) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "DELETE FROM ". $this->table_special_dates_calendar ." WHERE calendar = ".$this->id_calendar." AND date = '".$date."'; ";
		$result = $wpdb->query($querystr, OBJECT);
		
		if(is_numeric($sp)) {
			$querystr = "INSERT INTO ". $this->table_special_dates_calendar ." (special_date, calendar, date) VALUES ( ".$sp.", ".$this->id_calendar.", '".$date."' );";
			$result = $wpdb->query($querystr, OBJECT);
		}
		
		return;
	}
	
	function getSpecialDatesList() {
		global $wpdb;
		
		$querystr = "
		SELECT * 
		FROM ". $this->table_special_dates ." sp ";
		$result = $wpdb->get_results($querystr, OBJECT);
		
		return $result;
	}
	
	function getEventsByDate($date) {
		global $wpdb;
		
		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = "
		SELECT *
		FROM ". $this->table_events ."
		WHERE (date LIKE '".$date."%' || 
				('".$date."' BETWEEN date AND end_date AND recurring_frecuency = 1) ||
				((((UNIX_TIMESTAMP('".$date."') - UNIX_TIMESTAMP(DATE(date))) % 7) = 0) AND (UNIX_TIMESTAMP('".$date."') BETWEEN UNIX_TIMESTAMP(date) AND UNIX_TIMESTAMP(end_date)) AND recurring_frecuency = 2) ||
				((DAY('".$date."') = DAY(date)) AND (UNIX_TIMESTAMP('".$date."') BETWEEN UNIX_TIMESTAMP(date) AND UNIX_TIMESTAMP(end_date)) AND recurring_frecuency = 3) ||
				((DAY('".$date."') = DAY(date) && MONTH('".$date."') = MONTH(date)) AND (UNIX_TIMESTAMP('".$date."') BETWEEN UNIX_TIMESTAMP(date) AND UNIX_TIMESTAMP(end_date)) AND recurring_frecuency = 4)
		)  AND id_calendar = ".$this->id_calendar."
		ORDER BY DATE_FORMAT(date, '%T') ASC
		";
		
		return $wpdb->get_results($querystr, OBJECT);
	}
	
	function eventsListLayout($date) {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !isset($date)) { return false; }
		
		$querystr = " SET time_zone = '+00:00'";
		$wpdb->query($querystr);
		
		$html = '
			<div class="dp_pec_date_event_head dp_pec_date_event_daily dp_pec_isotope">
				<span>'.$this->parseMysqlDate($date).'</span><a href="" class="dp_pec_date_event_back"></a>
			</div>';
		
		$result = $this->getEventsByDate($date);
		if($this->getCountEventsByDate($date) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			
			$html .= $this->singleEventLayout($result);
			
		}
		
		return $html;
	}
	
	function getSearchResults($key) {
		global $wpdb;

		if(!is_numeric($this->id_calendar) || !isset($key)) { return false; }
		
		$html = '
			<div class="dp_pec_date_event_head dp_pec_date_event_search dp_pec_isotope">
				<span>'.$this->translation['TXT_RESULTS_FOR'].'</span><a href="" class="dp_pec_date_event_back"></a>
			</div>';
		$querystr = "
		SELECT *
		FROM ". $this->table_events ."
		WHERE title LIKE '%".$key."%' AND id_calendar = ".$this->id_calendar."
		ORDER BY date ASC";
		
		$result = $wpdb->get_results($querystr, OBJECT);
		if(count($result) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			
			$html .= $this->singleEventLayout($result, true);
			
		}
		
		return $html;
	}
	
	function singleEventLayout($result, $search = false) {
		$html = "";
		
		foreach($result as $event) {
			if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event->date));
			} else {
				$time = date('H:i', strtotime($event->date));				
			}
			$start_day = date('d', strtotime($event->date));
			$start_month = date('n', strtotime($event->date));
			$start_year = date('Y', strtotime($event->date));
			
			$end_date = '';
			$end_year = '';
			if($event->end_date != "" && $event->end_date != "0000-00-00") {
				$end_day = date('d', strtotime($event->end_date));
				$end_month = date('n', strtotime($event->end_date));
				$end_year = date('Y', strtotime($event->end_date));
				
				$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3).', '.$end_year;
			}
			
			$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
			
			$end_time = "";
			if($event->end_time_hh != "" && $event->end_time_mm != "") { $end_time = str_pad($event->end_time_hh, 2, "0", STR_PAD_LEFT).":".str_pad($event->end_time_mm, 2, "0", STR_PAD_LEFT); }
			
			if($end_time != "") {
				
				if($this->calendar_obj->format_ampm) {
					$end_time_tmp = date('h:i A', strtotime("2000-01-01 ".$end_time.":00"));
				} else {
					$end_time_tmp = date('H:i', strtotime("2000-01-01 ".$end_time.":00"));				
				}
				$end_time = " - ".$end_time_tmp;
			}
			
			
			if($start_year != $end_year) {
				$start_date .= ', '.$start_year;
			}
			
			if($event->all_day) {
				$time = $this->translation['TXT_ALL_DAY'];
			}
			$html .= '
			<div class="dp_pec_date_event '.($search ? 'dp_pec_date_eventsearch' : '').' dp_pec_isotope">';
			if($this->calendar_obj->show_time) {
				if($search) {
					$html .= '<span class="dp_pec_date_event_time">'.$start_date.$end_date.'<br />'.$time.'</span>';
				} else {
					$html .= '<span class="dp_pec_date_event_time">'.$time.$end_time.'</span>';
				}
			}
			$html .= '
				<div class="dp_pec_date_event_icons">';
			if($event->link != '') {
				$html .= '
				<a class="dp_pec_date_event_link" href="'.$event->link.'" target="_blank"></a>';
			}
			if($event->share != '') {
				$html .= '
				<a class="dp_pec_date_event_twitter" href="http://twitter.com/home?status='.urlencode($event->share).'" target="_blank"></a>';
			}
			/*
			if($event->map != '') {
				$html .= '
				
				<a class="dp_pec_date_event_map" href="javascript:void(0);"></a>';
			}
			*/
			$html .= '
				</div>';
			
			$html .= '
				<h1 class="dp_pec_event_title">'.$event->title.'</h1>
				<p class="dp_pec_event_description">
				';
			if($event->map != '') {
				$html .= '
				<iframe class="dp_pec_date_event_map_iframe" width="100%" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&source=s_q&q='.urlencode($event->map).'&ie=UTF8&output=embed"></iframe>';
			}
				$html .= '
					'.nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>')).'
				</p>
			</div>';
		}
		
		return $html;
	}
	
	function upcomingCalendarLayout() {
		global $wpdb;
		
		$html = "";
		
		$querystr = "
		SELECT *
		FROM ". $this->table_events ."
		WHERE (date >= '".date("Y-m-d h:i:s")."' OR (recurring_frecuency > 0 AND (end_date >= '".date("Y-m-d h:i:s")."' OR end_date = '0000-00-00') ) ) AND id_calendar = ".$this->id_calendar. "
		ORDER BY date ASC";
		/*
		
		$result = $wpdb->get_results($querystr, OBJECT);

		if(count($result) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			
			foreach($result as $event) {
				if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event->date));
				} else {
					$time = date('H:i', strtotime($event->date));				
				}
				$start_day = date('d', strtotime($event->date));
				$start_month = date('n', strtotime($event->date));
				
				$end_date = '';
				if($event->end_date != "" && $event->end_date != "0000-00-00") {
					$end_day = date('d', strtotime($event->end_date));
					$end_month = date('n', strtotime($event->end_date));
					
					$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3);
				}
				
				$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
				if($event->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
				}
				$html .= '
				<div class="dp_pec_date_event dp_pec_upcoming dp_pec_isotope">';
				if($this->calendar_obj->show_time) {
					$html .= '
					<span class="dp_pec_date_event_time">'.$start_date.$end_date.'<br />'.$time.'</span>';
				}
				$html .= '
					<div class="dp_pec_date_event_icons">';
				if($event->link != '') {
					$html .= '
					<a class="dp_pec_date_event_link" href="'.$event->link.'" target="_blank"></a>';
				}
				if($event->share != '') {
					$html .= '
					<a class="dp_pec_date_event_twitter" href="http://twitter.com/home?status='.urlencode($event->share).'" target="_blank"></a>';
				}
				$html .= '
					</div>';
				
				$html .= '
					<h1 class="dp_pec_event_title">'.$event->title.'</h1>
					<p class="dp_pec_event_description">
						'.nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>')).'
					</p>
				</div>';
			}
		}
		*/

		$events_obj = $wpdb->get_results($querystr);
		
		if(count($events_obj) == 0) {
			$html .= '
			<div class="dp_pec_date_event dp_pec_isotope">
				<p class="dp_pec_event_no_events">'.$this->translation['TXT_NO_EVENTS_FOUND'].'</p>
			</div>';
		} else {
			$order_events = array();
			foreach($events_obj as $event) {
	
				if($event->recurring_frecuency > 0) {
					switch($event->recurring_frecuency) {
						case 1:
							for($i = 1; $i <= $this->limit; $i++) {
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m"), date("d")+$i, date("y")));
								
								if(strtotime($eventdate) <= strtotime($event->end_date) || $event->end_date == '0000-00-00') {
									$order_events[strtotime($eventdate)] = new stdClass;
									$order_events[strtotime($eventdate)]->date = $eventdate;
									$order_events[strtotime($eventdate)]->end_date = $event->end_date;
									$order_events[strtotime($eventdate)]->title = $event->title;
									$order_events[strtotime($eventdate)]->description = $event->description;
									$order_events[strtotime($eventdate)]->link = $event->link;
									$order_events[strtotime($eventdate)]->share = $event->share;
								}
							}
							break;
						case 2:
							$last_day = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m"), date("d"), date("y")));
							for($i = 1; $i <= $this->limit; $i++) {
	
								$eventdate = date("Y-m-d", strtotime($last_day.' next '.date("l", strtotime($event->date))));
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($last_day)), date("i", strtotime($last_day)), 0, date("m", strtotime($eventdate)), date("d", strtotime($eventdate)), date("y", strtotime($eventdate))));
								$last_day = $eventdate;
	
								if(strtotime($eventdate) <= strtotime($event->end_date) || $event->end_date == '0000-00-00') {
									$order_events[strtotime($eventdate)] = new stdClass;
									$order_events[strtotime($eventdate)]->date = $eventdate;
									$order_events[strtotime($eventdate)]->end_date = $event->end_date;
									$order_events[strtotime($eventdate)]->title = $event->title;
									$order_events[strtotime($eventdate)]->description = $event->description;
									$order_events[strtotime($eventdate)]->link = $event->link;
									$order_events[strtotime($eventdate)]->share = $event->share;
								}
							}
							break;
						case 3:
							for($i = 1; $i <= $this->limit; $i++) {
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m")+(date("d", strtotime($event->date)) < date("d") ? $i : 0), date("d", strtotime($event->date)), date("y", strtotime($event->date))));
								
								if(strtotime($eventdate) <= strtotime($event->end_date) || $event->end_date == '0000-00-00') {
									$order_events[strtotime($eventdate)] = new stdClass;
									$order_events[strtotime($eventdate)]->date = $eventdate;
									$order_events[strtotime($eventdate)]->end_date = $event->end_date;
									$order_events[strtotime($eventdate)]->title = $event->title;
									$order_events[strtotime($eventdate)]->description = $event->description;
									$order_events[strtotime($eventdate)]->link = $event->link;
									$order_events[strtotime($eventdate)]->share = $event->share;
								}
							}
							break;	
						case 4:
							for($i = 1; $i <= $this->limit; $i++) {
								$eventdate = date("Y-m-d H:i:s", mktime(date("H", strtotime($event->date)), date("i", strtotime($event->date)), 0, date("m", strtotime($event->date)), date("d", strtotime($event->date)), date("y")+(date("m", strtotime($event->date)) < date("m") ? $i : 0)));
								
								if(strtotime($eventdate) <= strtotime($event->end_date) || $event->end_date == '0000-00-00') {
									$order_events[strtotime($eventdate)] = new stdClass;
									$order_events[strtotime($eventdate)]->date = $eventdate;
									$order_events[strtotime($eventdate)]->end_date = $event->end_date;
									$order_events[strtotime($eventdate)]->title = $event->title;
									$order_events[strtotime($eventdate)]->description = $event->description;
									$order_events[strtotime($eventdate)]->link = $event->link;
									$order_events[strtotime($eventdate)]->share = $event->share;
								}
							}
							break;
					}
				} else {
				
					$order_events[strtotime($event->date)] = $event;
				}
			}
	
			ksort($order_events);
	
			$event_counter = 1;
			foreach($order_events as $event) {
				if($event_counter > $this->limit) { break; }
				if($this->calendar_obj->format_ampm) {
					$time = date('h:i A', strtotime($event->date));
				} else {
					$time = date('H:i', strtotime($event->date));				
				}
				$start_day = date('d', strtotime($event->date));
				$start_month = date('n', strtotime($event->date));
				
				$end_date = '';
				if($event->end_date != "" && $event->end_date != "0000-00-00") {
					$end_day = date('d', strtotime($event->end_date));
					$end_month = date('n', strtotime($event->end_date));
					
					$end_date = ' / <br />'.$end_day.' '.substr($this->translation['MONTHS'][($end_month - 1)], 0, 3);
				}
				
				$start_date = $start_day.' '.substr($this->translation['MONTHS'][($start_month - 1)], 0, 3);
				if($event->all_day) {
					$time = $this->translation['TXT_ALL_DAY'];
				}
				$html .= '
				<div class="dp_pec_date_event dp_pec_upcoming dp_pec_isotope">';
				if($this->calendar_obj->show_time) {
					$html .= '
					<span class="dp_pec_date_event_time">'.$start_date.$end_date.'<br />'.$time.'</span>';
				}
				$html .= '
					<div class="dp_pec_date_event_icons">';
				if($event->link != '') {
					$html .= '
					<a class="dp_pec_date_event_link" href="'.$event->link.'" target="_blank"></a>';
				}
				if($event->share != '') {
					$html .= '
					<a class="dp_pec_date_event_twitter" href="http://twitter.com/home?status='.urlencode($event->share).'" target="_blank"></a>';
				}
				$html .= '
					</div>';
				
				$html .= '
					<h1 class="dp_pec_event_title">'.$event->title.'</h1>
					<p class="dp_pec_event_description">
						'.nl2br(strip_tags(str_replace("</p>", "<br />", preg_replace("/<p[^>]*?>/", "", $event->description)), '<strong><h1><h2><h3><h4><h5><h6><h7><b><i><em><pre><code><del><ins><img><a><ul><li><ol><blockquote><br><hr><span><p>')).'
					</p>
				</div>';
				$event_counter++;
			}	
		}
		return $html;
	}
	
	function parseMysqlDate($date) {
		
		$dateArr = explode("-", $date);
		$newDate = $dateArr[2] . " " . $this->translation['MONTHS'][($dateArr[1] - 1)] . ", " . $dateArr[0];
		
		return $newDate;
	}
	
	function addScripts( $print = false ) 
	{

		$script='<script type="text/javascript">
		// <![CDATA[
		jQuery(document).ready(function() {
			
			function startProEventCalendar() {
				
				jQuery("#dp_pec_id'.$this->nonce.'").dpProEventCalendar({
					nonce: "dp_pec_id'.$this->nonce.'", 
					monthNames: new Array("'.$this->translation['MONTHS'][0].'", "'.$this->translation['MONTHS'][1].'", "'.$this->translation['MONTHS'][2].'", "'.$this->translation['MONTHS'][3].'", "'.$this->translation['MONTHS'][4].'", "'.$this->translation['MONTHS'][5].'", "'.$this->translation['MONTHS'][6].'", "'.$this->translation['MONTHS'][7].'", "'.$this->translation['MONTHS'][8].'", "'.$this->translation['MONTHS'][9].'", "'.$this->translation['MONTHS'][10].'", "'.$this->translation['MONTHS'][11].'"), ';
				if($this->is_admin) {
					$script .= '
					draggable: false,
					isAdmin: true,
					';
				}
				if(is_numeric($this->id_calendar)) {
					$script .= '
					calendar: '.$this->id_calendar.',
					';	
				}
				if(isset($this->calendar_obj->date_range_start) && $this->calendar_obj->date_range_start != NULL && !$this->is_admin) {
					$script .= '
					dateRangeStart: "'.$this->calendar_obj->date_range_start.'",
					';	
				}
				if(isset($this->calendar_obj->date_range_end) && $this->calendar_obj->date_range_end != NULL && !$this->is_admin) {
					$script .= '
					dateRangeEnd: "'.$this->calendar_obj->date_range_end.'",
					';	
				}
				if(isset($this->calendar_obj->skin) && $this->calendar_obj->skin != "" && !$this->is_admin) {
					$script .= '
					skin: "'.$this->calendar_obj->skin.'",
					';	
				}
				$script .= '
					actualMonth: '.$this->datesObj->currentMonth.',
					actualYear: '.$this->datesObj->currentYear.'
				});
			}
			
			if(jQuery("#dp_pec_id'.$this->nonce.'").parent().css("display") == "none") {
				jQuery("#dp_pec_id'.$this->nonce.'").parent().onShowProCalendar(function(){
					startProEventCalendar();
				});
				return;
			}
			
			startProEventCalendar();
		});
		
		jQuery(window).resize(function(){
			if(jQuery(".dp_pec_layout", "#dp_pec_id'.$this->nonce.'").width() != null) {
	
				var instance = jQuery("#dp_pec_id'.$this->nonce.'");
				
				if(instance.width() < 500) {
					jQuery(instance).addClass("dp_pec_400");
	
					jQuery(".dp_pec_dayname span", instance).each(function(i) {
						jQuery(this).html(jQuery(this).html().substr(0,3));
					});
					
					jQuery(".prev_month strong", instance).hide();
					jQuery(".next_month strong", instance).hide();
					
				} else {
					jQuery(instance).removeClass("dp_pec_400");
					jQuery(".prev_month strong", instance).show();
					jQuery(".next_month strong", instance).show();
					
				}
			}
		});
		//]]>
		</script>';
		
		if($print)
			echo $script;	
		else
			return $script;
		
	}
	
	function output( $print = false ) 
	{
		$width = "";
		$html = "";
		
		if($this->type == 'calendar') {
			
			if(isset($this->calendar_obj->width) && !$this->is_admin && !$this->widget) { $width = 'style="width: '.$this->calendar_obj->width.$this->calendar_obj->width_unity.' " '; }
			
			if($this->is_admin) {
				$html .= '
				<div class="dpProEventCalendar_ModalCalendar">';
			}
			
			$html .= '
			<div class="dp_pec_wrapper" id="dp_pec_id'.$this->nonce.'" '.$width.'>
				<div class="dp_pec_nav">
					<span class="prev_month">&laquo; <strong>'.$this->translation['PREV_MONTH'].'</strong></span>
					<span class="actual_month">'.$this->translation['MONTHS'][($this->datesObj->currentMonth - 1)].' '.$this->datesObj->currentYear.'</span>
					<span class="next_month"><strong>'.$this->translation['NEXT_MONTH'].'</strong> &raquo;</span>
				</div>
			';
			
			if($this->calendar_obj->show_search && !$this->is_admin) {
				$specialDatesList = $this->getSpecialDatesList();
				$html .= '
				<div class="dp_pec_layout">';
				
				
				$html .= '
					<a href="javascript:void(0);" class="dp_pec_references">'.$this->translation['TXT_REFERENCES'].'</a>
					<div class="dp_pec_references_div">
						<a href="javascript:void(0);" class="dp_pec_references_close">x</a>';
						$html .= '
						<div class="dp_pec_references_div_sp">
							<div class="dp_pec_references_color" style="background-color: '.$this->calendar_obj->current_date_color.'"></div>
							<div class="dp_pec_references_title">'.$this->translation['TXT_CURRENT_DATE'].'</div>
							<div style="clear:both;"></div>
						</div>';
				if(count($specialDatesList) > 0) {
					foreach($specialDatesList as $key) {
						$html .= '
						<div class="dp_pec_references_div_sp">
							<div class="dp_pec_references_color" style="background-color: '.$key->color.'"></div>
							<div class="dp_pec_references_title">'.$key->title.'</div>
							<div style="clear:both;"></div>
						</div>';
					}
				}
				$html .= '
					</div>';
				
				$html .= '
					
					<form method="post" class="dp_pec_search_form">
						<input type="text" class="dp_pec_search" value="'.$this->translation['TXT_SEARCH'].'">
						<input type="submit" class="no-replace dp_pec_search_go" value="">
					</form>
					<div style="clear:both;"></div>
				</div>
				';
			}
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
						
			$html .= $this->monthlyCalendarLayout();
			
			$html .= '
								
				</div>
			</div>';
			
			if($this->is_admin) {
				$html .= '
				</div>';
			}
		} elseif($this->type == 'upcoming') {
			
			$html .= '
			<div class="dp_pec_wrapper" id="dp_pec_id'.$this->nonce.'" '.$width.'>
			';
			$html .= '
				<div style="clear:both;"></div>
				
				<div class="dp_pec_content">
					';
						
			$html .= $this->upcomingCalendarLayout();
			
			$html .= '
								
				</div>
			</div>';
			
		}
		
		if($print)
			echo $html;	
		else
			return $html;
		
	}
	
	function switchCalendarTo($type, $limit = 5) {
		if(!is_numeric($limit)) { $limit = 5; }
		$this->type = $type;
		$this->limit = $limit;
	}
	
}
?>
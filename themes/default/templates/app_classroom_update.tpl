<!-- classroom ekleme -->
<style type="text/css">
    <!--
        @import url("{$themePath}css/{$main_jqueryTimePicker_css}");
        -->
</style>
<script type="text/javascript" src="{$themePath}js/{$main_jqueryTimePicker_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_dayTimeTableControl_js}"></script>
<script type="text/javascript">
    /**
     * json metinleri
     *
     * @param strings
     */
    var warningText = '{$app_classroom_update_warningText}';
    
</script>
<script type="text/javascript">
    /**
     * ilk calistirilacaklar
     */
    $(function() {
        /**
         * sinif guncelleme form islemleri
         */
        $('#updateClassroomForm').append('<input type="hidden" name="tc:updateClassroom" value="updateClassroomForm|post" />');
        
           /**
            * uzerinde degisiklik yapilmasi icin acilan sınıfın kod numarasi 
            * hidden form nesnesine ataniyor
            */
           $('#updateClassroomForm').prepend('<input type="hidden" name="code" value="{$classroomValues["code"]}" />');
    
            /**
         * sinif donem suresi durum gizli nesnesi
         */
        $('#updateClassroomForm').append('<input type="hidden" name="termStatus" id="termStatus" />');
    
            /**
         * dayTime kodu için select nesnesi
         */
        $('#updateClassroomForm').append('<input type="hidden" name="dayTimeCodeSelect" id="dayTimeCodeSelect" />');
        
           /**
            * submit kontrol degiskeni
            */
           var submitControlValue = [0,0];
    
        /**
         * sinif kayit formu kontrolleri
         */
        $('#updateClassroomForm #submit').click(function(e) {
    
            $('#updateClassroomForm #name').validator('validate');
            $('#updateClassroomForm #dayTimeCounter').validator('validate');
            
				if (submitControlValue == '1,1') {
                   $('#updateClassroomForm').submit();
                }
		});
           
		$('#updateClassroomForm').live('submit', function() {
            
			$('#timeSelect').val( $('#timeSelect').val());
			$('#endTimeSelect').val( $('#endTimeSelect').val());
			
			if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
				if ($("#instructorPayment").val() < 0) {
					$("#instructorPayment").val(0);
				}
			
				if ($("#instructorPayment").val() > 100) {
					$("#instructorPayment").val(100);
				}
			}
			return true;
        });
        
		$('#updateClassroomForm #name').validator({
               format: 'alphanumeric',
               invalidEmpty: true,
               minLength: 1,
               correct: function() {
                   $.messages.correctMessage('updateClassroomForm','vres_name');
                   submitControlValue[0] = 1;
               },
               error: function() {
                   $.messages.notEmptyMessage('updateClassroomForm','vres_name');
               }
		});
    
        $('#updateClassroomForm #dayTimeCounter').validator({
            format: 'numeric',
            minValue: 1,
            invalidEmpty: true,
            correct: function() {
                submitControlValue[1] = 1;
            },
            error: function() {
                $.messages.notEmptyMessage('updateClassroomForm','vres_dayTimeCounter');
            }
        });
        
		/**
		 * ders zamani secimi
		 */
		$('#timeSelect').timepicker({
		       showLeadingZero: false,
		       onHourShow: tpStartOnHourShowCallback,
		       onMinuteShow: tpStartOnMinuteShowCallback
		}).attr('readonly', true).css('gTextInput');
		   
		$('#endTimeSelect').timepicker({
		       showLeadingZero: false,
		       onHourShow: tpEndOnHourShowCallback,
		       onMinuteShow: tpEndOnMinuteShowCallback
		}).attr('readonly', true).css('gTextInput');
   
		{literal}
			$("#slider").slider({
			   value: 0,
			   min: 0,
			   max: 100,
			   step: 1,
			   slide: function( event, ui ) {
			       $("#instructorPayment").val(ui.value);
				}
			});
		{/literal}
   
        $('#instructorPayment').keyup( function() {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
	            if ($(this).val() > 100) $(this).val('100');
	            if ($(this).val() < 0) $(this).val('0');
	        }
        });
   
        $('#instructorPayment').keydown( function(event) {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
	            if (event.keyCode >= 48 && event.keyCode <= 57) {
	                return true;
	            } else { 
	                if (event.keyCode == 8 || event.keyCode == 9  || event.keyCode >= 96 && event.keyCode <= 105)
	                    return true;
	                else 
	                    return false;
	            }
			}
        });
        
        $('#instructorPaymentPeriod').change(function() {
        	if ($('#instructorPaymentPeriod :selected').val() == 'percentMonthly') {
        		$('#instructorPayment').val(0);
        		$('#instructorPayment').attr('maxlength', 3);
        	} else {
        		$('#instructorPayment').val(0);
        		$('#instructorPayment').attr('maxlength', 5);
        	}
        });
           
        /** 
         * sinif sayisi bilgilendirme alanina uyari yazisi yaziliyor
         */
        $('#dayTimeCounterInfo').html(warningText);
        
        /**
         * sinif listesi alanini gizle
         */
        $('#listingWithHeader').hide();
    
        /**
         * sinif ekleme CLICK fonksiyonu
         */
        $('#addDayTime').click(function() {
            if ($.dayTimeControl()) {
                selectedDay = $("#daySelect :selected").val();
                selectedTime = $("#timeSelect").val();
                selectedEndTime = $("#endTimeSelect").val();
                
                $.dayTimeTableControl.addDayTime(selectedDay, selectedTime, selectedEndTime);
                $('#dayTimeCodeSelect').val( $.ajaxValueGet() );
            }
            $.addUpdateRow();
        });
        
        /**
         * sinif listesinden veri cikartma
         */
        $('[id^="removeBut"]').live("click", function() {
			if ($('#dayTimeListTable tr').length > 3) { 
	            tableIndex = $(this).closest('tr').index();
	            $.removeUpdateRow($(this).attr('id'),tableIndex);
	            $.dayTimeTableControl.removeDayTime(this);
			} else {
				alert("Sınıf en az bir gün/zaman bilgisine sahip olmalıdır!");
			}
        });
    
        /**
         * donem suresi tanımı alanini gizle
         */
        $('#termLimitTable').hide();
                
        /** donem suresi tablo kontrolu */
        $('#termStatusSelect').change(function() {
            if ($('#termStatusSelect').attr("checked") == "checked") {
                $('#termStatus').val("1");
                $('#termLimitTable').slideDown();
            } else {
                $('#termStatus').val("0");
                $('#termLimitTable').slideUp();
            }
        });
        
        /** donem suresi cesidi kontrolu */
        $("input[name='termLimitChooser']").change(function(){
            if ($("input[name='termLimitChooser']:checked").val() == 'countLimit') {
                $('#dateLimitTable :input').attr('disabled', true);
                $('#countLimitTable :input').removeAttr('disabled');
    
                
            } else if ($("input[name='termLimitChooser']:checked").val() == 'dateLimit') {
                $('#countLimitTable :input').attr('disabled', true);
                $('#dateLimitTable :input').removeAttr('disabled');
    
            }
        });
        
        $("#dateLimitCalendar").datepicker({
            showButtonPanel: true,
            dateFormat: 'yy/mm/dd'
        }).attr('readonly', true);
        
        /**
         * select listesin de secili olana gore tabloya satir ekleyen metot
         */
        $.addUpdateRow = function() {
            
            if ($.dayTimeControl()) { 
                // secili olani tabloya ekle
                var dayTimeCode = $("#dayTimeCodeSelect").val();
                var tableRowDay = $("#daySelect :selected").text();
                var tableRowTime = $("#timeSelect").val();
                var tableRowEndTime = $("#endTimeSelect").val();
                var tableRowButtonValue = $('#daySelect :selected').val() + "_" + tableRowTime.replace(/:/g, "") + "_" + tableRowEndTime.replace(/:/g, "") + "_" + dayTimeCode;
                
                tableRowHtml = '<tr>';
                tableRowHtml += '<td>' + tableRowDay + '</td>';
                tableRowHtml += '<td>' + tableRowTime + '</td>';
                tableRowHtml += '<td>' + tableRowEndTime + '</td>';
                tableRowHtml += '<td><a id="removeBut_' + tableRowButtonValue + '" href="javascript:" class="button">X</a></td>';
                tableRowHtml += '</tr>';

                // tabloya ekle 
                $.tableControl.addRow('dayTimeListTable',tableRowHtml);
                
                // gun/saat sayisini gizli form nesnesine ata
                $('#dayTimeCounter').val( $('#dayTimeListTable tr').length-2 );
    
                // sinif sayisi bilgilendirme alanini gizle
                $('#vres_dayTimeCounter, #dayTimeCounterInfo').hide();
                
                // sinif listesi tablosunu veri girisi oldugunda gorunur yap
                if ($('#dayTimeListTable tr').length == 3) { $('#listingWithHeader').slideToggle(); }
                
            } else {
                alert("Aynı gün içerisinde, istediginiz zaman aralıklarında veri girişi zaten var!");
            }
        };
        
        /**
         * tablodan satir kaldiran ve satirdaki bilgiyi listeye ekleyen metot
         */
        $.removeUpdateRow = function(id,tableIndex) {
            
            // butonun tiklanmasiyla gelen satir numarasini al
            // o satiri tablodan sil
            tableRowIndex = $.tableControl.getTableRowOfObj(id);
            
            $.tableControl.removeRow('dayTimeListTable',tableRowIndex);
    
            // sinif sayisini gizli form nesnesine ata
            $('#dayTimeCounter').val( $('#dayTimeListTable tr').length-3 );
    
            // sinif listesi tablosunda veri kalmadiginda gizle
            if ($('#dayTimeListTable tr').length == 3) { $('#listingWithHeader').slideToggle(); }
    
            // sinif sayisi bilgilendirme alanina icerik girisi yap
            if ($('#dayTimeCounter').val() < 1) $('#dayTimeCounterInfo, #vres_dayTimeCounter').show();
                else $('#dayTimeCounterInfo, #vres_dayTimeCounter').hide();
                
        };
        
        /**
         * girilen zaman degerlerini
         * tablodaki linkler araciligi ile karsilastir 
         */
        $.dayTimeControl = function() {
            
            selectedDay = $("#daySelect :selected").val();
            selectedTime = parseInt( $("#timeSelect").val().replace(/:/g, "") );
            selectedEndTime = parseInt( $('#endTimeSelect').val().replace(/:/g, "") );
            
            buttonId = "removeBut_" + selectedDay;
            dayTimeCount = $('#dayTimeCounter').val();
            result = true;
            var count = 0;
            
            if (dayTimeCount > 0 || dayTimeCount != "") {
                   //alert("Total Button : " + $('[id^="' + buttonId + '"]').length);
                   
                $('[id^="' + buttonId + '"]').each(function() {
                    
                        /** bir kere false alirsa, bir daha devam etmesini engelle */
                        if (!result) return false;
                        
                        /** true olmasi durumunda degiskenleri olustur */
                        count++;
                        buttonTime = $(this).attr('id').split('_')[2];
                        buttonEndTime = $(this).attr('id').split('_')[3];
                        //alert(count + " : " + selectedTime+","+selectedEndTime+","+buttonTime+","+buttonEndTime);
                                            
                        /** baslangic zamani listedeki degerlerden hic biri ile ayni olamaz */
                        if (selectedTime == buttonTime) {
                            //alert(selectedTime + "=" + buttonTime);
                            result = false;
                            return false;
                        }
                        
                        /** 
                         * baslangic zamani listedeki degerlerden;
                         * buyuk ise, bitis zamani kucuk veya esit olamaz 
                         * ayrica baslangic zamani bitis zamanindan kucuk olamaz 
                         */
                        if (selectedTime > buttonTime) {
                            if (selectedEndTime <= buttonEndTime) {
                                //alert(selectedTime + ">" + buttonTime + " & " + selectedEndTime + "<=" + buttonEndTime);
                                result = false;
                                return false;
                            }
                            if (selectedTime < buttonEndTime) {
                                //alert(selectedTime + ">" + buttonTime + " & " + selectedTime + "<" + buttonEndTime);
                                   result = false;
                                   return false;
                            }
                        }
                        
                        /** baslangic zamani kucuk ise, bitis zamani buyuk olamaz */
                           if (selectedTime < buttonTime) {
                               if (selectedEndTime > buttonTime) {
                                   //alert(selectedTime + "<" + buttonTime + " & " + selectedEndTime + ">" + buttonTime);
                                   result = false;
                                   return false;
                               }
                           }        
                });
            }
            
            return result;
                
        };
    
       /**
        * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        * UPDATE SAYFASI OTOMATIK ISLEMLERI
        * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        */
        $('#updateClassroomForm #name').val('{$classroomValues["name"]}');
        $('#updateClassroomForm #daySelect').val('1');
        $('#updateClassroomForm #program').val('{$classroomValues["program"]}');
        $('#updateClassroomForm #timeSelect').val('10:00');
        $('#updateClassroomForm #endTimeSelect').val('12:30');
        $('#updateClassroomForm #instructor option[value={$classroomValues["instructor"]}]').attr('selected', 'selected');
        $('#updateClassroomForm #instructorPaymentPeriod option[value={$classroomValues["instructorPaymentPeriod"]}]').attr('selected', 'selected');
        $('#updateClassroomForm #saloon').val('{$classroomValues["saloon"]}');
        $('#updateClassroomForm #startDate').val('{$classroomValues["startDate"]}');
        $('#updateClassroomForm #note').val('{$classroomValues["note"]}');
        
       /**
         * select listelerine gerekli secimleri ayarlayan metodlar
         */ 
        $.dayTimeCodeUpdateIndex = function(theValue) {
            $('#dayTimeCodeSelect').val(theValue).trigger('change');
        }
        $.dayUpdateIndex = function(theValue) {
            $('#daySelect').val(theValue).trigger('change');
        }
        
        $.timeUpdateIndex = function(theValue) {
            if (theValue == "") theValue = $('#timeSelect').val();
            $('#timeSelect').val(theValue).trigger('change');
        }
        
        $.endTimeUpdateIndex = function(theValue) {
            if (theValue == "") theValue = $('#endTimeSelect').val();
            $('#endTimeSelect').val(theValue).trigger('change');
        }
        
        $.termStatusSelectUpdateIndex = function(theValue) {
            if (theValue != undefined)
                $('#termStatusSelect').attr('checked', theValue).trigger('change');
        }
        
        $.countDigitUpdateIndex = function(theValue) {
            $('#countDigit').val(theValue).trigger('change');
        }
        
        $.countTypeUpdateIndex = function(theValue) {
            $('#countType').val(theValue).trigger('change');
        }
        
        $.dateLimitCalendarUpdateIndex = function(theValue) {
            $('#dateLimitCalendar').val(theValue).trigger('change');
        }
        
        $.termLimitChooserUpdateIndex = function(theValue) {
            var $radios = $('input:radio[name=termLimitChooser]');
            $radios.filter("[value='" + theValue + "']").attr('checked', true).trigger('change');
        }
        
        /** sinif sayisi bilgilendirme alanina uyari yazisi yaziliyor */
        $('#dayTimeCounterInfo').html(warningText);
    
        /** Gunler, Baslangic ve Bitis zamanlari array icerisine aktariliyor */
        var splittedDayTimeCodes = new Array({$dayTimeList|@count});
           {foreach from=$dayTimeList key=k item=v}
            splittedDayTimeCodes[{$k}] = '{$v.code}';
           {/foreach} 
           
        var splittedDays = new Array({$dayTimeList|@count});
           {foreach from=$dayTimeList key=k item=v}
            splittedDays[{$k}] = '{$v.day}';
           {/foreach} 
        
        var splittedTimes = new Array({$dayTimeList|@count});
           {foreach from=$dayTimeList key=k item=v}
            splittedTimes[{$k}] = '{$v.time}';
           {/foreach} 
        
        var splittedEndTimes = new Array({$dayTimeList|@count});
           {foreach from=$dayTimeList key=k item=v}
            splittedEndTimes[{$k}] = '{$v.endTime}';
           {/foreach}
           
        var splittedStatus = new Array({$dayTimeList|@count});
           {foreach from=$dayTimeList key=k item=v}
            splittedStatus[{$k}] = '{$v.status}';
           {/foreach} 
    
        /** Gunler, Baslangic ve Bitis zamanlari arrayleri tabloya isleniyor */
        for (i=0; i<splittedDays.length; i++) {
            if (splittedStatus[i] != 'deleted') {
                $.dayTimeCodeUpdateIndex( splittedDayTimeCodes[i] );
                $.dayUpdateIndex( splittedDays[i] );
                $.timeUpdateIndex( splittedTimes[i].slice(0,5) );
                $.endTimeUpdateIndex( splittedEndTimes[i].slice(0,5) );
                $.addUpdateRow();
            }  
        }
        
        /** egitmen odemesi alanlarini doldur */
		$('#updateClassroomForm #instructorPayment').val('{$classroomValues["instructorPayment"]}');
		$("#slider").slider('value', $('#updateClassroomForm #instructorPayment').val());
   
		/** donem bilgileri alanlari dolduruluyor */
		termCountLimit = '{$classroomValues["termCountLimit"]}';
		termDateLimit = '{$classroomValues["termDateLimit"]}';
   
		if (termCountLimit != "") {
			$.termStatusSelectUpdateIndex("checked");
			$.termLimitChooserUpdateIndex("countLimit");
			termCountLimitArray = $.getUpperCaseArray(termCountLimit);
			$('#countDigit').val(termCountLimitArray[0]);
			countTypeValue = termCountLimitArray[1].toLowerCase();
			$.countTypeUpdateIndex(countTypeValue);
    
		} else if (termDateLimit != "0000-00-00 00:00:00") {
			$.termStatusSelectUpdateIndex("checked");
			$.termLimitChooserUpdateIndex("dateLimit");
			$.dateLimitCalendarUpdateIndex(termDateLimit);
    
		} else {
			$.termStatusSelectUpdateIndex();
			$('#termStatus').val("0");
			$.termLimitChooserUpdateIndex("countLimit");
		}
    });
    
    /**
     * Bu kısım TimePicker'ın yazarı François Gélinas'ın sitesindeki örnekten alınmıştır. (This functions by François Gélinas)
     */
	function tpStartOnHourShowCallback(hour) {
		var tpEndHour = $('#endTimeSelect').timepicker('getHour');
		// Check if proposed hour is prior or equal to selected end time hour
		if (hour <= tpEndHour) { return true; }
		// if hour did not match, it can not be selected
		return false;
	}
   
	function tpStartOnMinuteShowCallback(hour, minute) {
		var tpEndHour = $('#endTimeSelect').timepicker('getHour');
		var tpEndMinute = $('#endTimeSelect').timepicker('getMinute');
		// Check if proposed hour is prior to selected end time hour
		if (hour < tpEndHour) { return true; }
		// Check if proposed hour is equal to selected end time hour and minutes is prior
		if ( (hour == tpEndHour) && (minute < tpEndMinute) ) { return true; }
		// if minute did not match, it can not be selected
		return false;
	}

	function tpEndOnHourShowCallback(hour) {
		var tpStartHour = $('#timeSelect').timepicker('getHour');
		// Check if proposed hour is after or equal to selected start time hour
		if (hour >= tpStartHour) { return true; }
		// if hour did not match, it can not be selected
		return false;
	}
   
	function tpEndOnMinuteShowCallback(hour, minute) {
		var tpStartHour = $('#timeSelect').timepicker('getHour');
		var tpStartMinute = $('#timeSelect').timepicker('getMinute');
		// Check if proposed hour is after selected start time hour
		if (hour > tpStartHour) { return true; }
		// Check if proposed hour is equal to selected start time hour and minutes is after
		if ( (hour == tpStartHour) && (minute > tpStartMinute) ) { return true; }
		// if minute did not match, it can not be selected
		return false;
	}

</script>
<div id="classroomUpdateMain">
    <div id="back">
        <p style="margin-right: 10px;">{$app_classroom_update_submitToClassroomText}</p>
        <a id="submitBackButton" class="button editLeft" href="main.php?tab=app_classroom" tabindex="{$tabStart++}">{$app_classroom_update_submitToClassroom}</a>
    </div>
    <div id="form">
        <div id="header" class="header brown bold">{$app_classroom_update_updateClassroomTitle}</div>
        <form method="post" name="updateClassroomForm" id="updateClassroomForm">
            <div id="formInner">
            	
                <div id="formLine">
                	<div id="dayTimeArea">
	                    <div id="selectAdd">
	                        <label for="daySelect">{$app_classroom_update_day}</label>
	                        <select id="daySelect" tabindex="{$tabStart++}" class="gSelect">
			                    {foreach from=$daysOfWeek key=k item=v}
			                    <option value="{$v.no}">{$v.day}</option>
			                    {/foreach}
	                        </select>
	                        <label for="timeSelect">{$app_classroom_update_time}</label>
	                        <input id="timeSelect" type="text" size="1" class="gTextInput" value="00:00" />
	                        <input id="endTimeSelect" type="text" size="1" class="gTextInput" value="23:00" />
	                        <a id="addDayTime" class="button" href="javascript:" tabindex="{$tabStart++}">{$app_classroom_update_addDayTime}</a>
	                        <div id="warningArea" class="pureRed">
	                            <input name="dayTimeCounter" id="dayTimeCounter" type="hidden" value="" />
	                            <span id="dayTimeCounterInfo"></span>
	                            <span id="vres_dayTimeCounter" class="requested"></span>
	                        </div>
	                    </div>
	                    <div id="dayTimes">
	                        <table>
	                            <tr>
	                                <td colspan="3">
	                                    <div id="listingWithHeader">
	                                        {$app_classroom_update_dayTimes}
	                                        <div id="listing">
	                                            <table id="dayTimeListTable">
	                                                <thead>
	                                                    <tr>
	                                                        <th scope="col">{$app_classroom_update_day}</th>
	                                                        <th scope="col">{$app_classroom_update_time}</th>
	                                                        <th scope="col">{$app_classroom_update_endTime}</th>
	                                                        <th scope="col">{$app_classroom_update_process}</th>
	                                                    </tr>
	                                                </thead>
	                                                <tfoot>
	                                                    <tr>
	                                                        <td colspan="4"><em><span id="warningTextArea"></span></em></td>
	                                                    </tr>
	                                                </tfoot>
	                                                <tbody>
	                                            </table>
	                                        </div>
	                                    </div>
	                                </td>
	                            </tr>
	                        </table>
	                    </div>
                    </div>
                </div>

                <div id="formLine">
                    <label for="name" class="requested">{$app_classroom_update_name}</label>
                    <input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput" />
                </div>
                
                <div id="formLine">
                    <label for="program">{$app_classroom_update_program}</label>
                    <select name="program" id="program" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$programsList key=k item=v}
                        <option value="{$k}">{$v}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div id="formLine">
                    <label for="instructor">{$app_classroom_update_instructor}</label>
                    <select name="instructor" id="instructor" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$instructorsList key=k item=v}
                        <option value="{$k}">{$v}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div id="formLine">
	                <label for="instructorPayment">{$app_classroom_update_instructorPayment}</label>
	                <select name="instructorPaymentPeriod" id="instructorPaymentPeriod" tabindex="{$tabStart++}" class="gSelect">
	                	
	                    <option value="percentMonthly">{$app_classroom_update_percentMonthly}</option>
						<option value="fixedMonthly">{$app_classroom_update_fixedMonthly}</option>
						
	                </select>
	                <input id="instructorPayment" name="instructorPayment" type="text" size="2" tabindex="{$tabStart++}" class="gTextInput">
                </div>
                
                <div id="formLine">
                    <label for="saloon">{$app_classroom_update_saloon}</label>
                    <select name="saloon" id="saloon" tabindex="{$tabStart++}" class="gSelect">
                        {foreach from=$saloonsList key=k item=v}
                        <option value="{$k}">{$v}</option>
                        {/foreach}
                    </select>
                </div>
                
                <div id="formLine">
                    Dönem Süresi Tanımla<input id="termStatusSelect" type="checkbox" />
                    <div id="termLimitTable">
                        <div>
                            <input name="termLimitChooser" id="termLimitChooser" type="radio" value="countLimit" />{$app_classroom_update_termCountLimit}
                            <div id="countLimitTable" style="padding-left: 20px">
                                <label for="countDigit">{$app_classroom_update_countDigit}</label>
                                <input id="countDigit" name="countDigit" type="text" size="1" class="gTextInput" />
                                <label for="countType">{$app_classroom_update_countType}</label>
                                <select id="countType" name="countType" class="gSelect">
                                    {foreach from=$termLimitCountTypes key=k item=v}
                                    <option value="{$v}">{$app_classroom_update_{$v}}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div>
                            <input name="termLimitChooser" id="termLimitChooser" type="radio" value="dateLimit" />{$app_classroom_update_termDateLimit}
                            <div id="dateLimitTable" style="padding-left: 20px">
                                <label for="dateLimitCalendar">{$app_classroom_update_dateLimitCalendar}</label>
                                <input id="dateLimitCalendar" name="dateLimitCalendar" type="text" size="8" class="gTextInput" />
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="submitArea" class="buttons">
                    <a id="submit" class="button editRight" href="javascript:" tabindex="{$tabStart++}">{$app_classroom_update_submitRecordLabel}</a>
                </div>
                
                <div id="clear"></div>

            </div>
        </form>
    </div>
</div>
<!-- /classroom ekleme -->
<!-- kisi ekleme -->
<script type="text/javascript" src="{$themePath}js/{$main_listControl_js}"></script>
<script type="text/javascript" src="{$themePath}js/{$main_tableControl_js}"></script>
<script type="text/javascript">
		/**
		 * json metinleri
		 *
		 * @param strings
		 */
		var warningText = '{$app_person_add_warningText}';
		var classInfoText = '{$app_person_add_classInfoText}';
		var noneText = '{$app_person_add_bloodTypeNone}';
		var weekly = '{$app_person_add_paymentPeriod_weekly}';
		var monthly = '{$app_person_add_paymentPeriod_monthly}';
		var monthly3 = '{$app_person_add_paymentPeriod_monthly3}';
		var monthly6 = '{$app_person_add_paymentPeriod_monthly6}';
		var monthly12 = '{$app_person_add_paymentPeriod_monthly12}';
		var position = '{$position}';
		var classroomInfo = $.parseJSON('{$classroomInfo|@json_encode}');

</script>
<script type="text/javascript">
		/**
		 * ilk calistirilacaklar
		 */
		$(function() {
				/**
				 * form ismini aktarabilmek icin gizli degisken yarat
				 */
				$('#addPersonForm').append('<input type="hidden" name="tc:addPerson" value="addPersonForm|post" />');
				/**
				 * kisinin pozisyonunu gizli degiskene aktar 
				 */
				$('#addPersonForm').append('<input type="hidden" name="position" value="{$position}" />');
				/**
				 * veritabaninda hicbir sinif kaydi yok ise sayfa acilirken
				 * sbyRoom seciliyse yan secenekleri disable et
				 */
				if ($("#classList :selected").val() == 'sbyRoom') {
						// tabloda ki sby-room odeme kutusunu disable et
						$('#paymentPeriod').val('0').attr('disabled', 'disabled');
						$('#payment').attr('disabled', 'disabled');
				}
				/**
				 * submit kontrol degiskeni
				 */
				switch (position) {
						case "student":
								var submitControlValue = [0, 0, 0, 0, 0];
								break;
						case "instructor":
								var submitControlValue = [0, 0, 0, 0];
								break;
						case "asistant":
								var submitControlValue = [0, 0, 0, 0];
								break;
						case "secretary":
								var submitControlValue = [0, 0, 0];
								break;
						case "cleaner":
								var submitControlValue = [0, 0, 0];
								break;
				}

				/**
				 * kayit formu kontrolleri
				 */
				$('#addPersonForm #submit').click(function(e) {
						$('#addPersonForm #name').validator('validate');
						$('#addPersonForm #surname').validator('validate');
						$('#addPersonForm #mobilePhone').validator('validate');

						if (position == "student" || position == "instructor" || position == "asistant")
								$('#addPersonForm #email').validator('validate');

						if (position == "student")
								$('#addPersonForm #classCounter').validator('validate');

						switch (position) {
								case "student":
										if (submitControlValue == '1,1,1,1,1') {
												$('#addPersonForm').submit();
										}
										break;
								case "instructor":
										if (submitControlValue == '1,1,1,1') {
												$('#addPersonForm').submit();
										}
										break;
								case "asistant":
										if (submitControlValue == '1,1,1,1') {
												$('#addPersonForm').submit();
										}
										break;
								case "secretary":
										if (submitControlValue == '1,1,1') {
												$('#addPersonForm').submit();
										}
										break;
								case "cleaner":
										if (submitControlValue == '1,1,1') {
												$('#addPersonForm').submit();
										}
										break;
						}
				});

				$('#addPersonForm').live('submit', function() {
						{if $position != "student"}
										$('#classList').removeAttr('name');
						{/if}

						// submit sirasinda gitmesi icin sby-room odeme kutusunun disable ozelligini kapat
						$('[id^="payment_sbyRoom"]').removeAttr('disabled');
						return true;
				});

				$('#addPersonForm #name').validator({
						format: 'alphanumeric',
						invalidEmpty: true,
						minLength: 1,
						correct: function() {
								$.messages.correctMessage('addPersonForm', 'vres_name');
								submitControlValue[0] = 1;
						},
						error: function() {
								$.messages.notEmptyMessage('addPersonForm', 'vres_name');
						}
				});

				$('#addPersonForm #surname').validator({
						format: 'alphanumeric',
						invalidEmpty: true,
						minLength: 1,
						correct: function() {
								$.messages.correctMessage('addPersonForm', 'vres_surname');
								submitControlValue[1] = 1;
						},
						error: function() {
								$.messages.notEmptyMessage('addPersonForm', 'vres_surname');
						}
				});

				$('#addPersonForm #mobilePhone').validator({
						format: 'numeric',
						minLength: 10,
						invalidEmpty: true,
						correct: function() {
								$.messages.correctMessage('addPersonForm', 'vres_mobilePhone');
								submitControlValue[2] = 1;
						},
						error: function() {
								$.messages.notPhoneMessage('addPersonForm', 'vres_mobilePhone');
						}
				});

		{if $position == "student" || $position == "instructor" || $position == "asistant"}
				$('#addPersonForm #email').validator({
						format: 'email',
						invalidEmpty: true,
						minLength: 1,
						correct: function() {
								$.messages.correctMessage('addPersonForm', 'vres_email');
								submitControlValue[3] = 1;
						},
						error: function() {
								$.messages.notEmailMessage('addPersonForm', 'vres_email');
						}
				});
		{/if}

				$('#addPersonForm #classCounter').validator({
						format: 'numeric',
						minValue: 1,
						invalidEmpty: true,
						correct: function() {
								submitControlValue[4] = 1;
						},
						error: function() {
								$.messages.notEmptyMessage('addPersonForm', 'vres_classCounter');
						}
				});

				/**
				 * sinif listesi 'option' listesini hafizala
				 */
				$.listControl.init('classList');

				// sbyRoom icin onay degiskeni
				var sbyRoomStatus = true;

				// splitter ve sbyroom'u cikartarak toplam sinif sayisini bul
				var classListClassCount = $('#classList option').length - 2;

				// listenin icerigindeki toplam satir
				var classListTotalCount = $('#classList option').length;

				// payment hanesine 0 yaziliyor
				$('#payment').val('0');
				
				// paymentInCase hanesine 0 yaziliyor
				$('#paymentInCase').val('0');

				// sinif sayisi bilgilendirme alanina uyari yazisi yaziliyor
				$('#classCounterInfo').html(warningText);

				/**
				 * sinif listesi alanini gizle
				 */
				$('#listingWithHeader').hide();

				/**
				 * sinif ekleme CLICK fonksiyonu
				 */
				$('#addClass').click(function() {
						$.addUpdateRow();
						$('#recordType').val('new').trigger('change');
				});

				/**
				 * sinif listesinden veri cikartma
				 */
				$('[id^="removeBut"]').live("click", function() {
						$.removeUpdateRow($(this).attr('id'));
				});

				/**
				 * dogum gunu secimine tarih secici koy ve sadece 'okunabilir' yap
				 */
				$("#birthDate").datepicker({
						changeMonth: true,
						changeYear: true,
						showAnim: 'slideDown',
						dateFormat: 'yy-mm-dd',
						yearRange: '-90:+0'
				}).attr('readonly', true).css('gTextInput');
				/**
				 * payment alanlari kontrolu
				 *
				 * bos birakmaya calisildiginda 0 yazarak dolduracak
				 */
				$('[id^="payment"]').live("change", function() {
						if ($(this).val() == "") {
								$(this).val('0');
						}
				});
				$('#payment').focus(function() {
						if ($(this).val() == "0") {
								$(this).val('');
						}
				});
				$('#payment').blur(function() {
						if ($(this).val() == "") {
								$(this).val('0');
						}
				});
				/**
				 * sby-room secildi mi? 
				 *
				 * secildi ise diger secenekleri disable et
				 */
				$("#classList").change(function() {
						if ($("#classList :selected").val() == 'sbyRoom') {
								$('#paymentPeriod, #payment').hide();
								$('#recordType').hide();
								$('#recordType').val('new').trigger('change');
						} else {
								$('#paymentPeriod, #payment').removeAttr('disabled');
								$('#paymentPeriod, #payment').show();
								$('#recordType').show();
								/**
								 * startingLecture kutusunu hazirla
								 */			
								$.updateStartingLecture();
						}
				});
				/**
				 * odeme periyodu degisti mi?
				 */
				$('#paymentPeriod').change(function() {
						/**
						 * startingLecture kutusunu hazirla
						 */			
						$.updateStartingLecture();
				});
				
				/**
				 * kayit tipi kontrolu
				 */
				$("#recordType").change(function() {
						if ($(this).val() == 'continue') {
								$('#continue').slideDown();
						} else {
								$('#continue').slideUp();
						}
				});
				/**
				 * alinan odeme alani kontrolu
				 *
				 * bos birakmaya calisildiginda 0 yazarak dolduracak
				 */
				$('#paymentInCase').live("change", function() {
						if ($(this).val() == "") {
								$(this).val('0');
						}
				});
				$('#paymentInCase').focus(function() {
						if ($(this).val() == "0") {
								$(this).val('');
						}
				});
				$('#paymentInCase').blur(function() {
						if ($(this).val() == "") {
								$(this).val('0');
						}
				});
				/**
				 * select listesin de secili olana gore tabloya satir ekleyen metot
				 */
				$.addUpdateRow = function() {
						// secili olani tabloya ekle
						tableRowButtonValue = $("#classList :selected").val();
						tableRowClassName = $("#classList :selected").text();
						tableRowPaymentPeriod = $("#paymentPeriod :selected").val();

						if($('#recordType :selected').val() == 'continue') {
								tableRowRecordType = $('#startingLecture :selected').val() + ',' + $('#paymentInCase').val();
								tableRowRecordTypeText = $('#recordType :selected').text() + '<br>' + $('#paymentInCase').val() + ' {$currency}<br>{$app_person_add_lecture}:' + $('#startingLecture :selected').val();
						} else {
								tableRowRecordType = $('#recordType :selected').val();
								tableRowRecordTypeText = $('#recordType :selected').text();
						}

						tableRowHtml = '<tr>';
						tableRowHtml += '<td>' + tableRowClassName + '</td>';
						/**
						 * odeme methodu secimi icin listeyi olustur ve belirleneni otomatik sec 
						 */
						tableRowHtml += '<td><select style="float:left" name="method_' + tableRowButtonValue + '" id="method_' + tableRowButtonValue + '" class="gSelect">';
						$("#paymentPeriod option").each(function(index) {
								if (tableRowPaymentPeriod == $(this).val())
										isSelected = "SELECTED";
								else
										isSelected = "";
								tableRowHtml += '<option value="' + $(this).val() + '"' + isSelected + '>' + $(this).text() + '</option>';
						});
						tableRowHtml += '</select></td>';

						tableRowHtml += '<td><input name="payment_' + tableRowButtonValue + '" id="payment_' + tableRowButtonValue + '" type="text" value="' + $('#payment').val() + '" class="gTextInput" tabindex="{$tabStart++}" size="4"></td>';
						tableRowHtml += '<td><input name="recordType_' + tableRowButtonValue + '" id="recordType_' + tableRowButtonValue + '" type="hidden" value="' + tableRowRecordType + '">' + tableRowRecordTypeText + '</td>';
						tableRowHtml += '<td><a id="removeBut_' + tableRowButtonValue + '" href="javascript:" class="button">X</a></td>';
						tableRowHtml += '</tr>';

						$.tableControl.addRow('classListTable', tableRowHtml);

						// sinif sayisini gizli form nesnesine ata
						$('#classCounter').val($('#classListTable tr').length - 2);

						// sinif listesi tablosunu veri girisi oldugunda gorunur yap
						if ($('#classListTable tr').length == 3) {
								$('#listingWithHeader').slideToggle();
						}

						// sinif listesinde secileni belirle 
						classListIndex = $("#classList :selected").index();

						// isleme sokulan sinif ise splitter ve sbyroom listeden kaldirilacak

						if ((classListIndex < classListClassCount) && ($('#classList option').length > 1) && (sbyRoomStatus == true)) {
								$.listControl.removeItem('classList', $('#classList option').length - 1);
								$.listControl.removeItem('classList', $('#classList option').length - 1);
								sbyRoomStatus = false;
						}

						// secilen son kalan secenek mi bak, ona gore slide et ardindan da belirlenen secileni kaldir
						// isleme sokulan sbyroom ise slide et kapat ve tabloya sadece onu ekle
						if (($('#classList option').length == 1) || ($("#classList :selected").val() == 'sbyRoom')) {
								$('#selectAdd').slideToggle('normal', function() {
										// secili olani listeden kaldir
										$.listControl.removeItem('classList', classListIndex);
								});

						} else {

								// secili olani listeden kaldir
								$.listControl.removeItem('classList', classListIndex);
								/**
								 * startingLecture kutusunu hazirla
								 */			
								$.updateStartingLecture();
						}

						// isleme sokulan sbyroom ise yan secenekleri disable et ve tabloya odeme icin YOK yaz.
						if ($("#classList :selected").val() == 'sbyRoom') {
								// tabloda ki sby-room odeme kutusunu disable et
								$('[id^="payment_sbyRoom"]').val('0').attr('disabled', 'disabled');

								// tablodaki sby-room satirindaki odeme methodu hanesinin icine NONE yaz
								$('#classListTable tr > td:nth-child(2)').each(function() {
										$(this).html(noneText);
								});
						}

						// varsa sinif listesindeki splitter'i disable et
						$.disableSplitter('classList');

						// sinif sayisi bilgilendirme alanini gizle
						$('#vres_classCounnter, #classCounterInfo').hide();
				};

				/**
				 * tablodan satir kaldiran ve satirdaki bilgiyi listeye ekleyen metot
				 */
				$.removeUpdateRow = function(id) {
						// dugmenin isminden liste VALUE degerini al
						// ilgili degere sahip nesneyi listeye geri ekle
						tableRowValue = $('#' + id).attr('id').split('_')[1];
						$.listControl.restoreItem('classList', tableRowValue);

						if (($('#classList option').length == classListClassCount) || (tableRowValue == 'sbyRoom')) {
								$.listControl.restoreAll('classList');
								sbyRoomStatus = true;
						}
						// butonun tiklanmasiyla gelen satir numarasini al
						// o satiri tablodan sil
						tableRowIndex = $('#' + id).closest('tr').index();
						$.tableControl.removeRow('classListTable', tableRowIndex);

						// sinif sayisini gizli form nesnesine ata
						$('#classCounter').val($('#classListTable tr').length - 3);

						// sinif listesi tablosunda veri kalmadiginda gizle
						if ($('#classListTable tr').length == 3) {
								$('#listingWithHeader').slideToggle();
						}

						// sinif listesinde 1 adet sinif eklenirse geri getir
						if ($('#classList option').length > 0) {
								$('#selectAdd').slideDown();
						}

						// varsa sinif listesindeki splitter'i disable et
						$.disableSplitter('classList');

						// sinif sayisi bilgilendirme alanina icerik girisi yap
						if ($('#classCounter').val() < 1)
								$('#classCounterInfo, #vres_classCounter').show();
						else
								$('#classCounterInfo, #vres_classCounter').hide();

						// sby-room remove edilmis ise paymentPeriod ve payment alanlari disable kaliyor
						// onlari enable et
						if ($("#classList :selected").val() != 'sbyRoom') {
								$('#paymentPeriod, #payment').removeAttr('disabled');
						}
				};

				/**
				 * sinifin period carpanini dondur
				 * 
				 * @author Bulent Gercek <bulentgercek@gmail.com>
				 * @return String
				 */
				$.getPeriodMultiplier = function(period)
				{
						switch (period) {
								case 'weekly': result = 1;
										break;
								case 'monthly': result = 4;
										break;
								case 'monthly3': result = 12;
										break;
								case 'monthly6': result = 24;
										break;
								case 'monthly12': result = 48;
										break;
								case 'yearly': result = 48;
										break;
								case 'fixed' : result = 1;
						}
						return result;
				};
				
				/**
				 * donemlik ders sayisini gonder
				 */
				$.getLessonCount = function(classroomValue, period) {
						if (!classroomInfo[classroomValue]['lessonLimit']) {
								periodMultiplier = $.getPeriodMultiplier(period);
								lessonCountWeekly = classroomInfo[classroomValue]['lessonCountWeekly'];
								result =  periodMultiplier * lessonCountWeekly;
						} else {
								result = classroomInfo[classroomValue]['lessonLimit'];
						}
						return result;
				};
				
				/**
				 *  startingLecture alanını gönderilen limit sayısı kadar doldurur 
				 */
				$.fillStartingLecture = function(count) {
						$('#startingLecture').empty();
						for(i=1; i<=count; i++) {
								$('#startingLecture').append( $('<option></option>').val(i).html(i) );
            }
				};
				
				/**
				 * startingLecture kutusunu guncelle
				 */
				$.updateStartingLecture = function() {
						lessonCount = $.getLessonCount($("#classList :selected").val() ,$("#paymentPeriod :selected").val());
						$.fillStartingLecture(lessonCount);
				}
				
				// varsayilan olarak continue alanini gizle
				$('#continue').hide();
				/**
				 * startingLecture kutusunu hazirla
				 */			
				$.updateStartingLecture();
		});
</script>
<div id="personAdd">
		<div id="photo">
				<img src="{$themePath}images/default_photo.gif" alt="" width="120" height="150" border="1">		
		</div>
		<div id="form">
				<form method="post" name="addPersonForm" id="addPersonForm">
						<div id="header" class="header brown bold">{$app_person_add_title_{$position}}</div>
						<div id="formLine">
								<label for="name" class="requested">{$app_person_add_name}</label>
								<input name="name" type="text" id="name" tabindex="{$tabStart++}" class="gTextInput">
								<span id="vres_name" class="requestedWarning"></span>
						</div>
						<div id="formLine">
								<label for="surname" class="requested">{$app_person_add_surname}</label>
								<input name="surname" type="text" id="surname" tabindex="{$tabStart++}" class="gTextInput">
								<span id="vres_surname" class="requestedWarning"></span>
						</div>
						<div id="formLine">
								<label for="gender">{$app_person_add_gender}</label>
								<select name="gender" id="gender" tabindex="{$tabStart++}" class="gTextInput">
										<option value="man">{$app_person_add_genderMan}</option>
										<option value="woman">{$app_person_add_genderWoman}</option>
								</select>
						</div>
						<div id="formLine">
								<label for="birthDate">{$app_person_add_birthDate}</label>
								<input name="birthDate" type="text" id="birthDate" size="10" tabindex="{$tabStart++}" class="gTextInput">
						</div>
						<div id="formLine">
								<label for="birthPlace">{$app_person_add_birthPlace}</label>
								<input name="birthPlace" type="text" id="birthPlace" tabindex="{$tabStart++}" class="gTextInput">
						</div>
						{if $position == "student"}
								<div id="formLine">
										<label for="job">{$app_person_add_job}</label>
										<input name="job" type="text" id="job" tabindex="{$tabStart++}" class="gTextInput">
								</div>
						{/if}
						<div id="formLine">
								<label for="mobilePhone" class="requested">{$app_person_add_mobilePhone}</label>
								<input name="mobilePhone" type="text" id="mobilePhone" tabindex="{$tabStart++}" class="gTextInput">
								<span id="vres_mobilePhone" class="requestedWarning"></span>
						</div>
						<div id="formLine">
								<label for="email"{if $position == "student" || $position == "instructor" || $position == "asistant"} class="requested" {/if}>{$app_person_add_email}</label>
								<input name="email" type="text" id="email" tabindex="{$tabStart++}" class="gTextInput">
								{if $position == "student" || $position == "instructor" || $position == "asistant"}
										<span id="vres_email" class="requestedWarning"></span>
								{/if}
						</div>
						<div id="formLine">
								<label for="address">{$app_person_add_address}</label>
								<textarea name="address" type="text" id="address" tabindex="{$tabStart++}" class="gTextInput"></textarea>
						</div>
						{if $position == "student" || $position == "instructor" || $position == "asistant"}
								<div id="formLine">
										<label for="height">{$app_person_add_height}</label>
										<input name="height" type="text" id="height" tabindex="{$tabStart++}" class="gTextInput">
										<div id="space"></div>
								</div>
								<div id="formLine">
										<label for="weight">{$app_person_add_weight}</label>
										<input name="weight" type="text" id="weight" tabindex="{$tabStart++}" class="gTextInput">
										<div id="space"></div>
								</div>
								<div id="formLine">
										<label for="shoeSize">{$app_person_add_shoeSize}</label>
										<input name="shoeSize" type="text" id="shoeSize" tabindex="{$tabStart++}" class="gTextInput">
										<div id="space"></div>
								</div>
						{/if}
						<div id="formLine">
								<label for="bloodType">{$app_person_add_bloodType}</label>
								<select name="bloodType" id="bloodType" tabindex="{$tabStart++}" class="gSelect">
										{foreach from=$bloodTypeList key=k item=v}
												<option value="{$v}">{$v}</option>
										{/foreach}
								</select>
						</div>
						<div id="formLine">
								<label for="healthInsurance">{$app_person_add_healthInsurance}</label>
								<input name="healthInsurance" type="text" id="healthInsurance" tabindex="{$tabStart++}" class="gTextInput">
								<div id="space"></div>
						</div>
						{if $position == "student"}
								
								<div id="list">
										
										<div id="selectAdd">
												<label>{$app_person_add_classes} / {$app_person_add_payment}</label>
												
												<div style="margin-top:10px; clear: both;">
														<span class="brownText14">
																<select name="classList" id="classList" tabindex="{$tabStart++}" class="gSelect">
																		{foreach from=$classList key=k item=v}
																				<option value="{$k}">{$v}</option>
																		{/foreach}
																		{if count($classList)>0}
																				<option value="splitter" disabled="disabled">- - - - - - - - - - - - - - - - -</option>
																		{/if}
																		<option value="sbyRoom">{$main_header_sbyRoom}</option>
																</select>
														</span>
																
														<select id="paymentPeriod" tabindex="{$tabStart++}" class="gSelect">
																{foreach from=$paymentPeriodList key=k item=v}
																		<option value="{$v}"{if $v == $app_person_add_defPayPeriod}SELECTED{/if}>{$app_person_add_paymentPeriod_{$v}}</option>
																{/foreach}
														</select>
														
														<input id="payment" type="text" class="gTextInput" tabindex="{$tabStart++}" size="4">
														
														<select id="recordType" tabindex="{$tabStart++}" class="gSelect">
																<option value="new">Yeni</option>
																<option value="continue">Devam</option>
														</select>
												</div>
																
												<div id="continue" style="clear: left; border-top: 1px solid red; border-bottom: 1px solid red; clear: left; padding: 5px; margin-top: 5px; margin-bottom: 5px">
														<label for="paymentInCase">{$app_person_add_paymentInCase}</label>
														<input id="paymentInCase" type="text" tabindex="{$tabStart++}" class="gTextInput" size="4">{$currency}
														<label for="startingLecture">{$app_person_add_startingLecture}</label>
														<select id="startingLecture" tabindex="{$tabStart++}" class="gSelect"></select>
												</div>
																
												<div class="buttons"style="float:left;">
														<a id="addClass" class="button" href="javascript:" tabindex="{$tabStart++}">{$app_person_add_addClass}</a>
												</div>
												
												<div id="warningArea" style="float:left;" class="pureRed">
														<input name="classCounter" id="classCounter" type="hidden" value=""><span id="classCounterInfo"></span><span id="vres_classCounter" class="requested"></span>
												</div>
										</div>
												
										<div id="classes" style="clear:both;">
												<table>
														<tr>
																<td colspan="3">
																		<div id="listingWithHeader">
																				{$app_person_add_classes}
																				<div id="listing">
																						<table id="classListTable">
																								<thead>
																										<tr>
																												<th scope="col">{$app_person_add_class}</th>
																												<th scope="col">{$app_person_add_paymentPeriod}</th>
																												<th scope="col">{$app_person_add_payment}</th>
																												<th scope="col">{$app_person_add_recordType}</th>
																												<th scope="col">{$app_person_add_removeClass}</th>
																										</tr>
																								</thead>
																								<tfoot>
																										<tr>
																												<td colspan="5"><em><span id="warningTextArea"></span></em></td>
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
						{/if}
						<br>
						<div id="clear"></div>
						<div id="formLine">
								<label for="notes">{$app_person_add_notes}</label>
								<textarea name="notes" cols="24" rows="4" id="notes" tabindex="{$tabStart++}" class="gTextInput"></textarea>
						</div>
						<div id="submitArea" class="buttons">
								<a id="submit" class="button add" href="javascript:" tabindex="{$tabStart++}">{$app_person_add_submitRecordLabel_{$position}}</a>
						</div>
						<div id="clear"></div>
				</form>
		</div>
</div>
<!-- /kisi ekleme -->
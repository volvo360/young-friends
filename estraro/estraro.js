// JavaScript Document
$(document).ready(function() {
	
	$(document).on("click", ".saveDraft", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		var $inputs = $("form#borderProtocolForm"+' :input');
		var $url;
		
		$url = "ajax_saveBorderProtocol.php";
		
		var $run = true;
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$inputs.each(function() {
			if($(this).prop('required'))
			{
				if ($(this).is("textarea"))
				{
					$formData[$(this).attr("id")] = tinyMCE.editors[$(this).attr('id')].getContent();
					
					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
					}
				}
				else
				{
					if ($(this).attr("id") !== undefined)
					{
						if ($(this).hasClass("selectpicker"))
						{
							$formData[$(this).attr("id")] = $(this).selectpicker('val');
						}
						else
						{
							console.log($(this).attr("id"));
							console.log($(this).val());

							$formData[$(this).attr("id")] = $(this).val();
						}
						if ($formData[$(this).attr("id")].length == 0)
						{
							$run = false;
						}
					}
				}
				
			}
			
			else if ($(this).is(":text"))
			{
				if ($(this).val().trim().length > 0)
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
				else
				{
					$block = true;
				}
			}
			else if ($(this).is(":checkbox"))
			{
				if ($(this).is(":checked"))
				{
					$formData[$(this).attr("id")] = 1;
				}
				else
				{
					$formData[$(this).attr("id")] = 0;
					$block = true;
				}
			}
			
			else if ($(this).is(":button"))
			{
				//Do nothing
			}
			else if ($(this).is("textarea"))
			{
				$formData[$(this).attr("id")] = tinyMCE.editors[$(this).attr('id')].getContent()
			}
			else if ($(this).attr("id") !== undefined)
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
			
		});
		
		if (!$run)
		{
			alert("Du har missat att ange information i en eller flera fält som behövs!")
		}
		
		console.log($formData);
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".protocolDone", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		var matches = $('[id ^= "meetingDay"]').attr("id").match(/\[(.*?)\]/);

		$formData['protocolDone'] = matches[1];
		
		$url = "ajax_saveBorderProtocol.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$url = "showAjax.php";
			
			$formData['id'] = $("#border_agenda_key").val();
		
			var request2 = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request2.done (function( msg )
			{
				$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);
			});

			request2.fail (function (msg)
			{
				
			});
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".verifyProtocol", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['verifyProtocol'] = $(this).data("replace_protocol");
		
		$url = "ajax_saveBorderProtocol.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$url = "showAjax.php";
			
			$formData['id'] = $("#border_agenda_key").val();
		
			var request2 = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request2.done (function( msg )
			{
				$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);
			});

			request2.fail (function (msg)
			{
				
			});
			
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	function responsive_filemanager_callback(field_id){
		console.log(field_id);
		var url=jQuery('#'+field_id).val();
		alert('update '+field_id+" with "+url);
		//your code
	}
	
	$(document).on("click", ".uploadFilesBorder", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceAgenda'] = $(this).data("replace_agenda");
		
		var $url = "ajax_filesBorder.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#modalHeader").text($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody").html());
			$("#modalFooter").html($(msg).filter("#modalFooter").html());
			
			$("#myModal").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
	
	$(document).on("click", ".publicateAnnualMetting", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceAnnualMetting'] = $(this).data("annualmetting_key");
		
		if (confirm("Vill du verkligen låsa denna dagordning och sända ut en inbjudan till medlemmarna med denna info? Det går inte att ånga denna åtgärd!!!!!"))
		{
			var $url = "ajax_callAnnualMetting.php";
		
			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{

			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
		
	}); 
	
	$(document).on('changed.bs.select', ".selectpicker", function (event) {
		
		var $formData = new Object();
		
		if ($(this).parents('form:first').attr("id") == "accountForm")
		{
			if ($(this).attr("id") === "accountType")
			{
				$formData['accountType'] = $(this).selectpicker("val");
				
				var $url = "ajax_syncAccounting.php";
		
				var request = $.ajax({
					url : $url,
					type: "POST",
					data: $formData,
					cache: false,
					async: false
				});

				request.done (function( msg )
				{
					$("#defaultType").html(msg);
					
					$(".selectpicker").selectpicker();
				});

				request.fail (function (msg)
				{
					$("#status-field").html(msg);
				});
			}
			else if ($(this).attr("id") === "typeIncome")
			{
				$formData['typeIncome'] = $(this).selectpicker("val");
				
				var $url = "ajax_syncAccounting.php";
		
				var request = $.ajax({
					url : $url,
					type: "POST",
					data: $formData,
					cache: false,
					async: false
				});

				request.done (function( msg )
				{
					$("#subSpanIncome").html(msg);
					
					if ($formData['typeIncome'] == "memberFee")
					{
						$("#amount").val('');
					}
					else
					{
						$("#amount").val('50');
					}
					
					$(".selectpicker").selectpicker();
				});

				request.fail (function (msg)
				{
					$("#status-field").html(msg);
				});
			}
			else if ($(this).attr("id") === "typeExpence")
			{
				$formData['typeExpence'] = $(this).selectpicker("val");
				
				if ($formData['typeExpence'] == "domain")
				{
					$("#amount").val("161,25");
				}
				else if ($formData['typeExpence'] == "sms")
				{
					$("#amount").val("200");
				}
				else
				{
					$("#amount").val('');
				}
				
				var $url = "ajax_syncAccounting.php";
		
				var request = $.ajax({
					url : $url,
					type: "POST",
					data: $formData,
					cache: false,
					async: false
				});

				request.done (function( msg )
				{
					$("#subSpanExpence").html(msg);
					
					$(".selectpicker").selectpicker();
				});

				request.fail (function (msg)
				{
					$("#status-field").html(msg);
				});
			}
		}
		
	});
	
	$(document).on("click", ".addPostAccounting", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		var $inputs = $("form#accountForm"+' :input');
		
		var $block = false;
		
		$inputs.each(function() {
			if ($(this).is(":text"))
			{
				if ($(this).val().trim().length > 0)
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
				else
				{
					alert("Minst ett fält är tomt som INTE får vara tomt!!!!");
					
					$block = true;
				}
			}
			else if ($(this).hasClass("selectpicker"))
			{
				$formData[$(this).attr("id")] = $(this).selectpicker("val");
				
				if ($formData[$(this).attr("id")].length == 0)
				{
					alert("Minst ett fält är tomt som INTE får vara tomt!!!!");
					
					$block = true;
				}
				
			}
			else if ($(this).is(":checkbox"))
			{
				if ($(this).is(":checked"))
				{
					$formData[$(this).attr("id")] = 1;
				}
				else
				{
					$formData[$(this).attr("id")] = 0;
				}
			}
			else if ($(this).attr("id"))
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
			
		});
		
		if (!$block)
		{
			var $url = "ajax_savePostAccounting.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				location.reload();
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	}); 
	
	$(document).on("click", ".addVerified", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceAccountYear'] = $(this).data("replace_key");
		
		var $url = "ajax_filesAccount.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#modalHeader").text($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody").html());
			$("#modalFooter").html($(msg).filter("#modalFooter").html());
			
			$("#myModal").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
	
	$(document).on("click", ".closeFiscalYear", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceKey'] = $(this).data("replace_key");
		
		var $targetDiv = $(this).data("target_div");
		
		console.log($targetDiv);
		
		$formData['closeFiscalYear'] = 1;
		
		if (confirm("Är du säker på att du vill stänga bokföringen, detta innebär att du inte kan addera några nya poster i bokföringen för det året?"))
		{
			var $url = "ajax_savePostAccounting.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				$("#"+$targetDiv).html(msg);
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	}); 
	
	$(document).on("click", ".requestRevision", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceKey'] = $(this).data("account_key");
		
		var $targetDiv = $(this).data("target_div");
		
		$formData['revision'] = 1;
		
		var $url = "ajax_savePostAccounting.php";

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#"+$targetDiv).html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
	
	$(document).on("click", ".revisionDone", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceAccountYear'] = $(this).data("account_key");
		
		var $url = "./estraro/ajax_revisionDone.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#modalHeader").text($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody").html());
			$("#modalFooter").html($(msg).filter("#modalFooter").html());
			
			$("#myModal").modal("show");
			
			initTinymce();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
	
	$(document).on("click", ".lockAauditReport", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['lockAauditReport'] = $(this).data("account_key");
		
		if (confirm("Vill du verkligen låsa denna revisionsrapport och bifoga denna till kallelsen för årsmötet?"))
		{		
			var $url = "./estraro/ajax_revisionDone.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				//location.reload();
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	});
	
	$(document).on("click", ".uploadFilesAnnualmeeting", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['replaceAgenda'] = $(this).data("replace_agenda");
		
		var $url = "ajax_filesAnnualmeeting.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#modalHeader").text($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody").html());
			$("#modalFooter").html($(msg).filter("#modalFooter").html());
			
			$("#myModal").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
	 
	$(document).on("click", ".testMember", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		var matches = $(this).attr("id").match(/\[(.*?)\]/);

		if (matches) {
			$formData['replaceTestperiod'] = matches[1];
		}
		
		var $url = "ajax_addTestPeriod.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#collapseNew").html(msg);
			return;
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
	
	$(document).on("click", ".getReportsAnnualmeeting", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		
		
		var $url = "ajax_filesAnnualmeeting.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$formData['replace_annualmeeting'] = $("#syncAnnualmeeting").val();

			$url = "ajax_syncFiles.php";

			var request2 = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request2.done (function( msg )
			{
				//$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);

				$("#agendaAreaFiles").html(msg);
				
				initFancytree(); 
				initTinymce();
				$(".selectpicker").selectpicker();
				
			});

			request2.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
			
			return;
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	}); 
    
    $(document).on("click", ".showVerificatFile", function(event)
    {
        event.preventDefault();
        
        if ($(this).data("show_file").length > 0 )
        {
            var $formData = new Object();

            $formData['tableKey'] = $(this).data("show_file");

            var $url = "ajax_showVertificateFiles.php";

            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
                async: false
            });

            request.done (function( msg )
            {
                $("#modalHeader").text($(msg).filter("#modalHeader").text());
                $("#modalBody").html($(msg).filter("#modalBody").html());
                $("#modalFooter").html($(msg).filter("#modalFooter").html());

                $("#myModal").modal("show");
            });

            request.fail (function (msg)
            {                
                $("#status-field").html(msg);
            });
        }
        else
        {
            return false;
        }
    });
    
    $(document).on("click", ".editAccountingPost", function(event)
    {
        event.preventDefault();
        
        if ($(this).data("replace_key").length > 0 )
        {
            var $formData = new Object();

            $formData['tableKey'] = $(this).data("replace_key");

            var $url = "ajax_showAccountingPost.php";

            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
                async: false
            });

            request.done (function( msg )
            {
                $("#modalHeader").text($(msg).filter("#modalHeader").text());
                $("#modalBody").html($(msg).filter("#modalBody").html());
                $("#modalFooter").html($(msg).filter("#modalFooter").html());

                $(".selectpicker").selectpicker();
                
                $("#myModal").modal("show");
                
                
            });

            request.fail (function (msg)
            {                
                $("#status-field").html(msg);
            });
        }
        else
        {
            return false;
        }
    });
     
    
    $(document).on("click", ".updatePostAccounting", function(event)
    {
        event.preventDefault();
        
        if ($(this).data("replace_table").length > 0 )
        {
            var $formData = new Object();

            $formData['replaceTable'] = $(this).data("replace_table");

            $("form#editAccountPost"+' :input').each(function() {
                if ($(this).attr("id") !== undefined)
                {
                    console.log($(this).attr("id"));
                
                    $formData[$(this).attr("id")] = $(this).val();
                }
                
            });
            
            var $url = "ajax_updateAccountingPost.php";

            var request = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
                async: false
            });

            request.done (function( msg )
            {
                var $url = "showAjax.php";
                var $formData = new Object();
                
                $formData['replaceTable'] = $(".fancyTreeClass:first").data("replace_table");
                var $ajaxTarget = $(".fancyTreeClass:first").data("ajax_target");
                
                $formData['id'] = $("#keyAccountYear").val();
                
                var request2 = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                    async: false
                });

                request2.done (function( msg )
                {
                    $("#"+$ajaxTarget).html(msg);
                    
                    initFancytree(); 
                    initDataTable();
                    initJeditable();
                    initJeditableTextarea();
                    initTinymce();
                    $(".selectpicker").selectpicker();
                    
                    $("#areaAccounting").modal("hide");

                });

                request2.fail (function (msg)
                {                
                    $("#status-field").html(msg);
                });
                
                $("#myModal").modal("hide");
                
            });

            request.fail (function (msg)
            {                
                $("#status-field").html(msg);
            });
        }
        else
        {
            return false;
        }
    });
	
	$(document).on("click", ".paymentMember", function(event){
		
		$formData = new Object();
		
		$url = "ajax_syncPayment.php";
		
		if ($(this).is(":checked"))
		{
			$formData['payment'] = 1;
		}
		else
		{
			$formData['payment'] = -1;
		}
		
		$formData['replaceTable'] = $(this).data("replace_table");
		$formData['id'] = $(this).attr("id");
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
});
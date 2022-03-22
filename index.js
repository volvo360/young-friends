// JavaScript Document
var $blockReg = false;

function initDataTable()
{
	$(".DataTable").each(function(){
		if ( $.fn.dataTable.isDataTable( "#"+$(this).attr("id") ) ) {
			//table = $(this).DataTable().destroy();
			/*table = $(this).DataTable( {
				aaSorting : [],
				"language": {
					"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Swedish.json"
				}
			} );*/
		}
		else 
		{
			table = $(this).DataTable( {
				aaSorting : [],
				"language": {
					"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Swedish.json"
				}
			} );
		}
	});
}

function saveTinyMceEdit(inst) 
{
	if (tinyMCE.activeEditor.isDirty())
	{
        if ($("#infoText").length > 0)
        {
            console.log("Hmmmmm....");
            
            $("#infoText").text($("#updateInfoText").val());
            
            setTimeout(function() { $("#infoText").text($("#defaultInfoText").val()); }, 5000);
        }
        
		var $id, $id2, $tableKey, $text;

		var $formData = new Object();

		$id2 = tinymce.activeEditor.id;

		$id = $id2.replace('[', '\\[');
		$id = $id.replace(']', '\\]');

		//$text = tinymce.activeEditor.getContent({format: 'raw'}); 
		$text = tinymce.activeEditor.getContent({}); 

		$formData[$id2] = $text;

		$tableKey = $("#"+$id).data("replace_table");

		if ($("#"+$id).data("replace_table"))
		{
			$formData['replaceTable'] = $("#"+$id).data("replace_table");
		}
		else
		{
			$formData['replaceTable'] = $("#replaTable").val();
		}

		$url = "../common/syncData.php";

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
}

function initTinymce()
{
	//Remove all active instance of TinyMCE, layout bug else
	tinymce.remove(".tinyMceArea");

	tinymce.init({
		placeholder: $(this).placeholder,
		selector: '.tinyMceArea',
		language: 'sv_SE',	
		plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars',
		imagetools_cors_hosts: ['picsum.photos'],
		menubar: 'file edit view insert format tools table help',
		toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
		toolbar_sticky: true,
		autosave_ask_before_unload: true,
		autosave_interval: '30s',
		autosave_prefix: '{path}{query}-{id}-',
		autosave_restore_when_empty: false,
		autosave_retention: '2m',
		image_advtab: true,
		save_enablewhendirty: true,
		save_onsavecallback: function () 
		{ 
			saveTinyMceEdit();
		},	
		setup: function(editor) {
			editor.on('keyup', function(e) {
				if ($(this).attr("id") == "messageMail")
				{
					if (tinymce.get("messageMail").getContent().length > 0)
					{
						$("#copy2SMS").prop("disabled", false);
						$("#sendMessages").prop("disabled", false);
					}
					else
					{
						$("#copy2SMS").prop("disabled", true);

						var $length = $("#messageSMS").val().length;

						if ($length > 0)
						{
							$("#sendMessages").prop("disabled", false);
						}
						else
						{
							$("#sendMessages").prop("disabled", true);
						}
					}
				}
			});
		 },
	  importcss_append: true,

		init_instance_callback: function (editor) {
			editor.on('blur', function (e) {
				saveTinyMceEdit();
			});
		},

	  height: 300,
	  image_caption: true,
	  quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
	  noneditable_noneditable_class: 'mceNonEditable',
	  toolbar_mode: 'sliding',
	  contextmenu: 'link image imagetools table',
	  content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
		image_advtab: true ,

		image_title: true,
	  /* enable automatic uploads of images represented by blob or data URIs*/
	  automatic_uploads: true,
	  /*
		URL of our upload handler (for more details check: https://www.tiny.cloud/docs/configure/file-image-upload/#images_upload_url)

		here we add custom filepicker only to Image dialog
	  */
		images_upload_url: 'postAcceptor.php',        
	  file_picker_types: 'image',
	  /* and here's our custom image picker*/
	  file_picker_callback: function (cb, value, meta) {
		var input = document.createElement('input');
		input.setAttribute('type', 'file');
		input.setAttribute('accept', 'image/*');

		/*
		  Note: In modern browsers input[type="file"] is functional without
		  even adding it to the DOM, but that might not be the case in some older
		  or quirky browsers like IE, so you might want to add it to the DOM
		  just in case, and visually hide it. And do not forget do remove it
		  once you do not need it anymore.
		*/

		input.onchange = function () {
		  var file = this.files[0];

		  var reader = new FileReader();
		  reader.onload = function () {
			/*
			  Note: Now we need to register the blob in TinyMCEs image blob
			  registry. In the next release this part hopefully won't be
			  necessary, as we are looking to handle it internally.
			*/
			var id = 'blobid' + (new Date()).getTime();
			var blobCache =  tinymce.activeEditor.editorUpload.blobCache;
			var base64 = reader.result.split(',')[1];
			var blobInfo = blobCache.create(id, file, base64);
			blobCache.add(blobInfo);

			/* call the callback and populate the Title field with the file name */
			cb(blobInfo.blobUri(), { title: file.name });
		  };
		  reader.readAsDataURL(file);
		};

		input.click();
	  },
	   link_list: './common/getInternalLinks.js.php',
		link_list: function(success) { // called on link dialog open
		var links = fetchLinkList(); // get link_list data
		success(links); // pass link_list data to TinyMCE
	  },
	 });
}

function urlExists(url)
{
	var http = new XMLHttpRequest();
	http.open('HEAD', url, false);
	http.send();
	return http.status!=404;
}

function initJeditable()
{
	var $url;

	$url = "../common/syncData.php";

	if (!urlExists($url))
	{
		$url = "../"+$url;    
	}

	$(".jeditable").each(function() {

		var $reload_tree;

		var $formData = new Object();

		$formData['replaceTable'] = $(this).data("replace_table");

		if ($(this).data("reload_tree"))
		{
			$formData["reload_tree"] = $(this).data("replace_table");
			$reload_tree = $(this).data("reload_tree");
			$project_key2 = $(this).data("replace_project2");
		}

		if ($(this).data("replace_lang"))
		{
			$formData["lang_code"] = $(this).data("replace_lang");
		}

		if ($(this).data("replace_project"))
		{
			$formData["replaceProject"] = $(this).data("replace_project");
		}

		//var $current_element = $(this).attr("id");

		$(this).editable($url,
		{
			//onblur: 'submit',
			placeholder: '',
			cancel : 'Cancel',
			cssclass : 'custom-class',
			cancelcssclass : 'btn btn-danger',
			select : true,
			submitcssclass : 'btn btn-success',
			submit : 'Save',
			submitdata: function(value, settings) {
				return {
					replaceTable : $(this).data("replace_table"),
					replaceLang : $(this).data("replace_lang"),
					jEditable : 1,
				};
			},
			callback : function(event) 
			{
				if ( typeof $reload_tree !== 'undefined')
				{
					$url = "reloadArea.php";

					if (typeof $project_key2 !== 'undefined')
					{
						$formData["project_key2"] = $project_key2;
					}

					$formData["reload_tree"] = $(this).data("replace_table");


					if (!urlExists($url))
					{
						$url = "../../"+$url;
					}
					if (!urlExists($url))
					{
						$url = "../"+$url;
					}
					if (!urlExists($url))
					{
						$url = "../"+$url;
					}

					var request2 = $.ajax({
						url : $url,
						type: "POST",
						data: $formData,
						cache: false,
						async: false
					});

					request2.done (function( msg )
					{
						console.log("#"+$reload_tree);

						if ( typeof $reload_tree !== 'undefined')
						{
							var tree = $.ui.fancytree.getTree("#"+$reload_tree);
							tree.reload(msg);
						}
						else
						{
							$("#"+$reload_tree).html(msg);
						}
					});

					request2.fail (function (msg)
					{
						$("#status-field").html(msg);
					});
				}
			}
		});
	});
}

function initJeditableTextarea()
{
	var $url;

	$url = "../common/syncData.php";

	if (!urlExists($url))
	{
		$url = "../"+$url;    
	}

	$(".editable_textarea").each(function() {

		var $reload_tree;

		var $formData = new Object();

		$formData['replaceTable'] = $(this).data("replace_table");

		if ($(this).data("reload_tree"))
		{
			$formData["reload_tree"] = $(this).data("replace_table");
			$reload_tree = $(this).data("reload_tree");
			$project_key2 = $(this).data("replace_project2");
		}

		if ($(this).data("replace_lang"))
		{
			$formData["lang_code"] = $(this).data("replace_lang");
		}

		if ($(this).data("replace_project"))
		{
			$formData["replaceProject"] = $(this).data("replace_project");
		}

		//var $current_element = $(this).attr("id");

		$(this).editable($url,
		{
			onblur: 'submit',
			placeholder: '',
			type   : 'textarea',
			//cancel : 'Cancel',
			cssclass : 'custom-class',
			cancelcssclass : 'btn btn-danger',
			select : true,
			submitcssclass : 'btn btn-success',
			//submit : 'Save',
			submitdata: function(value, settings) {
				return {
					replaceTable : $(this).data("replace_table"),
					replaceLang : $(this).data("replace_lang"),
					jEditable : 1,
				};
			},
			callback : function(event) 
			{
				if ( typeof $reload_tree !== 'undefined')
				{
					$url = "reloadArea.php";

					if (typeof $project_key2 !== 'undefined')
					{
						$formData["project_key2"] = $project_key2;
					}

					$formData["reload_tree"] = $(this).data("replace_table");


					if (!urlExists($url))
					{
						$url = "../../"+$url;
					}
					if (!urlExists($url))
					{
						$url = "../"+$url;
					}
					if (!urlExists($url))
					{
						$url = "../"+$url;
					}

					var request2 = $.ajax({
						url : $url,
						type: "POST",
						data: $formData,
						cache: false,
						async: false
					});

					request2.done (function( msg )
					{
						console.log("#"+$reload_tree);

						if ( typeof $reload_tree !== 'undefined')
						{
							var tree = $.ui.fancytree.getTree("#"+$reload_tree);
							tree.reload(msg);
						}
						else
						{
							$("#"+$reload_tree).html(msg);
						}
					});

					request2.fail (function (msg)
					{
						$("#status-field").html(msg);
					});
				}
			}
		});
	});
}

function extract_tree($treeid, $replaceTable)
{
	var tree = $.ui.fancytree.getTree("#"+$treeid);
	var d = tree.toDict(true);

	var $formData = new Object;
	//$formData['tree'] =JSON.stringify(d);
	$formData['tree'] = d;

	if($replaceTable == null)
	{
		$formData['replaceTable'] = $("#"+$treeid).data("replace_table");
	}
	else
	{
		$formData['replaceTable'] = $replaceTable;
	}

	$url = "./common/syncTreeData.php";

	if (!urlExists($url))
	{
		$url = "./common/syncTreeData.php";
	}

	if (!urlExists($url))
	{
		$url = "../common/syncTreeData.php";
	}

	if (!urlExists($url))
	{
		$url = "../../common/syncTreeData.php";
	}

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

function initFancytree()
{
	$.each( $( ".fancyTreeClass" ), function() {

		if ($.ui.fancytree.getTree("#"+$(this).attr("id")))
		{
			return;
		}

		$treeId = $(this).attr("id");

		$(this).fancytree({
			extensions: ["dnd5"],
			treeId : $(this).attr("id"),
			dnd5: {
			// autoExpandMS: 400,
			// preventForeignNodes: true,
			// preventNonNodes: true,
			// preventRecursiveMoves: true, // Prevent dropping nodes on own descendants
			// preventVoidMoves: true, // Prevent dropping nodes 'before self', etc.
			// scroll: true,
			// scrollSpeed: 7,
			// scrollSensitivity: 10,

			// --- Drag-support:

			dragStart: function(node, data) {

				if (node.hasClass("disableDrag"))
				{
					return false;
				}
			  /* This function MUST be defined to enable dragging for the tree.
			   *
			   * Return false to cancel dragging of node.
			   * data.dataTransfer.setData() and .setDragImage() is available
			   * here.
			   */
	//          data.dataTransfer.setDragImage($("<div>hurz</div>").appendTo("body")[0], -10, -10);
			  return true;
			},
			dragDrag: function(node, data) {
			  data.dataTransfer.dropEffect = "move";
			},
			dragEnd: function(node, data) {
				/*console.log(node);
				console.log(data);
				console.log(node.tree);
				console.log(node.data.replace_table);*/
				extract_tree(node.tree._id, node.data.replace_table);
			},

			// --- Drop-support:

			dragEnter: function(node, data) {
			  // node.debug("dragEnter", data);
			  data.dataTransfer.dropEffect = "move";
			  // data.dataTransfer.effectAllowed = "copy";
			  return true;
			},
			dragOver: function(node, data) {
			  data.dataTransfer.dropEffect = "move";
			  // data.dataTransfer.effectAllowed = "copy";
			},
			dragLeave: function(node, data) {
			},
			dragDrop: function(node, data) {
			  /* This function MUST be defined to enable dropping of items on
			   * the tree.
			   */
			  var transfer = data.dataTransfer;

			  node.debug("drop", data);

			  // alert("Drop on " + node + ":\n"
			  //   + "source:" + JSON.stringify(data.otherNodeData) + "\n"
			  //   + "hitMode:" + data.hitMode
			  //   + ", dropEffect:" + transfer.dropEffect
			  //   + ", effectAllowed:" + transfer.effectAllowed);

			  if( data.otherNode ) {
				// Drop another Fancytree node from same frame
				// (maybe from another tree however)
				var sameTree = (data.otherNode.tree === data.tree);

				data.otherNode.moveTo(node, data.hitMode);
			  } else if( data.otherNodeData ) {
				// Drop Fancytree node from different frame or window, so we only have
				// JSON representation available
				node.addChild(data.otherNodeData, data.hitMode);
			  } else {
				// Drop a non-nodenewRelativeFirstName
				node.addNode({
				  title: transfer.getData("text")
				}, data.hitMode);
			  }
			  node.setExpanded();

				//extract_tree($(this), $(this).data("replace_table"))
			}
		  },
			select: function (event, data) {
			if (data.targetType == 'checkbox') 
			{
				//I would like the update to be carried out here, but dosent want to work as intended on the page.
			  //extract_tree($tree_name, $replaceTable);
			}
		  },

			activate: function(event, data) {

				var $formData = new Object;

				var node = data.node;
				var id = data.node.key;

				//$("#"+id).text($("#"+id).text());
			   // $("#"+id).find("b").contents().unwrap();
			   //$.ui.fancytree.getTree("#"+$treeId).reload();

				var $target = $(this).data("ajax_target");

				var treeId = $(this).attr("id");

				if (data.node.data.target_div !== undefined)
				{
					$target = $(this).data("ajax_target");
					if (!$target)
					{
						$target = "ajaxHeaderTree";
					}

					if ($("#"+data.node.data.target_div))
					{
						/*console.log("#"+$target);
						if ($("#"+$target).length > 0)
						{
							console.log("Jippie diven finns.....");
						}

						console.log("#"+data.node.data.target_div);
						if ($("#"+data.node.data.target_div).length > 0)
						{
							console.log("Jippie subdiven finns.....");
						}*/
						$("#"+$target).scrollTop($("#"+data.node.data.target_div).position().top);
					}

					return false;
				}
				else if (data.node.data.modal !== undefined)
				{
					console.log(data.node.data);

					$formData['replaceAgenda'] = data.node.data.replace_agenda;
					$formData['id'] = id;

					var $url = "ajax_show_file.php";

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

					$("myModal").modal("show");

					return false;
				}

				if (data.node.extraClasses == "getTranslationLang")
				{
					if ($("#primaryLang").length > 0)
					{
						$formData['primary'] = $("#primaryLang").selectpicker("val");
						$formData['secondary'] = $("#secondaryLang").selectpicker("val");
					}
				}

				$formData['id'] = id;

				if (data.node.data['replace_table'] !== undefined)
				{
				   $formData['replaceTable'] = data.node.data['replace_table']; 
				}
				else
				{
					$formData['replaceTable'] = $(this).data("replace_table");
				}

				if ($("#projectReplaceKey").length > 0)
				{
					$formData['projectReplaceKey'] = $("#projectReplaceKey").val();
				}

				$url = "showAjax.php";

				var request = $.ajax({
					url : $url,
					type: "POST",
					data: $formData,
					cache: false,
				});

				request.done (function( msg )
				{
					if (data.node.data.badgecontact_messages)
					{
						console.log("Vi ska synka badgeContact_messages");

						$url = "syncBadge.php";

						var request2 = $.ajax({
							url : $url,
							type: "POST",
							data: $formData,
							cache: false,
						});

						request2.done (function( msg )
						{
							$("#badgeContact_messages").html(msg);
						});
					}
					else if (data.node.data.badgesupport_messages)
					{
						console.log("Vi ska synka badgeContact_messages");

						$url = "syncBadge.php";

						var request2 = $.ajax({
							url : $url,
							type: "POST",
							data: $formData,
							cache: false,
						});

						request2.done (function( msg )
						{
							$("#badgeSupport_messages").html(msg);
						});
					}

					$("#"+$target).html(msg);

					initFancytree
					initTinymce();

					initJeditable();
					initJeditableTextarea();

					initDataTable();

					$(".selectpicker").selectpicker();
					$(".selectpicker2").selectpicker({iconBase: 'fa',
						tickIcon: 'fa-check'});

					if ($('#blockSelect').length){
						validateSelectpicker();
					}
					/*if ($.ui.fancytree.getTree("#"+treeId).getNodeByKey(id) !== null)
					{
						$.ui.fancytree.getTree("#"+treeId).getNodeByKey(id).setFocus();
					}*/
				});

				request.fail (function (msg)
				{
					$("#status-field").html(msg);
				});
			  }
		});
	});
}

$(document).ready(function() {
	
	var $headerSMS = "YF ";
	
	initFancytree(); 
    initDataTable();
    initJeditable();
    initJeditableTextarea();
	initTinymce();
	$(".selectpicker").selectpicker();
    
    $( document ).ajaxComplete(function() {
        initFancytree(); 
        initDataTable();
        initJeditable();
        initJeditableTextarea();
		$(".selectpicker").selectpicker();
    });
    
    $(".selectpicker").selectpicker();

    $(function () {
      $('[data-toggle="tooltip"]').tooltip()
    })

	function IsEmail(email) {
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if(!regex.test(email)) {
           return false;
        }else{
           return true;
        }
	  }
    
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };
    
    $(document).on('change', '.syncData', function(event)	
	{
		var $id, $id2, $tableKey, $text, $url;
				
		var $formData = new Object();

        console.log("Vi ska synka data....");
        
        if ($("#infoText").length > 0)
        {
            console.log("Hmmmmm....");
            
            $("#infoText").text($("#updateInfoText").val());
            
            setTimeout(function() { $("#infoText").text($("#defaultInfoText").val()); }, 5000);
        }
        
        if ($(this).data("extra_key"))
        {
            console.log("Hmmmm, vi ska hämta extra data....");
            
            $extraData = $(this).data("extra_key");
        }
        
        
        if ($(this).data("replace_id"))
        {
            $id2 = $(this).data("replace_id");
        }
        else
        {
            $id2 = $(this).attr("id");
        }
        
        if ($(this).data("reload_tree"))
        {
            console.log("Vi ska synka träd.....");
            $reload_tree = $(this).data("reload_tree");
            console.log("Vi ska synka träd....."+$reload_tree);
        }
           
        if ($(this).data("replace_lang"))
        {
            $formData["lang_code"] = $(this).data("replace_lang");
        }

        if ($(this).data("replace_project"))
        {
            $formData["replaceProject"] = $(this).data("replace_project");
        }

		if ($(this).is(':checkbox'))
		{
			if ($(this).is(":checked"))
			{
				$formData[$id2] = 1;
			}
			else
			{
				$formData[$id2] = 0;
			}
		}
		else
		{
			$formData[$id2] = $(this).val();
		}
		
		
		if ($(this).data("replace_table"))
		{
			$formData['replaceTable'] = $(this).data("replace_table");
		}
		else
		{
			$formData['replaceTable'] = $("#replaceTable").val();
		}
        
        if ($(this).data("sync_nav"))
		{
			$sync_nav = $(this).data("sync_nav");
		}
		console.log($formData);
        
        $url = "../common/syncData.php";
        console.log($url);
        
        if (!urlExists($url))
        {
            $url = "./common/syncData.php";
        }
        
		if ($(this).attr("id").startsWith("password"))
		{
			if ($('[id ^= "repPassword"]').val().tim.length > 0)
			{
				if ($(this).val().trim.length >= 6)
				{
					$password = $(this).val().trim.length;
					
					$repPassword = $('[id ^= "repPassword"]').val().tim;
					
					if ($password !== $repPassword)
					{
						alert("Lösenorden är ej samma!");
						return false;
					}
					
					$formData[$(this).attr("id")] = $password;
				}
			}
			else
			{
				return false;
			}
		}
		

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			if ($(msg).filter("#resynctree").length > 0)
			{
                //console.log("Vi ska ladda om träd "+$(msg).filter("#resynctree").val())
                
                
                
				reloadTree($(msg).filter("#resynctree").val(), null, $extraData)
			}
            
            else if ( typeof $reload_tree !== 'undefined')
            {
                if (typeof $extraData !== 'undefined' && $extraData !== null) 
                {
                    $formData["extraData"] = $extraData;
                }
                $formData["reload_tree"] = $reload_tree;
                $url = "./common/resyncTree.php";
                if (!urlExists($url))
                {
                    $url = "../"+$url;
                }
                
                console.log($url);
                if (!urlExists($url))
                {
                    $url = "reloadArea.php";
                }
                if (!urlExists($url))
                {
                    $url = "../../"+$url;
                }
                if (!urlExists($url))
                {
                    $url = "../"+$url;
                }
                if (!urlExists($url))
                {
                    $url = "../"+$url;
                }

                var request2 = $.ajax({
                    url : $url,
                    type: "POST",
                    data: $formData,
                    cache: false,
                    async: false
                });

                request2.done (function( msg )
                {
                    var tree = $.ui.fancytree.getTree("#"+$reload_tree);
                    tree.reload(msg);
                    
                    if ( typeof $sync_nav !== 'undefined')
                    {
                        $formData["sync_nav"] = $sync_nav;
                        
                        var request3 = $.ajax({
                            url : $url,
                            type: "POST",
                            data: $formData,
                            cache: false,
                            async: false
                        });

                        request3.done (function( msg )
                        {
                            $("#"+$sync_nav).html(msg);
                        });
                        
                        request3.fail (function (msg)
                        {
                            $("#status-field").html(msg);
                        });
                    }
                });
                
                request2.fail (function (msg)
                {
                    $("#status-field").html(msg);
                });
            }
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});	
    
    
	
	$(document).on("click", "#regAccount", function(event){
		event.preventDefault();
		
		var $formData = new Object();
		var $block = false;
		var $replaceTable = $(this).data("replace_table");
		var $inputs = $("form#regForm"+' :input');
		var $url;
		
		
		$inputs.each(function() {
			if ($(this).is(":text"))
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
			
		});
		
		if (IsEmail($("#email").val()))
		{
			//Do nothing, valid email	
		}
		else
		{
			$block = true;	
		}
		
		if ($block)
		{
			alert("Du har inte fyllt i alla fält som behövs!")
		}
		else
		{
			$formData['replaceTable']= $replaceTable;
			$url = "addUser.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{	
				
				
				if ($(msg).filter("#status").text() === "ok")
				{
					alert("Första steget i registerings processen är genomförd, kolla din mailbox för nästa steg!");
					$inputs.each(function() {
					$(this).val('');
				});
				}
				else if ($(msg).filter("#status").text() === "existing")
				{
					alert("Ojdå! Du verkar redan ha ett konto i tjänsten, prova med att logga in eller begär ett nytt lösenod!")
				}	
				else
				{
					alert("Oops! Något gick fel vid registreringen, kontakta supporten med dina uppgifter så lägger vi in dessa till dig!")
				}
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	});
	
	
	$(document).on("click", "#resetPassword", function (event){
		event.preventDefault();
		var $url = "ajaxResetPassword.php";

		var $formData = new Object();
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#modalHeader").text($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody").html());
			$("#modalFooter").html($(msg).filter("#modalFooter").html());
			
			$("#reciverEmail").val($("#email").val());
			
			$("#myModal").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", "#processRequest", function(event){
		event.preventDefault();
		
		var $formData = new Object();
		
		var $url = "ajaxResetPassword.php";

		$formData['email'] = $("#reciverEmail").val();
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
			$("#myModal").modal("hide");
			
			return false;
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("submit", '#changePassword', function() {
		console.log($.trim($("#password").val()).length);
		console.log($.trim($("#repPassword").val()).length);
		
		if (($.trim($("#password").val()).length >= 6) && ($.trim($("#repPassword").val()).length >= 6))
		{
			if ($.trim($("#password").val()) === $.trim($("#repPassword").val()))
			{
				return true;
			}
			else
			{
				alert("Lösenorden ej samma!")	;
				return false;
			}
		}
		else
		{
			alert("Fält tomt eller uppfyller ej minkravet på längd!")	;
			
			return false;
		}
	});
    
    
    
    $(document).on("click", ".sendContactForm", function(event){
        event.preventDefault();
        
        var $url, $block;
        
        var $formData = new Object;
        
        var $form;
        
        $form = $(this).data("target_form");
        
        $url = $("#"+$form).attr('action');
        
        $block = false;
        
        $("#"+$form+" :input").each(function () {
            if ($.trim($(this).val()).length == 0 && $(this).attr("id") !== undefined)
            {
                console.log($(this).attr('id'));
                $block = true;
            }
            else if ($(this).attr("id") !== undefined)
            {
                $formData[$(this).attr('id')] = $(this).val();
            }
        });
        
        console.log($block);
        if ($block)
		{
			return false;
		}
		else
		{
			return true;
		}
		
        if (!$block)
        {
			grecaptcha.ready(function () {
                grecaptcha.execute('6LcjdZ0UAAAAACnmmP5s65vXAVUc7KLJxSaDi4lF', { action: 'contact' }).then(function (token) {
                    var recaptchaResponse = document.getElementById('recaptchaResponse');
                    recaptchaResponse.value = token;
			
					$formData['recaptchaResponse'] = token;
					
					var request = $.ajax({
						url : $url,
						type: "POST",
						data: $formData,
						cache: false,
						async: false
					});

					request.done (function( msg )
					{
						 /*$("#"+$form+" :input").each(function () {
							 if ($(this).attr("id") !== "contactLang" && $(this).attr("id") !== "replaceTable" && $(this).attr("id") !== "account")
							 $(this).val('');
						 });*/
						return false;
					});

					request.fail (function (msg)
					{
						$("#status-field").html(msg);
					});
				});	
			});	
        }
        
        else
        {
            alert($("#errorMessage").val());
        }
        return false;
    })
    
   
    $(document).on('changed.bs.select', ".selectpicker", function (event){
        
		var $formData = new Object;

		if ($(this).parents('form:first').attr("id") == "accountForm")
		{
			return false;
		}
		
        $formData['replaceTable'] = $(this).data("replace_table");
        $formData[$(this).attr("id")] = $(this).selectpicker('val');
		
        if ($(this).attr("id").startsWith("editActivity"))
        {
			var $targetDiv = $(this).data("target_div");
			
			$url = "ajax_editActivity.php";
			
				var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$("#"+$targetDiv).html($(msg).filter("#modalBody"));
				
				initFancytree(); 
				initDataTable();
				initJeditable();
				initJeditableTextarea();
				initTinymce();
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
			
            return false;
        }
		else if ($(this).attr("id").startsWith("selectActivity"))
        {
			var $targetDiv = $(this).data("target_div");
			
			$formData['selectActivity'] = $(this).selectpicker("val");
			
			$url = "ajax_editTornamentResult.php";
			
				var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$("#"+$targetDiv).html($(msg));
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
			
            return false;
        }
		else if ($(this).attr("id").startsWith("predefinedPlace"))
        {
			$("[id ^= plats]").val($('#predefinedPlace option:selected').text());
			
            return false;
        }
        
		$url = "../common/syncData.php";
        

		if (!urlExists($url))
		{
			$url = "../"+$url;
		}
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });

    $(document).on("click", "#loginUser", function(event)            
    {
		console.log($formData);
		
        event.preventDefault();
        
        $formId = $(this).parents('form:first').attr("id");
        
        var $formData = new Object();
        
        $("#"+$formId+" :input").each(function () {
            if ($(this).attr("id") !== undefined)
            {
                if ($(this).attr('type') == "text" || $(this).attr('type') == "password" )
                {
                    $formData[$(this).attr("id")] = $(this).val().trim();
                }
                else if ($(this).is(':checkbox'))
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
            }
        });
        

		console.log($formData);
		
		$url = "ajaxLogin.php";

        var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
		});

		request.done (function( msg )
		{
            msg = msg.replace(/(<([^>]+)>)/ig,"");
            
			if (msg == "OK")
            {
                window.location = 'index.php#activities';
    			//window.location.reload(true);
            }
            else
            {
                alert(msg);
            }
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
    });
    
    $(document).on("click", ".addToTree", function (event){
        event.preventDefault();
        var $formData = new Object();
        
		$target_tree = $(this).data("target_tree");
		$replaceTable = $(this).data("replace_table");
		$form_id = $(this.form).attr("id");
        $this = $(this);
        
        if ($("#projectReplaceKey").length > 0)
        {
            $formData['projectReplaceKey'] = $("#projectReplaceKey").val();
        }
		
        if ($(this).data("replace_lang"))
        {
            $formData['replace_lang'] = $(this).data("replace_lang");
        }
        
        if ($(this).data("nav_tab"))
        {    
            $navTabId = $(this).data("nav_tab_id"); 
            
            $syncNavTab = $(this).data("nav_tab");
        }
        
		$block = false;
		
        $url = "../common/addDataTree.php";
        
        if (!urlExists($url))
		{
            $url = "./common/"+$url;
        }
        
		var $inputs = $("form#"+$form_id+' :input');

        $inputs.each(function() {
			if( $(this).is('input:text') ) 
			{
                if ($(this).val().trim().length > 0)
                {
                    $formData[$(this).attr("id")] = $(this).val();
                }
			}
            else if( $(this).is('input:hidden') ) 
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
			else if ($(this).is(':checkbox') )
			{
				if ($(this).is(":checked"))
				{
					$addChildNode = true;
				}
				else
				{
					$addChildNode = false;
				}
			}
            else if ($(this).hasClass("selectpicker2"))
            {
                if ($(this).selectpicker("val") !== "-1")
                {
                    if ('note' in $formData && $formData['note'].length !== 0)
                    {
                        alert("Du kan inte addera egen nod och samtidigt valt ett menyalternativ!!!!");
                        $block = true;
                    }
                    
                    $formData[$(this).attr("id")] = $(this).selectpicker("val");
                }
            }
			else if( $(this).attr("id") ) 
			{
                if ($(this).val().trim().length > 0)
                {
                    $formData[$(this).attr("id")] = $(this).val();
                }
			}
        });
        
        console.log($formData);
        
        if ($("#"+$target_tree).hasClass("treeSpecial"))
        {
            var tree = $.ui.fancytree.getTree("#"+$target_tree);
            node = tree.getActiveNode();
            
            if (node.extraClasses == "disableDrag")
            {
                $block = true;
                
                alert("Du kan inte addera en ny post under denna \"nod\"!!!!");
            }
        }
		
		if (!$block)
		{
			if(typeof $addChildNode === 'undefined')
			{
				$addChildNode = false;
			}

			$formData['replaceTable'] = $replaceTable;

			if ('note' in $formData)
			{
				$text = $formData['note'];
                
				if ($text.trim().length == 0)
				{
                    console.log("2052 What the fuck happend!!!!")
					return false;
				}
			}
			else if ('question' in $formData)
			{
				$text = $formData['question'];
                
				if ($text.trim().length == 0)
				{
                    console.log("2052 What the fuck happend!!!!")
					return false;
				}
			}
            else if ('meetingDay' in $formData)
			{
				$text = $formData['meetingDay'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('education' in $formData)
			{
				$text = $formData['education'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('folder' in $formData)
			{
				$text = $formData['folder'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
			else if ('header' in $formData)
			{
				$text = $formData['header'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('dbTable' in $formData)
			{
				$text = $formData['dbTable'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('status_name' in $formData)
			{
				$text = $formData['status_name'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('title' in $formData)
			{
				$text = $formData['title'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
            else if ('variable' in $formData)
			{
				$text = $formData['variable'];
				if ($text.trim().length == 0)
				{
					return false;
				}
			}
			
			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$tableKey = $(msg).filter("#replaceKey").text();
                
                var tree = $.ui.fancytree.getTree("#"+$target_tree);
				node = tree.getActiveNode();
                
                if($(msg).filter('#blockInsertTree').length)
                {
                    $.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey);
                    return false;
                }
				
				/*if ($.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey))
				{
					return false;
				}*/

				//If we are to add a subnode in the tree

				if (typeof node != 'undefined' && node != null && $addChildNode )
				{
					node.addChildren({
						folder: false,
						title: $text, 
						key : $tableKey
					});

					$formData['key'] = node.key;
				}
				else if (typeof node != 'undefined' && node != null && !$addChildNode )
				{
                    var $formData = new Object();
                    
                    if ($("#masterMenuId").length > 0)
                    {
                        $text = $( "#masterMenuId :selected" ).text();
                    }
                    
					node.appendSibling({
						folder: false,
						insertBefore : true,
						title: $text, 
						key : $tableKey
					});

					$formData['key'] = node.key;
				}
				else
				{
                    console.log($tableKey);
					var rootNode = $.ui.fancytree.getTree("#"+$target_tree).getRootNode();
					var childNode = rootNode.addChildren({
						title: $text,
						folder: false,
						key : $tableKey
					});
				}

                console.log($tableKey);
                
				extract_tree($target_tree, $replaceTable);
                
                if($tableKey !== null)
                {
                    $.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey);
                }
				
				$inputs.each(function() {
					if( $(this).is('input:text') ) 
					{
						$(this).val('');
					}

				});
                
                if ($this.data("update_div"))
                {
                    var $formData = new Object();
                    
                    $targetDiv = $this.data("update_div");
                    $formData['replaceTable'] = $formData['replace_table'] = $replaceTable = $this.data("replace_table");
                    $formData['replace_lang'] = $replaceLang = $this.data("replace_lang");
                    $formData['projectReplaceKey'] = $this.data("project_replace_key");
                    $url = "showAjax.php";
                    var request2 = $.ajax({
                        url : $url,
                        type: "POST",
                        data: $formData,
                        cache: false,
                    });

                    request2.done (function( msg )
                    {
                        $("#"+$targetDiv).html(msg);
                        
                        initFancytree();

                        initTinymce();

                        initJeditable();

                        initDataTable();
                        
                        if ( typeof $syncNavTab !== 'undefined' ) 
                        {
                            $("#"+$syncNavTab+$tableKey+"-tab").click();
                        }
                    });
                    
                    request2.fail (function (msg)
                    {
                        $("#status-field").html(msg);
                    });
                }
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
    });
    
    
    $(document).on("click", ".addToStatutesTree", function (event){
        event.preventDefault();
        var $formData = new Object();
        
		$target_tree = $(this).data("target_tree");
		$replaceTable = $(this).data("replace_table");
		$form_id = $(this.form).attr("id");
        $this = $(this);
        
		$block = false;
		
        $url = "../common/addDataTree.php";
        
        var $inputs = $("form#"+$form_id+' :input');

        $inputs.each(function() {
			if( $(this).is('[type="date"]') ) 
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
            else if( $(this).is('input:hidden') ) 
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
			else if ($(this).is(':checkbox') )
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
            else if ($(this).hasClass("selectpicker2"))
            {
                if ($(this).selectpicker("val") !== "-1")
                {
                    if ('note' in $formData && $formData['note'].length !== 0)
                    {
                        alert("Du kan inte addera egen nod och samtidigt valt ett menyalternativ!!!!");
                        $block = true;
                    }
                    
                    $formData[$(this).attr("id")] = $(this).selectpicker("val");
                }
            }
        });
        
        console.log($formData);
        
		if (!$block)
		{
			if(typeof $addChildNode === 'undefined')
			{
				$addChildNode = false;
			}

			$formData['replaceTable'] = $replaceTable;

			if ('validFrom' in $formData)
			{
				$text = $formData['validFrom'];
            }
            
			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$tableKey = $(msg).filter("#replaceKey").text();
                
                var tree = $.ui.fancytree.getTree("#"+$target_tree);
				node = tree.getActiveNode();
                
                if($(msg).filter('#blockInsertTree').length)
                {
                    $.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey);
                    return false;
                }
				
				/*if ($.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey))
				{
					return false;
				}*/

				//If we are to add a subnode in the tree

				if (typeof node != 'undefined' && node != null && $addChildNode )
				{
					node.addChildren({
						folder: false,
						title: $text, 
						key : $tableKey
					});

					$formData['key'] = node.key;
				}
				else if (typeof node != 'undefined' && node != null && !$addChildNode )
				{
                    var $formData = new Object();
                    
                    if ($("#masterMenuId").length > 0)
                    {
                        $text = $( "#masterMenuId :selected" ).text();
                    }
                    
					node.appendSibling({
						folder: false,
						insertBefore : true,
						title: $text, 
						key : $tableKey
					});

					$formData['key'] = node.key;
				}
				else
				{
                    console.log($tableKey);
					var rootNode = $.ui.fancytree.getTree("#"+$target_tree).getRootNode();
					var childNode = rootNode.addChildren({
						title: $text,
						folder: false,
						key : $tableKey
					});
				}

                console.log($tableKey);
                
				extract_tree($target_tree, $replaceTable);
                
                if($tableKey !== null)
                {
                    $.ui.fancytree.getTree("#"+$target_tree).activateKey($tableKey);
                }
				
				$inputs.each(function() {
					if( $(this).is('input:text') ) 
					{
						$(this).val('');
					}

				});
                
                if ($this.data("update_div"))
                {
                    var $formData = new Object();
                    
                    $targetDiv = $this.data("update_div");
                    $formData['replaceTable'] = $formData['replace_table'] = $replaceTable = $this.data("replace_table");
                    $formData['replace_lang'] = $replaceLang = $this.data("replace_lang");
                    $formData['projectReplaceKey'] = $this.data("project_replace_key");
                    $url = "showAjax.php";
                    var request2 = $.ajax({
                        url : $url,
                        type: "POST",
                        data: $formData,
                        cache: false,
                    });

                    request2.done (function( msg )
                    {
                        $("#"+$targetDiv).html(msg);
                        
                        initFancytree();

                        initTinymce();

                        initJeditable();

                        initDataTable();
                        
                        if ( typeof $syncNavTab !== 'undefined' ) 
                        {
                            $("#"+$syncNavTab+$tableKey+"-tab").click();
                        }
                    });
                    
                    request2.fail (function (msg)
                    {
                        $("#status-field").html(msg);
                    });
                }
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
    });
    
    $(document).on("click", "#replace_table", function(event)
    {
        var $formData = new Object();
        
		$replaceTable = $(this).data("replace_table");
        $replaceVar = $(this).data("replace_var");
        $updatDiv = $(this).data("target_div");
		$form_id = $(this.form).attr("id");
        $this = $(this);
        
        console.log($form_id);
        
        $url = "ajax_addLangVar.php";
       
        var $inputs = $("form#"+$form_id+' :input');

        $inputs.each(function() {
            
            console.log($(this).attr("id"));
            console.log($(this).attr("id"));
            
			if( $(this).is('input:text') ) 
			{
                if ($(this).val().trim().length > 0)
                {
                    $formData[$(this).attr("id")] = $(this).val();
                }
			}
            else if( $(this).is('textarea') ) 
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
			/*else if ($(this).is(':checkbox') )
			{
				if ($(this).is(":checked"))
				{
					$addChildNode = true;
				}
				else
				{
					$addChildNode = false;
				}
			}
            else if ($(this).hasClass("selectpicker2"))
            {
                if ($(this).selectpicker("val") !== "-1")
                {
                    if ('note' in $formData && $formData['note'].length !== 0)
                    {
                        alert("Du kan inte addera egen nod och samtidigt valt ett menyalternativ!!!!");
                        $block = true;
                    }
                    
                    $formData[$(this).attr("id")] = $(this).selectpicker("val");
                }
            }*/
        });
        
        $formData['replaceTable'] = $replaceTable;
        $formData['replaceVar'] = $replaceVar;
        
        console.log($formData);
        
        var request = $.ajax({
            url : $url,
            type: "POST",
            data: $formData,
            cache: false,
        });

        request.done (function( msg )
        {
            $url = "showAjax.php";
            
            var $formData = new Object();
            
            $formData['replaceTable'] = $replaceTable;
            $formData['id'] = $replaceVar;
            
            console.log($formData);
            
            var request2 = $.ajax({
                url : $url,
                type: "POST",
                data: $formData,
                cache: false,
            });

            request2.done (function( msg )
            {
                $("#"+$updatDiv).html(msg);

            });

            request2.fail (function (msg)
            {
                $("#status-field").html(msg);
            });

        });

        request.fail (function (msg)
        {
            $("#status-field").html(msg);
        });
    });
    
    $(document).on("click", ".validFrom", function(event){
        event.preventDefault(); 
        
        if ($(this).is(":checked"))
        {
            if (confirm("Vill du låsa dessa stadgar för editering, det går inte att ångra detta!"))
            {
                $url = "../common/syncData.php";
                
                var $formData = new Object();
                
                $formData[$(this).attr("id")] = 1;
                $formData['replaceTable'] = $(this).data("replace_table");
                $formData['id'] = $("#replaceKey").val();
                
                $syncDiv = $(this).data("target_div");
                
                $url = "../common/syncData.php";
                
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
                    
                    var request2 = $.ajax({
                        url : $url,
                        type: "POST",
                        data: $formData,
                        cache: false,
                        async: false
                    });

                    request2.done (function( msg )
                    {
                        $("#"+$syncDiv).html(msg);
                    });

                    request2.fail (function (msg)
                    {
                        $("#status-field").html(msg);
                    });
                });

                request.fail (function (msg)
                {
                    $("#status-field").html(msg);
                });
            }
        }
    });
    
    $(document).on("click", ".removeStatutes", function(event){
        event.preventDefault(); 
        
        if (confirm("Vill du RADERA dessa stadgar, detta går inte att ångra detta!"))
        {
            $url = "ajax_removesSatutes.php";

            var $formData = new Object();

            $formData['id'] = $(this).attr("id");
            $formData['replaceTable'] = $(this).data("replace_table");

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
	
	$(document).on("mousedown", ".showActivity", function(event)
	{
        event.preventDefault(); 
		
		if (event.which == 1)
		{
			$url = "ajax_showActivity.php";
		}
		else if (event.which == 3)
		{
			$url = "ajax_editActivity.php";
		}
		else
		{
			$url = "ajax_showActivity.php";
		}

		var $formData = new Object();

		matches = $(this).attr("id").match(/\[(.*?)\]/);
		matches[0] = matches[0].replace('[', '');
		matches[0] = matches[0].replace(']', '');
		$formData['id'] = matches[0];
		$formData['replaceTable'] = $(this).data("replace_table");

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#modalHeader").html($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody"));
			
			$("#myModal").modal('show');
			$(".selectpicker").selectpicker();
			//location.reload();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		return false;
	});
	
	$(document).on("mousedown", ".clickMe", function(event)
	{
        event.preventDefault(); 
		
		if (event.which == 3)
		{
			$url = "ajax_editActivity.php";
		}
		else
		{
			return false;;
		}

		console.log(event.which);
		
		var $formData = new Object();

		$formData['date'] = $(this).attr("id");
		$formData['replaceTable'] = $(this).data("replace_table");

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#modalHeader").html($(msg).filter("#modalHeader").text());
			$("#modalBody").html($(msg).filter("#modalBody"));
			
			$("#myModal").modal('show');
			$(".selectpicker").selectpicker();
			//location.reload();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".subscribeActivity", function(event)
	{
        event.preventDefault(); 
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		var $inputs = $("form#activityForm"+' :input');
		var $url;
		
		
		$inputs.each(function() {
			if ($(this).attr("id"))
			{
				if ($(this).hasClass("selectpicker"))
				{
					$formData[$(this).attr("id")] = $(this).selectpicker("val");
				}
				else if (!$(this).hasClass("showActivity"))
				{
					$formData[$(this).attr("id")] = $(this).val();
				}
			}
		});
		
		console.log($formData);
		
		$url = "ajax_participatoryActivity.php";

		/*matches = $(this).attr("id").match(/\[(.*?)\]/);
		matches[0] = matches[0].replace('[', '');
		matches[0] = matches[0].replace(']', '');
		$formData['id'] = matches[0];*/

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$url = "ajax_showActivity.php";
			
			var $formData2 = new Object();
			
			$formData2['replaceTable'] = $formData['replaceTable'];
			$formData2['id'] = $formData['activityKey'];
			
			var request2 = $.ajax({
			url : $url,
			type: "POST",
			data: $formData2,
			cache: false,
			async: false
			});

			request2.done (function( msg )
			{
				$("#modalBody").html($(msg).filter("#modalBody"));
				$(".selectpicker").selectpicker();

				//location.reload();
			});
			
			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".unsubscribeActivity", function(event)
	{
        event.preventDefault(); 
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $("#tableKey").val();
		
		var matches = $(this).attr("id").match(/\[(.*?)\]/);

		console.log(matches);
		
		if (matches) {
			var submatch = matches[1];
		}
		
		$formData['userKey'] = submatch;
		
		console.log($formData);
		
		$url = "ajax_participatoryActivity.php";

		/*matches = $(this).attr("id").match(/\[(.*?)\]/);
		matches[0] = matches[0].replace('[', '');
		matches[0] = matches[0].replace(']', '');
		$formData['id'] = matches[0];*/

		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$url = "ajax_showActivity.php";
			
			var $formData2 = new Object();
			
			$formData2['replaceTable'] = $formData['replaceTable'];
			$formData2['id'] = $("#activityKey").val();
			
			var request2 = $.ajax({
			url : $url,
			type: "POST",
			data: $formData2,
			cache: false,
			async: false
			});

			request2.done (function( msg )
			{
				$("#modalBody").html($(msg).filter("#modalBody"));
				$(".selectpicker").selectpicker();

				//location.reload();
			});
			
			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", "#addActivity", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		var $inputs = $("form#addActivityForm"+' :input');
		var $url;
		
		$url = "ajax_saveActivity.php";
		
		$run = true;
		
		$inputs.each(function() {
			if($(this).prop('required'))
			{
				if ($(this).is("textarea"))
				{
					if(typeof(tinyMCE) != "undefined") 
					{
						try 
						{
						 	$formData[$(this).attr("id")] = tinyMCE.editors[$(this).attr('id')].getContent();
						}
						catch (err)
						{
							$formData[$(this).attr("id")] = $(this).val();
						}
					}
					else
					{
						$formData[$(this).attr("id")] = $(this).val();
					}
					
					
					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
					}
				}
				else
				{
					$formData[$(this).attr("id")] = $(this).val();
					
					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
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
            console.log("Vi ska ladda om sidan.....");
            $('#addActivityForm')[0].reset();
            
            alert("Aktiviteten sparad, sidan kommer att laddas om!");
            
			location.reload();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", "#ownSenderSMS", function (event){
		if ($(this).is(":checked"))
		{
			$("#areaSMSheader").hide();
			
			$headerSMS = $("#headerSMS").val();
			$("#headerSMS").val($("#masterHeaderSMS").val());
		}
		else
		{
			$("#areaSMSheader").show();
			$("#headerSMS").val($headerSMS);
		}
	});
	
	$(document).on("click", ".selectAllSMSMemeber", function (event){
		if ($(this).is(":checked"))
		{
			$all = true
			$(".selectAllSMSMemeber").each(function() {
				if (!$(this).is(":checked"))
				{
					$all = false;
					return;
				}
			});
			if ($all)
			{
				$("#selectAllSMSMemeber").prop('checked', true);
			}
		}
		else
		{
			$("#selectAllSMSMemeber").prop('checked', false);
		}
	});
	
	$(document).on("click", ".selectAllMailMemeber", function (event){
		if ($(this).is(":checked"))
		{
			$all = true
			$(".selectAllMailMemeber").each(function() {
				if (!$(this).is(":checked"))
				{
					$all = false;
					return;
				}
			});
			
			if ($all)
			{
				$("#selectAllMailMemeber").prop('checked', true);
			}
		}
		else
		{
			$("#selectAllMailMemeber").prop('checked', false);
		}
		
	});
	
	$(document).on("click", ".selectAllSMSGroup", function (event){
		if ($(this).is(":checked"))
		{
			$all = true
			$(".selectAllSMSGroup").each(function() {
				if (!$(this).is(":checked"))
				{
					$all = false;
					return;
				}
			});
			
			if ($all)
			{
				$("#selectAllSMSGroup").prop('checked', true);
			}
		}
		else
		{
			$("#selectAllSMSGroup").prop('checked', false);
		}
	});
	
	$(document).on("click", ".selectMailMGroup", function (event){
		if ($(this).is(":checked"))
		{
			$all = true
			$(".selectMailMGroup").each(function() {
				if (!$(this).is(":checked"))
				{
					$all = false;
					return;
				}
			});
			console.log($all);
			if ($all)
			{
				$("#selectMailMGroup").prop('checked', true);
			}
		}
		else
		{
			$("#selectMailMGroup").prop('checked', false);
		}
	});
	
	$(document).on("click", "#selectAllMailMemeber", function (event){
		if ($(this).is(":checked"))
		{
			$(".selectAllMailMemeber").each(function() {
				$(this).prop('checked', true);
			});
		}
		else
		{
			$(".selectAllMailMemeber").each(function() {
				$(this).prop('checked', false);
			});
		}
	});
	
	$(document).on("click", "#selectAllSMSMemeber", function (event){
		if ($(this).is(":checked"))
		{
			$(".selectAllSMSMemeber").each(function() {
				$(this).prop('checked', true);
			});
		}
		else
		{
			$(".selectAllSMSMemeber").each(function() {
				$(this).prop('checked', false);
			});
		}
	});
	
	$(document).on("click", "#selectAllSMSGroup", function (event){
		if ($(this).is(":checked"))
		{
			$(".selectAllSMSGroup").each(function() {
				$(this).prop('checked', true);
			});
		}
		else
		{
			$(".selectAllSMSGroup").each(function() {
				$(this).prop('checked', false);
			});
		}
	});
	
	$(document).on("click", "#selectAllMailGroup", function (event){
		if ($(this).is(":checked"))
		{
			$(".selectAllMailGroup").each(function() {
				$(this).prop('checked', true);
			});
		}
		else
		{
			$(".selectAllMailGroup").each(function() {
				$(this).prop('checked', false);
			});
		}
	});
	
	$(document).on("keyup", "#messageSMS", function(event){
		
		$length = $(this).val().length;
		
		if ($length > 0)
		{
			$("#copy2Mail").prop("disabled", false);
			
			$("#sendMessages").prop("disabled", false);
		}
		else
		{
			$("#copy2Mail").prop("disabled", true);
			
			$temp = tinymce.get("messageMail").getContent().trim();

			if ($temp.length > 0)
			{
				$("#sendMessages").prop("disabled", false);
			}
			else
			{
				$("#sendMessages").prop("disabled", true);
			}
		}
		
		$diff = 160 - $length;
		
		$("#countSMS").val($diff);
	});
	
	$(document).on("click", "#copy2Mail", function(event){
		
		event.preventDefault();
		
		$temp = tinymce.get("messageMail").getContent().trim();

		if ($temp.length > 0)
		{
			if (confirm("Vill du verkligen ersätta innehållet i ditt mail?"))
			{
				tinymce.get("messageMail").setContent($("#messageSMS").val().trim());
			}
		}
		else
		{
			tinymce.get("messageMail").setContent($("#messageSMS").val().trim());
		}
		
		$("#copy2SMS").prop("disabled", false);
		
	});
	
	$(document).on("click", "#copy2SMS", function(event){
		event.preventDefault();
		
		$temp = tinymce.get("messageMail").getContent({format: 'text'}).trim();

		if ($("#messageSMS").val().trim().length > 0)
		{
			if (confirm("Vill du verkligen ersätta innehållet i ditt sms?"))
			{
				$("#messageSMS").val($temp);
			}
		}
		else
		{
			$("#messageSMS").val($temp);
		}
		
		$("#copy2Mail").prop("disabled", false);
	});
	
	$(document).on("click", "#sendMessages", function(event){
		var $inputs = $("form#formSendMessageSMS"+' :input');
		var $url;
		
		var $formData = new Object;
		
		$url = "ajax_sendMessage.php";
		
		$run = true;
		
		if ($("#messageSMS").val().trim().length > 0)
		{
			$inputs.each(function() {
				if ($(this).attr("id") === "countSMS" || $(this).attr("id") === "selectAllSMSMemeber" || $(this).attr("id") === "masterHeaderSMS"  )
				{
					return;
				}
				
				if ($(this).is("textarea"))
				{
					$formData[$(this).attr("id")] = $(this).val().trim();

					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
					}
				}
				else if ($(this).is(":checkbox"))
				{
					if ($(this).is(":checked"))
					{
						$formData[$(this).attr("id")] = 1;
					}
					/*else
					{
						$formData[$(this).attr("id")] = 0;
						$block = true;
					}*/
				}
				else if ($(this).is(":text"))
				{
					if ($(this).val().trim().length > 0)
					{
						$formData[$(this).attr("id")] = $(this).val();
					}
				}
			});
		}
		
		var $inputs = $("form#formSendMessageMail"+' :input');
		
		if (tinymce.get("messageMail").getContent().trim().length > 0)
		{
			$inputs.each(function() {
				
				if ($(this).attr("id") === "selectAllMailMemeber")
				{
					return;
				}

				if ($(this).is("textarea"))
				{
					$formData[$(this).attr("id")] = tinyMCE.editors[$(this).attr('id')].getContent();

					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
					}
				}
				else if ($(this).is(":checkbox"))
				{
					if ($(this).is(":checked"))
					{
						$formData[$(this).attr("id")] = 1;
					}
					/*else
					{
						$formData[$(this).attr("id")] = 0;
						$block = true;
					}*/
				}
				else if ($(this).is(":text"))
				{
					if ($(this).val().trim().length > 0)
					{
						$formData[$(this).attr("id")] = $(this).val();
					}
				}
			});
		}
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			var $inputs = $("form#formSendMessageSMS"+' :input');
			$inputs.each(function() {
				if ($(this).is("textarea") && $("#"+$(this).attr('id')).hasClass("tinyMceArea"))
				{
					console.log($(this).attr('id'));
					tinyMCE.editors[$(this).attr('id')].setContent('');
				}
				else
				{
					$(this).val('');
				}
            });
			
			if (tinymce.get("messageMail").getContent().trim().length > 0)
			{
				tinyMCE.editors["messageMail"].setContent('');
			}
			
			alert("Meddelandena har sänts förhoppningsvis!");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
		console.log($formData);
	});
	
	$(document).on("click", ".updateTournament", function(event){
		event.preventDefault();
		
		$formData = new Object();
		
		$url = "ajax_syncTournamentResult.php";
		
		$run = true;
		
		var $inputs = $("form#formTorunamentResuluts"+' :input');
		
		$inputs.each(function() {
			if ($(this).attr("id"))
			{
				$formData[$(this).attr("id")] = $(this).val();
			}
		});
		
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
			var $targetDiv = "formTorunamentResuluts";
			
			$formData['selectActivity'] = $("#selectActivity").selectpicker("val");
			
			$url = "ajax_editTornamentResult.php";
			
				var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
			});

			request.done (function( msg )
			{
				$("#"+$targetDiv).html($(msg));
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".addMember2tournament", function(event){
		event.preventDefault();
		
		$formData = new Object();
		
		$url = "ajax_syncTournamentResult.php";
		
		$formData[$('[id^="addMember2tournament"]').attr("id")] = $('[id^="addMember2tournament"]').selectpicker("val");
		$formData['replaceTable'] = $(this).data("replace_table");
		
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
	
	$(document).on("click", "#addBlockDate", function(event){
		event.preventDefault();
		
		$formData = new Object();
		
		$url = "ajax_addBlockDate.php";
		
		$formData['blockDate'] = $("#blockDate").val();
		$formData['note'] = $("#note").val();
		$formData['replaceTable'] = $(this).data("replace_table");
		$formData['id'] = $(this).data("table_key");
		$targetDiv = $(this).data("target_div");
		
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
			$url = "showAjax.php";
			
			var request2 = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request2.done (function( msg )
			{
				$("#"+$targetDiv).html(msg);
			});

			request2.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".editPersonal", function(event){
		
		$formData = new Object();
		
		$url = "ajax_editProfile.php";
		
		matches = $(this).attr("id").match(/\[(.*?)\]/);
		
		$formData['replaceTable'] = $(this).data("replace_table");
		$formData['userTableKey'] = matches[1];
		
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
			
			$("#reciverEmail").val($("#email").val());
			
			$("#myModal").modal("show");
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	
	
	$(document).on("click", ".deleteAccount", function(event){
		
		$formData = new Object();
		
		$url = "ajax_deleteAccount.php";
		
		if (confirm("Vill du verkligen raddera detta konto?"))
		{
			matches = $(this).attr("id").match(/\[(.*?)\]/)
			
			$formData['replaceTable'] = $(this).data("replace_table");
			$formData['id'] = matches[1];

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				$("#myModal").modal("hide");
				$("#modalXl").modal("hide");
				location.reload();
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	});
	
	$(document).on("click", ".deleteAccountPersonal", function(event){
		
		$formData = new Object();
		
		$url = "ajax_deleteAccount.php";
		
		if (confirm("Vill du verkligen raddera ditt konto?"))
		{
			matches = $(this).attr("id").match(/\[(.*?)\]/)
			
			$formData['replaceTable'] = $(this).data("replace_table");
			$formData['id'] = matches[1];

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				window.location.href = "https://www.young-friends.org";
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	});
	
	$(document).on("click", ".closeModal", function(event){
		
		$("#myModal").modal("hide");
		$("#modalXl").modal("hide");
	});
	
	$(document).on('hide.bs.modal', "#myModal", function(event){
		$formData = new Object();
		
		if ($("#myModal #synAccount").length > 0)
		{
			console.log("Ska synka bokföringsfiler.......");
			
			
			$formData['replace_agenda'] = $("#synAccount").val();

			$url = "ajax_syncFilesAccount.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				//$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);

				$("#verifiedFileArea").html(msg);
				
				initFancytree(); 
				initDataTable();
				initJeditable();
				initJeditableTextarea();
				initTinymce();
				$(".selectpicker").selectpicker();
				
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
			
			return;
		}
		
		else if ($("#myModal #syncAnnualmeeting").length > 0)
		{
			console.log("Ska synka årsmötesfiler.......");
			
			
			$formData['replace_annualmeeting'] = $("#syncAnnualmeeting").val();

			$url = "ajax_syncFiles.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				//$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);

				$("#agendaAreaFiles").html(msg);
				
				initFancytree(); 
				initDataTable();
				initJeditable();
				initJeditableTextarea();
				initTinymce();
				$(".selectpicker").selectpicker();
				
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
			
			return;
		}
		
		else if ($("#myModal #syncBorder").length > 0)
		{
			$formData['replace_agenda'] = $("#syncBorder").val();

			$url = "ajax_syncFiles.php";

			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				//$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);

				$("#agendaAreaFiles").html(msg);
				
				initFancytree(); 
				initDataTable();
				initJeditable();
				initJeditableTextarea();
				initTinymce();
				$(".selectpicker").selectpicker();
				
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
			
			return;
		}
		
		$formData['replace_agenda'] = $("#replace_agenda").val();
		
		$url = "ajax_syncFiles.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			//$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);
			
			$("#agendaAreaFiles").html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
		$formData['replace_agenda'] = $("#replace_agenda").val();
		
		$url = "ajax_syncFiles.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			//$("#"+$('[id ^= "ajax_"]').attr("id")).html(msg);
			
			$("#agendaAreaFiles").html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
	});
	
	$(document).on("click", ".sendContactMessage", function(event){
		
		$formData = new Object();
		
		var $inputs = $("form#contactForm"+' :input');
		var $url;
		
		$url = "ajax_sendContactForm.php";
		
		$run = true;
		
		$inputs.each(function() {
			if($(this).prop('required'))
			{
				if ($(this).is("textarea"))
				{
					$formData['message'] = tinyMCE.editors[$(this).attr('id')].getContent();
					
					if ($formData['message'].trim().length == 0)
					{
						$run = false;
					}
				}
				else
				{
					$formData[$(this).attr("id")] = $(this).val();
					
					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
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
			alert("Du har missat att ange information i en eller flera fält som behövs!");
			return false;
		}
		else 
		{
			return true;
		}
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			var $inputs = $("form#contactForm"+' :input');
			
			$inputs.each(function() {
				if($(this).prop('required'))
				{
					if ($(this).is("textarea"))
					{
						tinyMCE.editors[$(this).attr('id')].setContent('');
					}
					else
					{
						$(this).val('');
					}

				}

				else if ($(this).is(":text"))
				{
					$(this).val('');
				}


				else if ($(this).is(":button"))
				{
					//Do nothing
				}
				else if ($(this).is("textarea"))
				{
					tinyMCE.editors[$(this).attr('id')].setContent('');
				}
				else if ($(this).attr("id") !== undefined)
				{
					$(this).val('');
				}

			});
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".cancelActivity", function(event){
		
		event.preventDefault();
		
		$formData = new Object();
		
		$url = "ajax_editActivity.php";
		
		$formData['tableKey'] = $("#tableKey").val();
		$formData['cancelActivity'] = $("#activityKey").val();
		
		if (confirm("Vill du verkligen ställa in denna aktivitet? Alla anmälda kommer att notifieras om detta!"))
		{		
			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				window.location.hash = '#activities';
    			window.location.reload(true);
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	});
	
	$(document).on("click", ".reactivateActivity", function(event){
		
		event.preventDefault();
		
		$formData = new Object();
		
		$url = "ajax_editActivity.php";
		
		$formData['tableKey'] = $("#tableKey").val();
		$formData['reactivateActivity'] = $("#activityKey").val();
		
		if (confirm("Vill du verkligen aktivera denna inställda aktivitet? Alla anmälda kommer att notifieras om detta!"))
		{		
			var request = $.ajax({
				url : $url,
				type: "POST",
				data: $formData,
				cache: false,
				async: false
			});

			request.done (function( msg )
			{
				window.location.hash = '#activities';
    			window.location.reload(true);
			});

			request.fail (function (msg)
			{
				$("#status-field").html(msg);
			});
		}
	});
	
	$(document).on("click", ".insertDefaultAnnualprotocol", function(event){
		event.preventDefault();
		
		var $formData = new Object();
		
		var $inputs = $("form#protocolForm"+' :input');
		
		$formData['replaceTable'] = $(this).data("replace_table");
		$formData['replace_agenda'] = $("#replace_agenda").val();
		
		$inputs.each(function() {
			if ($(this).hasClass("selectpicker"))
			{
				$formData[$(this).attr('id')] = $(this).selectpicker("val");
			}
			
		});
		
		$url = "ajax_insertDefaultAnnualmeetingProtocol.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#protocol").html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
		return false;
	});
	
	$(document).on("click", ".annualmeetingProtocolDone", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		var matches = $('[id ^= "meetingDay"]').attr("id").match(/\[(.*?)\]/);

		$formData['protocolDone'] = matches[1];
		$formData['meetingDay'] = matches[1];
		
		
		console.log($('[id ^= "secretary"]').attr('id'));
		$sec = $('[id ^= "secretary"]').attr('id').replace("[", "\\[");
		$sec = $sec.replace("]", "\\]");
		
		$adj = $('[id ^= "adjustment"]').attr('id').replace("[", "\\[");
		$adj = $adj.replace("]", "\\]");
		
		$secretary = $("#"+$sec).selectpicker("val");
		
		$adjustment = $("#"+$adj).selectpicker("val");
		
		console.log('"'+$secretary+'"');
		
		console.log($adjustment);
		
		
		if ($adjustment.indexOf($secretary) !== -1)
		{
			alert("Du kan inte ange att sekreterare ska justera sitt egna protokoll!!!!");
			return false;
		}
		
		$url = "ajax_saveAnnualmeetingProtocol.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#protocol").html(msg);
			
			initFancytree(); 
			initDataTable();
			initJeditable();
			initJeditableTextarea();
			initTinymce();
			$(".selectpicker").selectpicker();
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	$(document).on("click", ".verifyAnnualmeetingProtocol", function (event){
		event.preventDefault();
		
		var $formData = new Object();
		
		$formData['replaceTable'] = $(this).data("replace_table");
		
		$formData['verifyProtocol'] = $(this).data("replace_protocol");
		
		$url = "ajax_saveAnnualmeetingProtocol.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$("#protocol").html(msg);
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
	});
	
	/*$(document).on("change", "#createAccount #email", function (event){
		
		var $formData = new Object();
		
		$formData[$(this).attr("id")] = $(this).val();
		
		$url = "ajax_sendRegForm.php";
		
		var request = $.ajax({
			url : $url,
			type: "POST",
			data: $formData,
			cache: false,
			async: false
		});

		request.done (function( msg )
		{
			$result = $(msg).filter("#result").val().toLowerCase();;
			
			if ($result == "ok")
			{
				//Do nothing
				
				$blockReg = false;
			}
			else
			{	
				$blockReg = true;
				
				alert("Du verkar ha varit medlem tidigare, kontakta oss för att få uppgifter om inbetalning av medlemsavgiften!");
			}
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
	});*/
	
	$(document).on("click", ".createAccount", function (event){
		var $formData = new Object();
		
		$form = "createAccount";

		$url = "ajax_sendRegForm.php";

		$run = false;

		$("#"+$form+" :input").each(function () {
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
					$formData[$(this).attr("id")] = $(this).val();
					
					if ($formData[$(this).attr("id")].trim().length == 0)
					{
						$run = false;
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
					$run = true;
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
					$run = true;
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
			alert("Kontrollera att alla krävda inmatningfält är ifyllda med information som krävs!");
			return false;
		}
		
		if ($formData['password'].trim().length < 6)
		{
			$run = false;
		
			alert("Lösenordet är för kort, det ska vara minst 6 tecken!");	
		}
		
		if ($formData['password'].trim() !== $formData['repPassword'].trim())
		{
			$run = false;
			
			alert("Lösenordet är ej samma, vänligen kontrollera dessa!");			  
		}
		
		if ($run && !$blockReg)
		{
			grecaptcha.ready(function () {
				grecaptcha.execute('6LcjdZ0UAAAAACnmmP5s65vXAVUc7KLJxSaDi4lF', { action: 'contact' }).then(function (token) {
					var recaptchaResponse = document.getElementById('recaptchaResponse');
					recaptchaResponse.value = token;

					$formData['recaptchaResponse'] = token;
					$url = "ajax_sendRegForm.php";

					var request = $.ajax({
						url : $url,
						type: "POST",
						data: $formData,
						cache: false,
						async: false
					});

					request.done (function( msg )
					{
						$("#protocol").html(msg);
					});

					request.fail (function (msg)
					{
						$("#status-field").html(msg);
					});
				});
			});
		}
		
		return false;
	});
	$(document).on("click", "#resetPassword", function (event)
	{
		var $formData = new Object();
		
		$url = "ajax_requestNewPassword.php";
		
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
			
			$("#requestPassword").val($("#mail").val());
		});

		request.fail (function (msg)
		{
			$("#status-field").html(msg);
		});
		
	});
	
	$(document).on("click", ".sendPasswordRequest", function (event)
	{
		var $formData = new Object();
		
		$formData['requestPassword'] = $("#requestPassword").val();
		
		$url = "ajax_requestNewPassword.php";
		
		if (IsEmail($formData['requestPassword']))
		{
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
	});
	
	$(document).on("click", "#setNewPassword", function (event)
	{
		var $formData = new Object();
		
		$formData['tableKey'] = $(this).val();
		
		$("#setNewPasswordForm :input").each(function () 
		{
			if ($(this).attr("id") !== undefined)
			{
				$formData[$(this).attr("id")] = $(this).val().trim();
			}
		});
		
		if ($formData['password'].length >= 6)
		{
			if ($formData['password'] == $formData['repPassword'])
			{
				$url = "setNewPassword.php";
				
				var request = $.ajax({
					url : $url,
					type: "POST",
					data: $formData,
					cache: false,
					async: false
				});

				request.done (function( msg )
				{
					$("#setNewPasswordForm").html(msg);
					//window.location = 'index.php#activities';
				});

				request.fail (function (msg)
				{
					$("#status-field").html(msg);
				});
			}
			else
			{
				alert("Angivna lösenord är inte identiska!!!");
				return false;
			}
		}
		else
		{
			alert("Lösenordet är för kort!");
			return false;
		}
	});
    
    
});
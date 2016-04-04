/**
 * This file is used for both exporting and importing an experiment.
 */

/**
 * Will start the exporting process for an experiment, and force a download to the user.
 * @param experimentId is the ID for the experiment which to export.
 * @return link = string with downloadPath.
 */
function generateSQLForExperiment(experimentId) {

	var link;
    $.ajax
    ({
        url: 'ajax/scientist/exportExperiment.php',
        async: false,
        data: {
        	'option':'generateSQL',
        	'experimentId':experimentId,
        },
        dataType: 'json',
        success: function(data)
        {
        	link = "<a href=ajax/scientist/" + data + ">Download Experiment</a>";
			$('#wrapper').append("<div style=display:none;>" +
		    "<iframe id=frmDld src= ajax/scientist/" + data + "></iframe></div>");
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return link;
}

function generateSQLForHeatmap(experimentId) {
	var link;
    $.ajax
    ({
        url: 'ajax/scientist/exportExperiment.php',
        async: false,
        data: {
        	'option':'generateSQL',
        	'experimentId':experimentId,
        },
        dataType: 'json',
        success: function(data)
        {
        	link = "<a href=ajax/scientist/" + data + ">Download Experiment</a>";
			$('#wrapper').append("<div style=display:none;>" +
		    "<iframe id=frmDld src= ajax/scientist/" + data + "></iframe></div>");
        },
        error: function(request, status, error) {
            alert(request.responseText);
        }
    });
    return link;
}




/**
 * Will generate and show the uploader for importing experiments.
 */
function generateUploaderImportExperiment() {
	$('#right-panel').empty();
	$('#right-panel').append("<p class ='item-title-secondary'>Upload your earlier exported experiment.\n</p>");
	$('#right-panel').append("<div id=filelist>Your browser doesn't have Flash, Silverlight or HTML5 support.</div>" +
			"<br />" +
			"<div id=container>" +
			    "<button class ='primary' id=pickfiles href=javascript:;>Select file</button>    " +
			    "<button class ='primary' id=uploadfiles href=javascript:;>Upload file</button>"  +
			"</div>" +
			"<br />" +
			"<pre id=console></pre>");

	var uploader = new plupload.Uploader({
	    runtimes : 'html5,flash,silverlight,html4',

	    browse_button : 'pickfiles', // you can pass in id...
	    container: document.getElementById('container'), // ... or DOM Element itself
	    chunk_size: '200kb',


	    url : "ajax/scientist/uploadExperiment.php",

	    filters : {
	        max_file_size : '2048mb',
	        mime_types: [
	            {title : "Zip files", extensions : "zip"}
	        ],
	        multi_selection:false

	    },

	    // Flash settings
	    flash_swf_url : '/plupload/js/Moxie.swf',

	    // Silverlight settings
	    silverlight_xap_url : '/plupload/js/Moxie.xap',


	    init: {
	        PostInit: function() {
	            document.getElementById('filelist').innerHTML = '';

	            document.getElementById('uploadfiles').onclick = function() {
	                uploader.start();
	                return false;
	            };
	        },

	        FilesAdded: function(up, files) {
	        	var maxFiles = 1;
	        	console.log("[up]", up);
	        	console.log("[files]", files);
	    	    if(up.files.length > maxFiles)
	    	    {
	    	    	alert("Only 1 file!");
	    	    	generateUploaderImportExperiment();

	    	    }
	            plupload.each(files, function(file) {
	                document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
	            });
	        },

            UploadComplete: function(up, files) {
                // Called when all files are either uploaded or failed
                //console.log('[UploadComplete]');
            	importExperiment();
            },


	        UploadProgress: function(up, file) {
	            document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
	        },

	        Error: function(up, err) {
	            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
	        }
	    }
	});

	uploader.init();
}
/**
 * Imports the experiment.  This function is to run AFTER the zip file has been uploaded.
 */
function importExperiment() {
    $.ajax
    ({
        url: 'ajax/scientist/importExperiment.php',
        async: false,
        data: {
        	'option':'generateExperiment',
        },
        dataType: 'json',
        success: function(data)
        {
        	if(data == 1) {
        	generateUploaderImportExperiment();
        	$('#right-panel').append("" +
        			"<div class='notice marker-on-top' style='width:250px';>" +
        			"<div class='fg-white'>Experiment successfully imported!</br>Import another?</div></div>");
        	} else {
            	$('#right-panel').append("" +
            			"<div class='notice marker-on-top bg-darkRed' style='width:250px';>" +
            			"<div class='fg-white'>Something went wrong,are you sure it</br> is the correct file?</div></div>");
        	}
        },
        error: function(request, status, error) {
        	cleanUpDirectory();
          //  alert(request.responseText);
        	$('#right-panel').empty();
        	generateUploaderImportExperiment();
        	$('#right-panel').append("" +
        			"<div id='error-message' class='notice marker-on-top bg-darkRed' style='width:250px';>" +
        			"<div class='fg-white'>Something went wrong,are you sure it is the correct file?</div></div>");
        	$('#error-message').fadeIn("fast", function(){
                $("#error-message").fadeOut(7000);
            });

        }
    });
}
/**
 * Will delete the newly uploaded zip file.
 */
function cleanUpDirectory() {
	$.ajax
    ({
        url: 'ajax/scientist/importExperiment.php',
        data: {
        	'option':'cleanDirectory',
        }
    });
}

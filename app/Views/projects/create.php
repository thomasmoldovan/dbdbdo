<style>
    .CodeMirror { border: solid 1px #AAAAAA !important; }
</style>

<div class="container-fluid mx-auto">
    <div class="col-sm-6 offset-sm-3 pt-5">
        <div class="card">
            <h4 class="card-header">Import SQL schema</h4>
            <div class="card-body pb-0">
                <h6 class="card-title">Give this project a name:</h6>
                <div class="pb-3">
                    <input type="text" name="projectName" id="projectName" class="col-6 pl-1 form-control" value="">
                    <div class="small error text-danger invisible" name="projectNameError" id="projectNameError"></div>
                    <div class="pt-3">
                        <input type="checkbox" name="processAllTables" id="processAllTables" value="1" checked="checked">
                        <label for="processAllTables">Process tables after import</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <h6 class="card-title">Paste your SQL schema code below:</h6>
                        <div class="small bold text-danger"><b>Attention:</b></div>
                        <div class="small">- all tables are dropped before every import</div>
                        <div class="small">- comments will be removed before script execution</div>
                        <div class="small">- only CREATE TABLE statements will be executed</div>
                        <div class="small pb-3">- no changes to tables structure can be made after this step</div>
                    </div>
                    <div class="col-6">
                        <h6 class="card-title">Or choose from one of the examples:</h6>
                        <button type="button" class="btn btn-primary m-3 mt-0" id="loadToDoList" name="loadToDoList">To Do List</button>
                    </div>
                </div>
                
                <textarea name="databaseSchema" id="databaseSchema" class="w-100" rows="10" value=""></textarea>
                <button type="button" class="btn btn-primary m-3 mt-0" id="processDatabaseSchema" name="processDatabaseSchema">Proceed</button>
                <div class="small error text-danger invisible">Project name cannot be empty</div>
            </div>
        </div>
    </div>
</div>

<script src="/codemirror/codemirror.js"></script>
<script src="/codemirror/sql.js"></script>
<link href="/codemirror/codemirror.css" rel="stylesheet">

<div id="toDoListCode2" class="hidden invisible hide"
data-code="Q1JFQVRFIFRBQkxFIGBidWNrZXRgICgKICBgaWRgIGludCgxMSkgdW5zaWduZWQgTk9UIE5VTEwgQVVUT19JTkNSRU1FTlQsCiAgYGZEYXRlYCBkYXRldGltZSBERUZBVUxUIE5VTEwsCiAgYGZUaW1lYCBkYXRldGltZSBERUZBVUxUIE5VTEwsCiAgYGZJbWFnZWAgdmFyY2hhcigxNTApIENIQVJBQ1RFUiBTRVQgbGF0aW4xIERFRkFVTFQgTlVMTCwKICBgZkxpbmtgIHZhcmNoYXIoMTUwKSBDSEFSQUNURVIgU0VUIGxhdGluMSBERUZBVUxUIE5VTEwsCiAgYGZDaGVja2JveGAgdGlueWludCgxKSBERUZBVUxUIE5VTEwsCiAgYGZSYWRpb2AgdGlueWludCgxKSBERUZBVUxUIE5VTEwsCiAgYGZGaWxlYCB2YXJjaGFyKDE1MCkgQ0hBUkFDVEVSIFNFVCBsYXRpbjEgREVGQVVMVCBOVUxMLAogIGBmSW5wdXRgIHZhcmNoYXIoMTUwKSBDSEFSQUNURVIgU0VUIGxhdGluMSBERUZBVUxUIE5VTEwsCiAgYGZOdW1iZXJgIGludCgxMSkgREVGQVVMVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKQopIEVOR0lORT1NeUlTQU0gREVGQVVMVCBDSEFSU0VUPXV0ZjMyOwoKQ1JFQVRFIFRBQkxFIGBjZW50ZXJfdGFibGVgICgKICBgY2VudGVyX3RhYmxlX2lkYCBpbnQoMTEpIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBydF9pZGAgaW50KDExKSBERUZBVUxUIE5VTEwsCiAgYGN0X2YxYCB2YXJjaGFyKDEwKSBERUZBVUxUIE5VTEwsCiAgYGN0X2YyYCB2YXJjaGFyKDEwKSBERUZBVUxUIE5VTEwsCiAgUFJJTUFSWSBLRVkgKGBjZW50ZXJfdGFibGVfaWRgKQopIEVOR0lORT1Jbm5vREIgQVVUT19JTkNSRU1FTlQ9NCBERUZBVUxUIENIQVJTRVQ9dXRmOG1iNDsKCkNSRUFURSBUQUJMRSBgY29sb3JzYCAoCiAgYGlkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBuYW1lYCB2YXJjaGFyKDIwKSBERUZBVUxUIE5VTEwsCiAgYHZhbHVlYCB2YXJjaGFyKDEwKSBERUZBVUxUIE5VTEwsCiAgUFJJTUFSWSBLRVkgKGBpZGApCikgRU5HSU5FPUlubm9EQiBBVVRPX0lOQ1JFTUVOVD0yMSBERUZBVUxUIENIQVJTRVQ9bGF0aW4xOwoKQ1JFQVRFIFRBQkxFIGBlbXBsb3llZXNgICgKICBgZW1wbG95ZWVOdW1iZXJgIGludCgxMSkgTk9UIE5VTEwsCiAgYGxhc3ROYW1lYCB2YXJjaGFyKDUwKSBOT1QgTlVMTCwKICBgZmlyc3ROYW1lYCB2YXJjaGFyKDUwKSBOT1QgTlVMTCwKICBgZXh0ZW5zaW9uYCB2YXJjaGFyKDEwKSBOT1QgTlVMTCwKICBgZW1haWxgIHZhcmNoYXIoMTAwKSBOT1QgTlVMTCwKICBgb2ZmaWNlQ29kZWAgdmFyY2hhcigxMCkgTk9UIE5VTEwsCiAgYHJlcG9ydHNUb2AgaW50KDExKSBERUZBVUxUIE5VTEwsCiAgYGpvYlRpdGxlYCB2YXJjaGFyKDUwKSBOT1QgTlVMTCwKICBQUklNQVJZIEtFWSAoYGVtcGxveWVlTnVtYmVyYCksCiAgS0VZIGByZXBvcnRzVG9gIChgcmVwb3J0c1RvYCksCiAgS0VZIGBvZmZpY2VDb2RlYCAoYG9mZmljZUNvZGVgKQopIEVOR0lORT1Jbm5vREIgREVGQVVMVCBDSEFSU0VUPWxhdGluMTsKCkRST1AgVEFCTEUgSUYgRVhJU1RTIGBncm91cHNgOwoKQ1JFQVRFIFRBQkxFIGBncm91cHNgICgKICBgaWRgIGludCgxMSkgdW5zaWduZWQgTk9UIE5VTEwgQVVUT19JTkNSRU1FTlQsCiAgYG5hbWVgIHZhcmNoYXIoMzApIERFRkFVTFQgTlVMTCwKICBgY29sb3JfaWRgIGludCgxMSkgdW5zaWduZWQgREVGQVVMVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKQopIEVOR0lORT1Jbm5vREIgQVVUT19JTkNSRU1FTlQ9MTMgREVGQVVMVCBDSEFSU0VUPWxhdGluMTs="></div>
<div id="toDoListCode" class="hidden invisible hide"
data-code="CREATE TABLE `bucket` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fDate` datetime DEFAULT NULL,
  `fTime` datetime DEFAULT NULL,
  `fImage` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `fLink` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `fCheckbox` tinyint(1) DEFAULT NULL,
  `fRadio` tinyint(1) DEFAULT NULL,
  `fFile` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `fInput` varchar(150) CHARACTER SET latin1 DEFAULT NULL,
  `fNumber` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf32;

CREATE TABLE `center_table` (
  `center_table_id` int(11) NOT NULL AUTO_INCREMENT,
  `rt_id` int(11) DEFAULT NULL,
  `ct_f1` varchar(10) DEFAULT NULL,
  `ct_f2` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`center_table_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE `colors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) DEFAULT NULL,
  `value` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

CREATE TABLE `employees` (
  `employeeNumber` int(11) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `officeCode` varchar(10) NOT NULL,
  `reportsTo` int(11) DEFAULT NULL,
  `jobTitle` varchar(50) NOT NULL,
  PRIMARY KEY (`employeeNumber`),
  KEY `reportsTo` (`reportsTo`),
  KEY `officeCode` (`officeCode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `groups`;

CREATE TABLE `groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `color_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;">
</div>

<script>

    $(document).ready(function () {

        console.log(sessionStorage.getItem("projectId"));
        console.log(randomColor());
        $("#projectName").val(randomColor());

        $("#processDatabaseSchema").click(function (e) { 
            e.preventDefault();
            if ($("#projectName").val() == "") {
                $("#projectNameError").text("Project name cannot be empty").removeClass("invisible");
                $("#projectNameError").focus();
                toastr.error("Project name cannot be empty");
            } else {
                processDatabaseSchema();
            }
        });

        $("#projectName").keydown(function () {
            $("#projectNameError").text("").addClass("invisible");
        });

        var editor = CodeMirror.fromTextArea(document.getElementById("databaseSchema"), {
            lineNumbers: true,
            autoFormat: true
        });

        $("#projectName").focus();

        $("#loadToDoList").click(function (e) { 
            e.preventDefault();
            // console.log(window.atob($("#toDoListCode2").data("code")));
            editor.getDoc().setValue(window.atob($("#toDoListCode2").data("code")));
        });

        function processDatabaseSchema() {
            showLoadingScreen();

            var thetext = editor.getValue();
            var replaced = thetext.replace(/(?:\/\*(?:[\s\S]*?)\*\/)|(?:[\s;]+\/\/(?:.*)$)/gm, '');
            replaced = replaced.replace(/^\s*[;]/gm, '');
            replaced = replaced.replace(/^\s*[\r\n]/gm, '');
            editor.setValue(replaced);

            $.ajax({
                type: "post",
                url: "importSchema",
                data: {
                    "name": $("#projectName").val(),
                    "data": editor.getValue(),
                    "processAllTables": $("#processAllTables").is(':checked')
                },
                dataType: "json",
                success: function (response) {
                    
                },
                error: function (response) {
                    toastr.error("Error: " + response.responseText, "Error importing schema");
                    hideLoadingScreen();
                },
                complete: function (response) {
                    window.location = "http://localhost:8080/projects/" + response.responseJSON.project_hash;
                },
            });
        }
    });    
    
</script>

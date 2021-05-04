<? echo view("projects/menu"); ?>
<style>
    .CodeMirror { border: solid 1px #AAAAAA !important; }
</style>
<div class="container-fluid mx-auto">
    <div class="col-sm-6 offset-sm-3 pt-5">
        <div class="card">
            <h4 class="card-header">Import SQL schema</h4>
            <div class="card-body pb-0">
                <h6 class="card-title">Project name: <small class="bold text-danger">*</small></h6>
                <div class="pb-3">
                    <input type="text" name="projectName" id="projectName" class="pl-1 form-control" value="" autocomplete="off">
                    <div class="small error text-danger invisible" name="projectNameError" id="projectNameError"></div>
                    <div class="pt-3 hide">
                        <input type="checkbox" name="processAllTables" id="processAllTables" value="0" checked="">
                        <label for="processAllTables">Process tables after import</label>
                    </div>
                </div>

                <h6 class="card-title">Description:</h6>
                <div class="pb-3">
                    <textarea rows="3" name="projectDescription" id="projectDescription" class="pl-1 form-control" value=""></textarea>
                    <div class="small error text-danger invisible" name="projectDescriptionError" id="projectDescriptionError"></div>
                </div>

                <div class="">
                    <label class="">Paste your SQL schema code below or choose from one of the examples: <small class="bold text-danger">*</small></label>
                    <div class="d-row">
                        <button type="button" class="exampleCode btn btn-primary mb-3" id="ToDoList" name="ToDoList">To Do List</button>
                        <button type="button" class="exampleCode btn btn-primary mb-3" id="FAQs" name="FAQs">FAQs</button>
                        <button type="button" class="exampleCode btn btn-primary mb-3" id="SimpleForum" name="SimpleForum">Simple Forum</button>
                        <button type="button" class="exampleCode btn btn-primary mb-3" id="MySqlExample" name="MySqlExample">MySql Example</button>
                        <button type="button" class="exampleCode btn btn-primary mb-3" id="Authentication" name="Authentication">Authentication</button>
                    </div>                        
                </div>
                
                <textarea name="databaseSchema" id="databaseSchema" class="w-100" rows="10" value=""></textarea>
                <button type="button" class="btn btn-primary mt-3" id="processDatabaseSchema" name="processDatabaseSchema">Proceed</button>
                <div class="small error text-danger invisible">Project name cannot be empty</div>
            </div>
        </div>
    </div>
</div>

<script src="/codemirror/codemirror.js"></script>
<script src="/codemirror/sql.js"></script>
<link href="/codemirror/codemirror.css" rel="stylesheet">

<div id="ToDoListCode" class="hidden invisible hide" data-code="Q1JFQVRFIFRBQkxFIGBidWNrZXRgICgKICBgaWRgIGludCgxMSkgdW5zaWduZWQgTk9UIE5VTEwgQVVUT19JTkNSRU1FTlQsCiAgYGZEYXRlYCBkYXRldGltZSBERUZBVUxUIE5VTEwsCiAgYGZUaW1lYCBkYXRldGltZSBERUZBVUxUIE5VTEwsCiAgYGZJbWFnZWAgdmFyY2hhcigxNTApIENIQVJBQ1RFUiBTRVQgbGF0aW4xIERFRkFVTFQgTlVMTCwKICBgZkxpbmtgIHZhcmNoYXIoMTUwKSBDSEFSQUNURVIgU0VUIGxhdGluMSBERUZBVUxUIE5VTEwsCiAgYGZDaGVja2JveGAgdGlueWludCgxKSBERUZBVUxUIE5VTEwsCiAgYGZSYWRpb2AgdGlueWludCgxKSBERUZBVUxUIE5VTEwsCiAgYGZGaWxlYCB2YXJjaGFyKDE1MCkgQ0hBUkFDVEVSIFNFVCBsYXRpbjEgREVGQVVMVCBOVUxMLAogIGBmSW5wdXRgIHZhcmNoYXIoMTUwKSBDSEFSQUNURVIgU0VUIGxhdGluMSBERUZBVUxUIE5VTEwsCiAgYGZOdW1iZXJgIGludCgxMSkgREVGQVVMVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKQopIEVOR0lORT1NeUlTQU0gREVGQVVMVCBDSEFSU0VUPXV0ZjMyOwoKQ1JFQVRFIFRBQkxFIGBjZW50ZXJfdGFibGVgICgKICBgY2VudGVyX3RhYmxlX2lkYCBpbnQoMTEpIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBydF9pZGAgaW50KDExKSBERUZBVUxUIE5VTEwsCiAgYGN0X2YxYCB2YXJjaGFyKDEwKSBERUZBVUxUIE5VTEwsCiAgYGN0X2YyYCB2YXJjaGFyKDEwKSBERUZBVUxUIE5VTEwsCiAgUFJJTUFSWSBLRVkgKGBjZW50ZXJfdGFibGVfaWRgKQopIEVOR0lORT1Jbm5vREIgQVVUT19JTkNSRU1FTlQ9NCBERUZBVUxUIENIQVJTRVQ9dXRmOG1iNDsKCkNSRUFURSBUQUJMRSBgY29sb3JzYCAoCiAgYGlkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBuYW1lYCB2YXJjaGFyKDIwKSBERUZBVUxUIE5VTEwsCiAgYHZhbHVlYCB2YXJjaGFyKDEwKSBERUZBVUxUIE5VTEwsCiAgUFJJTUFSWSBLRVkgKGBpZGApCikgRU5HSU5FPUlubm9EQiBBVVRPX0lOQ1JFTUVOVD0yMSBERUZBVUxUIENIQVJTRVQ9bGF0aW4xOwoKQ1JFQVRFIFRBQkxFIGBlbXBsb3llZXNgICgKICBgZW1wbG95ZWVOdW1iZXJgIGludCgxMSkgTk9UIE5VTEwsCiAgYGxhc3ROYW1lYCB2YXJjaGFyKDUwKSBOT1QgTlVMTCwKICBgZmlyc3ROYW1lYCB2YXJjaGFyKDUwKSBOT1QgTlVMTCwKICBgZXh0ZW5zaW9uYCB2YXJjaGFyKDEwKSBOT1QgTlVMTCwKICBgZW1haWxgIHZhcmNoYXIoMTAwKSBOT1QgTlVMTCwKICBgb2ZmaWNlQ29kZWAgdmFyY2hhcigxMCkgTk9UIE5VTEwsCiAgYHJlcG9ydHNUb2AgaW50KDExKSBERUZBVUxUIE5VTEwsCiAgYGpvYlRpdGxlYCB2YXJjaGFyKDUwKSBOT1QgTlVMTCwKICBQUklNQVJZIEtFWSAoYGVtcGxveWVlTnVtYmVyYCksCiAgS0VZIGByZXBvcnRzVG9gIChgcmVwb3J0c1RvYCksCiAgS0VZIGBvZmZpY2VDb2RlYCAoYG9mZmljZUNvZGVgKQopIEVOR0lORT1Jbm5vREIgREVGQVVMVCBDSEFSU0VUPWxhdGluMTsKCkRST1AgVEFCTEUgSUYgRVhJU1RTIGBncm91cHNgOwoKQ1JFQVRFIFRBQkxFIGBncm91cHNgICgKICBgaWRgIGludCgxMSkgdW5zaWduZWQgTk9UIE5VTEwgQVVUT19JTkNSRU1FTlQsCiAgYG5hbWVgIHZhcmNoYXIoMzApIERFRkFVTFQgTlVMTCwKICBgY29sb3JfaWRgIGludCgxMSkgdW5zaWduZWQgREVGQVVMVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKQopIEVOR0lORT1Jbm5vREIgQVVUT19JTkNSRU1FTlQ9MTMgREVGQVVMVCBDSEFSU0VUPWxhdGluMTs="></div>
<div id="FAQsCode" class="hidden invisible hide" data-code=""></div>
<div id="SimpleForumCode" class="hidden invisible hide" data-code=""></div>
<div id="MySqlExampleCode" class="hidden invisible hide" data-code=""></div>
<div id="AuthenticationCode" class="hidden invisible hide" data-code="LyoKU1FMeW9nIFVsdGltYXRlIHYxMS4zMyAoNjQgYml0KQpNeVNRTCAtIDEwLjQuMTEtTWFyaWFEQiA6IERhdGFiYXNlIC0gb25saW5lCioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKioqKgoqLwoKCi8qITQwMTAxIFNFVCBOQU1FUyB1dGY4ICovOwoKLyohNDAxMDEgU0VUIFNRTF9NT0RFPScnKi87CgovKiE0MDAxNCBTRVQgQE9MRF9VTklRVUVfQ0hFQ0tTPUBAVU5JUVVFX0NIRUNLUywgVU5JUVVFX0NIRUNLUz0wICovOwovKiE0MDAxNCBTRVQgQE9MRF9GT1JFSUdOX0tFWV9DSEVDS1M9QEBGT1JFSUdOX0tFWV9DSEVDS1MsIEZPUkVJR05fS0VZX0NIRUNLUz0wICovOwovKiE0MDEwMSBTRVQgQE9MRF9TUUxfTU9ERT1AQFNRTF9NT0RFLCBTUUxfTU9ERT0nTk9fQVVUT19WQUxVRV9PTl9aRVJPJyAqLzsKLyohNDAxMTEgU0VUIEBPTERfU1FMX05PVEVTPUBAU1FMX05PVEVTLCBTUUxfTk9URVM9MCAqLzsKQ1JFQVRFIERBVEFCQVNFIC8qITMyMzEyIElGIE5PVCBFWElTVFMqL2BvbmxpbmVgIC8qITQwMTAwIERFRkFVTFQgQ0hBUkFDVEVSIFNFVCB1dGY4bWI0ICovOwoKLypUYWJsZSBzdHJ1Y3R1cmUgZm9yIHRhYmxlIGBhdXRoX2FjdGl2YXRpb25fYXR0ZW1wdHNgICovCgpEUk9QIFRBQkxFIElGIEVYSVNUUyBgYXV0aF9hY3RpdmF0aW9uX2F0dGVtcHRzYDsKCkNSRUFURSBUQUJMRSBgYXV0aF9hY3RpdmF0aW9uX2F0dGVtcHRzYCAoCiAgYGlkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBpcF9hZGRyZXNzYCB2YXJjaGFyKDI1NSkgTk9UIE5VTEwsCiAgYHVzZXJfYWdlbnRgIHZhcmNoYXIoMjU1KSBOT1QgTlVMTCwKICBgdG9rZW5gIHZhcmNoYXIoMjU1KSBERUZBVUxUIE5VTEwsCiAgYGNyZWF0ZWRfYXRgIGRhdGV0aW1lIE5PVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKQopIEVOR0lORT1Jbm5vREIgQVVUT19JTkNSRU1FTlQ9MyBERUZBVUxUIENIQVJTRVQ9dXRmODsKCi8qVGFibGUgc3RydWN0dXJlIGZvciB0YWJsZSBgYXV0aF9ncm91cHNgICovCgpEUk9QIFRBQkxFIElGIEVYSVNUUyBgYXV0aF9ncm91cHNgOwoKQ1JFQVRFIFRBQkxFIGBhdXRoX2dyb3Vwc2AgKAogIGBpZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCBBVVRPX0lOQ1JFTUVOVCwKICBgbmFtZWAgdmFyY2hhcigyNTUpIE5PVCBOVUxMLAogIGBkZXNjcmlwdGlvbmAgdmFyY2hhcigyNTUpIE5PVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKQopIEVOR0lORT1Jbm5vREIgREVGQVVMVCBDSEFSU0VUPXV0Zjg7CgovKlRhYmxlIHN0cnVjdHVyZSBmb3IgdGFibGUgYGF1dGhfZ3JvdXBzX3Blcm1pc3Npb25zYCAqLwoKRFJPUCBUQUJMRSBJRiBFWElTVFMgYGF1dGhfZ3JvdXBzX3Blcm1pc3Npb25zYDsKCkNSRUFURSBUQUJMRSBgYXV0aF9ncm91cHNfcGVybWlzc2lvbnNgICgKICBgZ3JvdXBfaWRgIGludCgxMSkgdW5zaWduZWQgTk9UIE5VTEwgREVGQVVMVCAwLAogIGBwZXJtaXNzaW9uX2lkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIERFRkFVTFQgMCwKICBLRVkgYGF1dGhfZ3JvdXBzX3Blcm1pc3Npb25zX3Blcm1pc3Npb25faWRfZm9yZWlnbmAgKGBwZXJtaXNzaW9uX2lkYCksCiAgS0VZIGBncm91cF9pZF9wZXJtaXNzaW9uX2lkYCAoYGdyb3VwX2lkYCxgcGVybWlzc2lvbl9pZGApLAogIENPTlNUUkFJTlQgYGF1dGhfZ3JvdXBzX3Blcm1pc3Npb25zX2dyb3VwX2lkX2ZvcmVpZ25gIEZPUkVJR04gS0VZIChgZ3JvdXBfaWRgKSBSRUZFUkVOQ0VTIGBhdXRoX2dyb3Vwc2AgKGBpZGApIE9OIERFTEVURSBDQVNDQURFLAogIENPTlNUUkFJTlQgYGF1dGhfZ3JvdXBzX3Blcm1pc3Npb25zX3Blcm1pc3Npb25faWRfZm9yZWlnbmAgRk9SRUlHTiBLRVkgKGBwZXJtaXNzaW9uX2lkYCkgUkVGRVJFTkNFUyBgYXV0aF9wZXJtaXNzaW9uc2AgKGBpZGApIE9OIERFTEVURSBDQVNDQURFCikgRU5HSU5FPUlubm9EQiBERUZBVUxUIENIQVJTRVQ9dXRmODsKCi8qVGFibGUgc3RydWN0dXJlIGZvciB0YWJsZSBgYXV0aF9ncm91cHNfdXNlcnNgICovCgpEUk9QIFRBQkxFIElGIEVYSVNUUyBgYXV0aF9ncm91cHNfdXNlcnNgOwoKQ1JFQVRFIFRBQkxFIGBhdXRoX2dyb3Vwc191c2Vyc2AgKAogIGBncm91cF9pZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCBERUZBVUxUIDAsCiAgYHVzZXJfaWRgIGludCgxMSkgdW5zaWduZWQgTk9UIE5VTEwgREVGQVVMVCAwLAogIEtFWSBgYXV0aF9ncm91cHNfdXNlcnNfdXNlcl9pZF9mb3JlaWduYCAoYHVzZXJfaWRgKSwKICBLRVkgYGdyb3VwX2lkX3VzZXJfaWRgIChgZ3JvdXBfaWRgLGB1c2VyX2lkYCksCiAgQ09OU1RSQUlOVCBgYXV0aF9ncm91cHNfdXNlcnNfZ3JvdXBfaWRfZm9yZWlnbmAgRk9SRUlHTiBLRVkgKGBncm91cF9pZGApIFJFRkVSRU5DRVMgYGF1dGhfZ3JvdXBzYCAoYGlkYCkgT04gREVMRVRFIENBU0NBREUsCiAgQ09OU1RSQUlOVCBgYXV0aF9ncm91cHNfdXNlcnNfdXNlcl9pZF9mb3JlaWduYCBGT1JFSUdOIEtFWSAoYHVzZXJfaWRgKSBSRUZFUkVOQ0VTIGB1c2Vyc2AgKGBpZGApIE9OIERFTEVURSBDQVNDQURFCikgRU5HSU5FPUlubm9EQiBERUZBVUxUIENIQVJTRVQ9dXRmODsKCi8qVGFibGUgc3RydWN0dXJlIGZvciB0YWJsZSBgYXV0aF9sb2dpbnNgICovCgpEUk9QIFRBQkxFIElGIEVYSVNUUyBgYXV0aF9sb2dpbnNgOwoKQ1JFQVRFIFRBQkxFIGBhdXRoX2xvZ2luc2AgKAogIGBpZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCBBVVRPX0lOQ1JFTUVOVCwKICBgaXBfYWRkcmVzc2AgdmFyY2hhcigyNTUpIERFRkFVTFQgTlVMTCwKICBgZW1haWxgIHZhcmNoYXIoMjU1KSBERUZBVUxUIE5VTEwsCiAgYHVzZXJfaWRgIGludCgxMSkgdW5zaWduZWQgREVGQVVMVCBOVUxMLAogIGBkYXRlYCBkYXRldGltZSBOT1QgTlVMTCwKICBgc3VjY2Vzc2AgdGlueWludCgxKSBOT1QgTlVMTCwKICBQUklNQVJZIEtFWSAoYGlkYCksCiAgS0VZIGBlbWFpbGAgKGBlbWFpbGApLAogIEtFWSBgdXNlcl9pZGAgKGB1c2VyX2lkYCkKKSBFTkdJTkU9SW5ub0RCIEFVVE9fSU5DUkVNRU5UPTY0IERFRkFVTFQgQ0hBUlNFVD11dGY4OwoKLypUYWJsZSBzdHJ1Y3R1cmUgZm9yIHRhYmxlIGBhdXRoX3Blcm1pc3Npb25zYCAqLwoKRFJPUCBUQUJMRSBJRiBFWElTVFMgYGF1dGhfcGVybWlzc2lvbnNgOwoKQ1JFQVRFIFRBQkxFIGBhdXRoX3Blcm1pc3Npb25zYCAoCiAgYGlkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBuYW1lYCB2YXJjaGFyKDI1NSkgTk9UIE5VTEwsCiAgYGRlc2NyaXB0aW9uYCB2YXJjaGFyKDI1NSkgTk9UIE5VTEwsCiAgUFJJTUFSWSBLRVkgKGBpZGApCikgRU5HSU5FPUlubm9EQiBERUZBVUxUIENIQVJTRVQ9dXRmODsKCi8qVGFibGUgc3RydWN0dXJlIGZvciB0YWJsZSBgYXV0aF9yZXNldF9hdHRlbXB0c2AgKi8KCkRST1AgVEFCTEUgSUYgRVhJU1RTIGBhdXRoX3Jlc2V0X2F0dGVtcHRzYDsKCkNSRUFURSBUQUJMRSBgYXV0aF9yZXNldF9hdHRlbXB0c2AgKAogIGBpZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCBBVVRPX0lOQ1JFTUVOVCwKICBgZW1haWxgIHZhcmNoYXIoMjU1KSBOT1QgTlVMTCwKICBgaXBfYWRkcmVzc2AgdmFyY2hhcigyNTUpIE5PVCBOVUxMLAogIGB1c2VyX2FnZW50YCB2YXJjaGFyKDI1NSkgTk9UIE5VTEwsCiAgYHRva2VuYCB2YXJjaGFyKDI1NSkgREVGQVVMVCBOVUxMLAogIGBjcmVhdGVkX2F0YCBkYXRldGltZSBOT1QgTlVMTCwKICBQUklNQVJZIEtFWSAoYGlkYCkKKSBFTkdJTkU9SW5ub0RCIERFRkFVTFQgQ0hBUlNFVD11dGY4OwoKLypUYWJsZSBzdHJ1Y3R1cmUgZm9yIHRhYmxlIGBhdXRoX3Rva2Vuc2AgKi8KCkRST1AgVEFCTEUgSUYgRVhJU1RTIGBhdXRoX3Rva2Vuc2A7CgpDUkVBVEUgVEFCTEUgYGF1dGhfdG9rZW5zYCAoCiAgYGlkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIEFVVE9fSU5DUkVNRU5ULAogIGBzZWxlY3RvcmAgdmFyY2hhcigyNTUpIE5PVCBOVUxMLAogIGBoYXNoZWRWYWxpZGF0b3JgIHZhcmNoYXIoMjU1KSBOT1QgTlVMTCwKICBgdXNlcl9pZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCwKICBgZXhwaXJlc2AgZGF0ZXRpbWUgTk9UIE5VTEwsCiAgUFJJTUFSWSBLRVkgKGBpZGApLAogIEtFWSBgYXV0aF90b2tlbnNfdXNlcl9pZF9mb3JlaWduYCAoYHVzZXJfaWRgKSwKICBLRVkgYHNlbGVjdG9yYCAoYHNlbGVjdG9yYCksCiAgQ09OU1RSQUlOVCBgYXV0aF90b2tlbnNfdXNlcl9pZF9mb3JlaWduYCBGT1JFSUdOIEtFWSAoYHVzZXJfaWRgKSBSRUZFUkVOQ0VTIGB1c2Vyc2AgKGBpZGApIE9OIERFTEVURSBDQVNDQURFCikgRU5HSU5FPUlubm9EQiBERUZBVUxUIENIQVJTRVQ9dXRmODsKCi8qVGFibGUgc3RydWN0dXJlIGZvciB0YWJsZSBgYXV0aF91c2Vyc19wZXJtaXNzaW9uc2AgKi8KCkRST1AgVEFCTEUgSUYgRVhJU1RTIGBhdXRoX3VzZXJzX3Blcm1pc3Npb25zYDsKCkNSRUFURSBUQUJMRSBgYXV0aF91c2Vyc19wZXJtaXNzaW9uc2AgKAogIGB1c2VyX2lkYCBpbnQoMTEpIHVuc2lnbmVkIE5PVCBOVUxMIERFRkFVTFQgMCwKICBgcGVybWlzc2lvbl9pZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCBERUZBVUxUIDAsCiAgS0VZIGBhdXRoX3VzZXJzX3Blcm1pc3Npb25zX3Blcm1pc3Npb25faWRfZm9yZWlnbmAgKGBwZXJtaXNzaW9uX2lkYCksCiAgS0VZIGB1c2VyX2lkX3Blcm1pc3Npb25faWRgIChgdXNlcl9pZGAsYHBlcm1pc3Npb25faWRgKSwKICBDT05TVFJBSU5UIGBhdXRoX3VzZXJzX3Blcm1pc3Npb25zX3Blcm1pc3Npb25faWRfZm9yZWlnbmAgRk9SRUlHTiBLRVkgKGBwZXJtaXNzaW9uX2lkYCkgUkVGRVJFTkNFUyBgYXV0aF9wZXJtaXNzaW9uc2AgKGBpZGApIE9OIERFTEVURSBDQVNDQURFLAogIENPTlNUUkFJTlQgYGF1dGhfdXNlcnNfcGVybWlzc2lvbnNfdXNlcl9pZF9mb3JlaWduYCBGT1JFSUdOIEtFWSAoYHVzZXJfaWRgKSBSRUZFUkVOQ0VTIGB1c2Vyc2AgKGBpZGApIE9OIERFTEVURSBDQVNDQURFCikgRU5HSU5FPUlubm9EQiBERUZBVUxUIENIQVJTRVQ9dXRmODsKCi8qVGFibGUgc3RydWN0dXJlIGZvciB0YWJsZSBgdXNlcnNgICovCgpEUk9QIFRBQkxFIElGIEVYSVNUUyBgdXNlcnNgOwoKQ1JFQVRFIFRBQkxFIGB1c2Vyc2AgKAogIGBpZGAgaW50KDExKSB1bnNpZ25lZCBOT1QgTlVMTCBBVVRPX0lOQ1JFTUVOVCwKICBgZW1haWxgIHZhcmNoYXIoMjU1KSBOT1QgTlVMTCwKICBgdXNlcm5hbWVgIHZhcmNoYXIoMzApIERFRkFVTFQgTlVMTCwKICBgcGFzc3dvcmRfaGFzaGAgdmFyY2hhcigyNTUpIE5PVCBOVUxMLAogIGByZXNldF9oYXNoYCB2YXJjaGFyKDI1NSkgREVGQVVMVCBOVUxMLAogIGByZXNldF9hdGAgZGF0ZXRpbWUgREVGQVVMVCBOVUxMLAogIGByZXNldF9leHBpcmVzYCBkYXRldGltZSBERUZBVUxUIE5VTEwsCiAgYGFjdGl2YXRlX2hhc2hgIHZhcmNoYXIoMjU1KSBERUZBVUxUIE5VTEwsCiAgYHN0YXR1c2AgdmFyY2hhcigyNTUpIERFRkFVTFQgTlVMTCwKICBgc3RhdHVzX21lc3NhZ2VgIHZhcmNoYXIoMjU1KSBERUZBVUxUIE5VTEwsCiAgYGFjdGl2ZWAgdGlueWludCgxKSBOT1QgTlVMTCBERUZBVUxUIDAsCiAgYGZvcmNlX3Bhc3NfcmVzZXRgIHRpbnlpbnQoMSkgTk9UIE5VTEwgREVGQVVMVCAwLAogIGBjcmVhdGVkX2F0YCBkYXRldGltZSBERUZBVUxUIE5VTEwsCiAgYHVwZGF0ZWRfYXRgIGRhdGV0aW1lIERFRkFVTFQgTlVMTCwKICBgZGVsZXRlZF9hdGAgZGF0ZXRpbWUgREVGQVVMVCBOVUxMLAogIFBSSU1BUlkgS0VZIChgaWRgKSwKICBVTklRVUUgS0VZIGBlbWFpbGAgKGBlbWFpbGApLAogIFVOSVFVRSBLRVkgYHVzZXJuYW1lYCAoYHVzZXJuYW1lYCkKKSBFTkdJTkU9SW5ub0RCIEFVVE9fSU5DUkVNRU5UPTIgREVGQVVMVCBDSEFSU0VUPXV0Zjg7CgovKiE0MDEwMSBTRVQgU1FMX01PREU9QE9MRF9TUUxfTU9ERSAqLzsKLyohNDAwMTQgU0VUIEZPUkVJR05fS0VZX0NIRUNLUz1AT0xEX0ZPUkVJR05fS0VZX0NIRUNLUyAqLzsKLyohNDAwMTQgU0VUIFVOSVFVRV9DSEVDS1M9QE9MRF9VTklRVUVfQ0hFQ0tTICovOwovKiE0MDExMSBTRVQgU1FMX05PVEVTPUBPTERfU1FMX05PVEVTICovOwo="></div>

<script>

    $(document).ready(function () {
        // Proceed button was pressed
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

        $(".exampleCode").click(function (e) { 
            e.preventDefault();
            editor.getDoc().setValue(window.atob($("#" + $(this).attr("id") + "Code").data("code")));
        });

        function processDatabaseSchema() {
            showLoadingScreen();

            var thetext = editor.getValue();
            var replaced = thetext.replace(/(?:\/\*(?:[\s\S]*?)\*\/)|(?:[\s;]+\/\/(?:.*)$)/gm, '');
            replaced = replaced.replace(/^\s*[;]/gm, '');
            replaced = replaced.replace(/^\s*[\r\n]/gm, '');
            
            if (replaced.trim() == "") {
                toastr.info("Enter one or more import statements, or choose one of the examples", "No data");
                return false;
            }

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
                    if (response.status == "error") {
                        toastr.error(response.message, response.code);
                    }
                    console.log("Success");
                    console.log(response);
                    toastr.success("Redirecting...", "Project was succesffully created");
                },
                error: function (response) {
                    toastr.error("Error: " + response.message, "Error importing schema");
                    hideLoadingScreen();
                },
                complete: function (response) {
                    console.log("Complete");
                    console.log(response);
                    window.location = "http://localhost:8080/projects/" + response.responseJSON.project_hash;
                },
            });
        }
    });    
    
</script>

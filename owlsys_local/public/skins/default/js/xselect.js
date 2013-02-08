/* ===================================================

 * xselect.js v1.0
 * http://rogercastaneda.com/xselect.html
 * ===================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
(function($) {
	$.fn.xselect = function(){
		var args = arguments[0] || {};
		var url = args.url;
		var data = args.data;
		var $subselect = args.subselect;
		$subselect.empty();
		$subselect.prepend($('<option></option>').attr("value", 0).text( args.lblSelectOption ));
		$subselect.trigger('change');
		if ( $(this).val() > 0 ) {
    		$.post(url, data, function(xdata, textStatus) {
    			if ( xdata != null ) {
    				$.each(xdata.data, function () {
    				    $subselect.append($('<option></option>').attr("value", this.id).text(this.lbl));
    			    });
    				$('#xtk').val(xdata.xtk);
    			};
    		}, "json");
		};
	};
})(jQuery);
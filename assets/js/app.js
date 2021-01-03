/*
 * This file is part of Part-DB (https://github.com/Part-DB/Part-DB-symfony).
 *
 * Copyright (C) 2019 - 2020 Jan Böhmer (https://github.com/jbtronics)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)

require('../css/app.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

//Only include javascript



import '@fortawesome/fontawesome-free/css/all.css'

import 'datatables.net-bs4/css/dataTables.bootstrap4.css'
import 'datatables.net-buttons-bs4/css/buttons.bootstrap4.css'
import 'datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.css'
import 'datatables.net-select-bs4/css/select.bootstrap4.css'
import 'bootstrap-select/dist/css/bootstrap-select.css'

import "patternfly-bootstrap-treeview/src/css/bootstrap-treeview.css"

import "bootstrap-fileinput/css/fileinput.css"

require('bootstrap');

//require( 'jszip' );
//#require( 'pdfmake' );
require( 'datatables.net-bs4' );
require( 'datatables.net-buttons-bs4' );
require( 'datatables.net-buttons/js/buttons.colVis.js' );
//require( 'datatables.net-buttons/js/buttons.html5.js' );
//require( 'datatables.net-buttons/js/buttons.print.js' );
require( 'datatables.net-fixedheader-bs4' );
require( 'datatables.net-select-bs4' );
require('datatables.net-colreorder-bs4');
require('datatables.net-responsive-bs4');
require('datatables.net-responsive-bs4/css/responsive.bootstrap4.css');
require('bootstrap-select');
require('jquery-form');
require('corejs-typeahead/dist/typeahead.bundle.min');
window.Bloodhound =  require('corejs-typeahead/dist/bloodhound.js');

//Define jquery globally
window.$ = window.jQuery = require("jquery");

require('patternfly-bootstrap-treeview/src/js/bootstrap-treeview');

require('bootstrap-fileinput');

require('./datatables.js');

window.bootbox = require('bootbox');

require("marked");
window.DOMPurify = require("dompurify");

// Includes required for tag input
require('./tagsinput.js');
require('../css/tagsinput.css');

//Tristate checkbox support
require('./jquery.tristate.js');

require('darkmode-js');

//Equation rendering
require('katex');
window.renderMathInElement = require('katex/contrib/auto-render/auto-render').default;
import 'katex/dist/katex.css';

import Turbo from "@hotwired/turbo";


window.ClipboardJS = require('clipboard');

import './tree_loader';
import './datatables_loader';

//Require all events;
require('../ts_src/event_listeners');


//Register darkmode (we must do it here, TS does not support ES6 constructor...
try {
    //The browser needs to support mix blend mode
    if(typeof window.getComputedStyle(document.body).mixBlendMode !== 'undefined') {
        const darkmode = new Darkmode();

        $(document).on("ajaxUI:start ajaxUI:reload", function () {
            //Show darkmode toggle to user
            $('#toggleDarkmodeContainer, #toggleDarkmodeSeparator').removeAttr('hidden');
            $('#toggleDarkmode').prop('checked', darkmode.isActivated());
            $('#toggleDarkmode').change(function () {
                darkmode.toggle();
            });
        });
    }
} catch (e) {
    //Ignore all errors (for compatibiltiy with old browsers)
}
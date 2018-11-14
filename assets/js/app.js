import Routing from 'router';
//Regenerate fos_js_routes.js by running `yarn js-routing`
Routing.setRoutingData(require('./fos_js_routes.json'));

import './camdram.js';
import './autocomplete.js';
import './home.js';

import '../scss/app.scss';
import '../scss/autocomplete.scss';
import '../scss/diary.scss';
import '../scss/entities.scss';
import '../scss/forms.scss';
import '../scss/framework.scss';
import '../scss/home.scss';
import '../scss/news.scss';
import '../scss/venues.scss';

import "@fancyapps/fancybox/dist/jquery.fancybox.css";
import "../scss/jquery-ui.custom.css"

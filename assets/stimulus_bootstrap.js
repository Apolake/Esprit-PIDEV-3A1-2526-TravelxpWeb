import { startStimulusApp } from '@symfony/stimulus-bundle';
import GeoAutocompleteController from './controllers/geo_autocomplete_controller.js';
import GeoapifyController from './controllers/geoapify_controller.js';
import PropertyMapController from './controllers/property_map_controller.js';
import PropertyLocationPickerController from './controllers/property_location_picker_controller.js';
import RouteMapController from './controllers/route_map_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
app.register('geo-autocomplete', GeoAutocompleteController);
app.register('geoapify', GeoapifyController);
app.register('property-map', PropertyMapController);
app.register('property-location-picker', PropertyLocationPickerController);
app.register('route-map', RouteMapController);

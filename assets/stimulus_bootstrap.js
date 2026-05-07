import { startStimulusApp } from '@symfony/stimulus-bundle';
import ActivityCalendarController from './controllers/activity_calendar_controller.js';
import TripMapController from './controllers/trip_map_controller.js';
import LocationAutocompleteController from './controllers/location_autocomplete_controller.js';
import TripAiToolsController from './controllers/trip_ai_tools_controller.js';
import TripCardAiDrawerController from './controllers/trip_card_ai_drawer_controller.js';
import PropertyLocationPickerController from './controllers/property_location_picker_controller.js';
import RouteMapController from './controllers/route_map_controller.js';

const app = startStimulusApp();
// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);
app.register('activity-calendar', ActivityCalendarController);
app.register('trip-map', TripMapController);
app.register('location-autocomplete', LocationAutocompleteController);
app.register('trip-ai-tools', TripAiToolsController);
app.register('trip-card-ai-drawer', TripCardAiDrawerController);
app.register('property-location-picker', PropertyLocationPickerController);
app.register('route-map', RouteMapController);

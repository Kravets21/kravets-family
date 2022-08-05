import { startStimulusApp } from '@symfony/stimulus-bridge';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

require('bootstrap');

$('body').append('<div style="width:100%;height:'+document.documentElement.scrollHeight+'px;background:#000000;opacity:0.5;position: absolute;top: 0;z-index: 1000;"></div>')
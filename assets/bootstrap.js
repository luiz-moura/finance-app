import { startStimulusApp } from '@symfony/stimulus-bridge';

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

import Inputmask from 'inputmask';

Inputmask.extendAliases({
    'currency': {
        radixPoint: ',',
        groupSeparator: '.',
        allowMinus: false,
        prefix: 'R$ ',
        digits: 2,
        digitsOptional: false,
        rightAlign: true,
        removeMaskOnSubmit: true
    },
});

Inputmask('currency').mask('.inputmask-currency');

// register any custom, 3rd party controllers here
// app.register('some_controller_name', SomeImportedController);

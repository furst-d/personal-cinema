import React from 'react';
import {SimpleForm, NumberInput} from 'react-admin';

export const VideoConversionForm: React.FC = () => {
    return (
        <SimpleForm>
            <NumberInput source="width" label="Šířka" step={1} />
            <NumberInput source="height" label="Výška" step={1} />
            <NumberInput source="bandwidth" label="Šířka pásma" step={1} />
        </SimpleForm>
    )
};

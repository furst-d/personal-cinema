import React from 'react';
import {Create, SimpleForm, NumberInput} from 'react-admin';

export const VideoConversionCreate: React.FC = (props) => {
    return (
        <Create {...props}>
            <SimpleForm>
                <NumberInput source="width" label="Šířka" step={1} />
                <NumberInput source="height" label="Výška" step={1} />
                <NumberInput source="bandwidth" label="Šířka pásma" step={1} />
            </SimpleForm>
        </Create>
    )
};

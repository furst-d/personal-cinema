import React from 'react';
import { Edit, NumberInput, SimpleForm } from 'react-admin';

export const VideoConversionEdit: React.FC = (props) => {
    return (
        <Edit {...props}>
            <SimpleForm>
                <NumberInput source="width" label="Šířka" step={1} />
                <NumberInput source="height" label="Výška" step={1} />
                <NumberInput source="bandwidth" label="Šířka pásma" step={1} />
            </SimpleForm>
        </Edit>
    );
};

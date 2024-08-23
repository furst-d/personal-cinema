import React from 'react';
import {Create, NumberInput, SimpleForm, TextInput} from 'react-admin';

export const StorageUpgradeCreate: React.FC = (props) => {
    return (
        <Create {...props}>
            <SimpleForm>
                <TextInput source="email" label="Email" />
                <NumberInput source="size" label="Velikost" />
            </SimpleForm>
        </Create>
    )
};

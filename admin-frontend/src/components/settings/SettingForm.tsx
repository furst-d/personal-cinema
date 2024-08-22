import React from 'react';
import {SimpleForm, TextInput} from 'react-admin';

export const SettingForm: React.FC = () => {
    return (
        <SimpleForm>
            <TextInput source="key" label="Klíč" />
            <TextInput source="value" label="Hodnota" />
        </SimpleForm>
    )
};

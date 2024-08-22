import React from 'react';
import {Edit, NumberInput, SimpleForm, TextInput} from 'react-admin';
import {UnDeletableToolbar} from "../../toolbar/Toolbar";

export const StorageUserEdit: React.FC = (props) => {
    return (
        <Edit {...props}>
            <SimpleForm toolbar={<UnDeletableToolbar />}>
                <TextInput source="email" label="Email" disabled />
                <NumberInput source="maxStorage" label="Maximální úložiště" />
                <NumberInput source="usedStorage" label="Využité úložiště" disabled />
            </SimpleForm>
        </Edit>
    );
};

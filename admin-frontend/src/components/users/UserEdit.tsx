import React, { useEffect, useState } from 'react';
import { Edit, SimpleForm, TextInput, BooleanInput, SelectArrayInput } from 'react-admin';
import {userDataProvider} from "../../providers/users/userDataProvider";

export const UserEdit: React.FC = (props) => {
    const [roles, setRoles] = useState([]);

    useEffect(() => {
        userDataProvider.getRoles()
            .then(fetchedRoles => {
                setRoles(fetchedRoles);
            })
            .catch(error => {
                console.error('Error fetching roles:', error);
            });
    }, []);

    return (
        <Edit {...props}>
            <SimpleForm>
                <TextInput source="email" label="Email" />
                <BooleanInput source="isActive" label="Aktivní" />
                <SelectArrayInput source="roles" choices={roles} label="Role" />
            </SimpleForm>
        </Edit>
    );
};

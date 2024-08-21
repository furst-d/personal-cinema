import React, {useEffect, useState} from 'react';
import { Create, SimpleForm, TextInput, BooleanInput, SelectArrayInput } from 'react-admin';
import {userDataProvider} from "../../providers/users/userDataProvider";

export const UserCreate: React.FC = (props) => {
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
        <Create {...props}>
            <SimpleForm>
                <TextInput source="email" label="Email" />
                <TextInput source="password" label="Heslo" />
                <BooleanInput source="isActive" label="Aktivní" />
                <SelectArrayInput source="roles" choices={roles} label="Role" />
            </SimpleForm>
        </Create>
    )
};

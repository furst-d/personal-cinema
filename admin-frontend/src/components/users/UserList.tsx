import React from 'react';
import { List, Datagrid, TextField, EmailField, DateField, TextInput } from 'react-admin';
import CustomBooleanField from "../fields/CustomBooleanField";
import RoleField from "../fields/RoleField";

const emailFilter = [
    <TextInput label="Email" source="email" alwaysOn />,
];

export const UserList: React.FC = (props) => (
    <List {...props} filters={emailFilter}>
        <Datagrid rowClick="edit">
            <TextField source="id" label="ID" />
            <EmailField source="email" label="Email" />
            <DateField source="createdAt" label="Vytvořeno" />
            <RoleField source="roles" label="Role" sortable={false} />
            <CustomBooleanField source="isActive" label="Aktivní" />
        </Datagrid>
    </List>
);

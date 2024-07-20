import React from 'react';
import { List, Datagrid, TextField, EmailField, DateField } from 'react-admin';
import { ListProps } from 'react-admin';
import CustomBooleanField from "../fields/CustomBooleanField";
import RoleField from "../fields/RoleField";

export const UserList: React.FC<ListProps> = (props) => (
    <List {...props}>
        <Datagrid rowClick="edit">
            <TextField source="id" label="ID" />
            <EmailField source="email" label="Email" />
            <DateField source="createdAt" label="Vytvořeno" />
            <RoleField source="roles" label="Role" />
            <CustomBooleanField source="isActive" label="Aktivní" />
        </Datagrid>
    </List>
);

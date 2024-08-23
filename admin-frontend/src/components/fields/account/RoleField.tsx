import React from 'react';
import { useRecordContext } from 'react-admin';
import {FieldProps} from "../../types/field/FieldProps";

const RoleField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    if (!record || !record[source]) return <span>-</span>;

    const roles = record[source];
    const roleNames = roles.map((role: { name: string }) => role.name).join(', ');

    return <span>{roleNames}</span>;
};

export default RoleField;

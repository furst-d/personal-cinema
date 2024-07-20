import React from 'react';
import { useRecordContext } from 'react-admin';

interface RoleFieldProps {
    source: string;
    label?: string;
}

const RoleField: React.FC<RoleFieldProps> = ({ source, label }) => {
    const record = useRecordContext();
    if (!record || !record[source]) return null;

    const roles = record[source];
    const roleNames = roles.map((role: { name: string }) => role.name).join(', ');

    return <span>{roleNames}</span>;
};

export default RoleField;

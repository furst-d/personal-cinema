import React from 'react';
import {BooleanField, BooleanFieldProps, useRecordContext} from 'react-admin';

interface CustomBooleanFieldProps {
    source: string;
    label: string;
    sortable?: boolean;
}

const CustomBooleanField: React.FC<CustomBooleanFieldProps> = ({ source }) => {
    const record = useRecordContext();
    const value = record ? record[source] : false;

    return <span>{value ? 'Ano' : 'Ne'}</span>;
};

export default CustomBooleanField;

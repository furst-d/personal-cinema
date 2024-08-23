import React from 'react';
import {useRecordContext} from 'react-admin';
import {FieldProps} from "../types/field/FieldProps";

const CustomBooleanField: React.FC<FieldProps> = ({ source = "" }) => {
    const record = useRecordContext();
    const value = record ? record[source] : false;

    return <span>{value ? 'Ano' : 'Ne'}</span>;
};

export default CustomBooleanField;

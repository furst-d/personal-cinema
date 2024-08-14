import {styled} from "styled-components";
import {TableCell, TableContainer} from "@mui/material";

export const TableContainerStyle = styled(TableContainer)`
    background-color: ${({ theme }) => theme.secondary};
    color: ${({ theme }) => theme.textLight};
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
`;

export const TableCellStyle = styled(TableCell)`
    color: ${({ theme }) => theme.textLight};
    border-bottom: 1px solid ${({ theme }) => theme.textDark};
`;

export const TableHeadCellStyle = styled(TableCellStyle)`
    font-weight: bold;
    background-color: ${({ theme }) => theme.primaryDarker};
`;
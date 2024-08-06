import {styled} from "styled-components";
import {Box, Container, ListItem} from "@mui/material";

export const FileManagerContainerStyle = styled(Container)`
    background-color: ${(props) => props.theme.secondary};
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
`;

export const FileManagerListItemStyle = styled(ListItem)`
    align-items: center;
    background-color: ${(props) => props.theme.secondary};
    padding: 6px 10px;
    border-radius: 4px;
    display: flex;
    justify-content: space-between;
    color: ${(props) => props.theme.textLight};
    cursor: pointer;

    &:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }
`;

export const FileManagerSeparator = styled(Box)`
    width: 100%;
    height: 1px;
    background-color: ${(props) => props.theme.textLight};
    margin: 2px 0;
    opacity: 0.2;
`;

export const FileManagerEmptyFolderStyle = styled(Box)`
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
    color: ${(props) => props.theme.textLight};
    opacity: 0.5;
    text-align: center;
    margin: auto;
`;
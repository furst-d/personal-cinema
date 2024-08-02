import {styled} from "styled-components";
import {Box} from "@mui/material";

export const VideoDetailsStyle = styled(Box)`
    margin-top: 16px;
    background-color: ${(props) => props.theme.secondary};
    padding: 16px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    color: ${(props) => props.theme.text_light};
`;

export const DetailsGridStyle = styled(Box)`
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
`;

export const DetailsItemStyle = styled(Box)`
    display: flex;
    flex-direction: column;

    .MuiTypography-subtitle1 {
        font-weight: bold;
        color: ${(props) => props.theme.text_light};
    }

    .MuiTypography-body2 {
        color: ${(props) => props.theme.text_light};
    }
`;
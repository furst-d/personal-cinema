import styled from "styled-components";
import {Link} from "@mui/material";

export const CenterFormWrapperStyle = styled.div`
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    height: 100vh;
    padding: 20px;
    background-color: ${(props) => props.theme.secondary};
    color: ${(props) => props.theme.text_light};
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);

    @media (min-width: 769px) {
        justify-content: center;
        max-width: 400px;
        border-radius: 10px;
        height: auto;
    }
`;

export const StyledLink = styled(Link)`
    text-decoration: none !important;
    cursor: pointer;
    &:hover {
        text-decoration: underline !important;
    }
`;
import styled from "styled-components";
import {Box, InputAdornment, Link, TextField} from "@mui/material";
import SearchIcon from "@mui/icons-material/Search";
import React from "react";

export const CenterFormWrapperStyle = styled.div`
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    width: 100%;
    height: 100vh;
    background-color: ${(props) => props.theme.secondary};
    color: ${(props) => props.theme.textLight};
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    padding-top: 25px;

    @media (min-width: 769px) {
        justify-content: center;
        max-width: 400px;
        border-radius: 10px;
        height: auto;
        padding: 20px;
    }
`;

export const FormWrapperStyle = styled(Box)`
    display: flex;
    flex-direction: column;
    width: 100%;
    color: ${(props) => props.theme.textLight};

    @media (min-width: 769px) {
        max-width: 400px;
    }
`;

export const StyledLink = styled(Link)`
    text-decoration: none !important;
    cursor: pointer;
    &:hover {
        text-decoration: underline !important;
    }
`;

export const SearchTextFieldStyle = styled(({ ...props }) => (
    <TextField
        {...props}
        InputProps={{
            endAdornment: (
                <InputAdornment position="end">
                    <SearchIcon />
                </InputAdornment>
            ),
        }}
    />
))`
    width: 100%;
    .MuiOutlinedInput-root {
        border-radius: 8px;
        fieldset {
            border-color: ${({ theme }) => theme.textLight};
        }
        &:hover fieldset {
            border-color: #888;
        }
        &.Mui-focused fieldset {
            border-color: ${({ theme }) => theme.primary};
        }
    }
    .MuiInputAdornment-root {
        .MuiSvgIcon-root {
            color: ${({ theme }) => theme.primary};
        }
    }
`;
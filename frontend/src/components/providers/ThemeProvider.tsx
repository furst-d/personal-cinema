import React from 'react';
import { createGlobalStyle, ThemeProvider as StyledThemeProvider } from 'styled-components';
import { createTheme } from '@mui/material/styles';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { ThemeProvider as MuiThemeProvider } from '@mui/material';
import {ProviderProps} from "../../types/Layout";

export const theme = {
    background: '#323232',
    primary: '#b40000',
    primary_darker: '#750000',
    secondary: '#3f3f3f',
    third: '#fff',
    text_light: '#f3f3f3',
    text_dark: '#111',
};

const muiTheme = createTheme({
    palette: {
        primary: {
            main: theme.primary,
        },
        secondary: {
            main: theme.secondary,
        },
        background: {
            default: theme.background,
        },
        text: {
            primary: theme.text_light,
            secondary: theme.text_dark,
        },
    },
    components: {
        MuiButton: {
            styleOverrides: {
                root: {
                    backgroundColor: theme.primary,
                    color: theme.text_light,
                    '&:hover': {
                        backgroundColor: theme.primary_darker,
                    },
                },
            },
        },
        MuiTextField: {
            styleOverrides: {
                root: {
                    '& label': {
                        color: theme.text_light,
                    },
                    '& .MuiInputBase-input': {
                        color: theme.text_light,
                    },
                    '& .MuiOutlinedInput-root .MuiOutlinedInput-notchedOutline': {
                        borderColor: theme.text_light,
                    },
                    '&:hover .MuiOutlinedInput-root .MuiOutlinedInput-notchedOutline': {
                        borderColor: theme.text_light,
                    },
                    '& .MuiOutlinedInput-root.Mui-focused .MuiOutlinedInput-notchedOutline': {
                        borderColor: theme.primary,
                    },
                },
            },
        },
        MuiLink: {
            styleOverrides: {
                root: {
                    color: theme.text_light,
                    '&:hover': {
                        color: theme.primary,
                    },
                },
            },
        },
        MuiMenu: {
            styleOverrides: {
                paper: {
                    color: theme.text_light,
                    backgroundColor: theme.secondary,
                },
            },
        },
        MuiInputBase: {
            styleOverrides: {
                input: {
                    '&:-webkit-autofill': {
                        '-webkitBackgroundClip': 'text',
                        '-webkitTextFillColor': theme.text_light,
                    },
                },
            },
        },
        MuiListItemText: {
            styleOverrides: {
                primary: {
                    color: theme.text_light,
                },
                secondary: {
                    color: theme.text_light,
                },
            },
        },
    },
});

const GlobalStyle = createGlobalStyle`
    * {
        margin: 0;
        padding: 0;
    }

    html, body {
        height: 100%;
        background-color: ${theme.background};
        color: ${theme.text_light};
    }

    #root {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .Toastify__toast-theme--dark {
        background-color: ${theme.secondary};
    }
`;

export type Theme = typeof theme;

const ThemeProvider: React.FC<ProviderProps> = ({ children }) => {
    return (
        <StyledThemeProvider theme={theme}>
            <MuiThemeProvider theme={muiTheme}>
                <GlobalStyle />
                <ToastContainer
                    position="bottom-right"
                    theme="dark"
                    autoClose={3000}
                    hideProgressBar
                    pauseOnFocusLoss={false}
                    draggable={false}
                    pauseOnHover={false}
                />
                {children}
            </MuiThemeProvider>
        </StyledThemeProvider>
    );
};

export default ThemeProvider;

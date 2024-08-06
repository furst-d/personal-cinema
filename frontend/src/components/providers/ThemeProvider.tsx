import React from 'react';
import { createGlobalStyle, ThemeProvider as StyledThemeProvider } from 'styled-components';
import { createTheme, ThemeProvider as MuiThemeProvider } from '@mui/material/styles';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import { ProviderProps } from "../../types/layout";

export const theme = {
    background: '#323232',
    primary: '#b40000',
    primaryDarker: '#750000',
    secondary: '#3f3f3f',
    third: '#fff',
    textLight: '#f3f3f3',
    textDark: '#111',
    breakpoints: {
        xs: 0,
        sm: 600,
        md: 900,
        lg: 1200,
        xl: 1536,
    },
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
            primary: theme.textLight,
            secondary: theme.textDark,
        },
    },
    breakpoints: {
        values: theme.breakpoints,
    },
    components: {
        MuiButton: {
            styleOverrides: {
                root: {
                    backgroundColor: theme.primary,
                    color: theme.textLight,
                    '&:hover': {
                        backgroundColor: theme.primaryDarker,
                    },
                },
            },
        },
        MuiTextField: {
            styleOverrides: {
                root: {
                    '& label': {
                        color: theme.textLight,
                    },
                    '& .MuiInputBase-input': {
                        color: theme.textLight,
                    },
                    '& .MuiOutlinedInput-root .MuiOutlinedInput-notchedOutline': {
                        borderColor: theme.textLight,
                    },
                    '&:hover .MuiOutlinedInput-root .MuiOutlinedInput-notchedOutline': {
                        borderColor: theme.textLight,
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
                    color: theme.textLight,
                    '&:hover': {
                        color: theme.primary,
                    },
                },
            },
        },
        MuiMenu: {
            styleOverrides: {
                paper: {
                    color: theme.textLight,
                    backgroundColor: theme.secondary,
                },
            },
        },
        MuiInputBase: {
            styleOverrides: {
                input: {
                    '&:-webkit-autofill': {
                        WebkitBackgroundClip: 'text',
                        WebkitTextFillColor: theme.textLight,
                    },
                },
            },
        },
        MuiListItemText: {
            styleOverrides: {
                primary: {
                    color: theme.textLight,
                },
                secondary: {
                    color: theme.textLight,
                },
            },
        },
        MuiDialog: {
            styleOverrides: {
                paper: {
                    backgroundColor: theme.secondary,
                    color: theme.textLight,
                },
            },
        },
        MuiDialogTitle: {
            styleOverrides: {
                root: {
                    backgroundColor: theme.secondary,
                    color: theme.textLight,
                },
            },
        },
        MuiDialogContent: {
            styleOverrides: {
                root: {
                    backgroundColor: theme.secondary,
                    color: theme.textLight,
                },
            },
        },
        MuiDialogActions: {
            styleOverrides: {
                root: {
                    backgroundColor: theme.secondary,
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
        color: ${theme.textLight};
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

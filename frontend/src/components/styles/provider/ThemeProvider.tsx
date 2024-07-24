import React from 'react';
import {createGlobalStyle, ThemeProvider as StyledThemeProvider} from "styled-components";
import {createTheme} from "@mui/material/styles";
import {ToastContainer} from "react-toastify";
import 'react-toastify/dist/ReactToastify.css';
import {ThemeProvider as MuiThemeProvider} from "@mui/material";

export const theme = {
    background: "#323232",
    primary: "#b40000",
    primary_darker: "#750000",
    secondary: "#3f3f3f",
    third: "#fff",
    text_light: "#f3f3f3",
    text_dark: "#111",
};

const muiTheme = createTheme({
    palette: {
        primary: {
            main: theme.primary,
        },
        secondary: {
            main: theme.secondary,
        },
        bg: {
            main: theme.background,
        },
        primary_darker: {
            main: theme.primary_darker,
        }
    },
    components: {
        MuiMenu: {
            styleOverrides: {
                paper: {
                    color: theme.text_light,
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
    }

    #root {
        height: 100%;
        display: flex;
        flex-direction: column;
    }
`

const ThemeProvider: ({children}: { children: any }) => JSX.Element = ({children}) => {
    return (
        <StyledThemeProvider theme={theme}>
            <MuiThemeProvider theme={muiTheme}>
                <GlobalStyle />
                <ToastContainer
                    position="bottom-right"
                    theme="dark"
                    autoClose={3000}
                    hideProgressBar={true}
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

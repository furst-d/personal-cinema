import styled from "styled-components";

export const ApplicationStyle = styled.div`
  background: ${p => p.theme.background};
  color: ${p => p.theme.text_light};
  font-family: 'Open Sans', sans-serif;
  display: flex;
  flex-direction: column;
  flex: 1 1 auto;
  width: 100%;  
`

export const ContainerStyle = styled.div`
    background-image: url("/images/background.png");
    display: flex;
    justify-content: center;
    flex: 1 1 auto;
    width: 100%;
`

export const CenteredContainerStyle = styled.div`
    display: flex;
    justify-content: center;
    align-items: flex-start;
    height: auto;
    background-size: cover;
    background-position: center;

    @media (min-width: 769px) {
        background-image: url("/images/background.png");
        align-items: center;
        height: 100vh;
    }
`;

export const ContentWrapperStyle = styled.div`
    background: ${p => p.theme.background};
    display: flex;
    justify-content: center;
    max-width: 90em;
    width: 100%;
    padding-left: 20px;

    @media (min-width: 768px) {
        justify-content: flex-start;
    }
`

export const ContentStyle = styled.div`
    margin-top: 2em;
    width: 100%;
`
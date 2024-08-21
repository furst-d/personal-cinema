import type { ReactNode } from "react";
import { Layout as RALayout, CheckForApplicationUpdate } from "react-admin";
import {CustomMenu} from "./components/menu/CustomMenu";

export const Layout = ({ children }: { children: ReactNode }) => (
  <RALayout menu={CustomMenu}>
    {children}
    <CheckForApplicationUpdate />
  </RALayout>
);

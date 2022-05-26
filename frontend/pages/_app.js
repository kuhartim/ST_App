import { createContext, useState } from "react";
import Header from "../components/Header.js";

import "../styles/globals.scss";

export const SessionContext = createContext({
  isLoggedIn: false,
  setIsLoggedIn: null,
  user: null,
  setUser: null,
});

export default function MyApp({ Component, pageProps }) {
  const [isLoggedIn, setIsLoggedIn] = useState(false);
  const [user, setUser] = useState(null);

  return (
    <SessionContext.Provider
      value={{ isLoggedIn, setIsLoggedIn, user, setUser }}
    >
      <Header />
      <Component {...pageProps} />
    </SessionContext.Provider>
  );
}

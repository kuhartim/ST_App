import { useMemo, useContext } from "react";
import { useRouter } from "next/router";

import { recoverToken, isLoggedIn } from "../services/api";
import { SessionContext } from "../pages/_app.js";

function withAuth(Component, silent) {
  return (props) => {
    const router = useRouter();
    const session = useContext(SessionContext);
    const hasToken = useMemo(() => {
      if (!isLoggedIn()) {
        const username = recoverToken();
        if (username) {
          session.setIsLoggedIn(true);
          session.setUser(username);
        } else {
          router.push("/login");
          return false;
        }
      }

      return true;
    }, [session]);

    return hasToken || silent ? <Component {...props} /> : null;
  };
}

export default withAuth;

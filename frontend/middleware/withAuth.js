import { useMemo, useContext } from "react";
import { useRouter } from "next/router";

import { recoverToken, isLoggedIn, me } from "../services/api";
import { SessionContext } from "../pages/_app.js";

function withAuth(Component, silent) {
  return (props) => {
    const router = useRouter();
    const session = useContext(SessionContext);
    const hasToken = useMemo(async () => {
      if (!isLoggedIn()) {
        if (recoverToken()) {
          try {
            const user = await me();
            session.setIsLoggedIn(true);
            session.setUser(user.data);
          } catch (err) {
            session.setIsLoggedIn(false);
            session.setUser(null);
          }
        } else {
          if (silent) return false;

          router.push({
            pathname: "/login",
          });
          return false;
        }
      }

      return true;
    }, [session]);

    return hasToken || silent ? <Component {...props} /> : null;
  };
}

export default withAuth;

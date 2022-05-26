import { useMemo, useContext } from "react";
import { useNavigate } from "react-router-dom";

import { recoverToken, isLoggedIn } from "../services/api";
import { SessionContext } from "../pages/_app.js";

function withAuth(Component, silent) {
  return (props) => {
    const session = useContext(SessionContext);

    const navigate = useNavigate();

    const hasToken = useMemo(() => {
      if (!isLoggedIn()) {
        if (recoverToken()) {
          session.setIsLoggedIn(true);
        } else {
          navigate("/login");
          return false;
        }
      }

      return true;
    }, [history, session]);

    return hasToken || silent ? <Component {...props} /> : null;
  };
}

export default withAuth;

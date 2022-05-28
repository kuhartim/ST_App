import { useRouter } from "next/router";
import { useContext, useEffect } from "react";
import Link from "next/link";
import clsx from "clsx";

import styles from "../styles/components/Header.module.scss";

import Button from "../components/Button";

import { SessionContext } from "../pages/_app";

import { logout, recoverToken } from "../services/api";

export default function Header() {
  const router = useRouter();
  const { isLoggedIn, user, setIsLoggedIn, setUser } =
    useContext(SessionContext);

  const logoutUser = async () => {
    try {
      await logout();
      setIsLoggedIn(false);
      setUser(null);
      router.push("/login");
    } catch (error) {
      console.error(error);
    }
  };

  useEffect(() => {
    const username = recoverToken();
    if (username) {
      setIsLoggedIn(true);
      setUser(username);
    }
  }, []);

  return ["/register", "/login"].includes(router.pathname) ? (
    <></>
  ) : (
    <nav className={styles["header"]}>
      <ul className={styles["header__list"]}>
        <li className={styles["header__list-element"]}>
          <Link href="/">
            <a
              className={clsx(
                styles["header__list-element-link"],
                router.pathname === "/" &&
                  styles["header__list-element-link--active"]
              )}
            >
              Home
            </a>
          </Link>
        </li>
        <li className={styles["header__list-element"]}>
          <Link href="/my-spots">
            <a
              className={clsx(
                styles["header__list-element-link"],
                router.pathname === "/my-spots" &&
                  styles["header__list-element-link--active"]
              )}
            >
              My spots
            </a>
          </Link>
        </li>
      </ul>
      <div className={styles["header__user"]}>
        {isLoggedIn ? (
          <>
            <span className={styles["header__user-name"]}>{user}</span>
            <Button
              className={styles["header__user-button"]}
              onClick={() => {
                logoutUser();
              }}
            >
              Logout
            </Button>
          </>
        ) : (
          <>
            <Link href="/login">
              <a>
                <Button className={styles["header__user-button"]}>Login</Button>
              </a>
            </Link>
            <Link href="/register">
              <a>
                <Button className={styles["header__user-button"]}>
                  Register
                </Button>
              </a>
            </Link>
          </>
        )}
      </div>
    </nav>
  );
}

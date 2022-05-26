import { useRouter } from "next/router";
import Link from "next/link";
import clsx from "clsx";

import styles from "../styles/components/Header.module.scss";

export default function Header() {
  const router = useRouter();

  return (
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
    </nav>
  );
}

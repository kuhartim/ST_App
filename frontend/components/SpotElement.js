import Head from "next/head";
import Link from "next/link";

import clsx from "clsx";

import styles from "../styles/components/SpotElement.module.scss";

export default function SpotElement({ spot }) {
  return (
    //<Link href={{ pathname: '/search', query: { keyword: 'this way' } }}>
    <Link href={`/spot/${spot.id}`}>
      <a className={styles["spot-element"]}>
        <span
          className={clsx(
            styles["spot-element__title"],
            styles["spot-element__field"]
          )}
        >
          {spot.title}
        </span>
        <span
          className={clsx(
            styles["spot-element__description"],
            styles["spot-element__field"]
          )}
        >
          {spot.description}
        </span>
        <span
          className={clsx(
            styles["spot-element__publisher"],
            styles["spot-element__field"]
          )}
        >
          {spot.publisher}
        </span>
      </a>
    </Link>
  );
}

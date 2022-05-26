import Head from "next/head";
import Link from "next/link";

import clsx from "clsx";

import styles from "../styles/components/SpotElement.module.scss";

function timeSince(date) {
  var seconds = Math.floor((new Date() - date) / 1000);

  var interval = seconds / 31536000;

  if (interval > 1) {
    return Math.floor(interval) + " years";
  }
  interval = seconds / 2592000;
  if (interval > 1) {
    return Math.floor(interval) + " months";
  }
  interval = seconds / 86400;
  if (interval > 1) {
    return Math.floor(interval) + " days";
  }
  interval = seconds / 3600;
  if (interval > 1) {
    return Math.floor(interval) + " hours";
  }
  interval = seconds / 60;
  if (interval > 1) {
    return Math.floor(interval) + " minutes";
  }
  return Math.floor(seconds) + " seconds";
}

export default function SpotElement({ spot }) {
  const date = timeSince(new Date(spot.created).getTime());
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
          {"by " + spot.publisher}
        </span>
        <span
          className={clsx(
            styles["spot-element__created"],
            styles["spot-element__field"]
          )}
        >
          {date + " ago"}
        </span>
      </a>
    </Link>
  );
}

import Head from "next/head";

import styles from "../styles/components/Title.module.scss";

export default function Title({ title, subtitle }) {
  return (
    <div className={styles["title"]}>
      <h2 className={styles["title__text"]}>{title}</h2>
      <span className={styles["title__text"]}>{subtitle}</span>
    </div>
  );
}

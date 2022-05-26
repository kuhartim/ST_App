import clsx from "clsx";
import { useState, useEffect } from "react";
import Router from "next/router";

import Head from "next/head";
import SpotElement from "./SpotElement";
import Pagination from "./Pagination";

import styles from "../styles/components/SpotList.module.scss";

export default function SpotList({
  spots,
  page,
  maxPage,
  perPage,
  sortBy,
  sortType,
}) {
  const onPageChange = (page) => {
    Router.push({
      pathname: Router.pathname,
      query: {
        page,
        perPage,
        sortBy,
        sortType,
      },
    });
  };

  return (
    <div className={styles["spot-list"]}>
      <h2 className={styles["spot-list__text"]}>Photo locations</h2>
      <span className={styles["spot-list__text"]}>
        Find your next location for your photo
      </span>
      <div className={styles["spot-list__list"]}>
        {spots.map((spot, i) => (
          <SpotElement spot={spot} key={i} />
        ))}
      </div>
      <Pagination
        currentPage={page}
        maxPage={maxPage}
        siblingCount={1}
        onPageChange={onPageChange}
      />
    </div>
  );
}

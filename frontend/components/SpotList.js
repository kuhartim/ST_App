import clsx from "clsx";
import { useState, useEffect } from "react";
import Router from "next/router";

import Head from "next/head";
import SpotElement from "./SpotElement";
import Pagination from "./Pagination";
import InputField from "./InputField";
import Selection from "./Selection";

import styles from "../styles/components/SpotList.module.scss";

export default function SpotList({
  spots,
  page,
  maxPage,
  perPage,
  sortBy,
  sortType,
  search,
}) {
  const selectionOptions = [
    "created descending",
    "created ascending",
    "title descending",
    "title ascending",
  ];

  const onOrderChange = (index) => {
    const selected = selectionOptions[index];
    let [newSortBy, newSortType] = selected.split(" ");
    newSortType = newSortType == "descending" ? "desc" : "asc";
    Router.push(
      {
        pathname: Router.pathname,
        query: {
          page,
          perPage,
          sortBy: newSortBy,
          sortType: newSortType,
          search,
        },
      },
      undefined,
      { scroll: false }
    );
  };

  const onPageChange = (page) => {
    Router.push(
      {
        pathname: Router.pathname,
        query: {
          page,
          perPage,
          sortBy,
          sortType,
          search,
        },
      },
      undefined,
      { scroll: false }
    );
  };

  const onSearch = (value) => {
    Router.push(
      {
        pathname: Router.pathname,
        query: {
          page,
          perPage,
          sortBy,
          sortType,
          search: value,
        },
      },
      undefined,
      { scroll: false }
    );
  };

  return (
    <div className={styles["spot-list"]}>
      <div className={styles["spot-list__settings"]}>
        <InputField
          onSubmit={onSearch}
          buttonText="Search"
          placeholderText="Search string"
        />
        <Selection
          fields={selectionOptions}
          primarySelectedIndex={0}
          onSelect={onOrderChange}
        />
      </div>
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

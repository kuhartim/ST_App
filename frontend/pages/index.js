import { useState, useCallback, useEffect } from "react";
import Router, { useRouter } from "next/router";

import Map from "../components/Map";
import SpotList from "../components/SpotList";

import { getSpots } from "../services/api";

import styles from "../styles/pages/index.module.scss";

export default function Home({
  apiKey,
  spots,
  perPage,
  page,
  maxPage,
  sortBy,
  sortType,
}) {
  return (
    <div className={styles["home"]}>
      <SpotList
        spots={spots}
        page={page}
        maxPage={maxPage}
        perPage={perPage}
        sortBy={sortBy}
        sortType={sortType}
      />
    </div>
  );
}

export async function getServerSideProps(context) {
  // API klic z query { sortBy: 'created', sortType: 'desc', page: '1', perPage: '5' }
  // če ni nastavlen nastavi privzeto
  const query = context.query;

  const sortBy = query["sortBy"] || "created";
  const sortType = query["sortType"] || "desc";
  const page = Number(query["page"]) || 1;
  const perPage = Number(query["perPage"]) || 5;

  const response = await getSpots(sortBy, sortType, page, perPage);
  const data = response.data;

  const maxPage = data["max_page"];
  const spots = data["spots"];

  return {
    props: {
      spots,
      maxPage,
      sortBy,
      sortType,
      page,
      perPage,
      apiKey: process.env.GOOGLE_MAPS_API,
    },
  };
}
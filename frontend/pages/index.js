import { useState, useCallback, useEffect } from "react";
import Router, { useRouter } from "next/router";

import Map from "../components/Map";
import SpotList from "../components/SpotList";
import Title from "../components/Title";

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
  search,
}) {
  return (
    <div className={styles["home"]}>
      <div className={styles["home__container"]}>
        <Title
          title="Choose location on map"
          subtitle="Where is your perfect location?"
        />
        <Map apiKey={apiKey} markers={spots} />
        <Title
          title="Find your perfect location"
          subtitle="Search for a location that you knew about, just forgot where it is"
        />
        <SpotList
          spots={spots}
          page={page}
          maxPage={maxPage}
          perPage={perPage}
          sortBy={sortBy}
          sortType={sortType}
          search={search}
        />
      </div>
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
  const search = query["search"] || null;

  const response = await getSpots(sortBy, sortType, page, perPage, search);
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
      search,
      apiKey: process.env.GOOGLE_MAPS_API,
    },
  };
}

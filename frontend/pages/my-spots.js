import Head from "next/head";
import Link from "next/link";

import styles from "../styles/pages/my-spots.module.scss";

import SpotList from "../components/SpotList";

import withAuth from "../middleware/withAuth";

import { getSpots, recoverToken } from "../services/api";

function MySpots({ spots, perPage, page, maxPage, sortBy, sortType, search }) {
  return (
    <div className={styles["my-spots"]}>
      <div className={styles["my-spots__container"]}>
        <Link href="/spot/add-new">
          <a className={styles["my-spots__add-spot"]}>
            <span className={styles["my-spots__add-spot-plus"]}>+</span>
            <span className={styles["my-spots__add-spot-text"]}>
              Add new spot
            </span>
          </a>
        </Link>
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
  recoverToken(context.req.cookies.Token);

  const query = context.query;

  const userId = "current";

  const sortBy = query["sortBy"] || "created";
  const sortType = query["sortType"] || "desc";
  const page = Number(query["page"]) || 1;
  const perPage = Number(query["perPage"]) || 5;
  const search = query["search"] || null;

  try {
    const response = await getSpots(
      sortBy,
      sortType,
      page,
      perPage,
      search,
      userId
    );
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
      },
    };
  } catch (error) {
    console.log(error);
    return {
      props: {
        spots: [],
        maxPage: 1,
        sortBy: "created",
        sortType: "desc",
        page: 1,
        perPage: 5,
        search: null,
      },
    };
  }
}

export default withAuth(MySpots);

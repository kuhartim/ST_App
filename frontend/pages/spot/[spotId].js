import Head from "next/head";
import Router from "next/router";
import Link from "next/link";

import Map from "../../components/Map";

import { getSpot } from "../../services/api";

import styles from "../../styles/pages/spot.module.scss";

function Spot({ spot, apiKey }) {
  return (
    <div className={styles["spot"]}>
      <div className={styles["spot__container"]}>
        <h1 className={styles["spot__title"]}>{spot.title}</h1>
        <Link href={`/user/spot.publisher_id`}>
          <a className={styles["spot__publisher"]}>{spot.publisher}</a>
        </Link>
        <div className={styles["spot__main"]}>
          <p className={styles["spot__description"]}>{spot.description}</p>
          <span className={styles["spot__subtitle"]}>
            Check out some images from this place
          </span>
          <div className={styles["spot__images"]}>
            {spot.images.map((image) => (
              <img
                key={image.id}
                src={image.image}
                alt="spot image"
                className={styles["spot__image"]}
              />
            ))}
            <div className={styles["spot__add-image"]}>
              <span className={styles["spot__add-image-plus"]}>+</span>
              <span className={styles["spot__add-image-text"]}>
                Add your image here!
              </span>
            </div>
          </div>
        </div>
        <span className={styles["spot__subtitle"]}>
          Check out location on the map
        </span>
        <Map apiKey={apiKey} markers={[spot]} />
      </div>
    </div>
  );
}

export async function getServerSideProps(context) {
  const { spotId } = context.query;
  const spot = await getSpot(spotId);

  return {
    props: {
      spot: spot.data,
      apiKey: process.env.GOOGLE_MAPS_API,
    },
  };
}

export default Spot;

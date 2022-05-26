import { useState, memo, useCallback } from "react";
import { GoogleMap, useJsApiLoader } from "@react-google-maps/api";

import styles from "../styles/components/Map.module.scss";

const containerStyle = {
  width: "600px",
  height: "500px",
};

const center = {
  lat: 46.130229,
  lng: 14.996102,
};

const zoom = 8;

const mapStyles = require("../settings/MapStyles.json");

export default function Map({ apiKey }) {
  const { isLoaded } = useJsApiLoader({
    id: "google-map-script",
    googleMapsApiKey: apiKey,
    language: "sl",
  });

  const [map, setMap] = useState(null);

  const onLoad = useCallback((map) => {
    // const bounds = new window.google.maps.LatLngBounds(center);
    // map.fitBounds(bounds);
    setMap(map);
  }, []);

  const onUnmount = useCallback((map) => {
    setMap(null);
  }, []);

  return (
    <div className={styles["map"]}>
      {isLoaded ? (
        <GoogleMap
          mapContainerStyle={containerStyle}
          center={center}
          zoom={zoom}
          onLoad={onLoad}
          onUnmount={onUnmount}
          options={{
            styles: mapStyles,
            disableDefaultUI: true,
          }}
        >
          {/* Child components, such as markers, info windows, etc. */}
          <></>
        </GoogleMap>
      ) : (
        <>loading</>
      )}
    </div>
  );
}

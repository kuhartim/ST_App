import { useState, memo, useCallback, useEffect } from "react";
import { GoogleMap, useJsApiLoader, Marker } from "@react-google-maps/api";
import Router from "next/router";

import styles from "../styles/components/Map.module.scss";

const containerStyle = {
  width: "100%",
  height: "100%",
};

const center = {
  lat: 46.130229,
  lng: 14.996102,
};

const zoom = 8;

const mapStyles = require("../settings/MapStyles.json");

export default function Map({ apiKey, markers }) {
  const { isLoaded } = useJsApiLoader({
    id: "google-map-script",
    googleMapsApiKey: apiKey,
    language: "sl",
  });

  const redirectToSpotPage = (spotId) => {
    Router.push(`/spot/${spotId}`);
  };

  return (
    <div className={styles["map"]}>
      {isLoaded ? (
        <GoogleMap
          mapContainerStyle={containerStyle}
          center={center}
          zoom={zoom}
          options={{
            styles: mapStyles,
            disableDefaultUI: true,
          }}
        >
          {markers.map((marker) => (
            <Marker
              key={marker.id}
              position={{ lat: marker.lat, lng: marker.lon }}
              icon={{
                url: "/static/icons/camera.png",
                scaledSize: new window.google.maps.Size(20, 17),
              }}
              onClick={() => {
                redirectToSpotPage(marker.id);
              }}
            />
          ))}
        </GoogleMap>
      ) : (
        <div>Loading...</div>
      )}
    </div>
  );
}

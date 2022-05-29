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

export default function Map({ apiKey, markers, onClick }) {
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
          onClick={(ev) => {
            onClick && onClick({ lat: ev.latLng.lat(), lng: ev.latLng.lng() });
          }}
          options={{
            styles: mapStyles,
            disableDefaultUI: true,
            draggableCursor: "pointer",
            draggingCursor: "pointer",
          }}
        >
          {markers.map((marker, i) => (
            <Marker
              key={marker.id || i}
              position={{ lat: Number(marker.lat), lng: Number(marker.lon) }}
              icon={{
                url: "/static/icons/camera.png",
                scaledSize: new window.google.maps.Size(20, 17),
              }}
              onClick={() => {
                marker.id && redirectToSpotPage(marker.id);
              }}
              onLoad={() => {
                console.log("marker loaded");
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

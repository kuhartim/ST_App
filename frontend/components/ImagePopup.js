import Head from "next/head";

import { useEffect, useRef } from "react";

import clsx from "clsx";

import styles from "../styles/components/ImagePopup.module.scss";

function ImagePopup({ image, isOpen, onClose }) {
  const ref = useRef();
  useEffect(() => {
    const handleClickOutside = (event) => {
      if (ref.current && !ref.current.contains(event.target)) {
        onClose();
      }
    };
    document.addEventListener("click", handleClickOutside, true);
    return () => {
      document.removeEventListener("click", handleClickOutside, true);
    };
  }, [close]);

  return (
    <div
      className={clsx(
        styles["image-popup"],
        isOpen && styles["image-popup--open"]
      )}
    >
      <div className={styles["image-popup__container"]}>
        <img ref={ref} src={image} className={styles["image-popup__image"]} />
        <button
          onClick={onClose}
          className={styles["image-popup__close-button"]}
        >
          X
        </button>
      </div>
    </div>
  );
}

export default ImagePopup;

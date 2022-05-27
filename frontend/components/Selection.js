import { useState, useRef, useEffect } from "react";
import clsx from "clsx";
import styles from "../styles/components/Selection.module.scss";

export default function Selection({ fields, primarySelectedIndex, onSelect }) {
  const ref = useRef(null);

  const [selectedIndex, setSelectedIndex] = useState(primarySelectedIndex);
  const [isOpen, setIsOpen] = useState(false);

  const toggle = () => {
    setIsOpen(!isOpen);
  };

  const close = () => {
    setIsOpen(false);
  };

  useEffect(() => {
    const handleClickOutside = (event) => {
      if (ref.current && !ref.current.contains(event.target)) {
        close();
      }
    };
    document.addEventListener("click", handleClickOutside, true);
    return () => {
      document.removeEventListener("click", handleClickOutside, true);
    };
  }, [close]);

  return (
    <div ref={ref} className={styles["selection"]}>
      <span
        className={clsx(
          styles["selection__selected"],
          isOpen && styles["selection__selected--open"]
        )}
        onClick={toggle}
      >
        {fields[selectedIndex]}
      </span>
      <div
        className={clsx(
          styles["selection__list"],
          isOpen && styles["selection__list--open"]
        )}
      >
        {fields.map((field, i) => (
          <div
            className={clsx(
              styles["selection__item"],
              i === selectedIndex ? styles["selection__item--selected"] : ""
            )}
            key={i}
            onClick={() => {
              setSelectedIndex(i);
              onSelect(i);
              toggle();
            }}
          >
            {field}
          </div>
        ))}
      </div>
    </div>
  );
}

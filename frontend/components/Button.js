import clsx from "clsx";
import styles from "../styles/components/Button.module.scss";

export default function Button({
  children,
  onClick,
  type,
  className,
  colorType,
}) {
  return (
    <button
      className={clsx(
        styles["button"],
        className,
        colorType == "dark" && styles["button--dark"]
      )}
      onClick={onClick}
      type={type}
    >
      {children}
    </button>
  );
}

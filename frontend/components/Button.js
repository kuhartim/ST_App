import clsx from "clsx";
import styles from "../styles/components/Button.module.scss";

export default function Button({
  children,
  onClick,
  type,
  className,
  colorType,
  disabled,
}) {
  return (
    <button
      className={clsx(
        styles["button"],
        className,
        colorType == "dark" && styles["button--dark"],
        disabled && styles["button--disabled"]
      )}
      onClick={onClick}
      type={type}
      disabled={disabled}
    >
      {children}
    </button>
  );
}

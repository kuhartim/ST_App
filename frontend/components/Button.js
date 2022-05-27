import styles from "../styles/components/Button.module.scss";

export default function Button({ children, onClick, type }) {
  return (
    <button className={styles["button"]} onClick={onClick} type={type}>
      {children}
    </button>
  );
}

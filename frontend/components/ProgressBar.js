import clsx from "clsx";
import styles from "../styles/components/ProgressBar.module.scss";

export default function ProgressBar({ steps, currentStep }) {
  return (
    <div className={styles["progress-bar"]}>
      {steps.map((step, index) => {
        return (
          <div
            key={index}
            className={clsx(
              styles["progress-bar__step"],
              currentStep >= step && styles["progress-bar__step--active"]
            )}
          >
            {step}
          </div>
        );
      })}
    </div>
  );
}

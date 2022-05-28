import { useState, useCallback } from "react";
import clsx from "clsx";
import Button from "./Button";
import styles from "../styles/components/InputField.module.scss";

export default function InputField({
  buttonText,
  placeholderText,
  onSubmit,
  children,
  colorType,
  type,
  value: outsideValue,
  onChange: outsideOnChange,
  className,
}) {
  const [value, setValue] = useState("");

  const onValueChange = useCallback(
    ({ target: { value } }) => {
      setValue(value);
    },
    [setValue]
  );

  const submit = (e) => {
    e.preventDefault();

    onSubmit(value);
  };

  return !outsideOnChange ? (
    <form onSubmit={submit} className={styles["input-field"]}>
      <input
        type={type || "text"}
        onChange={onValueChange}
        value={value}
        placeholder={placeholderText}
        className={clsx(
          styles["input-field__field"],
          colorType == "dark" && styles["input-field__field--dark"]
        )}
      />
      {children}
    </form>
  ) : (
    <>
      <input
        type={type || "text"}
        onChange={outsideOnChange}
        value={outsideValue}
        placeholder={placeholderText}
        className={clsx(
          styles["input-field__field"],
          colorType == "dark" && styles["input-field__field--dark"],
          className
        )}
      />
      {children}
    </>
  );
}

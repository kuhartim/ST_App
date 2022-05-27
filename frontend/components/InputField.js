import { useState, useCallback } from "react";
import Button from "./Button";
import styles from "../styles/components/InputField.module.scss";

export default function InputField({ buttonText, placeholderText, onSubmit }) {
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

  return (
    <form onSubmit={submit} className={styles["input-field"]}>
      <input
        type="text"
        onChange={onValueChange}
        placeholder={placeholderText}
        className={styles["input-field__field"]}
      />
      <Button type="submit">{buttonText}</Button>
    </form>
  );
}

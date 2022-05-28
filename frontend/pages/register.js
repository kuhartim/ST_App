import Head from "next/head";
import Router from "next/router";

import { useState, useCallback, useContext } from "react";

import { SessionContext } from "./_app";

import Card from "../components/Card";
import InputField from "../components/InputField";
import Button from "../components/Button";

import { registration, login } from "../services/api";

import styles from "../styles/pages/register.module.scss";

export default function Register() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");

  const { setIsLoggedIn, setUser } = useContext(SessionContext);

  const onUsernameChange = useCallback(
    ({ target: { value } }) => setUsername(value),
    [setUsername]
  );
  const onPasswordChange = useCallback(
    ({ target: { value } }) => setPassword(value),
    [setPassword]
  );
  const onConfirmPasswordChange = useCallback(
    ({ target: { value } }) => setConfirmPassword(value),
    [setConfirmPassword]
  );

  const onSubmit = async (e) => {
    e.preventDefault();
    try {
      await registration(username, password, confirmPassword);
      const user = await login(username, password);
      setIsLoggedIn(true);
      setUser(user.data);
      Router.push({
        pathname: "/",
      });
    } catch (err) {
      setIsLoggedIn(false);
      setUser(null);
      console.error(err);
    }
  };

  return (
    <div className={styles["register"]}>
      <Card>
        <form onSubmit={onSubmit} className={styles["register__form"]}>
          <h1 className={styles["register__title"]}>Register</h1>
          <InputField
            className={styles["register__field"]}
            placeholderText="username"
            colorType="dark"
            value={username}
            onChange={onUsernameChange}
          />
          <InputField
            className={styles["register__field"]}
            placeholderText="password"
            colorType="dark"
            value={password}
            onChange={onPasswordChange}
            type="password"
          />
          <InputField
            className={styles["register__field"]}
            placeholderText="confirm password"
            colorType="dark"
            value={confirmPassword}
            onChange={onConfirmPasswordChange}
            type="password"
          />
          <Button
            type="submit"
            className={styles["register__button"]}
            colorType="dark"
          >
            Register
          </Button>
        </form>
      </Card>
    </div>
  );
}

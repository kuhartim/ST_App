import Head from "next/head";
import Router from "next/router";

import { useState, useCallback, useContext } from "react";

import Card from "../components/Card";
import InputField from "../components/InputField";
import Button from "../components/Button";

import { login } from "../services/api";
import { SessionContext } from "./_app";

import styles from "../styles/pages/login.module.scss";

export default function Login() {
  const [username, setUsername] = useState("");
  const [password, setPassword] = useState("");

  const { setIsLoggedIn, setUser } = useContext(SessionContext);

  const onUsernameChange = useCallback(
    ({ target: { value } }) => setUsername(value),
    [setUsername]
  );
  const onPasswordChange = useCallback(
    ({ target: { value } }) => setPassword(value),
    [setPassword]
  );

  const onSubmit = async (e) => {
    e.preventDefault();
    try {
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
    <div className={styles["login"]}>
      <Card>
        <form onSubmit={onSubmit} className={styles["login__form"]}>
          <h1 className={styles["login__title"]}>login</h1>
          <InputField
            className={styles["login__field"]}
            placeholderText="username"
            colorType="dark"
            value={username}
            onChange={onUsernameChange}
          />
          <InputField
            className={styles["login__field"]}
            placeholderText="password"
            colorType="dark"
            value={password}
            onChange={onPasswordChange}
            type="password"
          />
          <Button
            type="submit"
            className={styles["login__button"]}
            colorType="dark"
          >
            Login
          </Button>
        </form>
      </Card>
    </div>
  );
}

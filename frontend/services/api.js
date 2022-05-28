import React from "react";
import axios from "axios";

import Cookies from "js-cookie";

const backend = axios.create({ baseURL: "http://localhost/backend" });

//USERS
export async function login(username, password) {
  // add expires to backend response
  const {
    data: { token, expire },
  } = await backend
    .post("/api/login.php", { username, password })
    .catch(handleUnauthorized);

  if (!token) throw Error("No Token");

  Cookies.set("Token", token, {
    expires: Date.now() + expire,
    secure: process.env.NODE_ENV === "production",
  });

  backend.defaults.headers.common.Authorization = `Bearer ${token}`;

  return me();
}

export function me() {
  return backend.get("/api/me.php").catch(handleUnauthorized);
}

export function logout() {
  Cookies.remove("Token");
  const response = backend.post("/api/logout.php").catch(handleUnauthorized);
  backend.defaults.headers.common.Authorization = "";
  return response;
}

export function deleteUser() {
  return backend.delete(`/api/users.php`).catch(handleUnauthorized);
}

export function recoverToken() {
  const token = Cookies.get("Token");
  if (!token) return false;
  backend.defaults.headers.common.Authorization = `Bearer ${token}`;
  return true;
}

export function isLoggedIn() {
  return !!backend.defaults.headers.common.Authorization;
}

export function registration(username, password, passwordConfirm) {
  return backend
    .post("/api/users.php", {
      username,
      password,
      confirm_password: passwordConfirm,
    })
    .catch(handleUnauthorized);
}

export function updateUser(password, passwordConfirm) {
  return backend
    .put("/api/users.php", {
      password,
      confirm_password: passwordConfirm,
    })
    .catch(handleUnauthorized);
}

export function getUser() {
  return backend.get("/api/users.php").catch(handleUnauthorized);
}

//SPOTS
export function createSpot(lon, lat, title, description, images) {
  const formData = new FormData();
  formData.append("lon", lon);
  formData.append("lat", lat);
  formData.append("title", title);
  formData.append("description", description);
  for (const image of images) {
    formData.append("images[]", image);
  }

  return backend
    .post("/api/spots.php", formData, {
      headers: { "content-type": "multipart/form-data" },
    })
    .catch(handleUnauthorized);
}

export function updateSpot(
  title = null,
  description = null,
  images = [],
  removedImages = []
) {
  const formData = new FormData();
  if (title) formData.append("title", title);
  if (description) formData.append("description", description);
  if (images.length)
    for (const image of images) {
      formData.append("images[]", image);
    }
  if (removedImages.length)
    formData.append("removed_images", JSON.stringify(removedImages));

  return backend
    .post("/api/spots.php", formData, {
      headers: { "content-type": "multipart/form-data" },
    })
    .catch(handleUnauthorized);
}

export function getSpots(sortBy, sortType, page, perPage, search) {
  return backend
    .get("/api/spots.php", {
      params: {
        sort_by: sortBy,
        sort_type: sortType,
        page: page,
        per_page: perPage,
        search: search,
      },
    })
    .catch(handleUnauthorized);
}

export function deleteSpot(id) {
  return backend
    .delete("/api/spots.php", { spot_id: id })
    .catch(handleUnauthorized);
}

// HANDLER
function handleUnauthorized(err) {
  if (err.response && err.response.status === 403) throw false;
  throw err;
}

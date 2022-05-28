import { useState, useCallback } from "react";
import Router from "next/router";
import clsx from "clsx";
import styles from "../../styles/pages/add-new.module.scss";

import ProgressBar from "../../components/ProgressBar";
import Map from "../../components/Map";
import Button from "../../components/Button";
import InputField from "../../components/InputField";

import { createSpot } from "../../services/api";

export default function AddNew({ apiKey }) {
  const [step, setStep] = useState(1);
  const [markers, setMarkers] = useState([]);
  const [position, setPosition] = useState(null);
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [images, setImages] = useState([]);

  const onTitleChange = useCallback(
    ({ target: { value } }) => {
      setTitle(value);
    },
    [setTitle]
  );

  const onDescriptionChange = useCallback(
    ({ target: { value } }) => {
      setDescription(value);
    },
    [setDescription]
  );

  const onFileChanges = useCallback(
    ({ target: { files } }) => {
      setImages([...images, ...files]);
    },
    [setImages]
  );

  const onMapClick = useCallback(
    (marker) => {
      setMarkers([marker]);
      setPosition(marker);
      console.log(markers);
    },
    [markers]
  );

  const removeImage = useCallback(
    (index) => {
      setImages(images.filter((image, i) => i !== index));
    },
    [images]
  );

  const submit = useCallback(async () => {
    try {
      await createSpot(position.lng, position.lat, title, description, images);
      Router.push({
        pathname: "/my-spots",
      });
    } catch (err) {
      console.log(err);
    }
  }, [position, title, description, images]);

  const nextStep = useCallback(() => {
    setStep(step + 1);
  }, [step]);

  return (
    <div className={styles["add-new"]}>
      <div className={styles["add-new__container"]}>
        <h1 className={styles["add-new__title"]}>Add new spot</h1>
        <ProgressBar steps={[1, 2, 3]} currentStep={step} />
        <div
          className={clsx(
            styles["add-new__step"],
            step == 1 && styles["add-new__step--show"]
          )}
        >
          <h2 className={styles["add-new__subtitle"]}>Choose location</h2>
          <Map apiKey={apiKey} onClick={onMapClick} markers={markers} />
          <Button onClick={nextStep} disabled={!position}>
            Next
          </Button>
        </div>
        <div
          className={clsx(
            styles["add-new__step"],
            step == 2 && styles["add-new__step--show"]
          )}
        >
          <h2 className={styles["add-new__subtitle"]}>Set title</h2>
          <InputField
            className={styles["add-new__input-field"]}
            placeholderText="Small mountain under Triglav"
            value={title}
            onChange={onTitleChange}
          />
          <h2 className={styles["add-new__subtitle"]}>
            Describe this awesome spot in few words
          </h2>
          <InputField
            isTextArea={true}
            value={description}
            onChange={onDescriptionChange}
            className={clsx(
              styles["add-new__input-field--textarea"],
              styles["add-new__input-field"]
            )}
            placeholderText="This is a small mountain just under Triglav. It has awesome view to Bled."
          />
          <Button onClick={nextStep} disabled={!title || !description}>
            Next
          </Button>
        </div>
        <div
          className={clsx(
            styles["add-new__step"],
            step == 3 && styles["add-new__step--show"]
          )}
        >
          <h2 className={styles["add-new__subtitle"]}>
            Add photos of this spot
          </h2>
          <div className={styles["add-new__photos"]}>
            {images.map((image, index) => (
              <div
                className={styles["add-new__photo-container"]}
                onClick={() => removeImage(index)}
              >
                <img
                  key={index}
                  src={URL.createObjectURL(image)}
                  alt="photo"
                  className={styles["add-new__photo"]}
                />
              </div>
            ))}
            <label className={styles["add-new__add-photo-button"]}>
              <input
                className={styles["add-new__add-photo-input"]}
                type="file"
                multiple
                onChange={onFileChanges}
              />
            </label>
          </div>
          <Button onClick={submit} disabled={!images.length}>
            Submit
          </Button>
        </div>
      </div>
    </div>
  );
}

export function getServerSideProps(context) {
  return {
    props: {
      apiKey: process.env.GOOGLE_MAPS_API,
    },
  };
}

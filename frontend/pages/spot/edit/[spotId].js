import { useState, useCallback, useEffect } from "react";
import Router from "next/router";
import clsx from "clsx";
import styles from "../../../styles/pages/edit.module.scss";

import ProgressBar from "../../../components/ProgressBar";
import Button from "../../../components/Button";
import InputField from "../../../components/InputField";

import withAuth from "../../../middleware/withAuth";

import { updateSpot, getSpot } from "../../../services/api";

function AddNew({ spot }) {
  const [step, setStep] = useState(1);
  const [title, setTitle] = useState(spot.title);
  const [description, setDescription] = useState(spot.description);
  const [images, setImages] = useState(spot.images);
  const [removedImages, setRemovedImages] = useState([]);

  useEffect(() => {
    if (!spot.title) {
      Router.push("/");
    }
  }, [spot]);

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
    [setImages, images]
  );

  const removeImage = useCallback(
    (index) => {
      setImages(images.filter((image, i) => i !== index));
    },
    [images]
  );

  const removeExistingImage = useCallback(
    (index) => {
      setRemovedImages([...removedImages, index]);
    },
    [removedImages]
  );

  const submit = useCallback(async () => {
    try {
      await updateSpot(
        spot.id,
        title,
        description,
        images.filter((image) => !image.id),
        removedImages
      );
      Router.push({
        pathname: "/my-spots",
      });
    } catch (err) {
      console.log(err);
    }
  }, [title, description, images]);

  const nextStep = useCallback(() => {
    setStep(step + 1);
  }, [step]);

  return (
    <div className={styles["edit"]}>
      <div className={styles["edit__container"]}>
        <h1 className={styles["edit__title"]}>Add new spot</h1>
        <ProgressBar steps={[1, 2]} currentStep={step} />
        <div
          className={clsx(
            styles["edit__step"],
            step == 1 && styles["edit__step--show"]
          )}
        >
          <h2 className={styles["edit__subtitle"]}>Set title</h2>
          <InputField
            className={styles["edit__input-field"]}
            placeholderText="Small mountain under Triglav"
            value={title}
            onChange={onTitleChange}
          />
          <h2 className={styles["edit__subtitle"]}>
            Describe this awesome spot in few words
          </h2>
          <InputField
            isTextArea={true}
            value={description}
            onChange={onDescriptionChange}
            className={clsx(
              styles["edit__input-field--textarea"],
              styles["edit__input-field"]
            )}
            placeholderText="This is a small mountain just under Triglav. It has awesome view to Bled."
          />
          <Button onClick={nextStep} disabled={!title || !description}>
            Next
          </Button>
        </div>
        <div
          className={clsx(
            styles["edit__step"],
            step == 2 && styles["edit__step--show"]
          )}
        >
          <h2 className={styles["edit__subtitle"]}>Add photos of this spot</h2>
          <div className={styles["edit__photos"]}>
            {images &&
              images.map((image, index) => (
                <div
                  key={index}
                  className={styles["edit__photo-container"]}
                  onClick={() => {
                    if (image.image) {
                      removeExistingImage(image.id);
                    }
                    removeImage(index);
                  }}
                >
                  <img
                    key={index}
                    src={image.image || URL.createObjectURL(image)}
                    alt="photo"
                    className={styles["edit__photo"]}
                  />
                </div>
              ))}
            <label className={styles["edit__add-photo-button"]}>
              <input
                className={styles["edit__add-photo-input"]}
                type="file"
                multiple
                onChange={onFileChanges}
              />
            </label>
          </div>
          <Button onClick={submit} disabled={images && !images.length}>
            Submit
          </Button>
        </div>
      </div>
    </div>
  );
}

export async function getServerSideProps(context) {
  const { spotId } = context.query;
  try {
    const spot = await getSpot(spotId);
    return {
      props: {
        spot: spot.data,
      },
    };
  } catch (err) {
    return {
      props: {
        spot: {
          title: "",
          description: "",
          images: [],
        },
      },
    };
  }
}

export default withAuth(AddNew);
